<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/uploads.php';

function faultForWorkflow(PDO $pdo, string $reference, bool $lock = false): ?array
{
    $sql = 'SELECT * FROM fault_reports WHERE fault_reference = :reference LIMIT 1'
        . ($lock ? ' FOR UPDATE' : '');
    $statement = $pdo->prepare($sql);
    $statement->execute([':reference' => $reference]);
    return $statement->fetch() ?: null;
}

function activeTechnicians(PDO $pdo): array
{
    return $pdo->query("SELECT id, full_name, username FROM users
                        WHERE role = 'TECHNICIAN' AND status = 'ACTIVE'
                        ORDER BY full_name, id")->fetchAll();
}

function assignmentForFault(PDO $pdo, int $faultId): ?array
{
    $statement = $pdo->prepare('SELECT ra.*, ra.assignment_notes AS instructions,
        u.full_name AS technician_name
        FROM repair_assignments ra JOIN users u ON u.id = ra.technician_id
        WHERE ra.fault_report_id = :id ORDER BY ra.id DESC LIMIT 1');
    $statement->execute([':id' => $faultId]);
    return $statement->fetch() ?: null;
}

function repairUpdates(PDO $pdo, int $faultId): array
{
    $statement = $pdo->prepare('SELECT ru.*, u.full_name AS technician_name
        FROM repair_updates ru JOIN repair_assignments ra ON ra.id = ru.assignment_id
        JOIN users u ON u.id = ru.updated_by
        WHERE ra.fault_report_id = :id ORDER BY ru.id DESC');
    $statement->execute([':id' => $faultId]);
    return $statement->fetchAll();
}

function workflowHistory(PDO $pdo, int $faultId, string $status, int $actor,
    string $publicNote, ?string $internalNote = null): void
{
    $statement = $pdo->prepare('INSERT INTO fault_status_history
        (fault_report_id, status, changed_by, public_note, internal_note)
        VALUES (:id, :status, :actor, :public_note, :internal_note)');
    $statement->execute([
        ':id' => $faultId, ':status' => $status, ':actor' => $actor,
        ':public_note' => $publicNote, ':internal_note' => $internalNote,
    ]);
}

function changeFaultStatus(PDO $pdo, int $faultId, string $status): void
{
    $statement = $pdo->prepare('UPDATE fault_reports SET status = :status WHERE id = :id');
    $statement->execute([':status' => $status, ':id' => $faultId]);
}

/** All state changes are serialized on the fault row and checked again inside the transaction. */
function performRepairAction(PDO $pdo, string $reference, string $action, int $actor): string
{
    $photoPath = null;
    // Validate and store evidence before locking; remove it if the DB operation fails.
    if (in_array($action, ['update', 'complete'], true)) {
        $photoPath = storeRepairPhoto();
    }
    try {
        $pdo->beginTransaction();
        $fault = faultForWorkflow($pdo, $reference, true);
        if (!$fault) {
            throw new DomainException('Fault report not found.');
        }
        $id = (int)$fault['id'];
        $status = (string)$fault['status'];
        $assignment = assignmentForFault($pdo, $id);
        $note = '';
        $next = $status;

        if ($action === 'assign') {
            if (!in_array($status, ['REVIEWED', 'RETURNED'], true)) {
                throw new DomainException('This fault is not ready for assignment.');
            }
            $technician = postedId('technician_id');
            $check = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'TECHNICIAN' AND status = 'ACTIVE'");
            $check->execute([$technician]);
            if (!$check->fetch()) {
                throw new DomainException('Select an active technician.');
            }
            $note = postedText('instructions', 2000, false);
            $deadlineInput = postedText('expected_completion_at', 16);
            $deadline = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $deadlineInput,
                new DateTimeZone('Africa/Kampala'));
            if (!$deadline || $deadline->format('Y-m-d\TH:i') !== $deadlineInput
                || $deadline <= new DateTimeImmutable('now', new DateTimeZone('Africa/Kampala'))
                || $deadline > new DateTimeImmutable('+1 year', new DateTimeZone('Africa/Kampala'))) {
                throw new DomainException('Choose a future expected completion date within one year.');
            }
            $insert = $pdo->prepare("INSERT INTO repair_assignments
                (fault_report_id, technician_id, assigned_by, status, assignment_notes,
                 expected_completion_at, assigned_at, updated_at)
                VALUES (?, ?, ?, 'ASSIGNED', ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
            $insert->execute([$id, $technician, $actor, $note,
                $deadline->format('Y-m-d H:i:s')]);
            $next = 'ASSIGNED';
            $public = 'A technician has been assigned to the reported fault.';
            $message = 'Technician assigned.';
        } elseif ($action === 'accept') {
            assertAssignedTechnician($assignment, $actor, ['ASSIGNED']);
            $pdo->prepare("UPDATE repair_assignments SET status = 'ACCEPTED', accepted_at = CURRENT_TIMESTAMP,
                updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                ->execute([(int)$assignment['id']]);
            $public = 'The technician accepted the repair assignment.';
            $message = 'Assignment accepted.';
        } elseif ($action === 'start') {
            if ($status !== 'ASSIGNED') {
                throw new DomainException('This repair cannot be started.');
            }
            assertAssignedTechnician($assignment, $actor, ['ASSIGNED', 'ACCEPTED']);
            $pdo->prepare("UPDATE repair_assignments SET status = 'IN_PROGRESS', started_at = CURRENT_TIMESTAMP,
                updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                ->execute([(int)$assignment['id']]);
            $next = 'IN_PROGRESS';
            $public = 'Repair work is in progress.';
            $message = 'Repair started.';
        } elseif ($action === 'update' || $action === 'complete') {
            if ($status !== 'IN_PROGRESS') {
                throw new DomainException('Start the repair before recording work.');
            }
            assertAssignedTechnician($assignment, $actor, ['IN_PROGRESS']);
            $note = postedText('repair_note', 2000);
            $cost = postedText('repair_cost', 17);
            if (!preg_match('/^(?:0|[1-9][0-9]{0,11})(?:\.[0-9]{1,2})?$/D', $cost)) {
                throw new DomainException('Enter a valid repair cost, or 0 if there is no cost.');
            }
            $pdo->prepare('INSERT INTO repair_updates
                (assignment_id, updated_by, update_type, notes, repair_cost, photo_path, created_at)
                VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)')
                ->execute([(int)$assignment['id'], $actor, $action === 'complete' ? 'COMPLETED' : 'PROGRESS', $note, $cost, $photoPath]);
            if ($action === 'complete') {
                $pdo->prepare("UPDATE repair_assignments SET status = 'COMPLETED', completed_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                    ->execute([(int)$assignment['id']]);
                $next = 'COMPLETED';
                $public = 'Repair completed; awaiting coordinator verification.';
                $message = 'Repair submitted for verification.';
            } else {
                $public = 'The technician recorded repair progress.';
                $message = 'Repair update recorded.';
            }
        } elseif ($action === 'verify' || $action === 'return') {
            if ($status !== 'COMPLETED' || !$assignment || $assignment['status'] !== 'COMPLETED') {
                throw new DomainException('Only completed repairs can be verified.');
            }
            $note = postedText('verification_note', 2000);
            $next = $action === 'verify' ? 'VERIFIED' : 'RETURNED';
            $pdo->prepare('INSERT INTO repair_verifications
                (assignment_id, fault_report_id, verified_by, decision, comments, verified_at)
                VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)')
                ->execute([(int)$assignment['id'], $id, $actor,
                    $action === 'verify' ? 'APPROVED' : 'RETURNED', $note]);
            if ($action === 'verify' && $fault['water_point_id'] !== null) {
                $pointStatus = postedText('water_point_status', 30);
                if (!in_array($pointStatus, ['FUNCTIONAL', 'PARTIALLY_FUNCTIONAL', 'NON_FUNCTIONAL'], true)) {
                    throw new DomainException('Select the verified water point condition.');
                }
                $pdo->prepare('UPDATE water_points SET status = ? WHERE id = ?')
                    ->execute([$pointStatus, (int)$fault['water_point_id']]);
            }
            $public = $action === 'verify'
                ? 'The coordinator verified the repair and recorded the current water point condition.'
                : 'The repair was returned for further work.';
            $message = $action === 'verify' ? 'Restoration verified.' : 'Repair returned for further work.';
        } elseif ($action === 'close') {
            if ($status !== 'VERIFIED') {
                throw new DomainException('Only verified repairs can be closed.');
            }
            $next = 'CLOSED';
            $public = 'The verified fault report has been closed.';
            $message = 'Fault report closed.';
        } else {
            throw new DomainException('Unknown workflow action.');
        }

        if ($next !== $status) {
            changeFaultStatus($pdo, $id, $next);
        }
        workflowHistory($pdo, $id, $next, $actor, $public, $note ?: null);
        writeAuditLog($pdo, $actor, 'REPAIR_' . strtoupper($action), 'FAULT_REPORT', $id, $public);
        $pdo->commit();
        return $message;
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($photoPath !== null) {
            @unlink(dirname(__DIR__) . '/public/' . $photoPath);
        }
        throw $error;
    }
}

function assertAssignedTechnician(?array $assignment, int $actor, array $statuses): void
{
    if (!$assignment || (int)$assignment['technician_id'] !== $actor
        || !in_array($assignment['status'], $statuses, true)) {
        throw new DomainException('This assignment is no longer available to you.');
    }
}
