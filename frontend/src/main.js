import { createApp } from 'vue'
import { createPinia } from 'pinia'
import Toast from 'vue-toastification'
import App from './App.vue'
import router from './router/index'

import "vue-toastification/dist/index.css";
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min'
import VueApexCharts from "vue3-apexcharts";

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.use(Toast, {
    position: "bottom-center",
    timeout: 2000,
    closeButton: false,
    hideProgressBar: true,
})

app.use(VueApexCharts)

app.mount('#app')
