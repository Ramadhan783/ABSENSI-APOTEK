<?php

session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../config/koneksi.php";

$tanggal_hari_ini = date('Y-m-d');

/* =========================
   TOTAL KARYAWAN
========================= */

$query_karyawan = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM karyawan
     WHERE status = 'aktif'"
);

$total_karyawan = mysqli_fetch_assoc($query_karyawan)['total'];


/* =========================
   TOTAL ABSENSI HARI INI
========================= */

$query_absensi = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM absensi
     WHERE tanggal = '$tanggal_hari_ini'"
);

$total_absensi = mysqli_fetch_assoc($query_absensi)['total'];


/* =========================
   TOTAL IZIN MENUNGGU
========================= */

$query_izin = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM izin
     WHERE status = 'Menunggu'"
);

$total_izin = mysqli_fetch_assoc($query_izin)['total'];


/* =========================
   TOTAL JADWAL HARI INI
========================= */

$query_jadwal = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM jadwal
     WHERE tanggal = '$tanggal_hari_ini'"
);

$total_jadwal = mysqli_fetch_assoc($query_jadwal)['total'];


/* =========================
   DATA ABSENSI HARI INI
========================= */

$query_data_absensi = mysqli_query(
    $conn,
    "SELECT
        a.*,
        k.nama,
        k.jabatan
     FROM absensi a
     INNER JOIN karyawan k
        ON a.karyawan_id = k.id
     WHERE a.tanggal = '$tanggal_hari_ini'
     ORDER BY a.jam_masuk ASC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - Absensi Apotek</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- CSS Admin -->
    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

</head>

<body>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

    <div class="brand">

        <img 
         src="../assets/img/logo-apotek.png"
         alt="Logo Apotek"
         class="brand-logo"
        >

        <h5>Absensi Apotek</h5>

        <small>Panel Administrator</small>

    </div>


    <div class="menu-title">
        Menu Utama
    </div>

    <nav class="nav flex-column">

        <a href="dashboard.php" class="nav-link active">
            <i class="bi bi-grid-fill"></i>
            Dashboard
        </a>

        <a href="#" class="nav-link">
            <i class="bi bi-people-fill"></i>
            Data Karyawan
        </a>

        <a href="#" class="nav-link">
            <i class="bi bi-calendar3"></i>
            Jadwal Kerja
        </a>

        <a href="#" class="nav-link">
            <i class="bi bi-clock-history"></i>
            Monitoring Absensi
        </a>

        <a href="#" class="nav-link">
            <i class="bi bi-file-earmark-text"></i>
            Pengajuan Izin
        </a>

    </nav>


    <div class="menu-title">
        Laporan
    </div>

    <nav class="nav flex-column">

        <a href="#" class="nav-link">
            <i class="bi bi-file-earmark-bar-graph"></i>
            Laporan Absensi
        </a>

    </nav>


    <div class="logout">

        <a
            href="../logout.php"
            class="btn btn-light w-100"
        >
            <i class="bi bi-box-arrow-right me-2"></i>
            Keluar
        </a>

    </div>

</div>


<!-- =========================
     MAIN CONTENT
========================= -->

<div class="main-content">


    <!-- TOPBAR -->

    <div class="topbar mb-4">

        <div class="row align-items-center">

            <div class="col">

                <h4 class="fw-bold mb-1">
                    Dashboard Admin
                </h4>

                <p class="text-muted mb-0 small">
                    Monitoring sistem absensi karyawan apotek
                </p>

            </div>

            <div class="col-auto">

                <div class="d-flex align-items-center">

                    <div class="text-end me-3">

                        <div class="fw-semibold">
                            <?= htmlspecialchars($_SESSION['username']); ?>
                        </div>

                        <small class="text-success">
                            Administrator
                        </small>

                    </div>

                    <div
                        class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                        style="width:42px;height:42px;"
                    >
                        <i class="bi bi-person-fill"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- STATISTIC CARDS -->

    <div class="row g-4 mb-4">


        <!-- KARYAWAN -->

        <div class="col-xl-3 col-md-6">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <p class="text-muted small mb-2">
                                Karyawan Aktif
                            </p>

                            <h2 class="fw-bold mb-1">
                                <?= $total_karyawan; ?>
                            </h2>

                            <small class="text-muted">
                                Karyawan terdaftar
                            </small>

                        </div>

                        <div class="stat-icon icon-green">

                            <i class="bi bi-people-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ABSENSI -->

        <div class="col-xl-3 col-md-6">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <p class="text-muted small mb-2">
                                Absensi Hari Ini
                            </p>

                            <h2 class="fw-bold mb-1">
                                <?= $total_absensi; ?>
                            </h2>

                            <small class="text-muted">
                                Sudah melakukan absensi
                            </small>

                        </div>

                        <div class="stat-icon icon-blue">

                            <i class="bi bi-check-circle-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- IZIN -->

        <div class="col-xl-3 col-md-6">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <p class="text-muted small mb-2">
                                Izin / Sakit
                            </p>

                            <h2 class="fw-bold mb-1">
                                <?= $total_izin; ?>
                            </h2>

                            <small class="text-muted">
                                Menunggu persetujuan
                            </small>

                        </div>

                        <div class="stat-icon icon-orange">

                            <i class="bi bi-file-earmark-medical-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- JADWAL -->

        <div class="col-xl-3 col-md-6">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <p class="text-muted small mb-2">
                                Jadwal Hari Ini
                            </p>

                            <h2 class="fw-bold mb-1">
                                <?= $total_jadwal; ?>
                            </h2>

                            <small class="text-muted">
                                Jadwal kerja
                            </small>

                        </div>

                        <div class="stat-icon icon-purple">

                            <i class="bi bi-calendar-check-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- CONTENT -->

    <div class="row g-4">


        <!-- ABSENSI -->

        <div class="col-xl-8">

            <div class="card content-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Absensi Hari Ini
                            </h5>

                            <small class="text-muted">
                                <?= date('d F Y'); ?>
                            </small>

                        </div>

                        <button class="btn btn-outline-success btn-sm">
                            <i class="bi bi-download me-1"></i>
                            Export
                        </button>

                    </div>


                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead>

                                <tr>

                                    <th>Karyawan</th>

                                    <th>Jabatan</th>

                                    <th>Masuk</th>

                                    <th>Pulang</th>

                                    <th>Status</th>

                                </tr>

                            </thead>

                            <tbody>


                                <?php if (mysqli_num_rows($query_data_absensi) > 0): ?>

                                    <?php while ($row = mysqli_fetch_assoc($query_data_absensi)): ?>

                                        <tr>

                                            <td>

                                                <span class="avatar">

                                                    <?= strtoupper(substr($row['nama'], 0, 1)); ?>

                                                </span>

                                                <?= htmlspecialchars($row['nama']); ?>

                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['jabatan']); ?>
                                            </td>

                                            <td>

                                                <?php if ($row['jam_masuk']): ?>

                                                    <span class="fw-semibold">
                                                        <?= $row['jam_masuk']; ?>
                                                    </span>

                                                <?php else: ?>

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td>

                                                <?php if ($row['jam_pulang']): ?>

                                                    <?= $row['jam_pulang']; ?>

                                                <?php else: ?>

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td>

                                                <?php if ($row['status'] === 'Hadir'): ?>

                                                    <span class="badge text-bg-success">
                                                        Hadir
                                                    </span>

                                                <?php elseif ($row['status'] === 'Terlambat'): ?>

                                                    <span class="badge text-bg-warning">
                                                        Terlambat
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge text-bg-secondary">
                                                        <?= htmlspecialchars($row['status']); ?>
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td colspan="5" class="text-center py-5">

                                            <i
                                                class="bi bi-calendar-x fs-1 text-muted"
                                            ></i>

                                            <p class="text-muted mt-2 mb-0">
                                                Belum ada absensi hari ini.
                                            </p>

                                        </td>

                                    </tr>

                                <?php endif; ?>


                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>


        <!-- MENU CEPAT -->

        <div class="col-xl-4">

            <div class="card content-card">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        Menu Cepat
                    </h5>


                    <div class="quick-menu d-grid gap-2">

                        <a
                            href="#"
                            class="btn btn-light border"
                        >
                            <i class="bi bi-person-plus-fill text-success me-2"></i>
                            Tambah Karyawan
                        </a>

                        <a
                            href="#"
                            class="btn btn-light border"
                        >
                            <i class="bi bi-calendar-plus text-primary me-2"></i>
                            Kelola Jadwal
                        </a>

                        <a
                            href="#"
                            class="btn btn-light border"
                        >
                            <i class="bi bi-clock-history text-warning me-2"></i>
                            Monitoring Absensi
                        </a>

                        <a
                            href="#"
                            class="btn btn-light border"
                        >
                            <i class="bi bi-file-earmark-medical text-danger me-2"></i>
                            Pengajuan Izin
                        </a>

                        <a
                            href="#"
                            class="btn btn-light border"
                        >
                            <i class="bi bi-file-earmark-bar-graph text-info me-2"></i>
                            Laporan Absensi
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>