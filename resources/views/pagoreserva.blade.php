<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Pago de Reserva</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../../css/realizar-reserva/styles.css">
    <link rel="stylesheet" href="../../css/inicio/style.css">
    <!-- Favicon -->
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:400,700,400italic%7CPoppins:300,400,500,700">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
    <link rel="icon" href="../../images/inicio/favicon.ico" type="image/x-icon">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        .form-container {
            max-width: 800px;
            margin: auto;
        }

        .form-label {
            display: flex;
            align-items: center;
        }

        .scrollable-select {
            max-height: 200px !important;
            overflow-y: auto !important;
        }
    </style>
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
        $rolUsuario = Auth::user()->rolID; 
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

    <br>
    <main>
        <div class="cell-lg-4 cell-xl-6 d-flex flex-column mx-auto" style="max-width: 900px; padding: 0 15px;">
            <div class="hotel-booking-form flex-grow-1">
                <h3 class="text-center">Pago de la reserva</h3>
                <form class="needs-validation" novalidate="" action="{{ route('exitoreserva', ['reservaIDs' => implode(',', $reservaIDsArray)]) }}">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-wrap">
                                <label for="firstName">Nombre</label>
                                <input type="text" class="form-control" id="firstName" placeholder="" value="" required="">
                                <div class="invalid-feedback">
                                    Se requiere un nombre válido.
                                </div>
                                @if (!empty($reservaIDs))
                                <input type="hidden" id="reservaID" value="{{ is_array($reservaIDs) ? implode(',', $reservaIDs) : $reservaIDs }}">
                                @endif
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-wrap">
                                <label for="lastName">Apellido</label>
                                <input type="text" class="form-control" id="lastName" placeholder="" value="" required="">
                                <div class="invalid-feedback">
                                    Se requiere apellido válido.
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-wrap">
                                <label for="address">Dirección</label>
                                <input type="text" class="form-control" id="address" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Por favor introduce tu direccion de envio.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-wrap">
                                <label for="municipio">Municipio</label>
                                <input type="text" class="form-control" id="municipio" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Por favor introduce tu direccion de envio.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-wrap">
                                <label for="provincia">Provincia</label>
                                <input type="text" class="form-control" id="provincia" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Por favor introduce tu direccion de envio.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-wrap">
                                <label for="zip">Código postal</label>
                                <input type="text" class="form-control" id="zip" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Código postal requerido.
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h4 class="mb-3">Pago</h4>
                            <div class="my-3">
                                <div class="form-check">
                                    <input id="credit" name="paymentMethod" type="radio" class="form-check-input" checked="" required="">
                                    <label class="form-check-label" for="credit">Tarjeta de crédito</label>
                                </div>
                                <div class="form-check">
                                    <input id="debit" name="paymentMethod" type="radio" class="form-check-input" required="">
                                    <label class="form-check-label" for="debit">Tarjeta de débito</label>
                                </div>
                                <div class="form-check">
                                    <input id="paypal" name="paymentMethod" type="radio" class="form-check-input" required="">
                                    <label class="form-check-label" for="paypal">PayPal</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label for="cc-name">Nombre en la tarjeta</label>
                                <input type="text" class="form-control" id="cc-name" placeholder="" required="">
                                <small class="text-muted">Nombre completo como se muestra en la tarjeta</small>
                                <div class="invalid-feedback">
                                    Se requiere el nombre en la tarjeta
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label for="cc-number">Número de tarjeta de crédito</label>
                                <input type="text" class="form-control" id="cc-number" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Se requiere número de tarjeta de crédito
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-wrap">
                                <label for="cc-expiration">Vencimiento</label>
                                <input type="text" class="form-control" id="cc-expiration" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Fecha de vencimiento requerida
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-wrap">
                                <label for="cc-cvv">CVV</label>
                                <input type="text" class="form-control" id="cc-cvv" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Código de seguridad requerido
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit">Continuar con el pago</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            (function() {
                'use strict'


                var forms = document.querySelectorAll('.needs-validation')

                Array.prototype.slice.call(forms)
                    .forEach(function(form) {
                        form.addEventListener('submit', function(event) {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }

                            form.classList.add('was-validated')
                        }, false)
                    })
            })()
        </script>
    </main>

    <script src="/docs/5.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="form-validation.js"></script>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS -->
<script src="../../js/realizar-reserva/js.js"></script>
<script src="../../js/inicio/core.min.js"></script>
<script src="../../js/inicio/script.js"></script>


</html>