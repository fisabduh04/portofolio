import './bootstrap';
import { initializeFaceEnrollment } from './siswa-face-enrollment';
import { initializeFaceAttendance } from './face-attendance';
import { initializeAttendanceLiveRecap } from './attendance-live-recap';

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initializeAttendanceLiveRecap(), { once: true });
    document.addEventListener('DOMContentLoaded', () => initializeFaceEnrollment(), { once: true });
    document.addEventListener('DOMContentLoaded', () => initializeFaceAttendance(), { once: true });
} else {
    initializeAttendanceLiveRecap();
    initializeFaceEnrollment();
    initializeFaceAttendance();
}

async function initializeSearchableSelects(root = document) {
    if (root.matches?.('[data-searchable-select]') || root.querySelector('[data-searchable-select]')) {
        const component = await import('./searchable-select');
        if (root.isConnected) {
            component.initializeSearchableSelects(root);
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initializeSearchableSelects(), { once: true });
} else {
    initializeSearchableSelects();
}

document.addEventListener('searchable-select:init', (event) => initializeSearchableSelects(event.target));
