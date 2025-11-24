<template>
  <div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
      <div>
        <h1 class="fw-bold mb-1">Mi actividad</h1>
        <p class="text-muted mb-0">Resumen de tus compras recientes y artículos publicados.</p>
      </div>
      <div class="d-flex gap-2">
        <router-link to="/vender" class="btn btn-primary">Publicar nuevo artículo</router-link>
        <router-link to="/" class="btn btn-outline-dark rounded-pill px-4">Explorar</router-link>
      </div>
    </div>

    <div v-if="!currentUser" class="card p-5 text-center">
      <h4 class="mb-3">Inicia sesión para ver tu actividad</h4>
      <p class="text-muted mb-4">Necesitamos identificarte para mostrarte tus compras y ventas.</p>
      <router-link to="/" class="btn btn-primary">Volver al inicio</router-link>
    </div>

    <div v-else>
      <div class="row g-4">
        <div class="col-lg-6">
          <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="mb-0">Mis compras</h4>
              <span class="badge bg-primary-subtle text-primary">{{ compras.length }}</span>
            </div>
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border" role="status"></div>
            </div>
            <div v-else-if="compras.length === 0" class="empty-state">
              <h5>Aún no tienes compras</h5>
              <p class="text-muted mb-0">Explora el catálogo y consigue tu próxima pieza.</p>
            </div>
            <div v-else class="activity-list">
              <div class="activity-item" v-for="item in compras" :key="item.id">
                <div class="activity-info">
                  <strong>{{ item.titulo }}</strong>
                  <small class="text-muted">{{ formatDate(item.fecha_compra) }} · {{ item.vendedor_nombre || item.vendedor_email || 'Vendedor verificado' }}</small>
                </div>
                <div class="text-end">
                  <div class="badge-status" :class="statusClass(item.estado_envio)">{{ estadoLabel(item.estado_envio) }}</div>
                  <p class="fw-semibold mb-0">{{ formatCurrency(item.precio) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="mb-0">Mis artículos</h4>
              <span class="badge bg-primary-subtle text-primary">{{ productos.length }}</span>
            </div>
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border" role="status"></div>
            </div>
            <div v-else-if="productos.length === 0" class="empty-state">
              <h5>No tienes publicaciones</h5>
              <p class="text-muted mb-0">Publica tu primera pieza y llega a miles de coleccionistas.</p>
            </div>
            <div v-else class="activity-list">
              <div class="activity-item" v-for="art in productos" :key="art.id">
                <div class="activity-info">
                  <strong>{{ art.titulo }}</strong>
                  <small class="text-muted">{{ formatDate(art.fecha_publicacion) }} · {{ productSubtitle(art) }}</small>
                </div>
                <div class="text-end">
                  <div class="badge-status" :class="productoStatusClass(art.estado)">{{ productoStatusLabel(art.estado) }}</div>
                  <p class="fw-semibold mb-0">{{ formatCurrency(art.precio) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="errorMessage" class="alert alert-danger mt-4">
        {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useUser } from '../stores/user'

const { currentUser } = useUser()

const compras = ref([])
const productos = ref([])
const loading = ref(false)
const errorMessage = ref('')

const fetchActivity = async () => {
  if (!currentUser.value) {
    compras.value = []
    productos.value = []
    return
  }
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get('http://localhost/TPFinalInterfaces/backend/api/historial.php', {
      params: { firebase_uid: currentUser.value.uid }
    })
    compras.value = response.data.compras || []
    productos.value = response.data.productos || []
  } catch (error) {
    console.error('Error loading activity:', error)
    errorMessage.value = 'No se pudo cargar tu actividad. Intenta nuevamente.'
  } finally {
    loading.value = false
  }
}

const formatDate = (date) => {
  if (!date) return 'Pendiente'
  return new Date(date).toLocaleDateString('es-AR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatCurrency = (value) => {
  return Number(value || 0).toLocaleString('es-AR', {
    style: 'currency',
    currency: 'USD'
  })
}

const statusClass = (estado) => {
  if (estado === 'entregado') return 'success'
  if (estado === 'enviado') return 'neutral'
  return 'warning'
}

const estadoLabel = (estado) => {
  if (estado === 'entregado') return 'Entregado'
  if (estado === 'enviado') return 'Enviado'
  return 'Pendiente'
}

const productoStatusClass = (estado) => {
  return estado === 'vendido' ? 'success' : 'neutral'
}

const productoStatusLabel = (estado) => {
  return estado === 'vendido' ? 'Vendido' : 'En venta'
}

const productSubtitle = (art) => {
  if (art.estado === 'vendido' && (art.comprador_nombre || art.comprador_email)) {
    return `Comprado por ${art.comprador_nombre || art.comprador_email}`
  }
  return `Categoría ${art.categoria}`
}

onMounted(fetchActivity)

watch(() => currentUser.value, (val, oldVal) => {
  if (val && !oldVal) {
    fetchActivity()
  }
  if (!val) {
    compras.value = []
    productos.value = []
  }
})
</script>
