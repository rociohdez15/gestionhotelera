
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlojaDirecto | Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('../../css/login/styles.css') }}">
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
            <div class="contenedor-titulos">
                @if ($errors->any())
                    <div class="alert alert-danger ml-2">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                    <br>
                @endif

                <!-- Mostrar mensaje de éxito -->
                @if(session('status'))
                    <div class="alert alert-success">
                            {{ session('status') }}
                    </div>
                @endif

                <h2 class="text-center">Iniciar sesión</h2>
                <form action="{{ route('login') }}" method="post" class="needs-validation" novalidate style="max-width: 400px; margin: 0 auto;">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label text-start d-block">Correo electrónico:</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        <div class="invalid-feedback">Por favor ingresa tu correo electrónico.</div>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="password" class="form-label text-start d-block">Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                        <div class="invalid-feedback">Por favor ingresa tu contraseña.</div>
                    </div>
                    <br>
                    <div>
                        <a href="#">¿Has olvidado tu contraseña?</a>
                    </div>
                    <br>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary w-30 me-5">Entrar</button>
                        <a href="{{ route('register') }}" class="btn btn-primary w-30">Registrarse</a>
                    </div>
                </form>
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
    <script src="../../js/login/js.js"></script>
</html>
