<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Informacion de usuario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../../css/misreservas/styles.css">
    <!-- Favicon -->
    <link rel="icon" href="../../images/inicio/favicon.ico" type="image/x-icon">
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
                <li class="divider">|</li>
                <li><a href="{{ route('informacionusuario') }}">Perfil</a></li>
                <li class="divider">|</li>
                <li><a href="{{ route ('mostrarMisReservas') }}">Mis Reservas</a></li>
                <li class="divider">|</li>
                <li><a href="{{ route ('mostrarResenas', ['clienteID' => Auth::id()]) }}">Mis Reseñas</a></li>
            </ul>
        </nav>

        @guest
        <!-- Botón de usuario para iniciar sesión -->
        <a href="{{ route('login') }}" class="btn btn-secondary icono-user" id="dropdownMenuButton" role="button" aria-expanded="false">
            <i class="fas fa-user"></i>
        </a>

        <!-- Botón de menu para iniciar sesión como admin o recepcionista -->
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
        <a href="{{ route('informacionusuario') }}" class="btn btn-secondary user-name icono-user d-flex align-items-center" id="dropdownMenuButton" role="button" aria-expanded="false">
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
        <br>
        <h2 class="titulo text-center">Mis Reservas</h2>
        <br>
        @php
        // Definir la clase de columna dependiendo de la cantidad de reservas
        // Dependiendo del numero de columnas contadas se muestra un ancho u otro
        $col_class = $datos->count() === 1 ? 'col-md-12' : 'col-md-4';
        @endphp

        @if ($datos->isEmpty())
        <p class="text-center">No tienes reservas.</p>
        @else
        <div class="container">
            <div class="row justify-content-center"> <!-- Alineación central -->
                @foreach($datos as $reserva)
                <div class="{{ $col_class }} col-sm-12 mb-4"> <!-- Usar col_class -->
                    <div class="card">
                        <img src="{{ asset($reserva->hotel_imagen) }}" class="card-img-top" alt="Imagen del hotel">
                        <div class="card-body">
                            <h5 class="card-title">{{ $reserva->hotel_nombre }}</h5>
                            <p class="card-text"><strong>Huesped: </strong>{{ $reserva->cliente_nombre }}</p>
                            <p class="card-text"><strong>Fecha de entrada: </strong>{{ $reserva->fechainicio }}</p>
                            <p class="card-text"><strong>Fecha de salida: </strong>{{ $reserva->fechafin }}</p>
                            <p class="card-text"><strong>Días: </strong>{{ $reserva->num_dias }}</p>
                            <p class="card-text"><strong>Servicios Adicionales:</strong></p>
                            <ul class="list-unstyled">
                                @if($reserva->servicio_detalles)
                                @foreach(explode(', ', $reserva->servicio_detalles) as $detalle)
                                <li class="mb-2">{{ $detalle }}</li>
                                @endforeach
                                @else
                                <li>No hay servicios adicionales contratados.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <br>
        <!-- Paginación -->
        <div class="container text-center">
            <p>
                Página {{ $pagina_actual }} de {{ $total_paginas }} | Mostrar {{ $registros_por_pagina }} registros por página | Ir a página:
                @for ($i = 1; $i <= $total_paginas; $i++)
                    <a href="{{ route('mostrarMisReservas', array_merge(request()->except('pagina'), ['pagina' => $i])) }}">{{ $i }}</a>
                    @endfor
            </p>
        </div>
        @endif

    </main>

    <footer>
        <a href="#">Términos y Condiciones</a> |
        <a href="#">Sobre AlojaDirecto.com</a>
        <p>Copyright © 2024 AlojaDirecto.com<br>Todos los derechos reservados</p>
    </footer>
</body>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/informacion-usuario/js.js"></script>

</html>