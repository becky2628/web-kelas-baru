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
    <title>TKJ 5 - Website Kelas</title>
    <style>
        :root {
            --bg: #eef8ff;
            --surface: #ffffff;
            --surface-strong: #f4f9ff;
            --text: #172b4d;
            --muted: #5f6c7b;
            --primary: #1c7ed6;
            --primary-dark: #1864ab;
            --accent: #22b8cf;
            --shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(180deg, #f2fbff 0%, #eef8ff 55%, #f8fafc 100%);
            color: var(--text);
            line-height: 1.65;
        }

        header {
            background: linear-gradient(135deg, #1c7ed6, #22b8cf);
            color: #fff;
            padding: 60px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: padding 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(255,255,255,0.18), transparent 40%), radial-gradient(circle at bottom right, rgba(255,255,255,0.12), transparent 35%);
            pointer-events: none;
        }

        .hero {
            position: relative;
            z-index: 1;
            max-width: 980px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: clamp(2.3rem, 4vw, 4rem);
            margin: 0 0 18px;
            letter-spacing: -0.04em;
        }

        .hero p {
            max-width: 720px;
            margin: 0 auto 28px;
            font-size: 1.05rem;
            color: rgba(255,255,255,.88);
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
        }

        .button,
        .button-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            border-radius: 999px;
            font-weight: 700;
            text-decoration: none;
            transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
        }

        .button {
            background: #fff;
            color: #1c7ed6;
            box-shadow: 0 18px 30px rgba(15, 23, 42, 0.14);
        }

        .button:hover,
        .button-secondary:hover {
            transform: translateY(-1px);
        }

        .button-secondary {
            background: rgba(255,255,255,0.18);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.26);
        }

        nav {
            display: flex;
            justify-content: center;
            gap: 18px;
            flex-wrap: wrap;
            padding: 18px 20px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 999px;
            transition: background .2s ease;
        }

        nav {
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }
        nav a:hover {
            background: rgba(255,255,255,0.18);
        }
        body.scrolled header {
            transform: translateY(-2px);
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.18);
            padding: 48px 24px;
        }
        body.scrolled nav {
            background: rgba(255,255,255,0.22);
        }

        .container {
            max-width: 1080px;
            margin: -40px auto 40px;
            padding: 0 20px 40px;
        }

        .section-card {
            background: var(--surface);
            border-radius: 28px;
            padding: 32px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
        }

        .section-card h2 {
            margin-top: 0;
            font-size: 1.75rem;
        }

        .section-card p {
            color: var(--muted);
            margin-top: 12px;
        }

        .grid {
            display: grid;
            gap: 20px;
        }

        .info-grid,
        .feature-grid {
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .info-box,
        .feature-card {
            background: var(--surface-strong);
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
            border: 1px solid rgba(15, 23, 42, 0.04);
        }

        .info-box h3,
        .feature-card h3 {
            margin-top: 0;
            font-size: 1.15rem;
        }

        .info-box p,
        .feature-card p {
            color: var(--muted);
            margin-bottom: 0;
        }

        .feature-card {
            display: grid;
            gap: 16px;
        }

        .feature-card strong {
            display: inline-block;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .timeline {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 16px;
        }

        .timeline li {
            background: var(--surface-strong);
            border-radius: 18px;
            padding: 20px;
            border: 1px solid rgba(15, 23, 42, 0.04);
        }

        .timeline li span {
            display: inline-flex;
            background: var(--accent);
            color: #fff;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 0.85rem;
            margin-bottom: 10px;
            width: fit-content;
        }

        footer {
            text-align: center;
            color: var(--muted);
            padding: 24px 20px;
        }

        @media (max-width: 720px) {
            header {
                padding: 48px 18px;
            }

            .section-card {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="hero">
            <h1><?php echo htmlspecialchars($siteContent['home_title'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <p><?php echo nl2br(htmlspecialchars($siteContent['home_subtitle'], ENT_QUOTES, 'UTF-8')); ?></p>
            <div class="hero-buttons">
                <a class="button" href="profil.php"><?php echo htmlspecialchars($siteContent['home_cta_profile'], ENT_QUOTES, 'UTF-8'); ?></a>
                <a class="button-secondary" href="login.php"><?php echo htmlspecialchars($siteContent['home_cta_login'], ENT_QUOTES, 'UTF-8'); ?></a>
            </div>
        </div>
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
        <section class="section-card">
            <h2><?php echo htmlspecialchars($siteContent['home_welcome_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($siteContent['home_welcome_text'], ENT_QUOTES, 'UTF-8')); ?></p>
        </section>

        <section class="section-card">
            <div class="info-grid">
                <div class="info-box">
                    <h3>Nama Kelas</h3>
                    <p>TKJ 5</p>
                </div>
                <div class="info-box">
                    <h3>Jurusan</h3>
                    <p>Teknik Komputer dan Jaringan</p>
                </div>
                <div class="info-box">
                    <h3>Tingkat</h3>
                    <p>SMK / Sekolah Menengah Kejuruan</p>
                </div>
            </div>
        </section>

        <section class="section-card">
            <h2>Materi Utama</h2>
            <div class="feature-grid">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="feature-card">
                        <strong><?php echo $i; ?></strong>
                        <h3><?php echo htmlspecialchars($siteContent['feature_' . $i . '_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($siteContent['feature_' . $i . '_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endfor; ?>
            </div>
        </section>

        <section class="section-card">
            <h2><?php echo htmlspecialchars($siteContent['schedule_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($siteContent['schedule_subtitle'], ENT_QUOTES, 'UTF-8')); ?></p>
            <ul class="timeline">
                <?php foreach ($scheduleItems as $item): ?>
                    <li><span><?php echo htmlspecialchars($item['day'], ENT_QUOTES, 'UTF-8'); ?></span> <?php echo htmlspecialchars($item['activity'], ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
            <p style="margin-top:16px; color: var(--muted);"><?php echo htmlspecialchars($siteContent['schedule_note'], ENT_QUOTES, 'UTF-8'); ?></p>
        </section>

        <section class="section-card">
            <h2><?php echo htmlspecialchars($siteContent['contact_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($siteContent['contact_subtitle'], ENT_QUOTES, 'UTF-8')); ?></p>
            <div class="feature-grid">
                <div class="feature-card">
                    <h3><?php echo htmlspecialchars($siteContent['contact_email_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?php echo htmlspecialchars($siteContent['contact_email_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="feature-card">
                    <h3><?php echo htmlspecialchars($siteContent['contact_leader_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?php echo htmlspecialchars($siteContent['contact_leader_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <p style="margin-top:16px; color: var(--muted);"><?php echo nl2br(htmlspecialchars($siteContent['contact_address_text'], ENT_QUOTES, 'UTF-8')); ?></p>
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
