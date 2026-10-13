<?= $this->include('layout/header') ?>
<section class="page-heading"><div><p class="eyebrow">YOUR WORKSPACE</p><h1>All tasks</h1><p class="lede">Every task in your schedule, arranged by date.</p></div><div class="record-count"><strong><?= count($tasks) ?></strong><span><?= count($tasks) === 1 ? 'record' : 'records' ?></span></div></section>
<?php if ($tasks === []): ?>
    <div class="empty-state"><div class="empty-icon" aria-hidden="true">○</div><h3>No tasks yet</h3><p>Your task list is empty. Sample tasks can be added by running the database seeder.</p></div>
<?php else: ?>
    <div class="table-wrap"><table><thead><tr><th scope="col">Task</th><th scope="col">Status</th><th scope="col">Scheduled date</th><th scope="col">Created</th></tr></thead><tbody>
    <?php foreach ($tasks as $task): ?><tr><td class="task-title"><?= esc($task['title']) ?></td><td><span class="status <?= esc(str_replace(' ', '-', $task['status'])) ?>"><?= esc(ucwords($task['status'])) ?></span></td><td><?= esc(date('M j, Y', strtotime($task['task_date']))) ?></td><td class="muted"><?= esc(date('M j, Y · g:i a', strtotime($task['created_at']))) ?></td></tr><?php endforeach ?>
    </tbody></table></div>
<?php endif ?>
<?= $this->include('layout/footer') ?>
