import test from 'node:test';
import assert from 'node:assert/strict';
import {
    normalizeSearch,
    searchOptions,
    nextEnabledIndex,
    SearchableSelect,
    initializeSearchableSelects,
} from '../../resources/js/searchable-select.js';

const options = [
    { value: '0', label: 'Umum', searchText: 'umum' },
    {
        value: '1',
        label: 'Pemrograman Laravel',
        searchText: 'pemrograman laravel',
    },
    {
        value: '2',
        label: 'Pemrograman JavaScript',
        searchText: 'pemrograman javascript',
    },
    { value: '3', label: 'José & Rekan', searchText: 'jose & rekan' },
];

test('search ignores case accents and surrounding whitespace', () => {
    assert.equal(normalizeSearch('  JOSÉ  '), 'jose');
    assert.deepEqual(
        searchOptions(options, ' JOSÉ ').results.map((option) => option.value),
        ['3'],
    );
});

test('every search word must match regardless of word order', () => {
    assert.deepEqual(
        searchOptions(options, 'LARAVEL   pemrograman').results.map((option) => option.value),
        ['1'],
    );
    assert.deepEqual(searchOptions(options, 'laravel javascript').results, []);
});

test('an empty search preserves original ordering and zero values', () => {
    assert.deepEqual(
        searchOptions(options, ' ').results.map((option) => option.value),
        ['0', '1', '2', '3'],
    );
});

test('limits displayed matches and distinguishes a full list from truncated results', () => {
    const limited = searchOptions(options, 'pemrograman', 1);
    assert.deepEqual(
        limited.results.map((option) => option.value),
        ['1'],
    );
    assert.equal(limited.hasMore, true);
    assert.equal(searchOptions(options, 'pemrograman', 2).hasMore, false);
});

test('search reaches matching options beyond the initial render limit', () => {
    const manyOptions = Array.from({ length: 10000 }, (_, index) => ({
        value: String(index),
        searchText: 'pegawai ' + index,
    }));

    assert.equal(searchOptions(manyOptions, '').results.length, 50);
    assert.deepEqual(
        searchOptions(manyOptions, 'pegawai 9999').results.map((option) => option.value),
        ['9999'],
    );
});

test('hidden options are omitted while disabled options remain discoverable', () => {
    const result = searchOptions(
        [
            { value: '1', searchText: 'arsip', hidden: true },
            { value: '2', searchText: 'arsip', disabled: true },
        ],
        'arsip',
    );

    assert.deepEqual(
        result.results.map((option) => option.value),
        ['2'],
    );
});

test('unmatched text returns an empty result instead of creating a value', () => {
    assert.deepEqual(searchOptions(options, '<script>'), {
        results: [],
        hasMore: false,
    });
});

test('keyboard navigation skips disabled options in both directions', () => {
    const choices = [{ disabled: true }, {}, { disabled: true }, {}];
    assert.equal(nextEnabledIndex(choices, -1, 1), 1);
    assert.equal(nextEnabledIndex(choices, 1, 1), 3);
    assert.equal(nextEnabledIndex(choices, 3, -1), 1);
    assert.equal(nextEnabledIndex(choices, choices.length, -1), 3);
});

test('keyboard navigation stops at boundaries and handles empty or disabled lists', () => {
    assert.equal(nextEnabledIndex([{}, {}], 1, 1), 1);
    assert.equal(nextEnabledIndex([{}, {}], 0, -1), 0);
    assert.equal(nextEnabledIndex([], -1, 1), -1);
    assert.equal(nextEnabledIndex([{ disabled: true }], -1, 1), -1);
    assert.equal(nextEnabledIndex([{ disabled: true }], 1, -1), -1);
});

test('students can be found by name NIPD or both without losing leading zeros', () => {
    const students = [
        { value: '7', searchText: 'siti aminah - 00123' },
        { value: '8', searchText: 'budi santoso - 00199' },
    ];

    for (const query of ['SITI', '00123', 'aminah 00123']) {
        assert.deepEqual(
            searchOptions(students, query).results.map((student) => student.value),
            ['7'],
        );
    }
});

test('table popups fit the viewport and open above when there is insufficient room below', (context) => {
    const previousWindow = globalThis.window;
    globalThis.window = { innerWidth: 800, innerHeight: 600 };
    context.after(() => {
        globalThis.window = previousWindow;
    });
    const select = Object.create(SearchableSelect.prototype);
    select.portal = true;
    select.popup = { style: {} };
    select.list = { style: {} };
    select.trigger = { getBoundingClientRect: () => ({ left: 700, top: 100, bottom: 140, width: 200 }) };

    select.position();

    assert.equal(select.popup.style.position, 'fixed');
    assert.equal(select.popup.style.left, '592px');
    assert.equal(select.popup.style.width, '200px');
    assert.equal(select.popup.style.top, '144px');
    assert.equal(select.popup.style.bottom, 'auto');
    assert.equal(select.list.style.maxHeight, '240px');

    select.trigger.getBoundingClientRect = () => ({ left: 100, top: 500, bottom: 540, width: 200 });

    select.position();

    assert.equal(select.popup.style.top, 'auto');
    assert.equal(select.popup.style.bottom, '104px');
});

test('a detached popup remains interactive and returns to its row when closed', (context) => {
    const previousDocument = globalThis.document;
    const previousWindow = globalThis.window;
    const document = new EventTarget();
    document.querySelectorAll = () => [];
    document.body = {
        append: (element) => {
            element.parentElement = document.body;
        },
    };
    globalThis.document = document;
    globalThis.window = new EventTarget();
    context.after(() => {
        globalThis.document = previousDocument;
        globalThis.window = previousWindow;
    });
    const select = Object.create(SearchableSelect.prototype);
    const search = { value: 'lama', setAttribute() {}, removeAttribute() {}, focus() {} };
    select.portal = true;
    select.select = { matches: () => false };
    select.search = search;
    select.trigger = { setAttribute() {}, focus() {} };
    select.popup = { hidden: true, contains: (element) => element === search };
    select.root = {
        contains: () => false,
        append: (element) => {
            element.parentElement = select.root;
        },
    };
    select.sync = () => {};
    select.render = () => {};
    select.position = () => {};
    context.after(() => select.close());
    initializeSearchableSelects(document);

    select.open();

    assert.equal(select.popup.parentElement, document.body);
    assert.equal(select.popup.hidden, false);
    assert.equal(search.value, '');

    for (const type of ['focusin', 'pointerdown', 'scroll']) {
        const event = new Event(type);
        Object.defineProperty(event, 'target', { value: search });
        document.dispatchEvent(event);
        assert.equal(select.isOpen, true);
    }

    document.dispatchEvent(new Event('pointerdown'));

    assert.equal(select.isOpen, false);
    assert.equal(select.popup.hidden, true);
    assert.equal(select.popup.parentElement, select.root);

    select.open();
    document.dispatchEvent(new Event('scroll'));

    assert.equal(select.isOpen, false);
    assert.equal(select.popup.parentElement, select.root);
});
