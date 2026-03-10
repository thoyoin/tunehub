<script setup lang="ts">
import ApexCharts from "vue3-apexcharts";
import { useOverviewStore } from "@/stores/AdminPanel/overview";
import { computed } from "vue";

const overviewStore = useOverviewStore();

const series = computed(() => [
    {
        name: "User Growth",
        data: overviewStore.userGrowth?.map(i => i.users) ?? []
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
        categories: overviewStore.userGrowth?.map(i => i.date) ?? [],
        labels: {
            style: {
                fontSize: "12px",
                colors: "rgb(228,228,228)"
            }
        }
    },
    yaxis: {
        labels: {
            formatter: (val: number) => Math.round(val).toString(),
            style: {
                colors: "rgb(228,228,228)"
            }
        }
    },
    tooltip: {
        y: {
            formatter: (val: number) => `${val} users`
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
        height="300"
        :options="options"
        :series="series"
    />
</template>

<style scoped></style>
