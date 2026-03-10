<script setup lang="ts">
import ApexCharts from "vue3-apexcharts";
import { useOverviewStore } from "@/stores/AdminPanel/overview";
import { computed } from "vue";

const overviewStore = useOverviewStore();

const series = computed(() => [
    {
        name: "Streams",
        data: overviewStore.playsPerMonth?.map(i => i.plays) ?? []
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
        categories: overviewStore.playsPerMonth?.map(i => i.date) ?? [],
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
    <ApexCharts
        type="line"
        height="300"
        :options="options"
        :series="series"
    />
</template>

<style scoped></style>
