<script setup>
import { useReleaseStore} from "@/stores/release.js";
import { useRouter } from "vue-router";

const releaseStore = useReleaseStore();
const router = useRouter();

</script>

<template>
    <div class="modal fade" id="authenticateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-5 w-100">
                <div
                    class="modal-body fw-bold d-flex flex-row align-items-start gap-5"
                >
                    <div>
                        <img
                        style="width: 300px; height: 300px; box-shadow: 0 0 5px 1px rgba(228,228,228, 10%)"
                        :src="releaseStore.pickedRelease?.cover_url"
                        alt="cover"
                        >
                    </div>
                    <div class="d-flex flex-column align-items-center">
                        <div class="fs-3 mb-5">
                            Listen to anything in the free version TuneHub!
                        </div>
                        <button
                            data-bs-dismiss="modal"
                            @click="router.push('register')"
                            type="button"
                            class="btn btn-primary mb-4 w-50 rounded-5"
                        >
                            Register
                        </button>
                        <div>
                            <span class="opacity-50 me-2">Already have an account?</span>
                            <a
                                data-bs-dismiss="modal"
                                class="link-light"
                                @click="router.push('login')"
                                href="#"
                            >Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
