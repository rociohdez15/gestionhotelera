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
    <link rel="stylesheet" href="../../css/inicio/style.css">
    <!-- Favicon -->
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:400,700,400italic%7CPoppins:300,400,500,700">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
    <link rel="icon" href="../../images/inicio/favicon.ico" type="image/x-icon">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>

<body>
    <!-- Page Header-->
    <header class="page-header" style="padding-bottom: 24px">
        <!-- RD Navbar-->
        <div class="rd-navbar-wrap">
            <nav class="rd-navbar rd-navbar-default-with-top-panel" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed" data-md-layout="rd-navbar-fullwidth" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-fullwidth" data-lg-device-layout="rd-navbar-fullwidth" data-md-stick-up-offset="90px" data-lg-stick-up-offset="150px" data-stick-up="true" data-sm-stick-up="true" data-md-stick-up="true" data-lg-stick-up="true">
                <div class="rd-navbar-top-panel rd-navbar-collapse">
                    <div class="rd-navbar-top-panel-inner">
                        @guest
                        <div class="left-side">
                            <div class="group d-flex align-items-center">
                                <span class="icon icon-sm icon-secondary-5 fa fa-user-shield me-2"></span>
                                <a href="{{ route('panelrecepcionista') }}" class="text-italic">Acceso a administración</a>
                            </div>
                        </div>
                        @endguest
                        <div class="center-side">
                            <!-- RD Navbar Brand-->
                            <div class="rd-navbar-brand fullwidth-brand">
                                <a class="brand-name" href="{{ route('inicio') }}">
                                    <img class="brand-logo img-fluid" src="{{ asset('images/inicio/logo.png') }}" alt="Aloja Directo">
                                </a>
                            </div>
                        </div>

                        <div class="right-side">
                            <!-- Contact Info-->
                            <div class="right-side">
                                <div class="d-flex align-items-center">
                                    @guest
                                    <span class="icon icon-primary text-middle fas fa-user me-2"></span>
                                    <a class="text-italic" href="{{ route('login') }}">Iniciar Sesión</a>
                                    @endguest
                                    @auth
                                    <a href="{{ route('informacionusuario') }}" class="btn btn-secondary user-name icono-user d-flex align-items-center" id="dropdownMenuButton" role="button" aria-expanded="false">
                                        {{ Auth::user()->name }} {{ Auth::user()->apellido }}
                                        <i class="fa-solid fa-user ms-2"></i>
                                    </a>
                                    <a href="{{ route('logout') }}" class="btn btn-secondary icono-user" role="button" aria-expanded="false">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rd-navbar-inner">
                    <!-- RD Navbar Panel-->
                    <div class="rd-navbar-panel">
                        <!-- RD Navbar Toggle-->
                        <button class="rd-navbar-toggle" data-rd-navbar-toggle=".rd-navbar-nav-wrap"><span></span></button>
                        <!-- RD Navbar collapse toggle-->
                        <button class="rd-navbar-collapse-toggle" data-rd-navbar-toggle=".rd-navbar-collapse"><span></span></button>
                        <!-- RD Navbar Brand-->
                        <div class="rd-navbar-brand mobile-brand"><a class="brand-name" href="{{ route('inicio') }}"><img src="{{ asset('images/inicio/logo.png') }}" alt="" width="314" height="48" /></a></div>
                    </div>
                    <div class="rd-navbar-aside-right">
                        <div class="rd-navbar-nav-wrap">
                            <div class="rd-navbar-nav-scroll-holder">
                                <ul class="rd-navbar-nav">
                                    <li><a href="{{ route('inicio') }}">Inicio</a></li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="{{ route('inicio') }}" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Descubre España
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <li><a class="dropdown-item" href="{{ route('descubreEspana', ['ciudad' => 'Valencia']) }}">Valencia</a></li>
                                            <li><a class="dropdown-item" href="{{ route('descubreEspana', ['ciudad' => 'Madrid']) }}">Madrid</a></li>
                                            <li><a class="dropdown-item" href="{{ route('descubreEspana', ['ciudad' => 'Menorca']) }}">Menorca</a></li>
                                            <li><a class="dropdown-item" href="{{ route('descubreEspana', ['ciudad' => 'Sevilla']) }}">Sevilla</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{ asset('about-us.html') }}">Sobre Nosotros</a></li>
                                    <li><a href="{{ asset('contacts.html') }}">Contacto</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <br>
        <h3 class="text-center">Reservar ahora</h3>
        <!-- Contenedor Principal -->
        <div class="container mt-4">
            <div class="row justify-content-center align-items-center">
                <!-- Carrusel de Imágenes (más pequeño) -->
                <div class="col-md-6">
                    <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach ($imagenes as $index => $imagen)
                            <button type="button" data-bs-target="#carouselImages" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach ($imagenes as $index => $imagen)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset($imagen) }}" class="d-block w-100" alt="Imagen del hotel">
                            </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>

                <!-- Carrusel de Reseñas y Mapa -->
                <div class="col-md-4 d-flex flex-column" style="height: 100%;">
                    <!-- Carrusel de Reseñas (mitad del alto del carrusel) -->
                    <div id="carouselReviews" class="carousel slide mb-2" data-bs-ride="carousel" style="flex: 1;">
                        <div class="carousel-inner">
                            @foreach ($resenas as $index => $resena)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }} p-3 border rounded" style="background-color: #f9f9f9;">
                                <h5 class="mb-1"><i class="fa-solid fa-user"></i> <strong>{{ $resena->nombre_cliente }}</strong></h5>
                                <p class="mb-1" style="color: black;"><i class="fa-solid fa-comment"></i> "{{ $resena->texto }}"</p>
                                <br>
                                <p class="mb-1" style="color: black;"><strong>Puntuación: </strong>{{ $resena->puntuacion }}/10</p>
                                <small class="text-muted">Fecha: {{ $resena->fecha }}</small>
                            </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselReviews" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselReviews" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>

                    <!-- Div del Mapa (mitad inferior) -->
                    <div id="mapContainer" class="border rounded" style="flex: 1; background-color: #e9ecef; height: 400px;">
                        <!-- Aquí puedes insertar el código del mapa, como Google Maps o OpenStreetMap -->
                    </div>
                </div>
            </div>
        </div>
        <br>
        <form id="reservaForm" action="{{ route('guardarreserva') }}" method="POST" class="hotel-booking-form" style="max-width: 600px; margin: 0 auto;">
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
                    <button id="validar-fechas" type="button" class="btn btn-primary">Reservar servicios</button>
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
        <br>
    </main>
    <!-- Footer -->
    <footer class="page-footer text-left text-sm-left">
        <div class="shell-wide">
            <div class="page-footer-minimal">
                <div class="shell-wide">
                    <div class="range range-50">
                        <div class="cell-sm-6 cell-md-3 cell-lg-4 wow fadeInUp" data-wow-delay=".1s">
                            <div class="page-footer-minimal-inner">
                                <h4>Copyright</h4>
                                <ul class="list-unstyled">
                                    <li>
                                        <p class="rights"><span>&copy;&nbsp;</span><span>2024</span><span>&nbsp;</span><span class="copyright-year"></span>AlojaDirecto. Todos los derechos reservados. Diseñado por <a href="#">AlojaDirecto.com</a></p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="cell-sm-6 cell-md-5 cell-lg-4 wow fadeInUp" data-wow-delay=".2s">
                            <div class="page-footer-minimal-inner">
                                <h4>Dirección</h4>
                                <ul class="list-unstyled">
                                    <li>
                                        <dl class="list-desc">
                                            <dt><span class="icon icon-sm mdi mdi-map-marker"></span></dt>
                                            <dd><a class="link link-gray-darker" href="#">Villanueva de los Castillejos, Huelva, 21540 (España)</a></dd>
                                        </dl>
                                    </li>
                                    <li>
                                        <dl class="list-desc">
                                            <dt><span class="icon icon-sm mdi mdi-phone"></span></dt>
                                            <dd class="text-gray-darker">Llámanos: <a class="link link-gray-darker" href="tel:#">+34 612345678</a>
                                            </dd>
                                        </dl>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="cell-sm-8 cell-md-4 wow fadeInUp" data-wow-delay=".3s">
                            <div class="page-footer-minimal-inner-subscribe">
                                <h4>Suscríbete a nuestra Newsletter</h4>
                                <!-- RD Mailform-->
                                <form class="rd-mailform rd-mailform-inline form-center" data-form-output="form-output-global" data-form-type="subscribe" method="post" action="#">
                                    <div class="form-wrap">
                                        <input class="form-input" id="subscribe-email" type="email" name="email">
                                        <label class="form-label" for="subscribe-email">Introduce tu e-mail</label>
                                    </div>
                                    <button class="button button-primary-2 button-effect-ujarak button-square" type="submit"><span>Subscribirse</span></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="range range-50 text-center mt-4">
                        <div class="cell-sm-12">
                            <h4>Síguenos en</h4>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="https://twitter.com" target="_blank" class="icon icon-lg mdi mdi-twitter"></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://instagram.com" target="_blank" class="icon icon-lg mdi mdi-instagram"></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://facebook.com" target="_blank" class="icon icon-lg mdi mdi-facebook"></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
<!-- Bootstrap JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('mapContainer').setView([40.4168, -3.7038], 13); // Coordenadas de Madrid, España

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([40.4168, -3.7038]).addTo(map) // Coordenadas de Madrid, España
            .bindPopup('Ubicación en Madrid, España.')
            .openPopup();

        document.getElementById('reservaForm').addEventListener('submit', function(event) {
            var submitButton = document.getElementById('guardar-reserva');
            submitButton.disabled = true;
            submitButton.innerText = 'Enviando...'; // Cambia el texto del botón para indicar que se está enviando
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS -->
<script src="{{ asset('js/realizar-reserva/js.js') }}"></script>
<script src="{{ asset('js/inicio/core.min.js') }}"></script>
<script src="{{ asset('js/inicio/script.js') }}"></script>

</html>