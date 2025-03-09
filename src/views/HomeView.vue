<script setup lang="ts">
import Carrusel from '../components/Carrusel.vue';
import Buscador from '../components/Buscador.vue';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const rutas = ref([]);
const filteredRutas = ref([]);
const isLoading = ref(true);


const props = defineProps({
  usuario: Object
});

const emits = defineEmits(['sesionIniciada']);

const fetchRutas = async () => {
  try {
    const response = await fetch('http://localhost:8080/API_freetours/api.php/rutas', {
      method: 'GET',
    });
    const data = await response.json();
    const hoy = new Date().toISOString().split('T')[0];
    // Filtramos las fechas futuras
    rutas.value = data.filter(ruta => ruta.fecha >= hoy);
    filteredRutas.value = rutas.value;
  } catch (error) {
    console.error('Error:', error);
  } finally {
    // Cuando termina el fetch, cambia el estado de isLoading
    isLoading.value = false;
  }
};

// Muestra el formato de la fecha como dd-mm-aaaa
const fechaFormateada = (dateString) => {
  const [year, month, day] = dateString.split('-');
  return `${day}-${month}-${year}`;
};

const mostrarMasInfo = (id) => {
  router.push({ name: 'ruta-detalle', params: { id } });
};

// Filtro para el emit del buscador
const filterRutas = ({ localidad, fecha }) => {
  filteredRutas.value = rutas.value.filter(ruta => {
    return (!localidad || ruta.localidad.includes(localidad)) && (!fecha || ruta.fecha === fecha);
  });
};

onMounted(() => {
  fetchRutas();
});
</script>





<template>
  <Carrusel />
  <div v-if="props.usuario?.rol === 'cliente' || props.usuario === null">
    <div>
      <div class="bg-red text-white py-2 w-100">
        <h1 class="text-center">Listado de Rutas</h1>
      </div>
      <Buscador @filter="filterRutas" />
      <div class="container">
        <div>
          <div class="d-flex justify-content-center align-items-center" v-if="isLoading">
            <div class="spinner-border text-danger" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
          <!-- Si hay rutas que mostrar -->
          <div v-else-if="filteredRutas.length">
            <div v-for="ruta in filteredRutas" :key="ruta.id" class="card my-5 mx-auto border-danger shadow" style="width: 80%; border-width: 2px;">
              <div class="row g-0">
                <div class="col-md-4 d-flex align-items-center justify-content-center p-3">
                  <img :src="ruta.foto" alt="Foto de la ruta" class="img-fluid rounded" style="height: 100%; object-fit: cover;">
                </div>
                <div class="col-md-8">
                  <div class="card-body">
                    <h5 class="card-title">{{ ruta.nombre }}</h5>
                    <div class="d-flex justify-content-between">
                      <p class="card-text fs-4 fw-bold">{{ ruta.titulo }}</p>
                      <p class="card-text fs-5 fw-semibold bg-red text-white p-2 ms-auto">
                        <i class="bi bi-geo-alt"></i> {{ ruta.localidad }}
                      </p>          
                    </div>
                    <hr class="my-2">
                    <p class="card-text">Descripción: {{ ruta.descripcion }}</p>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                      <p class="card-text mb-0">
                        <i class="bi bi-calendar-event"></i> FECHA: {{ fechaFormateada(ruta.fecha) }} a las {{ ruta.hora.slice(0,5) }}
                      </p>
                        <button class="btn btn-lg btn-black ms-auto me-2" @click="mostrarMasInfo(ruta.id)">Más info</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Si no existen rutas futuras o si no hay coincidencias con el filtro del buscador -->
          <div v-else>
            <p class="text-center">No se han encontrado rutas 😞</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-if="props.usuario?.rol === 'admin'" class="d-flex justify-content-center align-items-center my-5">
    <div class="card text-center shadow" style="width: 50%;">
      <div class="card-body">
        <h1 class="card-title">Bienvenido/a al panel de administrador</h1>
      </div>
    </div>
  </div>

  <div v-if="props.usuario?.rol === 'guia'" class="d-flex justify-content-center align-items-center my-5">
    <div class="card text-center shadow" style="width: 50%;">
      <div class="card-body">
        <h1 class="card-title">Bienvenido/a al panel de guías</h1>
      </div>
    </div>
  </div>

</template>




<style scoped>
.btn-black {
  border: 3px solid #ffffff;
  background-color: black;
  color: white;
}

.btn-black:hover {
  border: 3px solid #fd4854;
  background-color: black;
  color: white;
}
</style>