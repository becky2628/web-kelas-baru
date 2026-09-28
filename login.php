<?php
require_once 'auth.php';

// allow choosing visitor mode from the login page link
if (isset($_GET['visitor'])) {
    setVisitor();
    header('Location: profil.php');
    exit;
}

if (isset($_GET['clearvisitor'])) {
    clearVisitor();
    header('Location: index.php');
    exit;
}

if (isLoggedIn()) {
    header('Location: profil.php');
    exit;
}

$errorMessage = '';
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailValue = trim($_POST['email'] ?? '');

    if ($emailValue === '') {
        $errorMessage = 'Kode login / username harus diisi.';
    } elseif (!isValidLoginKey($emailValue)) {
        $errorMessage = 'Gunakan kode login atau username yang valid (3-64 karakter, huruf, angka, tanda _ . -).';
    } else {
        $studentName = getAllowedStudentNameByKey($emailValue);
        if ($studentName !== null) {
            loginUser($emailValue, $studentName);
            header('Location: profil.php');
            exit;
        }

        if (isAdminKey($emailValue)) {
            loginAdmin($emailValue);
            header('Location: profil.php');
            exit;
        }

        $errorMessage = 'Kode login / username belum terdaftar.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login TKJ 5</title>
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
        nav a:hover {
            background: rgba(255,255,255,0.16);
        }
        .page {
            max-width: 520px;
            margin: 32px auto 48px;
            padding: 28px 26px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }
        .page h1 {
            margin-top: 0;
            color: #0f3c67;
        }
        form {
            display: grid;
            gap: 18px;
        }
        label {
            font-weight: 700;
            color: #112c44;
        }
        input[type="email"], input[type="text"], input[type="file"] {
            width: 100%;
            padding: 14px;
            border: 1px solid #d3dce6;
            border-radius: 16px;
            background: #f9fbff;
            color: #1c2e3f;
        }
        button, .button-link {
            padding: 14px 20px;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-weight: 700;
        }
        button {
            background: #0099cc;
            color: #fff;
        }
        .button-link {
            background: #5f6c7d;
            color: #fff;
            display: inline-block;
        }
        .error {
            color: #a60000;
            background: #ffecec;
            padding: 14px;
            border-radius: 18px;
            border: 1px solid #f2b8b8;
        }
        .note {
            background: #e8f7ff;
            padding: 18px;
            border-radius: 18px;
            border: 1px solid #c8ecff;
            color: #164a6f;
            line-height: 1.75;
        }
        .note strong {
            color: #0d3f64;
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
            .page {
                margin: 20px auto 32px;
                padding: 20px 18px;
                border-radius: 18px;
            }
            .page h1 {
                font-size: 1.5rem;
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
            .page {
                margin: 16px auto 24px;
                padding: 16px 14px;
                border-radius: 16px;
            }
            .page h1 {
                font-size: 1.2rem;
                margin-bottom: 14px;
            }
            form {
                gap: 14px;
            }
            label {
                font-size: 0.95rem;
            }
            input[type="email"], input[type="text"], input[type="file"] {
                padding: 12px;
                font-size: 0.95rem;
            }
            button, .button-link {
                padding: 12px 16px;
                font-size: 0.95rem;
            }
            .error, .note {
                padding: 12px;
                font-size: 0.9rem;
                border-radius: 14px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Login TKJ 5</h1>
        <p>Masuk menggunakan kode login/username khusus yang sudah terdaftar. Admin bisa mengedit semua profil dan siswa dapat mengubah profil masing-masing.</p>
    </header>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="profil.php">Profil</a>
        <a href="materi.php">Galeri</a>
        <a href="jadwal.php">Jadwal</a>
        <a href="kontak.php">Kontak</a>
        <?php if (isLoggedIn()): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <?php if (shouldShowLogin()): ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
    <div class="page">
        <?php if ($errorMessage): ?>
            <div class="error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <div class="note">
            Masuk hanya dengan kode login atau username khusus yang sudah diizinkan. Satu kode admin dapat mengedit semua profil. Pengunjung bisa memilih <strong>Lihat saja</strong> tanpa login.
        </div>
        <form action="login.php" method="post">
            <label for="email">Kode login / Username</label>
            <input id="email" type="text" name="email" placeholder="masukkan kode atau username" value="<?php echo htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8'); ?>" required>
            <button type="submit">Login</button>
        </form>
        <p style="margin-top: 20px;"><a class="button-link" href="login.php?visitor=1">Lihat saja sebagai pengunjung</a></p>
    </div>
</body>
</html>
