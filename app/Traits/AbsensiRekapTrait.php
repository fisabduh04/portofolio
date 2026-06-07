<?php

namespace App\Traits;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Siswa;
use App\Models\Tahun;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

trait AbsensiRekapTrait
{
    /**
     * Get Private Rekap Data for Daily Recap
     */
    protected function getPrivateRekapData($date, $kelasId, $pegawaiId, $typeGuru)
    {
        $rekapData = collect([]);
        $summaryStats = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0, 'Pulang' => 0, 'Libur' => 0, 'Total' => 0];

        if ($kelasId) {
            $tahunAktif = Tahun::aktif()->first();

            // 1. Ambil Siswa
            $students = Siswa::whereHas('KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
            })->orderBy('nama')->get();

            // 2. Ambil Absensi
            $absensis = Absensi::with(['logbook.jadwal.mapel', 'logbook.jadwal.pegawai', 'logbook.pegawai'])
                ->whereHas('siswa', function ($q) use ($kelasId, $tahunAktif) {
                    $q->whereHas('KelasSiswa', function ($sq) use ($kelasId, $tahunAktif) {
                        $sq->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
                    });
                })
                ->whereHas('logbook', function ($q) use ($date, $pegawaiId, $typeGuru) {
                    $q->where('tanggal', $date);

                    // Filter Pegawai Spesifik
                    if ($pegawaiId) {
                        $q->where('pegawai_id', $pegawaiId);
                    }

                    // Filter Tipe Guru (Mapel vs Piket)
                    if ($typeGuru === 'mapel') {
                        // Kategori 'mapel' atau 'piket_sub' (karena piket_sub adalah guru pengganti mapel)
                        $q->whereIn('kategori', ['mapel', 'piket_sub']);
                    } elseif ($typeGuru === 'piket') {
                        // Piket Harian Murni
                        $q->whereIn('kategori', ['piket_masuk', 'piket_pulang']);
                    }
                })
                ->get();

            // 3. Proses Data
            $isLibur = HariLibur::isLibur($date, $tahunAktif->id);

            $rekapData = $students->map(function ($student) use ($absensis, $isLibur) {
                $studentLogs = $absensis->where('siswa_id', $student->id);

                $stats = [
                    'Hadir' => $studentLogs->where('status', 'Hadir')->count(),
                    'Sakit' => $studentLogs->where('status', 'Sakit')->count(),
                    'Izin' => $studentLogs->where('status', 'Izin')->count(),
                    'Alpha' => $studentLogs->where('status', 'Alpha')->count(),
                    'Pulang' => $studentLogs->where('status', 'Pulang')->count(),
                ];

                $details = $studentLogs->map(function ($log) {
                    $kategori = $log->logbook->kategori;
                    $mapel = '-';
                    $guru = $log->logbook->pegawai->name ?? '-';
                    $isPiketSub = false;

                    if ($kategori == 'mapel') {
                        $mapel = $log->logbook->jadwal->mapel->mapel ?? 'Mapel';
                    } elseif ($kategori == 'piket_sub') {
                        $mapel = ($log->logbook->jadwal->mapel->mapel ?? 'Mapel').' (Guru Pengganti)';
                        $isPiketSub = true;
                    } elseif ($kategori == 'piket_masuk') {
                        $mapel = 'Piket Masuk';
                    } elseif ($kategori == 'piket_pulang') {
                        $mapel = 'Piket Pulang';
                    }

                    return (object) [
                        'jam_ke' => $log->logbook->jadwal->mulai ?? '-',
                        'mapel' => $mapel,
                        'guru' => $guru,
                        'status' => $log->status,
                        'catatan' => $log->logbook->catatan,
                        'is_piket_sub' => $isPiketSub,
                        'foto' => $log->logbook->foto,
                    ];
                })->sortBy('jam_ke');

                // Logic Verdict Strict
                $dailyStatus = 'Hadir';

                if ($stats['Alpha'] > 0) {
                    $dailyStatus = 'Alpha';
                } elseif ($stats['Sakit'] > 0) {
                    $dailyStatus = 'Sakit';
                } elseif ($stats['Izin'] > 0) {
                    $dailyStatus = 'Izin';
                } elseif ($stats['Pulang'] > 0) {
                    $dailyStatus = 'Pulang';
                } elseif ($studentLogs->count() == 0) {
                    $dailyStatus = $isLibur ? 'Libur' : '-';
                }

                // Collect subjects for non-present statuses
                $statDetails = [
                    'Alpha' => $details->where('status', 'Alpha')->pluck('mapel')->unique()->values()->toArray(),
                    'Sakit' => $details->where('status', 'Sakit')->pluck('mapel')->unique()->values()->toArray(),
                    'Izin' => $details->where('status', 'Izin')->pluck('mapel')->unique()->values()->toArray(),
                    'Pulang' => $details->where('status', 'Pulang')->pluck('mapel')->unique()->values()->toArray(),
                ];

                return (object) [
                    'id' => $student->id,
                    'nama' => $student->nama,
                    'daily_status' => $dailyStatus,
                    'stats' => $stats,
                    'stat_details' => $statDetails,
                    'details' => $details,
                ];
            });
        }

        return ['rekapData' => $rekapData, 'summaryStats' => $summaryStats];
    }

    /**
     * Get Private Rekap Data for Monthly Recap
     */
    protected function getPrivateRekapBulananData($month, $year, $kelasId, $typeGuru = 'mapel')
    {
        $tahunAktif = Tahun::aktif()->first();
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $dates = range(1, $daysInMonth);
        $summaryStats = ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0, 'Pulang' => 0];

        $students = Siswa::whereHas('KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
            $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
        })->orderBy('nama')->get();

        $summaryStats['Total'] = $students->count();

        // Corrected Query: Filter via Logbook relationship
        $absensis = Absensi::with('logbook')
            ->whereHas('logbook', function ($q) use ($month, $year, $typeGuru) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);

                if ($typeGuru === 'mapel') {
                    $q->whereIn('kategori', ['mapel', 'piket_sub']);
                } elseif ($typeGuru === 'piket') {
                    $q->whereIn('kategori', ['piket_masuk', 'piket_pulang']);
                }
            })
            ->whereHas('siswa.KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
            })
            ->get()
            ->groupBy('siswa_id');

        $rekapData = $students->map(function ($student) use ($absensis, $dates, $year, $month, &$summaryStats, $tahunAktif) {
            $studentLogs = $absensis->get($student->id, collect([]));
            $dailyStatuses = [];
            $studentStats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'P' => 0];

            foreach ($dates as $day) {
                $dateStr = Carbon::createFromDate($year, $month, $day)->toDateString();

                // Corrected Filter: Check Logbook Date
                $logsForDay = $studentLogs->filter(function ($log) use ($dateStr) {
                    return $log->logbook && $log->logbook->tanggal === $dateStr;
                });

                $status = '-';
                $isLibur = HariLibur::isLibur($dateStr, $tahunAktif->id);
                $counts = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'P' => 0];

                if ($logsForDay->isNotEmpty()) {
                    // Determine Dominant Status for Simple View
                    if ($logsForDay->contains('status', 'Alpha')) {
                        $status = 'Alpha';
                    } elseif ($logsForDay->contains('status', 'Sakit')) {
                        $status = 'Sakit';
                    } elseif ($logsForDay->contains('status', 'Izin')) {
                        $status = 'Izin';
                    } elseif ($logsForDay->contains('status', 'Pulang')) {
                        $status = 'Pulang';
                    } elseif ($logsForDay->contains('status', 'Hadir')) {
                        $status = 'Hadir';
                    }

                    // Count Sessions for Detailed View
                    foreach ($logsForDay as $log) {
                        if ($log->status == 'Hadir') {
                            $counts['H']++;
                        } elseif ($log->status == 'Sakit') {
                            $counts['S']++;
                        } elseif ($log->status == 'Izin') {
                            $counts['I']++;
                        } elseif ($log->status == 'Alpha') {
                            $counts['A']++;
                        } elseif ($log->status == 'Pulang') {
                            $counts['P']++;
                        }
                    }
                } else {
                    if ($isLibur) {
                        $status = 'Libur';
                    }
                }

                $code = '-';
                if ($status == 'Hadir') {
                    $code = 'H';
                    $studentStats['H']++;
                    $summaryStats['Hadir']++;
                } elseif ($status == 'Sakit') {
                    $code = 'S';
                    $studentStats['S']++;
                    $summaryStats['Sakit']++;
                } elseif ($status == 'Izin') {
                    $code = 'I';
                    $studentStats['I']++;
                    $summaryStats['Izin']++;
                } elseif ($status == 'Alpha') {
                    $code = 'A';
                    $studentStats['A']++;
                    $summaryStats['Alpha']++;
                } elseif ($status == 'Pulang') {
                    $code = 'P';
                    $studentStats['P']++;
                    $summaryStats['Pulang']++;
                } elseif ($status == 'Libur') {
                    $code = 'L';
                }

                $dailyStatuses[$day] = [
                    'code' => $code,
                    'is_libur' => $isLibur,
                    'counts' => $counts,
                ];
            }

            return (object) [
                'id' => $student->id, 'nama' => $student->nama,
                'statuses' => $dailyStatuses, 'stats' => $studentStats,
            ];
        });

        return ['rekapData' => $rekapData, 'summaryStats' => $summaryStats, 'dates' => $dates];
    }

    /**
     * Get Private Rekap Data for Yearly Recap
     */
    protected function getPrivateRekapTahunanData($year, $kelasId, $typeGuru = 'mapel')
    {
        $tahunAktif = Tahun::aktif()->first();
        $months = range(1, 12);
        $summaryStats = ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0, 'Pulang' => 0];

        $students = Siswa::whereHas('KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
            $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
        })->orderBy('nama')->get();

        $summaryStats['Total'] = $students->count();

        // Corrected Query: Filter via Logbook relationship
        $absensis = Absensi::with('logbook')
            ->whereHas('logbook', function ($q) use ($year, $typeGuru) {
                $q->whereYear('tanggal', $year);

                if ($typeGuru === 'mapel') {
                    $q->whereIn('kategori', ['mapel', 'piket_sub']);
                } elseif ($typeGuru === 'piket') {
                    $q->whereIn('kategori', ['piket_masuk', 'piket_pulang']);
                }
            })
            ->whereHas('siswa.KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
            })
            ->get()
            ->groupBy('siswa_id');

        $rekapData = $students->map(function ($student) use ($absensis, $months, &$summaryStats) {
            $studentLogs = $absensis->get($student->id, collect([]));
            $monthlyStats = [];
            $totalStats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'P' => 0];

            foreach ($months as $m) {
                // Corrected Filter: Check Logbook Month
                $logsMonth = $studentLogs->filter(function ($log) use ($m) {
                    return $log->logbook && Carbon::parse($log->logbook->tanggal)->month == $m;
                });
                $stats = [
                    'H' => $logsMonth->where('status', 'Hadir')->count(),
                    'S' => $logsMonth->where('status', 'Sakit')->count(),
                    'I' => $logsMonth->where('status', 'Izin')->count(),
                    'A' => $logsMonth->where('status', 'Alpha')->count(),
                    'P' => $logsMonth->where('status', 'Pulang')->count(),
                ];
                $monthlyStats[$m] = $stats;
                $totalStats['H'] += $stats['H'];
                $summaryStats['Hadir'] += $stats['H'];
                $totalStats['S'] += $stats['S'];
                $summaryStats['Sakit'] += $stats['S'];
                $totalStats['I'] += $stats['I'];
                $summaryStats['Izin'] += $stats['I'];
                $totalStats['A'] += $stats['A'];
                $summaryStats['Alpha'] += $stats['A'];
                $totalStats['P'] += $stats['P'];
                $summaryStats['Pulang'] += $stats['P'];
            }

            // Get detailed absent logs list for the whole year
            $details = $studentLogs->filter(function ($log) {
                return in_array($log->status, ['Sakit', 'Izin', 'Alpha', 'Pulang']);
            })->map(function ($log) {
                return [
                    'date' => $log->logbook->tanggal,
                    'status' => $log->status,
                    'month' => Carbon::parse($log->logbook->tanggal)->month,
                ];
            })->values();

            return (object) [
                'id' => $student->id, 'nama' => $student->nama,
                'months' => $monthlyStats, 'total' => $totalStats, 'details' => $details,
            ];
        });

        return ['rekapData' => $rekapData, 'summaryStats' => $summaryStats];
    }

    /**
     * Get Private Rekap Data for Periodic Recap
     */
    protected function getPrivateRekapPeriodeData($startDate, $endDate, $kelasId, $typeGuru = 'mapel')
    {
        $tahunAktif = Tahun::aktif()->first();
        $summaryStats = ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0, 'Pulang' => 0];

        $students = Siswa::whereHas('KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
            $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
        })->orderBy('nama')->get();

        $summaryStats['Total'] = $students->count();

        // Generate date range
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $absensis = Absensi::with('logbook')
            ->whereHas('logbook', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate])
                    ->whereIn('kategori', ['mapel', 'piket_sub']);
            })
            ->whereHas('siswa.KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
            })
            ->get()
            ->groupBy('siswa_id');

        $rekapData = $students->map(function ($student) use ($absensis, &$summaryStats, $dates, $tahunAktif) {
            $studentLogs = $absensis->get($student->id, collect([]));
            $dailyLogs = [];

            // 1. Statistik Per Sesi (Existing)
            $sessionStats = [
                'H' => $studentLogs->where('status', 'Hadir')->count(),
                'S' => $studentLogs->where('status', 'Sakit')->count(),
                'I' => $studentLogs->where('status', 'Izin')->count(),
                'A' => $studentLogs->where('status', 'Alpha')->count(),
                'P' => $studentLogs->where('status', 'Pulang')->count(),
            ];

            // 2. Statistik Per Hari (New)
            $dailyTotal = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'P' => 0];

            // Map data for daily views and calculate daily totals
            foreach ($dates as $dateStr) {
                $logsForDay = $studentLogs->filter(function ($log) use ($dateStr) {
                    return $log->logbook && $log->logbook->tanggal === $dateStr;
                });

                $status = '-';
                $isLibur = HariLibur::isLibur($dateStr, $tahunAktif->id);
                $counts = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'P' => 0];

                if ($logsForDay->isNotEmpty()) {
                    // Determine Dominant Status (Strict Logic)
                    if ($logsForDay->contains('status', 'Alpha')) {
                        $status = 'Alpha';
                        $dailyTotal['A']++;
                    } elseif ($logsForDay->contains('status', 'Sakit')) {
                        $status = 'Sakit';
                        $dailyTotal['S']++;
                    } elseif ($logsForDay->contains('status', 'Izin')) {
                        $status = 'Izin';
                        $dailyTotal['I']++;
                    } elseif ($logsForDay->contains('status', 'Pulang')) {
                        $status = 'Pulang';
                        $dailyTotal['P']++;
                    } elseif ($logsForDay->where('status', 'Hadir')->count() == $logsForDay->count()) {
                        // Hanya H jika SEMUA sesi Hadir
                        $status = 'Hadir';
                        $dailyTotal['H']++;
                    } else {
                        // Campuran H dan lainnya (atau belum lengkap)
                        $status = '-';
                    }

                    // Count Sessions
                    foreach ($logsForDay as $log) {
                        if ($log->status == 'Hadir') {
                            $counts['H']++;
                        } elseif ($log->status == 'Sakit') {
                            $counts['S']++;
                        } elseif ($log->status == 'Izin') {
                            $counts['I']++;
                        } elseif ($log->status == 'Alpha') {
                            $counts['A']++;
                        } elseif ($log->status == 'Pulang') {
                            $counts['P']++;
                        }
                    }
                } else {
                    if ($isLibur) {
                        $status = 'Libur';
                    }
                }

                $code = '-';
                if ($status == 'Hadir') {
                    $code = 'H';
                } elseif ($status == 'Sakit') {
                    $code = 'S';
                } elseif ($status == 'Izin') {
                    $code = 'I';
                } elseif ($status == 'Alpha') {
                    $code = 'A';
                } elseif ($status == 'Pulang') {
                    $code = 'P';
                } elseif ($status == 'Libur') {
                    $code = 'L';
                }

                $dailyLogs[$dateStr] = [
                    'code' => $code,
                    'is_libur' => $isLibur,
                    'counts' => $counts,
                ];
            }

            // Update Global Summary Stats
            $summaryStats['Hadir'] += $sessionStats['H'];
            $summaryStats['Sakit'] += $sessionStats['S'];
            $summaryStats['Izin'] += $sessionStats['I'];
            $summaryStats['Alpha'] += $sessionStats['A'];
            $summaryStats['Pulang'] += $sessionStats['P'];

            // Get detailed absent logs list (for 'detail' view)
            $details = $studentLogs->filter(function ($log) {
                return in_array($log->status, ['Sakit', 'Izin', 'Alpha', 'Pulang']);
            })->map(function ($log) {
                // Determine Mapel Name
                $kategori = $log->logbook->kategori;
                $mapelName = 'Lain-lain';

                if ($kategori === 'mapel' || $kategori === 'piket_sub') {
                    $mapelName = $log->logbook->jadwal->mapel->mapel ?? 'Mata Pelajaran';
                } elseif ($kategori === 'piket_masuk') {
                    $mapelName = 'Piket Masuk';
                } elseif ($kategori === 'piket_pulang') {
                    $mapelName = 'Piket Pulang';
                }

                return [
                    'date' => $log->logbook->tanggal,
                    'status' => $log->status,
                    'mapel' => $mapelName,
                ];
            })->sortBy('date')->values();

            return (object) [
                'id' => $student->id, 'nama' => $student->nama,
                'total' => $sessionStats, 'daily_total' => $dailyTotal, 'details' => $details, 'daily_logs' => $dailyLogs,
            ];
        });

        return ['rekapData' => $rekapData, 'summaryStats' => $summaryStats, 'dates' => $dates];
    }
}
