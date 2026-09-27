<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function staffUsers(PDO $pdo): array
{
    return $pdo->query('SELECT id, full_name, username, email, phone, role, status
        FROM users ORDER BY full_name, id')->fetchAll();
}

function createStaffUser(PDO $pdo, int $actor): void
{
    $name = postedText('full_name', 120);
    $username = strtolower(postedText('username', 50));
    if (!preg_match('/^[a-z][a-z0-9._-]{2,49}$/D', $username)) {
        throw new DomainException('Username must start with a letter and use at least three letters, numbers, dots, underscores, or hyphens.');
    }
    $email = postedText('email', 190);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new DomainException('Enter a valid email address.');
    }
    $phone = preg_replace('/[\s()-]+/', '', postedText('phone', 25));
    if (preg_match('/^0([37]\d{8})$/D', $phone, $match)) {
        $phone = '+256' . $match[1];
    } elseif (preg_match('/^256([37]\d{8})$/D', $phone, $match)) {
        $phone = '+256' . $match[1];
    }
    if (!preg_match('/^\+256[37]\d{8}$/D', $phone)) {
        throw new DomainException('Enter a valid Ugandan phone number.');
    }
    $role = postedText('role', 20);
    if (!in_array($role, ['ADMIN', 'COORDINATOR', 'TECHNICIAN'], true)) {
        throw new DomainException('Select a staff role.');
    }
    $password = postedText('password', 72);
    if (strlen($password) < 12 || !preg_match('/[A-Z]/', $password)
        || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)
        || !preg_match('/[^A-Za-z0-9]/', $password)) {
        throw new DomainException('Use at least 12 characters with upper and lower case, a number, and a symbol.');
    }
    $pdo->beginTransaction();
    try {
        $insert = $pdo->prepare("INSERT INTO users
            (full_name, username, email, phone, password_hash, role, status, created_by)
            VALUES (?, ?, ?, ?, ?, ?, 'ACTIVE', ?)");
        $insert->execute([$name, $username, $email, $phone, password_hash($password, PASSWORD_DEFAULT), $role, $actor]);
        writeAuditLog($pdo, $actor, 'USER_CREATED', 'USER', (int)$pdo->lastInsertId(), 'Created ' . $role . ' account.');
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($error instanceof PDOException && $error->getCode() === '23000') {
            throw new DomainException('The username, email, or phone is already in use.');
        }
        throw $error;
    }
}

function changeStaffStatus(PDO $pdo, int $actor, int $id, string $status): void
{
    if ($id === $actor || !in_array($status, ['ACTIVE', 'INACTIVE'], true)) {
        throw new DomainException('This account status cannot be changed.');
    }
    $pdo->beginTransaction();
    try {
        $check = $pdo->prepare('SELECT id, role, status FROM users WHERE id = ? FOR UPDATE');
        $check->execute([$id]);
        $staff = $check->fetch();
        if (!$staff) {
            throw new DomainException('Staff account not found.');
        }
        if ($staff['role'] === 'ADMIN' && $status === 'INACTIVE') {
            $active = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'ADMIN' AND status = 'ACTIVE'")->fetchColumn();
            if ((int)$active < 2) {
                throw new DomainException('At least one active administrator is required.');
            }
        }
        $pdo->prepare('UPDATE users SET status = ? WHERE id = ?')->execute([$status, $id]);
        writeAuditLog($pdo, $actor, 'USER_STATUS_CHANGED', 'USER', $id, 'Account status changed to ' . $status . '.');
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }
}
