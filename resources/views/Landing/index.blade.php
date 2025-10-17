{{-- resources/views/landing/index.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Sipmainputvalue - Inicio</title>

  {{-- Google Fonts --}}
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Raleway:300,400,500,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

  {{-- Assets del landing (asegúrate de tenerlos en /public/assets ) --}}
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style-index.css') }}" rel="stylesheet">

  {{-- Template DeskApp legacy (ahora en public/legacy) --}}
  <link rel="stylesheet" href="{{ asset('legacy/vendors/styles/core.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/vendors/styles/icon-font.min.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/vendors/styles/style.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/src/plugins/jquery-steps/jquery.steps.css') }}">
  {{-- Tus estilos propios si los dejaste legacy --}}
  <link rel="stylesheet" href="{{ asset('legacy/styles/style.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/styles/style-inicio.css') }}">
</head>

<body>

  {{-- Modal Login --}}
  <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="loginLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
      <div class="modal-content">
        <div class="login-box bg-white box-shadow border-radius-10">
          <div class="login-title">
            <h2 class="text-center text-primary">Iniciar Sesión</h2>
          </div>

          {{-- MENSAJES DE ERROR --}}
          @if ($errors->any())
            <div class="alert alert-danger mx-3">
              <ul class="mb-0">
                @foreach ($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="post" action="{{ route('login') }}" onsubmit="return validarFormulario();">
            @csrf
            <div class="input-group custom">
              <input type="number" class="form-control form-control-lg" placeholder="Cédula" id="cedula" name="cedula" value="{{ old('cedula') }}" />
              <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
              </div>
            </div>
            <div class="input-group custom">
              <input type="password" class="form-control form-control-lg" placeholder="Contraseña" id="contrasena" name="contrasena" />
              <div class="input-group-append custom">
                <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
              </div>
            </div>

            <div class="row align-items-center">
              <div class="col-5">
                <div class="input-group mb-0">
                  <button class="btn btn-primary btn-lg btn-block" type="submit">Iniciar sesión</button>
                </div>
              </div>
              <div class="col-2">
                <div class="font-16 weight-600 text-center" data-color="#707373">Ó</div>
              </div>
              <div class="col-5">
                <div class="input-group mb-0">
                  <button type="button" class="btn btn-outline-primary btn-lg btn-block" data-dismiss="modal">
                    Salir
                  </button>
                </div>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>

  {{-- Header --}}
  <header id="header" class="fixed-top d-flex align-items-center">
    <div class="container d-flex align-items-center">
      <div class="logo me-auto">
        <h1><a href="#">Sipmainputvalue</a></h1>
      </div>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto active" href="#hero">Inicio</a></li>
          <li class="dropdown"><a href="#"><span>Acerca de</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a class="nav-link scrollto" href="#about">Acerca de</a></li>
              <li><a class="nav-link scrollto" href="#team">Nuestro equipo</a></li>
            </ul>
          </li>
          <li><a class="nav-link scrollto" href="#services">Servicios</a></li>
          <li><a class="nav-link scrollto" href="#contact">Contacto</a></li>
          <li>
            <a href="#" class="btn-block" data-backdrop="static" data-toggle="modal" data-target="#login-modal">Iniciar sesión</a>
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>

  {{-- Hero --}}
  <section id="hero">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 pt-5 pt-lg-0 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up">
          <div>
            <h1>Bienvenido a la Plataforma de distribución de participación</h1>
            <h2>Distribuye tu capital de manera eficiente, con el software de justicia accionaria actualizado</h2>
            <a href="#about" class="btn-get-started scrollto">Enséñame más</a>
          </div>
        </div>
        <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="fade-left">
          <img src="{{ asset('assets/img/hero-img.png') }}" class="img-fluid" alt="">
        </div>
      </div>
    </div>
  </section>

  {{-- ... Resto de secciones (About, Features, Services, Team, FAQ, Contact) ... --}}
  {{-- Conserva tu mismo HTML y SOLO cambia rutas de imágenes a asset('assets/...') como arriba --}}

  {{-- Footer --}}
  <footer id="footer">
    {{-- ... tu footer idéntico, cambiando rutas de imágenes a asset() ... --}}
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  {{-- JS landing assets --}}
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>

  {{-- JS DeskApp legacy --}}
  <script src="{{ asset('legacy/vendors/scripts/core.js') }}"></script>
  <script src="{{ asset('legacy/vendors/scripts/script.min.js') }}"></script>
  <script src="{{ asset('legacy/vendors/scripts/process.js') }}"></script>
  <script src="{{ asset('legacy/vendors/scripts/layout-settings.js') }}"></script>
  <script src="{{ asset('legacy/src/plugins/jquery-steps/jquery.steps.js') }}"></script>
  <script src="{{ asset('legacy/vendors/scripts/steps-setting.js') }}"></script>

  <script>
    function validarFormulario() {
      var cedula = document.getElementById("cedula").value;
      var contrasena = document.getElementById("contrasena").value;
      if (cedula.trim() === "" || contrasena.trim() === "") {
        alert("Por favor, complete todos los campos.");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
