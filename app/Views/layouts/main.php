<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | Simple POS</title>
    <style>
        :root {
            color-scheme: light;
            --primary: #155e75;
            --primary-dark: #164e63;
            --background: #f1f5f9;
            --surface: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --border: #cbd5e1;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: var(--text);
            background: var(--background);
        }

        header { background: var(--primary); color: #fff; }

        .nav-wrap,
        main,
        footer {
            width: min(1100px, calc(100% - 2rem));
            margin-inline: auto;
        }

        .nav-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 0;
        }

        .brand { margin: 0; font-size: 1.25rem; font-weight: 700; }
        nav { display: flex; flex-wrap: wrap; gap: .35rem; }

        nav a {
            padding: .45rem .7rem;
            color: #fff;
            text-decoration: none;
            border-radius: .35rem;
        }

        nav a:hover,
        nav a:focus { background: var(--primary-dark); }

        main { padding: 2.5rem 0; }

        .panel {
            padding: clamp(1.25rem, 4vw, 2.5rem);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: .75rem;
            box-shadow: 0 8px 25px rgba(15, 23, 42, .06);
        }

        h1 { margin-top: 0; color: var(--primary-dark); line-height: 1.2; }
        .lead { max-width: 700px; color: var(--muted); font-size: 1.1rem; }
        .actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.5rem; }

        .button {
            display: inline-block;
            padding: .65rem 1rem;
            color: #fff;
            background: var(--primary);
            border-radius: .4rem;
            text-decoration: none;
        }

        .button:hover,
        .button:focus { background: var(--primary-dark); }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .8rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { color: var(--primary-dark); background: #ecfeff; }
        tbody tr:hover { background: #f8fafc; }
        footer { padding: 1.5rem 0; color: var(--muted); text-align: center; }

        @media (max-width: 700px) {
            .nav-wrap { align-items: flex-start; flex-direction: column; }
            main { padding: 1.5rem 0; }
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-wrap">
            <p class="brand">Simple POS</p>
            <nav aria-label="Main navigation">
                <a href="<?= base_url() ?>">Home</a>
                <a href="<?= base_url('about') ?>">About</a>
                <a href="<?= base_url('customers') ?>">Customer Accounts</a>
                <a href="<?= base_url('users') ?>">User Accounts</a>
            </nav>
        </div>
    </header>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <footer>
        <small>&copy; <?= date('Y') ?> Simple POS Laboratory Project</small>
    </footer>
</body>
</html>
