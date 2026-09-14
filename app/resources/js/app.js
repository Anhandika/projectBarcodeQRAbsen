import './bootstrap';
import Alpine from 'alpinejs';
import { attendanceScanner } from './attendance-scanner';
import { monitorCountdown } from './monitor-countdown';

window.Alpine = Alpine;

Alpine.data('attendanceScanner', attendanceScanner);
Alpine.data('monitorCountdown', monitorCountdown);

Alpine.start();
