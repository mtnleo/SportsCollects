<template>
  <div class="modal fade" ref="modalEl" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isLogin ? 'Iniciar Sesión' : 'Registrarse' }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="handleSubmit">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" v-model="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input type="password" class="form-control" v-model="password" required>
            </div>
            <div v-if="!isLogin" class="mb-3">
              <label class="form-label">Nombre Completo</label>
              <input type="text" class="form-control" v-model="nombre">
            </div>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>
            <button type="submit" class="btn btn-primary w-100">
              {{ isLogin ? 'Iniciar Sesión' : 'Registrarse' }}
            </button>
          </form>
          <div class="text-center mt-3">
            <a href="#" @click.prevent="isLogin = !isLogin">
              {{ isLogin ? '¿No tienes cuenta? Regístrate' : '¿Ya tienes cuenta? Inicia sesión' }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { signInWithEmailAndPassword, createUserWithEmailAndPassword, updateProfile } from 'firebase/auth'
import { auth } from '../firebase'
import { Modal } from 'bootstrap'

const modalEl = ref(null)
let modalInstance = null

const isLogin = ref(true)
const email = ref('')
const password = ref('')
const nombre = ref('')
const error = ref('')

onMounted(() => {
  modalInstance = new Modal(modalEl.value)
})

const show = () => {
  modalInstance.show()
}

const hide = () => {
  modalInstance.hide()
}

const handleSubmit = async () => {
  error.value = ''
  try {
    if (isLogin.value) {
      await signInWithEmailAndPassword(auth, email.value, password.value)
    } else {
      const userCredential = await createUserWithEmailAndPassword(auth, email.value, password.value)
      if (nombre.value) {
        await updateProfile(userCredential.user, { displayName: nombre.value })
      }
    }
    hide()
    email.value = ''
    password.value = ''
    nombre.value = ''
  } catch (err) {
    error.value = err.message
  }
}

defineExpose({ show, hide })
</script>
