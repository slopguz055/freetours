
<script setup>
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import {useRouter} from 'vue-router';

const router = useRouter();
const props = defineProps({
  usuario: {
    type: Object,
    required: false
  }
});

const reservas = ref([]);

const fetchReservas = async () => {
  if (!props.usuario?.email) return;
  try {
    console.log('Fetching reservas for user:', props.usuario.email);
    const response = await fetch(`http://localhost:8080/API_freetours/api.php/reservas?email=${props.usuario.email}`, {
      method: 'GET',
    });
    const data = await response.json();
    console.log('Reservas fetched:', data);
    const hoy = new Date().toISOString().split('T')[0];
    reservas.value = data.filter(reserva => reserva.ruta_fecha >= hoy);
  } catch (error) {
    console.error('Error fetching reservas:', error);
  }
};

const modificarReserva = async (reserva) => {
  console.log(reserva.num_personas);
  if (reserva.num_personas < 1) {
    alert('El número de reservas no puede ser menor a 1.');
    return;
  }

  if (reserva.num_personas > 8) {
    alert('No puedes reservar más de 8 asistentes.');
    return;
  }

  try {
    console.log('Modifying reserva:', reserva);
    // Eliminar la reserva existente
    const deleteResponse = await fetch(`http://localhost:8080/API_freetours/api.php/reservas?id=${reserva.reserva_id}`, {
      method: 'DELETE',
    });

    if (!deleteResponse.ok) {
      throw new Error('Error al eliminar la reserva');
    }

    console.log('Reserva eliminada:', reserva.reserva_id);

    // Crear una nueva reserva con el número de plazas actualizado
    const nuevaReserva = {
      email: props.usuario.email,
      ruta_id: reserva.ruta_id,
      num_personas: reserva.num_personas
    };

    const createResponse = await fetch('http://localhost:8080/API_freetours/api.php/reservas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(nuevaReserva)
    });

    if (!createResponse.ok) {
      throw new Error('Error al crear la nueva reserva');
    }

    const data = await createResponse.json();

    Swal.fire({
      title: "Reserva actualizada",
      text: "El número de asistentes ha sido modificado con éxito.",
      icon: "success",
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#fd4854"
    });

    console.log('Nueva reserva creada:', data);
    fetchReservas(); // Actualiza la lista de reservas
  } catch (error) {
    console.error('Error modifying reserva:', error);
  }
};

const eliminarReserva = async (reservaId) => {
  try {
    console.log('Eliminando reserva:', reservaId);
    


    Swal.fire({
      title: "¿Estás seguro/a?",
      text: "Vas a eliminar la reserva seleccionada, esta acción no es revertible, ¿estás seguro/a de que deseas continuar?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      cancelButtonText: "Cancelar acción",
      confirmButtonText: "Sí, deseo eliminar la reserva"
    }).then(async (result) => {
      if (result.isConfirmed) {
        const response = await fetch(`http://localhost:8080/API_freetours/api.php/reservas?id=${reservaId}`, {
          method: 'DELETE',
        });

        if (!response.ok) {
          throw new Error('Error al eliminar la reserva');
        }

        const data = await response.json();
        console.log('Reserva eliminada:', data);
        fetchReservas(); 

        Swal.fire({
          title: "Reserva cancelada",
          text: "La reserva ha sido cancelada exitósamente.",
          icon: "success"
        });
      }
    });

  } catch (error) {
    console.error('Error eliminando reserva:', error);
  }
};

onMounted(() => {
  let usuario = JSON.parse(localStorage.getItem('usuario')|| null);
  if(usuario == null || usuario.rol != "cliente"){
    router.push('/');
  }
  fetchReservas();
});
</script>




<template>
  <div>
    <div class="bg-red text-white py-2 w-100">
        <h1 class="text-center my-0">Mis reservas</h1>
      </div>
    <div v-if="reservas.length === 0">
      <h2 class="h4 text-muted">Actualmente no has realizado ninguna ruta</h2>
    </div>
    <div v-else>
      <div v-for="reserva in reservas" :key="reserva.reserva_id" class="card my-5 mx-auto border-danger shadow card-90">
        <div class="row g-0">
          <div class="col-md-4 d-flex align-items-center justify-content-center p-3">
            <img :src="reserva.ruta_foto" alt="Foto de la ruta" class="img-fluid rounded fotoruta">
          </div>
          <div class="col-md-8">
            <div class="card-body">
              <h5 class="card-title">{{ reserva.ruta_nombre }}</h5>
              <div class="d-flex justify-content-between">
                <p class="card-text fs-4 fw-bold">{{ reserva.ruta_titulo }}</p>
                <p class="card-text fs-5 fw-semibold bg-danger text-white p-2 ms-auto"><i class="bi bi-geo-alt"></i> {{ reserva.ruta_localidad }}</p>
              </div>
              <hr class="my-2">
              <p class="card-text">DESCRIPCIÓN: {{ reserva.ruta_descripcion }}</p>
              <hr class="my-2">
              <div class="d-flex justify-content-between align-items-center">
                <p class="card-text mb-0"><i class="bi bi-calendar-event"></i> FECHA: {{ reserva.ruta_fecha }} a las {{ reserva.ruta_hora.slice(0,5) }}</p>
              </div>
              <div class="mt-3">
                <label for="numPlazas" class="form-label">Modificar número de plazas:</label>
                <input type="number" v-model="reserva.num_personas" min="1" max="8" class="form-control d-inline-block w-auto" />
                <button @click="modificarReserva(reserva)" class="btn btn-danger ms-2">Guardar cambios</button>
                <button @click="eliminarReserva(reserva.reserva_id)" class="btn btn-dark ms-2">Cancelar ruta</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>





<style scoped>
</style>
