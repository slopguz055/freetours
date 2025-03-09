
<script setup>
import { ref, onUnmounted } from 'vue';
import Breadcrumb from '../components/Breadcrumb.vue';


const medio = ref(null);
const playButtonLabel = ref('▶️');
const silenciarButtonLabel = ref('🔉');
const videoLoaded = ref(false);



// Acciones del vídeo
const accionPlay = () => {
  if (!medio.value.paused && !medio.value.ended) {
    medio.value.pause();
    playButtonLabel.value = '▶️';
    document.body.style.backgroundColor = '#fff';
  } else {
    medio.value.play();
    playButtonLabel.value = '||';
    document.body.style.backgroundColor = 'grey';
  }
};

const accionReiniciar = () => {
  medio.value.currentTime = 0;
  medio.value.play();
  playButtonLabel.value = '||';
};

const accionRetrasar = () => {
  if (medio.value.currentTime > 2) {
    medio.value.currentTime -= 2;
  } else {
    medio.value.currentTime = 0;
  }
};

const accionAdelantar = () => {
  if (medio.value.duration - medio.value.currentTime > 2) {
    medio.value.currentTime += 2;
  } else {
    medio.value.currentTime = medio.value.duration;
  }
};

const accionSilenciar = () => {
  if (medio.value.muted) {
    medio.value.muted = false;
    silenciarButtonLabel.value = '🔉';
  } else {
    medio.value.muted = true;
    silenciarButtonLabel.value = '🔇';
  }
};

const accionMasVolumen = () => {
  medio.value.volume = Math.min(medio.value.volume + 0.1, 1);
};

const accionMenosVolumen = () => {
  medio.value.volume = Math.max(medio.value.volume - 0.1, 0);
};

onUnmounted(() => { // Cambio del fondo al salir
  document.body.style.backgroundColor = '#fff';
});

// Prueba breadcrumbs
const breadcrumbItems = ref([
  {
    text: 'Index',
    href: '/'
  },
  {
    text: 'Sobre nosotros',
    href: '/about'
  }
]);
</script>



<template>
  <div class="my-1 bg-light p-1">
    <Breadcrumb :items="breadcrumbItems" />
  </div>
  <div class="bg-red text-white py-2 w-100">
    <h1 class="text-center my-0">Sobre nosotros</h1>
  </div>
  <div class="container text-center my-4">
    <div class="video-container position-relative">
      <div class="loading-message position-absolute top-50 start-50 translate-middle" v-show="!videoLoaded">
        Cargando vídeo...
      </div>
      <video ref="medio" width="720" height="400" @loadeddata="videoLoaded = true" v-show="videoLoaded" class="w-100" aria-label="Video sobre nosotros">
        <source src="../assets/multimedia/turismo.mp4" type="video/mp4">
      </video>
      <nav v-show="videoLoaded" class="d-flex justify-content-center gap-2 mt-3">
        <input type="button" @click="accionReiniciar" value="⏮️" class="btn btn-secondary" aria-label="Reiniciar">
        <input type="button" @click="accionRetrasar" value="⏪" class="btn btn-secondary" aria-label="Retrasar">
        <input type="button" @click="accionPlay" :value="playButtonLabel" class="btn btn-secondary" :aria-label="playButtonLabel === '▶️' ? 'Reproducir' : 'Pausar'">
        <input type="button" @click="accionAdelantar" value="⏩" class="btn btn-secondary" aria-label="Adelantar">
        <input type="button" @click="accionSilenciar" :value="silenciarButtonLabel" class="btn btn-secondary" :aria-label="silenciarButtonLabel === '🔉' ? 'Silenciar' : 'Activar sonido'">
        <label class="form-label" for="volumen">Volumen</label>
        <input type="button" @click="accionMenosVolumen" value="➖" class="btn btn-dark" aria-label="Disminuir volumen">
        <input type="button" @click="accionMasVolumen" value="➕" class="btn btn-dark" aria-label="Aumentar volumen">
      </nav>
    </div>
  </div>
</template>





<style scoped>
</style>