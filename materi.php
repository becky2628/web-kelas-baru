<?php require_once 'auth.php';
$siteContent = loadSiteContent();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri TKJ 5</title>
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
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-top: 22px;
        }
        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 16px;
            }
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
            }
            section {
                padding: 16px 18px;
            }
        }
        @media (max-width: 480px) {
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            header {
                padding: 24px 12px;
            }
            header h1 {
                font-size: 1.5rem;
            }
            nav {
                flex-direction: column;
                gap: 8px;
            }
            nav a {
                width: 100%;
                padding: 10px 12px;
                font-size: 0.85rem;
            }
            .container {
                padding: 0 10px 16px;
                margin: 16px auto;
            }
            section {
                padding: 12px 14px;
                border-radius: 16px;
            }
            .lightbox-nav {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            .lightbox-close {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }
        }
        .gallery-card {
            border-radius: 20px;
            overflow: hidden;
            background: #f8fbff;
            border: 1px solid #dce7f0;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.07);
        }
        .gallery-photo {
            aspect-ratio: 4 / 3;
            background: linear-gradient(135deg, #0099cc 0%, #005f80 100%);
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .gallery-photo:hover {
            transform: scale(1.03);
        }
        .gallery-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .gallery-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            border: none;
            background: transparent;
            padding: 0;
            cursor: zoom-in;
            color: inherit;
            font: inherit;
            text-align: center;
        }
        .gallery-button:hover {
            opacity: 0.95;
        }
        .gallery-button[disabled] {
            cursor: not-allowed;
            opacity: 0.7;
        }
        .gallery-card p {
            margin: 16px;
            color: #334a5e;
            line-height: 1.6;
        }
        .lightbox-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .lightbox-overlay.active {
            display: flex;
        }
        .lightbox-content {
            position: relative;
            max-width: 1000px;
            width: 100%;
            max-height: 90vh;
            overflow: hidden;
            border-radius: 24px;
            background: #111;
        }
        .lightbox-content img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
            max-height: 80vh;
            opacity: 0;
            transform: translateX(0);
            transition: opacity 0.35s ease, transform 0.35s ease;
        }
        .lightbox-content img.loaded {
            opacity: 1;
            transform: translateX(0);
        }
        .lightbox-content img.slide-in-right {
            transform: translateX(30px);
            opacity: 0;
        }
        .lightbox-content img.slide-in-left {
            transform: translateX(-30px);
            opacity: 0;
        }
        .lightbox-caption {
            color: #fff;
            padding: 16px;
            background: rgba(0,0,0,0.6);
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .lightbox-close,
        .lightbox-nav {
            position: absolute;
            top: 16px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.18);
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            display: grid;
            place-items: center;
        }
        .lightbox-close {
            right: 16px;
        }
        .lightbox-nav {
            top: 50%;
            transform: translateY(-50%);
            width: 52px;
            height: 52px;
        }
        .lightbox-prev {
            left: 16px;
        }
        .lightbox-next {
            right: 16px;
        }
        .lightbox-overlay .lightbox-content {
            box-shadow: 0 28px 80px rgba(0,0,0,0.45);
            z-index: 2001;
        }
        .lightbox-close:active,
        .lightbox-nav:active {
            transform: scale(0.95);
        }
        footer {
            background: #0077aa;
            color: #fff;
            text-align: center;
            padding: 18px 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($siteContent['gallery_title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($siteContent['gallery_subtitle'], ENT_QUOTES, 'UTF-8')); ?></p>
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
            <h2>Foto Kenangan</h2>
            <p style="margin-top: 0.75rem; color: #576a7f;">Klik foto untuk melihat versi yang lebih besar. 🖼️</p>
            <div class="gallery-grid">
                <?php for ($i = 1; $i <= 8; $i++): ?>
                    <?php $photoFile = $siteContent['gallery_photo_' . $i] ?? ''; ?>
                    <?php $hasPhoto = $photoFile && file_exists(__DIR__ . '/uploads/' . basename($photoFile)); ?>
                    <div class="gallery-card">
                        <?php if ($hasPhoto): ?>
                            <div class="gallery-photo" onclick="openGalleryLightbox('uploads/<?php echo htmlspecialchars(basename($photoFile), ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars(addslashes($siteContent['gallery_card_' . $i]), ENT_QUOTES, 'UTF-8'); ?>')">
                                <img src="uploads/<?php echo htmlspecialchars(basename($photoFile), ENT_QUOTES, 'UTF-8'); ?>" alt="Foto <?php echo $i; ?>">
                            </div>
                        <?php else: ?>
                            <div class="gallery-photo" style="cursor: default; opacity: 0.6;">
                                <span style="font-size: 3rem; opacity: 0.4;">📸</span>
                            </div>
                        <?php endif; ?>
                        <p><?php echo htmlspecialchars($siteContent['gallery_card_' . $i], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endfor; ?>
            </div>
        </section>
    </div>
    <div id="galleryLightbox" class="lightbox-overlay" onclick="if(event.target === this) closeGalleryLightbox()">
        <div class="lightbox-content">
            <button class="lightbox-close" onclick="event.stopPropagation(); closeGalleryLightbox()">×</button>
            <img id="lightboxImage" src="" alt="Gallery Photo">
            <div class="lightbox-caption" id="lightboxCaption"></div>
        </div>
    </div>
    <footer>
        <p>TKJ 5 &copy; 2026</p>
    </footer>
    <script>
        window.addEventListener('scroll', function() {
            document.body.classList.toggle('scrolled', window.scrollY > 20);
        });
        
        function openGalleryLightbox(src, caption) {
            document.getElementById('lightboxImage').src = src;
            document.getElementById('lightboxCaption').textContent = caption;
            document.getElementById('galleryLightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeGalleryLightbox() {
            document.getElementById('galleryLightbox').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('galleryLightbox').classList.contains('active')) {
                if (e.key === 'Escape') closeGalleryLightbox();
            }
        });
    </script>
</body>
</html>
