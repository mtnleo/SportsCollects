<template>
  <div id="app">
    <Navbar @open-auth="openAuthModal" />
    <router-view />
    <Footer />
    <AuthModal ref="authModal" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import AuthModal from './components/AuthModal.vue'

const authModal = ref(null)

const openAuthModal = () => {
  authModal.value.show()
}
</script>

<style>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.router-view {
  flex: 1;
}
</style>
