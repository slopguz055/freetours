<script setup>
import { defineProps, defineEmits } from 'vue';
import { RouterLink } from 'vue-router';

const props = defineProps({
  usuario: Object
});

const emit = defineEmits(['logout']);

const logout = () => {
  emit('logout');
};
</script>

<template>
  <header class="header">
    <nav class="navbar navbar-expand navbar-light bg-light">
      <div class="container-fluid">
        <RouterLink to="/" class="navbar-brand">Mi Aplicación</RouterLink>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
            <RouterLink to="/users">Gestión de Usuarios</RouterLink>
            <RouterLink to="/crearEditarRutas">Crear/Editar Rutas</RouterLink>
          </ul>
        </div>
        <div class="d-flex align-items-center">
          <span v-if="usuario" class="me-3">Sesión iniciada: {{ usuario.email }}</span>
          <RouterLink v-if="!usuario" to="/login" class="btn btn-primary">Login</RouterLink>
          <button v-else @click="logout" class="btn btn-primary">Logout</button>
        </div>
      </div>
    </nav>
  </header>
</template>

<style scoped>
.header {
  width: 100%;
  background-color: #0c2743;
}
</style>