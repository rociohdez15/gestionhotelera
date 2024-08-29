<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Inicio</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../../css/inicio/styles.css">
</head>

<body>
    <header>
        <!-- Logo -->
        <img src="../../images/inicio/logo.png" alt="Logo de Aloja Directo" class="logo-cabecera">
        
        <!-- Botón de usuario para iniciar sesión -->
        <a href="#" class="btn btn-secondary icono-user" id="dropdownMenuButton" role="button" aria-expanded="false">
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
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="#">
                    <i class="fas fa-headset"></i> Iniciar sesión como Recepcionista
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-user-shield"></i> Iniciar sesión como Administrador
                    </a>
                </li>
            </ul>
        </div>
       
    </header>
    
    <main>
        <!-- Cabecera -->
        <div class="contenedor-titulos">
            <h1 class="titulo">Encuentra tu próximo alojamiento</h1>
            <p class="eslogan">Alojamientos que hacen que cada momento cuente.</p>
        </div>

        <!-- Buscador de alojamientos -->
        <form action="{{ route('buscarHoteles') }}" method="GET" class="container" novalidate>
            <div class="contenedor-busqueda">
                <input type="text" class="form-control mb-3" id="ubicacion" placeholder="Ubicación" name="ubicacion" list="dList">
                <datalist id="dList">
                </datalist>
                <div class="invalid-feedback" id="ubicacion-error" style="display: none;">
                    Por favor, introduzca una ciudad o un nombre de hotel.
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fecha-entrada">Fecha de entrada</label>
                        <input type="date" id="fecha-entrada" name="fecha_entrada" class="form-control form-control2">
                        <div class="invalid-feedback">
                            Por favor, introduzca la fecha de entrada.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha-salida">Fecha de salida</label>
                        <input type="date" id="fecha-salida" name="fecha_salida" class="form-control form-control2">
                        <div class="invalid-feedback">
                            Por favor, introduzca la fecha de salida.
                        </div>
                    </div>
                </div>
                
                <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="adultos">Adultos</label>
                    <input type="number" id="adultos" name="adultos" value="2" min="1" class="form-control">
                    <div class="invalid-feedback" id="adultos-error">
                        Por favor, introduzca al menos un adulto.
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="ninos">Niños</label>
                    <input type="number" id="ninos" name="ninos" value="0" min="0" class="form-control">
                    <div class="invalid-feedback" id="ninos-error">
                        Por favor, introduzca al menos un niño si no hay adultos.
                    </div>
                </div>
                    <div class="col-md-4 mb-3">
                        <label for="habitaciones">Habitaciones</label>
                        <input type="number" id="habitaciones" name="habitaciones" value="1" min="1" class="form-control">
                    </div>
                </div>
                
                <div id="edades-de-ninos" class="campo-edad"></div><br>
                
                <button type="submit" class="btn btn-primary w-100">Buscar</button>
            </div>
        </form>

        <!-- Sección descubre España -->
        <div class="seccion-descubre">
            <h2 class="text-center">Descubre España</h2>
            <br>
            <div class="row text-center">
                <div class="col-md-3 mb-3">
                    <div class="ciudad">
                        <a href="#">
                            <img src="../../images/inicio/valencia.jpg" alt="Valencia" class="img-fluid img-ciudades">
                        </a>
                        <a href="#">
                            <p class="txt-ciudades"><strong>Valencia</strong></p>
                        </a>
                        <p>{{ $totalHotelesValencia }} alojamientos</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="ciudad">
                        <a href="#">
                            <img src="../../images/inicio/madrid.jpg" alt="Madrid" class="img-fluid img-ciudades">
                        </a>
                        <a href="#">
                            <p class="txt-ciudades"><strong>Madrid</strong></p>
                        </a>
                        <p>{{ $totalHotelesMadrid }} alojamiento</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="ciudad">
                        <a href="#">
                            <img src="../../images/inicio/menorca.jpg" alt="Menorca" class="img-fluid img-ciudades">
                        </a>
                        <a href="#">
                            <p class="txt-ciudades"><strong>Menorca</strong></p>
                        </a>
                        <p>{{ $totalHotelesMenorca }} alojamientos</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="ciudad">
                        <a href="#">
                            <img src="../../images/inicio/sevilla.jpg" alt="Sevilla" class="img-fluid img-ciudades">
                        </a>
                        <a href="#">
                            <p class="txt-ciudades"><strong>Sevilla</strong></p>
                        </a>
                        <p>{{ $totalHotelesSevilla }} alojamientos</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer>
        <a href="#">Términos y Condiciones</a> | 
        <a href="#">Sobre AlojaDirecto.com</a>
        <p>Copyright © 2024 AlojaDirecto.com<br>Todos los derechos reservados</p>
    </footer>
</body>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/inicio/js.js"></script>
</html>









