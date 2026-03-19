<script setup lang="ts">
import ApexCharts from "vue3-apexcharts";
import { useArtistStore } from "@/stores/artistStudio";
import { computed } from "vue";

const artistStore = useArtistStore();

const series = computed(() => [
    {
        name: "Earnings",
        data: Array.isArray(artistStore.artistEarnings)
            ? artistStore.artistEarnings.map(i => i.earnings)
            : artistStore.artistEarnings?.earnings ?? []
    }
])

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
            borderRadius: 20,
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
        labels: {
            formatter: (val: number) => val / 100,
            style: {
                colors: "rgb(228,228,228)"
            }
        }
    },
    tooltip: {
        y: {
            formatter: (val: number) => `$${val / 100} earnings`
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
        width="1400px"
        :options="options"
        :series="series"
    />
</template>

<style scoped></style>
