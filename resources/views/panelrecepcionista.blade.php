<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Panel de Recepcionista</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../../css/editar-perfil/styles.css">
    <!-- Favicon -->
    <link rel="icon" href="../../images/inicio/favicon.ico" type="image/x-icon">
    <!-- Agrega Vue.js -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.js"></script>
    <!-- Agrega Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <main id="app">
        <div class="container-fluid">
            <div class="row">
                <!-- Menú Vertical Izquierdo -->
                <div class="col-md-3 col-lg-2 d-flex flex-column align-items-start border-end menu-opciones" style="min-height: 100vh;">
                    <h4 class="mt-4 txt-opciones" style="align-self: center;">Panel de opciones</h4>
                    <ul class="nav flex-column w-100 txt-listado">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Lista reservas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Check - in</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Check - out</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Habitaciones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Servicios</a>
                        </li>
                    </ul>
                </div>

                <!-- Contenido Principal -->
                <div class="col-md-9 col-lg-10 grafica">
                    <br>
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

                    <br>
                    <h2 class="text-center">PANEL DE RECEPCIONISTA</h2>

                    <!-- Gráfica de reservas -->
                    <div class="container my-4 d-flex justify-content-center">
                        <div class="card" style="width: 100%; max-width: 900px;">
                            <div class="card-header">
                                <h5 class="mb-0 text-center">Dashboard de reservas</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="graficaReservas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <a href="#">Términos y Condiciones</a> |
        <a href="#">Sobre AlojaDirecto.com</a>
        <p>Copyright © 2024 AlojaDirecto.com<br>Todos los derechos reservados</p>
    </footer>

    <script>
        window.chartData = <?php echo json_encode($data); ?>;
    </script>
</body>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Incluye el archivo de Vue -->
<script src="{{ asset('../../vue/panelrecepcionistas/panel1.js') }}"></script>

</html>