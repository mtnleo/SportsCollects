import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'leaflet/dist/leaflet.css'
import '@fortawesome/fontawesome-free/css/all.min.css'
import './assets/style.css'

createApp(App).use(router).mount('#app')
