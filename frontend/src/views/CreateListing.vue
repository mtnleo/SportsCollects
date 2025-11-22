<template>
  <div class="container my-5">
    <h1 class="mb-4">📦 Vender Artículo</h1>
    
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label class="form-label">Título del producto</label>
                <input type="text" class="form-control" v-model="form.titulo" required>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" v-model="form.descripcion" rows="4" required></textarea>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Precio (USD)</label>
                  <input type="number" step="0.01" class="form-control" v-model="form.precio" required>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label class="form-label">Categoría</label>
                  <select class="form-select" v-model="form.categoria" required>
                    <option value="">Seleccionar...</option>
                    <option value="Futbol">Futbol</option>
                    <option value="Basketball">Basketball</option>
                    <option value="F. Americano">F. Americano</option>
                    <option value="Hockey">Hockey</option>
                    <option value="Baseball">Baseball</option>
                    <option value="Tenis">Tenis</option>
                  </select>
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label">URL de la imagen</label>
                <input type="url" class="form-control" v-model="form.imagen_url" placeholder="https://ejemplo.com/imagen.jpg">
              </div>
              
              <div class="mb-3">
                <label class="form-label">Ubicación</label>
                <p class="text-muted small">Haz clic en el mapa para seleccionar la ubicación</p>
                <div ref="mapContainer" style="height: 300px; border-radius: 8px;"></div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Latitud</label>
                  <input type="number" step="any" class="form-control" v-model="form.latitud" required>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label class="form-label">Longitud</label>
                  <input type="number" step="any" class="form-control" v-model="form.longitud" required>
                </div>
              </div>
              
              <button type="submit" class="btn btn-primary w-100" :disabled="submitting">
                {{ submitting ? 'Publicando...' : 'Publicar Producto' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import L from 'leaflet'
import { useUser } from '../stores/user'

const router = useRouter()
const { currentUser } = useUser()

const mapContainer = ref(null)
let map = null
let marker = null

const form = ref({
  titulo: '',
  descripcion: '',
  precio: '',
  categoria: '',
  imagen_url: '',
  latitud: -34.6037,
  longitud: -58.3816
})

const submitting = ref(false)

onMounted(() => {
  if (!currentUser.value) {
    alert('Debes iniciar sesión para vender productos')
    router.push('/')
    return
  }
  
  map = L.map(mapContainer.value).setView([form.value.latitud, form.value.longitud], 13)
  
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map)
  
  marker = L.marker([form.value.latitud, form.value.longitud], { draggable: true }).addTo(map)
  
  marker.on('dragend', () => {
    const position = marker.getLatLng()
    form.value.latitud = position.lat
    form.value.longitud = position.lng
  })
  
  map.on('click', (e) => {
    form.value.latitud = e.latlng.lat
    form.value.longitud = e.latlng.lng
    marker.setLatLng(e.latlng)
  })
})

const handleSubmit = async () => {
  submitting.value = true
  
  try {
    const response = await axios.post('http://localhost/TPFinalInterfaces/backend/api/productos.php', {
      ...form.value,
      firebase_uid: currentUser.value.uid
    })
    
    if (response.data.success) {
      alert('Producto publicado exitosamente')
      router.push('/')
    } else {
      alert('Error al publicar el producto')
    }
  } catch (error) {
    console.error('Error creating listing:', error)
    alert('Error: ' + (error.response?.data?.error || error.message))
  } finally {
    submitting.value = false
  }
}
</script>
