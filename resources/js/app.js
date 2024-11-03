
import { createApp } from 'vue';
import EditarReserva from './components/EditarReserva.vue'; 
import RegistrarCheckout from './components/RegistrarCheckout.vue'; 
import RegistrarCheckin from './components/RegistrarCheckin.vue'; 
import EditarServicio from './components/EditarServicio.vue'; 


createApp(EditarReserva).mount('#app');
createApp(RegistrarCheckout).mount('#app2');
createApp(RegistrarCheckin).mount('#app3');
createApp(EditarServicio).mount('#app4');
