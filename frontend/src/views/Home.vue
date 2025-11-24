<template>
  <div class="home-view">
    <section class="hero-section">
      <div class="container hero-content">
        <div class="row align-items-center g-5">
          <div class="col-lg-7 text-center text-lg-start">
            <span class="badge bg-light text-dark px-3 py-2 mb-3">Marketplace verificado</span>
            <h1 class="display-4 fw-bold mb-3">Encuentra tesoros deportivos únicos</h1>
            <p class="lead mb-4">Compra, vende y conecta con miles de coleccionistas apasionados. Descubre memorabilia certificada y piezas históricas cerca de ti.</p>
            <div class="d-flex hero-actions justify-content-center justify-content-lg-start">
              <router-link to="/vender" class="btn btn-primary">Publicar artículo</router-link>
              <router-link to="/carrito" class="btn btn-outline-light">Ver mi carrito</router-link>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card p-4 text-center">
              <h4 class="mb-3">Confianza de coleccionistas</h4>
              <p class="text-muted mb-4">Transacciones protegidas, envíos asegurados y soporte especializado las 24 hs.</p>
              <div class="row g-3">
                <div class="col-6">
                  <div class="stat-card h-100">
                    <div class="stat-value">4.9/5</div>
                    <p class="text-muted mb-0">Calificación global</p>
                  </div>
                </div>
                <div class="col-6">
                  <div class="stat-card h-100">
                    <div class="stat-value">+120K</div>
                    <p class="text-muted mb-0">Ventas exitosas</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="container">
      <div class="stat-grid">
        <div class="stat-card" v-for="item in metrics" :key="item.label">
          <div class="stat-value">{{ item.value }}</div>
          <p class="text-muted mb-0">{{ item.label }}</p>
        </div>
      </div>
    </section>

    <section class="container py-5">
      <div class="filter-panel">
        <button class="filter-chip" :class="{ active: !selectedCategory }" @click="filterByCategory(null)">Todos</button>
        <button class="filter-chip" v-for="cat in categories" :key="cat" :class="{ active: selectedCategory === cat }" @click="filterByCategory(cat)">
          {{ cat }}
        </button>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>

      <div v-else class="row g-4">
        <div v-for="product in productos" :key="product.id" class="col-md-6 col-xl-3">
          <ProductCard :product="product" />
        </div>
      </div>

      <div v-if="!loading && productos.length === 0" class="empty-state mt-4">
        <h5>No se encontraron productos para esta categoría</h5>
        <p class="text-muted mb-0">Prueba con otra categoría o vuelve a intentar más tarde.</p>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import ProductCard from '../components/ProductCard.vue'

const productos = ref([])
const loading = ref(true)
const selectedCategory = ref(null)
const categories = ['Futbol', 'Basketball', 'F. Americano', 'Hockey', 'Baseball', 'Tenis']

const metrics = computed(() => [
  {
    label: 'Publicaciones activas',
    value: productos.value.length.toString().padStart(2, '0')
  },
  {
    label: 'Categorías disponibles',
    value: categories.length
  },
  {
    label: 'Coleccionistas felices',
    value: '2.5K+'
  },
  {
    label: 'Tasa de satisfacción',
    value: '98%'
  }
])

const fetchProductos = async (categoria = null) => {
  loading.value = true
  try {
    let url = 'http://localhost/TPFinalInterfaces/backend/api/productos.php'
    if (categoria) {
      url += `?categoria=${encodeURIComponent(categoria)}`
    }
    const response = await axios.get(url)
    productos.value = response.data
  } catch (error) {
    console.error('Error fetching products:', error)
  } finally {
    loading.value = false
  }
}

const filterByCategory = (categoria) => {
  selectedCategory.value = categoria
  fetchProductos(categoria)
}

onMounted(() => {
  fetchProductos()
})
</script>
