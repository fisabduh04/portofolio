import test from 'node:test';
import assert from 'node:assert/strict';
import { runHeadTurnChallenge } from '../../resources/js/face-attendance-liveness.js';

const descriptor = () => Array(128).fill(0.1);
function trial(yaws, overrides = {}) {
    let time = 0;
    let frame = 0;
    const prompts = [];
    const options = {
        reference: descriptor(), active: () => true, notify: text => prompts.push(text),
        random: { getRandomValues: bytes => { bytes[0] = 1; return bytes; } },
        now: () => time, pause: async () => {},
        capture: async () => { time += 250; return { yaw: yaws[Math.min(frame++, yaws.length - 1)], descriptor: descriptor() }; },
        ...overrides,
    };
    return { options, prompts, advance: value => { time += value; } };
}

for (const direction of [-1, 1]) {
    test(`requires neutral, sustained turn ${direction}, and return before passing`, async () => {
        const run = trial([0, 0, 0.1 * direction, 0.22 * direction, 0.22 * direction, 0.1 * direction, 0, 0], {
            random: { getRandomValues: bytes => { bytes[0] = direction === 1 ? 1 : 0; return bytes; } },
        });

        const result = await runHeadTurnChallenge(run.options);

        assert.deepEqual(result.descriptor, descriptor());
        assert.equal(result.elapsed, 2000);
        assert.match(run.prompts.at(-1), /3\/3/);
    });
}

for (const [name, yaws] of [['static face', [0]], ['wrong direction', [0, 0, -0.22]], ['no return', [0, 0, 0.22]]]) {
    test(`${name} never passes the challenge`, async () => {
        const run = trial(yaws);
        await assert.rejects(runHeadTurnChallenge(run.options), /waktu.*habis|Waktu.*habis/);
    });
}

test('front camera directions match the mirrored preview', async () => {
    const run = trial([0, 0, 0.22, 0.22, 0, 0], { mirrored: true });
    await runHeadTurnChallenge(run.options);
    assert.ok(run.prompts.some(text => text.includes('← kiri')));
});

test('rejects switching identity during motion', async () => {
    const run = trial([0]);
    run.options.capture = async () => ({ yaw: 0, descriptor: Array(128).fill(0.9) });
    await assert.rejects(runHeadTurnChallenge(run.options), /Wajah berubah/);
});

test('rejects invalid landmarks and a sudden frame jump', async () => {
    await assert.rejects(runHeadTurnChallenge(trial([NaN]).options), /belum terbaca/);
    await assert.rejects(runHeadTurnChallenge(trial([0, 0, 0.6]).options), /terlalu mendadak/);
});

test('face loss during a challenge fails instead of releasing the queue gate', async () => {
    const run = trial([0], { capture: async () => { throw Object.assign(new Error('No face'), { code: 'no_face' }); } });
    await assert.rejects(runHeadTurnChallenge(run.options), { code: 'liveness_failed' });
});

test('cancellation while capturing cannot complete the challenge', async () => {
    let active = true;
    const run = trial([0], { active: () => active, capture: async () => {
        active = false;
        return { yaw: 0, descriptor: descriptor() };
    } });
    await assert.rejects(runHeadTurnChallenge(run.options), { code: 'liveness_cancelled' });
});

test('a processing gap invalidates the challenge', async () => {
    const run = trial([0]);
    run.options.capture = async () => {
        run.advance(4500);
        return { yaw: 0, descriptor: descriptor() };
    };
    await assert.rejects(runHeadTurnChallenge(run.options), /terputus/);
});
