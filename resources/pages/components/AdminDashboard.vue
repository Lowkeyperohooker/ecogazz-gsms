<template>
    <div class="flex flex-col gap-3 h-full overflow-y-auto no-scrollbar">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 shrink-0">
            <div class="bg-card p-3.5 rounded-xl shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-md after:content-[''] after:absolute after:top-0 after:left-0 after:right-0 after:h-0.75 after:bg-linear-to-br after:from-primary after:to-primary-hover">
                <h4 class="text-[0.56rem] text-gray font-semibold uppercase mb-1 tracking-[0.5px]">Revenue</h4>
                <div class="text-xl font-extrabold text-dark">
                    <i class="fa-solid fa-spinner fa-spin text-sm" v-if="isLoading"></i>
                    <span v-else>₱{{ stats.revenue.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                </div>
                <div class="text-[0.56rem] text-gray mt-1 font-medium capitalize">{{ filterText }}</div>
            </div>
            
            <div class="bg-card p-3.5 rounded-xl shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-md after:content-[''] after:absolute after:top-0 after:left-0 after:right-0 after:h-0.75 after:bg-linear-to-br after:from-success after:to-primary-hover">
                <h4 class="text-[0.56rem] text-gray font-semibold uppercase mb-1 tracking-[0.5px]">Est. Profit</h4>
                <div class="text-xl font-extrabold text-success">
                    <i class="fa-solid fa-spinner fa-spin text-sm" v-if="isLoading"></i>
                    <span v-else>₱{{ stats.profit.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                </div>
                <div class="text-[0.56rem] text-gray mt-1 font-medium capitalize">{{ filterText }}</div>
            </div>
            
            <div class="bg-card p-3.5 rounded-xl shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-md after:content-[''] after:absolute after:top-0 after:left-0 after:right-0 after:h-0.75 after:bg-linear-to-br after:from-warning after:to-[#e8941a]">
                <h4 class="text-[0.56rem] text-gray font-semibold uppercase mb-1 tracking-[0.5px]">Pending Shifts</h4>
                <div class="text-xl font-extrabold text-warning">
                    <i class="fa-solid fa-spinner fa-spin text-sm" v-if="isLoading"></i>
                    <span v-else>{{ stats.pending }}</span>
                </div>
                <div class="text-[0.56rem] text-gray mt-1 font-medium">Needs review</div>
            </div>
            
            <div class="bg-card p-3.5 rounded-xl shadow-sm relative overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-md after:content-[''] after:absolute after:top-0 after:left-0 after:right-0 after:h-0.75 after:bg-linear-to-br after:from-blue after:to-[#4a6ae8]">
                <h4 class="text-[0.56rem] text-gray font-semibold uppercase mb-1 tracking-[0.5px]">Volume Dispensed</h4>
                <div class="text-xl font-extrabold text-blue">
                    <i class="fa-solid fa-spinner fa-spin text-sm" v-if="isLoading"></i>
                    <span v-else>{{ stats.volume.toLocaleString('en-US', {minimumFractionDigits: 2}) }} L</span>
                </div>
                <div class="text-[0.56rem] text-gray mt-1 font-medium capitalize">{{ filterText }}</div>
            </div>
        </div>

        <div class="bg-card rounded-xl shadow-sm border border-light flex flex-col flex-1 min-h-87.5 relative">
            <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
                <h3 class="text-[0.85rem] font-bold">Revenue & Profit Trend</h3>
                
                <select 
                    v-model="timeFilter" 
                    @change="fetchDashboardStats" 
                    class="p-[6px_12px] border-2 border-light rounded-lg text-xs font-bold text-dark transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white cursor-pointer"
                >
                    <option value="today">Today (Hourly)</option>
                    <option value="week">This Week (Daily)</option>
                    <option value="month">This Month (Daily)</option>
                    <option value="year">This Year (Monthly)</option>
                </select>
            </div>

            <div v-if="isLoading" class="absolute inset-0 top-12.5 bg-white/60 backdrop-blur-[2px] z-10 flex flex-col items-center justify-center rounded-b-xl">
                <i class="fa-solid fa-spinner fa-spin text-primary text-3xl mb-2"></i>
                <span class="text-xs font-bold text-gray uppercase tracking-widest">Crunching Data...</span>
            </div>

            <div class="p-4 flex-1 relative min-h-75">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';

const isLoading = ref(true);
const timeFilter = ref('week'); // Default view
let chartInstance = null;

const stats = ref({
    revenue: 0,
    profit: 0,
    pending: 0,
    volume: 0
});

// Helper for the stat cards text
const filterText = computed(() => {
    const texts = {
        'today': 'Today',
        'week': 'This Week',
        'month': 'This Month',
        'year': 'This Year'
    };
    return texts[timeFilter.value] || 'All Time';
});

const fetchDashboardStats = async () => {
    try {
        isLoading.value = true;
        
        // Pass the selected filter to Laravel
        const response = await axios.get('/api/dashboard/stats', {
            params: { filter: timeFilter.value }
        });
        
        stats.value = response.data;
        renderChart(response.data.chartData);

    } catch (error) {
        console.error("Error fetching dashboard stats:", error);
    } finally {
        isLoading.value = false;
    }
};

const renderChart = (chartData) => {
    const ctx = document.getElementById('salesChart');
    if (!ctx || !window.Chart || !chartData) return;

    // Destroy the old chart before drawing a new one so they don't overlap
    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new window.Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Gross Revenue (₱)',
                    data: chartData.sales,
                    borderColor: '#3dbb91',
                    backgroundColor: 'rgba(61,187,145,.1)',
                    fill: true, tension: .4, borderWidth: 2.5,
                    pointRadius: 4, pointBackgroundColor: '#3dbb91',
                    pointBorderColor: '#fff', pointBorderWidth: 2
                },
                {
                    label: 'Est. Profit (₱)',
                    data: chartData.profit,
                    borderColor: '#f5a623',
                    backgroundColor: 'rgba(245,166,35,.1)',
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
                legend: { 
                    position: 'top',
                    labels: { font: { family: 'sans-serif', size: 11, weight: 'bold' }, usePointStyle: true, padding: 16 } 
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += '₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2});
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#f0f3f1' }, 
                    ticks: { 
                        font: { family: 'sans-serif', size: 10, weight: 'bold' },
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    } 
                },
                x: { 
                    grid: { display: false }, 
                    ticks: { font: { family: 'sans-serif', size: 10, weight: 'bold' } } 
                }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
};

onMounted(() => {
    // If Chart.js is not loaded via CDN in your index.html, you may need to inject it here.
    // Assuming it is globally available as window.Chart based on your previous code.
    fetchDashboardStats();
});

onUnmounted(() => {
    if (chartInstance) chartInstance.destroy();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>