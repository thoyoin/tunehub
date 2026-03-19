<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
import { useSubscriptionStore } from "@/stores/subscription";
import { computed } from "vue";

const auth = useAuthStore();
const subscriptionStore = useSubscriptionStore();

const expires = computed(
    () =>
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
        <div class="modal-dialog modal-dialog-centered" style="width: 400px">
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
                    <div class="d-flex flex-row align-items-center justify-content-evenly">
                        <img
                            class="rounded-4 me-2"
                            style="width: 100px; height: 100px"
                            :src="auth.user?.profile_picture"
                            alt="cover"
                        />
                        <div style="font-size: 15px" class="fw-bold d-flex flex-column">
                            <div class="d-flex flex-column gap-2 align-items-center">
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
                    </div>
                    <div class="mt-3" style="border-top: 1px solid rgba(228, 228, 228, 0.15)">
                        <div class="row d-flex flex-row my-2">
                            <div class="col d-flex flex-column">
                                <span class="opacity-50">Plan</span>
                                <span class="fs-5 fw-bold">
                                    {{ auth.user?.subscriptions[0]?.type }}
                                </span>
                            </div>
                            <div class="col d-flex flex-column">
                                <span class="opacity-50">Next Billing</span>
                                <span class="fs-5 fw-bold">
                                    {{ subscriptionStore.subscriptionDetails?.next_billing }}
                                </span>
                            </div>
                        </div>
                        <div class="row d-flex flex-row mb-2">
                            <div class="col d-flex flex-column">
                                <span class="opacity-50">Price</span>
                                <div class="d-flex flex-row align-items-end">
                                    <span class="fs-5 fw-bold">
                                        ${{ subscriptionStore.subscriptionDetails?.amount / 100 }}
                                    </span>
                                    <span style="margin-left: 3px" class="fw-bold">
                                        / {{ subscriptionStore.subscriptionDetails?.interval }}
                                    </span>
                                </div>
                            </div>
                            <div class="col d-flex flex-column">
                                <span class="opacity-50">Status</span>
                                <span class="fs-5 fw-bold">
                                    {{ auth.user?.subscriptions[0]?.stripe_status }}
                                </span>
                            </div>
                        </div>
                        <div style="border-top: 1px solid rgba(228, 228, 228, 0.15)">
                            <div class="py-2 fw-bold d-flex flex-row gap-2 align-items-center">
                                <span>Payment Method</span>
                                <img src="@/assets/svg/credit.svg" alt="" />
                            </div>
                            <div class="d-flex flex-column gap-1 card-element">
                                <div
                                    class="d-flex flex-row align-items-center w-100 justify-content-between"
                                >
                                    <div class="d-flex flex-row">
                                        <template
                                            v-if="
                                                subscriptionStore.subscriptionDetails?.card
                                                    .brand === 'mastercard'
                                            "
                                        >
                                            <img
                                                class="me-1"
                                                style="width: 30px"
                                                src="@/assets/svg/ma_symbol.svg"
                                                alt="mastercard"
                                            />
                                        </template>
                                        <template v-else>
                                            <img
                                                class="me-1"
                                                style="width: 35px"
                                                src="@/assets/svg/visa.svg"
                                                alt="visa"
                                            />
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
                                    <div class="ms-2">
                                        <div
                                            @click="subscriptionStore.goToBillingPortal()"
                                            style="font-size: 14px"
                                            class="btn-change-card"
                                        >
                                            Change
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-center w-100">
                        <button
                            @click="subscriptionStore.goToBillingPortal()"
                            class="btn-cancel-subscription"
                        >
                            Manage Subscription
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(40,40,41, .5);
    z-index: 1000;
    pointer-events: auto;
    user-select: none;
    border-radius: 30px;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
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
    background-color: rgb(158, 23, 63);
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
    border: solid 1px rgb(75, 75, 75);
    border-radius: 15px;
    padding: 3px 8px;
    font-size: 12px;
}

.btn-change-card {
    border: solid 1px rgb(75, 75, 75);
    padding: 3px 6px;
    border-radius: 20px;
    transition: 0.1s ease-in-out;

    &:hover {
        border-color: rgb(228, 228, 228, 0.5);
    }

    &:active {
        color: rgba(228, 228, 228, 0.5);
    }
}

.btn-cancel-subscription {
    background: rgb(32, 32, 32, 0.2) !important;
    backdrop-filter: blur(4px);
    border: 1px solid rgb(46, 46, 46) !important;
    border-radius: 15px !important;
    color: rgb(228, 228, 228) !important;
    height: 30px;
    display: flex;
    align-items: center;
    padding: 0 10px !important;
    cursor: default;
    transition: 0.1s ease-in-out;
    font-weight: bold;
    font-size: 14px;

    &:hover {
        border-color: rgb(75, 75, 75) !important;
    }

    &:active {
        border-color: rgb(32, 32, 32) !important;
    }
}
</style>
