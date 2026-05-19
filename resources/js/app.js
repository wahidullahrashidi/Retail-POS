//
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import ApexCharts from 'apexcharts';

// Make it available globally so Alpine can use `new ApexCharts(...)`
window.ApexCharts = ApexCharts;