<template>
  <div class="card h-100">
    <img :src="product.imagen_url || 'https://via.placeholder.com/300x200'" class="product-image" :alt="product.titulo">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <h5 class="card-title mb-0">{{ product.titulo }}</h5>
        <span class="badge bg-secondary">{{ product.categoria }}</span>
      </div>
      <p class="card-text text-muted small">{{ truncateDescription(product.descripcion) }}</p>
      <div class="d-flex justify-content-between align-items-center">
        <span class="price">${{ parseFloat(product.precio).toFixed(2) }}</span>
        <router-link :to="`/producto/${product.id}`" class="btn btn-primary btn-sm">Ver Detalle</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const truncateDescription = (text) => {
  if (!text) return ''
  return text.length > 80 ? text.substring(0, 80) + '...' : text
}
</script>
