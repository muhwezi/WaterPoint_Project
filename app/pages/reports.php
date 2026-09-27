<?php
require_once dirname(__DIR__) . '/layouts/staff.php';
staffHeader('Reports', 'reports.php', ['reports.css']);
?>
<main class="container reports-page">
  <div class="report-heading">
    <div><p class="report-kicker">STAFF REPORTS</p><h2>Repair and fault reports</h2>
      <p>Current work and technician repairs recorded in the selected period.</p></div>
    <button type="button" class="print-button" onclick="window.print()">Print report</button>
  </div>

  <?php if ($error): ?><p class="report-error" role="alert"><?= h($error) ?> Showing the last 30 days.</p><?php endif; ?>
  <form method="get" action="reports.php" class="report-filters">
    <label for="from-date">Completed from</label>
    <input id="from-date" name="from" type="date" value="<?= h($from) ?>" required>
    <label for="to-date">To</label>
    <input id="to-date" name="to" type="date" value="<?= h($to) ?>" required>
    <button type="submit">Apply dates</button>
  </form>
  <p class="filter-help">The date filter applies to completed technician work. Open faults and overdue assignments show their current position.</p>

  <section class="report-metrics" aria-label="Current fault summary">
    <article><strong><?= (int)$summary['total_open'] ?></strong><span>Open faults</span></article>
    <article><strong><?= (int)$summary['awaiting_review'] ?></strong><span>Awaiting review</span></article>
    <article><strong><?= (int)$summary['awaiting_assignment'] ?></strong><span>Awaiting assignment</span></article>
    <article><strong><?= (int)$summary['awaiting_verification'] ?></strong><span>Awaiting verification</span></article>
    <article class="overdue-metric"><strong><?= $overdueCount ?></strong><span>Overdue assignments</span></article>
  </section>

  <section class="report-section" aria-labelledby="overdue-title">
    <div class="section-heading"><h3 id="overdue-title">Overdue assignments</h3><p><?= $overdueCount ?> total<?= $overdueCount > 100 ? '; showing first 100' : '' ?></p></div>
    <div class="report-table-wrap"><table><thead><tr>
      <th scope="col">Reference</th><th scope="col">Location</th><th scope="col">Technician</th>
      <th scope="col">Expected by</th><th scope="col">Days overdue</th><th scope="col">Status</th>
    </tr></thead><tbody>
    <?php foreach ($overdue as $job): ?><tr>
      <td><a href="fault-details.php?reference=<?= rawurlencode($job['fault_reference']) ?>"><?= h($job['fault_reference']) ?></a></td>
      <td><?= h($job['location']) ?>, <?= h($job['village']) ?></td>
      <td><?= h($job['technician_name']) ?></td><td><?= h($job['expected_completion_at']) ?></td>
      <td><?= (int)$job['days_overdue'] ?></td><td><?= h(str_replace('_', ' ', $job['assignment_status'])) ?></td>
    </tr><?php endforeach; ?>
    <?php if (!$overdue): ?><tr><td colspan="6" class="empty-row">No overdue assignments.</td></tr><?php endif; ?>
    </tbody></table></div>
  </section>

  <section class="report-section" aria-labelledby="open-title">
    <div class="section-heading"><h3 id="open-title">Open faults</h3><p><?= (int)$summary['total_open'] ?> total<?= (int)$summary['total_open'] > 100 ? '; showing first 100 by priority and age' : '' ?></p></div>
    <div class="report-table-wrap"><table><thead><tr>
      <th scope="col">Reference</th><th scope="col">Location</th><th scope="col">Priority</th>
      <th scope="col">Status</th><th scope="col">Technician</th><th scope="col">Reported</th>
    </tr></thead><tbody>
    <?php foreach ($openFaults as $fault): ?><tr>
      <td><a href="fault-details.php?reference=<?= rawurlencode($fault['fault_reference']) ?>"><?= h($fault['fault_reference']) ?></a></td>
      <td><?= h($fault['location']) ?>, <?= h($fault['village']) ?></td>
      <td><?= h($fault['priority']) ?></td><td><?= h(str_replace('_', ' ', $fault['status'])) ?></td>
      <td><?= h($fault['technician_name'] ?: 'Not assigned') ?></td><td><?= h($fault['reported_at']) ?></td>
    </tr><?php endforeach; ?>
    <?php if (!$openFaults): ?><tr><td colspan="6" class="empty-row">No open faults.</td></tr><?php endif; ?>
    </tbody></table></div>
  </section>

  <section class="report-section" aria-labelledby="technicians-title">
    <div class="section-heading"><h3 id="technicians-title">Completed work by technician</h3><p><?= h($from) ?> to <?= h($to) ?></p></div>
    <div class="report-table-wrap"><table><thead><tr>
      <th scope="col">Technician</th><th scope="col">Completed assignments</th>
      <th scope="col">Verified</th><th scope="col">Recorded cost (UGX)</th>
    </tr></thead><tbody>
    <?php foreach ($technicianWork as $row): ?><tr>
      <td><?= h($row['full_name']) ?> <small>(<?= h($row['username']) ?>)</small></td>
      <td><?= (int)$row['completed_jobs'] ?></td><td><?= (int)$row['verified_jobs'] ?></td>
      <td><?= h(formatReportAmount((string)$row['recorded_cost'])) ?></td>
    </tr><?php endforeach; ?>
    <?php if (!$technicianWork): ?><tr><td colspan="4" class="empty-row">No completed assignments in this period.</td></tr><?php endif; ?>
    </tbody></table></div>
  </section>
</main>
<?php staffFooter(); ?>
