<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Tahun;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WaliKelasImport implements ToCollection, WithHeadingRow
{
    /** @var array<int, array{kelas_id: int, pegawai_id: int, is_active: bool, keterangan: ?string}> */
    public array $rows = [];

    /** @var array<string, bool> */
    private array $identities = [];

    /** @var array<int, bool> */
    private array $activeClasses = [];

    public function __construct(private Tahun $period) {}

    public function collection(Collection $collection): void
    {
        $classesByName = Kelas::get(['id', 'kelas'])->groupBy('kelas');
        $employees = Pegawai::get(['id', 'nuptk']);
        $employeesById = $employees->keyBy('id');
        $employeesByNuptk = $employees->groupBy('nuptk');

        foreach ($collection as $index => $row) {
            $row = $row->all();
            if (collect($row)->every(fn (mixed $value): bool => $value === null || $value === '')) {
                continue;
            }
            $line = $index + 2;
            $validator = Validator::make($row, [
                'tahun' => ['required'], 'semester' => ['required'], 'kelas' => ['required'],
                'pegawai_id' => ['nullable', 'integer'],
                'nuptk' => ['required_without:pegawai_id'],
                'status' => ['required', 'in:aktif,nonaktif,1,0'],
                'keterangan' => ['nullable', 'string', 'max:2000'],
            ]);
            if ($validator->fails()) {
                throw ValidationException::withMessages(['file' => "Baris {$line}: ".$validator->errors()->first()]);
            }
            if ((string) $row['tahun'] !== (string) $this->period->tahun || (string) $row['semester'] !== (string) $this->period->semester) {
                throw ValidationException::withMessages(['file' => "Baris {$line}: tahun dan semester harus sesuai periode terpilih."]);
            }
            $classes = $classesByName->get($row['kelas'], collect());
            $matches = ! empty($row['pegawai_id'])
                ? collect([$employeesById->get($row['pegawai_id'])])->filter()
                : $employeesByNuptk->get($row['nuptk'], collect());
            if ($classes->count() !== 1 || $matches->count() !== 1) {
                throw ValidationException::withMessages(['file' => "Baris {$line}: kelas atau pegawai tidak ditemukan atau tidak unik."]);
            }
            $assignment = ['kelas_id' => $classes->first()->id, 'pegawai_id' => $matches->first()->id,
                'is_active' => in_array((string) $row['status'], ['aktif', '1'], true),
                'keterangan' => $row['keterangan'] ?? null];
            $identity = $assignment['kelas_id'].':'.$assignment['pegawai_id'];
            if (isset($this->identities[$identity]) || ($assignment['is_active'] && isset($this->activeClasses[$assignment['kelas_id']]))) {
                throw ValidationException::withMessages(['file' => "Baris {$line}: penugasan berulang atau terdapat dua wali aktif pada kelas yang sama."]);
            }
            $this->identities[$identity] = true;
            if ($assignment['is_active']) {
                $this->activeClasses[$assignment['kelas_id']] = true;
            }
            $this->rows[] = $assignment;
            if (count($this->rows) > 5000) {
                throw ValidationException::withMessages(['file' => 'Maksimal 5000 baris dalam satu impor.']);
            }
        }
    }
}
