var app = Vue.createApp({
    data() {
        return {
            etiquetas: [], // Nombres de los hoteles
            datos: [], // Habitaciones disponibles por hotel para hoy
            etiquetasIngresos: [], // Meses
            datosIngresos: [], // Ingresos por mes
            etiquetasClientes: [], // Meses para clientes
            datosClientes: [], // Clientes registrados por mes
            etiquetasServicios: [], // Categorías de servicios
            datosServicios: [] // Servicios por categoría
        };
    },
    
    mounted() {
        this.etiquetas = window.hotelNames; // Nombres de los hoteles desde el backend
        this.datos = window.availableRooms; // Habitaciones disponibles desde el backend
        this.etiquetasIngresos = window.ingresosMeses; // Meses desde el backend
        this.datosIngresos = window.ingresosTotales; // Ingresos desde el backend
        this.etiquetasClientes = window.clientesMeses; // Meses para clientes desde el backend
        this.datosClientes = window.clientesTotales; // Clientes registrados desde el backend
        this.etiquetasServicios = window.serviciosCategorias; // Categorías de servicios desde el backend
        this.datosServicios = window.serviciosTotales; // Servicios por categoría desde el backend
    
        console.log('Nombres de Hoteles:', this.etiquetas);
        console.log('Habitaciones Disponibles:', this.datos);
        console.log('Meses de Ingresos:', this.etiquetasIngresos);
        console.log('Ingresos Totales:', this.datosIngresos);
        console.log('Meses de Clientes:', this.etiquetasClientes);
        console.log('Clientes Totales:', this.datosClientes);
        console.log('Categorías de Servicios:', this.etiquetasServicios);
        console.log('Servicios Totales:', this.datosServicios);
    
        this.renderizarGrafico(); // Llama al método para visualizar la gráfica
        this.renderizarGraficoIngresos(); // Llama al método para visualizar la gráfica de ingresos
        this.renderizarGraficoClientes(); // Llama al método para visualizar la gráfica de clientes
        this.renderizarGraficoServicios(); // Llama al método para visualizar la gráfica de servicios
    },

    methods: {
        renderizarGrafico() {
            var grafica = document.getElementById('graficaHabitacionesDisponibles').getContext('2d'); 
            new Chart(grafica, {
                type: 'pie', 
                data: {
                    labels: this.etiquetas, // Usa los nombres de los hoteles
                    datasets: [{
                        label: 'Habitaciones disponibles por hotel (hoy)', 
                        data: this.datos, 
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#28B463', '#9966FF', '#FF9F40', '#66FF66', '#FF66B2', '#66B2FF', '#FFB266'
                        ], 
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: {
                        legend: {
                            position: 'top', // Posición de la leyenda en la parte superior
                        },
                        tooltip: {
                            callbacks: {
                                label: (tooltipItem) => {
                                    var hotel = this.etiquetas[tooltipItem.dataIndex];
                                    var habitaciones = this.datos[tooltipItem.dataIndex];
                                    return `${hotel}: ${habitaciones} habitaciones disponibles`;
                                }
                            }
                        }
                    }
                }
            });
        },
        renderizarGraficoIngresos() {
            var graficaIngresos = document.getElementById('graficaIngresosPorMes').getContext('2d'); 
            new Chart(graficaIngresos, {
                type: 'bar', 
                data: {
                    labels: this.etiquetasIngresos, // Usa los meses
                    datasets: [{
                        label: 'Ingresos totales por mes', 
                        data: this.datosIngresos, 
                        backgroundColor: '#36A2EB', 
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: {
                        legend: {
                            position: 'top', // Posición de la leyenda en la parte superior
                        },
                        tooltip: {
                            callbacks: {
                                label: (tooltipItem) => {
                                    var mes = this.etiquetasIngresos[tooltipItem.dataIndex];
                                    var ingresos = this.datosIngresos[tooltipItem.dataIndex];
                                    return `${mes}: ${ingresos} €`;
                                }
                            }
                        }
                    }
                }
            });
        },
        renderizarGraficoClientes() {
            var graficaClientes = document.getElementById('graficaClientesPorMes').getContext('2d'); 
            new Chart(graficaClientes, {
                type: 'bar', 
                data: {
                    labels: this.etiquetasClientes, // Usa los meses
                    datasets: [{
                        label: 'Clientes registrados por mes', 
                        data: this.datosClientes, 
                        backgroundColor: '#FF6384', 
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: {
                        legend: {
                            position: 'top', // Posición de la leyenda en la parte superior
                        },
                        tooltip: {
                            callbacks: {
                                label: (tooltipItem) => {
                                    var mes = this.etiquetasClientes[tooltipItem.dataIndex];
                                    var clientes = this.datosClientes[tooltipItem.dataIndex];
                                    return `${mes}: ${clientes} clientes`;
                                }
                            }
                        }
                    }
                }
            });
        },
        renderizarGraficoServicios() {
            var graficaServicios = document.getElementById('graficaServiciosPorCategoria').getContext('2d'); 
            new Chart(graficaServicios, {
                type: 'pie', 
                data: {
                    labels: this.etiquetasServicios, // Usa las categorías de servicios
                    datasets: [{
                        label: 'Servicios adicionales por categoría', 
                        data: this.datosServicios, 
                        backgroundColor: [
                            '#28B463', '#FFCE56', '#FF6384', '#36A2EB', '#9966FF', '#FF9F40', '#66FF66', '#FF66B2', '#66B2FF', '#FFB266'
                        ], 
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: {
                        legend: {
                            position: 'top', // Posición de la leyenda en la parte superior
                        },
                        tooltip: {
                            callbacks: {
                                label: (tooltipItem) => {
                                    var categoria = this.etiquetasServicios[tooltipItem.dataIndex];
                                    var total = this.datosServicios[tooltipItem.dataIndex];
                                    return `${categoria}: ${total} servicios`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    app.mount('#app'); // Monta la instancia de Vue en el elemento con el id "app"
});