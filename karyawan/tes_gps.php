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

require_once "../config/lokasi.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Test GPS - Absensi Apotek</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #f5f7fb;
        }

        .gps-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }

        .gps-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .06);
        }

        .gps-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            border-radius: 20px;
            background: #eef2ff;
            color: #4f46e5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .location-box {
            background: #f7f8fa;
            border-radius: 14px;
            padding: 15px;
            margin-top: 20px;
        }

        .location-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 8px 0;
            border-bottom: 1px solid #e8ebef;
        }

        .location-row:last-child {
            border-bottom: none;
        }

        .label {
            color: #7c8492;
        }

        .value {
            font-weight: 600;
            text-align: right;
        }

        #status {
            margin-top: 20px;
        }
    </style>
</head>

<body>

<div class="gps-container">

    <div class="gps-card text-center">

        <div class="gps-icon">
            <i class="bi bi-geo-alt-fill"></i>
        </div>

        <h3 class="fw-bold">Tes Lokasi GPS</h3>

        <p class="text-secondary">
            Pastikan izin lokasi browser sudah diaktifkan.
        </p>

        <button
            type="button"
            class="btn btn-dark w-100 py-3"
            onclick="cekLokasi()">

            <i class="bi bi-crosshair me-2"></i>
            Cek Lokasi Saya

        </button>


        <div id="status"></div>


        <div class="location-box text-start">

            <div class="location-row">
                <span class="label">Latitude HP</span>
                <span class="value" id="latitude">-</span>
            </div>

            <div class="location-row">
                <span class="label">Longitude HP</span>
                <span class="value" id="longitude">-</span>
            </div>

            <div class="location-row">
                <span class="label">Latitude Apotek</span>
                <span class="value">
                    <?= $latitude_apotek; ?>
                </span>
            </div>

            <div class="location-row">
                <span class="label">Longitude Apotek</span>
                <span class="value">
                    <?= $longitude_apotek; ?>
                </span>
            </div>

            <div class="location-row">
                <span class="label">Jarak</span>
                <span class="value" id="jarak">-</span>
            </div>

            <div class="location-row">
                <span class="label">Batas Radius</span>
                <span class="value">
                    <?= $radius_maksimal; ?> meter
                </span>
            </div>

        </div>

        <div class="mt-4">

            <a href="dashboard.php"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-2"></i>
                Kembali

            </a>

        </div>

    </div>

</div>


<script>

const latitudeApotek = <?= $latitude_apotek; ?>;
const longitudeApotek = <?= $longitude_apotek; ?>;
const radiusMaksimal = <?= $radius_maksimal; ?>;

const modeTesting = <?= $mode_testing ? 'true' : 'false'; ?>;

const latitudeTesting = <?= $latitude_testing; ?>;
const longitudeTesting = <?= $longitude_testing; ?>;


/*
|--------------------------------------------------------------------------
| CEK GPS
|--------------------------------------------------------------------------
*/

function cekLokasi() {

    const status = document.getElementById("status");

    /*
    |--------------------------------------------------------------------------
    | MODE TESTING
    |--------------------------------------------------------------------------
    */

    if (modeTesting) {

        const latitude = latitudeTesting;
        const longitude = longitudeTesting;

        document.getElementById("latitude").innerText =
            latitude.toFixed(7);

        document.getElementById("longitude").innerText =
            longitude.toFixed(7);

        const jarak = hitungJarak(
            latitude,
            longitude,
            latitudeApotek,
            longitudeApotek
        );

        document.getElementById("jarak").innerText =
            jarak.toFixed(2) + " meter";

        status.innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-tools me-2"></i>
                <strong>MODE TESTING AKTIF</strong><br>
                Lokasi simulasi digunakan untuk pengujian aplikasi.
            </div>

            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Lokasi valid.</strong><br>
                Sistem menganggap Anda berada di lokasi apotek.
            </div>
        `;

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | MODE PRODUKSI - GPS ASLI
    |--------------------------------------------------------------------------
    */

    if (!navigator.geolocation) {

        status.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-x-circle-fill me-2"></i>
                Browser tidak mendukung GPS.
            </div>
        `;

        return;
    }


    status.innerHTML = `
        <div class="alert alert-info">
            <i class="bi bi-hourglass-split me-2"></i>
            Sedang mengambil lokasi...
        </div>
    `;


    navigator.geolocation.getCurrentPosition(

        function(position) {

            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            document.getElementById("latitude").innerText =
                latitude.toFixed(7);

            document.getElementById("longitude").innerText =
                longitude.toFixed(7);


            const jarak = hitungJarak(
                latitude,
                longitude,
                latitudeApotek,
                longitudeApotek
            );


            document.getElementById("jarak").innerText =
                jarak.toFixed(2) + " meter";


            if (jarak <= radiusMaksimal) {

                status.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Lokasi valid.</strong><br>
                        Anda berada dalam radius ${radiusMaksimal} meter dari apotek.
                    </div>
                `;

            } else {

                status.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        <strong>Lokasi di luar radius.</strong><br>
                        Anda belum dapat melakukan absensi dari lokasi ini.
                    </div>
                `;

            }

        },

        function(error) {

            let pesan = "";

            switch (error.code) {

                case error.PERMISSION_DENIED:
                    pesan = "Izin lokasi ditolak. Aktifkan izin lokasi browser.";
                    break;

                case error.POSITION_UNAVAILABLE:
                    pesan = "Lokasi tidak tersedia.";
                    break;

                case error.TIMEOUT:
                    pesan = "Pengambilan lokasi terlalu lama. Coba lagi.";
                    break;

                default:
                    pesan = "Terjadi kesalahan saat mengambil lokasi.";

            }


            status.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    ${pesan}
                </div>
            `;

        },

        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }

    );

}


/*
|--------------------------------------------------------------------------
| HITUNG JARAK
| Haversine Formula
|--------------------------------------------------------------------------
*/

function hitungJarak(lat1, lon1, lat2, lon2) {

    const R = 6371000;

    const dLat =
        (lat2 - lat1) * Math.PI / 180;

    const dLon =
        (lon2 - lon1) * Math.PI / 180;


    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) *
        Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);


    const c =
        2 * Math.atan2(
            Math.sqrt(a),
            Math.sqrt(1 - a)
        );


    return R * c;
}

</script>

</body>

</html>