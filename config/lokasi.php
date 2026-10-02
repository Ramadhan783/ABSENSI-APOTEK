<?php

// ==========================================
// LOKASI APOTEK
// ==========================================

$latitude_apotek = -6.892208;
$longitude_apotek = 107.562199;


// ==========================================
// RADIUS ABSENSI
// ==========================================

$radius_maksimal = 30;


// ==========================================
// MODE TESTING
// ==========================================
//
// true  = mode testing AKTIF
// false = mode produksi / GPS asli
//
// SAAT APLIKASI SUDAH DIGUNAKAN KARYAWAN,
// UBAH MENJADI false.
//

$mode_testing = true;


// ==========================================
// KOORDINAT TESTING
// ==========================================
//
// Saat testing, sistem akan menggunakan
// koordinat ini sebagai lokasi HP.
//
// Kita buat sama dengan lokasi apotek
// sehingga jaraknya 0 meter.
//

$latitude_testing = -6.892208;
$longitude_testing = 107.562199;