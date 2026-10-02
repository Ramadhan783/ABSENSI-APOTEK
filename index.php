<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Absensi Apotek</title>


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


    <!-- CSS Halaman Utama -->

    <link
        rel="stylesheet"
        href="assets/css/index.css"
    >

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<nav class="navbar navbar-expand-lg sticky-top">

    <div class="container">


        <a
            class="navbar-brand d-flex align-items-center"
            href="index.php"
        >

            <img
                src="assets/img/logo-apotek.png"
                alt="Logo Apotek"
            >

            <span>
                Absensi Apotek
            </span>

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="navbarMenu"
        >

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">

                    <a
                        class="nav-link px-3"
                        href="#beranda"
                    >
                        Beranda
                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="nav-link px-3"
                        href="#fitur"
                    >
                        Fitur
                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="nav-link px-3"
                        href="#cara-kerja"
                    >
                        Cara Kerja
                    </a>

                </li>

                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">

                    <a
                        href="login.php"
                        class="btn btn-login"
                    >

                        <i class="bi bi-box-arrow-in-right me-1"></i>

                        Login

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>



<!-- =========================
     HERO
========================= -->

<section
    class="hero"
    id="beranda"
>

    <div class="container">

        <div class="row align-items-center">


            <!-- TEXT -->

            <div class="col-lg-7">

                <span
                    class="badge rounded-pill text-bg-success mb-3 px-3 py-2"
                >

                    Sistem Absensi Karyawan

                </span>


                <h1 class="hero-title mb-4">

                    Absensi Karyawan
                    <br>

                    <span>Lebih Mudah & Teratur</span>

                </h1>


                <p class="hero-description mb-4">

                    Sistem absensi karyawan apotek yang membantu
                    mengelola kehadiran, jadwal kerja, izin,
                    dan laporan absensi secara lebih praktis.

                </p>


                <div class="d-flex flex-wrap gap-3">

                    <a
                        href="login.php"
                        class="btn btn-login"
                    >

                        <i class="bi bi-box-arrow-in-right me-2"></i>

                        Mulai Login

                    </a>


                    <a
                        href="#fitur"
                        class="btn btn-outline-custom"
                    >

                        Lihat Fitur

                    </a>

                </div>

            </div>


            <!-- LOGO -->

            <div class="col-lg-5 text-center">

                <div class="hero-logo-wrapper">

                    <img
                        src="assets/img/logo-apotek.png"
                        alt="Logo Apotek"
                        class="hero-logo"
                    >

                </div>

            </div>


        </div>

    </div>

</section>



<!-- =========================
     FITUR
========================= -->

<section
    class="features"
    id="fitur"
>

    <div class="container">


        <div class="text-center mb-5">

            <h2 class="section-title">
                Fitur Sistem
            </h2>

            <p class="section-subtitle mt-2">

                Fitur utama yang membantu admin dan karyawan
                mengelola proses absensi apotek.

            </p>

        </div>


        <div class="row g-4">


            <!-- FITUR 1 -->

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">

                        <i class="bi bi-phone"></i>

                    </div>

                    <h5>
                        Absensi Lewat HP
                    </h5>

                    <p>

                        Karyawan dapat melakukan absensi
                        langsung melalui smartphone.

                    </p>

                </div>

            </div>


            <!-- FITUR 2 -->

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">

                        <i class="bi bi-camera"></i>

                    </div>

                    <h5>
                        Foto Saat Absen
                    </h5>

                    <p>

                        Sistem meminta foto sebagai bukti
                        ketika karyawan melakukan absensi.

                    </p>

                </div>

            </div>


            <!-- FITUR 3 -->

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">

                        <i class="bi bi-geo-alt"></i>

                    </div>

                    <h5>
                        Validasi Lokasi
                    </h5>

                    <p>

                        Absensi hanya dapat dilakukan ketika
                        karyawan berada dalam radius yang
                        ditentukan dari lokasi apotek.

                    </p>

                </div>

            </div>


            <!-- FITUR 4 -->

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">

                        <i class="bi bi-calendar-check"></i>

                    </div>

                    <h5>
                        Jadwal Kerja
                    </h5>

                    <p>

                        Admin dapat mengatur jadwal masuk
                        dan pulang setiap karyawan.

                    </p>

                </div>

            </div>


            <!-- FITUR 5 -->

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">

                        <i class="bi bi-file-earmark-text"></i>

                    </div>

                    <h5>
                        Pengajuan Izin
                    </h5>

                    <p>

                        Karyawan dapat mengajukan izin atau
                        sakit melalui sistem.

                    </p>

                </div>

            </div>


            <!-- FITUR 6 -->

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">

                        <i class="bi bi-bar-chart"></i>

                    </div>

                    <h5>
                        Laporan Absensi
                    </h5>

                    <p>

                        Admin dapat melihat dan mengelola
                        laporan kehadiran karyawan.

                    </p>

                </div>

            </div>


        </div>

    </div>

</section>



<!-- =========================
     CARA KERJA
========================= -->

<section
    class="how-it-works"
    id="cara-kerja"
>

    <div class="container">


        <div class="text-center mb-5">

            <h2 class="section-title">
                Cara Kerja
            </h2>

            <p class="section-subtitle mt-2">

                Proses absensi dibuat sederhana untuk
                karyawan dan admin.

            </p>

        </div>


        <div class="row g-4 text-center">


            <div class="col-md-4">

                <div class="step-number">
                    1
                </div>

                <h5 class="fw-bold">
                    Login
                </h5>

                <p class="text-muted small">

                    Karyawan masuk menggunakan akun
                    yang telah diberikan.

                </p>

            </div>


            <div class="col-md-4">

                <div class="step-number">
                    2
                </div>

                <h5 class="fw-bold">
                    Ambil Foto & Absen
                </h5>

                <p class="text-muted small">

                    Karyawan mengambil foto dan sistem
                    memeriksa lokasi sebelum absensi.

                </p>

            </div>


            <div class="col-md-4">

                <div class="step-number">
                    3
                </div>

                <h5 class="fw-bold">
                    Admin Memantau
                </h5>

                <p class="text-muted small">

                    Admin dapat melihat data kehadiran
                    karyawan melalui dashboard.

                </p>

            </div>


        </div>

    </div>

</section>



<!-- =========================
     FOOTER
========================= -->

<footer class="footer">

    <div class="container">

        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">


            <div class="d-flex align-items-center gap-3">

                <img
                    src="assets/img/logo-apotek.png"
                    alt="Logo Apotek"
                    class="footer-logo"
                >

                <div>

                    <strong>
                        Absensi Apotek
                    </strong>

                    <p>
                        Sistem Absensi Karyawan
                    </p>

                </div>

            </div>


            <p>

                &copy; <?= date('Y'); ?> Absensi Apotek.
                All rights reserved.

            </p>


        </div>

    </div>

</footer>



<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>