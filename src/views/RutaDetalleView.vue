<script setup lang="ts">
import { ref, onMounted, defineProps, defineEmits } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import Swal from 'sweetalert2';

// Define props
const props = defineProps({
  id: {
    type: String,
    required: true
  },
  usuario: {
    type: Object,
    required: false,
    default: () => ({})
  }
});

const emits = defineEmits(['sesionIniciada']);

const route = useRoute();
const router = useRouter();

const ruta = ref(null);
const map = ref(null);
const marker = ref(null);
const numReservas = ref(1);

const fetchRuta = async (id) => {
  try {
    const response = await fetch(`http://localhost:8080/API_freetours/api.php/rutas`);
    const data = await response.json();
    ruta.value = data.find(ruta => ruta.id === id);
  } catch (error) {
    console.error('Error:', error);
  }
};

const realizarReserva = async () => {
  if (numReservas.value < 1 || numReservas.value > 8) {

    Swal.fire({
      title: "Número de reservas inválido",
      text: "El número de asistentes no puede ser menor que 1 ni mayor que 8.",
      icon: "error",
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#fd4854"
    });

    return;
  }


  const reservaData = {
    email: props.usuario.email, // Email del usuario autenticado
    ruta_id: ruta.value.id,
    num_personas: numReservas.value
  };

  try {
    const response = await fetch('http://localhost:8080/API_freetours/api.php/reservas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(reservaData)
    });

    if (!response.ok) {
      throw new Error('Error al realizar la reserva');
    }

    const data = await response.json();
    console.log("Reserva confirmada:", data);
    
    Swal.fire({
      title: "Reserva confirmada",
      text: "Has reservado tu plaza con éxito",
      icon: "success",
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#fd4854"
    });

  } catch (error) {
    console.error('Error:', error);
  }
};

const irRegistro = () => {
  router.push('/registro');
};

onMounted(() => {
  const id = route.params.id;
  // Una vez obtenemos el ID de la ruta, cargamos el mapa con los datos recibidos
  fetchRuta(id).then(() => {
    if (ruta.value?.latitud && ruta.value?.longitud) {
      map.value = L.map('map').setView([ruta.value.latitud, ruta.value.longitud], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map.value);
      marker.value = L.marker([ruta.value.latitud, ruta.value.longitud]).addTo(map.value);
    }
  });
});

const fechaFormateada = (dateString) => {
  const [year, month, day] = dateString.split('-');
  return `${day}-${month}-${year}`;
};
</script>

<template>
  <div v-if="ruta">
    <div class="bg-red text-white py-2 w-100">
        <h1 class="text-center my-0">{{ ruta.titulo }}
</h1>
      </div>
    <div class="my-4 container">  
      <img :src="ruta.foto" alt="Foto de la ruta" class="img-fluid rounded img-30vh" style="width: 100%; object-fit: cover;">
      <div class="row mt-4">
        <div class="col-md-6">
          <p><strong>Localidad:</strong> {{ ruta.localidad }}</p>
          <p><strong>Descripción:</strong> {{ ruta.descripcion }}</p>
          <p><strong>Fecha:</strong> {{ fechaFormateada(ruta.fecha) }} a las {{ ruta.hora.slice(0, 5) }}</p>
          <p><strong>Guía:</strong> {{ ruta.guia_nombre || 'Guía no disponible' }}</p>
          <div class="mt-3">
            <h2>¿Te interesa?</h2>
            <button v-if="props.usuario?.email && props.usuario?.rol === 'cliente'" 
                    class="btn btn-danger btn-xl mt-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#reservaModal">
              Reserva ahora
            </button>
            <button v-else class="btn btn-secondary btn-xl mt-2" @click="irRegistro">
              Regístrate para reservar
            </button>
          </div>
        </div>
        <div class="col-md-6">
          <div v-if="ruta.latitud && ruta.longitud" id="map" style="height: 400px;"></div>
        </div>
      </div>
    </div>
  </div>
  <div v-else>
    <p class="text-center">Cargando...</p>
  </div>

  <!-- Modal para reservar -->
  <div class="modal fade" id="reservaModal" tabindex="-1" aria-labelledby="reservaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reservaModalLabel">Realizar Reserva</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="numReservasModal" class="form-label">Número de reservas</label>
            <input type="number" class="form-control" id="numReservasModal" v-model="numReservas" min="1" max="8" required>          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-danger" @click="realizarReserva" data-bs-dismiss="modal">
            Confirmar Reserva
          </button>
        </div>
      </div>
    </div>
  </div>
</template>




<style scoped>

.img-30vh {
  height: 30vh;
}
.btn-xl {
  padding: 1rem 2rem;
  font-size: 1.5rem;
}
</style>
