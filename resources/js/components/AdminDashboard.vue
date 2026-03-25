<template>
    <div class="flex flex-col gap-3 h-full overflow-y-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 shrink-0">
            <div class="bg-card p-3.5 rounded-xl shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-md after:content-[''] after:absolute after:top-0 after:left-0 after:right-0 after:h-[3px] after:bg-linear-to-br after:from-primary after:to-primary-hover">
                <h4 class="text-[0.56rem] text-gray font-semibold uppercase mb-1 tracking-[0.5px]">Today's Revenue</h4>
                <div class="text-xl font-extrabold text-dark">₱{{ stats.revenue.toLocaleString() }}</div>
                <div class="text-[0.56rem] text-gray mt-1 font-medium">All shifts</div>
            </div>
            <div class="bg-card p-3.5 rounded-xl shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-md after:content-[''] after:absolute after:top-0 after:left-0 after:right-0 after:h-[3px] after:bg-linear-to-br after:from-success after:to-primary-hover">
                <h4 class="text-[0.56rem] text-gray font-semibold uppercase mb-1 tracking-[0.5px]">Est. Profit</h4>
                <div class="text-xl font-extrabold text-success">₱{{ stats.profit.toLocaleString() }}</div>
                <div class="text-[0.56rem] text-gray mt-1 font-medium">~7% margin</div>
            </div>
            <div class="bg-card p-3.5 rounded-xl shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-md after:content-[''] after:absolute after:top-0 after:left-0 after:right-0 after:h-[3px] after:bg-linear-to-br after:from-warning after:to-[#e8941a]">
                <h4 class="text-[0.56rem] text-gray font-semibold uppercase mb-1 tracking-[0.5px]">Pending Shifts</h4>
                <div class="text-xl font-extrabold text-warning">{{ stats.pending }}</div>
                <div class="text-[0.56rem] text-gray mt-1 font-medium">Needs review</div>
            </div>
            <div class="bg-card p-3.5 rounded-xl shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-md after:content-[''] after:absolute after:top-0 after:left-0 after:right-0 after:h-[3px] after:bg-linear-to-br after:from-blue after:to-[#4a6ae8]">
                <h4 class="text-[0.56rem] text-gray font-semibold uppercase mb-1 tracking-[0.5px]">Volume Dispensed</h4>
                <div class="text-xl font-extrabold text-blue">{{ stats.volume.toLocaleString() }} L</div>
                <div class="text-[0.56rem] text-gray mt-1 font-medium">Today</div>
            </div>
        </div>

        <div class="bg-card rounded-xl shadow-sm border border-light flex flex-col flex-1 min-h-[280px]">
            <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
                <h3 class="text-[0.85rem] font-bold">Revenue Trend</h3>
            </div>
            <div class="p-4 flex-1 relative min-h-[240px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const stats = ref({
    revenue: 145000,
    profit: 10150,
    pending: 2,
    volume: 2590.5
});

let chartInstance = null;

onMounted(() => {
    // Note: Make sure Chart.js is still loaded via CDN in your welcome.blade.php
    // or run `npm install chart.js` and import it here.
    const ctx = document.getElementById('salesChart');
    if (ctx && window.Chart) {
        chartInstance = new window.Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [
                    {
                        label: 'Sales (₱)',
                        data: [125000, 145000, 138000, 158000],
                        borderColor: '#3dbb91',
                        backgroundColor: 'rgba(61,187,145,.08)',
                        fill: true, tension: .4, borderWidth: 2.5,
                        pointRadius: 4, pointBackgroundColor: '#3dbb91',
                        pointBorderColor: '#fff', pointBorderWidth: 2
                    },
                    {
                        label: 'Profit (₱)',
                        data: [12500, 14500, 13800, 15800],
                        borderColor: '#f5a623',
                        backgroundColor: 'rgba(245,166,35,.08)',
                        fill: true, tension: .4, borderWidth: 2.5,
                        pointRadius: 4, pointBackgroundColor: '#f5a623',
                        pointBorderColor: '#fff', pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { font: { family: 'Poppins', size: 11, weight: '600' }, usePointStyle: true, padding: 16 } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f3f1' }, ticks: { font: { family: 'Poppins', size: 10 } } },
                    x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 10 } } }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });
    }
});

onUnmounted(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});
</script>