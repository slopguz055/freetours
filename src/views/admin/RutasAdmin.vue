<script setup>
import { ref, onMounted } from 'vue';
import { Modal } from 'bootstrap';
import {useRouter} from 'vue-router';
import Swal from 'sweetalert2';

const router = useRouter();

// Definición de variables reactivas
const rutas = ref([]); // Lista de rutas

const selectedRutaId = ref(null); // ID de la ruta seleccionada para modificar guía
const selectedGuiaId = ref(''); // ID del guía seleccionado en el modal
const guias = ref([]); // Lista de guías disponibles
const isLoading = ref(true); // Estado de carga

const duplicateRuta = ref({
  fecha: '',
  hora: '',
  guia_id: ''
}); // Datos de la ruta a duplicar

// Función para obtener la lista de rutas
const fetchRutas = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('http://localhost:8080/API_freetours/api.php/rutas', {
      method: 'GET',
    });
    const data = await response.json();
    const today = new Date().toISOString().split('T')[0];
    rutas.value = data.filter(ruta => ruta.fecha >= today);
    console.log("Rutas obtenidas:", rutas.value);
  } catch (error) {
    console.error('Error:', error);
  } finally {
    isLoading.value = false;
  }
};

// Función para obtener los guías disponibles para una fecha específica
const fetchGuiasDisponibles = async (fecha) => {
  try {
    const response = await fetch(`http://localhost:8080/API_freetours/api.php/asignaciones?fecha=${fecha}`, {
      method: 'GET',
    });
    const data = await response.json();
    guias.value = data;
  } catch (error) {
    console.error('Error:', error);
  }
};

// Función para formatear la fecha
const fechaFormateada = (dateString) => {
  const [year, month, day] = dateString.split('-');
  return `${day}-${month}-${year}`;
};

// Función para abrir el modal de modificar guía
const abrirModalModificarGuia = (id, guiaId, fecha) => {
  selectedRutaId.value = id;
  selectedGuiaId.value = guiaId;
  fetchGuiasDisponibles(fecha); // Obtener guías disponibles para la fecha de la ruta
  const modal = new Modal(document.getElementById('modalModificarGuia'));
  modal.show();
};

// Función para abrir el modal de duplicar ruta
const abrirModalDuplicarRuta = (ruta) => {
  duplicateRuta.value = {
    ...ruta,
    fecha: '',
    hora: '',
    guia_id: ''
  };
  const modal = new Modal(document.getElementById('modalDuplicarRuta'));
  modal.show();
};

// Función para guardar la guía seleccionada
const guardarGuia = async () => {
  const asignacionData = {
    ruta_id: selectedRutaId.value,
    guia_id: selectedGuiaId.value
  };

  try {
    const response = await fetch('http://localhost:8080/API_freetours/api.php/asignaciones', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(asignacionData)
    });

    if (response.ok) {
      fetchRutas(); 
      const modal = Modal.getInstance(document.getElementById('modalModificarGuia'));
      modal.hide();

      Swal.fire({
      title: "Guía asignado",
      text: "Has asignado al guía con éxito",
      icon: "success",
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#fd4854"
    });
    } else {
      console.error('Error al modificar la guía');
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

// Función para duplicar una ruta
const duplicarRuta = async () => {
  const nuevaRuta = { ...duplicateRuta.value, id: undefined }; // Crear una copia de la ruta sin el ID
  try {
    const response = await fetch('http://localhost:8080/API_freetours/api.php/rutas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(nuevaRuta)
    });

    if (response.ok) {
      fetchRutas(); // Actualizar la lista de rutas después de duplicar
      const modal = Modal.getInstance(document.getElementById('modalDuplicarRuta'));
      modal.hide();

      Swal.fire({
      title: "Ruta duplicada",
      text: "Has duplicado la ruta con éxito",
      icon: "success",
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#fd4854"
    });
    } else {
      console.error('Error al duplicar la ruta');
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

// Función para borrar una ruta
const borrarRuta = async (id) => {

  
  Swal.fire({
      title: "¿Estás seguro/a?",
      text: "Vas a eliminar la ruta seleccionada, esta acción no es revertible, ¿estás seguro/a de que deseas continuar?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      cancelButtonText: "Cancelar acción",
      confirmButtonText: "Sí, deseo cancelar y eliminar esta ruta."
    }).then(async (result) => { // Marcar la función como async
      if (result.isConfirmed) {
        try {
          const response = await fetch(`http://localhost:8080/API_freetours/api.php/rutas?id=${id}`, {
            method: 'DELETE',
          });

          if (response.ok) {
            fetchRutas(); // Actualizar la lista de rutas después de borrar
          } else {
            console.error('Error al borrar la ruta');
          }
        } catch (error) {
          console.error('Error:', error);
        }
        Swal.fire({
          title: "Ruta cancelada",
          text: "La ruta ha sido eliminada exitósamente.",
          icon: "success"
        });
      }
    });
};

// Ejecutar fetchRutas cuando el componente se monta
onMounted(() => {
  let usuario = JSON.parse(localStorage.getItem('usuario') || null);
  if(usuario == null || usuario.rol != "admin"){
    router.push('/');
  }
  fetchRutas();
});
</script>



<template>
  <div>
    <div class="bg-red text-white py-2 w-100">
        <h1 class="text-center my-0">Rutas pendientes</h1>
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
                <img :src="ruta.foto" alt="Foto de la ruta" class="img-fluid rounded" >
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
                <div v-if="!ruta.guia_id" class="alert alert-warning mt-2" role="alert">
                  ❗🚨No hay guía asignado🚨❗
                </div>
                <div v-else class="alert alert-info mt-2" role="alert">
                  Rutas asignada al guía: {{ ruta.guia_nombre }}
                </div>
                <div v-if="ruta.asistentes < 10" class="alert alert-danger mt-2" role="alert">
                  ⚠️ La ruta tiene menos de 10 asistentes ⚠️
                </div>
                <div class="d-flex justify-content-end mt-3">
                  <button class="btn btn-dark me-2" @click="abrirModalModificarGuia(ruta.id, ruta.guia_id, ruta.fecha)">Asignar Guía</button>
                  <button class="btn btn-dark me-2" @click="abrirModalDuplicarRuta(ruta)">Duplicar ruta</button>
                  <button class="btn btn-dark" @click="borrarRuta(ruta.id)">Cancelar ruta</button>
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

    <!-- Modal para modificar guía -->
    <div class="modal fade" id="modalModificarGuia" tabindex="-1" aria-labelledby="modalModificarGuiaLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalModificarGuiaLabel">Asignar Guía</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="guia_id" class="form-label">Guía</label>
              <select id="guia_id" v-model="selectedGuiaId" class="form-control">
                <option v-for="guia in guias" :key="guia.id" :value="guia.id">{{ guia.nombre }}</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" @click="guardarGuia">Guardar cambios</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para duplicar ruta -->
    <div class="modal fade" id="modalDuplicarRuta" tabindex="-1" aria-labelledby="modalDuplicarRutaLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalDuplicarRutaLabel">Duplicar Ruta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="fecha" class="form-label">Fecha</label>
              <input type="date" id="fecha" v-model="duplicateRuta.fecha" class="form-control" @change="fetchGuiasDisponibles(duplicateRuta.fecha)" required>
            </div>
            <div class="mb-3">
              <label for="hora" class="form-label">Hora</label>
              <input type="time" id="hora" v-model="duplicateRuta.hora" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="guia_id" class="form-label">Guía</label>
              <select id="guia_id" v-model="duplicateRuta.guia_id" class="form-control">
                <option v-for="guia in guias" :key="guia.id" :value="guia.id">{{ guia.nombre }}</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" @click="duplicarRuta">Guardar cambios</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>




<style scoped>
</style>