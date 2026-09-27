<?php
require_once dirname(__DIR__) . '/layouts/staff.php';
staffHeader('Staff Users', 'users.php', ['workflow.css']);
?>
<main class="container workflow-page">
  <div class="page-heading"><h2>Staff users</h2><p>Manage access to fault review and repairs.</p></div>
  <?php if ($notice): ?><div class="workflow-success" role="status"><?= h($notice) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="workflow-error" role="alert"><?= h($error) ?></div><?php endif; ?>
  <?php if (userHasRole('ADMIN')): ?>
  <section class="workflow-card">
    <h3>Create staff account</h3>
    <form action="users.php" method="post" class="workflow-form">
      <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
      <input type="hidden" name="action" value="create">
      <label for="full-name">Full name</label><input id="full-name" name="full_name" required maxlength="120" value="<?= h($_POST['full_name'] ?? '') ?>">
      <label for="username">Username</label><input id="username" name="username" required maxlength="50" value="<?= h($_POST['username'] ?? '') ?>">
      <label for="email">Email</label><input id="email" name="email" type="email" required maxlength="190" value="<?= h($_POST['email'] ?? '') ?>">
      <label for="phone">Phone</label><input id="phone" name="phone" type="tel" required maxlength="25" value="<?= h($_POST['phone'] ?? '') ?>">
      <label for="role">Role</label><select id="role" name="role" required>
        <?php foreach (['TECHNICIAN' => 'Technician', 'COORDINATOR' => 'Coordinator', 'ADMIN' => 'Administrator'] as $key => $label): ?>
          <option value="<?= h($key) ?>" <?= ($_POST['role'] ?? 'TECHNICIAN') === $key ? 'selected' : '' ?>><?= h($label) ?></option>
        <?php endforeach; ?>
      </select>
      <label for="password">Temporary password (12+ characters, mixed case, number, symbol)</label>
      <input id="password" name="password" type="password" autocomplete="new-password" required minlength="12" maxlength="72">
      <button type="submit">Create account</button>
    </form>
  </section>
  <?php endif; ?>
  <section class="workflow-card"><h3>Accounts</h3><div class="table-scroll"><table>
    <thead><tr><th scope="col">Name</th><th scope="col">Username</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead><tbody>
    <?php foreach ($staff as $person): ?><tr>
      <td><?= h($person['full_name']) ?></td><td><?= h($person['username']) ?></td>
      <td><?= h($person['role']) ?></td><td><?= h($person['status']) ?></td>
      <td><?php if (userHasRole('ADMIN') && (int)$person['id'] !== (int)currentUser()['id']): ?>
        <form action="users.php" method="post">
          <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
          <input type="hidden" name="action" value="status">
          <input type="hidden" name="user_id" value="<?= (int)$person['id'] ?>">
          <input type="hidden" name="status" value="<?= $person['status'] === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE' ?>">
          <button type="submit"><?= $person['status'] === 'ACTIVE' ? 'Deactivate' : 'Activate' ?></button>
        </form>
      <?php endif; ?></td>
    </tr><?php endforeach; ?>
    <?php if (!$staff): ?><tr><td colspan="5">No staff accounts found.</td></tr><?php endif; ?>
    </tbody></table></div></section>
</main>
<?php staffFooter(); ?>
