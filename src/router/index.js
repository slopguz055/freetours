import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import LoginView from "../views/LoginView.vue";
// ADMIN
import GestionUsuarios from "../views/admin/GestionUsuarios.vue";
import CrearRuta from "../views/admin/CrearRuta.vue";
import RutasAdmin from "../views/admin/RutasAdmin.vue";
// CLIENTE
import MisReservas from "../views/cliente/MisReservas.vue";
import MisRutas from "../views/cliente/MisRutas.vue";
//GUÍA
import RutasAsignadas from "../views/guia/RutasAsignadas.vue";
//import ListadoAsistentes from '../views/guia/ListadoAsistentes.vue'

import About from "../views/About.vue";
import RegisterView from "../views/RegisterView.vue";
import RutaDetalleView from "../views/RutaDetalleView.vue";
import HistorialRutas from "@/views/admin/HistorialRutas.vue";

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: "/",
			name: "home",
			component: HomeView,
		},
		{
			path: "/login",
			name: "login",
			// route level code-splitting
			// this generates a separate chunk (About.[hash].js) for this route
			// which is lazy-loaded when the route is visited.
			component: LoginView,
		},
		{
			// ADMIN
			path: "/users",
			name: "users",
			component: GestionUsuarios,
		},
		{
			path: "/crearRuta",
			name: "crearRuta",
			component: CrearRuta,
		},
		{
			path: "/rutasadmin",
			name: "rutasadmin",
			component: RutasAdmin,
		},
		{
			// CLIENTE
			path: "/misReservas",
			name: "misReservas",
			component: MisReservas,
		},
		{
			path: "/misRutas",
			name: "misRutas",
			component: MisRutas,
		},
		{
			path: "/rutasAsignadas",
			name: "rutasAsignadas",
			component: RutasAsignadas,
		},
		{
			path: "/about",
			name: "about",
			component: About,
		},
		{
			path: "/registro",
			name: "registro",
			component: RegisterView,
		},
		{
			path: "/ruta-detalle/:id",
			name: "ruta-detalle",
			component: RutaDetalleView,
			props: true,
		},
		{
			path: "/historial",
			name: "historial",
			component: HistorialRutas,
			props: true,
		},
	],
});

export default router;
