import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

test('rebuilds every failed bulk row with its own values before enhancing dropdowns', () => {
    const fields = ['id', 'kelas_id', 'hari', 'mapel_id', 'pegawai_id', 'jam', 'mulai', 'akhir', 'ket'];
    const old = {
        _jadwal_bulk: true, id: ['', ''], kelas_id: ['2', '3'], hari: ['Senin', 'Selasa'],
        mapel_id: ['5', '6'], pegawai_id: ['7', '8'], jam: ['1', '2'],
        mulai: ['07:00', '08:00'], akhir: ['08:00', '09:00'], ket: ['Catatan pertama', ''],
    };
    const rows = [];
    const controls = { classList: { remove() {}, add() {} }, addEventListener() {} };
    const document = {
        addEventListener() {},
        querySelectorAll: () => [],
        getElementById(id) {
            if (id === 'jadwalTable') return { insertBefore: (clone) => rows.unshift(clone.row) };
            if (id !== 'tplNewJadwalRow') return controls;
            return { content: { cloneNode() {
                const inputs = fields.map(field => ({ name: field + '[]', value: 'default' }));
                const row = {
                    inputs,
                    querySelectorAll: selector => selector === '[name]' ? inputs : [],
                    dispatchEvent() {
                        assert.ok(inputs.every(input => input.value !== 'default'));
                    },
                };
                return { row, querySelector: () => row };
            } } };
        },
    };
    const blade = readFileSync(new URL('../../resources/views/jadwal/index.blade.php', import.meta.url), 'utf8');
    const script = blade.match(/<script>([\s\S]*?)<\/script>/)[1]
        .replace(/{{ Illuminate\\Support\\Js::from\(session\(\)->getOldInput\(\)\) }}/, JSON.stringify(old));
    runInNewContext(script, { document, CustomEvent: class {}, setTimeout, clearTimeout });
    assert.equal(rows.length, 2);
    rows.forEach((row, index) => {
        fields.forEach((field, column) => assert.equal(row.inputs[column].value, old[field][index]));
    });
});
