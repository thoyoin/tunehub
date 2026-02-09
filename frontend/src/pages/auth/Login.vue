<script setup>
    import { ref } from 'vue'
    import { useAuthStore } from '@/stores/auth'
    import { useRouter } from 'vue-router'

    const auth = useAuthStore()
    const email = ref('')
    const password = ref('')
    const router = useRouter()

    async function handleLogin() {
        await auth.login({
            email: email.value,
            password: password.value
        })
    }

</script>

<template>
    <div class="d-flex flex-column justify-content-center align-items-center">
        <div class="d-flex mb-5 mt-4">
            <button
                @click="router.push('/')"
                style="color: rgb(228,228,228)"
                class="btn btn-tunehub fw-lighter fs-1"
            >
                TuneHub
            </button>
        </div>
        <div class="d-flex">
            <form @submit.prevent="handleLogin">
                <div style="width: 400px !important" class="d-flex flex-column justify-content-center align-items-center">
                    <div>
                        <h4 style="color: rgb(228,228,228)" class="fw-lighter mb-5">Login</h4>
                    </div>
                    <div class="form-floating mb-3 w-100">
                        <input
                            type="email"
                            v-model="email"
                            style="border-color: rgb(75,75,75); color: rgb(228,228,228); max-width: 600px"
                            class="form-control rounded-4 bg-minor"
                            :class="{'is-invalid': auth.errors.email}"
                            required
                        />
                        <label for="floatingInput" style="color: rgb(75,75,75)">Email address</label>
                        <span
                            v-if="auth.errors.email"
                            class="text-danger m-0"
                        >
                            {{auth.errors.email[0]}}
                        </span>
                    </div>
                    <div class="form-floating w-100">
                        <input
                            type="password"
                            v-model="password"
                            style="border-color: rgb(75,75,75); color: rgb(228,228,228); max-width: 600px"
                            class="form-control rounded-4 bg-minor"
                            :class="{'is-invalid': auth.errors.password}"
                            required
                        />
                        <label for="floatingPassword" style="color: rgb(75,75,75)">Password</label>
                        <span
                            v-if="auth.errors.password"
                            class="text-danger m-0"
                        >
                            {{auth.errors.email[0]}}
                        </span>
                    </div>
                    <div class="mt-5 d-flex">
                        <button @click.prevent="handleLogin" class="btn btn-primary">Sign In</button>
                    </div>
                    <div class="mt-3 w-100 text-start">
                        <a href="#" @click="router.push('register')" class="link-light">Register</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
    .form-control {
        &:focus {
            box-shadow: none !important;
            border-color: #ff2667 !important;
        }
    }
    .btn-tunehub {
        border: none !important;
        &:hover {
            color: rgba(228,228,228, .7) !important;
        }
    }
</style>
