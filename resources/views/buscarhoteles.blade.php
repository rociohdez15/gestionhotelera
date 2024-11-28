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
    <link rel="stylesheet" href="../../css/buscar-hoteles/styles.css">
    <link rel="stylesheet" href="{{ asset('css/inicio/style.css') }}">
    <!-- Favicon -->
    <link rel="icon" href="../../images/inicio/favicon.ico" type="image/x-icon">
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
                                <a href="{{ route('login') }}" class="text-italic">Acceso a administración</a>
                            </div>
                        </div>
                        @endguest
                        <div class="center-side">
                            <!-- RD Navbar Brand-->
                            <div class="rd-navbar-brand fullwidth-brand">
                                <a class="brand-name" href="{{ route('inicio') }}">
                                    <img class="brand-logo img-fluid" src="../../images/inicio/logo.png" alt="Aloja Directo">
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
                                    @php
                                    $rolUsuario = Auth::user()->rolID;
                                    @endphp
                                    @if ($rolUsuario !== 2)
                                    <a href="{{ route('informacionusuario') }}" class="btn btn-secondary user-name icono-user d-flex align-items-center" id="dropdownMenuButton" role="button" aria-expanded="false">
                                        {{ Auth::user()->name }} {{ Auth::user()->apellido }}
                                        <i class="fa-solid fa-user ms-2"></i>
                                    </a>
                                    @endif
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
                        <div class="rd-navbar-brand mobile-brand"><a class="brand-name" href="index.html"><img src="../../images/inicio/logo.png" alt="" width="314" height="48" /></a></div>
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
                                    <li><a href="{{ route('cargarSobreNosotros') }}">Sobre Nosotros</a></li>
                                    <li><a href="{{ route('cargarContacto') }}">Contacto</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        @auth
        @php
        $rolUsuario = Auth::user()->rolID; // Suponiendo que 'rolID' es el campo que contiene el rol
        @endphp

        @if ($rolUsuario === 2) <!-- Si el rol del usuario es recepcionista -->
        <script>
            // Redirigir al panel de recepcionista
            window.location.href = "{{ route('panelrecepcionista') }}";
        </script>
        @else <!-- Si el rol del usuario es otro -->
        <script>
            // Redirigir al inicio
            window.location.href = "#";
        </script>
        @endif
        @endauth
    </header>

    <main>
        <br>
        <h3 class="text-center">Búsqueda de hoteles</h3>
        <br>
        <!-- Buscador de alojamientos -->
        <div class="cell-lg-4 cell-xl-6 d-flex flex-column mx-auto" style="max-width: 900px; padding: 0 15px;">
            <div class="hotel-booking-form flex-grow-1">
                <form action="{{ route('buscarHoteles') }}" method="GET" data-form-output="form-output-global" novalidate>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-wrap">
                                <label for="destino">Destino</label>
                                <input type="text" class="form-control" id="ubicacion" placeholder="Ubicación" name="ubicacion" list="dList">
                                <datalist id="dList"></datalist>
                                <div class="invalid-feedback" id="ubicacion-error" style="display: none;">
                                    Por favor, introduzca una ciudad o un nombre de hotel.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="fecha-entrada">Fecha de entrada</label>
                            <input type="date" id="fecha-entrada" name="fecha_entrada" class="form-control">
                            <div class="invalid-feedback">Por favor, introduzca la fecha de entrada.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="fecha-salida">Fecha de salida</label>
                            <input type="date" id="fecha-salida" name="fecha_salida" class="form-control">
                            <div id="fecha-salida-error" class="invalid-feedback">
                                Por favor, introduzca una fecha de salida posterior a la fecha de entrada.
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="adultos">Adultos</label>
                            <input type="number" id="adultos" name="adultos" value="2" min="1" class="form-control">
                            <div class="invalid-feedback" id="adultos-error">
                                Por favor, introduzca al menos un adulto.
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="ninos">Niños</label>
                            <input type="number" id="ninos" name="ninos" value="0" min="0" class="form-control">
                            <div class="invalid-feedback" id="ninos-error">
                                Por favor, introduzca al menos un niño si no hay adultos.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="habitaciones">Habitaciones</label>
                            <input type="number" id="habitaciones" name="habitaciones" value="1" min="1" class="form-control">
                        </div>

                        <div class="col-12">
                            <div id="edades-de-ninos" class="campo-edad"></div><br>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
        @if ($totalAlojamientos > 0)
        <div class="contenedor-alojamientos">
            <p><strong>{{ $ubicacion }}: {{ $totalAlojamientos }} alojamientos encontrados</strong></p>
        </div>
        @else
        <div class="contenedor-alojamientos">
            <p><strong>No se encontraron alojamientos en el destino introducido.</strong></p>
        </div>
        @endif
        <br>

        @if ($datos && count($datos) > 0)
        @foreach ($datos as $hotel)
        <div class="container">
            <div class="row align-items-start mb-3 mostrar-alojamientos border rounded bg-light">
                <!-- Imagen del hotel -->
                <div class="col-12 col-md-4 hotel-imagen mb-3 mb-md-0">
                    @if ($hotel->imagen_url)
                    <img src="{{ $hotel->imagen_url }}" alt="{{ $hotel->nombre }}" class="img-fluid border rounded bg-light ">
                    @endif
                </div>

                <!-- Información del hotel -->
                <div class="col-12 col-md-8 hotel-info ">
                    <h5>{{ $hotel->nombre }}</h5>
                    <p><strong>Descripción: </strong>{{ $hotel->descripcion }}</p>
                    <p style="line-height: 0;"><strong>Dirección: </strong>{{ $hotel->direccion }}</p>

                    <!-- Mostrar habitaciones disponibles -->
                    @if ($hotel->habitaciones && count($hotel->habitaciones) > 0)
                    <div class="habitaciones-disponibles">
                        <ul>
                            <br>
                            <strong>Número de habitaciones: {{ count($hotel->habitaciones) }}</strong>
                            @foreach ($hotel->habitaciones as $index => $habitacion)
                            <li>Habitación {{ $index + 1 }}: Capacidad: {{ $habitacion->tipohabitacion }} personas - Precio: {{ number_format($habitacion->precio, 2) }}€/Noche</li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-end">
                            <form action="{{ route('reservar') }}" method="GET">
                                <input type="hidden" name="hotelID" value="{{ $hotel->hotelID }}">
                                <input type="hidden" name="fechaEntrada" value="{{ $fechaEntrada }}">
                                <input type="hidden" name="fechaSalida" value="{{ $fechaSalida }}">
                                <input type="hidden" name="adultos" value="{{ $num_adultos }}">
                                <input type="hidden" name="ninos" value="{{ $num_ninos }}">
                                <input type="hidden" name="ubicacion" value="{{ $ubicacion }}">
                                <input type="hidden" name="clienteID" value="{{ Auth::id() }}">
                                @foreach($hotel->habitaciones as $habitacion)
                                <input type="hidden" name="habitacionID[]" value="{{ $habitacion->habitacionID }}">
                                @endforeach
                                <input type="hidden" name="precioHabitacion" value="{{ $habitacion->precio }}">
                                @foreach(range(1, $num_ninos) as $index)
                                <input type="hidden" name="edadesNinos[]" value="{{ request()->input('edad-nino-' . $index) }}">
                                @endforeach
                                <button type="submit" class="btn btn-primary reservar mt-3">Reservar</button>
                            </form>
                        </div>
                    </div>
                    @else
                    <p>No hay habitaciones disponibles que cumplan con los requisitos.</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @endif



        <br>
        <!-- Paginación -->
        <div class="container text-center" style="color: black;">
            <p>
                Página {{ $pagina_actual }} de {{ $total_paginas }} | Mostrar {{ $registros_por_pagina }} registros por página | Ir a página:
                @for ($i = 1; $i <= $total_paginas; $i++)
                    <a href="{{ route('buscarHoteles', array_merge(request()->except('pagina'), ['pagina' => $i])) }}">{{ $i }}</a>
                    @endfor
            </p>
        </div>

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
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/buscar-hoteles/js.js"></script>
<script src="{{ asset('js/inicio/core.min.js') }}"></script>
<script src="{{ asset('js/inicio/script.js') }}"></script>

</html>