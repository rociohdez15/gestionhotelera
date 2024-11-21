<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Listado de Reservas</title>
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
                                    @auth
                                    @php
                                    $rolUsuario = Auth::user()->rolID;
                                    @endphp
                                    @if ($rolUsuario === 2)
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Reservas
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <li><a class="dropdown-item" href="{{ route('listarReservas') }}">Lista de reservas</a></li>
                                            <li><a class="dropdown-item" href="{{ route('listadoCheckin') }}">Realizar Check-In</a></li>
                                            <li><a class="dropdown-item" href="{{ route('listadoCheckout') }}">Realizar Check-Out</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{ route('dispHabitaciones') }}">Estadísticas</a></li>
                                    <li><a href="{{ route('listarServicios') }}">Servicios</a></li>
                                    @else
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
                                    @endif
                                    @endauth
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
            <h3 class="text-center">LISTADO DE RESERVAS</h3>

            <br>
            <!-- Buscador -->
            <form action="{{ route('buscarReservas') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="Buscar por ID, cliente, hotel, adultos o niños" aria-label="Buscar reservas">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>

            <!-- Muestra la tabla de reservas -->
            <div class="table-responsive mx-auto">
                <table class="table table-bordered table-striped text-center" id="tabla-reservas">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Hotel</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Nº Habitación</th>
                            <th>Precio Total Reserva</th>
                            <th>Nº Adultos</th>
                            <th>Nº Niños</th>
                            <th>Operaciones</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>

                </table>
                <div id="paginacion" class="text-center" style="color: black; margin-top: 20px;">
                    <!-- Los enlaces de paginación se agregarán aquí dinámicamente -->
                </div>

                <script>
    $(document).ready(function() {
        var currentPage = 1;
        var currentQuery = '';

        function actualizarTabla(page = 1, query = '') {
            $.ajax({
                url: '{{ route("buscarReservas") }}',
                method: 'GET',
                data: {
                    page: page,
                    query: query
                },
                success: function(response) {
                    currentPage = page;
                    currentQuery = query;

                    var tabla = $('#tabla-reservas tbody');
                    tabla.empty();
                    $.each(response.data, function(index, reserva) {
                        var pdfUrl = '{{ route("generar_pdf_listar_reservas", ":reservaID") }}';
                        pdfUrl = pdfUrl.replace(':reservaID', reserva.reservaID);

                        tabla.append(
                            '<tr>' +
                            '<td>' + reserva.reservaID + '</td>' +
                            '<td>' + reserva.nombre + ', ' + reserva.apellidos + '</td>' +
                            '<td>' + reserva.hotel_nombre + '</td>' +
                            '<td>' + reserva.fechainicio + '</td>' +
                            '<td>' + reserva.fechafin + '</td>' +
                            '<td>' + reserva.numhabitacion + '</td>' +
                            '<td>' + reserva.preciototal + '€</td>' +
                            '<td>' + reserva.num_adultos + '</td>' +
                            '<td>' + reserva.num_ninos + '</td>' +
                            '<td class="d-flex justify-content-center gap-2">' +
                            '<button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal' + reserva.reservaID + '">' +
                            '<i class="fa-solid fa-trash"></i>' +
                            '</button>' +
                            '<div class="modal fade" id="confirmDeleteModal' + reserva.reservaID + '" tabindex="-1" aria-labelledby="confirmDeleteModalLabel' + reserva.reservaID + '" aria-hidden="true">' +
                            '<div class="modal-dialog">' +
                            '<div class="modal-content">' +
                            '<div class="modal-header">' +
                            '<h5 class="modal-title" id="confirmDeleteModalLabel' + reserva.reservaID + '">Confirmar eliminación</h5>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                            '</div>' +
                            '<div class="modal-body">¿Estás seguro de que deseas eliminar esta reserva?</div>' +
                            '<div class="modal-footer">' +
                            '<form action="{{ route("delReserva", "") }}/' + reserva.reservaID + '" method="POST">' +
                            '@csrf' +
                            '@method("DELETE")' +
                            '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancelar</button>' +
                            '<button type="submit" class="btn btn-danger">Eliminar</button>' +
                            '</form>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#confirmEditModal' + reserva.reservaID + '">' +
                            '<i class="fa-solid fa-pen-to-square"></i>' +
                            '</button>' +
                            '<div class="modal fade" id="confirmEditModal' + reserva.reservaID + '" tabindex="-1" aria-labelledby="confirmEditModalLabel' + reserva.reservaID + '" aria-hidden="true">' +
                            '<div class="modal-dialog">' +
                            '<div class="modal-content">' +
                            '<div class="modal-header">' +
                            '<h5 class="modal-title" id="confirmEditModalLabel' + reserva.reservaID + '">Confirmar editar</h5>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                            '</div>' +
                            '<div class="modal-body">¿Estás seguro de que deseas editar esta reserva?</div>' +
                            '<div class="modal-footer">' +
                            '<form action="{{ route("mostrarReserva", "") }}/' + reserva.reservaID + '" method="POST">' +
                            '@csrf' +
                            '@method("GET")' +
                            '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancelar</button>' +
                            '<button type="submit" class="btn btn-danger">Editar</button>' +
                            '</form>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '<a href="' + pdfUrl + '" class="btn btn-success btn-sm">' +
                            '<i class="fa-solid fa-file-pdf"></i>' +
                            '</a>' +
                            '</td>' +
                            '</tr>'
                        );
                    });

                    // Construir enlaces de paginación
                    var enlacesPaginacion = $('#paginacion');
                    enlacesPaginacion.empty();
                    enlacesPaginacion.append(
                        '<span>Página ' + response.current_page + ' de ' + response.last_page + ' | Mostrar ' + response.per_page + ' registros por página | Ir a página: </span>'
                    );
                    for (var i = 1; i <= response.last_page; i++) {
                        enlacesPaginacion.append(
                            '<a href="#" class="page-link" data-page="' + i + '" style="color: black; margin: 0 5px; display: inline-block;">' + i + '</a>'
                        );
                    }

                    // Añadir evento click a los enlaces de paginación
                    $('.page-link').click(function(e) {
                        e.preventDefault();
                        var page = $(this).data('page');
                        actualizarTabla(page, currentQuery);
                    });
                }
            });
        }

        // Llama a la función para actualizar la tabla inicialmente
        actualizarTabla();

        // Llama a la función para actualizar la tabla cada 5 segundos
        setInterval(function() {
            actualizarTabla(currentPage, currentQuery);
        }, 5000);

        // Manejar el evento de envío del formulario de búsqueda
        $('form').submit(function(e) {
            e.preventDefault();
            var query = $('input[name="query"]').val();
            actualizarTabla(1, query);
        });
    });
</script>
            </div>
            <br>
            <div class="text-center">
                <a href="{{ route('generar_pdf_listar_reservas_total') }}" class="btn btn-primary btn-sm">
                    Generar listado reservas
                </a>
            </div>
            <br>
        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            // Eliminar el parámetro de consulta 'success' de la URL
            const url = new URL(window.location);
            url.searchParams.delete('success');
            window.history.replaceState({}, document.title, url);

            // Ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 2500);
        }

        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            // Eliminar el parámetro de consulta 'error' de la URL
            const url = new URL(window.location);
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url);

            // Ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 2500);
        }
    });
</script>

</html>