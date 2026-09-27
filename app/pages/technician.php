<?php
require_once dirname(__DIR__) . '/layouts/staff.php';
staffHeader('Technician Workspace', 'technician.php', ['faults.css', 'workflow.css']);
?>
<main class="container workflow-page">
  <div class="page-heading"><p>STAFF PORTAL</p><h2>My repairs</h2>
    <p>Accept assignments, record progress, and submit completed work for verification.</p></div>
  <?php if ($notice): ?><div class="workflow-success" role="status"><?= h($notice) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="workflow-error" role="alert"><?= h($error) ?></div><?php endif; ?>
  <?php if (!$jobs): ?><section class="workflow-card"><h3>No assignments yet</h3><p>Your assigned repairs will appear here.</p></section><?php endif; ?>
  <div class="workflow-grid">
    <section class="workflow-card" aria-label="My assignments">
      <h3>Assignments</h3>
      <?php foreach ($jobs as $job): ?>
        <a class="job-link<?= $reference === $job['fault_reference'] ? ' selected' : '' ?>"
           href="technician.php?reference=<?= rawurlencode($job['fault_reference']) ?>">
          <strong><?= h($job['fault_reference']) ?></strong>
          <span><?= h(($job['water_point_name'] ?: $job['fault_location']) ?: $job['village']) ?></span>
          <small><?= h(str_replace('_', ' ', $job['assignment_status'])) ?> · <?= h($job['priority']) ?></small>
        </a>
      <?php endforeach; ?>
    </section>
    <?php if ($selected): ?>
    <section class="workflow-card" aria-labelledby="selected-repair">
      <h3 id="selected-repair">Repair <?= h($selected['fault_reference']) ?></h3>
      <p><strong><?= h($selected['water_point_name'] ?: $selected['fault_location']) ?></strong>, <?= h($selected['village']) ?></p>
      <p><?= h(str_replace('_', ' ', $selected['fault_type'])) ?> · <?= h($selected['fault_status']) ?></p>
      <p><?= nl2br(h($selected['description'])) ?></p>
      <?php if ($selected['instructions']): ?><div class="workflow-note"><strong>Coordinator instructions</strong><p><?= nl2br(h($selected['instructions'])) ?></p></div><?php endif; ?>
      <p><strong>Expected completion:</strong> <?= h($selected['expected_completion_at']) ?></p>
      <?php if ($selected['assignment_status'] === 'ASSIGNED'): ?>
        <form method="post" action="repair-action.php" class="workflow-form">
          <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
          <input type="hidden" name="reference" value="<?= h($selected['fault_reference']) ?>">
          <button name="workflow_action" value="accept">Accept assignment</button>
          <button name="workflow_action" value="start">Start repair</button>
        </form>
      <?php elseif ($selected['assignment_status'] === 'ACCEPTED'): ?>
        <form method="post" action="repair-action.php" class="workflow-form">
          <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
          <input type="hidden" name="reference" value="<?= h($selected['fault_reference']) ?>">
          <button name="workflow_action" value="start">Start repair</button>
        </form>
      <?php elseif ($selected['assignment_status'] === 'IN_PROGRESS' && $selected['fault_status'] === 'IN_PROGRESS'): ?>
        <form method="post" action="repair-action.php" enctype="multipart/form-data" class="workflow-form">
          <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
          <input type="hidden" name="reference" value="<?= h($selected['fault_reference']) ?>">
          <label for="repair-note">Work carried out or progress update</label>
          <textarea id="repair-note" name="repair_note" required maxlength="2000" rows="5"></textarea>
          <label for="repair-cost">Cost for this update (UGX; enter 0 if none)</label>
          <input id="repair-cost" name="repair_cost" type="number" min="0" max="999999999999.99" step="0.01" value="0" required>
          <label for="repair-photo">Repair photo (optional, JPG, PNG or WebP, up to 5 MB)</label>
          <input id="repair-photo" name="repair_photo" type="file" accept="image/jpeg,image/png,image/webp" capture="environment">
          <div class="workflow-form"><button name="workflow_action" value="update">Save progress</button>
          <button name="workflow_action" value="complete">Submit completed repair</button></div>
        </form>
      <?php else: ?><p>This assignment is awaiting coordinator action or has finished.</p><?php endif; ?>
    </section>
    <?php endif; ?>
  </div>
</main>
<?php staffFooter(); ?>
