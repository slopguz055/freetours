<template>
    <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="../assets/carrusel/carrusel_foto.jpg" class="d-block w-100" alt="Imagen 1">
        </div>
        <div class="carousel-item">
          <img src="../assets/carrusel/carrusel_palacio.jpg" class="d-block w-100" alt="Imagen 2">
        </div>
        <div class="carousel-item">
          <img src="../assets/carrusel/carrusel_men.jpg" class="d-block w-100" alt="Imagen 3">
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  // No se necesita lógica adicional para este carrusel
  </script>
  
  <style scoped>
  .carousel-item img {
    height: 400px; /* Ajusta la altura de las imágenes según tus necesidades */
    object-fit: cover;
  }
  </style>