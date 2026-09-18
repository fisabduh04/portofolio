import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

function openPage(oldInput) {
    const rows = [];
    const elements = new Map();
    const panelClasses = new Set(['hidden']);
    const control = () => ({ style: {}, value: '', name: '', classList: { add() {}, remove() {} }, dataset: {}, getAttribute: () => 'csrf-token', addEventListener() {}, dispatchEvent() {} });
    const year = control();
    year.name = 'tahun_id';
    const panel = { classList: { add: value => panelClasses.add(value), remove: value => panelClasses.delete(value) } };
    let method = null;
    const form = {
        querySelector: () => method,
        appendChild(input) { method = input; input.remove = () => { method = null; }; },
        reset() { year.value = ''; },
    };
    const container = {
        appendChild(clone) { rows.push(clone.row); },
        replaceChildren() { rows.length = 0; },
        querySelector: () => rows[0],
        querySelectorAll: selector => selector === '.repeater-row' ? rows : [],
    };
    const template = { content: { cloneNode() {
        const fields = Object.fromEntries(['siswa_id', 'kelas_id', 'ket'].map(name => [name, { ...control(), name: name + '[]' }]));
        const row = {
            fields,
            querySelectorAll: selector => selector === 'select[name]' ? Object.values(fields) : [],
            querySelector: selector => fields[selector.match(/name="(.*?)\[\]"/)?.[1]] ?? control(),
            dispatchEvent() {},
        };
        return { row, querySelector: () => row };
    } } };
    const document = {
        getElementById(id) {
            if (!elements.has(id)) elements.set(id, control());
            return { InputSiswa: form, InputSiswaPanel: panel, tahun: year,
                'repeater-container': container, 'row-template': template }[id] ?? elements.get(id);
        },
        querySelector: () => control(), querySelectorAll: () => [], addEventListener() {}, createElement: control,
    };
    const blade = readFileSync(new URL('../../resources/views/kelassiswa/index.blade.php', import.meta.url), 'utf8');
    const script = blade.match(/<script>([\s\S]*?)<\/script>/)[1]
        .replace(/{{ Illuminate\\Support\\Js::from\(session\(\)->getOldInput\(\)\) }}/, JSON.stringify(oldInput))
        .replace(/{{ Illuminate\\Support\\Js::from\(\$initialAssignment\) }}/, 'null')
        .replace(/{{ route\('kelassiswa.update', '__ID__'\) }}/, '/kelassiswa/__ID__')
        .replace(/{{[\s\S]*?}}/g, '/kelassiswa');
    const context = { document, CustomEvent: class {}, Event: class {}, window: {}, alert() {} };
    runInNewContext(script, context);
    return { rows, year, form, panelClasses, context, elements, method: () => method };
}

test('failed bulk rows retain their distinct values when the panel is closed and reopened', () => {
    const page = openPage({ _kelassiswa_form: '1', siswa_id: ['7', ''], kelas_id: ['2', '3'], ket: ['aktif', 'do'] });
    assert.equal(page.panelClasses.has('hidden'), false);
    assert.equal(page.rows.length, 2);
    page.context.tutup();
    page.context.tambah();
    assert.equal(page.rows.length, 2);
    assert.equal(page.rows[0].fields.siswa_id.value, '7');
    assert.equal(page.rows[1].fields.siswa_id.value, '');
    assert.equal(page.rows[1].fields.kelas_id.value, '3');
    assert.equal(page.rows[1].fields.ket.value, 'do');
});

test('a failed edit reopens with the correct method and preserved values', () => {
    const page = openPage({ _kelassiswa_form: '1', _kelassiswa_edit_id: 12, siswa: '7', kelas: '', tahun: '5', ket: 'do' });
    assert.equal(page.form.action, '/kelassiswa/12');
    assert.equal(page.method().value, 'PUT');
    assert.equal(page.rows[0].fields.siswa_id.name, 'siswa_id');
    assert.equal(page.rows[0].fields.kelas_id.value, '');
    assert.equal(page.rows[0].fields.ket.value, 'do');
    assert.equal(page.year.value, '5');
    page.context.tutup();
    assert.equal(page.year.name, 'tahun_id');
});

test('switching from edit to add restores bulk field names and removes the PUT method', () => {
    const page = openPage({ _kelassiswa_form: '1', _kelassiswa_edit_id: 12, siswa_id: '7', kelas_id: '2', tahun_id: '5', ket: 'aktif' });
    page.context.tambah();
    assert.equal(page.method(), null);
    assert.equal(page.form.action, '/kelassiswa');
    assert.equal(page.rows.length, 1);
    assert.equal(page.rows[0].fields.siswa_id.name, 'siswa_id[]');
    assert.equal(page.rows[0].fields.siswa_id.value, '');
});

test('status updates request JSON and show success only after a saved response', async () => {
    const page = openPage({});
    const select = { value: 'do', dataset: { savedStatus: 'aktif' }, classList: { add() {} } };
    page.context.fetch = async (url, request) => {
        assert.equal(url, '/kelassiswa/12');
        assert.equal(request.method, 'PUT');
        assert.equal(request.headers.Accept, 'application/json');
        assert.deepEqual(JSON.parse(request.body), { ket: 'do' });
        return { ok: true, json: async () => ({ data: { ket: 'do' } }) };
    };
    await page.context.updateStatus(12, select);
    assert.equal(select.dataset.savedStatus, 'do');
    assert.equal(select.disabled, false);
    assert.equal(page.elements.get('status-msg-12').textContent, 'Tersimpan!');
});

test('failed status updates restore the saved selection and show the validation message', async () => {
    const page = openPage({});
    const select = { value: 'invalid', dataset: { savedStatus: 'aktif' }, classList: { add() {} } };
    page.context.fetch = async () => ({ ok: false, json: async () => ({ errors: { ket: ['Status siswa tidak valid.'] } }) });
    await page.context.updateStatus(12, select);
    assert.equal(select.value, 'aktif');
    assert.equal(select.disabled, false);
    assert.equal(page.elements.get('status-msg-12').textContent, 'Status siswa tidak valid.');
});

test('a successful HTTP response without saved data does not report success', async () => {
    const page = openPage({});
    const select = { value: 'do', dataset: { savedStatus: 'aktif' }, classList: { add() {} } };
    page.context.fetch = async () => ({ ok: true, json: async () => ({}) });
    await page.context.updateStatus(12, select);
    assert.equal(select.value, 'aktif');
    assert.equal(page.elements.get('status-msg-12').textContent, 'Perubahan status gagal disimpan.');
});
