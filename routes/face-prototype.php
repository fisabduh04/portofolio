<?php

use App\Http\Controllers\FacePrototypeController;
use Illuminate\Support\Facades\Route;

Route::get('/uji/absensi-wajah', [FacePrototypeController::class, 'index'])->name('face-prototype.index');
Route::post('/uji/absensi-wajah/access', [FacePrototypeController::class, 'access'])->name('face-prototype.access');
Route::post('/uji/absensi-wajah/match', [FacePrototypeController::class, 'store'])->name('face-prototype.match');
