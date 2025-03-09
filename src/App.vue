<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';

const usuario = ref(JSON.parse(localStorage.getItem('usuario'))||null);

const router = useRouter();

// Viene de "Login"
const handleSesionIniciada = (user) => {
 usuario.value = user;
 localStorage.setItem('usuario', JSON.stringify(user));
};


// Viene de "Header"
const logout = () => {
 usuario.value = null;
 router.push('/');
 localStorage.removeItem('usuario');
};


</script>

<template>
  <div id="iu" class="d-flex flex-column min-vh-100">
    <Header :usuario="usuario" @logout="logout" />
    <router-view :usuario="usuario" @sesionIniciada="handleSesionIniciada" />
    <Footer />
  </div>
</template>

<style scoped>
#iu {
  padding-bottom: 2rem; 
}
</style>