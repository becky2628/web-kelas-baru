<?php
session_start();

$studentNames = [
    'Acep Agung',
    'Ahmad Jamil',
    'Alimudin',
    'Anisa Aulia',
    'Bella Shinta',
    'Bintang Sri Mulyani',
    'Devi Aulia',
    'Fazar Azlitya',
    'Ferdiansyah',
    'Gia Putra',
    'Gilbran Aqil Azmi',
    'Mochammad Ilyas',
    'Muhamad Abil',
    'Muhamad Zibran',
    'Muhammad Cikal',
    'Muhammad Wildan',
    'Putri Qisty',
    'Raka Siwa',
    'Razki Muhamad Salam',
    'Revan Mardiansyah',
    'Revan Rifkiansah',
    'Rezan Fauzi',
    'Rifki Bangbang',
    'Rizky Firmansyah',
    'Sarah Maulinda',
    'Tedi Salim',
    'Teguh Bayu Pratama',
    'Wina Suryani'
];

$allowedLoginKeys = [
'axp701' => 'Acep Agung',
'jml842' => 'Ahmad Jamil',
'lnq593' => 'Alimudin',
'nsa274' => 'Anisa Aulia',
'bxs915' => 'Bella Shinta',
'btg438' => 'Bintang Sri',
'dlc726' => 'Delis Cantika',
'dva184' => 'Devi Aulia',
'fzl967' => 'Fazar Azlitya',
'fdy352' => 'Ferdiansyah',
'gpr681' => 'Gia Putra',
'gbn247' => 'Gilbran Aqil',
'mil534' => 'Mochammad Ilyas',
'mab893' => 'Muhamad Abil',
'mzh146' => 'Muhamad Zibran',
'mcw705' => 'Muhammad Cikal',
'mws328' => 'Muhammad Wildan',
'pqq614' => 'Putri Qisty',
'rso257' => 'Raka Siwa',
'rms901' => 'Razki Muhamad',
'rmd473' => 'Revan Mardiansyah',
'rrk826' => 'Revan Ripkiansah',
'rpz150' => 'Rezan Pauzi',
'rbs694' => 'Rifki Bangbang',
'rfm312' => 'Rizky Firmansyah',
'sml587' => 'Sarah Maulinda',
'tsm264' => 'Tedi Salim',
'tbp731' => 'Teguh Bayu',
'wsy948' => 'Wina Suryani'
    // Tambahkan kode/username yang diizinkan dan nama siswa yang sesuai di sini.
];

$adminKeys = [
    'adm1n_tkj5.secure-2024',
    'master.control_admin-key',
    'secure_admin.tkj5-2026',
    // Tambahkan satu kode admin di sini. Kode ini boleh mengedit semua profil.
];

function getAllowedStudentNameByKey($key)
{
    global $allowedLoginKeys;
    $lookup = strtolower(trim($key));
    return $allowedLoginKeys[$lookup] ?? null;
}

function isAdminKey($key)
{
    global $adminKeys;
    $lookup = strtolower(trim($key));
    return in_array($lookup, $adminKeys, true);
}

$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$profilesFilePath = __DIR__ . '/profiles.json';
$siteContentFilePath = __DIR__ . '/site_content.json';

function getDefaultSiteContent()
{
    return [
        'home_title' => 'Website Kelas TKJ 5',
        'home_subtitle' => 'Temukan profil, materi, jadwal, dan informasi kontak dengan tampilan yang lebih menarik dan rapi.',
        'home_cta_profile' => 'Lihat Profil',
        'home_cta_login' => 'Login',
        'home_welcome_title' => 'Selamat Datang',
        'home_welcome_text' => 'Website ini dibuat untuk kelas TKJ 5 sebagai pusat informasi yang mudah digunakan, nyaman, dan terlihat profesional.',
        'feature_1_title' => 'Jaringan Komputer Dasar',
        'feature_1_text' => 'Dasar-dasar jaringan, topologi, dan perangkat keras.',
        'feature_2_title' => 'Administrasi Server',
        'feature_2_text' => 'Manajemen server, instalasi, dan konfigurasi layanan.',
        'feature_3_title' => 'Keamanan Jaringan',
        'feature_3_text' => 'Proteksi jaringan, firewall, dan best practice keamanan.',
        'feature_4_title' => 'IoT',
        'feature_4_text' => 'Internet of Things untuk perangkat dan sistem cerdas.',
        'feature_5_title' => 'Pemrograman Dasar',
        'feature_5_text' => 'Logika pemrograman, struktur data sederhana, dan aplikasi.',
        'schedule_title' => 'Jadwal Kegiatan',
        'schedule_subtitle' => 'Jadwal bisa diedit langsung oleh admin untuk menyesuaikan kegiatan kelas.',
        'schedule_list' => "Senin|Jaringan Dasar\nSelasa|Pemrograman Dasar\nRabu|Praktikum Server\nKamis|Keamanan Jaringan\nJumat|Ujian dan evaluasi",
        'schedule_note' => 'Jadwal dapat diperbarui oleh wali kelas atau perwakilan sesuai kalender sekolah.',
        'contact_title' => 'Kontak',
        'contact_subtitle' => 'Hubungi guru pembimbing atau perwakilan kelas dengan mudah.',
        'contact_email_title' => 'Email',
        'contact_email_text' => 'tkj5@sekolah.example',
        'contact_leader_title' => 'Ketua Kelas',
        'contact_leader_text' => 'Nama Siswa',
        'contact_address_text' => 'Jl. Pendidikan No. 10, Kota - mudah dijangkau, dekat transportasi umum.',
        'gallery_title' => 'Galeri Kenangan TKJ 5',
        'gallery_subtitle' => 'Temukan momen-momen terbaik kelas dalam tampilan yang bersih dan profesional.',
        'gallery_card_1' => 'Liburan kelas bersama di luar sekolah.',
        'gallery_card_2' => 'Praktikum jaringan ulang tahun kelas.',
        'gallery_card_3' => 'Menghadiri seminar komputer dan keamanan.',
        'gallery_card_4' => 'Foto kegiatan workshop server dan IT.',
        'gallery_card_5' => 'Belajar bersama di laboratorium jaringan.',
        'gallery_card_6' => 'Pertandingan cerdas cermat kompetensi IT.',
        'gallery_card_7' => 'Perayaan kelulusan dan kenangan bersama.',
        'gallery_card_8' => 'Foto gabungan tim saat presentasi proyek.',
        'gallery_photo_1' => '',
        'gallery_photo_2' => '',
        'gallery_photo_3' => '',
        'gallery_photo_4' => '',
        'gallery_photo_5' => '',
        'gallery_photo_6' => '',
        'gallery_photo_7' => '',
        'gallery_photo_8' => '',
    ];
}

function loadSiteContent()
{
    global $siteContentFilePath;
    $default = getDefaultSiteContent();
    if (file_exists($siteContentFilePath)) {
        $content = json_decode(file_get_contents($siteContentFilePath), true);
        if (is_array($content)) {
            return array_replace($default, $content);
        }
    }
    return $default;
}

function saveSiteContent($content)
{
    global $siteContentFilePath;
    file_put_contents($siteContentFilePath, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function getSiteContentValue($key, $default = '')
{
    $content = loadSiteContent();
    return isset($content[$key]) ? $content[$key] : $default;
}

function getProfilePhotoUrl($profile)
{
    global $uploadDir;
    $filename = '';
    if (!empty($profile['photo_filename'])) {
        $filename = $profile['photo_filename'];
    } elseif (!empty($profile['photo_id'])) {
        $filename = $profile['photo_id'];
    }
    if ($filename === '') {
        return null;
    }
    $filename = basename($filename);
    $path = $uploadDir . '/' . $filename;
    if (file_exists($path)) {
        return 'uploads/' . $filename;
    }
    return null;
}

function getProfilePhotoPath($profile)
{
    global $uploadDir;
    $filename = '';
    if (!empty($profile['photo_filename'])) {
        $filename = $profile['photo_filename'];
    } elseif (!empty($profile['photo_id'])) {
        $filename = $profile['photo_id'];
    }
    if ($filename === '') {
        return null;
    }
    $filename = basename($filename);
    $path = $uploadDir . '/' . $filename;
    return file_exists($path) ? $path : null;
}

function deleteProfilePhotoFile(&$profile)
{
    $path = getProfilePhotoPath($profile);
    if ($path !== null) {
        @unlink($path);
    }
    $profile['photo_filename'] = '';
}

function normalizeName($name)
{
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
    return trim($slug, '_');
}

function toInstagramHandle($name)
{
    return normalizeName($name);
}

function getInitials($name)
{
    $parts = preg_split('/\s+/', trim($name));
    $initials = '';
    foreach ($parts as $part) {
        $initials .= strtoupper($part[0] ?? '');
        if (strlen($initials) >= 2) {
            break;
        }
    }
    return $initials;
}

function getDefaultProfiles()
{
    global $studentNames;
    $profiles = [];
    foreach ($studentNames as $studentName) {
        $key = normalizeName($studentName);
        $profiles[$key] = [
            'name' => $studentName,
            'instagram' => toInstagramHandle($studentName),
            'bio' => 'Halo, saya ' . $studentName . ' dari TKJ 5.',
            'photo_filename' => '',
        ];
    }
    return $profiles;
}

function loadProfiles()
{
    global $profilesFilePath;
    $default = getDefaultProfiles();
    if (file_exists($profilesFilePath)) {
        $content = file_get_contents($profilesFilePath);
        $data = json_decode($content, true);
        if (is_array($data)) {
            return array_replace($default, $data);
        }
    }
    return $default;
}

function saveProfiles($profiles)
{
    global $profilesFilePath;
    file_put_contents($profilesFilePath, json_encode($profiles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function findStudentKey($studentName)
{
    global $studentNames;
    $normalized = normalizeName($studentName);

    foreach ($studentNames as $name) {
        if (normalizeName($name) === $normalized) {
            return normalizeName($name);
        }
    }

    return null;
}

function isValidLoginKey($key)
{
    return is_string($key) && preg_match('/^[a-zA-Z0-9_\.-]{3,64}$/', trim($key));
}

function getCurrentUser()
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn()
{
    return !empty($_SESSION['user']);
}

function isVisitor()
{
    return !empty($_SESSION['visitor']);
}

function setVisitor()
{
    $_SESSION['visitor'] = true;
}

function clearVisitor()
{
    if (isset($_SESSION['visitor'])) {
        unset($_SESSION['visitor']);
    }
}

function shouldShowLogin()
{
    return !isLoggedIn() && !isVisitor();
}

function isAdmin()
{
    $user = getCurrentUser();
    return !empty($user['role']) && $user['role'] === 'admin';
}

function loginUser($loginKey, $studentName)
{
    $key = findStudentKey($studentName);
    if ($key === null) {
        return false;
    }
    $_SESSION['user'] = [
        'name' => $studentName,
        'email' => $loginKey,
        'key' => $key,
        'role' => 'student',
    ];
    return true;
}

function loginAdmin($loginKey)
{
    $_SESSION['user'] = [
        'name' => 'Admin',
        'email' => $loginKey,
        'key' => 'admin',
        'role' => 'admin',
    ];
    return true;
}

function logoutUser()
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

function userCanEditProfile($profileKey)
{
    $user = getCurrentUser();
    if (!$user) {
        return false;
    }
    if (!empty($user['role']) && $user['role'] === 'admin') {
        return true;
    }
    return $user['key'] === $profileKey;
}

$profiles = loadProfiles();
