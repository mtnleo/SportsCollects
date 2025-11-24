<template>
  <div class="product-card card h-100">
    <div class="product-media" :style="{ backgroundImage: `url(${imageUrl})` }">
      <span>{{ product.categoria }}</span>
    </div>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start">
        <h5 class="fw-semibold mb-0">{{ product.titulo }}</h5>
        <small class="text-muted">{{ published }}</small>
      </div>
      <p class="text-muted small flex-grow-1">{{ truncatedDescription }}</p>
      <div class="d-flex justify-content-between align-items-center product-card__actions">
        <span class="price mb-0">${{ formattedPrice }}</span>
        <router-link :to="`/producto/${product.id}`" class="btn btn-primary btn-sm px-3 rounded-pill">Ver detalle</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const imageUrl = computed(() => props.product.imagen_url || 'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=800&q=60')

const truncatedDescription = computed(() => {
  const text = props.product.descripcion || ''
  return text.length > 90 ? `${text.slice(0, 90)}...` : text
})

const formattedPrice = computed(() => Number(props.product.precio || 0).toFixed(2))

const published = computed(() => {
  if (!props.product.fecha_publicacion) return 'Reciente'
  return new Date(props.product.fecha_publicacion).toLocaleDateString('es-AR', {
    month: 'short',
    day: 'numeric'
  })
})
</script>
