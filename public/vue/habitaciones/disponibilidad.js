const app = Vue.createApp({
    data() {
        return {
            labels: [], // Nombres de los hoteles
            data: [] // Habitaciones disponibles por hotel para hoy
        };
    },
    
    mounted() {
        this.labels = window.hotelNames; // Nombres de los hoteles desde el backend
        this.data = window.availableRooms; // Habitaciones disponibles desde el backend
        this.renderChart(); // Llama al método para visualizar la gráfica
    },

    methods: {
        renderChart() {
            var grafica = document.getElementById('graficaHabitacionesDisponibles').getContext('2d'); 
            new Chart(grafica, {
                type: 'pie', 
                data: {
                    labels: this.labels, // Usa los nombres de los hoteles
                    datasets: [{
                        label: 'Habitaciones disponibles por hotel (hoy)', 
                        data: this.data, 
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
                                    const hotel = this.labels[tooltipItem.dataIndex];
                                    const habitaciones = this.data[tooltipItem.dataIndex];
                                    return `${hotel}: ${habitaciones} habitaciones disponibles`;
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
