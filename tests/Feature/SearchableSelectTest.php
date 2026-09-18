<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

uses(TestCase::class);

test('allows repeated forms to supply their own scoped old input', function () {
    $this->session(['_old_input' => ['kelas_id' => ['9', '10']]]);
    $view = $this->blade(
        '<x-form.searchable-select name="kelas_id" :selected="2" :restore-old-input="false" :options="$options" />',
        ['options' => [2 => 'Kelas B', 9 => 'Kelas I']]
    );
    $html = searchableSelectXpath((string) $view);
    expect($html->evaluate('string(//select/option[@selected]/@value)'))->toBe('2');
});

beforeEach(function () {
    $this->withViewErrors([]);
    $this->app['request']->setLaravelSession($this->app['session.store']);
});

function searchableSelectXpath(string $html): DOMXPath
{
    $document = new DOMDocument;
    $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);

    return new DOMXPath($document);
}

test('renders a native form field with the selected zero value before javascript loads', function () {
    $view = $this->blade(
        '<x-form.searchable-select id="category" name="category_id" label="Kategori" :options="$options" :selected="0" required disabled class="custom-width" />',
        ['options' => [0 => 'Umum', 2 => 'Laravel']]
    );

    $html = searchableSelectXpath((string) $view);

    expect($html->evaluate('string(//select/@name)'))->toBe('category_id');
    expect($html->evaluate('string(//select/option[@selected]/@value)'))->toBe('0');
    expect($html->query('//select[@required and @disabled and not(@hidden)]')->length)->toBe(1);
    expect($html->evaluate('string(//select/@class)'))->toContain('custom-width');
    expect($html->evaluate('string(//label/@for)'))->toBe('category');
    expect($html->query('//button[@type="button" and @hidden]')->length)->toBe(1);
    expect($html->query('//input[@name]')->length)->toBe(0);
});

test('restores nested old input and associates the validation message with the field', function () {
    $this->session(['_old_input' => ['rows' => [['category_id' => '2']]]]);
    $this->withViewErrors(['rows.0.category_id' => 'Kategori tidak tersedia.']);

    $view = $this->blade(
        '<x-form.searchable-select id="category" name="rows[0][category_id]" :options="$options" :selected="1" aria-describedby="category-hint" />',
        ['options' => [1 => 'Umum', 2 => 'Laravel']]
    );

    $html = searchableSelectXpath((string) $view);

    expect($html->evaluate('string(//select/option[@selected]/@value)'))->toBe('2');
    expect($html->evaluate('string(//select/@aria-describedby)'))->toBe('category-hint category-error');
    expect($html->evaluate('string(//select/@aria-invalid)'))->toBe('true');
    $view->assertSee('Kategori tidak tersedia.');
});

test('keeps the placeholder selected when the previous input was cleared', function () {
    $this->session(['_old_input' => ['category_id' => '']]);

    $view = $this->blade(
        '<x-form.searchable-select name="category_id" :selected="2" :options="$options" />',
        ['options' => [2 => 'Laravel']]
    );

    $html = searchableSelectXpath((string) $view);

    expect($html->query('//select/option[@selected]')->length)->toBe(1);
    expect($html->evaluate('string(//select/option[@selected]/@value)'))->toBe('');
});

test('generates distinct accessible identifiers for repeated components', function () {
    $view = $this->blade(
        '<x-form.searchable-select name="category_id" label="Pertama" /><x-form.searchable-select name="category_id" label="Kedua" />'
    );

    $html = searchableSelectXpath((string) $view);
    $ids = array_map(fn (DOMNode $node): string => $node->nodeValue, iterator_to_array($html->query('//@id')));

    expect(count($ids))->toBe(count(array_unique($ids)));
    expect($html->evaluate('string((//label)[1]/@for)'))->toBe($html->evaluate('string((//select)[1]/@id)'));
    expect($html->evaluate('string((//input)[2]/@aria-controls)'))->toBe($html->evaluate('string((//div[@role="listbox"])[2]/@id)'));
});

test('escapes option labels values and search text without creating executable markup', function () {
    $dangerousLabel = '<img src=x onerror=alert(1)>';
    $dangerousValue = '" onfocus="alert(1)';

    $view = $this->blade(
        '<x-form.searchable-select name="category_id" :options="$options" :label="$label" :search-placeholder="$label" />',
        ['options' => [$dangerousValue => $dangerousLabel], 'label' => $dangerousLabel]
    );

    $html = searchableSelectXpath((string) $view);

    expect($html->query('//img|//*[@onfocus or @onerror]')->length)->toBe(0);
    expect($html->evaluate('string(//select/option[2])'))->toBe($dangerousLabel);
    expect($html->evaluate('string(//select/option[2]/@value)'))->toBe($dangerousValue);
    expect($html->evaluate('string(//input/@placeholder)'))->toBe($dangerousLabel);
});

test('accepts native disabled options through the slot', function () {
    $view = $this->blade(
        '<x-form.searchable-select name="category_id"><optgroup label="Arsip" disabled><option value="9">Lama</option></optgroup></x-form.searchable-select>'
    );

    $html = searchableSelectXpath((string) $view);

    expect($html->query('//select/optgroup[@disabled]/option[@value="9"]')->length)->toBe(1);
});

test('renders the searchable employee field in the manual attendance form', function () {
    $this->actingAs(User::factory()->make(['role' => UserRole::Admin]));
    $this->withoutVite();
    View::share('sekolah', (object) ['logo_url' => '/logo.png', 'nama_sekolah' => 'Sekolah']);

    $view = $this->view('attendance.create', [
        'pegawais' => collect([(object) ['id' => 7, 'name' => 'Siti Aminah']]),
    ]);

    $html = searchableSelectXpath((string) $view);

    expect($html->query('//form//div[@data-searchable-select]/select[@name="pegawai_id" and @required]')->length)->toBe(1);
    expect($html->evaluate('string(//select[@name="pegawai_id"]/option[@value="7"])'))->toBe('Siti Aminah');
    expect($html->evaluate('string(//form[.//select[@name="pegawai_id"]]/@action)'))->toBe(route('attendance.store'));
});

test('bounds the number of rendered search results', function (int $requested, string $expected) {
    $view = $this->blade(
        '<x-form.searchable-select name="category_id" :max-results="$limit" />',
        ['limit' => $requested]
    );

    $html = searchableSelectXpath((string) $view);

    expect($html->evaluate('string(//div[@data-searchable-select]/@data-max-results)'))->toBe($expected);
})->with([[0, '1'], [50, '50'], [1000, '200']]);

test('renders searchable students and classes in the initial and repeatable class assignment rows', function () {
    $this->actingAs(User::factory()->make(['role' => UserRole::Admin]));
    $this->withoutVite();
    View::share('sekolah', (object) ['logo_url' => '/logo.png', 'nama_sekolah' => 'Sekolah']);

    $view = $this->view('kelassiswa.index', [
        'pemetaans' => new LengthAwarePaginator([], 0, 10),
        'siswa' => collect([(object) ['id' => 7, 'nama' => 'Siti Aminah', 'nipd' => '00123']]),
        'kelas' => collect([(object) ['id' => 2, 'kelas' => 'X A']]),
        'tahun' => collect([(object) ['id' => 3, 'tahun' => '2026/2027', 'semester' => 'Ganjil']]),
    ]);

    $html = searchableSelectXpath((string) $view);

    foreach (['//div[@id="repeater-container"]', '//template[@id="row-template"]'] as $rowPath) {
        expect($html->query($rowPath.'//select[@data-select-native and @name="siswa_id[]" and @required and not(@multiple)]')->length)->toBe(1);
        expect($html->evaluate('string('.$rowPath.'//select[@data-select-native]/option[@value="7"])'))->toBe('Siti Aminah - 00123');
        expect($html->evaluate('string('.$rowPath.'//input[@role="combobox"]/@placeholder)'))->toBe('Cari nama atau NIPD siswa...');
        expect($html->query($rowPath.'//select[@data-select-native and @name="kelas_id[]" and @required and not(@multiple)]')->length)->toBe(1);
        expect($html->evaluate('string('.$rowPath.'//select[@name="kelas_id[]"]/option[@value="2"])'))->toBe('X A');
        expect($html->evaluate('string('.$rowPath.'//div[select[@name="kelas_id[]"]]//input[@role="combobox"]/@placeholder)'))->toBe('Cari kelas...');
    }

    expect($html->evaluate('string(//form[@id="InputSiswa"]/@action)'))->toBe(route('kelassiswa.store'));
});

test('renders searchable teachers and subjects with the correct forms and saved schedule selections', function () {
    $this->actingAs(User::factory()->make(['role' => UserRole::Admin]));
    $this->withoutVite();
    View::share('sekolah', (object) ['logo_url' => '/logo.png', 'nama_sekolah' => 'Sekolah']);
    $teacher = (object) ['id' => 7, 'name' => 'Siti <img src=x onerror=alert(1)>'];
    $subject = (object) ['id' => 5, 'mapel' => 'Bahasa & Sastra'];
    $class = (object) ['id' => 2, 'kelas' => 'X A'];
    $schedules = collect([11, 12])->map(fn (int $id): object => (object) [
        'id' => $id,
        'kelas_id' => 2,
        'mapel_id' => 5,
        'pegawai_id' => 7,
        'tahun_id' => 3,
        'kelas' => $class,
        'mapel' => $subject,
        'pegawai' => $teacher,
        'hari' => 'Senin',
        'jam' => 1,
        'mulai' => '07:00:00',
        'akhir' => '07:45:00',
        'ket' => 'aktif',
    ]);

    $view = $this->view('jadwal.index', [
        'perpage' => 10,
        'filter_tahun' => null,
        'jadwals' => new LengthAwarePaginator($schedules, 2, 10),
        'pegawai' => collect([$teacher]),
        'mapel' => collect([$subject]),
        'kelas' => collect([$class]),
        'hari' => ['Senin', 'Selasa'],
        'tahun' => collect([(object) ['id' => 3, 'tahun' => '2026/2027', 'semester' => 'Ganjil']]),
    ]);

    $html = searchableSelectXpath((string) $view);

    foreach (['mapel_id' => ['5', 'Bahasa & Sastra'], 'pegawai_id' => ['7', $teacher->name]] as $field => [$value, $label]) {
        $newField = '//template[@id="tplNewJadwalRow"]//select[@data-select-native and @name="'.$field.'[]"]';
        expect($html->evaluate('string('.$newField.'/@form)'))->toBe('bulkFormJadwal');
        expect($html->evaluate('string('.$newField.'/option[@value="'.$value.'"])'))->toBe($label);
        expect($html->query($newField.'/parent::div[@data-select-portal]')->length)->toBe(1);

        foreach ([11, 12] as $id) {
            $editField = '//tr[@id="edit-form-'.$id.'"]//select[@data-select-native and @name="'.$field.'"]';
            expect($html->evaluate('string('.$editField.'/@form)'))->toBe('form-edit-'.$id);
            expect($html->query($editField.'/option[@selected]')->length)->toBe(1);
            expect($html->evaluate('string('.$editField.'/option[@selected]/@value)'))->toBe($value);
        }
    }

    $ids = array_map(fn (DOMNode $node): string => $node->nodeValue, iterator_to_array($html->query('//div[@data-searchable-select]//@id')));
    expect(count($ids))->toBe(count(array_unique($ids)));
    expect($html->query('//*[@onerror]')->length)->toBe(0);
});
