import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Chart.js dipecah jadi chunk terpisah, hanya diunduh di halaman yang memanggilnya
window.muatChart = () => import('chart.js/auto').then((m) => m.default);

Alpine.start();
