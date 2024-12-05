var app = Vue.createApp({
    data() {
        return {
            etiquetas: [], 
            datos: [], 
            etiquetasIngresos: [], 
            datosIngresos: [], 
            datosIngresosServicios: [], 
            etiquetasClientes: [], 
            datosClientes: [], 
            etiquetasServicios: [], 
            datosServicios: [] 
        };
    },
    
    mounted() {
        this.etiquetas = window.hotelNames; 
        this.datos = window.availableRooms; 
        this.etiquetasIngresos = window.ingresosMeses; 
        this.datosIngresos = window.ingresosTotales; 
        this.datosIngresosServicios = window.ingresosServiciosTotales; 
        this.etiquetasClientes = window.clientesMeses; 
        this.datosClientes = window.clientesTotales; 
        this.etiquetasServicios = window.serviciosCategorias; 
        this.datosServicios = window.serviciosTotales; 
    
        console.log('Nombres de Hoteles:', this.etiquetas);
        console.log('Habitaciones Disponibles:', this.datos);
        console.log('Meses de Ingresos:', this.etiquetasIngresos);
        console.log('Ingresos Totales:', this.datosIngresos);
        console.log('Ingresos por Servicios:', this.datosIngresosServicios);
        console.log('Meses de Clientes:', this.etiquetasClientes);
        console.log('Clientes Totales:', this.datosClientes);
        console.log('Categorías de Servicios:', this.etiquetasServicios);
        console.log('Servicios Totales:', this.datosServicios);
    
        this.renderizarGrafico(); 
        this.renderizarGraficoIngresos(); 
        this.renderizarGraficoClientes(); 
        this.renderizarGraficoServicios(); 
    },

    methods: {
        renderizarGrafico() {
            var grafica = document.getElementById('graficaHabitacionesDisponibles').getContext('2d'); 
            new Chart(grafica, {
                type: 'pie', 
                data: {
                    labels: this.etiquetas, 
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
                            position: 'top', 
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
                    labels: this.etiquetasIngresos, 
                    datasets: [
                        {
                            label: 'Ingresos totales por mes (Reservas)', 
                            data: this.datosIngresos, 
                            backgroundColor: '#36A2EB', 
                            hoverOffset: 4
                        },
                        {
                            label: 'Ingresos totales por mes (Servicios)', 
                            data: this.datosIngresosServicios, 
                            backgroundColor: '#FFCE56', 
                            hoverOffset: 4
                        }
                    ]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: {
                        legend: {
                            position: 'top', 
                        },
                        tooltip: {
                            callbacks: {
                                label: (tooltipItem) => {
                                    var mes = this.etiquetasIngresos[tooltipItem.dataIndex];
                                    var ingresos = tooltipItem.datasetIndex === 0 ? this.datosIngresos[tooltipItem.dataIndex] : this.datosIngresosServicios[tooltipItem.dataIndex];
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
                    labels: this.etiquetasClientes, 
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
                            position: 'top', 
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
                    labels: this.etiquetasServicios, 
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
                            position: 'top', 
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
    app.mount('#app'); 
});