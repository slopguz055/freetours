<script setup>
import { ref } from 'vue';
import { defineProps, defineEmits } from 'vue';
import { RouterLink } from 'vue-router';

const props = defineProps({
  usuario: Object
});

const emit = defineEmits(['logout']);

const logout = () => {
  emit('logout');
};

import logo from '../assets/logo/logo.png';
import friturs from '../assets/logo/friturs.png';

// Controla si el menú está abierto o cerrado
const menuAbierto = ref(false);

// Cambia el estado del menú
const toggleMenu = () => {
  menuAbierto.value = !menuAbierto.value;
};
</script>

<template>
  <header class="header bg-primary">

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">

        <RouterLink to="/" class="navbar-brand">
          <img :src="logo" alt="Logo" class="logo ms-3">
          <img :src="friturs" alt="Friturs" class="logo ms-2">
        </RouterLink>

        <button class="navbar-toggler" type="button" @click="toggleMenu">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div :class="['collapse', 'navbar-collapse', { show: menuAbierto }]" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

            <template v-if="usuario?.rol === 'admin'">
              <li class="nav-item">
                <RouterLink to="/users" class="nav-link" @click="menuAbierto = false">Gestión de Usuarios</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink to="/crearRuta" class="nav-link" @click="menuAbierto = false">Crear Rutas</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink to="/rutasadmin" class="nav-link" @click="menuAbierto = false">Rutas pendientes</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink to="/historial" class="nav-link" @click="menuAbierto = false">Historial rutas</RouterLink>
              </li>
            </template>

            <template v-if="usuario?.rol === 'cliente'">
              <li class="nav-item">
                <RouterLink to="/misReservas" class="nav-link" @click="menuAbierto = false">Mis reservas</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink to="/misRutas" class="nav-link" @click="menuAbierto = false">Mis rutas</RouterLink>
              </li>
            </template>

            <template v-if="usuario?.rol === 'guia'">
              <li class="nav-item">
                <RouterLink to="/rutasAsignadas" class="nav-link" @click="menuAbierto = false">Rutas asignadas</RouterLink>
              </li>
            </template>

            <template v-if="usuario?.rol != 'admin' && usuario?.rol != 'guia'">
            <li class="nav-item">
              <RouterLink to="/about" class="nav-link" @click="menuAbierto = false">Sobre nosotros</RouterLink>
            </li>
            </template>
          </ul>
          <div class="d-flex align-items-center login-logout ms-auto">
            <span v-if="usuario" class="me-3">Sesión iniciada: {{ usuario.email }}</span>
            <RouterLink v-if="!usuario" to="/login" class="btn btn-dark">Login</RouterLink>
            <button v-else @click="logout" class="btn btn-dark">Logout</button>
          </div>
        </div>
      </div>
    </nav>
  </header>
</template>

<style scoped>
.header {
  width: 100%;
}

.logo {
  height: 40px; 
}

.nav-link {
  font-weight: bold; 
}

.nav-link:hover {
  color: #ed3440 !important;
}

@media (max-width: 992px) {
  .login-logout {
    width: 100%;
    justify-content: flex-end;
    margin-top: 10px;
  }
}
</style>