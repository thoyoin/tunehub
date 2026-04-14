<script setup lang="ts">
import ApexCharts from "vue3-apexcharts";
import { computed } from "vue";

import { useArtistStore } from "@/stores/artistStudio";

const artistStore = useArtistStore();

const series = computed(() => [
    {
        name: "Streams",
        data: artistStore.artistStreamsDaily?.map(i => Number(i.plays)) ?? []
    }
])

const options = computed(() => ({
    colors: ["rgb(158, 23, 63)"],
    chart: {
        type: "area",
        toolbar: { show: false },
        zoom: { enabled: false },
        animations: {
            enabled: true,
            easing: "easeinout",
            speed: 600
        },
    },
    xaxis: {
        categories:  artistStore.artistStreamsDaily?.map(i => i.date) ?? [],
        labels: {
            style: {
                colors: "rgb(228,228,228)",
            }
        }
    },
    yaxis: {
        labels: {
            style: {
                colors: "rgb(228,228,228)"
            }
        }
    },
    stroke: {
        curve: "smooth",
        width: 3
    },
    markers: {
        size: 4,
        hover: {
            size: 6
        }
    },
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.05,
            stops: [0, 90, 100]
        },
    },
    tooltip: {
        theme: false,
        x: {
            show: true
        },
        style: {
            fontSize: '13px'
        }
    },
    grid: {
        borderColor: "rgb(75,75,75)",
        strokeDashArray: 4
    },
}))
</script>

<template>
    <div class="chart-wrapper">
        <ApexCharts
            type="area"
            height="400"
            :options="options"
            :series="series"
        />
    </div>
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
.chart-wrapper {
    width: 100%;
    max-width: 1300px;
}
</style>
