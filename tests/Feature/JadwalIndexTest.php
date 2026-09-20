<?php

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    DB::purge('sqlite');
    DB::connection()->getPdo()->sqliteCreateFunction('FIELD', function ($value, ...$values) {
        $position = array_search($value, $values, true);

        return $position === false ? 0 : $position + 1;
    });
    $this->withoutVite();
    \Illuminate\Support\Facades\View::share('sekolah', new \App\Models\Sekolah);
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110240_create_mapels_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
        'database/migrations/2024_05_04_110343_create_jadwals_table.php',
        'database/migrations/2024_05_04_110407_create_logbooks_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    DB::purge('sqlite');
});

test('daily attendance uses the current active year instead of an old cached year', function () {
    $old = Tahun::factory()->create(['isActive' => true]);
    $current = Tahun::factory()->create(['isActive' => false]);
    Cache::put('active_year_default', $old, 3600);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));

    $old->update(['isActive' => false]);
    $current->update(['isActive' => true]);

    $this->get(route('jadwal.presensiHarian', ['date' => '2026-09-21']))->assertOk()
        ->assertViewHas('activeYear', fn ($year) => $year->id === $current->id);

    $current->update(['isActive' => false]);
    $this->from(route('jadwal.index'))->get(route('jadwal.presensiHarian'))
        ->assertRedirect(route('jadwal.index'))
        ->assertSessionHas('error', 'Tidak ada tahun ajaran aktif.');
});

test('schedule employee and class options reflect current data despite old caches', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
    Cache::put('dropdown_pegawai', collect(), 3600);
    Cache::put('dropdown_kelas', collect(), 3600);
    $this->get(route('jadwal.index'))->assertOk();
    $pegawai = Pegawai::factory()->create(['name' => 'Guru Baru']);
    $kelas = Kelas::factory()->create(['kelas' => 'Kelas Baru']);

    $this->get(route('jadwal.index'))->assertOk()
        ->assertViewHas('pegawai', fn ($options) => $options->firstWhere('id', $pegawai->id)?->name === 'Guru Baru')
        ->assertViewHas('kelas', fn ($options) => $options->firstWhere('id', $kelas->id)?->kelas === 'Kelas Baru');

    $pegawai->update(['name' => 'Guru Terbaru']);
    $kelas->update(['kelas' => 'Kelas Terbaru']);
    $this->get(route('jadwal.index'))->assertOk()
        ->assertViewHas('pegawai', fn ($options) => $options->firstWhere('id', $pegawai->id)?->name === 'Guru Terbaru')
        ->assertViewHas('kelas', fn ($options) => $options->firstWhere('id', $kelas->id)?->kelas === 'Kelas Terbaru');

    $pegawai->delete();
    $kelas->delete();
    $this->get(route('jadwal.index'))->assertOk()
        ->assertViewHas('pegawai', fn ($options) => ! $options->contains('id', $pegawai->id))
        ->assertViewHas('kelas', fn ($options) => ! $options->contains('id', $kelas->id));
});

test('schedule subject options reflect additions edits and deletions despite a stale cache', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
    Cache::put('dropdown_mapel', collect(), 3600);
    $this->get(route('jadwal.index'))->assertOk();

    Livewire::test(\App\Livewire\Mapel\Data::class)->call('add')
        ->set('kode.1', 'BARU')->set('mapel.1', 'Mata Pelajaran Baru')
        ->set('jurusan.1', '')->call('store')->assertHasNoErrors();
    $mapel = Mapel::where('kode', 'BARU')->firstOrFail();

    $response = $this->get(route('jadwal.index'))->assertOk();
    $document = new DOMDocument;
    $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($document);
    expect($xpath->query('//select[@data-select-native]//option[@value="'.$mapel->id.'" and text()="Mata Pelajaran Baru"]')->length)->toBeGreaterThan(0);

    Livewire::test(\App\Livewire\Mapel\Data::class)->call('edit', $mapel->id)
        ->set('editmapel', 'Nama Mapel Terbaru')->call('update', $mapel->id)->assertHasNoErrors();
    $this->get(route('jadwal.index'))->assertOk()
        ->assertViewHas('mapel', fn ($options) => $options->firstWhere('id', $mapel->id)?->mapel === 'Nama Mapel Terbaru');

    Livewire::test(\App\Livewire\Mapel\Data::class)->call('del', $mapel->id);
    $this->get(route('jadwal.index'))->assertOk()
        ->assertViewHas('mapel', fn ($options) => ! $options->contains('id', $mapel->id));
});

test('all years shows every active period and ignores a stale dropdown cache', function (array $filters) {
    $old = Tahun::factory()->create();
    Cache::put('dropdown_tahun', collect([$old]), 3600);
    Jadwal::factory()->create(['tahun_id' => $old->id]);
    $active = Tahun::factory()->count(2)->create(['isActive' => true]);
    $first = Jadwal::factory()->create(['tahun_id' => $active[0]->id]);
    $second = Jadwal::factory()->create(['tahun_id' => $active[1]->id]);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->get(route('jadwal.index', $filters))
        ->assertOk()
        ->assertViewHas('jadwals', fn ($rows) => $rows->pluck('id')->sort()->values()->all() === [$first->id, $second->id])
        ->assertViewHas('tahun', fn ($years) => $years->pluck('id')->sort()->values()->all() === $active->pluck('id')->all());
})->with(['default' => [[]], 'empty' => [['filter_tahun' => '']], 'all' => [['filter_tahun' => 'all']]]);

test('an explicit period only shows its schedules and conflicts', function () {
    $active = Tahun::factory()->count(2)->create(['isActive' => true]);
    $first = Jadwal::factory()->create(['tahun_id' => $active[0]->id]);
    $conflict = Jadwal::factory()->create(['tahun_id' => $active[0]->id, 'kelas_id' => $first->kelas_id]);
    Jadwal::factory()->create(['tahun_id' => $active[1]->id, 'kelas_id' => $first->kelas_id]);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->get(route('jadwal.index', ['filter_tahun' => $active[0]->id]))
        ->assertOk()
        ->assertViewHas('jadwals', fn ($rows) => $rows->total() === 2)
        ->assertViewHas('jadwalBentrokIds', [$first->id, $conflict->id]);
});

test('deactivated periods disappear immediately even with an explicit filter', function () {
    $year = Tahun::factory()->create(['isActive' => true]);
    Jadwal::factory()->create(['tahun_id' => $year->id]);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->get(route('jadwal.index'))->assertOk();
    $year->update(['isActive' => false]);

    foreach ([[], ['filter_tahun' => $year->id]] as $filters) {
        $this->get(route('jadwal.index', $filters))->assertOk()
            ->assertViewHas('jadwals', fn ($rows) => $rows->total() === 0)
            ->assertViewHas('totalBentrok', 0);
    }
});

test('schedule writes return to the same filters sorting and page', function (string $operation) {
    $year = Tahun::factory()->create(['isActive' => true]);
    $jadwal = Jadwal::factory()->create(['tahun_id' => $year->id]);
    $origin = route('jadwal.index', ['filter_tahun' => $year->id, 'filter_kelas' => $jadwal->kelas_id,
        'filter_hari' => 'Senin', 'search' => 'Guru', 'per_page' => 25, 'sort' => 'jam', 'direction' => 'desc', 'page' => 2]);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from($origin);
    $data = $jadwal->only(['tahun_id', 'kelas_id', 'mapel_id', 'pegawai_id', 'hari', 'jam', 'mulai', 'akhir']);
    $data['jam'] = '2';
    $data['mulai'] = '09:00';
    $data['akhir'] = '10:00';

    $response = match ($operation) {
        'store' => $this->post(route('jadwal.store'), $data),
        'update' => $this->put(route('jadwal.update', $jadwal), $data),
        'destroy' => $this->delete(route('jadwal.destroy', $jadwal)),
        'bulkDelete' => $this->delete(route('jadwal.bulkDelete'), ['ids' => [$jadwal->id]]),
        'updateAll' => $this->post(route('jadwal.updateAll'), collect($data)->except('tahun_id')->map(fn ($value) => [$value])->all() + ['tahun_id' => $year->id, 'id' => [$jadwal->id]]),
    };
    $response->assertRedirect($origin);
    if (in_array($operation, ['destroy', 'bulkDelete'])) {
        $this->assertDatabaseMissing('jadwals', ['id' => $jadwal->id]);
    } else {
        $this->assertDatabaseHas('jadwals', $data);
    }
})->with(['store', 'update', 'destroy', 'bulkDelete', 'updateAll']);

test('changing filters preserves the page size in the submitted form', function () {
    $response = $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->get(route('jadwal.index', ['per_page' => 25]))->assertOk();
    $document = new DOMDocument;
    $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($document);
    expect($xpath->evaluate('string(//form[@id="searchForm"]//input[@name="per_page"]/@value)'))->toBe('25');
});

test('all active periods detect conflicts within each period only', function () {
    $years = Tahun::factory()->count(2)->create(['isActive' => true]);
    $first = Jadwal::factory()->create(['tahun_id' => $years[0]->id]);
    $second = Jadwal::factory()->create(['tahun_id' => $years[1]->id, 'kelas_id' => $first->kelas_id]);
    $conflict = Jadwal::factory()->create(['tahun_id' => $years[1]->id, 'kelas_id' => $first->kelas_id]);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->get(route('jadwal.index', ['filter_tahun' => 'all']))->assertOk()
        ->assertViewHas('jadwalBentrokIds', [$second->id, $conflict->id]);
});

test('import returns to the same table state', function () {
    $jadwal = Jadwal::factory()->create(['tahun_id' => Tahun::factory()->create(['isActive' => true])->id]);
    $origin = route('jadwal.index', ['filter_tahun' => $jadwal->tahun_id, 'per_page' => 25, 'page' => 2]);
    $csv = "tahun,kelas,mapel,guru,hari,jam,mulai,akhir\n{$jadwal->tahun_id},{$jadwal->kelas_id},{$jadwal->mapel_id},{$jadwal->pegawai_id},Selasa,2,09:00,10:00\n";
    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('jadwal.csv', $csv);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from($origin)->post(route('jadwal.import'), ['file' => $file])
        ->assertRedirect($origin)->assertSessionHas('success');
    $this->assertDatabaseHas('jadwals', ['tahun_id' => $jadwal->tahun_id, 'hari' => 'Selasa', 'jam' => 2]);
});

test('exported schedules can be imported repeatedly without duplicates or losing their period', function () {
    $jadwal = Jadwal::factory()->create(['tahun_id' => Tahun::factory()->create(['isActive' => false])->id, 'mulai' => '07:00:02', 'akhir' => '08:00:00', 'ket' => 'Catatan ekspor']);
    Tahun::factory()->create(['isActive' => true]);
    Pegawai::factory()->create(['name' => $jadwal->pegawai->name]);
    $bytes = \Maatwebsite\Excel\Facades\Excel::raw(new \App\Exports\JadwalExport([$jadwal->id]), \Maatwebsite\Excel\Excel::XLSX);
    $jadwal->update(['jam' => 9, 'ket' => 'Diubah']);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));

    foreach ([1, 2] as $attempt) {
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('jadwal.xlsx', $bytes);
        $this->from(route('jadwal.index'))->post(route('jadwal.import'), ['file' => $file])
            ->assertSessionHasNoErrors()->assertSessionHas('success', 'Impor selesai: 0 jadwal ditambahkan, 1 jadwal diperbarui.');
    }
    $this->assertDatabaseCount('jadwals', 1);
    $this->assertDatabaseHas('jadwals', ['id' => $jadwal->id, 'tahun_id' => $jadwal->tahun_id, 'jam' => 1, 'mulai' => '07:00:02', 'ket' => 'Catatan ekspor']);
});

test('legacy exported period and Excel numeric times import correctly', function () {
    $jadwal = Jadwal::factory()->create(['tahun_id' => Tahun::factory()->create(['isActive' => false])->id]);
    Tahun::factory()->create(['isActive' => true]);
    $csv = "ID,Tahun,Kelas,Hari,Mapel,Guru,Jam,Mulai,Akhir,Keterangan\n{$jadwal->id},{$jadwal->tahun->tahun} - {$jadwal->tahun->semester},{$jadwal->kelas_id},selasa,{$jadwal->mapel_id},{$jadwal->pegawai_id},2,0.291666666666667,0.333333333333333,Diperbarui\n";
    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('jadwal.csv', $csv);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('jadwal.index'))->post(route('jadwal.import'), ['file' => $file])
        ->assertSessionHasNoErrors()->assertSessionHas('success');
    $this->assertDatabaseCount('jadwals', 1);
    $this->assertDatabaseHas('jadwals', ['id' => $jadwal->id, 'tahun_id' => $jadwal->tahun_id, 'hari' => 'Selasa', 'mulai' => '07:00:00', 'akhir' => '08:00:00', 'ket' => 'Diperbarui']);
});

test('separate semester selects the right period when importing names without ids', function () {
    $ganjil = Tahun::factory()->create(['tahun' => '2026-2027', 'semester' => 'Ganjil']);
    $genap = Tahun::factory()->create(['tahun' => '2026-2027', 'semester' => 'Genap']);
    $jadwal = Jadwal::factory()->create(['tahun_id' => $ganjil->id]);
    $jadwal->kelas->update(['kelas' => 'XI A DKV']);
    $jadwal->mapel->update(['mapel' => 'Matematika']);
    $jadwal->pegawai->update(['name' => 'Guru Contoh']);
    $csv = "Tahun,Semester,Kelas,Hari,Mapel,Guru,Jam,Mulai,Akhir\n2026-2027,Genap,XI A DKV,Selasa,Matematika,Guru Contoh,1,07:00,08:00\n";
    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('jadwal.csv', $csv);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('jadwal.index'))->post(route('jadwal.import'), ['file' => $file])
        ->assertSessionHasNoErrors()->assertSessionHas('success', 'Impor selesai: 1 jadwal ditambahkan, 0 jadwal diperbarui.');
    $this->assertDatabaseHas('jadwals', ['tahun_id' => $genap->id, 'kelas_id' => $jadwal->kelas_id, 'mapel_id' => $jadwal->mapel_id, 'pegawai_id' => $jadwal->pegawai_id, 'hari' => 'Selasa']);
});

test('an empty schedule file is not reported as a successful import', function () {
    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('jadwal.csv', "Tahun,Kelas,Hari,Jam\n");
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('jadwal.index'))->post(route('jadwal.import'), ['file' => $file])
        ->assertSessionHasErrors('file')->assertSessionHas('message', 'Baris 2: Berkas tidak berisi data jadwal.');
    $this->assertDatabaseCount('jadwals', 0);
});

test('invalid schedule import reports its row and leaves all schedules untouched', function (string $failure) {
    $jadwal = Jadwal::factory()->create(['tahun_id' => Tahun::factory()->create(['isActive' => true])->id]);
    $row = ['', $jadwal->tahun_id, $jadwal->kelas_id, 'Selasa', $jadwal->mapel_id, $jadwal->pegawai_id, 2, '09:00', '10:00'];
    $bad = $row;
    match ($failure) {
        'year' => $bad[1] = 'Tahun tidak ada',
        'id' => $bad[0] = 999999,
        'class' => $bad[2] = 999999,
        'time' => $bad[7] = '25:00',
        'end' => $bad[8] = '08:00',
        'required' => $bad[3] = '',
        'jam' => $bad[6] = 0,
        'duplicate' => $bad[0] = $row[0] = $jadwal->id,
        'ambiguous' => $bad[5] = 'Nama Sama',
    };
    if ($failure === 'ambiguous') {
        Pegawai::factory()->count(2)->create(['name' => 'Nama Sama']);
    }
    $csv = "ID,Tahun,Kelas,Hari,Mapel,Guru,Jam,Mulai,Akhir\n".implode(',', $row)."\n".implode(',', $bad)."\n";
    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('jadwal.csv', $csv);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('jadwal.index'))->post(route('jadwal.import'), ['file' => $file])
        ->assertSessionHasErrors('file')->assertSessionHas('message', fn ($message) => str_contains($message, 'Baris 3:'));
    $this->assertDatabaseCount('jadwals', 1);
    $this->assertDatabaseHas('jadwals', ['id' => $jadwal->id, 'jam' => 1]);
})->with(['year', 'id', 'class', 'time', 'end', 'required', 'jam', 'duplicate', 'ambiguous']);

test('failed edits restore all values only to the edited row', function (string $failure) {
    $year = Tahun::factory()->create(['isActive' => true]);
    $jadwal = Jadwal::factory()->create(['tahun_id' => $year->id]);
    $other = Jadwal::factory()->create(['tahun_id' => $year->id]);
    $data = $jadwal->only(['tahun_id', 'kelas_id', 'mapel_id', 'pegawai_id', 'hari', 'jam', 'mulai', 'akhir']);
    $data['jam'] = '4';
    $data['ket'] = 'Catatan yang belum disimpan';
    if ($failure === 'invalid') {
        $data['akhir'] = '06:00';
    } else {
        $data['kelas_id'] = $other->kelas_id;
    }
    $origin = route('jadwal.index');
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from($origin)->put(route('jadwal.update', $jadwal), $data)
        ->assertRedirect($origin)->assertSessionHas('_old_input.ket', $data['ket']);
    $this->assertDatabaseHas('jadwals', ['id' => $jadwal->id, 'jam' => 1]);

    $response = $this->get($origin)->assertOk();
    if ($failure === 'invalid') {
        $response->assertSee('Jam selesai harus lebih dari jam mulai.');
    }
    $document = new DOMDocument;
    $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($document);
    expect($xpath->evaluate('string(//input[@form="form-edit-'.$jadwal->id.'" and @name="ket"]/@value)'))->toBe($data['ket']);
    expect($xpath->evaluate('string(//input[@form="form-edit-'.$jadwal->id.'" and @name="jam"]/@value)'))->toBe('4');
    expect($xpath->evaluate('string(//input[@form="form-edit-'.$other->id.'" and @name="jam"]/@value)'))->toBe('1');
    expect($xpath->evaluate('string(//select[@form="form-edit-'.$jadwal->id.'" and @name="kelas_id"]/option[@selected]/@value)'))->toBe((string) $data['kelas_id']);
})->with(['invalid', 'conflict']);

test('a failed bulk row restores the whole batch without partially saving', function (string $failure) {
    $jadwal = Jadwal::factory()->create(['tahun_id' => Tahun::factory()->create(['isActive' => true])->id]);
    $data = [
        'tahun_id' => $jadwal->tahun_id, 'id' => ['', ''],
        'kelas_id' => [$jadwal->kelas_id, $jadwal->kelas_id],
        'mapel_id' => [$jadwal->mapel_id, $jadwal->mapel_id],
        'pegawai_id' => [$jadwal->pegawai_id, $jadwal->pegawai_id],
        'hari' => ['Selasa', 'Selasa'], 'jam' => ['2', '3'],
        'mulai' => ['09:00', '09:30'], 'akhir' => ['10:00', $failure === 'invalid' ? '08:00' : '10:30'],
        'ket' => ['Catatan pertama', 'Catatan kedua'],
    ];
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('jadwal.index'))->post(route('jadwal.updateAll'), $data)
        ->assertRedirect(route('jadwal.index'))->assertSessionHasErrors()
        ->assertSessionHas('_old_input.ket', $data['ket'])
        ->assertSessionHas('_old_input.mulai', $data['mulai']);
    $this->assertDatabaseCount('jadwals', 1);
    $response = $this->get(route('jadwal.index'))->assertOk()->assertSee('Data belum disimpan.');
    if ($failure === 'invalid') {
        $response->assertSee('Jam selesai harus lebih dari jam mulai.');
    }
})->with(['invalid', 'conflict']);

test('bulk validation keeps drafts when the year or a required field is missing', function () {
    $data = ['id' => [''], 'kelas_id' => [''], 'jam' => ['3'], 'ket' => ['Tetap simpan isian ini']];
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('jadwal.index'))->post(route('jadwal.updateAll'), $data)
        ->assertSessionHasErrors('tahun_id')->assertSessionHas('_old_input.ket', $data['ket']);
    $this->assertDatabaseCount('jadwals', 0);
});
