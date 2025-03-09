<script setup>
  import { ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  
  const router = useRouter();
  const rutas = ref([]);
  const isLoading = ref(true);
  const valoraciones = ref({});
  
  async function fetchValoraciones(rutaId) {
    try {
      const response = await fetch(`http://localhost:8080/API_freetours/api.php/valoraciones?ruta_id=${rutaId}`);
      if (!response.ok) {
        throw new Error('Error en la solicitud: ' + response.status);
      }
      const data = await response.json();
      console.log(`Valoraciones para la ruta ${rutaId}:`, data);
      // Procesar los datos según sea necesario
      return data;
    } catch (error) {
      console.error(`Error al obtener las valoraciones para la ruta ${rutaId}:`, error);
      return null;
    }
  }
  
  const fetchRutas = async () => {
    isLoading.value = true;
    try {
      const response = await fetch('http://localhost:8080/API_freetours/api.php/rutas', {
        method: 'GET',
      });
      const data = await response.json();
      const today = new Date().toISOString().split('T')[0];
      rutas.value = data.filter(ruta => ruta.fecha < today);
      console.log("Rutas obtenidas:", rutas.value);
  
      // Fetch valoraciones for each ruta
      for (const ruta of rutas.value) {
        const valoracion = await fetchValoraciones(ruta.id);
        if (valoracion && valoracion.length > 0) {
          const media = valoracion.reduce((acc, val) => acc + val.puntuacion, 0) / valoracion.length;
          valoraciones.value[ruta.id] = media.toFixed(2); // Redondear a 2 decimales
        } else {
          valoraciones.value[ruta.id] = null; // No hay valoraciones
        }
      }
    } catch (error) {
      console.error('Error:', error);
    } finally {
      isLoading.value = false;
    }
  };
  
  const fechaFormateada = (dateString) => {
    const [year, month, day] = dateString.split('-');
    return `${day}-${month}-${year}`;
  };
  
  onMounted(() => {
    let usuario = JSON.parse(localStorage.getItem('usuario') || null);
    if (usuario == null || usuario.rol != "admin") {
      router.push('/');
    }
    fetchRutas();
  });
</script>






<template>
  <div>
    <div class="bg-red text-white py-2 w-100">
      <h1 class="text-center my-0">Historial rutas</h1>
    </div>
    <div class="container">
      <div class="d-flex justify-content-center align-items-center" v-if="loading">
          <div class="spinner-border text-danger" role="status">
              <span class="sr-only">Loading...</span>
          </div>
      </div>
      <div v-else-if="rutas.length">
        <div v-for="ruta in rutas" :key="ruta.id" class="card mb-3 mt-3 mx-auto">
          <div class="row g-0">
            <div class="col-md-4 d-flex align-items-center justify-content-center p-3">
              <img :src="ruta.foto" alt="Foto de la ruta" class="img-fluid rounded">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <h5 class="card-title">{{ ruta.nombre }}</h5>
                <p class="card-text fs-5 fw-semibold">LOCALIDAD: {{ ruta.localidad }}</p>
                <p class="card-text fs-4 fw-bold">TÍTULO: {{ ruta.titulo }}</p>
                <hr class="my-2">
                <!--<p class="card-text">DESCRIPCIÓN: {{ ruta.descripcion }}</p>
                <hr class="my-2"> -->
                <p class="card-text mb-0">FECHA: {{ fechaFormateada(ruta.fecha) }} a las {{ ruta.hora.slice(0,5) }}</p>
                <hr class="my-2">
                <div v-if="!ruta.guia_id" class="alert alert-warning mt-1" role="alert">
                  <p>Guía no asignado.</p>
                </div>
                <div v-else>
                  <p>Rutas asignada al guía: {{ ruta.guia_nombre }}</p>
                  <p>Valoración media: {{ valoraciones[ruta.id] ? valoraciones[ruta.id] : 'Todavía no hay valoraciones' }}</p>
                  {{ console.log(valoraciones[ruta.id]) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <p class="text-center">No hay rutas disponibles</p>
      </div>
    </div>
  </div>
</template>





<style scoped>
</style>