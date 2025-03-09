<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import Swal from 'sweetalert2';

const router = useRouter();
// Definición del objeto 'ruta' con sus propiedades iniciales
const ruta = ref({
  titulo: '',
  localidad: '',
  descripcion: '',
  foto: '',
  fecha: '',
  hora: '',
  latitud: '',
  longitud: '',
  guia_id: ''
});

// Referencias para el mapa y el marcador
const map = ref(null);
const marker = ref(null);

onMounted(() => {
  // Redirección de usuarios no autenticados como administradores
  let usuario = JSON.parse(localStorage.getItem('usuario')) || null;
  if(usuario == null ||usuario.rol != "admin"){
    router.push('/');
  }


  // Inicializa el mapa centrado en Madrid con un zoom de 13
  map.value = L.map('map').setView([40.4168, -3.7038], 13);
  // Añade una capa de mosaico de OpenStreetMap al mapa
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map.value);

  // Inicializa el marcador
  marker.value = L.marker();

  // Evento para actualizar la posición del marcador y las coordenadas de la ruta al hacer clic en el mapa
  map.value.on('click', (e) => {
    marker.value.setLatLng(e.latlng).addTo(map.value);
    ruta.value.latitud = e.latlng.lat;
    ruta.value.longitud = e.latlng.lng;
  });
});

// Referencias para la dirección, mensaje flash y error de fecha
const address = ref('');
const flashMessage = ref('');
const errorFecha = ref('');

// Función para buscar una ubicación basada en la dirección ingresada
const searchLocation = async () => {
  if (!address.value) return;
  const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address.value)}`);
  const data = await response.json();
  if (data.length > 0) {
    const { lat, lon } = data[0];
    marker.value.setLatLng([lat, lon]).addTo(map.value).bindPopup(address.value).openPopup();
    map.value.setView([lat, lon], 13);
    ruta.value.latitud = lat;
    ruta.value.longitud = lon;
  } else {
    alert('Dirección no encontrada');
  }
};

const validarFecha = () => {
  if (new Date(ruta.value.fecha) < new Date(minDate.value)) {
    errorFecha.value = 'La fecha no puede ser anterior al día actual';
  } else {
    errorFecha.value = '';
  }
};

const guardarRuta = async () => {
  validarFecha();
  if (errorFecha.value) return;

  const rutaData = { ...ruta.value };

  try {
    const response = await fetch('http://localhost:8080/API_freetours/api.php/rutas', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(rutaData)
    });

    if (response.ok) {

      Swal.fire({
        position: "center",
        icon: "success",
        title: "Ruta creada con éxito.",
        showConfirmButton: false,
        timer: 3000
      });
      ruta.value = { titulo: '', localidad: '', descripcion: '', foto: '', fecha: '', hora: '', latitud: '', longitud: '', guia_id: '' };
    } else {
      throw new Error('Error al guardar la ruta');
    }
  } catch (error) {
    console.error(error);
    flashMessage.value = 'Error al guardar la ruta';
    setTimeout(() => { flashMessage.value = ''; }, 3000);
  }
};

// Lista de guías
const guias = ref([]);

// Preparamos la lista de guías disponibles para una fecha concreta
const fetchGuias = async (fecha) => {
  try {
    const response = await fetch(`http://localhost:8080/API_freetours/api.php/asignaciones?fecha=${fecha}`);
    const data = await response.json();
    guias.value = data;
  } catch (error) {
    console.error('Error al obtener los guías:', error);
  }
};

// Actualizr la lista de guías cuando cambia la fecha
watch(() => ruta.value.fecha, (newFecha) => {
  validarFecha();
  if (newFecha) {
    fetchGuias(newFecha);
  } else {
    guias.value = [];
  }
});

// Definición de la fecha mínima permitida (el día actual)
const minDate = ref(new Date().toISOString().split('T')[0]);

const isFormValid = computed(() => {
  return ruta.value.titulo && ruta.value.localidad && ruta.value.descripcion && ruta.value.foto && ruta.value.fecha && ruta.value.hora && ruta.value.latitud && ruta.value.longitud;
});
</script>

<template>
  <div class="bg-red text-white py-2 w-100">
    <h1 class="text-center">Crear nueva ruta</h1>
  </div>
  <div class="container">
    <form class="border rounded bg-light p-4" @submit.prevent="guardarRuta">
      <fieldset class="mb-4">
        <legend>Información general</legend>
        <div class="mb-3">
          <label for="titulo" class="form-label">Título de la ruta</label>
          <input type="text" id="titulo" v-model="ruta.titulo" class="form-control" required placeholder="Introduce el título de la ruta">
        </div>
        <div class="mb-3">
          <label for="localidad" class="form-label">Localidad de la ruta</label>
          <input type="text" id="localidad" v-model="ruta.localidad" class="form-control" required placeholder="Introduce la localidad de la ruta">
        </div>
        <div class="mb-3">
          <label for="descripcion" class="form-label">Descripción</label>
          <textarea id="descripcion" v-model="ruta.descripcion" class="form-control" required placeholder="Introduce la descripción de la ruta"></textarea>
        </div>
        <div class="mb-3">
          <label for="foto" class="form-label">Foto (URL)</label>
          <input type="input" id="foto" v-model="ruta.foto" class="form-control" placeholder="Introduce la URL de la foto de la ruta">
        </div>
      </fieldset>
      <fieldset class="mb-4">
        <legend>Detalles de la ruta</legend>
        <div class="mb-3">
          <label for="fecha" class="form-label">Fecha</label>
          <input type="date" id="fecha" v-model="ruta.fecha" class="form-control" required :min="minDate" :class="{ 'is-invalid': errorFecha }">
          <div v-if="errorFecha" class="invalid-feedback">{{ errorFecha }}</div>
        </div>
        <div class="mb-3">
          <label for="hora" class="form-label">Hora</label>
          <input type="time" id="hora" v-model="ruta.hora" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="hora" class="form-label">Punto de partida</label>
          <input v-model="address" @keydown.enter.prevent="searchLocation" placeholder="Buscar dirección" class="form-control mb-3" />
          <div id="map"></div>
        </div>
        <div class="mb-3">
          <label for="guia_id" class="form-label">Guía (opcional)</label>
          <select id="guia_id" v-model="ruta.guia_id" class="form-control">
            <option v-if="!ruta.fecha" disabled>Introduce una fecha para ver disponibilidad de guías</option>
            <option v-for="guia in guias" :key="guia.id" :value="guia.id">{{ guia.nombre }}</option>
          </select>
        </div>
      </fieldset>
      <button type="submit" class="btn btn-dark" :disabled="!isFormValid" aria-label="Botón para crear nueva ruta">Guardar</button>
    </form>
  </div>
</template>

<style scoped>
.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
}

#map {
  height: 400px;
  width: 100%;
  z-index: 1;
}
</style>