<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Listado de Servicios</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../../css/listar-reservas/styles.css">
    <!-- Favicon -->
    <link rel="icon" href="../../images/inicio/favicon.ico" type="image/x-icon">
    <!-- Agrega Vue.js -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.js"></script>
    <!-- Agrega Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header>
        <!-- Logo -->
        <a href="{{ route('inicio') }}">
            <img src="../../images/inicio/logo.png" alt="Logo de Aloja Directo" class="logo-cabecera">
        </a>

        <nav class="menu-izquierda">
            <ul class="d-flex flex-wrap list-unstyled">
                <li><a href="{{ route('inicio') }}">Inicio</a></li>
            </ul>
        </nav>

        @guest
        <a href="{{ route('login') }}" class="btn btn-secondary icono-user" id="dropdownMenuButton" role="button" aria-expanded="false">
            <i class="fas fa-user"></i>
        </a>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle icono-menu" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-globe mundo"></i>
                        <p class="texto-mundo"> Cambiar de idioma </p>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('login') }}">
                        <i class="fas fa-headset"></i> Iniciar sesión como Recepcionista
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('login') }}">
                        <i class="fas fa-user-shield"></i> Iniciar sesión como Administrador
                    </a>
                </li>
            </ul>
        </div>
        @endguest

        @auth
        <a href="#" class="btn btn-secondary user-name icono-user d-flex align-items-center" id="dropdownMenuButton" role="button" aria-expanded="false">
            {{ Auth::user()->name }}
            <i class="fa-solid fa-right-from-bracket ms-2"></i>
        </a>
        <a href="{{ route('logout') }}" class="btn btn-secondary icono-user" role="button" aria-expanded="false">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
        @endauth
    </header>

    <main>
        <br>
        <div class="container">
            <!-- Contenido Principal -->
            @if ($errors->any())
            <div class="alert alert-danger ml-2" style="max-width: 400px; margin: 0 auto;">
                @foreach ($errors->all() as $error)
                {{ $error }}
                @endforeach
            </div>
            <br>
            @endif

            @if(session('status'))
            <div class="alert alert-success" style="max-width: 400px; margin: 0 auto;">
                {{ session('status') }}
            </div>
            @endif

            <!-- Mostrar mensaje de éxito si está presente en la URL -->
            @if(request()->has('success'))
            <div class="alert alert-success" id="success-message" style="text-align: center; max-width: 400px; margin: 0 auto;">
                {{ request()->get('success') }}
            </div>
            @endif

            <!-- Mostrar mensaje de error si está presente en la URL -->
            @if(request()->has('error'))
            <div class="alert alert-danger" id="error-message" style="text-align: center; max-width: 400px; margin: 0 auto;">
                {{ request()->get('error') }}
            </div>
            @endif

            <br>
            <h2 class="text-center">LISTADO DE SERVICIOS</h2>

            <br>
            <!-- Buscador -->
            <form action="{{ route('buscarReservas') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="Buscar por ID, cliente, hotel, adultos o niños" aria-label="Buscar reservas">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
            
            <!-- Muestra la tabla de reservas -->
            <div class="table-responsive mx-auto">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Nº Habitación</th>
                            <th>Hotel</th>
                            <th>Servicio</th>
                            <th>Día</th>
                            <th>Hora</th>
                            <th>Operaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservas as $reserva)
                        <tr>
                            <td>{{ $reserva->servicioID }}</td>
                            <td>{{ $reserva->nombre }}, {{ $reserva->apellidos }}</td>
                            <td>{{ $reserva->numhabitacion }}</td>
                            <td>{{ $reserva->nombre_hotel }}</td>
                            <td>{{ $reserva->nombre_servicio }}</td>
                            <td>{{ $reserva->dia_servicio }}</td>
                            <td>{{ $reserva->hora_servicio }}</td>
                            <td class="d-flex justify-content-center gap-2">
                                <!-- Botón para abrir el modal de eliminacion-->
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $reserva->servicioID }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                                <!-- Modal de confirmación de eliminación-->
                                <div class="modal fade" id="confirmDeleteModal{{ $reserva->servicioID }}" tabindex="-1" aria-labelledby="confirmDeleteModalLabel{{ $reserva->servicioID }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmDeleteModalLabel{{ $reserva->servicioID }}">Confirmar eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro de que deseas eliminar este servicio?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('delServicio', $reserva->servicioID) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Botón para abrir el modal de editar-->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#confirmEditModal{{ $reserva->reservaID }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <!-- Modal de confirmación de editar reserva-->
                                <div class="modal fade" id="confirmEditModal{{ $reserva->reservaID }}" tabindex="-1" aria-labelledby="confirmEditModalLabel{{ $reserva->reservaID }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmEditarModalLabel{{ $reserva->reservaID }}">Confirmar editar</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro de que deseas editarar esta reserva?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('mostrarReserva', $reserva->reservaID) }}" method="POST">
                                                    @csrf
                                                    @method('GET')
                                                    <button type="submit" class="btn btn-danger">Editar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('generar_pdf_listar_reservas') }}" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <br>
            <!-- Paginación -->
            <div class="container text-center">
                <p>
                    Página {{ $pagina_actual }} de {{ $total_paginas }} | Mostrar {{ $registros_por_pagina }} registros por página | Ir a página:
                    @for ($i = 1; $i <= $total_paginas; $i++)
                        <a href="{{ route('listarReservas', array_merge(request()->except('pagina'), ['pagina' => $i])) }}">{{ $i }} </a>
                        @endfor
                </p>
            </div>
        </div>
    </main>
    <footer>
        <a href="#">Términos y Condiciones</a> |
        <a href="#">Sobre AlojaDirecto.com</a>
        <p>Copyright © 2024 AlojaDirecto.com<br>Todos los derechos reservados</p>
    </footer>
</body>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Incluye el archivo de Vue -->
<script src="{{ asset('../../vue/panelrecepcionistas/panel1.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            // Eliminar el parámetro de consulta 'success' de la URL
            const url = new URL(window.location);
            url.searchParams.delete('success');
            window.history.replaceState({}, document.title, url);

            // Ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 2500);
        }

        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            // Eliminar el parámetro de consulta 'error' de la URL
            const url = new URL(window.location);
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url);

            // Ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 2500);
        }
    });
</script>

</html>