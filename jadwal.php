<?php require_once 'auth.php';
$siteContent = loadSiteContent();
$scheduleItems = [];
foreach (explode("\n", $siteContent['schedule_list']) as $line) {
    $parts = explode('|', $line, 2);
    $scheduleItems[] = [
        'day' => trim($parts[0] ?? ''),
        'activity' => trim($parts[1] ?? ''),
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal TKJ 5</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #eef4f8;
            color: #1c2e3f;
        }
        header {
            background: linear-gradient(135deg, #0e7fc2, #005f8a);
            color: #fff;
            padding: 42px 24px;
            text-align: center;
            transition: padding 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }
        header h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3rem);
        }
        header p {
            margin: 14px auto 0;
            max-width: 760px;
            line-height: 1.7;
            opacity: 0.92;
        }
        nav {
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            padding: 14px 20px;
            background: #0077aa;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        nav a {
            color: #fff;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 999px;
            font-weight: 700;
        }
        nav {
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }
        nav a:hover {
            background: rgba(255,255,255,0.16);
        }
        body.scrolled header {
            padding: 24px 24px;
            transform: translateY(-2px);
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.18);
        }
        body.scrolled nav {
            background: rgba(0, 119, 170, 0.96);
        }
        .container {
            max-width: 1080px;
            margin: 32px auto;
            padding: 0 20px 40px;
        }
        section {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            padding: 28px 30px;
            margin-bottom: 24px;
        }
        section h2 {
            margin-top: 0;
            color: #0f3c67;
        }
        .timeline {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 14px;
        }
        .timeline li {
            background: #f8fbff;
            border: 1px solid #dce7f0;
            border-radius: 18px;
            padding: 18px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .timeline li span {
            font-weight: 700;
            color: #0d3f64;
        }
        footer {
            background: #0077aa;
            color: #fff;
            text-align: center;
            padding: 18px 20px;
        }
        @media (max-width: 768px) {
            header {
                padding: 32px 16px;
            }
            header h1 {
                font-size: 1.8rem;
            }
            nav {
                gap: 8px;
                padding: 10px 12px;
            }
            nav a {
                padding: 10px 14px;
                font-size: 0.9rem;
            }
            .container {
                padding: 0 12px 20px;
                margin: 16px auto;
            }
            section {
                padding: 16px 18px;
                border-radius: 16px;
            }
            .timeline li {
                padding: 14px 16px;
                border-radius: 14px;
            }
        }
        @media (max-width: 480px) {
            header {
                padding: 24px 12px;
            }
            header h1 {
                font-size: 1.4rem;
            }
            header p {
                font-size: 0.9rem;
            }
            nav {
                flex-direction: column;
                gap: 6px;
            }
            nav a {
                width: 100%;
                padding: 10px 12px;
                font-size: 0.85rem;
                border-radius: 12px;
            }
            .container {
                padding: 0 10px 16px;
                margin: 12px auto;
            }
            section {
                padding: 12px 14px;
                border-radius: 14px;
                margin-bottom: 16px;
            }
            section h2 {
                font-size: 1.3rem;
            }
            .timeline {
                gap: 10px;
            }
            .timeline li {
                padding: 12px 14px;
                border-radius: 12px;
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }
            .timeline li span {
                display: block;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($siteContent['schedule_title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($siteContent['schedule_subtitle'], ENT_QUOTES, 'UTF-8')); ?></p>
    </header>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="profil.php">Profil</a>
        <a href="materi.php">Galeri</a>
        <a href="jadwal.php">Jadwal</a>
        <a href="kontak.php">Kontak</a>
        <?php if (isAdmin()): ?>
            <a href="admin.php">Admin</a>
        <?php endif; ?>
        <?php if (isLoggedIn()): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <?php if (shouldShowLogin()): ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
    <div class="container">
        <section>
            <h2><?php echo htmlspecialchars($siteContent['schedule_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <ul class="timeline">
                <?php foreach ($scheduleItems as $item): ?>
                    <li><span><?php echo htmlspecialchars($item['day'], ENT_QUOTES, 'UTF-8'); ?></span> <?php echo htmlspecialchars($item['activity'], ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <section>
            <p><?php echo nl2br(htmlspecialchars($siteContent['schedule_note'], ENT_QUOTES, 'UTF-8')); ?></p>
        </section>
    </div>
    <footer>
        <p>TKJ 5 &copy; 2026</p>
    </footer>
    <script>
        window.addEventListener('scroll', function() {
            document.body.classList.toggle('scrolled', window.scrollY > 20);
        });
    </script>
</body>
</html>
