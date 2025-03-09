<template>
    <div class="buscador-container d-flex align-items-center mb-3 justify-content-center mt-3">
      <div class="w-75">
        <input
          type="text"
          v-model="inputLocalidad"
          placeholder="Buscar por localidad"
          class="form-control me-2"
          aria-label="Buscar por localidad"        />
      </div>
      <div class="w-25">
        <input type="date" v-model="fecha" class="form-control me-2" />
      </div>
      <button @click="emitFilter" class="btn btn-dark mx-2" aria-label="Botón de búsqueda"><i class="bi bi-search"></i></button>
    </div>
  </template>
  
  <script setup>
    import { ref } from 'vue';
    import { defineEmits } from 'vue';
  
    const emit = defineEmits(['filter']);
    const inputLocalidad = ref('');
    const fecha = ref('');
  
    const emitFilter = () => {
      emit('filter', { localidad: inputLocalidad.value, fecha: fecha.value });
    };
  </script>
  
  <style scoped>
    .buscador-container {
      width: 80%;
      max-width: 800px;
      margin: 0 auto;
    }
  
  </style>