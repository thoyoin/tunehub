<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
import { useSubscriptionStore } from "@/stores/subscription";
import { computed, onMounted } from "vue";

const auth = useAuthStore();
const subscriptionStore = useSubscriptionStore();

const expires = computed(
    () =>
        "0" +
        subscriptionStore.subscriptionDetails?.card.exp_month +
        "/" +
        (subscriptionStore.subscriptionDetails?.card.exp_year % 100),
);
</script>

<template>
    <div
        class="modal fade"
        id="subscriptionModal"
        tabindex="-1"
        aria-labelledby="subscriptionModalTitle"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <transition name="fade">
                    <div
                        v-if="subscriptionStore.isLoading"
                        class="loading-overlay d-flex flex-column align-items-center justify-content-center"
                    >
                        <div class="fw-bold opacity-75">Fetching your data</div>
                        <div class="search-spinner my-4"></div>
                        <div class="fw-bold opacity-75">wait a little bit...</div>
                    </div>
                </transition>
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="subscriptionModalTitle">
                        <span style="color: rgb(158, 23, 63)" class="fw-bold">TuneHub</span>
                        <span class="fw-normal"> Subscription</span>
                    </h1>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-row align-items-center justify-content-between">
                        <img
                            class="rounded-4 me-2"
                            style="width: 90px; height: 90px"
                            :src="auth.user?.profile_picture"
                            alt="cover"
                        />
                        <div style="font-size: 15px" class="fw-bold d-flex flex-column">
                            <div class="d-flex flex-column gap-2 align-items-center">
                                <span>
                                    {{ auth.user?.subscriptions[0]?.type }}
                                </span>
                                <span class="badge">
                                    {{ auth.user?.subscriptions[0]?.stripe_status }}
                                </span>
                                <span class="opacity-50" style="font-size: 11px">
                                    Member since
                                    {{
                                        subscriptionStore.subscriptionDetails?.current_period_start
                                    }}
                                </span>
                            </div>
                        </div>
                        <div></div>
                    </div>
                    <div class="mt-3" style="border-top: 1px solid rgba(228, 228, 228, 0.15)">
                        <div class="pt-1 fw-bold">Subscription details</div>
                        <div class="row d-flex flex-row mb-2">
                            <div class="col d-flex flex-column">
                                <span class="opacity-50">Plan</span>
                                <span class="fs-5 fw-bold" style="color: rgb(158, 23, 63)">
                                    {{ auth.user?.subscriptions[0]?.type }}
                                </span>
                            </div>
                            <div class="col d-flex flex-column">
                                <span class="opacity-50">Next Billing</span>
                                <span class="fs-5 fw-bold" style="color: rgb(158, 23, 63)">
                                    {{ subscriptionStore.subscriptionDetails?.next_billing }}
                                </span>
                            </div>
                        </div>
                        <div class="row d-flex flex-row mb-2">
                            <div class="col d-flex flex-column">
                                <span class="opacity-50">Price</span>
                                <span class="fs-5 fw-bold" style="color: rgb(158, 23, 63)">
                                    {{ subscriptionStore.subscriptionDetails?.amount / 100 }}
                                </span>
                            </div>
                            <div class="col d-flex flex-column">
                                <span class="opacity-50">Status</span>
                                <span class="fs-5 fw-bold" style="color: rgb(158, 23, 63)">
                                    {{ auth.user?.subscriptions[0]?.stripe_status }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-3" style="border-top: 1px solid rgba(228, 228, 228, 0.15)">
                            <div class="py-1 fw-bold d-flex flex-row gap-2 align-items-center">
                                <span>Payment Method</span>
                                <img src="@/assets/svg/credit.svg" alt="" />
                            </div>
                            <div class="d-flex flex-column gap-1 card-element">
                                <div class="d-flex flex-row align-items-center">
                                    <template v-if="subscriptionStore.subscriptionDetails?.card.brand === 'mastercard'">
                                        <img
                                            class="me-1"
                                            style="width: 30px"
                                            src="@/assets/svg/ma_symbol.svg"
                                            alt="mastercard"
                                        >
                                    </template>
                                    <template v-else>
                                        <img
                                            class="me-1"
                                            style="width: 35px"
                                            src="@/assets/svg/visa.svg"
                                            alt="visa"
                                        >
                                    </template>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex flex-row gap-1 align-items-center">
                                            <span>
                                                {{
                                                    subscriptionStore.subscriptionDetails?.card
                                                        .brand
                                                }}
                                            </span>
                                            <span style="margin-top: 6px"> **** </span>
                                            <span>
                                                {{
                                                    subscriptionStore.subscriptionDetails?.card
                                                        .last4
                                                }}
                                            </span>
                                        </div>
                                        <div class="d-flex flex-row">
                                            <span> Expires {{ expires }} </span>
                                            <span>
                                                <img
                                                    style="width: 20px"
                                                    src="@/assets/svg/dot.svg"
                                                    alt=""
                                            /></span>
                                            <span>
                                                {{
                                                    subscriptionStore.subscriptionDetails?.card
                                                        .country
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(32, 32, 32, 0.5);
    //backdrop-filter: blur(4px);
    z-index: 1000;
    pointer-events: auto;
    user-select: none;
    border-radius: 30px;
    backdrop-filter: saturate(180%) blur(10px);
    -webkit-backdrop-filter: saturate(180%) blur(10px);
}
.search-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(228, 228, 228, 0.2);
    border-top: 2px solid rgb(158, 23, 63);
    border-radius: 50%;
    animation: spin 0.4s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.modal-content {
    background: rgb(40, 40, 41);
    color: rgb(228, 228, 228);
    .modal-header {
        border-color: rgb(75, 75, 75);
    }
    .modal-footer {
        border-color: rgb(75, 75, 75);
    }
}
.form-control {
    border-color: rgb(75, 75, 75) !important;
    color: rgb(228, 228, 228) !important;
    max-width: 600px !important;

    &:focus {
        box-shadow: none;
        border-color: #ff2667 !important;
    }
}
.badge {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(228, 228, 228, 0.2);
    border-radius: 15px;
    padding: 3px 6px;
    font-weight: bold;
    font-size: 13px;
}

.card-element {
    font-weight: bold;
    display: flex;
    align-items: start;
    justify-content: center;
    //background-color: rgba(228, 228, 228, 0.2);
    border: solid 1px rgb(75, 75, 75);
    border-radius: 15px;
    padding: 3px 8px;
    font-size: 12px;
}
</style>
