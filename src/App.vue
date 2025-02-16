<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Header from './components/Header.vue';
import NavBar from './components/NavBar.vue';
import Footer from './components/Footer.vue'; // Importa el componente Footer

const usuario = ref(null);

const handleSesionIniciada = (user) => {
  usuario.value = user;
};

const logout = () => {
  usuario.value = null;
  // Aquí puedes agregar la lógica para cerrar sesión, como eliminar tokens, etc.
};

</script>

<template>
  <Header :usuario="usuario" @logout="logout" />
  <router-view @sesionIniciada="handleSesionIniciada" />
  <Footer />
</template>