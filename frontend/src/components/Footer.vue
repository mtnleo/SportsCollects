<template>
  <footer class="bg-dark text-white py-4 mt-auto">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4 col-sm-12 d-flex justify-content-center align-items-center">
          <div class="footer-logo-wrapper mb-3">
            <img :src="brandLogo" alt="SportsCollects" class="footer-logo">
          </div>
        </div>
        <div class="col-md-4 col-sm-6">
          <h6>Enlaces</h6>
          <ul class="list-unstyled mb-0">
            <li class="mb-2"><router-link to="/" class="hover-link">Home</router-link></li>
            <li class="mb-2"><router-link to="/perfil" class="hover-link">Mi perfil</router-link></li>
            <li class="mb-2"><router-link to="/carrito" class="hover-link">Carrito</router-link></li>
            <li><router-link to="/vender" class="hover-link">Publicar artículo</router-link></li>
          </ul>
        </div>
        <div class="col-md-4 col-sm-6">
          <h6>Contacto</h6>
          <p class="footer-text mb-1">info@sportscollects.com</p>
          <p class="footer-text mb-1">Brickell Ave 1201, Miami, FL</p>
          <p class="footer-text mb-0">+1 (305) 555-9274</p>
        </div>
      </div>
      <hr class="bg-secondary my-3">
      <div class="text-center footer-text">
        <small>&copy; 2025 SportsCollects. Todos los derechos reservados.</small>
      </div>
    </div>
  </footer>
</template>

<script setup>
import logo from '../assets/logo.png'

const brandLogo = logo
</script>

<style scoped>
.hover-link {
  color: rgba(255, 255, 255, 0.85) !important;
}

.hover-link:hover {
  color: #fff !important;
  transition: color 0.2s;
}

.footer-text {
  color: rgba(255, 255, 255, 0.85);
}

.footer-logo-wrapper {
  height: 78px;
  overflow: hidden;
  width: fit-content;
  display: flex;
  justify-content: center;
}

.footer-logo {
  height: 156px;
  width: auto;
  display: block;
  margin-top: -42px;
}
</style>
