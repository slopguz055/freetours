
<script setup>
import { ref, reactive, onMounted } from 'vue'
import { defineProps, toRefs } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
const router = useRouter();

const props = defineProps({
  ipserver: String
});

const { ipserver } = toRefs(props);
const showModalEditarUsuario = ref(false);

const cerrarModalEditarUsuario = () => {
  showModalEditarUsuario.value = false;
};

const loading = ref(true);
const users = ref([]);
const editingUser = reactive({
  id: null,
  nombre: '',
  email: '',
  rol: ''
});

const loadUsers = async () => {
  try {
    const response = await fetch(`${ipserver.value}`);
    if (!response.ok) {
      if (response.status === 404) {
        loading.value = false;
        throw new Error('Resource not found (404)');
      } else {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
    }
    const data = await response.json();
    users.value = data;
    loading.value = false;
  } catch (error) {
    console.error('Error loading users:', error);
  }
};

onMounted(() => {
  let usuario = JSON.parse(localStorage.getItem('usuario')) || null;
  if (usuario == null || usuario.rol != "admin") {
    router.push('/');
  }

  loadUsers();
});


// Función para editar un usuario existente
const editUser = (user) => {
  editingUser.id = user.id;
  editingUser.nombre = user.nombre;
  editingUser.email = user.email;
  editingUser.rol = user.rol;
  showModalEditarUsuario.value = true;
};



const saveChanges = async () => {
  try {
    const response = await fetch(`${ipserver.value}/api.php/usuarios?id=${editingUser.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ rol: editingUser.rol }) // Solo se actualiza el rol
    });
    const data = await response.json();
    const index = users.value.findIndex(user => user.id === data.id);
    users.value.splice(index, 1, data);
    showModalEditarUsuario.value = false;

    Swal.fire({
      title: "Rol de usuario editado",
      text: "Has actualizado el rol del usuario con éxito",
      icon: "success",
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#fd4854"
    });

    loadUsers(); // Actualiza la lista de usuarios
  } catch (error) {
    console.error('Error saving changes:', error);
  }
};



const deleteUser = async (userId) => {

  Swal.fire({
      title: "¿Estás seguro/a?",
      text: "Vas a eliminar el usuario seleccionado, esta acción no es revertible, ¿estás seguro/a de que deseas continuar?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      cancelButtonText: "Cancelar acción",
      confirmButtonText: "Sí, deseo eliminar el usuario."
    }).then(async (result) => { 
      if (result.isConfirmed) {
        
        try {
          await fetch(`${ipserver.value}/api.php/usuarios?id=${userId}`, {
            method: 'DELETE'
          });
          users.value = users.value.filter((user) => user.id !== userId);
          loadUsers();
        
          Swal.fire({
          title: "Usuario eliminado",
          text: "El usuario ha sido eliminado exitósamente.",
          icon: "success"
        });
        } catch (error) {
          console.error('Error deleting user:', error);
        }

      }
    });

};
</script>






<template>
  <div class="bg-red text-white py-2 w-100">
    <h1 class="text-center">Gestión de usuarios</h1>
  </div>
  <div class="container mt-2">
    <div class="d-flex justify-content-center align-items-center" v-if="loading">
      <div class="spinner-border text-danger" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    <div v-else>
      <div class="table-responsive">
        <table class="table table-striped mt-3">
          <thead>
            <tr>
              <th scope="col" id="nombre"><abbr title="Nombre">Nombre</abbr></th>
              <th scope="col" id="id"><abbr title="Identificación numérica en el sistema Free Tours">ID</abbr></th>
              <th scope="col" id="correo"><abbr title="Correo electrónico registrado">Correo</abbr></th>
              <th scope="col" id="rol"><abbr title="Rol del usuario (admin, guía o cliente)">Rol</abbr></th>
              <th scope="col" id="acciones"><abbr title="Acciones disponibles (editar rol o borrar usuario)">Acciones</abbr></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td headers="nombre">{{ user.nombre }}</td>
              <td headers="id">{{ user.id }}</td>
              <td headers="correo">{{ user.email }}</td>
              <td headers="rol">{{ user.rol }}</td>
              <td headers="acciones">
                <button @click="editUser(user)" class="btn btn-sm btn-danger mb-1 w-100" aria-label="Editar rol del usuario">Editar rol del usuario</button>
                <button @click="deleteUser(user.id)" class="btn btn-sm btn-secondary mb-1 w-100" aria-label="Eliminar usuario">Eliminar usuario</button>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5">Total de usuarios: {{ users.length }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <div class="modal" :class="{ 'd-block': showModalEditarUsuario}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
          <h5 class="modal-title">Editando usuario</h5>
          <button type="button" class="close" aria-label="Close" @click="cerrarModalEditarUsuario">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre:</label>
              <input type="text" id="nombre" v-model="editingUser.nombre" class="form-control" readonly />
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Correo:</label>
              <input type="email" id="email" v-model="editingUser.email" class="form-control" readonly />
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6">
              <label for="rol" class="form-label">Rol:</label>
              <select id="rol" v-model="editingUser.rol" class="form-control" required>
                <option value="guia">Guía</option>
                <option value="cliente">Cliente</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="cerrarModalEditarUsuario">Cerrar ventana</button>
          <button type="button" class="btn btn-danger" @click="saveChanges">Guardar cambios</button>
        </div>
      </div>
    </div>
  </div>
</template>





<style scoped>
</style>