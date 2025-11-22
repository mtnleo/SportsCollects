<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
      <router-link to="/" class="navbar-brand fw-bold">🏆 SportsCollects</router-link>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <form class="d-flex mx-auto my-2 my-lg-0" style="max-width: 500px; width: 100%;">
          <input class="form-control" type="search" placeholder="Buscar productos..." v-model="searchQuery">
        </form>
        
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item" v-if="!currentUser">
            <button class="btn btn-outline-light" @click="$emit('open-auth')">Ingresar</button>
          </li>
          
          <template v-if="currentUser">
            <li class="nav-item">
              <router-link to="/vender" class="btn btn-primary me-2">+ Vender Artículo</router-link>
            </li>
            
            <li class="nav-item">
              <router-link to="/carrito" class="nav-link position-relative">
                🛒
                <span v-if="cartCount > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  {{ cartCount }}
                </span>
              </router-link>
            </li>
            
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown" :class="{ show: dropdownOpen }">
                {{ currentUser.email }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end" :class="{ show: dropdownOpen }">
                <li><router-link to="/perfil" class="dropdown-item" @click="dropdownOpen = false">Mi Perfil</router-link></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" @click.prevent="handleLogout">Cerrar Sesión</a></li>
              </ul>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useUser } from '../stores/user'
import { signOut } from 'firebase/auth'
import { auth } from '../firebase'

const { currentUser } = useUser()
const searchQuery = ref('')
const dropdownOpen = ref(false)

const cartCount = computed(() => {
  const cart = JSON.parse(localStorage.getItem('cart') || '[]')
  return cart.length
})

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const handleLogout = async () => {
  dropdownOpen.value = false
  try {
    await signOut(auth)
  } catch (error) {
    console.error('Error logging out:', error)
  }
}

defineEmits(['open-auth'])
</script>
