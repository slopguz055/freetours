<template>
  <footer class="footer bg-dark text-white text-center">
    <p>&copy; {{ new Date().getFullYear() }} - Todos los derechos reservados.</p>
  </footer>
</template>

<style scoped>
.footer {
  position: fixed;
  bottom: 0;
  width: 100%;
  padding: 0.3rem;
  z-index: 2;
}
</style>