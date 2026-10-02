<?php

session_start();

require_once "../config/koneksi.php";
require_once "../config/lokasi.php";


/*
|--------------------------------------------------------------------------
| CEK LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo json_encode([
        'status' => false,
        'message' => 'Anda belum login.'
    ]);
    exit;
}

if ($_SESSION['role'] !== 'karyawan') {
    echo json_encode([
        'status' => false,
        'message' => 'Akses ditolak.'
    ]);
    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL ID KARYAWAN
|--------------------------------------------------------------------------
|
| Kita gunakan session id.
| Kalau sebelumnya login menyimpan karyawan_id,
| kode ini juga bisa membacanya.
|
*/

$karyawan_id = $_SESSION['karyawan_id'] ?? $_SESSION['id'] ?? null;

if (!$karyawan_id) {

    echo json_encode([
        'status' => false,
        'message' => 'ID karyawan tidak ditemukan dalam session.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| CEK FOTO
|--------------------------------------------------------------------------
*/

if (!isset($_FILES['foto'])) {

    echo json_encode([
        'status' => false,
        'message' => 'Foto absensi belum dikirim.'
    ]);

    exit;
}


$foto = $_FILES['foto'];


/*
|--------------------------------------------------------------------------
| VALIDASI FOTO
|--------------------------------------------------------------------------
*/

if ($foto['error'] !== UPLOAD_ERR_OK) {

    echo json_encode([
        'status' => false,
        'message' => 'Foto gagal diupload.'
    ]);

    exit;
}


$allowed_types = [
    'image/jpeg',
    'image/png',
    'image/webp'
];

if (!in_array($foto['type'], $allowed_types)) {

    echo json_encode([
        'status' => false,
        'message' => 'Format foto harus JPG, PNG, atau WEBP.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| BATAS UKURAN FOTO
|--------------------------------------------------------------------------
*/

$max_size = 5 * 1024 * 1024;

if ($foto['size'] > $max_size) {

    echo json_encode([
        'status' => false,
        'message' => 'Ukuran foto maksimal 5 MB.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL GPS
|--------------------------------------------------------------------------
*/

$latitude = isset($_POST['latitude'])
    ? (float) $_POST['latitude']
    : null;

$longitude = isset($_POST['longitude'])
    ? (float) $_POST['longitude']
    : null;


if ($latitude === null || $longitude === null) {

    echo json_encode([
        'status' => false,
        'message' => 'Lokasi GPS belum tersedia.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| MODE TESTING
|--------------------------------------------------------------------------
|
| Kalau testing aktif, gunakan koordinat testing.
| Kalau false, gunakan GPS asli dari HP.
|
*/

if ($mode_testing) {

    $latitude = $latitude_testing;
    $longitude = $longitude_testing;

}


/*
|--------------------------------------------------------------------------
| HITUNG JARAK KE APOTEK
|--------------------------------------------------------------------------
*/

function hitungJarak(
    $lat1,
    $lon1,
    $lat2,
    $lon2
) {

    $R = 6371000;

    $dLat = deg2rad($lat2 - $lat1);

    $dLon = deg2rad($lon2 - $lon1);

    $a =
        sin($dLat / 2) * sin($dLat / 2)
        +
        cos(deg2rad($lat1))
        *
        cos(deg2rad($lat2))
        *
        sin($dLon / 2)
        *
        sin($dLon / 2);

    $c =
        2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

    return $R * $c;
}


$jarak = hitungJarak(
    $latitude,
    $longitude,
    $latitude_apotek,
    $longitude_apotek
);


/*
|--------------------------------------------------------------------------
| CEK RADIUS
|--------------------------------------------------------------------------
*/

if ($jarak > $radius_maksimal) {

    echo json_encode([
        'status' => false,
        'message' => 'Anda berada di luar radius absensi.',
        'jarak' => round($jarak, 2)
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| TANGGAL & JAM
|--------------------------------------------------------------------------
*/

date_default_timezone_set('Asia/Jakarta');

$tanggal = date('Y-m-d');
$jam_masuk = date('H:i:s');


/*
|--------------------------------------------------------------------------
| CEK APAKAH SUDAH ABSEN HARI INI
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT id
     FROM absensi
     WHERE karyawan_id = ?
     AND tanggal = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $karyawan_id,
    $tanggal
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) > 0) {

    echo json_encode([
        'status' => false,
        'message' => 'Anda sudah melakukan absensi masuk hari ini.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL JADWAL HARI INI
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT jam_masuk, jam_pulang
     FROM jadwal
     WHERE karyawan_id = ?
     AND tanggal = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $karyawan_id,
    $tanggal
);

mysqli_stmt_execute($stmt);

$result_jadwal = mysqli_stmt_get_result($stmt);

$jadwal = mysqli_fetch_assoc($result_jadwal);


/*
|--------------------------------------------------------------------------
| CEK JADWAL
|--------------------------------------------------------------------------
*/

if (!$jadwal) {

    echo json_encode([
        'status' => false,
        'message' => 'Tidak ada jadwal kerja untuk hari ini.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| TENTUKAN STATUS
|--------------------------------------------------------------------------
*/

if ($jam_masuk <= $jadwal['jam_masuk']) {

    $status = 'Hadir';

} else {

    $status = 'Terlambat';

}


/*
|--------------------------------------------------------------------------
| BUAT FOLDER UPLOAD
|--------------------------------------------------------------------------
*/

$folder = "../uploads/absensi/";


if (!is_dir($folder)) {

    mkdir(
        $folder,
        0777,
        true
    );

}


/*
|--------------------------------------------------------------------------
| NAMA FILE
|--------------------------------------------------------------------------
*/

$extension = strtolower(
    pathinfo(
        $foto['name'],
        PATHINFO_EXTENSION
    )
);

$nama_file =
    'masuk_' .
    $karyawan_id .
    '_' .
    date('Ymd_His') .
    '_' .
    bin2hex(random_bytes(4)) .
    '.' .
    $extension;


$path_file =
    $folder . $nama_file;


/*
|--------------------------------------------------------------------------
| SIMPAN FOTO
|--------------------------------------------------------------------------
*/

if (!move_uploaded_file(
    $foto['tmp_name'],
    $path_file
)) {

    echo json_encode([
        'status' => false,
        'message' => 'Foto gagal disimpan.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| SIMPAN ABSENSI
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,

    "INSERT INTO absensi
    (
        karyawan_id,
        tanggal,
        jam_masuk,
        foto_masuk,
        latitude_masuk,
        longitude_masuk,
        jarak_masuk,
        status
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?)"
);


mysqli_stmt_bind_param(
    $stmt,
    "isssddds",
    $karyawan_id,
    $tanggal,
    $jam_masuk,
    $nama_file,
    $latitude,
    $longitude,
    $jarak,
    $status
);


if (!mysqli_stmt_execute($stmt)) {

    /*
    |--------------------------------------------------------------
    | Kalau database gagal, hapus foto yang sudah disimpan
    |--------------------------------------------------------------
    */

    if (file_exists($path_file)) {
        unlink($path_file);
    }


    echo json_encode([
        'status' => false,
        'message' => 'Absensi gagal disimpan ke database.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| BERHASIL
|--------------------------------------------------------------------------
*/

echo json_encode([
    'status' => true,
    'message' => 'Absensi masuk berhasil.',
    'tanggal' => $tanggal,
    'jam' => $jam_masuk,
    'status_absensi' => $status,
    'jarak' => round($jarak, 2)
]);

exit;