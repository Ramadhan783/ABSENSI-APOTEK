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

$username = $_SESSION['username'] ?? 'Karyawan';
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Absen Masuk - Absensi Apotek</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="../assets/css/absen.css">

</head>

<body>

<div class="container py-4">

    <!-- HEADER -->

    <div class="page-header">

        <a href="dashboard.php"
           class="back-button">

            <i class="bi bi-arrow-left"></i>

        </a>

        <div>

            <span>ABSENSI KARYAWAN</span>

            <h3>Absen Masuk</h3>

        </div>

    </div>


    <!-- INFO -->

    <div class="info-card">

        <div class="info-icon">

            <i class="bi bi-person-check"></i>

        </div>

        <div>

            <strong>
                Halo, <?= htmlspecialchars($username); ?> 👋
            </strong>

            <p>
                Silakan ambil foto dan pastikan lokasi berada
                dalam radius 30 meter dari apotek.
            </p>

        </div>

    </div>


    <!-- FOTO -->

    <div class="absen-card">

        <div class="card-title">

            <div class="number">
                1
            </div>

            <div>
                <strong>Foto Kehadiran</strong>

                <small>
                    Ambil foto untuk bukti absensi
                </small>
            </div>

        </div>


        <div class="camera-area">

            <!-- Preview -->

            <img
                id="previewFoto"
                class="photo-preview"
                alt="Preview Foto">


            <!-- Placeholder -->

            <div
                id="cameraPlaceholder"
                class="camera-placeholder">

                <i class="bi bi-camera"></i>

                <span>
                    Belum ada foto
                </span>

            </div>

        </div>


        <!-- File Camera -->

        <input
            type="file"
            id="foto"
            name="foto"
            accept="image/*"
            capture="user"
            class="d-none">


        <button
            type="button"
            class="btn btn-dark w-100 mt-3"
            onclick="document.getElementById('foto').click()">

            <i class="bi bi-camera-fill me-2"></i>

            Ambil Foto

        </button>


        <div
            id="fotoStatus"
            class="small text-secondary text-center mt-2">

            Foto wajib diambil sebelum melakukan absensi.

        </div>

    </div>


    <!-- GPS -->

    <div class="absen-card">

        <div class="card-title">

            <div class="number">
                2
            </div>

            <div>

                <strong>Verifikasi Lokasi</strong>

                <small>
                    Pastikan berada di area apotek
                </small>

            </div>

        </div>


        <div class="location-status">

            <div class="location-icon">

                <i class="bi bi-geo-alt-fill"></i>

            </div>

            <div class="location-text">

                <strong id="locationTitle">
                    Lokasi belum dicek
                </strong>

                <span id="locationDescription">
                    Tekan tombol untuk memeriksa lokasi.
                </span>

            </div>

        </div>


        <div class="location-data">

            <div>

                <span>Latitude</span>

                <strong id="latitude">
                    -
                </strong>

            </div>


            <div>

                <span>Longitude</span>

                <strong id="longitude">
                    -
                </strong>

            </div>


            <div>

                <span>Jarak ke Apotek</span>

                <strong id="jarak">
                    -
                </strong>

            </div>

        </div>


        <button
            type="button"
            id="btnLokasi"
            class="btn btn-outline-dark w-100 mt-3"
            onclick="cekLokasi()">

            <i class="bi bi-crosshair me-2"></i>

            Cek Lokasi

        </button>

    </div>


    <!-- HASIL -->

    <div id="hasil"></div>


    <!-- BUTTON ABSEN -->

    <button
        type="button"
        id="btnAbsen"
        class="btn btn-success btn-lg w-100"
        disabled
        onclick="prosesAbsensi()">

        <i class="bi bi-check-circle me-2"></i>

        Kirim Absen Masuk

    </button>


    <div class="text-center mt-3">

        <small class="text-secondary">

            <i class="bi bi-shield-check me-1"></i>

            Foto dan lokasi digunakan sebagai bukti kehadiran.

        </small>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| KONFIGURASI
|--------------------------------------------------------------------------
*/

const latitudeApotek =
    <?= $latitude_apotek; ?>;

const longitudeApotek =
    <?= $longitude_apotek; ?>;

const radiusMaksimal =
    <?= $radius_maksimal; ?>;

const modeTesting =
    <?= $mode_testing ? 'true' : 'false'; ?>;

const latitudeTesting =
    <?= $latitude_testing; ?>;

const longitudeTesting =
    <?= $longitude_testing; ?>;


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

let fotoSudahDiambil = false;

let lokasiValid = false;

let latitudeUser = null;

let longitudeUser = null;

let jarakUser = null;


/*
|--------------------------------------------------------------------------
| FOTO
|--------------------------------------------------------------------------
*/

document
    .getElementById("foto")
    .addEventListener("change", function(event) {

        const file = event.target.files[0];

        if (!file) {
            return;
        }


        const preview =
            document.getElementById("previewFoto");

        const placeholder =
            document.getElementById("cameraPlaceholder");


        preview.src =
            URL.createObjectURL(file);

        preview.style.display =
            "block";

        placeholder.style.display =
            "none";


        fotoSudahDiambil = true;


        document.getElementById("fotoStatus").innerHTML = `
            <span class="text-success">
                <i class="bi bi-check-circle-fill"></i>
                Foto berhasil diambil.
            </span>
        `;


        cekTombolAbsen();

    });


/*
|--------------------------------------------------------------------------
| CEK LOKASI
|--------------------------------------------------------------------------
*/

function cekLokasi() {

    const title =
        document.getElementById("locationTitle");

    const description =
        document.getElementById("locationDescription");

    const hasil =
        document.getElementById("hasil");


    /*
    |--------------------------------------------------------------------------
    | MODE TESTING
    |--------------------------------------------------------------------------
    */

    if (modeTesting) {

        latitudeUser =
            latitudeTesting;

        longitudeUser =
            longitudeTesting;


        jarakUser =
            hitungJarak(
                latitudeUser,
                longitudeUser,
                latitudeApotek,
                longitudeApotek
            );


        tampilkanLokasi();

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | GPS ASLI
    |--------------------------------------------------------------------------
    */

    if (!navigator.geolocation) {

        hasil.innerHTML = `
            <div class="alert alert-danger">
                Browser tidak mendukung GPS.
            </div>
        `;

        return;
    }


    title.innerText =
        "Mencari lokasi...";

    description.innerText =
        "Mohon tunggu sebentar.";


    navigator.geolocation.getCurrentPosition(

        function(position) {

            latitudeUser =
                position.coords.latitude;

            longitudeUser =
                position.coords.longitude;


            jarakUser =
                hitungJarak(
                    latitudeUser,
                    longitudeUser,
                    latitudeApotek,
                    longitudeApotek
                );


            tampilkanLokasi();

        },


        function(error) {

            title.innerText =
                "Lokasi gagal diperoleh";

            description.innerText =
                "Pastikan GPS dan izin lokasi aktif.";


            hasil.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Tidak dapat mengambil lokasi.
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
| TAMPILKAN LOKASI
|--------------------------------------------------------------------------
*/

function tampilkanLokasi() {

    document.getElementById("latitude").innerText =
        latitudeUser.toFixed(7);


    document.getElementById("longitude").innerText =
        longitudeUser.toFixed(7);


    document.getElementById("jarak").innerText =
        jarakUser.toFixed(2) + " meter";


    const title =
        document.getElementById("locationTitle");

    const description =
        document.getElementById("locationDescription");


    if (jarakUser <= radiusMaksimal) {

        lokasiValid = true;


        title.innerHTML = `
            <span class="text-success">
                <i class="bi bi-check-circle-fill"></i>
                Lokasi Valid
            </span>
        `;


        description.innerText =
            "Anda berada dalam radius absensi.";


        document.getElementById("hasil").innerHTML = `

            <div class="alert alert-success">

                <strong>
                    Lokasi sesuai.
                </strong>

                <br>

                Jarak Anda:
                ${jarakUser.toFixed(2)} meter

            </div>

        `;

    } else {

        lokasiValid = false;


        title.innerHTML = `
            <span class="text-danger">
                <i class="bi bi-x-circle-fill"></i>
                Di Luar Radius
            </span>
        `;


        description.innerText =
            "Anda harus berada maksimal 30 meter dari apotek.";


        document.getElementById("hasil").innerHTML = `

            <div class="alert alert-danger">

                <strong>
                    Absensi tidak dapat dilakukan.
                </strong>

                <br>

                Jarak Anda:
                ${jarakUser.toFixed(2)} meter

            </div>

        `;

    }


    cekTombolAbsen();

}


/*
|--------------------------------------------------------------------------
| CEK TOMBOL ABSEN
|--------------------------------------------------------------------------
*/

function cekTombolAbsen() {

    const tombol =
        document.getElementById("btnAbsen");


    if (
        fotoSudahDiambil &&
        lokasiValid
    ) {

        tombol.disabled = false;

        tombol.classList.remove(
            "btn-secondary"
        );

        tombol.classList.add(
            "btn-success"
        );

    } else {

        tombol.disabled = true;

    }

}


/*
|--------------------------------------------------------------------------
| HITUNG JARAK
|--------------------------------------------------------------------------
*/

function hitungJarak(
    lat1,
    lon1,
    lat2,
    lon2
) {

    const R = 6371000;


    const dLat =
        (lat2 - lat1) *
        Math.PI / 180;


    const dLon =
        (lon2 - lon1) *
        Math.PI / 180;


    const a =
        Math.sin(dLat / 2) *
        Math.sin(dLat / 2) +

        Math.cos(
            lat1 * Math.PI / 180
        ) *

        Math.cos(
            lat2 * Math.PI / 180
        ) *

        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);


    const c =
        2 *
        Math.atan2(
            Math.sqrt(a),
            Math.sqrt(1 - a)
        );


    return R * c;

}


/*
|--------------------------------------------------------------------------
| PROSES ABSENSI
|--------------------------------------------------------------------------
*/

function prosesAbsensi() {

    if (!fotoSudahDiambil) {

        alert("Silakan ambil foto terlebih dahulu.");

        return;
    }


    if (!lokasiValid) {

        alert("Lokasi Anda belum memenuhi syarat.");

        return;
    }


    const fileInput =
        document.getElementById("foto");

    const file =
        fileInput.files[0];


    if (!file) {

        alert("Foto belum tersedia.");

        return;
    }


    const formData =
        new FormData();


    formData.append(
        "foto",
        file
    );


    formData.append(
        "latitude",
        latitudeUser
    );


    formData.append(
        "longitude",
        longitudeUser
    );


    const tombol =
        document.getElementById("btnAbsen");


    tombol.disabled = true;

    tombol.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Menyimpan Absensi...
    `;


    fetch("proses_absen_masuk.php", {

        method: "POST",

        body: formData

    })

    .then(response => {

        return response.json();

    })

    .then(data => {

        if (data.status) {

            document.getElementById("hasil").innerHTML = `

                <div class="alert alert-success">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    <strong>
                        Absensi berhasil!
                    </strong>

                    <br>

                    Jam:
                    ${data.jam}

                    <br>

                    Status:
                    ${data.status_absensi}

                    <br>

                    Jarak:
                    ${data.jarak} meter

                </div>

            `;


            tombol.innerHTML = `
                <i class="bi bi-check-circle-fill me-2"></i>
                Absensi Berhasil
            `;


            tombol.classList.remove(
                "btn-success"
            );


            tombol.classList.add(
                "btn-secondary"
            );


            setTimeout(function() {

                window.location.href =
                    "dashboard.php";

            }, 2000);


        } else {

            alert(
                data.message
                || "Absensi gagal."
            );


            tombol.disabled = false;


            tombol.innerHTML = `
                <i class="bi bi-check-circle me-2"></i>
                Kirim Absen Masuk
            `;

        }

    })

    .catch(error => {

        console.error(
            "Error:",
            error
        );


        alert(
            "Terjadi kesalahan saat mengirim absensi."
        );


        tombol.disabled = false;


        tombol.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>
            Kirim Absen Masuk
        `;

    });

}

</script>

</body>

</html>