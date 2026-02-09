import { createApp } from 'vue'
import { createPinia } from 'pinia'
import Toast from 'vue-toastification'
import App from './App.vue'
import router from './router'

import "vue-toastification/dist/index.css";
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.use(Toast, {
    position: "bottom-center",
    timeout: 2000,
    closeButton: false,
    hideProgressBar: true,
})

app.mount('#app')
