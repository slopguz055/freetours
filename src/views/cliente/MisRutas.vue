
<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';

const router = useRouter();

const props = defineProps({
  usuario: {
    type: Object,
    required: false
  }
});

const reservas = ref([]);

// Reservar del usuario autenticado
const fetchReservas = async () => {
  if (!props.usuario?.email) return;
  try {
    const response = await fetch(`http://localhost:8080/API_freetours/api.php/reservas?email=${props.usuario.email}`);
    const data = await response.json();
    const hoy = new Date().toISOString().split('T')[0];
    reservas.value = data
      .filter(reserva => reserva.ruta_fecha < hoy) // Filtramos las reservas pasadas
      .map(reserva => ({
        ...reserva,
        valoracion_temp: reserva.valoracion || 0, // Valoración temporal
        hover_temp: 0, // Hover temporal
        comentario_temp: '' // Comentario temporal
      }));
  } catch (error) {
    console.error('Error:', error);
  }
};

// Manejo de la valoración y y del hover

const setRating = (reserva, rating) => {
  reserva.valoracion_temp = rating;
};

const setHover = (reserva, rating) => {
  reserva.hover_temp = rating;
};

const resetHover = (reserva) => {
  reserva.hover_temp = 0;
};

// Enviar valoración
const valorarRuta = async (reserva) => {
  const nuevaValoracion = {
    user_id: parseInt(reserva.cliente_id, 10),
    ruta_id: parseInt(reserva.ruta_id, 10),
    estrellas: reserva.valoracion_temp,
    comentario: reserva.comentario_temp
  };

  try {
    const response = await fetch('http://localhost:8080/API_freetours/api.php/valoraciones', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(nuevaValoracion)
    });

    if (!response.ok) {
      throw new Error('Error al valorar la ruta');
    }

    Swal.fire({
      title: "Has enviado tu valoración",
      text: "¡Gracias por compartir tu opinión con el resto!",
      icon: "success",
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#fd4854"
    });

    fetchReservas();
  } catch (error) {
    console.error('Error valorando ruta:', error);
  }
};

onMounted(() => {
  let usuario = JSON.parse(localStorage.getItem('usuario')||null);
  if (usuario == null || usuario.rol != 'cliente') {
    router.push('/');
  }
  
  fetchReservas();
});
</script>



<template>
  <div>
    <div class="bg-red text-white py-2 w-100">
        <h1 class="text-center my-0">Mis rutas</h1>
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
              <p class="card-text">Descripción: {{ reserva.ruta_descripcion }}</p>
              <div class="d-flex justify-content-between align-items-center">
                <p class="card-text mb-0"><i class="bi bi-calendar-event"></i> FECHA: {{ reserva.ruta_fecha }} a las {{ reserva.ruta_hora.slice(0,5) }}</p>
              </div>
              <hr class="my-2">
              <div class="mt-3">
                <template v-if="reserva.valoracion">
                  <p>Tu valoración: {{ reserva.valoracion }} - 
                    <!-- Estrellas según la valoración -->
                    <span 
                      v-for="star in 5" 
                      :key="star" 
                      :class="{
                        'text-warning': star <= reserva.valoracion, // Estrella amarilla
                        'text-muted': star > reserva.valoracion // Estrella gris
                      }" 
                      class="fa fa-star">
                    </span>
                    - {{ reserva.comentario }}
                  </p>
                </template>
                <template v-else>
                  <label for="valoracion" class="form-label">Valorar la ruta:</label>
                  <div class="rating" @mouseleave="resetHover(reserva)">
                    <span v-for="star in 5" :key="star" @click="setRating(reserva, star)" @mouseover="setHover(reserva, star)" :class="{'text-warning': star <= (reserva.hover_temp || reserva.valoracion_temp), 'text-muted': star > (reserva.hover_temp || reserva.valoracion_temp)}" class="fa fa-star"></span>
                  </div>
                  <input v-model="reserva.comentario_temp" type="text" class="form-control mt-2" placeholder="Escribe un comentario (opcional)">
                  <button @click="valorarRuta(reserva)" class="btn btn-danger ms-2 mt-2" :disabled="reserva.valoracion_temp === 0">Enviar comentario</button>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>





<style scoped>
.rating .fa-star {
  cursor: pointer;
  font-size: 1.5em;
}
</style>