<template>
  <div class="container my-5">
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
    
    <div v-else-if="producto" class="row">
      <div class="col-md-6">
        <img :src="producto.imagen_url || 'https://via.placeholder.com/500'" class="img-fluid rounded" :alt="producto.titulo">
      </div>
      
      <div class="col-md-6">
        <span class="badge bg-secondary mb-3">{{ producto.categoria }}</span>
        <h1 class="mb-3">{{ producto.titulo }}</h1>
        <p class="price mb-4">${{ parseFloat(producto.precio).toFixed(2) }}</p>
        <p class="text-muted">{{ producto.descripcion }}</p>
        
        <div class="mb-4">
          <strong>Vendedor:</strong> {{ producto.nombre_completo || producto.email }}
        </div>
        
        <button v-if="producto.estado === 'disponible' && !isOwnProduct" class="btn btn-primary btn-lg w-100 mb-3" @click="addToCart">
          Agregar al Carrito
        </button>
        
        <div v-else-if="isOwnProduct" class="alert alert-info">
          Este es tu producto
        </div>
        
        <div v-else class="alert alert-warning">
          Este producto ya fue vendido
        </div>
        
        <div v-if="producto.latitud && producto.longitud" class="mt-4">
          <h5 class="mb-3">Ubicación del vendedor</h5>
          <MapView :lat="parseFloat(producto.latitud)" :lng="parseFloat(producto.longitud)" />
        </div>
      </div>
    </div>
    
    <div v-if="otrosProductos.length > 0" class="mt-5">
      <h3 class="mb-4">Otros productos que te pueden interesar</h3>
      <div class="row g-4">
        <div v-for="product in otrosProductos" :key="product.id" class="col-md-3">
          <ProductCard :product="product" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import MapView from '../components/MapView.vue'
import ProductCard from '../components/ProductCard.vue'
import { useUser } from '../stores/user'

const route = useRoute()
const router = useRouter()
const { currentUser, userData } = useUser()

const producto = ref(null)
const otrosProductos = ref([])
const loading = ref(true)

const isOwnProduct = computed(() => {
  return userData.value && producto.value && userData.value.id === producto.value.usuario_id
})

const fetchProducto = async (id) => {
  try {
    const response = await axios.get(`http://localhost/TPFinalInterfaces/backend/api/productos.php?id=${id}`)
    producto.value = response.data
    
    const allResponse = await axios.get('http://localhost/TPFinalInterfaces/backend/api/productos.php')
    otrosProductos.value = allResponse.data
      .filter(p => p.id != id)
      .slice(0, 4)
  } catch (error) {
    console.error('Error fetching product:', error)
  } finally {
    loading.value = false
  }
}

const addToCart = () => {
  if (!currentUser.value) {
    alert('Debes iniciar sesión para agregar productos al carrito')
    return
  }
  
  const cart = JSON.parse(localStorage.getItem('cart') || '[]')
  const exists = cart.find(item => item.id === producto.value.id)
  
  if (!exists) {
    cart.push(producto.value)
    localStorage.setItem('cart', JSON.stringify(cart))
    alert('Producto agregado al carrito')
    router.push('/carrito')
  } else {
    alert('Este producto ya está en tu carrito')
  }
}

onMounted(() => {
  fetchProducto(route.params.id)
})

watch(() => route.params.id, (newId, oldId) => {
  if (newId && newId !== oldId) {
    loading.value = true
    fetchProducto(newId)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})
</script>
