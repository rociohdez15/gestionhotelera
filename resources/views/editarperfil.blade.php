<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Editar Perfil</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../../css/editar-perfil/styles.css">
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
                <li><a href="#">Mis Reseñas</a></li>
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
        <div class="container">
            <br>
            @if ($errors->any())
            <div class="alert alert-danger ml-2" style="max-width: 400px; margin: 0 auto;">
                @foreach ($errors->all() as $error)
                {{ $error }}
                @endforeach
            </div>
            <br>
            @endif

            <!-- Mostrar mensaje de éxito -->
            @if(session('status'))
            <div class="alert alert-success" style="max-width: 400px; margin: 0 auto;">
                {{ session('status') }}
            </div>
            @endif

            <br>
            <h2 class="text-center">Editar perfil</h2>

            <form action="{{ route('editarPerfil', ['clienteID' => $cliente->clienteID, 'id' => $usuario->id]) }}" method="post" class="needs-validation" novalidate style="max-width: 400px; margin: 0 auto;">
                @csrf

                <!-- clienteID (campo oculto) -->
                <input type="hidden" name="clienteID" value="{{ $cliente->clienteID }}">

                <!-- Nombre -->
                <div class="form-group">
                    <label for="nombre" class="form-label text-start d-block">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $cliente->nombre }}" required>
                    <div class="invalid-feedback">Por favor ingresa tu nombre.</div>
                </div>
                <br>

                <!-- Apellidos -->
                <div class="form-group">
                    <label for="apellidos" class="form-label text-start d-block">Apellidos:</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" value="{{ $cliente->apellidos }}" required>
                    <div class="invalid-feedback">Por favor ingresa tus apellidos.</div>
                </div>
                <br>

                <!-- Dirección -->
                <div class="form-group">
                    <label for="direccion" class="form-label text-start d-block">Dirección:</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $cliente->direccion }}" required>
                    <div class="invalid-feedback">Por favor ingresa tu dirección.</div>
                </div>
                <br>

                <!-- Teléfono -->
                <div class="form-group">
                    <label for="telefono" class="form-label text-start d-block">Teléfono:</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $cliente->telefono }}" required>
                    <div class="invalid-feedback">Por favor ingresa tu teléfono.</div>
                </div>
                <br>

                <!-- DNI -->
                <div class="form-group">
                    <label for="dni" class="form-label text-start d-block">DNI:</label>
                    <input type="text" class="form-control" id="dni" name="dni" value="{{ $cliente->dni }}" required>
                    <div class="invalid-feedback">Por favor ingresa tu DNI.</div>
                </div>
                <br>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label text-start d-block">Correo electrónico:</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $cliente->email }}" required>
                    <div class="invalid-feedback">Por favor ingresa tu correo electrónico.</div>
                </div>
                <br>

                <!-- Botón de envío -->
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary w-30 me-3">Guardar cambios</button>
                    <a href="{{ route('informacionusuario') }}" class="btn btn-secondary w-30">Cancelar</a>
                </div>
                <br>
                <br>
            </form>
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
<script src="../../js/informacion-usuario/js.js"></script>

</html>