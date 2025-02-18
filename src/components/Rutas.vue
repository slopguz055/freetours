<template>
    <div class="container">
      <h2>Crear Nueva Ruta</h2>
      <form @submit.prevent="guardarRuta">
        <div class="mb-3">
          <label for="titulo" class="form-label">Título de la ruta</label>
          <input type="text" id="titulo" v-model="ruta.titulo" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="localidad" class="form-label">Localidad de la ruta</label>
          <input type="text" id="localidad" v-model="ruta.localidad" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="descripcion" class="form-label">Descripción de la ruta</label>
          <textarea id="descripcion" v-model="ruta.descripcion" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
          <label for="foto" class="form-label">Foto general de la ruta</label>
          <input type="file" id="foto" @change="onFileChange" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="fecha" class="form-label">Fecha</label>
          <input type="date" id="fecha" v-model="ruta.fecha" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="hora" class="form-label">Hora</label>
          <input type="time" id="hora" v-model="ruta.hora" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Ruta</button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const ruta = ref({
    titulo: '',
    localidad: '',
    descripcion: '',
    foto: null,
    fecha: '',
    hora: ''
  });
  
  const onFileChange = (event) => {
    const file = event.target.files[0];
    ruta.value.foto = file;
  };
  
  const guardarRuta = async () => {
    try {
      const formData = new FormData();
      formData.append('titulo', ruta.value.titulo);
      formData.append('localidad', ruta.value.localidad);
      formData.append('descripcion', ruta.value.descripcion);
      formData.append('foto', ruta.value.foto);
      formData.append('fecha', ruta.value.fecha);
      formData.append('hora', ruta.value.hora);
  
      const response = await fetch('http://localhost:8080/API_freetours/api.php/rutas', {
        method: 'POST',
        body: formData
      });
  
      // Verificar el estado de la respuesta
      if (!response.ok) {
        const errorText = await response.text();
        console.error('Error en la respuesta del servidor:', errorText);
        throw new Error('Error en la respuesta del servidor');
      }
  
      const data = await response.json();
      console.log('Respuesta:', data);
  
      // Limpiar el formulario después de guardar
      ruta.value = {
        titulo: '',
        localidad: '',
        descripcion: '',
        foto: null,
        fecha: '',
        hora: ''
      };
    } catch (error) {
      console.error('Error al guardar la ruta:', error);
    }
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .form-label {
    font-weight: bold;
  }
  
  .btn-primary {
    width: 100%;
  }
  </style>