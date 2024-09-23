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
    <link rel="stylesheet" href="../../css/deja-resena/styles.css">
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
                <li><a href="#">Mis Reservas</a></li>
                <li class="divider">|</li>
                <li><a href="#">Mis Reseñas</a></li>
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
        <h2 class="titulo text-center">DÉJANOS TUS RESEÑAS</h2>
        <br>
        @if ($hotelesSinResena->isEmpty())
        <p class="text-center">No tienes hoteles pendientes de reseñar.</p>
        @else
        <div class="container">
            @foreach ($datos as $hotel)
                <div class="row align-items-start mb-3 mostrar-alojamientos">
                    <!-- Imagen del hotel -->
                    <div class="col-12 col-md-4 hotel-imagen mb-3 mb-md-0">
                        @if ($hotel->imagen_url)
                            <img src="{{ asset($hotel->imagen_url) }}" alt="{{ $hotel->nombre }}" class="img-fluid rounded imagen-portada-alojamiento">
                        @else
                            <p>No hay imagen de portada disponible.</p>
                        @endif
                    </div>
                    <div class="col-12 col-md-8">
                        <h5>{{ $hotel->nombre }}</h5>
                        <p><strong>Dirección:</strong> {{ $hotel->direccion }}</p>
                        <p><strong>Descripción:</strong> {{ $hotel->descripcion }}</p>
                        <a href="#" class="btn btn-primary mt-2">Escribir Reseña</a>
                    </div>
                </div>
            @endforeach
        </div>

        <br>
        <!-- Paginación -->
        <div class="container text-center">
            <p>
                Página {{ $pagina_actual }} de {{ $total_paginas }} | Mostrar {{ $registros_por_pagina }} registros por página | Ir a página:
                @for ($i = 1; $i <= $total_paginas; $i++)
                <a href="{{ route('dejarResenas', array_merge(request()->except('pagina'), ['pagina' => $i])) }}">{{ $i }}</a>
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