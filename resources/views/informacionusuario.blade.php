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
    <link rel="stylesheet" href="../../css/informacion-usuario/styles.css">
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
                <li><a href="{{ route ('mostrarResenas', ['clienteID' => Auth::id()]) }}">Mis Reseñas</a></li>
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
        <h2 class="titulo">INFORMACIÓN DEL USUARIO</h2>
        <br>
        <div class="container-fluid px-2 container">
            <div class="row g-1">
                <!-- Cuadro izquierdo -->
                <div class="col-md-3 d-flex justify-content-center align-items-center">
                    <div class="cuadrado d-flex flex-column align-items-center">
                        <div class="icono-user2">
                            <i class="fas fa-user"></i>
                            <h6>{{ $cliente->apellidos}}, {{ $cliente->nombre }}</h6>
                            <a href="{{ route ('editarperfil', ['clienteID' => Auth::user()->id ] , ['id' => Auth::user()->id ]) }}" class="btn btn-primary boton-perfil" role="button">Editar perfil</a>
                            <p class="informacion-personal"><strong>Información Personal</strong></p>
                            <h6 class="email"><strong>Dirección de correo: </strong>{{ $cliente->email}}</h6>
                            <h6 class="domicilio"><strong>Domicilio: </strong>{{ $cliente->direccion}}</h6>
                            <h6 class="tlf"><strong>Telefono: </strong>{{ $cliente->telefono}}</h6>
                            <h6 class="dni"><strong>DNI: </strong>{{ $cliente->dni}}</h6>
                        </div>
                        <div>

                        </div>
                    </div>
                </div>
                <!-- Cuadros centrales -->
                <div class="col-md-6">
                    <div class="d-flex flex-column justify-content-center align-items-start">
                        <div class="cuadrado-central mb-1">
                            <a href="{{ route ('editarperfil', ['clienteID' => Auth::user()->id ] , ['id' => Auth::user()->id ]) }}" class="enlace-perfil">Editar perfil</a>
                            <h6 class="detalles-usuario"><strong>Detalles de usuario</strong></h6>
                            <h6 class="nombre"><strong>Nombre: </strong>{{ $cliente->nombre}}</h6>
                            <h6 class="apellidos"><strong>Apellidos: </strong>{{ $cliente->apellidos}}</h6>
                            <h6 class="email2"><strong>Correo: </strong>{{ $cliente->email}}</h6>
                        </div>
                        <div class="cuadrado-central">
                            <a href="#" class="enlace-perfil">Ver Mis Reservas</a>
                            <h6 class="detalles-usuario"><strong>Mis Reservas</strong></h6>
                            @if($reservas->isEmpty())
                            <p>No hay reservas para este cliente.</p>
                            @else
                            <table class="tabla-reservas">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Check-IN</th>
                                        <th>Check-OUT</th>
                                        <th>Dias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservas as $reserva)
                                    <tr>
                                        <td>{{ $reserva->reservaID }}</td>
                                        <td>{{ $reserva->fecha_checkin }}</td>
                                        <td>{{ $reserva->fecha_checkout }}</td>
                                        <td>{{ $reserva->num_dias }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Cuadro derecho -->
                <div class="col-md-3 d-flex justify-content-center align-items-center">
                    <div class="cuadrado">
                        <h6 class="resenas"><strong>Mis Reseñas</strong></h6>
                        @if($resenas->isEmpty())
                        <p>No hay reseñas de este cliente.</p>
                        @else
                        <div class="reseñas-container">
                            @foreach ($resenas as $resena)
                            <div class="contenedor-resena">
                                <img src="{{ asset($resena->imagen_portada) }}" alt="Imagen del hotel" class="imagen-resena" />
                                <div>
                                    <h6 class="nombre-hotel">Hotel: {{ $resena->nombre }}</h6>
                                    <p class="texto-resena">{{ $resena->texto }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route ('mostrarResenas', ['clienteID' => Auth::id()]) }}" class="enlace-perfil enlace-resena">Ver Mis Reseñas</a>
                        <br>
                        <a href="{{ route ('dejarResenas') }}" class="enlace-perfil enlace-dejaresena">Dejar Reseñas</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <br>
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