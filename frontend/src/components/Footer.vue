<template>
  <footer class="bg-dark text-white py-4 mt-auto">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4 col-sm-12">
          <h5>🏆 SportsCollects</h5>
          <p class="text-muted mb-0">El marketplace líder en coleccionables deportivos</p>
        </div>
        <div class="col-md-4 col-sm-6">
          <h6>Enlaces</h6>
          <ul class="list-unstyled mb-0">
            <li class="mb-2"><router-link to="/" class="text-muted text-decoration-none hover-link">Inicio</router-link></li>
            <li><router-link to="/vender" class="text-muted text-decoration-none hover-link">Vender</router-link></li>
          </ul>
        </div>
        <div class="col-md-4 col-sm-6">
          <h6>Contacto</h6>
          <p class="text-muted mb-0">info@sportscollects.com</p>
        </div>
      </div>
      <hr class="bg-secondary my-3">
      <div class="text-center text-muted">
        <small>&copy; 2025 SportsCollects. Todos los derechos reservados.</small>
      </div>
    </div>
  </footer>
</template>

<script setup>
</script>

<style scoped>
.hover-link:hover {
  color: #fff !important;
  transition: color 0.2s;
}
</style>
