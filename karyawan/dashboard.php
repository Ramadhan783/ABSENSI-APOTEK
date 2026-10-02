<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['role'] !== 'karyawan') {
    header("Location: ../login.php");
    exit;
}

require_once "../config/koneksi.php";

$username = $_SESSION['username'] ?? 'Karyawan';

/*
|--------------------------------------------------------------------------
| DATA DASHBOARD
|--------------------------------------------------------------------------
*/

$tanggal_hari_ini = date('Y-m-d');

/* Jadwal hari ini */
$jadwal = null;

$query_jadwal = mysqli_query($conn, "
    SELECT *
    FROM jadwal
    WHERE tanggal = '$tanggal_hari_ini'
    LIMIT 1
");

if ($query_jadwal && mysqli_num_rows($query_jadwal) > 0) {
    $jadwal = mysqli_fetch_assoc($query_jadwal);
}

/* Absensi hari ini */
$absensi_hari_ini = null;

$query_absensi = mysqli_query($conn, "
    SELECT *
    FROM absensi
    WHERE tanggal = '$tanggal_hari_ini'
    LIMIT 1
");

if ($query_absensi && mysqli_num_rows($query_absensi) > 0) {
    $absensi_hari_ini = mysqli_fetch_assoc($query_absensi);
}

/* Riwayat absensi */
$riwayat = mysqli_query($conn, "
    SELECT *
    FROM absensi
    ORDER BY tanggal DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Karyawan - Absensi Apotek</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../assets/css/karyawan.css">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">

            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <img src="../assets/img/logo-apotek.png"
                    alt="Logo Apotek"
                    class="navbar-logo">

                <div class="brand-text">
                    <strong>Absensi Apotek</strong>
                    <small>Sistem Absensi Karyawan</small>
                </div>
            </a>

            <div class="dropdown">

                <button class="btn profile-btn dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown">

                    <span class="profile-icon">
                        <i class="bi bi-person"></i>
                    </span>

                    <span class="d-none d-md-inline">
                        <?= htmlspecialchars($username); ?>
                    </span>

                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow">

                    <li>
                        <a class="dropdown-item" href="profil.php">
                            <i class="bi bi-person me-2"></i>
                            Profil
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item text-danger" href="../logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </a>
                    </li>

                </ul>

            </div>

        </div>
    </nav>


    <!-- CONTENT -->
    <main class="main-content">

        <div class="container">

            <!-- WELCOME -->
            <div class="welcome-card">

                <div>
                    <span class="welcome-label">
                        Dashboard Karyawan
                    </span>

                    <h2>
                        Selamat datang, <?= htmlspecialchars($username); ?> 👋
                    </h2>

                    <p>
                        Silakan lakukan absensi sesuai jadwal kerja Anda.
                    </p>
                </div>

                <div class="date-box">
                    <i class="bi bi-calendar3"></i>

                    <span>
                        <?= date('d F Y'); ?>
                    </span>
                </div>

            </div>


            <!-- ABSENSI -->
            <section class="section">

                <div class="section-title">
                    <div>
                        <span class="small-title">ABSENSI HARI INI</span>
                        <h4>Presensi Karyawan</h4>
                    </div>
                </div>


                <div class="row g-3">

                    <!-- ABSEN MASUK -->
                    <div class="col-12 col-md-6">

                        <div class="attendance-card masuk">

                            <div class="attendance-icon">
                                <i class="bi bi-box-arrow-in-right"></i>
                            </div>

                            <div class="attendance-info">

                                <span>ABSEN MASUK</span>

                                <?php if ($absensi_hari_ini && !empty($absensi_hari_ini['jam_masuk'])): ?>

                                    <h3>
                                        <?= htmlspecialchars($absensi_hari_ini['jam_masuk']); ?>
                                    </h3>

                                    <small class="status-success">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Sudah Absen
                                    </small>

                                <?php else: ?>

                                    <h3>--:--</h3>

                                    <small>
                                        Belum melakukan absen masuk
                                    </small>

                                <?php endif; ?>

                            </div>

                            <?php if (!$absensi_hari_ini || empty($absensi_hari_ini['jam_masuk'])): ?>

                                <a href="absen_masuk.php"
                                    class="btn btn-absen btn-masuk">
                                    Absen Masuk
                                    <i class="bi bi-arrow-right"></i>
                                </a>

                            <?php else: ?>

                                <button class="btn btn-secondary" disabled>
                                    Sudah Absen
                                </button>

                            <?php endif; ?>

                        </div>

                    </div>


                    <!-- ABSEN PULANG -->
                    <div class="col-12 col-md-6">

                        <div class="attendance-card pulang">

                            <div class="attendance-icon">
                                <i class="bi bi-box-arrow-right"></i>
                            </div>

                            <div class="attendance-info">

                                <span>ABSEN PULANG</span>

                                <?php if ($absensi_hari_ini && !empty($absensi_hari_ini['jam_pulang'])): ?>

                                    <h3>
                                        <?= htmlspecialchars($absensi_hari_ini['jam_pulang']); ?>
                                    </h3>

                                    <small class="status-success">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Sudah Absen
                                    </small>

                                <?php else: ?>

                                    <h3>--:--</h3>

                                    <small>
                                        Belum melakukan absen pulang
                                    </small>

                                <?php endif; ?>

                            </div>

                            <?php if ($absensi_hari_ini && !empty($absensi_hari_ini['jam_masuk']) && empty($absensi_hari_ini['jam_pulang'])): ?>

                                <a href="absen_pulang.php"
                                    class="btn btn-absen btn-pulang">
                                    Absen Pulang
                                    <i class="bi bi-arrow-right"></i>
                                </a>

                            <?php elseif (!$absensi_hari_ini || empty($absensi_hari_ini['jam_masuk'])): ?>

                                <button class="btn btn-secondary" disabled>
                                    Absen Masuk Dulu
                                </button>

                            <?php else: ?>

                                <button class="btn btn-secondary" disabled>
                                    Sudah Absen
                                </button>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </section>


            <!-- JADWAL -->
            <section class="section">

                <div class="section-title">
                    <div>
                        <span class="small-title">JADWAL KERJA</span>
                        <h4>Jadwal Hari Ini</h4>
                    </div>
                </div>


                <div class="schedule-card">

                    <div class="schedule-icon">
                        <i class="bi bi-clock"></i>
                    </div>

                    <div class="schedule-info">

                        <span>JAM KERJA</span>

                        <?php if ($jadwal): ?>

                            <h3>
                                <?= htmlspecialchars($jadwal['jam_masuk']); ?>
                                -
                                <?= htmlspecialchars($jadwal['jam_pulang']); ?>
                            </h3>

                            <p>
                                <i class="bi bi-check-circle-fill"></i>
                                Jadwal tersedia hari ini
                            </p>

                        <?php else: ?>

                            <h3>
                                Tidak ada jadwal
                            </h3>

                            <p>
                                Belum ada jadwal kerja untuk hari ini.
                            </p>

                        <?php endif; ?>

                    </div>

                </div>

            </section>


            <!-- RIWAYAT -->
            <section class="section">

                <div class="section-title d-flex justify-content-between align-items-center">

                    <div>
                        <span class="small-title">AKTIVITAS</span>
                        <h4>Riwayat Absensi</h4>
                    </div>

                    <a href="riwayat.php" class="lihat-semua">
                        Lihat Semua
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>


                <div class="history-card">

                    <?php if ($riwayat && mysqli_num_rows($riwayat) > 0): ?>

                        <?php while ($data = mysqli_fetch_assoc($riwayat)): ?>

                            <div class="history-item">

                                <div class="history-date">

                                    <strong>
                                        <?= date('d', strtotime($data['tanggal'])); ?>
                                    </strong>

                                    <span>
                                        <?= date('M', strtotime($data['tanggal'])); ?>
                                    </span>

                                </div>


                                <div class="history-detail">

                                    <strong>
                                        Absensi
                                    </strong>

                                    <span>
                                        Masuk:
                                        <?= !empty($data['jam_masuk'])
                                            ? htmlspecialchars($data['jam_masuk'])
                                            : '--:--'; ?>

                                        &nbsp; | &nbsp;

                                        Pulang:
                                        <?= !empty($data['jam_pulang'])
                                            ? htmlspecialchars($data['jam_pulang'])
                                            : '--:--'; ?>
                                    </span>

                                </div>


                                <div>

                                    <?php
                                    $status = $data['status'] ?? 'Hadir';

                                    if ($status == 'Hadir') {
                                        $badge = 'badge-hadir';
                                    } elseif ($status == 'Terlambat') {
                                        $badge = 'badge-terlambat';
                                    } elseif ($status == 'Izin') {
                                        $badge = 'badge-izin';
                                    } elseif ($status == 'Sakit') {
                                        $badge = 'badge-sakit';
                                    } else {
                                        $badge = 'badge-default';
                                    }
                                    ?>

                                    <span class="status-badge <?= $badge; ?>">
                                        <?= htmlspecialchars($status); ?>
                                    </span>

                                </div>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="empty-history">

                            <i class="bi bi-calendar-x"></i>

                            <p>
                                Belum ada riwayat absensi.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </section>


            <!-- MENU CEPAT -->
            <section class="section">

                <div class="section-title">

                    <div>
                        <span class="small-title">MENU</span>
                        <h4>Akses Cepat</h4>
                    </div>

                </div>


                <div class="row g-3">

                    <div class="col-6 col-md-3">

                        <a href="absen_masuk.php"
                            class="quick-menu">

                            <div>
                                <i class="bi bi-camera"></i>
                            </div>

                            <span>
                                Absensi
                            </span>

                        </a>

                    </div>


                    <div class="col-6 col-md-3">

                        <a href="riwayat.php"
                            class="quick-menu">

                            <div>
                                <i class="bi bi-clock-history"></i>
                            </div>

                            <span>
                                Riwayat
                            </span>

                        </a>

                    </div>


                    <div class="col-6 col-md-3">

                        <a href="izin.php"
                            class="quick-menu">

                            <div>
                                <i class="bi bi-file-earmark-text"></i>
                            </div>

                            <span>
                                Pengajuan Izin
                            </span>

                        </a>

                    </div>


                    <div class="col-6 col-md-3">

                        <a href="profil.php"
                            class="quick-menu">

                            <div>
                                <i class="bi bi-person"></i>
                            </div>

                            <span>
                                Profil
                            </span>

                        </a>

                    </div>

                </div>

            </section>

        </div>

    </main>


    <!-- BOTTOM NAV MOBILE -->
    <div class="mobile-bottom-nav d-md-none">

        <a href="dashboard.php" class="active">
            <i class="bi bi-house"></i>
            <span>Home</span>
        </a>

        <a href="absen_masuk.php">
            <i class="bi bi-camera"></i>
            <span>Absen</span>
        </a>

        <a href="riwayat.php">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat</span>
        </a>

        <a href="profil.php">
            <i class="bi bi-person"></i>
            <span>Profil</span>
        </a>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>