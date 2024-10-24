<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AlojaDirecto | Editar Reserva</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/listar-reservas/styles.css') }}">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/inicio/favicon.ico') }}" type="image/x-icon">
    <script src="https://unpkg.com/vue@3.5.12/dist/vue.global.js"></script>
    @vite('resources/css/app.css')
</head>

<body>
    <header>
        <!-- Logo -->
        <a href="{{ route('inicio') }}">
            <img src="{{ asset('images/inicio/logo.png') }}" alt="Logo de Aloja Directo" class="logo-cabecera">
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
    <div id="app" 
             data-reserva="{{ json_encode($reserva) }}" 
             data-edades-ninos="{{ json_encode($edadesNinos) }}" 
             data-hotel="{{ json_encode($hotel) }}"
             data-cliente="{{ json_encode($cliente) }}"
             data-habitacion="{{ json_encode($habitacion) }}"
             data-reserva-id="{{ $reserva->reservaID }}">
        </div>
        @vite('resources/js/app.js')
    </main>

    <footer>
        <a href="#">Términos y Condiciones</a> |
        <a href="#">Sobre AlojaDirecto.com</a>
        <p>Copyright © 2024 AlojaDirecto.com<br>Todos los derechos reservados</p>
    </footer>

</body>
</html>
