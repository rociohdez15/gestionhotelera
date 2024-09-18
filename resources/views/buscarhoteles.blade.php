
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
    <!-- Favicon -->
    <link rel="icon" href="../../images/inicio/favicon.ico" type="image/x-icon">
</head>
<body>
    <header>
        <!-- Logo -->
        <a href="{{ route('inicio') }}">
            <img src="../../images/inicio/logo.png" alt="Logo de Aloja Directo" class="logo-cabecera">
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
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('login') }}">
                    <i class="fas fa-headset"></i> Iniciar sesión como Recepcionista
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('login') }}">
                        <i class="fas fa-user-shield"></i> Iniciar sesión como Administrador
                    </a>
                </li>
            </ul>
        </div>
        @endguest

        @auth
        <a href="{{ route('logout') }}" class="btn btn-secondary icono-user" id="dropdownMenuButton" role="button" aria-expanded="false">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>   
        @endauth
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
                        <div id="fecha-salida-error" class="invalid-feedback">
                            Por favor, introduzca una fecha de salida posterior a la fecha de entrada.
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

        @if ($totalAlojamientos > 0)
            <div class="contenedor-alojamientos">
                <p><strong>{{ $ubicacion }}: {{ $totalAlojamientos }} alojamientos encontrados</strong></p>
            </div>
        @else
            <div class="contenedor-alojamientos">
                <p><strong>No se encontraron alojamientos en el destino introducido.</strong></p>
            </div>
        @endif

        @if ($datos && count($datos) > 0)
            @foreach ($datos as $hotel)
            <div class="container">
                <div class="row align-items-start mb-3 mostrar-alojamientos">
                    <!-- Imagen del hotel -->
                    <div class="col-12 col-md-4 hotel-imagen mb-3 mb-md-0">
                        @if ($hotel->imagen_url)
                            <img src="{{ $hotel->imagen_url }}" alt="{{ $hotel->nombre }}" class="img-fluid rounded imagen-portada-alojamiento">
                        @endif
                    </div>

                    <!-- Información del hotel -->
                    <div class="col-12 col-md-8 hotel-info">
                        <h5>{{ $hotel->nombre }}</h5>
                        <p>{{ $hotel->descripcion }}</p>
                        <p>{{ $hotel->direccion }}</p>
                        
                        <!-- Mostrar habitaciones disponibles -->
                        @if ($hotel->habitaciones && count($hotel->habitaciones) > 0)
                            <div class="habitaciones-disponibles">
                                <ul>
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
        <div class="container text-center">
            <p>
                Página {{ $pagina_actual }} de {{ $total_paginas }} | Mostrar {{ $registros_por_pagina }} registros por página | Ir a página:
                @for ($i = 1; $i <= $total_paginas; $i++)
                <a href="{{ route('buscarHoteles', array_merge(request()->except('pagina'), ['pagina' => $i])) }}">{{ $i }}</a>
                @endfor
            </p>
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
<script src="../../js/buscar-hoteles/js.js"></script>
</html>
