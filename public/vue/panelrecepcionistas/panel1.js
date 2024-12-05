const app = Vue.createApp({
    data() {
        return {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datos: [], 
            mesSeleccionado: -1, 
            grafica: null 
        }
    },
    mounted() {
        this.datos = window.chartData; 
        this.renderizarGrafica(); 
    },
    methods: {
        renderizarGrafica() {
            var contextoGrafica = document.getElementById('graficaReservas').getContext('2d');
            this.grafica = new Chart(contextoGrafica, {
                type: 'bar',
                data: {
                    labels: this.mesSeleccionado === -1 ? this.labels : Array.from({ length: 31 }, (_, i) => i + 1), 
                    datasets: [{
                        label: this.mesSeleccionado === -1 ? 'Nº de reservas por mes' : 'Nº de reservas por día',
                        data: this.mesSeleccionado === -1 ? this.datos.map(mesDatos => mesDatos.reduce((a, b) => a + b, 0)) : this.datos[this.mesSeleccionado], 
                        backgroundColor: '#0b31ee',
                        borderColor: '#0b31ee',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) { return Number.isInteger(value) ? value : null; }
                            },
                            grid: {
                                color: 'rgba(128, 128, 128, 0.5)',
                                lineWidth: 2
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(128, 128, 128, 0.5)',
                                lineWidth: 2
                            }
                        }
                    }
                }
            });
        },
        actualizarGrafica() {
            if (this.grafica) {
                this.grafica.destroy();
            }
            this.renderizarGrafica();
        },
        cambiarVista(mes) {
            this.mesSeleccionado = mes;
            this.actualizarGrafica();
        },
        cambiarVistaAnual() {
            this.mesSeleccionado = -1;
            this.actualizarGrafica();
        },
        manejarCambioMes(evento) {
            if (evento.target.value == -1) {
                this.cambiarVistaAnual();
            } else {
                this.cambiarVista(evento.target.value);
            }
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    app.mount('#app');
});

