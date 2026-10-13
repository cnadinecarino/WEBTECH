<?= $this->include('layout/header') ?>
<section class="welcome-head">
    <div>
        <p class="eyebrow">YOUR DAY, IN FOCUS</p>
        <h1>Welcome back</h1>
        <p class="lede">A clear view of the tasks you’ve scheduled for today.</p>
    </div>
    <div class="date-chip"><span class="date-dot"></span><?= esc(date('l, F j, Y', strtotime($today))) ?></div>
</section>
<section class="today-summary" aria-label="Today's task total">
    <div><p class="eyebrow">ON THE LIST TODAY</p><div class="count-line"><strong><?= count($tasks) ?></strong><span><?= count($tasks) === 1 ? 'task' : 'tasks' ?> scheduled</span></div></div>
    <div class="summary-note">Keep your next step small and steady.</div>
</section>
<section class="section-block">
    <div class="section-heading"><div><p class="eyebrow">DAILY PLAN</p><h2>Today’s tasks</h2></div><a class="text-link" href="<?= site_url('tasks') ?>">View all tasks <span aria-hidden="true">→</span></a></div>
    <?php if ($tasks === []): ?>
        <div class="empty-state"><div class="empty-icon" aria-hidden="true">○</div><h3>Your day is open</h3><p>There are no tasks scheduled for today. Check your full task list for upcoming plans.</p></div>
    <?php else: ?>
        <div class="today-list">
            <?php foreach ($tasks as $task): ?>
                <article class="today-task">
                    <span class="task-marker <?= esc(str_replace(' ', '-', $task['status'])) ?>" aria-hidden="true"></span>
                    <div class="task-copy"><h3><?= esc($task['title']) ?></h3><p><?= esc(date('M j, Y', strtotime($task['task_date']))) ?></p></div>
                    <span class="status <?= esc(str_replace(' ', '-', $task['status'])) ?>"><?= esc(ucwords($task['status'])) ?></span>
                </article>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</section>
<?= $this->include('layout/footer') ?>
