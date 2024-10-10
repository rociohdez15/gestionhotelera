const app = Vue.createApp({
    // Define los datos utilizados en la aplicación
    data() {
        return {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
            // Etiquetas para los meses del año
            data: [] // Inicializa el array de datos vacío, se llenará más tarde con los datos recibidos del backend
        }
    },
    
    // Hook que se ejecuta cuando el componente se monta en el DOM
    mounted() {
        this.data = window.chartData; // Asigna los datos pasados desde la vista a la variable 'data'
        this.renderChart(); // Llama al método para visualizar la gráfica
    },

    // Definición de los métodos de la aplicación
    methods: {
        // Método que se encarga de crear y visualizar la gráfica con Chart.js
        renderChart() {
            var grafica = document.getElementById('graficaReservas').getContext('2d'); 
            // Obtiene el canvas donde se dibujará la gráfica
            new Chart(grafica, {
                type: 'bar', // Define el tipo de gráfico como un gráfico de barras
                data: {
                    labels: this.labels, // Usa las etiquetas de los meses del año
                    datasets: [{
                        label: 'Nº de reservas por meses', // Etiqueta que aparecerá en la gráfica
                        data: this.data, // Los datos que se mostrarán
                        backgroundColor: '#3c3c3c', // Color de fondo de las barras
                        borderColor: '#3c3c3c', // Color de los bordes de las barras
                        borderWidth: 1 // Grosor del borde de las barras
                    }]
                },
                options: {
                    responsive: true, // Hace que la gráfica sea responsive
                    maintainAspectRatio: false, 
                    scales: {
                        // Configuración de las lineas verticales
                        y: {
                            beginAtZero: true, 
                            grid: {
                                color: 'rgba(128, 128, 128, 0.5)', // Color de las líneas verticales de la cuadrícula
                                lineWidth: 2 // Grosor de las líneas verticales de la cuadrícula 
                            }
                        },
                        // Configuración de las lineas horizontales 
                        x: {
                            grid: {
                                color: 'rgba(128, 128, 128, 0.5)', // Color de las líneas horizontales de la cuadrícula 
                                lineWidth: 2 // Grosor de las líneas horizontales de la cuadrícula 
                            }
                        }
                    }
                }
            });
        }
    }
});

// Monta la aplicación Vue después de que el DOM haya sido completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    app.mount('#app'); // Monta la instancia de Vue en el elemento con el id "app"
});

