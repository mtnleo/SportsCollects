<template>
  <div class="container my-5">
    <h1 class="mb-4">Mi Perfil</h1>
    
    <div v-if="currentUser" class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body text-center">
            <div class="mb-3">
              <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                   style="width: 100px; height: 100px; font-size: 2.5rem;">
                {{ getInitials() }}
              </div>
            </div>
            <h5>{{ currentUser.displayName || currentUser.email }}</h5>
            <p class="text-muted">{{ currentUser.email }}</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title mb-4">Información de la cuenta</h5>
            
            <div class="mb-3">
              <label class="form-label fw-bold">Email</label>
              <p>{{ currentUser.email }}</p>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-bold">Nombre</label>
              <p>{{ currentUser.displayName || 'No configurado' }}</p>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-bold">Fecha de registro</label>
              <p>{{ formatDate(currentUser.metadata.creationTime) }}</p>
            </div>
            
            <hr>
            
            <h5 class="card-title mb-3">Mis publicaciones</h5>
            <p class="text-muted">Aquí aparecerán tus productos publicados</p>
          </div>
        </div>
      </div>
    </div>
    
    <div v-else class="text-center py-5">
      <h4 class="text-muted">Debes iniciar sesión para ver tu perfil</h4>
      <router-link to="/" class="btn btn-primary mt-3">Volver al inicio</router-link>
    </div>
  </div>
</template>

<script setup>
import { useUser } from '../stores/user'

const { currentUser } = useUser()

const getInitials = () => {
  if (!currentUser.value) return ''
  const name = currentUser.value.displayName || currentUser.value.email
  return name.charAt(0).toUpperCase()
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>
