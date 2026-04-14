<script setup lang="ts">
import ApexCharts from "vue3-apexcharts";
import { useOverviewStore } from "@/stores/AdminPanel/overview";
import { computed } from "vue";

const overviewStore = useOverviewStore();

const visibleLabelStep = computed(() => {
    const pointsCount = overviewStore.userGrowth?.length ?? 0;

    if (pointsCount <= 8) return 1;
    if (pointsCount <= 16) return 2;
    if (pointsCount <= 24) return 3;

    return 4;
});

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
            columnWidth: "55%"
        }
    },
    dataLabels: {
        enabled: false,
    },
    xaxis: {
        categories: overviewStore.userGrowth?.map(i => i.date) ?? [],
        tickPlacement: "on",
        labels: {
            rotate: -45,
            rotateAlways: true,
            hideOverlappingLabels: true,
            trim: true,
            style: {
                fontSize: "11px",
                colors: "rgb(228,228,228)"
            },
            formatter: (value: string, _timestamp?: number, index?: number) => {
                const step = visibleLabelStep.value;

                if (typeof index === "number" && index % step !== 0) {
                    return "";
                }

                return value;
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
        height="340"
        :options="options"
        :series="series"
    />
</template>

<style scoped></style>
