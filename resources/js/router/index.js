import { createRouter, createWebHistory } from "vue-router";

import editReserva from '../components/EditarReserva.vue'

const routes = [
  {
    path: '/editReserva',
    name: 'editarReserva',
    component: editReserva,
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})