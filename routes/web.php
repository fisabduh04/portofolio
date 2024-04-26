<?php

use app\Livewire\Home;
use Livewire\Livewire;
use app\Livewire\About;
use App\Livewire\Kelas;
use App\Livewire\Tutorial;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CobaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TahunAjaranController;


Route::get('/', [HomeController::class, 'dashboard']);
Route::get('/index', [HomeController::class, 'index']);
Route::resource('pegawai', PegawaiController::class);

Route::resource('tahun', TahunAjaranController::class);

Route::resource('kelas', KelasController::class);

Route::resource('jurusan', JurusanController::class);

Route::resource('mapel', MapelController::class);
Route::post('importmapel', [MapelController::class,'import'])->name('importmapel');

Route::resource('siswa', SiswaController::class);
Route::get('/Addpegawai', [PegawaiController::class, 'Addpegawai']);






// Route uji coba
Route::get('/coba', [CobaController::class, 'index']);
Route::get('/tabel', [CobaController::class, 'tabel']);

Route::get('product', [ProductController::class, 'index'])->name('product.index');
Route::post('product', [ProductController::class, 'update'])->name('product.update');
Route::post('product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');



Route::get('/tutorial', function () {
  return view('coba.tutorial');
});

Route::get('/tampilan', function () {
  return view('coba.tabel');
});

Route::get('/employe', function () {
    return view('welcome');
  });

Route::get('tutorial', Tutorial::class);
// Route::get('/', action: \App\Livewire\Home::class)->name('home');
// Route::get('/about', action: \App\Livewire\About::class)->name('home');
