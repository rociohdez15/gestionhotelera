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
        <h2 class="titulo text-center">ESCRIBE TU RESEÑAS</h2>
        <br>
        <div class="container">
            <form action="{{ route('guardarResena', ['hotelID' => $hotel->hotelID]) }}" method="POST">
                @csrf
                <input type="hidden" name="clienteID" value="{{ Auth::id() }}">
                <input type="hidden" name="fecha" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                <input type="hidden" name="nombre_cliente" value="{{ Auth::user()->name }}">
                <div class="form-group row align-items-center">
                    <div class="col-sm-6 offset-sm-3">
                        <h6><strong>Hotel:</strong> {{ $hotel->nombre }}</h6>
                        <h6><strong>Fecha:</strong> {{ $fechaHoy }}</h6>

                        <label for="resena"><strong>Reseña: </strong></label>
                        <textarea class="form-control" id="resena" name="resena" rows="4" placeholder="Escribe tu reseña aquí..."></textarea>

                        <label for="puntuacion"><strong>Puntuación (0-10): </strong></label>
                        <input type="number" class="form-control" id="puntuacion" name="puntuacion" min="0" max="10" step="1" placeholder="Introduce una puntuación">
                        <br>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Publicar</button>
                        </div>
                    </div>
                </div>
                <br>
            </form>

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
<script src="../../js/informacion-usuario/js.js"></script>

</html>