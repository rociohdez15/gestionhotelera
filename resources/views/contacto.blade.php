<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Contacto</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../../css/listar-reservas/styles.css">
    <link rel="stylesheet" href="../../css/inicio/style.css">
    <!-- Favicon -->
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:400,700,400italic%7CPoppins:300,400,500,700">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
    <link rel="icon" href="../../images/inicio/favicon.ico" type="image/x-icon">
    <!-- Agrega Vue.js -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.js"></script>
    <!-- Agrega Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <!-- Breadcrumbs & Page title-->
        <section class="section-md text-center bg-image breadcrumbs-01 imagen-contacto">
            <div class="shell shell-fluid">
                <div class="range range-xs-center">
                    <div class="cell-xs-12 cell-xl-11">
                        <h2 class="text-white">Contacta con nosotros</h2>
                        <ul class="breadcrumbs-custom">
                            <li><a href="{{ route ('inicio') }}">Inicio</a></li>
                            <li class="active">Contacto</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section-md bg-white text-center">
            <div class="shell">
                <div class="range range-50 range-md-center">
                    <div class="cell-sm-8">
                        <div class="contact-box">
                            <h3>Contáctenos</h3>
                            <form class="rd-mailform" data-form-output="form-output-global" data-form-type="contact" method="post" action="bat/rd-mailform.php">
                                <div class="range range-sm-bottom spacing-20">
                                    <div class="cell-sm-6">
                                        <div class="form-wrap">
                                            <input class="form-input" id="contact-first-name" type="text" name="name">
                                            <label class="form-label" for="contact-first-name">Nombre</label>
                                        </div>
                                    </div>
                                    <div class="cell-sm-6">
                                        <div class="form-wrap">
                                            <input class="form-input" id="contact-last-name" type="text" name="phone">
                                            <label class="form-label" for="contact-last-name">Teléfono</label>
                                        </div>
                                    </div>
                                    <div class="cell-xs-12">
                                        <div class="form-wrap">
                                            <textarea class="form-input" id="contact-message" name="message"></textarea>
                                            <label class="form-label" for="contact-message">Mensaje</label>
                                        </div>
                                    </div>
                                    <div class="cell-sm-6">
                                        <div class="form-wrap">
                                            <input class="form-input" id="contact-email" type="email" name="email">
                                            <label class="form-label" for="contact-email">E-mail</label>
                                        </div>
                                    </div>
                                    <div class="cell-sm-6">
                                        <button class="button button-primary button-square button-block button-effect-ujarak" type="submit"><span>Enviar mensaje</span></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="cell-sm-4">
                        <aside class="contact-box-aside text-left">
                            <div class="range range-50">
                                <div class="cell-xs-6 cell-sm-12">
                                    <p class="aside-title">Redes Sociales</p>
                                    <hr class="divider divider-left divider-custom">
                                    <ul class="list-inline">
                                        <li class="list-inline-item"><a class="icon icon-sm icon-gray-3 fa fa-instagram" href="#"></a></li>
                                        <li class="list-inline-item"><a class="icon icon-sm icon-gray-3 fa fa-facebook" href="#"></a></li>
                                        <li class="list-inline-item"><a class="icon icon-sm icon-gray-3 fa fa-twitter" href="#"></a></li>
                                        <li class="list-inline-item"><a class="icon icon-sm icon-gray-3 fa fa-youtube" href="#"></a></li>
                                    </ul>
                                </div>
                                <div class="cell-xs-6 cell-sm-12">
                                    <p class="aside-title">Teléfono</p>
                                    <hr class="divider divider-left divider-custom">
                                    <div class="unit unit-middle unit-horizontal unit-spacing-xs unit-xs-top">
                                        <div class="unit__left"><span class="text-middle icon icon-sm mdi mdi-phone icon-primary"></span></div>
                                        <div class="unit__body"><a class="text-middle link link-gray-dark" href="tel:#">959999999</a></div>
                                    </div>
                                </div>
                                <div class="cell-xs-6 cell-sm-12">
                                    <p class="aside-title"> Dirección </p>
                                    <hr class="divider divider-left divider-custom">
                                    <div class="unit unit-middle unit-horizontal unit-spacing-xs unit-xs-top">
                                        <div class="unit__left"><span class="text-middle icon icon-sm mdi mdi-map-marker icon-primary"></span></div>
                                        <div class="unit__body"><a class="text-middle link link-gray-dark" href="contacts.html">Villanueva de los Castillejos, Huelva, 21540</a></div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </section>
    </main>
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
<!-- Incluye el archivo de Vue -->
<script src="{{ asset('../../vue/panelrecepcionistas/panel1.js') }}"></script>
<script src="{{ asset('js/inicio/core.min.js') }}"></script>
<script src="{{ asset('js/inicio/script.js') }}"></script>

</html>