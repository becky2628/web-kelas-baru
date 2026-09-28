<?php
require_once 'auth.php';

if (!isAdmin()) {
    header('Location: profil.php');
    exit;
}

$siteContent = loadSiteContent();
$profiles = loadProfiles();
$message = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteContent = [
        'home_title' => trim($_POST['home_title'] ?? ''),
        'home_subtitle' => trim($_POST['home_subtitle'] ?? ''),
        'home_cta_profile' => trim($_POST['home_cta_profile'] ?? ''),
        'home_cta_login' => trim($_POST['home_cta_login'] ?? ''),
        'home_welcome_title' => trim($_POST['home_welcome_title'] ?? ''),
        'home_welcome_text' => trim($_POST['home_welcome_text'] ?? ''),
        'feature_1_title' => trim($_POST['feature_1_title'] ?? ''),
        'feature_1_text' => trim($_POST['feature_1_text'] ?? ''),
        'feature_2_title' => trim($_POST['feature_2_title'] ?? ''),
        'feature_2_text' => trim($_POST['feature_2_text'] ?? ''),
        'feature_3_title' => trim($_POST['feature_3_title'] ?? ''),
        'feature_3_text' => trim($_POST['feature_3_text'] ?? ''),
        'feature_4_title' => trim($_POST['feature_4_title'] ?? ''),
        'feature_4_text' => trim($_POST['feature_4_text'] ?? ''),
        'feature_5_title' => trim($_POST['feature_5_title'] ?? ''),
        'feature_5_text' => trim($_POST['feature_5_text'] ?? ''),
        'schedule_title' => trim($_POST['schedule_title'] ?? ''),
        'schedule_subtitle' => trim($_POST['schedule_subtitle'] ?? ''),
        'schedule_list' => trim($_POST['schedule_list'] ?? ''),
        'schedule_note' => trim($_POST['schedule_note'] ?? ''),
        'contact_title' => trim($_POST['contact_title'] ?? ''),
        'contact_subtitle' => trim($_POST['contact_subtitle'] ?? ''),
        'contact_email_title' => trim($_POST['contact_email_title'] ?? ''),
        'contact_email_text' => trim($_POST['contact_email_text'] ?? ''),
        'contact_leader_title' => trim($_POST['contact_leader_title'] ?? ''),
        'contact_leader_text' => trim($_POST['contact_leader_text'] ?? ''),
        'contact_address_text' => trim($_POST['contact_address_text'] ?? ''),
        'gallery_title' => trim($_POST['gallery_title'] ?? ''),
        'gallery_subtitle' => trim($_POST['gallery_subtitle'] ?? ''),
        'gallery_card_1' => trim($_POST['gallery_card_1'] ?? ''),
        'gallery_card_2' => trim($_POST['gallery_card_2'] ?? ''),
        'gallery_card_3' => trim($_POST['gallery_card_3'] ?? ''),
        'gallery_card_4' => trim($_POST['gallery_card_4'] ?? ''),
        'gallery_card_5' => trim($_POST['gallery_card_5'] ?? ''),
        'gallery_card_6' => trim($_POST['gallery_card_6'] ?? ''),
        'gallery_card_7' => trim($_POST['gallery_card_7'] ?? ''),
        'gallery_card_8' => trim($_POST['gallery_card_8'] ?? ''),
        'gallery_photo_1' => $siteContent['gallery_photo_1'] ?? '',
        'gallery_photo_2' => $siteContent['gallery_photo_2'] ?? '',
        'gallery_photo_3' => $siteContent['gallery_photo_3'] ?? '',
        'gallery_photo_4' => $siteContent['gallery_photo_4'] ?? '',
        'gallery_photo_5' => $siteContent['gallery_photo_5'] ?? '',
        'gallery_photo_6' => $siteContent['gallery_photo_6'] ?? '',
        'gallery_photo_7' => $siteContent['gallery_photo_7'] ?? '',
        'gallery_photo_8' => $siteContent['gallery_photo_8'] ?? '',
    ];

    for ($i = 1; $i <= 8; $i++) {
        $fileKey = 'gallery_photo_' . $i;
        $deleteKey = 'delete_gallery_photo_' . $i;
        if (!empty($_POST[$deleteKey]) && !empty($siteContent[$fileKey]) && file_exists(__DIR__ . '/uploads/' . $siteContent[$fileKey])) {
            @unlink(__DIR__ . '/uploads/' . $siteContent[$fileKey]);
            $siteContent[$fileKey] = '';
        }

        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES[$fileKey]['tmp_name'];
            $originalName = $_FILES[$fileKey]['name'];
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                $errorMessage = "Format file foto galeri {$i} tidak didukung.";
                continue;
            }
            $filename = 'gallery_' . $i . '_' . uniqid() . '.' . $extension;
            $uploadPath = __DIR__ . '/uploads/' . $filename;
            if (move_uploaded_file($tmpName, $uploadPath)) {
                if (!empty($siteContent[$fileKey]) && file_exists(__DIR__ . '/uploads/' . $siteContent[$fileKey])) {
                    @unlink(__DIR__ . '/uploads/' . $siteContent[$fileKey]);
                }
                $siteContent[$fileKey] = $filename;
            } else {
                $errorMessage = "Gagal mengunggah foto galeri {$i}.";
            }
        }
    }

    foreach ($profiles as $profileKey => &$profile) {
        $profile['name'] = trim($_POST['profile_' . $profileKey . '_name'] ?? $profile['name']);
        $profile['instagram'] = trim($_POST['profile_' . $profileKey . '_instagram'] ?? $profile['instagram']);
        $profile['bio'] = trim($_POST['profile_' . $profileKey . '_bio'] ?? $profile['bio']);
    }
    unset($profile);

    saveSiteContent($siteContent);
    saveProfiles($profiles);
    $message = 'Perubahan teks situs dan profil siswa berhasil disimpan.';
}

function esc($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Konten TKJ 5</title>
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
        nav a:hover {
            background: rgba(255,255,255,0.16);
        }
        .container {
            max-width: 1080px;
            margin: 32px auto;
            padding: 0 20px 40px;
        }
        .card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            padding: 28px 32px;
            margin-bottom: 24px;
        }
        .card h2 {
            margin-top: 0;
            color: #0f3c67;
        }
        .form-grid {
            display: grid;
            gap: 18px;
        }
        label {
            font-weight: 700;
            color: #112c44;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #d3dce6;
            border-radius: 16px;
            background: #f9fbff;
            color: #1c2e3f;
            box-sizing: border-box;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 12px;
        }
        .button {
            padding: 14px 22px;
            border: none;
            border-radius: 999px;
            background: #0099cc;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
        .success {
            background: #e6ffed;
            border: 1px solid #b7f0c4;
            color: #175a29;
            padding: 16px;
            border-radius: 18px;
            margin-bottom: 20px;
        }
        footer {
            text-align: center;
            padding: 18px 20px;
            color: #5f6c7b;
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
            .card {
                padding: 20px 18px;
                border-radius: 18px;
            }
            .card h2 {
                font-size: 1.3rem;
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
            .card {
                padding: 14px 12px;
                border-radius: 14px;
                margin-bottom: 16px;
            }
            .card h2 {
                font-size: 1.2rem;
            }
            label {
                font-size: 0.9rem;
            }
            input[type="text"], textarea {
                padding: 10px;
                font-size: 0.95rem;
            }
            textarea {
                min-height: 80px;
            }
            .buttons {
                gap: 8px;
            }
            .button {
                padding: 10px 14px;
                font-size: 0.9rem;
                flex: 1;
                min-width: 100px;
            }
            .form-grid {
                gap: 12px;
            }
            .dropzone {
                border: 3px dashed #0099cc;
                border-radius: 16px;
                padding: 40px 20px;
                text-align: center;
                background: #f0f8ff;
                cursor: pointer;
                transition: all 0.2s ease;
                margin-bottom: 20px;
            }
            .dropzone:hover {
                background: #e0f0ff;
                border-color: #0077aa;
            }
            .dropzone.drag-over {
                background: #d0e8ff;
                border-color: #005f8a;
            }
            .gallery-preview {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 12px;
                margin-top: 20px;
            }
            .gallery-item {
                position: relative;
                border-radius: 12px;
                overflow: hidden;
                background: #f0f0f0;
                aspect-ratio: 1;
            }
            .gallery-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .gallery-item-delete {
                position: absolute;
                top: 4px;
                right: 4px;
                background: rgba(255, 0, 0, 0.8);
                color: white;
                border: none;
                border-radius: 50%;
                width: 28px;
                height: 28px;
                cursor: pointer;
                font-size: 1.2rem;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
            }
            .gallery-item-delete:hover {
                background: rgba(255, 0, 0, 1);
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Konten TKJ 5</h1>
        <p>Ubah teks halaman beranda, jadwal, kontak, galeri, dan informasi lain yang ditampilkan pada situs.</p>
    </header>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="profil.php">Profil</a>
        <a href="materi.php">Galeri</a>
        <a href="jadwal.php">Jadwal</a>
        <a href="kontak.php">Kontak</a>
        <a href="admin.php">Admin</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <?php if ($message): ?>
            <div class="success"><?php echo esc($message); ?></div>
        <?php endif; ?>
        <form action="admin.php" method="post" enctype="multipart/form-data">
            <div class="card">
                <h2>Halaman Beranda</h2>
                <div class="form-grid">
                    <label for="home_title">Judul Beranda</label>
                    <input type="text" id="home_title" name="home_title" value="<?php echo esc($siteContent['home_title']); ?>">
                    <label for="home_subtitle">Subjudul Beranda</label>
                    <textarea id="home_subtitle" name="home_subtitle"><?php echo esc($siteContent['home_subtitle']); ?></textarea>
                    <label for="home_cta_profile">Tombol Profil</label>
                    <input type="text" id="home_cta_profile" name="home_cta_profile" value="<?php echo esc($siteContent['home_cta_profile']); ?>">
                    <label for="home_cta_login">Tombol Login</label>
                    <input type="text" id="home_cta_login" name="home_cta_login" value="<?php echo esc($siteContent['home_cta_login']); ?>">
                    <label for="home_welcome_title">Judul Selamat Datang</label>
                    <input type="text" id="home_welcome_title" name="home_welcome_title" value="<?php echo esc($siteContent['home_welcome_title']); ?>">
                    <label for="home_welcome_text">Teks Selamat Datang</label>
                    <textarea id="home_welcome_text" name="home_welcome_text"><?php echo esc($siteContent['home_welcome_text']); ?></textarea>
                </div>
            </div>

            <div class="card">
                <h2>Materi Utama</h2>
                <div class="form-grid">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label for="feature_<?php echo $i; ?>_title">Judul Fitur <?php echo $i; ?></label>
                        <input type="text" id="feature_<?php echo $i; ?>_title" name="feature_<?php echo $i; ?>_title" value="<?php echo esc($siteContent['feature_' . $i . '_title']); ?>">
                        <label for="feature_<?php echo $i; ?>_text">Deskripsi Fitur <?php echo $i; ?></label>
                        <textarea id="feature_<?php echo $i; ?>_text" name="feature_<?php echo $i; ?>_text"><?php echo esc($siteContent['feature_' . $i . '_text']); ?></textarea>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="card">
                <h2>Jadwal</h2>
                <div class="form-grid">
                    <label for="schedule_title">Judul Jadwal</label>
                    <input type="text" id="schedule_title" name="schedule_title" value="<?php echo esc($siteContent['schedule_title']); ?>">
                    <label for="schedule_subtitle">Subjudul Jadwal</label>
                    <textarea id="schedule_subtitle" name="schedule_subtitle"><?php echo esc($siteContent['schedule_subtitle']); ?></textarea>
                    <label for="schedule_list">Daftar Jadwal (format: Hari|Kegiatan, satu per baris)</label>
                    <textarea id="schedule_list" name="schedule_list"><?php echo esc($siteContent['schedule_list']); ?></textarea>
                    <label for="schedule_note">Catatan Jadwal</label>
                    <textarea id="schedule_note" name="schedule_note"><?php echo esc($siteContent['schedule_note']); ?></textarea>
                </div>
            </div>

            <div class="card">
                <h2>Kontak</h2>
                <div class="form-grid">
                    <label for="contact_title">Judul Kontak</label>
                    <input type="text" id="contact_title" name="contact_title" value="<?php echo esc($siteContent['contact_title']); ?>">
                    <label for="contact_subtitle">Subjudul Kontak</label>
                    <textarea id="contact_subtitle" name="contact_subtitle"><?php echo esc($siteContent['contact_subtitle']); ?></textarea>
                    <label for="contact_email_title">Judul Email</label>
                    <input type="text" id="contact_email_title" name="contact_email_title" value="<?php echo esc($siteContent['contact_email_title']); ?>">
                    <label for="contact_email_text">Email</label>
                    <input type="text" id="contact_email_text" name="contact_email_text" value="<?php echo esc($siteContent['contact_email_text']); ?>">
                    <label for="contact_leader_title">Judul Perwakilan</label>
                    <input type="text" id="contact_leader_title" name="contact_leader_title" value="<?php echo esc($siteContent['contact_leader_title']); ?>">
                    <label for="contact_leader_text">Teks Perwakilan</label>
                    <input type="text" id="contact_leader_text" name="contact_leader_text" value="<?php echo esc($siteContent['contact_leader_text']); ?>">
                    <label for="contact_address_text">Alamat Sekolah</label>
                    <textarea id="contact_address_text" name="contact_address_text"><?php echo esc($siteContent['contact_address_text']); ?></textarea>
                </div>
            </div>

            <div class="card">
                <h2>Galeri</h2>
                <div class="form-grid">
                    <label for="gallery_title">Judul Galeri</label>
                    <input type="text" id="gallery_title" name="gallery_title" value="<?php echo esc($siteContent['gallery_title']); ?>">
                    <label for="gallery_subtitle">Subjudul Galeri</label>
                    <textarea id="gallery_subtitle" name="gallery_subtitle"><?php echo esc($siteContent['gallery_subtitle']); ?></textarea>

                    <div class="dropzone" id="dropzone" onclick="document.getElementById('galleryFileInput').click()">
                        <p style="margin: 0; font-size: 3rem; color: #0099cc;">📸</p>
                        <p style="margin: 10px 0 0; color: #0077aa; font-weight: 700;">Drag foto di sini atau klik untuk pilih</p>
                        <p style="margin: 8px 0 0; color: #666; font-size: 0.9rem;">Format: JPG, PNG, GIF, WebP</p>
                    </div>
                    <input type="file" id="galleryFileInput" multiple accept="image/*" style="display:none;">
                    <div id="galleryPreview" class="gallery-preview"></div>
                    <div id="uploadStatus" style="margin-top:16px;"></div>
                </div>
            </div>

            <div class="card">
                <h2>Profil Siswa</h2>
                <div class="form-grid">
                    <?php foreach ($profiles as $profileKey => $profileData): ?>
                        <label for="profile_<?php echo esc($profileKey); ?>_name">Nama Siswa (<?php echo esc($profileData['name']); ?>)</label>
                        <input type="text" id="profile_<?php echo esc($profileKey); ?>_name" name="profile_<?php echo esc($profileKey); ?>_name" value="<?php echo esc($profileData['name']); ?>">
                        <label for="profile_<?php echo esc($profileKey); ?>_instagram">Instagram @</label>
                        <input type="text" id="profile_<?php echo esc($profileKey); ?>_instagram" name="profile_<?php echo esc($profileKey); ?>_instagram" value="<?php echo esc($profileData['instagram']); ?>">
                        <label for="profile_<?php echo esc($profileKey); ?>_bio">Bio singkat</label>
                        <textarea id="profile_<?php echo esc($profileKey); ?>_bio" name="profile_<?php echo esc($profileKey); ?>_bio"><?php echo esc($profileData['bio']); ?></textarea>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="buttons">
                <button class="button" type="submit">Simpan Perubahan</button>
                <a class="button" href="index.php">Kembali ke Beranda</a>
            </div>
        </form>
    </div>
    <footer>
        <p>Admin TKJ 5 &copy; 2026</p>
    </footer>
    <script>
        // Gallery upload drag-drop
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('galleryFileInput');
        const preview = document.getElementById('galleryPreview');
        const status = document.getElementById('uploadStatus');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => {
            dropzone.addEventListener(e, preventDefault, false);
        });

        function preventDefault(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(e => {
            dropzone.addEventListener(e, () => dropzone.classList.add('drag-over'), false);
        });

        ['dragleave', 'drop'].forEach(e => {
            dropzone.addEventListener(e, () => dropzone.classList.remove('drag-over'), false);
        });

        dropzone.addEventListener('drop', (e) => {
            fileInput.files = e.dataTransfer.files;
            handleFiles();
        });

        fileInput.addEventListener('change', handleFiles);

        function handleFiles() {
            if (!fileInput.files || fileInput.files.length === 0) return;
            status.innerHTML = '<p style="color: #0099cc;">Uploading...</p>';

            const formData = new FormData();
            formData.append('action', 'upload');
            Array.from(fileInput.files).forEach(f => formData.append('files[]', f));

            fetch('gallery-api.php', {method: 'POST', body: formData})
                .then(r => r.json())
                .then(data => {
                    let html = '';
                    if (data.errors && data.errors.length) {
                        html += '<div style="color: #a60000; background: #ffecec; padding: 12px; border-radius: 12px;">';
                        data.errors.forEach(e => html += '<p style="margin: 4px 0;">' + e + '</p>');
                        html += '</div>';
                    }
                    if (data.uploaded && data.uploaded.length) {
                        html += '<div style="color: #175a29; background: #e6ffed; padding: 12px; border-radius: 12px;">';
                        html += '<strong>' + data.uploaded.length + ' foto berhasil diupload!</strong></div>';
                        loadGalleryPreview();
                    }
                    status.innerHTML = html;
                    fileInput.value = '';
                })
                .catch(e => {
                    status.innerHTML = '<p style="color: #a60000;">Error: ' + e + '</p>';
                });
        }

        function loadGalleryPreview() {
            fetch('gallery-api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=list'
            })
            .then(r => r.json())
            .then(photos => {
                if (!Array.isArray(photos) || photos.length === 0) {
                    preview.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999;">Belum ada foto.</p>';
                    return;
                }
                preview.innerHTML = photos.map(p => `
                    <div class="gallery-item">
                        <img src="${p.url}" alt="">
                        <button class="gallery-item-delete" onclick="deletePhoto('${p.name.replace("'","\\'")}')">×</button>
                    </div>
                `).join('');
            });
        }

        function deletePhoto(filename) {
            if (!confirm('Hapus foto ini?')) return;

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('filename', filename);

            fetch('gallery-api.php', {method: 'POST', body: formData})
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        status.innerHTML = '<p style="color: #175a29; background: #e6ffed; padding: 12px; border-radius: 12px;">Foto dihapus!</p>';
                        loadGalleryPreview();
                    } else {
                        status.innerHTML = '<p style="color: #a60000;">' + (data.error || 'Error') + '</p>';
                    }
                });
        }

        loadGalleryPreview();
    </script>
</body>
</html>
