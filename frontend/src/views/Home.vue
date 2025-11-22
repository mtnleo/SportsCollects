<template>
  <div>
    <div class="hero-section">
      <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Encuentra tesoros deportivos</h1>
        <p class="lead">El marketplace líder en coleccionables y memorabilia deportiva</p>
      </div>
    </div>
    
    <div class="container">
      <div class="row mb-4">
        <div class="col-12">
          <ul class="nav nav-pills justify-content-center">
            <li class="nav-item">
              <a class="nav-link" :class="{ active: !selectedCategory }" href="#" @click.prevent="filterByCategory(null)">
                Todos
              </a>
            </li>
            <li class="nav-item" v-for="cat in categories" :key="cat">
              <a class="nav-link" :class="{ active: selectedCategory === cat }" href="#" @click.prevent="filterByCategory(cat)">
                {{ cat }}
              </a>
            </li>
          </ul>
        </div>
      </div>
      
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>
      
      <div v-else class="row g-4">
        <div v-for="product in productos" :key="product.id" class="col-md-4 col-lg-3">
          <ProductCard :product="product" />
        </div>
      </div>
      
      <div v-if="!loading && productos.length === 0" class="text-center py-5">
        <h4 class="text-muted">No se encontraron productos</h4>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import ProductCard from '../components/ProductCard.vue'

const productos = ref([])
const loading = ref(true)
const selectedCategory = ref(null)
const categories = ['Futbol', 'Basketball', 'F. Americano', 'Hockey', 'Baseball', 'Tenis']

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
