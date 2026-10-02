<?php

session_start();

require_once "config/koneksi.php";


/*
|--------------------------------------------------------------------------
| CEK METHOD
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: login.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| AMBIL INPUT
|--------------------------------------------------------------------------
*/

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';


if ($username === '' || $password === '') {

    header("Location: login.php?error=username_password");
    exit;

}


/*
|--------------------------------------------------------------------------
| CARI USER
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, username, password, role, status
     FROM users
     WHERE username = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $username
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);


/*
|--------------------------------------------------------------------------
| USER TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

if (!$user) {

    header("Location: login.php?error=username_password");
    exit;

}


/*
|--------------------------------------------------------------------------
| CEK STATUS AKUN
|--------------------------------------------------------------------------
*/

if ($user['status'] !== 'aktif') {

    header("Location: login.php?error=nonaktif");
    exit;

}


/*
|--------------------------------------------------------------------------
| CEK PASSWORD
|--------------------------------------------------------------------------
*/

if (!password_verify(
    $password,
    $user['password']
)) {

    header("Location: login.php?error=username_password");
    exit;

}


/*
|--------------------------------------------------------------------------
| REGENERATE SESSION
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);


/*
|--------------------------------------------------------------------------
| SESSION USER
|--------------------------------------------------------------------------
*/

$_SESSION['login'] = true;

$_SESSION['user_id'] =
    $user['id'];

$_SESSION['username'] =
    $user['username'];

$_SESSION['role'] =
    $user['role'];


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

if ($user['role'] === 'admin') {

    header(
        "Location: admin/dashboard.php"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| KARYAWAN
|--------------------------------------------------------------------------
*/

if ($user['role'] === 'karyawan') {


    /*
    |--------------------------------------------------------------------------
    | CARI DATA KARYAWAN
    |--------------------------------------------------------------------------
    |
    | users.id dihubungkan dengan
    | karyawan.user_id
    |
    */

    $stmt_karyawan = mysqli_prepare(
        $conn,

        "SELECT id, nama, jabatan, status
         FROM karyawan
         WHERE user_id = ?
         LIMIT 1"
    );


    mysqli_stmt_bind_param(
        $stmt_karyawan,
        "i",
        $user['id']
    );


    mysqli_stmt_execute(
        $stmt_karyawan
    );


    $result_karyawan =
        mysqli_stmt_get_result(
            $stmt_karyawan
        );


    $karyawan =
        mysqli_fetch_assoc(
            $result_karyawan
        );


    /*
    |--------------------------------------------------------------------------
    | DATA KARYAWAN TIDAK DITEMUKAN
    |--------------------------------------------------------------------------
    */

    if (!$karyawan) {

        session_destroy();

        header(
            "Location: login.php?error=username_password"
        );

        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | CEK STATUS KARYAWAN
    |--------------------------------------------------------------------------
    */

    if ($karyawan['status'] !== 'aktif') {

        session_destroy();

        header(
            "Location: login.php?error=nonaktif"
        );

        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN SESSION KARYAWAN
    |--------------------------------------------------------------------------
    */

    $_SESSION['karyawan_id'] =
        $karyawan['id'];

    $_SESSION['nama'] =
        $karyawan['nama'];

    $_SESSION['jabatan'] =
        $karyawan['jabatan'];


    /*
    |--------------------------------------------------------------------------
    | REDIRECT DASHBOARD
    |--------------------------------------------------------------------------
    */

    header(
        "Location: karyawan/dashboard.php"
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| ROLE TIDAK DIKENAL
|--------------------------------------------------------------------------
*/

session_destroy();

header(
    "Location: login.php?error=username_password"
);

exit;