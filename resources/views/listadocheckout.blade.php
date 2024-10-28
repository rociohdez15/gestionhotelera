<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Listado de Check-Out</title>
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
            <div class="alert alert-success" id="success-message" style="text-align:center; max-width: 400px; margin: 0 auto;">
                {{ request()->get('success') }}
            </div>
            @endif

            <!-- Mostrar mensaje de error si está presente en la URL -->
            @if(request()->has('error'))
            <div class="alert alert-danger" id="error-message" style="text-align:center; max-width: 400px; margin: 0 auto;">
                {{ request()->get('error') }}
            </div>
            @endif

            <br>
            <h2 class="text-center">LISTADO DE CHECK-OUT</h2>

            <br>
            <!-- Buscador -->
            <form action="{{ route('buscarCheckout') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="Buscar por ID, cliente o nº de habitación" aria-label="Buscar" value="{{ request('query') }}">
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
                            <th>Chech-Out</th>
                            <th>Operaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservas as $reserva)
                        <tr>
                            <td>{{ $reserva->reservaID }}</td>
                            <td>{{ $reserva->nombre }}, {{ $reserva->apellidos }}</td>
                            <td>{{ $reserva->numhabitacion }}</td>
                            <td>{{ $reserva->fecha_checkout }}</td>
                            <td class="d-flex justify-content-center gap-2">
                                @if ($reserva->fecha_checkout == $fecha_actual)
                                <a href="#" class="btn btn-primary btn-sm">
                                    GESTIONAR CHECK-OUT
                                </a>
                                @else
                                <span class="text-muted">No se puede gestionar Check-out</span>
                                @endif
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
                        <a href="{{ route('listadoCheckout', array_merge(request()->except('pagina'), ['pagina' => $i, 'query' => $query])) }}">{{ $i }} </a>
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

</html>