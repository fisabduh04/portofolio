import assert from 'node:assert/strict';
import { test } from 'node:test';
import { metricsCsv } from '../../resources/js/face-prototype-export.js';
import { trialScenario, trialError, failureOutcome, trialWarning, summarizeTrials } from '../../resources/js/face-prototype-trials.js';
import { detectorInputSize, rendererCategory, graphicsDiagnostic, initializeFaceBackend } from '../../resources/js/face-prototype-performance.js';
import { motionSignals, randomMotionPlan, createMotionChallenge, runMotionChallenge, motionFailureInstruction } from '../../resources/js/face-prototype-challenge.js';

const motionSample = (values = {}) => ({ leftEar: 0.3, rightEar: 0.3, yaw: 0, descriptor: Array(128).fill(0), ...values });
const closedEyes = () => motionSample({ leftEar: 0.1, rightEar: 0.1 });

test('motion instructions retain the requested step and explain restarting after capture failure', async () => {
    const samples = [motionSample(), motionSample()];
    let at = 0;
    await assert.rejects(runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
        now: () => at, active: () => true, notify: () => {}, pause: async () => {},
        capture: async () => {
            at += 100;
            if (!samples.length) throw trialError('NO_FACE', 'Wajah belum terdeteksi.');
            const sample = samples.shift();
            return { started: at, motion: sample, descriptor: sample.descriptor };
        },
    }), (error) => {
        const message = motionFailureInstruction(error);
        assert.match(message, /Petunjuk terakhir: Langkah 1\/2: tutup kedua mata/);
        assert.match(message, /Uji dihentikan: Wajah belum terdeteksi/);
        assert.match(message, /untuk memulai ulang/);
        return true;
    });
    assert.doesNotMatch(motionFailureInstruction(new Error('Jaringan terputus')), /undefined|Petunjuk terakhir/);
});

test('motion plans vary both action order and image direction', () => {
    for (const a of [0, 1]) for (const b of [0, 1]) {
        const plan = randomMotionPlan({ getRandomValues: (bytes) => { bytes.set([a, b]); return bytes; } });
        assert.deepEqual(plan.actions, a ? ['turn', 'blink'] : ['blink', 'turn']);
        assert.equal(plan.direction, b ? 1 : -1);
    }
});

test('motion signals use eye openness and nose projection independent of image scale and translation', () => {
    const eye = [{ x: 0, y: 0 }, { x: 1, y: -0.6 }, { x: 3, y: -0.6 },
        { x: 4, y: 0 }, { x: 3, y: 0.6 }, { x: 1, y: 0.6 }];
    for (const scale of [1, 10]) {
        const transform = (p) => ({ x: p.x * scale + 70, y: p.y * scale + 30 });
        const signals = motionSignals({ getLeftEye: () => eye.map(transform),
            getRightEye: () => eye.map((p) => transform({ x: p.x + 10, y: p.y })),
            getNose: () => [null, null, null, transform({ x: 9, y: 4 })] });
        assert.ok(Math.abs(signals.leftEar - 0.3) < 0.00001);
        assert.ok(Math.abs(signals.rightEar - 0.3) < 0.00001);
        assert.ok(Math.abs(signals.yaw - 0.2) < 0.00001);
    }
});

test('motion check requires both ordered actions and a return to open eyes facing forward', () => {
    for (const actions of [['blink', 'turn'], ['turn', 'blink']]) for (const direction of [-1, 1]) {
        const challenge = createMotionChallenge({ actions, direction }, 0);
        let at = 0;
        const observe = (sample) => challenge.observe(sample, at += 100);
        assert.equal(observe(motionSample()).done, false);
        assert.equal(observe(motionSample()).done, false);
        for (const [index, action] of actions.entries()) {
            if (action === 'blink') {
                assert.equal(observe(motionSample({ leftEar: 0.1 })).done, false);
                assert.equal(observe(closedEyes()).done, false);
            } else {
                assert.equal(observe(motionSample({ yaw: -direction * 0.25 })).done, false);
                assert.equal(observe(motionSample({ yaw: direction * 0.25 })).done, false);
                assert.equal(observe(motionSample({ yaw: direction * 0.25 })).done, false);
            }
            assert.equal(observe(motionSample()).done, false);
            assert.equal(observe(motionSample()).done, index === 1);
        }
    }
});

test('static observations time out and changed faces or gaps fail closed', () => {
    const plan = { actions: ['blink', 'turn'], direction: 1 };
    const still = createMotionChallenge(plan, 0, 1000);
    for (let at = 100; at <= 1000; at += 100) assert.equal(still.observe(motionSample(), at).done, false);
    assert.equal(still.observe(motionSample(), 1100).failed, 'MOTION_TIMEOUT');
    assert.equal(still.observe(closedEyes(), 1200).failed, 'MOTION_TIMEOUT');

    const changed = createMotionChallenge(plan, 0);
    changed.observe(motionSample(), 100);
    changed.observe(motionSample(), 200);
    assert.equal(changed.observe(motionSample({ descriptor: Array(128).fill(1) }), 300).failed, 'MOTION_FACE_CHANGED');
    assert.equal(createMotionChallenge(plan, 0).observe(motionSample(), 2600).failed, 'MOTION_INTERRUPTED');
    assert.equal(createMotionChallenge(plan, 0).observe(motionSample({ yaw: NaN }), 100).failed, 'MOTION_INVALID');
    assert.equal(createMotionChallenge(plan, 0).observe(motionSample({ descriptor: [] }), 100).failed, 'MOTION_INVALID');
    assert.throws(() => createMotionChallenge({ actions: ['blink', 'blink'], direction: 1 }, 0), /Invalid/);
});

test('motion runner returns only after the full sequence and exports no biometric measurements', async () => {
    const sequence = [motionSample(), motionSample(), closedEyes(), motionSample(), motionSample(),
        motionSample({ yaw: 0.25 }), motionSample({ yaw: 0.25 }), motionSample(), motionSample()];
    let at = 0;
    let count = 0;
    const result = await runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
        now: () => at, active: () => true, notify: () => {}, pause: async () => {},
        capture: async () => {
            at += 100;
            const sample = sequence[count++];
            return { started: at, descriptor: sample.descriptor, motion: sample };
        },
    });
    assert.equal(count, 9);
    assert.equal(result.started, 100);
    assert.equal(result.motion_ms, 900);
    assert.equal(result.motion_frames, 9);
    assert.equal(result.motion_plan, 'blink_turn_right');
    assert.equal(result.preparation_ms, 100);
    assert.equal(result.preparation_no_face_frames, 0);
    const csv = metricsCsv([{ ...result, motion_status: 'passed_motion_check' }]);
    assert.match(csv, /passed_motion_check,900,9,blink_turn_right/);
    assert.doesNotMatch(csv, /descriptor|leftEar|rightEar|yaw/);
});

test('motion runner aborts on lost faces and cancellation without completing a match', async () => {
    const base = { plan: { actions: ['blink', 'turn'], direction: 1 }, now: () => 100,
        active: () => true, notify: () => {}, pause: async () => {} };
    let captures = 0;
    await assert.rejects(runMotionChallenge({ ...base,
        capture: async () => {
            if (captures++ === 0) return { started: 100, motion: motionSample(), descriptor: motionSample().descriptor };
            throw trialError('NO_FACE', 'Wajah hilang');
        },
    }), (error) => error.code === 'NO_FACE' && error.motion_frames === 1 && error.preparation_no_face_frames === 0);
    assert.equal(captures, 2);
    await assert.rejects(runMotionChallenge({ ...base, active: () => false,
        capture: async () => assert.fail('Cancelled sessions must not capture'),
    }), (error) => error.code === 'MOTION_CANCELLED');
    let active = true;
    await assert.rejects(runMotionChallenge({ ...base, active: () => active,
        capture: async () => { active = false; return {}; },
    }), (error) => error.code === 'MOTION_CANCELLED');
});

test('preparation retries missing faces and completes the ordered challenge after acquisition', async () => {
    const sequence = [motionSample(), motionSample(), closedEyes(), motionSample(), motionSample(),
        motionSample({ yaw: 0.25 }), motionSample({ yaw: 0.25 }), motionSample(), motionSample()];
    const prompts = [];
    let at = 0;
    let captures = 0;
    const result = await runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
        now: () => at, active: () => true, notify: (message) => prompts.push(message), pause: async () => {},
        capture: async () => {
            if (++captures <= 4) {
                at += 1000;
                throw trialError('NO_FACE', 'Wajah belum terdeteksi.');
            }
            at += 100;
            const sample = sequence.shift();
            return { started: at - 100, motion: sample, descriptor: sample.descriptor };
        },
    });

    assert.equal(captures, 13);
    assert.equal(result.started, 4000);
    assert.equal(result.motion_frames, 9);
    assert.equal(result.motion_ms, 4900);
    assert.equal(result.preparation_ms, 4100);
    assert.equal(result.preparation_no_face_frames, 4);
    assert.match(prompts[0], /Persiapan: mencari wajah \(5 detik tersisa\)/);
    assert.match(prompts[4], /Persiapan: mencari wajah \(1 detik tersisa\)/);
    assert.match(prompts[5], /hadapkan wajah lurus/);
    assert.match(prompts[6], /Langkah 1\/2: tutup kedua mata/);
    const [header, row] = metricsCsv([result]).split('\r\n').map((line) => line.split(','));
    assert.equal(row[header.indexOf('preparation_ms')], '4100');
    assert.equal(row[header.indexOf('preparation_no_face_frames')], '4');
});

test('empty preparation ends after five seconds with a distinct reason and exportable measurements', async () => {
    let at = 0;
    let captures = 0;
    await assert.rejects(runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
        now: () => at, active: () => true, notify: () => {}, pause: async () => {},
        capture: async () => {
            captures++;
            at += 1000;
            throw trialError('NO_FACE', 'Wajah belum terdeteksi.');
        },
    }), (error) => {
        assert.equal(error.code, 'MOTION_PREPARATION_TIMEOUT');
        assert.equal(error.motion_ms, 5000);
        assert.equal(error.motion_frames, 0);
        assert.equal(error.preparation_ms, 5000);
        assert.equal(error.preparation_no_face_frames, 5);
        assert.match(motionFailureInstruction(error), /Petunjuk terakhir: Persiapan: mencari wajah/);
        assert.match(motionFailureInstruction(error), /Wajah belum ditemukan dalam 5 detik/);
        const outcome = failureOutcome(error, 'capture');
        assert.deepEqual(outcome, { status: 'motion_rejected', reason_code: 'MOTION_PREPARATION_TIMEOUT' });
        const [header, row] = metricsCsv([{ ...error, ...outcome }]).split('\r\n').map((line) => line.split(','));
        assert.equal(row[header.indexOf('preparation_ms')], '5000');
        assert.equal(row[header.indexOf('preparation_no_face_frames')], '5');
        assert.equal(row[header.indexOf('total_ms')], '');
        return true;
    });
    assert.equal(captures, 5);
});

test('a face acquired at or after the preparation deadline cannot start the challenge', async () => {
    for (const elapsed of [5000, 5100]) {
        let at = 0;
        await assert.rejects(runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
            now: () => at, active: () => true, notify: () => {},
            pause: async () => assert.fail('Expired preparation must not continue'),
            capture: async () => {
                at = elapsed;
                return { started: 0, motion: motionSample(), descriptor: motionSample().descriptor };
            },
        }), (error) => error.code === 'MOTION_PREPARATION_TIMEOUT' && error.motion_frames === 0);
    }
});

test('preparation checks its deadline before capturing another frame after a pause', async () => {
    let at = 0;
    let captures = 0;
    await assert.rejects(runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
        now: () => at, active: () => true, notify: () => {}, pause: async () => { at = 5000; },
        capture: async () => {
            captures++;
            assert.equal(captures, 1);
            throw trialError('NO_FACE', 'Wajah belum terdeteksi.');
        },
    }), (error) => error.code === 'MOTION_PREPARATION_TIMEOUT' && error.preparation_no_face_frames === 1);
});

test('preparation preserves rejections for multiple faces, quality, passage gates and processing errors', async () => {
    for (const code of ['MULTIPLE_FACES', 'FACE_TOO_SMALL', 'FACE_NEAR_EDGE', 'POSITION_INVALID', 'IMAGE_QUALITY',
        'VIDEO_NOT_READY', 'PASSAGE_WAIT', 'PASSAGE_READY', undefined]) {
        const failure = trialError(code, 'Gambar ditolak');
        await assert.rejects(runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
            now: () => 0, active: () => true, notify: () => {},
            pause: async () => assert.fail('Only NO_FACE may be retried during preparation'),
            capture: async () => { throw failure; },
        }), (error) => error === failure && error.motion_frames === 0 && error.preparation_no_face_frames === 0);
    }
});

test('cancelling preparation during a failed capture or retry pause stops without another capture', async () => {
    for (const cancelDuring of ['capture', 'pause']) {
        let active = true;
        let captures = 0;
        await assert.rejects(runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
            now: () => 100, active: () => active, notify: () => {}, pause: async () => { active = false; },
            capture: async () => {
                captures++;
                if (cancelDuring === 'capture') active = false;
                throw trialError('NO_FACE', 'Wajah belum terdeteksi.');
            },
        }), (error) => error.code === 'MOTION_CANCELLED' && error.motion_frames === 0);
        assert.equal(captures, 1);
    }
});

test('face consistency starts with the first acquired face before neutral pose preparation completes', async () => {
    const sequence = [motionSample({ yaw: 0.3 }), motionSample({ descriptor: Array(128).fill(1) })];
    let at = 0;
    await assert.rejects(runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
        now: () => at, active: () => true, notify: () => {}, pause: async () => {},
        capture: async () => {
            at += 100;
            const sample = sequence.shift();
            return { started: at, motion: sample, descriptor: sample.descriptor };
        },
    }), (error) => error.code === 'MOTION_FACE_CHANGED' && error.motion_frames === 2);
});

test('the twenty five second challenge limit starts after face acquisition and still rejects a static face', async () => {
    let at = 0;
    let captures = 0;
    await assert.rejects(runMotionChallenge({ plan: { actions: ['blink', 'turn'], direction: 1 },
        now: () => at, active: () => true, notify: () => {}, pause: async () => {},
        capture: async () => {
            if (++captures <= 4) {
                at += 1000;
                throw trialError('NO_FACE', 'Wajah belum terdeteksi.');
            }
            at += captures === 5 ? 100 : 2000;
            return { started: at, motion: motionSample(), descriptor: motionSample().descriptor };
        },
    }), (error) => {
        assert.equal(error.code, 'MOTION_TIMEOUT');
        assert.equal(error.preparation_ms, 4100);
        assert.equal(error.motion_ms, 30100);
        assert.equal(error.motion_frames, 14);
        return true;
    });
});

test('motion failures and latency summaries remain separate from ordinary scans', () => {
    assert.deepEqual(failureOutcome(trialError('MOTION_TIMEOUT', 'timeout'), 'capture'), {
        status: 'motion_rejected', reason_code: 'MOTION_TIMEOUT',
    });
    const samples = [
        { status: 'candidate', detector_input: 320, scenario: 'photo', total_ms: 200, motion_status: 'not_run' },
        { status: 'candidate', detector_input: 320, scenario: 'photo', total_ms: 5000, motion_status: 'passed_motion_check' },
        { status: 'motion_rejected', detector_input: 320, scenario: 'photo', total_ms: null, motion_status: 'incomplete' },
    ];
    assert.equal(summarizeTrials(samples, 320, 'photo').p50, 200);
    const summary = summarizeTrials(samples, 320, 'photo', true);
    assert.equal(summary.attempts, 2);
    assert.equal(summary.p50, 5000);
    assert.equal(summary.motionRejected, 1);
});

test('trial scenarios accept declared conditions and reject inherited or unknown keys', () => {
    for (const scenario of ['frontal', 'turned', 'lighting', 'photo', 'replay', 'empty']) {
        assert.equal(trialScenario(scenario), scenario);
    }
    for (const scenario of ['__proto__', 'constructor', undefined, 'unexpected']) {
        assert.equal(trialScenario(scenario), 'frontal');
    }
});

test('trial failures distinguish capture rejection from processing errors and exclude gate checks', () => {
    for (const code of ['PASSAGE_WAIT', 'PASSAGE_READY']) {
        assert.equal(failureOutcome(trialError(code, 'waiting'), 'capture'), null);
    }
    for (const code of ['NO_FACE', 'MULTIPLE_FACES', 'POSITION', 'POSITION_INVALID', 'FACE_TOO_SMALL', 'FACE_NEAR_EDGE', 'IMAGE_QUALITY', 'VIDEO_NOT_READY']) {
        assert.deepEqual(failureOutcome(trialError(code, 'rejected'), 'capture'), { status: 'capture_rejected', reason_code: code });
    }
    assert.deepEqual(failureOutcome(new Error('private details'), 'capture'), { status: 'error', reason_code: 'PROCESSING_ERROR' });
    assert.deepEqual(failureOutcome(new TypeError('private details'), 'server'), { status: 'error', reason_code: 'SERVER_OR_NETWORK' });
});

test('trial summary counts failed attempts without treating missing durations as fast matches', () => {
    const sample = (status, total_ms, scenario = 'frontal', detector_input = 320) => ({ status, total_ms, scenario, detector_input });
    const samples = [sample('candidate', 100), sample('unknown', 200), sample('ambiguous', 300),
        sample('capture_rejected', null), sample('error', null), sample('candidate', 1, 'photo'), sample('candidate', 2, 'frontal', 224)];

    assert.deepEqual(summarizeTrials(samples, 320, 'frontal'), {
        attempts: 5, completed: 3, candidates: 1, rejected: 1, motionRejected: 0, errors: 1, p50: 200, p95: 300,
    });
    assert.equal(summarizeTrials([sample('error', null)], 320, 'frontal').p50, null);
    assert.equal(summarizeTrials([], 320, 'frontal').p95, null);
    assert.equal(summarizeTrials([sample('candidate', 0)], 320, 'frontal').p50, 0);
});

test('photo and replay results never imply proof of liveness protection', () => {
    for (const scenario of ['photo', 'replay']) {
        assert.match(trialWarning(scenario, 'candidate'), /menunjukkan celah/);
        assert.match(trialWarning(scenario, 'unknown'), /belum membuktikan/);
        assert.match(trialWarning(scenario, 'capture_rejected'), /belum membuktikan/);
        assert.match(trialWarning(scenario, 'error'), /belum dapat dinilai/);
    }
    assert.equal(trialWarning('frontal', 'candidate'), '');
});

test('CSV retains rejected trial conditions while leaving unmeasured timing empty', () => {
    const csv = metricsCsv([{ status: 'capture_rejected', scenario: 'empty', reason_code: 'NO_FACE', attempt_ms: 50, total_ms: null }]);
    const [header, row] = csv.split('\r\n').map((line) => line.split(','));

    assert.equal(row[header.indexOf('total_ms')], '');
    assert.equal(row[header.indexOf('scenario')], 'empty');
    assert.equal(row[header.indexOf('reason_code')], 'NO_FACE');
    assert.equal(row[header.indexOf('attempt_ms')], '50');
});

test('backend initialization retains working WebGL without switching to CPU', async () => {
    const selected = [];
    const backend = await initializeFaceBackend({ setBackend: async (name) => { selected.push(name); return true; },
        ready: async () => {}, getBackend: () => 'webgl' });
    assert.equal(backend, 'webgl');
    assert.deepEqual(selected, ['webgl']);
});

test('unavailable, failed or inactive WebGL falls back to a verified CPU backend', async () => {
    for (const failure of ['false', 'throw', 'inactive', 'ready']) {
        let active;
        const backend = await initializeFaceBackend({
            setBackend: async (name) => {
                active = name;
                if (name === 'webgl' && failure === 'throw') throw new Error('WebGL unavailable');
                return !(name === 'webgl' && failure === 'false');
            },
            ready: async () => { if (active === 'webgl' && failure === 'ready') throw new Error('Context failed'); },
            getBackend: () => active === 'webgl' && failure === 'inactive' ? 'cpu' : active,
        });
        assert.equal(backend, 'cpu');
    }
});

test('backend initialization reports failure if neither backend can operate', async () => {
    await assert.rejects(initializeFaceBackend({ setBackend: async () => false }), /WebGL maupun CPU/);
    await assert.rejects(initializeFaceBackend({ setBackend: async () => true, ready: async () => {}, getBackend: () => 'unknown' }), /WebGL maupun CPU/);
});

test('experimental detection is opt in and unknown profiles retain the standard resolution', () => {
    assert.equal(detectorInputSize('compact'), 224);
    assert.equal(detectorInputSize('detailed'), 416);
    assert.equal(detectorInputSize('standard'), 320);
    assert.equal(detectorInputSize('unexpected'), 320);
});

test('graphics hints identify software rendering without claiming hardware acceleration', () => {
    assert.equal(rendererCategory('ANGLE SwiftShader'), 'software');
    assert.equal(rendererCategory('Intel Graphics'), 'unconfirmed');
    assert.equal(rendererCategory(''), 'unknown');
    assert.equal(graphicsDiagnostic({ createElement: () => ({ getContext: () => null }) }).category, 'unknown');
    assert.equal(graphicsDiagnostic({ createElement: () => { throw new Error('blocked'); } }).category, 'unknown');
    let released = false;
    const gl = { getParameter: () => 'SwiftShader', getExtension: (name) => name === 'WEBGL_debug_renderer_info'
        ? { UNMASKED_RENDERER_WEBGL: 1 } : { loseContext: () => { released = true; } } };
    assert.equal(graphicsDiagnostic({ createElement: () => ({ getContext: () => gl }) }).category, 'software');
    assert.equal(released, true);
});

test('CSV preserves experiment settings so different detector sizes can be compared', () => {
    const csv = metricsCsv([{ sample: 1, detector_input: 224, backend: 'webgl', graphics_hint: 'unknown', frame_width: 640, frame_height: 480, total_ms: 1234 }]);
    assert.match(csv, /detector_input,backend,graphics_hint,frame_width,frame_height/);
    assert.match(csv, /224,webgl,unknown,640,480,1234/);
});

test('CSV keeps separate server stages and cache state for profiling', () => {
    const csv = metricsCsv([{ bootstrap_ms: 10, guard_ms: 2, dispatch_validation_ms: 3, opcode_cache: 1 }]);
    assert.match(csv, /bootstrap_ms,guard_ms,dispatch_validation_ms,opcode_cache/);
    assert.ok(csv.includes('10,2,3,1'));
});
import { facePositionAssessment, facePositionIssue, faceImageQuality, createPassageGate } from '../../resources/js/face-prototype-quality.js';

test('position rejects invalid geometry and distinguishes small faces from image edges in CSV', () => {
    const cases = [
        [{ x: 200, y: 100, width: 99, height: 120 }, 'FACE_TOO_SMALL', /Mendekatlah/],
        [{ x: 200, y: 100, width: 120, height: 99 }, 'FACE_TOO_SMALL', /Mendekatlah/],
        [{ x: 7, y: 100, width: 120, height: 120 }, 'FACE_NEAR_EDGE', /tengah kamera/],
        [{ x: 200, y: 7, width: 120, height: 120 }, 'FACE_NEAR_EDGE', /tengah kamera/],
        [{ x: 513, y: 100, width: 120, height: 120 }, 'FACE_NEAR_EDGE', /tengah kamera/],
        [{ x: 200, y: 353, width: 120, height: 120 }, 'FACE_NEAR_EDGE', /tengah kamera/],
        [{ x: NaN, y: 100, width: 120, height: 120 }, 'POSITION_INVALID', /tidak valid/],
        [{ x: 200, y: 100, width: 0, height: 120 }, 'POSITION_INVALID', /tidak valid/],
    ];
    for (const [box, code, message] of cases) {
        const issue = facePositionAssessment(box, 640, 480);
        const outcome = failureOutcome(trialError(issue.code, issue.message), 'capture');
        const [header, row] = metricsCsv([outcome]).split('\r\n').map((line) => line.split(','));

        assert.equal(issue.code, code);
        assert.match(issue.message, message);
        assert.equal(row[header.indexOf('status')], 'capture_rejected');
        assert.equal(row[header.indexOf('reason_code')], code);
    }
    assert.equal(facePositionAssessment({ x: 8, y: 8, width: 100, height: 100 }, 640, 480), null);
    assert.equal(facePositionAssessment({ x: 532, y: 372, width: 100, height: 100 }, 640, 480), null);
    assert.equal(facePositionAssessment({ x: 8, y: 8, width: 100, height: 100 }, 0, 480).code, 'POSITION_INVALID');
});

test('quality rejects small or clipped faces while accepting a central face', () => {
    assert.match(facePositionIssue({ x: 100, y: 100, width: 80, height: 90 }, 640, 480), /terlalu kecil/);
    assert.match(facePositionIssue({ x: 0, y: 100, width: 120, height: 120 }, 640, 480), /tepi/);
    assert.match(facePositionIssue({ x: 530, y: 100, width: 120, height: 120 }, 640, 480), /tepi/);
    assert.equal(facePositionIssue({ x: 200, y: 100, width: 160, height: 160 }, 640, 480), '');
});

test('quality distinguishes darkness, overexposure and low detail in the face crop', () => {
    const crop = (pixel) => ({ width: 64, height: 64,
        data: Uint8ClampedArray.from({ length: 64 * 64 * 4 }, (_, i) => i % 4 === 3 ? 255 : pixel(Math.floor(i / 4))) });
    assert.match(faceImageQuality(crop(() => 20)).issue, /gelap/);
    assert.match(faceImageQuality(crop(() => 240)).issue, /terang/);
    assert.match(faceImageQuality(crop(() => 120)).issue, /tajam/);
    assert.equal(faceImageQuality(crop((i) => (i + Math.floor(i / 64)) % 2 ? 100 : 150)).issue, '');
    assert.throws(() => faceImageQuality({ width: 0, height: 0, data: [] }), /tidak valid/);
});

test('accepted participant stays blocked until two consecutive empty observations', () => {
    const gate = createPassageGate();
    assert.equal(gate.observe(1), false);
    gate.accept();
    assert.equal(gate.locked, true);
    assert.equal(gate.observe(1), true);
    assert.equal(gate.observe(2), true);
    assert.equal(gate.observe(0), true);
    assert.equal(gate.observe(1), true);
    assert.equal(gate.observe(0), true);
    assert.equal(gate.observe(0), false);
    assert.equal(gate.locked, false);
    assert.equal(gate.observe(1), false);
    gate.accept();
    gate.reset();
    assert.equal(gate.observe(1), false);
});

test('CSV exports timing samples without face vectors, aliases, or credentials', () => {
    const csv = metricsCsv([{ sample: 1, status: 'candidate', total_ms: 3200,
        alias: 'PRIVATE_ALIAS', descriptor: [12345], token: 'PRIVATE_TOKEN' }]);
    assert.match(csv, /sample,status,references/);
    assert.match(csv, /1,candidate/);
    const [header, row] = csv.split('\n').map((line) => line.trim().split(','));
    assert.equal(row[header.indexOf('total_ms')], '3200');
    assert.doesNotMatch(csv, /PRIVATE|12345|descriptor|token|alias/);
});

test('CSV gives an actionable error for empty samples and escapes cell contents', () => {
    assert.throws(() => metricsCsv([]), /Selesaikan satu pemindaian/);
    assert.ok(metricsCsv([{ status: 'a,"b"\nc' }]).includes('"a,""b""\nc"'));
    assert.ok(metricsCsv([{ status: '=1+1' }]).includes("'=1+1"));
});
import { cameraPrerequisiteMessage, cameraActivationIssue, pauseHiddenCamera, faceRequestError, scanFailureMessage, operatorCodeFromLink, createOperatorCodeSource, verifyOperatorCode } from '../../resources/js/face-prototype-camera.js';

test('operator access uses the link code even if browser autofill changes the displayed input', async () => {
    const input = { value: 'previous-code', readOnly: false };
    const source = createOperatorCodeSource(input);
    const code = 'a'.repeat(48);
    assert.equal(source.applyLink(`#code=${code}`), true);
    assert.equal(input.value, code);
    assert.equal(input.readOnly, true);
    assert.equal(source.fromLink, true);
    input.value = 'autofilled-old-code';

    await verifyOperatorCode('/access', source.value, undefined, async (url, options) => {
        assert.equal(options.headers.Authorization, `Bearer ${code}`);
        return { ok: true, json: async () => ({ authorized: true }) };
    });
});

test('applying another link in the same page replaces the code without accepting malformed fragments', () => {
    const input = { value: '', readOnly: false };
    const source = createOperatorCodeSource(input);
    source.applyLink(`#code=${'a'.repeat(48)}`);

    assert.equal(source.applyLink(`#code=${'b'.repeat(48)}`), true);
    assert.equal(source.value, 'b'.repeat(48));
    assert.equal(input.value, 'b'.repeat(48));
    for (const fragment of ['', '#code=wrong']) {
        assert.equal(source.applyLink(fragment), false);
        assert.equal(source.value, 'b'.repeat(48));
    }
});

test('without a valid link the operator can enter and update the code manually', () => {
    const input = { value: ' manual-code ', readOnly: false };
    const source = createOperatorCodeSource(input);

    assert.equal(source.applyLink('#code=wrong'), false);
    assert.equal(source.fromLink, false);
    assert.equal(source.value, 'manual-code');
    assert.equal(input.readOnly, false);
    input.value = ' replacement-code ';
    assert.equal(source.value, 'replacement-code');
});

test('activation explains the missing field instead of silently blocking the button', () => {
    assert.equal(cameraActivationIssue('', true).field, 'token');
    assert.equal(cameraActivationIssue('short', false).field, 'token');
    assert.equal(cameraActivationIssue('a'.repeat(48), false).field, 'consent');
    assert.equal(cameraActivationIssue(` ${'a'.repeat(48)} `, true), null);
});

test('terminal link accepts only a complete generated code', () => {
    assert.equal(operatorCodeFromLink(`#code=${'a'.repeat(48)}`), 'a'.repeat(48));
    for (const fragment of ['', '#code=wrong', '#code=<script>', `#code=${'a'.repeat(49)}`]) {
        assert.equal(operatorCodeFromLink(fragment), '');
    }
});

test('access probe sends only the credential and requires server confirmation', async () => {
    const signal = new AbortController().signal;
    await verifyOperatorCode('/access', ' code ', signal, async (url, options) => {
        assert.equal(url, '/access');
        assert.equal(options.headers.Authorization, 'Bearer code');
        assert.equal(options.body, '{}');
        assert.equal(options.signal, signal);
        assert.equal(options.credentials, 'omit');
        return { ok: true, json: async () => ({ authorized: true }) };
    });
    await assert.rejects(verifyOperatorCode('/access', 'wrong', signal, async () => ({
        ok: false, status: 401, json: async () => ({}),
    })), /terminal aktif/);
    await assert.rejects(verifyOperatorCode('/access', 'code', signal, async () => ({
        ok: true, json: async () => ({}),
    })), /belum mengonfirmasi/);
});

test('camera explains missing or incomplete operator codes before asking for consent', () => {
    for (const token of ['', '   ', 'abc']) {
        assert.match(cameraPrerequisiteMessage(token, false), /Langkah 1: salin kode operator/);
        assert.match(cameraPrerequisiteMessage(token, true), /Langkah 1: salin kode operator/);
    }
});

test('camera explains required consent after an operator code is supplied', () => {
    assert.match(cameraPrerequisiteMessage('a'.repeat(48), false), /Langkah 2: centang persetujuan/);
});

test('camera guides the operator to Chrome permissions when both prerequisites are met', () => {
    assert.equal(cameraPrerequisiteMessage(` ${'a'.repeat(48)} `, true),
        'Siap. Klik Aktifkan kamera, lalu pilih Izinkan jika Chrome meminta akses kamera.');
});

test('switching tabs before camera activation preserves the existing instruction or error', () => {
    let message = 'Kode operator belum diisi.';
    pauseHiddenCamera({ hidden: true, active: false,
        stop: () => assert.fail('An inactive camera should not be stopped.'),
        notify: (value) => { message = value; },
    });
    assert.equal(message, 'Kode operator belum diisi.');
    message = 'Izin kamera ditolak.';
    pauseHiddenCamera({ hidden: true, active: false,
        stop: () => assert.fail('An inactive camera should not be stopped.'),
        notify: (value) => { message = value; },
    });
    assert.equal(message, 'Izin kamera ditolak.');
});

test('leaving an active camera stops it and explains how to resume', () => {
    let stopped = false;
    let message = '';
    pauseHiddenCamera({ hidden: true, active: true,
        stop: () => { stopped = true; }, notify: (value) => { message = value; },
    });
    assert.equal(stopped, true);
    assert.match(message, /klik Aktifkan kamera untuk melanjutkan/);
});

test('returning to the visible tab does not stop the camera or overwrite its status', () => {
    pauseHiddenCamera({ hidden: false, active: true,
        stop: () => assert.fail('Visible cameras should remain active.'),
        notify: () => assert.fail('Status should remain unchanged.'),
    });
});

test('failed scans preserve actionable server errors for the result panel', () => {
    const missingCode = faceRequestError(503, 'Kode operator belum diterima server.');
    assert.equal(scanFailureMessage(new Error(missingCode)), 'Server menolak uji (503): Kode operator belum diterima server.');
    assert.match(scanFailureMessage(new Error(faceRequestError(401, ''))), /terminal aktif/);
    assert.match(scanFailureMessage(new TypeError('Failed to fetch')), /Server\/jaringan tidak tersedia/);
});
