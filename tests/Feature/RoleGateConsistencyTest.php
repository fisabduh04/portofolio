<?php

use App\Models\Jadwal;
use App\Models\Pegawai;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;

uses(Tests\TestCase::class);

it('applies the role matrix to gates and refuses inactive accounts', function () {
    $matrix = [
        'kepala' => [true, true, true, true, false],
        'admin' => [true, true, true, true, false],
        'operator' => [true, false, true, false, false],
        'guru' => [false, false, true, false, true],
        'staff' => [false, false, true, false, false],
        'bendahara' => [false, false, false, true, false],
        'siswa' => [false, false, false, false, false],
    ];
    foreach ($matrix as $role => [$management, $employees, $reports, $payroll, $teacher]) {
        $actor = new User(['role' => $role, 'is_active' => 1]);
        $abilities = [
            'manage-jadwal' => $management,
            'manage-data-master' => $management,
            'manage-sekolah' => $management,
            'view-kepegawaian' => $employees,
            'view-rekapitulasi' => $reports,
            'manage-payroll' => $payroll,
            'is-guru' => $teacher,
            'access-school-modules' => $role !== 'bendahara',
        ];
        foreach ($abilities as $ability => $allowed) {
            expect(Gate::forUser($actor)->allows($ability))->toBe($allowed, "$role: $ability");
        }
        expect($actor->canManagePayroll())->toBe($payroll);
        $actor->is_active = 0;
        foreach (array_keys($abilities) as $ability) {
            expect(Gate::forUser($actor)->allows($ability))->toBeFalse("$role: $ability");
        }
        expect($actor->canManagePayroll())->toBeFalse();
        expect($actor->hasPiketSchedule())->toBeFalse();
        expect($actor->isPiketOn('2026-10-02'))->toBeFalse();
        expect(Gate::forUser($actor)->allows('input-presensi', [new Jadwal, 'mapel']))->toBeFalse();
    }
    expect(Gate::allows('manage-payroll'))->toBeFalse();
});

it('allows finance managers to open payroll and slips without granting personnel access to treasurers', function () {
    $this->artisan('migrate', ['--no-interaction' => true])->assertExitCode(0);
    $this->withoutVite();
    View::share('sekolah', new Sekolah);
    $employee = Pegawai::factory()->create();
    foreach (['kepala', 'admin', 'bendahara'] as $role) {
        $actor = User::factory()->create(['role' => $role, 'is_active' => 1]);
        $response = $this->actingAs($actor)->get(route('attendance.payroll.index'))->assertOk()
            ->assertSee($employee->name)->assertSee(route('attendance.payroll.index'), false);
        $response->assertSee('id="dropdown-payroll"', false)->assertSee('Rekap Gaji');
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new DOMXPath($document);
        expect($xpath->query('//button[@aria-controls="dropdown-kepegawaian" and @aria-expanded="true"]')->length)->toBe(0);
        expect($xpath->query('//a[not(ancestor::aside) and contains(@href, "/attendance/rules")]')->length)->toBe(0);
        expect($xpath->query('//aside[@id="top-bar-sidebar"]/div/ul/li[last()]/button[@aria-controls="dropdown-payroll" and @aria-expanded="true"]/svg')->length)->toBeGreaterThan(0);
        $this->get(route('attendance.payroll.slip', $employee->id))->assertOk()->assertSee($employee->name);
        if ($role === 'bendahara') {
            $response->assertDontSee('id="dropdown-kepegawaian"', false)
                ->assertDontSee(route('attendance.rules.index'), false)
                ->assertDontSee('id="dropdown-presensi"', false)
                ->assertDontSee('id="dropdown-akademik"', false)
                ->assertDontSee('id="dropdown-absensi"', false)
                ->assertDontSee('id="dropdown-rekap"', false)
                ->assertDontSee(route('operator.users.index'), false);
            $this->get(route('dashboard.index'))->assertRedirect(route('attendance.payroll.index'));
            $this->get(route('face-attendance.index', ['mode' => 'piket']))->assertForbidden();
            $this->get(route('jadwal.index'))->assertForbidden();
            $this->get(route('siswa.index'))->assertForbidden();
            $this->get(route('absensi.rekap-harian'))->assertForbidden();
            $this->post(route('operator.users.store'), [])->assertForbidden();
            $this->post(route('absensi.harian.store'), [])->assertForbidden();
            $this->delete(route('kelassiswa.bulkDelete'), ['ids' => [1]])->assertForbidden();
            foreach (['attendance.rules.index', 'attendance.index', 'attendance.create', 'attendance.setting', 'attendance.report', 'attendance.fingerprint.index'] as $routeName) {
                $this->get(route($routeName))->assertForbidden();
            }
            $this->post(route('attendance.rules.store'), [])->assertForbidden();
            $this->post(route('attendance.store'), [])->assertForbidden();
            $this->get(route('operator.users.index'))->assertForbidden();
        }
    }
});

it('refuses payroll routes to other roles guests and inactive treasurers', function () {
    foreach (['operator', 'guru', 'staff', 'siswa'] as $role) {
        $actor = new User(['role' => $role, 'is_active' => 1, 'pegawai_id' => 12]);
        $this->actingAs($actor)->get(route('attendance.payroll.index'))->assertForbidden();
        $this->get(route('attendance.payroll.slip', 12))->assertForbidden();
    }
    $this->actingAs(new User(['role' => 'bendahara', 'is_active' => 0]))
        ->get(route('attendance.payroll.index'))->assertRedirect(route('login'));
    $this->get(route('attendance.payroll.slip', 12))->assertRedirect(route('login'));
});

it('never grants duty attendance to a treasurer linked to an employee', function () {
    $actor = new User(['role' => 'bendahara', 'is_active' => 1, 'pegawai_id' => 12]);
    expect($actor->hasPiketSchedule())->toBeFalse();
    expect($actor->isPiketOn('2026-10-02'))->toBeFalse();
    expect(Gate::forUser($actor)->allows('input-presensi', [new Jadwal(['pegawai_id' => 12]), 'mapel']))->toBeFalse();
});
it('refuses an existing livewire master data page after the account becomes a treasurer', function () {
    $this->artisan('migrate', ['--no-interaction' => true])->assertExitCode(0);
    $this->withoutVite();
    View::share('sekolah', new Sekolah);
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => 1]);
    $page = $this->actingAs($actor)->get(route('tahun.index'))->assertOk();
    preg_match('/wire:snapshot="([^"]+)"/', $page->getContent(), $matches);
    expect($matches)->toHaveKey(1);
    $snapshot = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
    $actor->update(['role' => 'bendahara']);
    $this->actingAs($actor->fresh())->postJson(route('livewire.update'), [
        'components' => [[
            'snapshot' => $snapshot,
            'updates' => [],
            'calls' => [['path' => '', 'method' => '$refresh', 'params' => []]],
        ]],
    ], ['X-Livewire' => 'true'])->assertForbidden();
});
