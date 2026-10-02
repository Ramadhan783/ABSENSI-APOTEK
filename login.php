<?php
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } else {
        header("Location: karyawan/dashboard.php");
        exit;
    }
}

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Absensi Apotek</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            padding: 20px;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .logo {
            width: 65px;
            height: 65px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: #0f766e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            font-weight: bold;
        }

        h2 {
            text-align: center;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: bold;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: #0f766e;
        }

        .btn-login {
            width: 100%;
            border: none;
            padding: 13px;
            border-radius: 9px;
            background: #0f766e;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #115e59;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 11px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 22px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>

<body>

<div class="login-box">

    <div class="logo">
        AP
    </div>

    <h2>Absensi Apotek</h2>

    <p class="subtitle">
        Silakan masuk ke akun Anda
    </p>

    <?php if ($error === 'username_password'): ?>
        <div class="error">
            Username atau password salah.
        </div>
    <?php elseif ($error === 'nonaktif'): ?>
        <div class="error">
            Akun Anda sedang nonaktif.
        </div>
    <?php endif; ?>

    <form action="proses_login.php" method="POST">

        <div class="form-group">
            <label>Username</label>
            <input
                type="text"
                name="username"
                placeholder="Masukkan username"
                required
                autocomplete="username"
            >
        </div>

        <div class="form-group">
            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
                autocomplete="current-password"
            >
        </div>

        <button type="submit" class="btn-login">
            Masuk
        </button>

    </form>

    <div class="footer">
        Sistem Absensi Karyawan Apotek
    </div>

</div>

</body>
</html>