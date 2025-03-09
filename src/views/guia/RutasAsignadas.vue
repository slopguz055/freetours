
<script setup>
import { ref, onMounted, defineProps } from 'vue';
import { Modal } from 'bootstrap';
import {useRouter} from 'vue-router';

const router = useRouter();

const props = defineProps({
  usuario: {
    type: Object,
    required: false
  }
});

const rutas = ref([]);
const usuarioId = ref(null);
const rutaSeleccionada = ref(null);
let modalAsistentes = null;

const obtenerUsuarioId = async (email) => {
  try {
    const response = await fetch('http://localhost:8080/API_freetours/api.php/usuarios');
    const data = await response.json();
    const usuarioEncontrado = data.find(u => u.email === email);
    return usuarioEncontrado ? usuarioEncontrado.id : null;
  } catch (error) {
    console.error('Error al obtener la ID del usuario:', error);
    return null;
  }
};

const obtenerRutasAsignadas = async (id) => {
  if (!id) return;
  try {
    const response = await fetch(`http://localhost:8080/API_freetours/api.php/asignaciones?guia_id=${id}`);
    const data = await response.json();
    const hoy = new Date().toISOString().split('T')[0];
    rutas.value = data.filter(ruta => ruta.ruta_fecha >= hoy)
                      .sort((a, b) => new Date(a.ruta_fecha) - new Date(b.ruta_fecha));
  } catch (error) {
    console.error('Error al obtener las rutas:', error);
  }
};

const abrirModal = (ruta) => {
  rutaSeleccionada.value = ruta;
  modalAsistentes.show();
};

const cerrarModal = () => {
  modalAsistentes.hide();
};

onMounted(async () => {
  let usuario = JSON.parse(localStorage.getItem('usuario')||null);
  if (usuario == null || usuario.rol != 'guia') {
    router.push('/');
  }
  
  usuarioId.value = await obtenerUsuarioId(props.usuario.email);
  await obtenerRutasAsignadas(usuarioId.value);
  modalAsistentes = new Modal(document.getElementById('asistentesModal'));
});
</script>




<template>
  <div>
    <div class="bg-red text-white py-2 w-100">
        <h1 class="text-center my-0">Rutas asignadas</h1>
      </div>    <div v-if="rutas.length === 0">
      <h2 class="h4 text-muted">Actualmente no tienes rutas asignadas</h2>
    </div>
    <div v-else>
      <div v-for="ruta in rutas" :key="ruta.ruta_id" class="card my-5 mx-auto border-danger shadow container">
        <div class="row g-0">
          <div class="col-md-4 d-flex align-items-center justify-content-center p-3">
            <img :src="ruta.ruta_foto" alt="Foto de la ruta" class="img-fluid rounded fotoruta">
          </div>
          <div class="col-md-8">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <p class="card-text fs-4 fw-bold">{{ ruta.ruta_titulo }}</p>
                <p class="card-text fs-5 fw-semibold bg-danger text-white p-2 ms-auto"><i class="bi bi-geo-alt"></i> {{ ruta.ruta_localidad }}</p>
              </div>
              <hr class="my-2">
              <p class="card-text">DESCRIPCIÓN: {{ ruta.ruta_descripcion }}</p>
              <hr class="my-2">
              <div class="d-flex justify-content-between align-items-center">
                <p class="card-text mb-0"><i class="bi bi-calendar-event"></i> FECHA: {{ ruta.ruta_fecha }} a las {{ ruta.ruta_hora.slice(0,5) }}</p>
              </div>
              <hr class="my-2">
              <div>
                <h6>Asistentes:</h6>
                <ul>
                  <li v-if="ruta.reservas.length === 0">Aún no hay reservas para esta ruta</li>
                  <li v-for="reserva in ruta.reservas" :key="reserva.reserva_id">
                    {{ reserva.cliente.nombre }} - {{ reserva.num_personas }} asistentes
                  </li>
                </ul>
              </div>
              <div v-if="ruta.reservas.length != 0">
                <button class="btn btn-lg btn-dark" @click="abrirModal(ruta)">Pasar lista</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal asistentes -->
    <div class="modal fade" id="asistentesModal" tabindex="-1" aria-labelledby="asistentesModalLabel" aria-hidden="true" ref="modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="asistentesModalLabel">Lista de Asistentes</h5>
            <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <ul>
              <li v-for="reserva in rutaSeleccionada?.reservas" :key="reserva.reserva_id">
                {{ reserva.cliente.nombre }} - {{ reserva.num_personas }} asistentes
              </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="cerrarModal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>





<style scoped>
ul {
  list-style-type: none;
  padding: 0;
}

li {
  margin-bottom: 0.5em;
}
</style>