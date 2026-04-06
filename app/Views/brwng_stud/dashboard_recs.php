<?php foreach ($recent as $row): ?>
<tr>
    <td class="p-2"><?= esc($row['tool_name']) ?></td>
    <td class="p-2 text-muted"><?= esc($row['borrowed_at']) ?></td>
    <td class="p-2 text-muted"><?= esc($row['time_from']) ?> - <?= esc($row['time_to']) ?></td>
    <td class="p-2 text-muted"><?= esc($row['due_date']) ?></td>
    <td class="p-2">
        <?php if ($row['status'] == 'Active'): ?>
            <span class="badge" style="background:#f0faf0; color:#3a7d44; font-size:11px;">Active</span>
        <?php elseif ($row['status'] == 'Overdue'): ?>
            <span class="badge" style="background:#fff8f0; color:#b35900; font-size:11px;">Overdue</span>
        <?php else: ?>
            <span class="badge" style="background:#f5f5f5; color:#888; font-size:11px;">Returned</span>
        <?php endif; ?>
    </td>
    <td class="p-2">
        <?php if ($row['status'] == 'Active' || $row['status'] == 'Overdue'): ?>
            <button class="btn btn-sm btnReturn"
                style="font-size:11px; border: 0.5px solid #e9e9e9;"
                data-brw_code="<?= esc($row['brw_code']) ?>"
                data-tool_name="<?= esc($row['tool_name']) ?>">
                Return
            </button>
        <?php else: ?>
            <span class="text-muted" style="font-size:11px;">—</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

<?php if (empty($recent)): ?>
<tr>
    <td colspan="6" class="text-center text-muted py-3">
        <small>No borrowings yet.</small>
    </td>
</tr>
<?php endif; ?>