<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tituloventana }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/realizar-reserva/styles.css') }}">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/inicio/favicon.ico') }}" type="image/x-icon">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
</head>

<body>
    <header>
        <!-- Logo -->
        <a href="{{ route('inicio') }}">
            <img src="{{ asset('images/inicio/logo.png') }}" alt="Logo de Aloja Directo" class="logo-cabecera">
        </a>

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
        <!-- Cabecera -->
        <div class="contenedor-titulos">
            <h1 class="titulo">RESERVAR AHORA</h1>
            <p class="eslogan">{{$hotel->nombre}}</p>
        </div>
        <!-- Galería de Imágenes -->
        <div class="carousel">
            <div class="carousel-inner">
                @foreach ($imagenes as $index => $imagen)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset($imagen) }}" alt="Imagen del hotel">
                </div>
                @endforeach
            </div>
            <a class="carousel-control-prev" href="#prev" role="button">
                <span class="carousel-control-prev-icon" aria-hidden="true">&lt;</span>
            </a>
            <a class="carousel-control-next" href="#next" role="button">
                <span class="carousel-control-next-icon" aria-hidden="true">&gt;</span>
            </a>
        </div>
        <br>
        <br>
        <form action="{{ route('guardarreserva') }}" method="POST">
            <p class="servicios"><strong>SERVICIOS ADICIONALES</strong></p>
            <div class="container">
                <div class="form-group">
                    <div class="form-row">
                        <p><strong>RESTAURANTE-> </strong> FECHA Y HORA:</p>
                        <input type="datetime-local" id="fecha-restaurante" name="fecha-restaurante" class="form-control">
                    </div>
                    <div class="form-row">
                        <p><strong>SPA-> </strong> FECHA Y HORA:</p>
                        <input type="datetime-local" id="fecha-spa" name="fecha-spa" class="form-control">
                    </div>
                    <div class="form-row">
                        <p><strong>TOURS-> </strong> FECHA Y HORA:</p>
                        <input type="datetime-local" id="fecha-tours" name="fecha-tours" class="form-control">
                    </div>
                    <p id="mensaje-error" class="text-danger"></p>
                    <button id="validar-fechas" class="btn btn-primary">Reservar servicios</button>
                </div>
            </div>
            <br>
            <p class="servicios"><strong>SERVICIOS POPULARES</strong></p>
            <div class="container">
                <div class="form-group">
                    <div class="form-row">
                        <p><strong>GIMNASIO: </strong> 09:00h - 21:00h</p>
                    </div>
                    <div class="form-row">
                        <p><strong>PISCINA: </strong> 10:00h - 20:30h (Solo en temporada de verano.)</p>
                    </div>
                    <div class="form-row">
                        <p><strong>PARKING PRIVADO: </strong> 24h</p>
                    </div>
                    <div class="form-row">
                        <p><strong>WIFI: </strong> 24h (Solicitar la password en recepción.)</p>
                    </div>

                    @csrf
                    <input type="hidden" name="fechaEntrada" value="{{ $fechaEntrada }}">
                    <input type="hidden" name="fechaSalida" value="{{ $fechaSalida }}">
                    <input type="hidden" name="clienteID" value="{{ $clienteID }}">
                    <input type="hidden" name="adultos" value="{{ $adultos }}">
                    <input type="hidden" name="ninos" value="{{ $ninos }}">

                    @foreach($habitacionID as $id)
                    <input type="hidden" name="habitacionID[]" value="{{ $id }}">
                    @endforeach
                    <input type="hidden" name="precioHabitacion" value="{{ $precioHabitacion }}">

                    @foreach($edadesNinos as $edad)
                    <input type="hidden" name="edadesNinos[]" value="{{ $edad }}">
                    @endforeach

                    <button type="submit" id="guardar-reserva" class="btn btn-primary mt-3">Realizar reserva</button>

                </div>
            </div>
        </form>

        <!-- Sección de Reseñas -->
        <div class="container mt-5">
            @if ($resenas->isEmpty())
            <p>No hay reseñas disponibles para este hotel.</p>
            @else
            <div id="reseñasCarousel" class="carousel slide">
                <div class="carousel-inner">
                    @foreach ($resenas->chunk(3) as $index => $resenasChunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="d-flex justify-content-center">
                            @foreach ($resenasChunk as $resena)
                            <div class="cuadro-resena">
                                <h5 class="mb-1"><i class="fa-solid fa-user"></i> <strong>{{ $resena->nombre_cliente }}</strong></h5>
                                <p class="mb-1"><i class="fa-solid fa-comment"></i> "{{ $resena->texto }}"</p>
                                <br>
                                <p class="mb-1"><strong>Puntuación: </strong>{{ $resena->puntuacion }}/10</p>
                                <small class="text-muted">Fecha: {{ $resena->fecha }}</small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <a class="carousel-control-prev reseñas-carousel-control-prev" href="#reseñasCarousel" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </a>
                <a class="carousel-control-next reseñas-carousel-control-next" href="#reseñasCarousel" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </a>

            </div>
            @endif
        </div>
    </main>
    <footer>
        <a href="#">Términos y Condiciones</a> |
        <a href="#">Sobre AlojaDirecto.com</a>
        <p>Copyright © 2024 AlojaDirecto.com<br>Todos los derechos reservados</p>
    </footer>
</body>
<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS -->
<script src="{{ asset('js/realizar-reserva/js.js') }}"></script>

</html>