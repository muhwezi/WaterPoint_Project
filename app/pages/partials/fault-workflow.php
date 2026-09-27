<?php
// This partial shares the controller's fault and assignment state.
?>
<?php if ($workflowNotice): ?><div class="workflow-success" role="status"><?= h($workflowNotice) ?></div><?php endif; ?>
<?php if ($workflowError): ?><div class="workflow-error" role="alert"><?= h($workflowError) ?></div><?php endif; ?>

<?php if (in_array($fault['status'], ['REVIEWED', 'RETURNED'], true)): ?>
<section class="workflow-card" aria-labelledby="assignment-heading">
  <h3 id="assignment-heading">Assign a technician</h3>
  <?php if (!$technicians): ?><p>Create an active technician account before assigning this fault.</p><?php else: ?>
  <form action="repair-action.php" method="post" class="workflow-form">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <input type="hidden" name="reference" value="<?= h($reference) ?>">
    <input type="hidden" name="workflow_action" value="assign">
    <label for="technician-id">Technician</label>
    <select id="technician-id" name="technician_id" required>
      <option value="">Choose a technician</option>
      <?php foreach ($technicians as $technician): ?>
      <option value="<?= (int)$technician['id'] ?>"><?= h($technician['full_name']) ?> (<?= h($technician['username']) ?>)</option>
      <?php endforeach; ?>
    </select>
    <label for="assignment-instructions">Repair instructions (optional)</label>
    <textarea id="assignment-instructions" name="instructions" maxlength="2000" rows="3"></textarea>
    <label for="expected-completion">Expected completion date and time</label>
    <input id="expected-completion" name="expected_completion_at" type="datetime-local" required>
    <button type="submit">Assign technician</button>
  </form>
  <?php endif; ?>
</section>
<?php endif; ?>

<?php if ($currentAssignment): ?>
<section class="workflow-card" aria-labelledby="repair-heading">
  <h3 id="repair-heading">Repair assignment</h3>
  <p><strong><?= h($currentAssignment['technician_name']) ?></strong> · <?= h(str_replace('_', ' ', $currentAssignment['status'])) ?></p>
  <?php if ($currentAssignment['instructions']): ?><p><?= nl2br(h($currentAssignment['instructions'])) ?></p><?php endif; ?>
  <?php foreach ($repairUpdates as $update): ?>
    <div class="workflow-note"><strong><?= h($update['update_type']) ?></strong>
      <span> · <?= h($update['technician_name']) ?> · <?= h($update['created_at']) ?></span>
      <p><?= nl2br(h($update['notes'])) ?></p>
      <p>Cost recorded: UGX <?= h($update['repair_cost']) ?></p>
      <?php if ($update['photo_path']): ?><a href="<?= h($update['photo_path']) ?>" target="_blank" rel="noopener">View repair photo</a><?php endif; ?>
    </div>
  <?php endforeach; ?>
</section>
<?php endif; ?>

<?php if ($fault['status'] === 'COMPLETED'): ?>
<section class="workflow-card" aria-labelledby="verification-heading">
  <h3 id="verification-heading">Verify restoration</h3>
  <p>Confirm the water service on site. Return the repair if further work is needed.</p>
  <form action="repair-action.php" method="post" class="workflow-form">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <input type="hidden" name="reference" value="<?= h($reference) ?>">
    <label for="verification-note">Verification findings</label>
    <textarea id="verification-note" name="verification_note" maxlength="2000" rows="4" required></textarea>
    <?php if ($fault['water_point_id'] !== null): ?>
      <label for="water-point-status">Water point condition after verification</label>
      <select id="water-point-status" name="water_point_status" required>
        <option value="FUNCTIONAL">Functional</option>
        <option value="PARTIALLY_FUNCTIONAL">Partially functional</option>
        <option value="NON_FUNCTIONAL">Non-functional</option>
      </select>
    <?php endif; ?>
    <button name="workflow_action" value="verify">Verify restored</button>
    <button name="workflow_action" value="return">Return for more work</button>
  </form>
</section>
<?php endif; ?>

<?php if ($fault['status'] === 'VERIFIED'): ?>
<section class="workflow-card"><h3>Close report</h3>
  <p>The repair has been verified. Close the report to complete its record.</p>
  <form method="post" action="repair-action.php" class="workflow-form">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <input type="hidden" name="reference" value="<?= h($reference) ?>">
    <button name="workflow_action" value="close">Close report</button>
  </form>
</section>
<?php endif; ?>
