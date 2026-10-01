const columns = ['sample', 'status', 'references', 'payload_bytes', 'detect_ms', 'quality_ms', 'extract_ms',
    'roundtrip_ms', 'server_ms', 'match_ms', 'render_ms', 'bootstrap_ms', 'guard_ms', 'dispatch_validation_ms', 'opcode_cache',
    'detector_input', 'backend', 'graphics_hint', 'frame_width', 'frame_height', 'total_ms', 'scenario', 'reason_code', 'attempt_ms',
    'motion_status', 'motion_ms', 'motion_frames', 'motion_plan', 'preparation_ms', 'preparation_no_face_frames'];

export function metricsCsv(samples) {
    if (!samples.length) throw new Error('Belum ada sampel. Selesaikan satu pemindaian terlebih dahulu.');
    const cell = (value) => {
        const text = String(value ?? '');
        const safe = /^[=+\-@\t\r]/.test(text) ? `'${text}` : text;
        return /[",\r\n]/.test(safe) ? `"${safe.replaceAll('"', '""')}"` : safe;
    };
    return [columns.join(','), ...samples.map((sample) => columns.map((key) => cell(sample[key])).join(','))].join('\r\n');
}
