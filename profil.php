<?php
require_once 'auth.php';

$user = getCurrentUser();
$profiles = loadProfiles();
$isAdmin = isset($user['role']) && $user['role'] === 'admin';
$currentProfileKey = $user['key'] ?? null;
$currentProfile = (!$isAdmin && $currentProfileKey) ? ($profiles[$currentProfileKey] ?? null) : null;
$selectedProfileKey = isset($_GET['student']) ? trim($_GET['student']) : null;
$selectedProfile = $selectedProfileKey && isset($profiles[$selectedProfileKey]) ? $profiles[$selectedProfileKey] : null;
$editMode = isset($_GET['edit']) && $user !== null && (!$isAdmin || $selectedProfile !== null);
$message = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $profileKey = $_POST['profile_key'] ?? '';

    if (!userCanEditProfile($profileKey)) {
        $errorMessage = 'Anda tidak dapat mengedit profil siswa ini.';
    } else {
        $bio = trim($_POST['bio'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');

        if ($bio === '' || $instagram === '') {
            $errorMessage = 'Isi bio dan Instagram terlebih dahulu.';
        } else {
            $oldPhotoPath = getProfilePhotoPath($profiles[$profileKey]);

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['photo']['tmp_name'];
                $mimeType = mime_content_type($tmpName);
                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                ];

                if (!isset($allowedTypes[$mimeType])) {
                    $errorMessage = 'Unggah foto JPG, PNG, atau GIF saja.';
                } else {
                    $extension = $allowedTypes[$mimeType];
                    $filename = $profileKey . '_' . time() . '.' . $extension;
                    $uploadPath = __DIR__ . '/uploads/' . $filename;
                    if (!move_uploaded_file($tmpName, $uploadPath)) {
                        $errorMessage = 'Gagal menyimpan foto. Silakan coba lagi.';
                    } else {
                        if ($oldPhotoPath && basename($oldPhotoPath) !== $filename) {
                            @unlink($oldPhotoPath);
                        }
                        $profiles[$profileKey]['photo_filename'] = $filename;
                    }
                }
            }

            if ($errorMessage === '') {
                $profiles[$profileKey]['bio'] = $bio;
                $profiles[$profileKey]['instagram'] = preg_replace('/[^a-zA-Z0-9_\.]/', '', $instagram);
                saveProfiles($profiles);
                $currentProfile = $profiles[$profileKey];
                $message = 'Profil berhasil diperbarui.';
                $editMode = false;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Kelas TKJ 5</title>
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
            text-align: center;
            padding: 42px 24px;
            transition: padding 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }
        header h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            margin: 0;
            letter-spacing: -0.04em;
        }
        header p {
            margin: 14px auto 0;
            max-width: 820px;
            line-height: 1.75;
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
            padding: 12px 18px;
            text-decoration: none;
            font-weight: 700;
            border-radius: 999px;
            transition: background 0.2s ease;
        }
        body.scrolled header {
            padding: 20px 24px;
            transform: translateY(-2px);
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.18);
        }
        body.scrolled nav {
            background: rgba(0, 119, 170, 0.96);
        }
        nav a:hover {
            background: rgba(255, 255, 255, 0.16);
        }
        .container {
            max-width: 1080px;
            margin: 32px auto;
            padding: 0 20px 40px;
        }
        .notice, .success, .error, section {
            border-radius: 20px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }
        .notice, .success, .error {
            padding: 20px;
            margin-bottom: 24px;
        }
        .notice {
            background: #e8f7ff;
            border: 1px solid #c8ecff;
        }
        .success {
            background: #e6ffed;
            border: 1px solid #b7f0c4;
            color: #175a29;
        }
        .error {
            background: #ffecec;
            border: 1px solid #f2b8b8;
            color: #a60000;
        }
        section {
            background: #fff;
            margin-bottom: 24px;
            padding: 28px 30px;
        }
        section h2 {
            margin-top: 0;
            color: #0f3c67;
        }
        .grid, .social-grid {
            display: grid;
            gap: 20px;
        }
        .grid {
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }
        .social-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: 16px;
        }
        .social-card, .profile-card {
            background: #f8fbff;
            border: 1px solid #e6eff8;
        }
        .social-card {
            padding: 20px;
            display: grid;
            gap: 12px;
            text-align: center;
            border-radius: 18px;
            min-width: 0;
        }
        .social-card a {
            color: #0077aa;
            text-decoration: none;
            font-weight: 700;
            min-width: 0;
        }
        .social-card strong {
            color: #0d3f64;
            font-size: 0.95rem;
        }
        .social-card span {
            color: #4e5c70;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
        }
        .social-photo {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0099cc, #0077aa);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0 auto;
            overflow: hidden;
        }
        .social-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .profile-card {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 20px;
            align-items: center;
            padding: 24px;
            text-align: left;
            min-width: 0;
        }
        .profile-card img {
            width: 160px;
            height: 160px;
            max-width: 100%;
            border-radius: 24px;
            object-fit: cover;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        }
        .profile-info {
            text-align: left;
        }
        .profile-info {
            display: grid;
            gap: 10px;
        }
        .profile-actions {
            margin-top: 18px;
        }
        .button {
            padding: 14px 20px;
            border: none;
            border-radius: 999px;
            background: #0099cc;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(0,0,0,0.12);
        }
        .button-secondary {
            background: #5f6c7d;
            color: #fff;
        }
        label {
            display: block;
            font-weight: 700;
            color: #112c44;
            margin-bottom: 8px;
        }
        input[type="text"], textarea, input[type="email"], input[type="file"] {
            width: 100%;
            padding: 14px;
            border: 1px solid #d3dce6;
            border-radius: 16px;
            background: #f9fbff;
            color: #1c2e3f;
            box-sizing: border-box;
            outline: none;
        }
        input[type="text"]:focus, textarea:focus, input[type="email"]:focus, input[type="file"]:focus {
            border-color: #0099cc;
            box-shadow: 0 0 0 3px rgba(0,153,204,0.15);
        }
        textarea {
            min-height: 140px;
            resize: vertical;
        }
        footer {
            background: #0077aa;
            color: #fff;
            text-align: center;
            padding: 18px 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
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
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .timeline li span {
            font-weight: 700;
            color: #0d3f64;
        }
        .social-photo.clickable,
        .profile-card img.clickable {
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .social-photo.clickable:hover,
        .profile-card img.clickable:hover {
            transform: scale(1.05);
        }
        .photo-lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.92);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .photo-lightbox.active {
            display: flex;
        }
        .photo-lightbox-content {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
        }
        .photo-lightbox img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 8px;
        }
        .photo-lightbox-close {
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: #fff;
            font-size: 2rem;
            cursor: pointer;
            font-weight: 700;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-lightbox-close:hover {
            opacity: 0.8;
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
            section, .notice, .success, .error {
                padding: 16px 18px;
                border-radius: 16px;
            }
            .profile-card {
                grid-template-columns: 1fr;
                padding: 16px;
                gap: 16px;
                text-align: center;
            }
            .profile-card img {
                width: 120px;
                height: 120px;
            }
            }
            .social-grid {
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 12px;
            }
            .social-card {
                padding: 14px;
            }
            .social-photo {
                width: 72px;
                height: 72px;
                font-size: 1.1rem;
            }
            label {
                margin-bottom: 6px;
            }
            input[type="text"], textarea, input[type="email"], input[type="file"] {
                padding: 12px;
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
            section, .notice, .success, .error {
                padding: 12px 14px;
                border-radius: 14px;
                margin-bottom: 16px;
            }
            section h2 {
                font-size: 1.3rem;
            }
            .profile-card {
                grid-template-columns: 1fr;
                padding: 12px;
                gap: 12px;
                text-align: center;
            }
            .profile-card img {
                width: 100px;
                height: 100px;
                border-radius: 16px;
            }
            .social-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }
            .social-card {
                padding: 10px;
                gap: 8px;
            }
            .social-photo {
                width: 60px;
                height: 60px;
                font-size: 0.9rem;
            }
            .social-card strong {
                font-size: 0.8rem;
            }
            .social-card span {
                font-size: 0.75rem;
            }
            .button {
                padding: 12px 16px;
                font-size: 0.9rem;
                width: 100%;
                margin-bottom: 8px;
            }
            label {
                font-size: 0.9rem;
                margin-bottom: 4px;
            }
            input[type="text"], textarea, input[type="email"], input[type="file"] {
                padding: 10px;
                font-size: 0.95rem;
            }
            textarea {
                min-height: 100px;
            }
            .photo-lightbox-close {
                width: 36px;
                height: 36px;
                font-size: 1.5rem;
                top: -36px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Profil Kelas TKJ 5</h1>
        <p>Informasi lengkap tentang kelas dan jurusan.</p>
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
        <?php if ($message): ?>
            <div class="success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($user): ?>
            <section class="notice">
                <p>Halo <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>, Anda login sebagai <?php echo ($isAdmin) ? 'Admin' : 'Siswa'; ?>.</p>
                <?php if ($isAdmin): ?>
                    <p>Anda bisa mengedit apapun. Gunakan dengan bijak!</p>
                <?php else: ?>
                    <p>Hanya profil Anda yang dapat diedit.</p>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <section class="notice">
                <p>Anda sedang melihat situs sebagai pengunjung. Login dengan kode atau username untuk mengedit profil siswa Anda.</p>
                <?php if (shouldShowLogin()): ?>
                    <p><a class="button" href="login.php">Login</a></p>
                <?php endif; ?>
                <?php if (isVisitor()): ?>
                    <p><a class="button button-secondary" href="login.php?clearvisitor=1">Kembali</a></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if ($selectedProfile): ?>
            <section>
                <h2>Profil <?php echo htmlspecialchars($selectedProfile['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <div class="profile-card">
                    <div>
                        <div class="social-photo<?php echo ($selectedPhotoUrl = getProfilePhotoUrl($selectedProfile)) ? ' clickable' : ''; ?>" <?php if ($selectedPhotoUrl): ?>onclick="openPhotoLightbox('<?php echo htmlspecialchars($selectedPhotoUrl, ENT_QUOTES, 'UTF-8'); ?>')"<?php endif; ?>>
                            <?php if ($selectedPhotoUrl): ?>
                                <img src="<?php echo htmlspecialchars($selectedPhotoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto <?php echo htmlspecialchars($selectedProfile['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                                <?php echo getInitials($selectedProfile['name']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($selectedProfile['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><strong>Instagram:</strong> @<?php echo htmlspecialchars($selectedProfile['instagram'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($selectedProfile['bio'], ENT_QUOTES, 'UTF-8')); ?></p>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section>
            <h2>Data Profil Siswa</h2>
            <?php if ($user && ($currentProfile || $selectedProfile)): ?>
                <?php $profileToUse = $isAdmin ? $selectedProfile : $currentProfile; ?>
                <?php if ($profileToUse): ?>
                    <?php if ($editMode): ?>
                        <form action="profil.php?edit=1<?php echo $selectedProfileKey ? '&student=' . urlencode($selectedProfileKey) : ''; ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="profile_key" value="<?php echo htmlspecialchars($profileToUse === $currentProfile ? $currentProfileKey : $selectedProfileKey, ENT_QUOTES, 'UTF-8'); ?>">
                            <label for="photo">Unggah Foto Profil</label>
                            <input id="photo" type="file" name="photo" accept="image/png, image/jpeg, image/gif">
                            <?php $photoUrl = getProfilePhotoUrl($profileToUse); ?>
                            <?php if ($photoUrl): ?>
                                <p>Foto saat ini:</p>
                                <img src="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto <?php echo htmlspecialchars($profileToUse['name'], ENT_QUOTES, 'UTF-8'); ?>" style="width:120px;height:120px;border-radius:16px;object-fit:cover;display:block;margin-bottom:12px;">
                            <?php endif; ?>
                            <label for="instagram">Instagram</label>
                            <input id="instagram" type="text" name="instagram" value="<?php echo htmlspecialchars($profileToUse['instagram'], ENT_QUOTES, 'UTF-8'); ?>">
                            <label for="bio">Bio singkat</label>
                            <textarea id="bio" name="bio"><?php echo htmlspecialchars($profileToUse['bio'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <div class="profile-actions">
                                <button class="button" type="submit">Simpan Profil</button>
                                <a class="button button-secondary" href="profil.php">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="profile-card">
                            <div>
                                <div class="social-photo<?php echo ($photoUrl = getProfilePhotoUrl($profileToUse)) ? ' clickable' : ''; ?>" <?php if ($photoUrl): ?>onclick="openPhotoLightbox('<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>')"<?php endif; ?>>
                                    <?php if ($photoUrl): ?>
                                        <img src="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto <?php echo htmlspecialchars($profileToUse['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php else: ?>
                                        <?php echo getInitials($profileToUse['name']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="profile-info">
                                <h3><?php echo htmlspecialchars($profileToUse['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p><strong>Instagram:</strong> @<?php echo htmlspecialchars($profileToUse['instagram'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><?php echo nl2br(htmlspecialchars($profileToUse['bio'], ENT_QUOTES, 'UTF-8')); ?></p>
                                <div class="profile-actions">
                                    <a class="button" href="profil.php?edit=1<?php echo $selectedProfileKey ? '&student=' . urlencode($selectedProfileKey) : ''; ?>">Edit Profil</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php elseif ($user && $isAdmin): ?>
                <p>Anda login sebagai admin. Pilih seorang siswa melalui daftar di bawah untuk melihat atau mengedit profilnya.</p>
            <?php elseif ($user): ?>
                <p>Profil Anda tidak ditemukan. Silakan login ulang atau hubungi admin.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Instagram Siswa TKJ 5</h2>
            <p>Daftar akun Instagram setiap siswa kelas TKJ 5. Klik profil untuk melihat bio singkat.</p>
            <div class="social-grid">
                <?php foreach ($studentNames as $studentName):
                    $profileKey = normalizeName($studentName);
                    $profileData = $profiles[$profileKey] ?? null;
                    $handle = $profileData ? $profileData['instagram'] : toInstagramHandle($studentName);
                    $initials = getInitials($studentName);
                    $photoUrl = $profileData ? getProfilePhotoUrl($profileData) : null;
                ?>
                <div class="social-card">
                    <a class="card-link" href="profil.php?student=<?php echo htmlspecialchars($profileKey, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="social-photo<?php echo $photoUrl ? ' clickable' : ''; ?>"<?php if ($photoUrl): ?> onclick="event.stopPropagation(); openPhotoLightbox('<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>')"<?php endif; ?>>
                            <?php if ($photoUrl): ?>
                                <img src="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto <?php echo htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                                <?php echo $initials; ?>
                            <?php endif; ?>
                        </div>
                        <span><?php echo htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8'); ?></span>
                    </a>
                    <div><a href="https://instagram.com/<?php echo htmlspecialchars($handle, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">@<?php echo htmlspecialchars($handle, ENT_QUOTES, 'UTF-8'); ?></a></div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    <footer>
        <p>TKJ 5 &copy; 2026</p>
    </footer>
    <div id="photoLightbox" class="photo-lightbox">
        <div class="photo-lightbox-content">
            <button class="photo-lightbox-close" onclick="closePhotoLightbox()">×</button>
            <img id="lightboxImage" src="" alt="Photo">
        </div>
    </div>
    <script>
        window.addEventListener('scroll', function() {
            document.body.classList.toggle('scrolled', window.scrollY > 20);
        });
        
        function openPhotoLightbox(src) {
            document.getElementById('lightboxImage').src = src;
            document.getElementById('photoLightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closePhotoLightbox() {
            document.getElementById('photoLightbox').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        document.getElementById('photoLightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closePhotoLightbox();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoLightbox();
            }
        });
    </script>
</body>
</html>
