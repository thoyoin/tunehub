<script setup lang="ts">
import ApexCharts from "vue3-apexcharts";
import { computed } from "vue";

import { useArtistStore } from "@/stores/artistStudio";

const artistStore = useArtistStore();

const series = computed(() => [
    {
        name: "Earnings",
        data: Array.isArray(artistStore.artistEarnings)
            ? artistStore.artistEarnings.map(i => i.earnings / 100)
            : artistStore.artistEarnings?.earnings.map(i => i / 100) ?? []
    }
])

const maxEarnings = computed(() => {
    const data = Array.isArray(artistStore.artistEarnings)
        ? artistStore.artistEarnings.map(i => i.earnings)
        : artistStore.artistEarnings?.earnings ?? [];

    if (!data.length) return 0;

    return Math.max(...data) / 100;
});

const options = computed(() => ({
    colors: ["rgb(158, 23, 63)"],
    chart: {
        type: "bar",
        toolbar: {
            show: false
        }
    },
    plotOptions: {
        bar: {
            columnWidth: "40%"
        }
    },
    dataLabels: {
        enabled: false,
    },
    xaxis: {
        categories: Array.isArray(artistStore.artistEarnings)
            ? artistStore.artistEarnings.map(i => i.date)
            : artistStore.artistEarnings?.date ?? [],
        labels: {
            style: {
                fontSize: "12px",
                colors: "rgb(228,228,228)"
            }
        }
    },
    yaxis: {
        min: 0,
        max: maxEarnings.value,
        labels: {
            formatter: (val: number) => val.toFixed(2),
            style: {
                colors: "rgb(228,228,228)"
            }
        }
    },
    tooltip: {
        y: {
            formatter: (val: number) => `$${val} earnings`
        }
    },
    grid: {
        borderColor: "rgb(75,75,75)",
        strokeDashArray: 4
    },
}))

</script>

<template>
    <ApexCharts
        type="bar"
        height="400"
        width="1300px"
        :options="options"
        :series="series"
    />
</template>

<style scoped>
:deep(.apexcharts-tooltip) {
    background: transparent !important;
    color: rgb(228, 228, 228) !important;
    backdrop-filter: blur(10px) !important;
    border: 1px solid #3a3a3a !important;
    border-radius: 12px !important;
}

:deep(.apexcharts-tooltip-title) {
    background: rgba(158, 23, 63, .2) !important;
    backdrop-filter: blur(10px) !important;
    color: #ffffff !important;
    border-bottom: none !important;
}

:deep(.apexcharts-xaxistooltip) {
    background: none !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
}
</style>
