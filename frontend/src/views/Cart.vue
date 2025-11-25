<template>
  <div class="container my-5">
    <h1 class="mb-4">Tu Carrito</h1>
    
    <div v-if="cart.length === 0" class="text-center py-5">
      <h4 class="text-muted">Tu carrito está vacío</h4>
      <router-link to="/" class="btn btn-primary mt-3">Explorar productos</router-link>
    </div>
    
    <div v-else class="row">
      <div class="col-md-8">
        <div class="card mb-3" v-for="item in cart" :key="item.id">
          <div class="row g-0">
            <div class="col-md-3">
              <img :src="item.imagen_url || 'https://via.placeholder.com/150'" class="img-fluid rounded-start" :alt="item.titulo">
            </div>
            <div class="col-md-9">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h5 class="card-title">{{ item.titulo }}</h5>
                    <p class="card-text text-muted">{{ item.categoria }}</p>
                  </div>
                  <div>
                    <p class="h4 text-primary">${{ parseFloat(item.precio).toFixed(2) }}</p>
                    <button class="btn btn-sm btn-danger" @click="removeFromCart(item.id)">Eliminar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Resumen de compra</h5>
            <hr>
            <div class="d-flex justify-content-between mb-2">
              <span>Subtotal</span>
              <span>${{ total.toFixed(2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Envío</span>
              <span>$10.00</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-3">
              <strong>Total</strong>
              <strong class="text-primary">${{ (total + 10).toFixed(2) }}</strong>
            </div>
            
            <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#checkoutModal">
              Proceder al pago
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal fade" id="checkoutModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Finalizar Compra</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handleCheckout">
              <h6 class="mb-3">Datos de la tarjeta</h6>
              <div class="mb-3">
                <label class="form-label">Número de tarjeta</label>
                <input type="text" class="form-control" v-model="cardNumber" placeholder="1234 5678 9012 3456" maxlength="16" required>
              </div>
              <div class="row">
                <div class="col-6 mb-3">
                  <label class="form-label">Fecha exp.</label>
                  <input type="text" class="form-control" v-model="cardExpiry" placeholder="MM/YY" maxlength="5" required>
                </div>
                <div class="col-6 mb-3">
                  <label class="form-label">CVV</label>
                  <input type="text" class="form-control" v-model="cardCvv" placeholder="123" maxlength="3" required>
                </div>
              </div>
              
              <h6 class="mb-3 mt-4">Dirección de envío</h6>
              <div class="mb-3">
                <label class="form-label">Dirección completa</label>
                <textarea class="form-control" v-model="shippingAddress" rows="3" required></textarea>
              </div>
              
              <button type="submit" class="btn btn-primary w-100" :disabled="processing">
                {{ processing ? 'Procesando...' : 'Confirmar Compra' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useUser } from '../stores/user'
import { Modal } from 'bootstrap'

const router = useRouter()
const { currentUser } = useUser()

const cart = ref([])
const cardNumber = ref('')
const cardExpiry = ref('')
const cardCvv = ref('')
const shippingAddress = ref('')
const processing = ref(false)

const total = computed(() => {
  return cart.value.reduce((sum, item) => sum + parseFloat(item.precio), 0)
})

const loadCart = () => {
  cart.value = JSON.parse(localStorage.getItem('cart') || '[]')
}

const removeFromCart = (id) => {
  cart.value = cart.value.filter(item => item.id !== id)
  localStorage.setItem('cart', JSON.stringify(cart.value))
}

const handleCheckout = async () => {
  if (!currentUser.value) {
    alert('Debes iniciar sesión')
    return
  }
  
  processing.value = true
  
  try {
    for (const item of cart.value) {
      await axios.post('http://localhost/TPFinalInterfaces/backend/api/comprar.php', {
        producto_id: item.id,
        comprador_uid: currentUser.value.uid,
        datos_envio: shippingAddress.value
      })
    }
    
    localStorage.removeItem('cart')
    cart.value = []
    
    const modalEl = document.getElementById('checkoutModal')
    const modal = Modal.getInstance(modalEl)
    modal.hide()
    
    alert('¡Compra realizada exitosamente!')
    router.push('/')
  } catch (error) {
    console.error('Error processing purchase:', error)
    alert('Error al procesar la compra: ' + (error.response?.data?.error || error.message))
  } finally {
    processing.value = false
  }
}

onMounted(() => {
  loadCart()
})
</script>
