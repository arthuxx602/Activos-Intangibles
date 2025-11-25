-- Active: 1764040690518@@127.0.0.1@3306@activos_intangibles_db
{{-- resources/views/landing/Sipmainputvalue.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Sipmainputvalue - Inicio</title>

  {{-- Google Fonts --}}
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Raleway:300,400,500,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

  {{-- Assets del landing (DEBEN estar en /public/assets) --}}
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style-index.css') }}" rel="stylesheet">

  {{-- Template DeskApp legacy (DEBEN estar en /public/legacy) --}}
  <link rel="stylesheet" href="{{ asset('legacy/vendors/styles/core.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/vendors/styles/icon-font.min.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/vendors/styles/style.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/src/plugins/jquery-steps/jquery.steps.css') }}">
  {{-- Tus estilos propios que estaban en src/styles --}}
  <link rel="stylesheet" href="{{ asset('legacy/src/styles/style.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/src/styles/style-inicio.css') }}">

  {{-- Estilo del HERO con fondo usando hero-img.png --}}
  <style>
    #hero{
      background: url("{{ asset('assets/css/img/hero-img.png') }}") right center no-repeat;
      background-size: cover;
      padding: 80px 0;
    }
    #hero .btn-get-started{
      display:inline-block;
      padding: 12px 24px;
      border-radius: 4px;
      background:#0d6efd;
      color:#fff;
      font-weight:600;
      text-decoration:none;
    }
    @media (max-width: 991.98px) {
      #hero{ background-position: center; }
    }
  </style>
</head>

<body>

  {{-- Modal Login --}}
  <div class="modal fade" id="login-modal" tabindex="-1" aria-labelledby="loginLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
      <div class="modal-content">
        <div class="login-box bg-white box-shadow border-radius-10">
          <div class="login-title">
            <h2 class="text-center text-primary" id="loginLabel">Iniciar Sesión</h2>
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
              <input type="email" class="form-control form-control-lg" placeholder="correo" id="correo" name="correo" value="{{ old('correo') }}">
              <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
              </div>
            </div>
            <div class="input-group custom">
              <input type="password" class="form-control form-control-lg" placeholder="Contraseña" id="contrasena" name="contrasena">
              <div class="input-group-append custom">
                <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
              </div>
            </div>

            <div class="row align-items-center">
              <div class="col-5">
                <div class="input-group mb-0">
                  <button class="btn btn-primary btn-lg w-100" type="submit">Iniciar sesión</button>
                </div>
              </div>
              <div class="col-2">
                <div class="font-16 weight-600 text-center" data-color="#707373">Ó</div>
              </div>
              <div class="col-5">
                <div class="input-group mb-0">
                  <button type="button" class="btn btn-outline-primary btn-lg w-100"
                          data-bs-dismiss="modal" data-dismiss="modal">
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
            {{-- Compatibilidad BS5 y BS4 --}}
            <a href="#"
               class="btn-block"
               data-bs-backdrop="static"
               data-bs-toggle="modal" data-bs-target="#login-modal"
               data-toggle="modal" data-target="#login-modal">
              Iniciar sesión
            </a>
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>

  {{-- Hero con fondo + ilustración --}}
  <section id="hero" class="d-flex align-items-center">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 pt-5 pt-lg-0 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up">
          <div>
            <h1>Bienvenido a la Plataforma de distribución de participación</h1>
            <h2>Distribuye tu capital de manera eficiente, con el software de justicia accionaria actualizado del momento</h2>
            <a href="#about" class="btn-get-started scrollto">ENSÉÑAME MÁS</a>
          </div>
        </div>
        <div class="col-lg-6 order-1 order-lg-2 hero-img d-flex justify-content-center" data-aos="zoom-in">
          <img src="{{ asset('assets/img/hero-img.png') }}" class="img-fluid" alt="Ilustración analítica" style="max-height:520px">
        </div>
      </div>
    </div>
  </section>

  {{-- ======= About ======= --}}
  <section id="about" class="about">
    <div class="container">
      <div class="row">
        <div class="col-lg-6" data-aos="zoom-in">
          <img src="{{ asset('assets/img/about.jpg') }}" class="img-fluid" alt="">
        </div>
        <div class="col-lg-6 d-flex flex-column justify-contents-center" data-aos="fade-left">
          <div class="content pt-4 pt-lg-0">
            <h3>Un poco más sobre nuestro proyecto</h3>
            <p class="fst-italic">
              En esta plataforma podrás gestionar y visualizar cómo se distribuye la participación monetaria entre los
              diferentes participantes de tu proyecto.
            </p>
            <ul>
              <li><i class="bi bi-check-circle"></i> Certificados e información de tu empresa.</li>
              <li><i class="bi bi-check-circle"></i> Inversiones de capital e industria.</li>
              <li><i class="bi bi-check-circle"></i> Control del ciclo de vida del proyecto.</li>
            </ul>
            <p>¡Anímate y descubre más funcionalidades!</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ======= Features ======= --}}
  <section id="features" class="features">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 mt-2 mb-tg-0 order-2 order-lg-1">
          <ul class="nav nav-tabs flex-column">
            <li class="nav-item" data-aos="fade-up">
              <a class="nav-link active show" data-bs-toggle="tab" href="#tab-1">
                <h4>Estadísticas</h4>
                <p>Resumen de las inversiones y su relevancia en el proyecto.</p>
              </a>
            </li>
            <li class="nav-item mt-2" data-aos="fade-up" data-aos-delay="100">
              <a class="nav-link" data-bs-toggle="tab" href="#tab-2">
                <h4>Conectividad</h4>
                <p>Notificaciones de alerta sobre eventos del proyecto.</p>
              </a>
            </li>
            <li class="nav-item mt-2" data-aos="fade-up" data-aos-delay="200">
              <a class="nav-link" data-bs-toggle="tab" href="#tab-3">
                <h4>Resumen contable</h4>
                <p>Valores invertidos con su información legal.</p>
              </a>
            </li>
            <li class="nav-item mt-2" data-aos="fade-up" data-aos-delay="300">
              <a class="nav-link" data-bs-toggle="tab" href="#tab-4">
                <h4>Seguridad</h4>
                <p>Información personal y monetaria segura.</p>
              </a>
            </li>
          </ul>
        </div>
        <div class="col-lg-6 order-1 order-lg-2" data-aos="zoom-in">
          <div class="tab-content">
            <div class="tab-pane active show" id="tab-1">
              <figure><img src="{{ asset('assets/img/features-1.png') }}" alt="" class="img-fluid"></figure>
            </div>
            <div class="tab-pane" id="tab-2">
              <figure><img src="{{ asset('assets/img/features-2.png') }}" alt="" class="img-fluid"></figure>
            </div>
            <div class="tab-pane" id="tab-3">
              <figure><img src="{{ asset('assets/img/features-3.png') }}" alt="" class="img-fluid"></figure>
            </div>
            <div class="tab-pane" id="tab-4">
              <figure><img src="{{ asset('assets/img/features-4.png') }}" alt="" class="img-fluid"></figure>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ======= Services ======= --}}
  <section id="services" class="services section-bg">
    <div class="container">
      <div class="section-title" data-aos="fade-up">
        <h2>Servicios</h2>
        <p>Presentamos más funcionalidades del proyecto.</p>
      </div>

      <div class="row">
        <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0" data-aos="zoom-in">
          <div class="icon-box icon-box-pink">
            <div class="icon"><i class="bx bxl-dribbble"></i></div>
            <h4 class="title"><a href="#">Roles</a></h4>
            <p class="description">
              No se preocupe por el ingreso en apartados de administración,
              cada persona contará con su propia visualización y control de información.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0" data-aos="zoom-in" data-aos-delay="100">
          <div class="icon-box icon-box-cyan">
            <div class="icon"><i class="bx bx-file"></i></div>
            <h4 class="title"><a href="#">Documentación</a></h4>
            <p class="description">
              Descargue y cargue los certificados y documentos legales de sus inversiones.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0" data-aos="zoom-in" data-aos-delay="200">
          <div class="icon-box icon-box-green">
            <div class="icon"><i class="bx bx-tachometer"></i></div>
            <h4 class="title"><a href="#">Rendimiento</a></h4>
            <p class="description">
              Visualice y logre procesar la información en tiempo real y sin contratiempos.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0" data-aos="zoom-in" data-aos-delay="300">
          <div class="icon-box icon-box-blue">
            <div class="icon"><i class="bx bx-world"></i></div>
            <h4 class="title"><a href="#">Globalización</a></h4>
            <p class="description">
              Realice su distribución desde cualquier parte del mundo.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ======= Team ======= --}}
  <section id="team" class="team">
    <div class="container">
      <div class="section-title" data-aos="fade-up">
        <h2>Nuestro equipo</h2>
        <p>Profesionales en desarrollo de software y gestión de inversiones.</p>
      </div>

      @php
        $team = [
          ['img' => 'team/Cristhian Raul.jpg',                     'name' => 'Cristhian Raul Mora Angulo',           'role' => 'Desarrollador'],
          ['img' => 'team/Diana Karina Lopez Carreño.jpg',         'name' => 'Diana Karina López Carreño',           'role' => 'Docente'],
          ['img' => 'team/Diego Alejandro Penagos Rojas.jpg',      'name' => 'Diego Alejandro Penagos Rojas',        'role' => 'Desarrollador'],
          ['img' => 'team/Jonatan Riveros.jpg',                    'name' => 'Jonatan Mateo Riveros Méndez',         'role' => 'Desarrollador'],
          ['img' => 'team/Kevin Alexander Pena Conejo.jpg',        'name' => 'Kevin Alexander Peña Conejo',          'role' => 'CTO'],
          ['img' => 'team/Paula Cantor Caballero.jpg',             'name' => 'Paula Andrea Cantor Caballero',        'role' => ''],
          ['img' => 'team/team-1.jpg',                             'name' => 'Micher Alexander Gonzales Monroy',     'role' => 'Docente'],
          ['img' => 'team/team-1.jpg',                             'name' => 'Campo Eli Castillo Eraso',             'role' => 'Docente'],
        ];
      @endphp

      <div class="row">
        @foreach ($team as $p)
          <div class="col-lg-4 col-md-6">
            <div class="member" data-aos="zoom-in">
              <div class="pic">
                <img src="{{ asset('assets/img/'.$p['img']) }}" class="img-fluid" alt="{{ $p['name'] }}">
              </div>
              <div class="member-info">
                <h4>{{ $p['name'] }}</h4>
                @if(!empty($p['role'])) <span>{{ $p['role'] }}</span> @endif
                <div class="social">
                  <a href="#"><i class="bi bi-twitter"></i></a>
                  <a href="#"><i class="bi bi-facebook"></i></a>
                  <a href="#"><i class="bi bi-instagram"></i></a>
                  <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </section>

  {{-- ======= FAQ ======= --}}
  <section id="faq" class="faq">
    <div class="container">
      <div class="section-title" data-aos="fade-up">
        <h2>Preguntas frecuentes</h2>
      </div>

      <ul class="faq-list">
        <li>
          <div data-bs-toggle="collapse" class="collapsed question" href="#faq1">
            ¿Qué es la tasa libre de riesgo?
            <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
          </div>
          <div id="faq1" class="collapse" data-bs-parent=".faq-list">
            <p>Rendimiento esperado de una inversión considerada sin riesgo.</p>
          </div>
        </li>
      </ul>
    </div>
  </section>

  {{-- ======= Contact ======= --}}
  <section id="contact" class="contact section-bg">
    <div class="container">
      <div class="section-title" data-aos="fade-up">
        <h2>Contáctenos</h2>
      </div>

      <div class="row">
        <div class="col-lg-5 d-flex align-items-stretch" data-aos="fade-right">
          <div class="info">
            <div class="address">
              <i class="bi bi-geo-alt"></i>
              <h4>Ubicación:</h4>
              <p>Diagonal 18 No. 20-29 Fusagasugá barrio Manila</p>
            </div>

            <div class="email">
              <i class="bi bi-envelope"></i>
              <h4>Correo:</h4>
              <p>unicundi@ucundinamarca.edu.co</p>
            </div>

            <div class="phone">
              <i class="bi bi-phone"></i>
              <h4>Celular:</h4>
              <p>+57 3228237214</p>
            </div>

            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1311.9270222192467!2d-74.36964982384944!3d4.333868218831487!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f04f314d97f1b%3A0x669cea7143a50cb0!2sUNIVERSIDAD%20DE%20CUNDINAMARCA%20-%20SEDE%20FUSAGASUG%C3%81!5e0!3m2!1ses-419!2sco!4v1716957351722!5m2!1ses-419!2sco"
              frameborder="0" style="border:0; width: 100%; height: 290px;" allowfullscreen></iframe>
          </div>
        </div>

        <div class="col-lg-7 mt-5 mt-lg-0 d-flex align-items-stretch" data-aos="fade-left">
          <form action="#" method="post" role="form" class="php-email-form">
            @csrf
            <div class="row">
              <div class="form-group col-md-6">
                <label>Nombre y apellido</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="form-group col-md-6 mt-3 mt-md-0">
                <label>Su correo</label>
                <input type="email" class="form-control" name="email" required>
              </div>
            </div>
            <div class="form-group mt-3">
              <label>Asunto</label>
              <input type="text" class="form-control" name="subject" required>
            </div>
            <div class="form-group mt-3">
              <label>Mensaje</label>
              <textarea class="form-control" name="message" rows="10" required></textarea>
            </div>
            <div class="my-3">
              <div class="loading">Cargando...</div>
              <div class="error-message"></div>
              <div class="sent-message">Su mensaje ha sido enviado.</div>
            </div>
            <div class="text-center"><button type="submit">Enviar correo</button></div>
          </form>
        </div>
      </div>
    </div>
  </section>

  {{-- Footer --}}
  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">
          <div class="col-lg-3 col-md-6">
            <div class="footer-info">
              <h3>Colombia</h3>
              <p>
                Fusagasugá Cundinamarca<br>
                Diagonal 18 No. 20-29<br><br>
                <strong>Celular:</strong> +57 3228237214<br>
                <strong>Correo:</strong> unicundi@ucundinamarca.edu.co<br>
              </p>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 footer-links">
            <h4>Navegación</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Inicio</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Acerca de</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Servicios</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Nuestros servicios</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Documentación inversiones</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Distribución accionaria</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Soporte del software</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Soporte técnico</a></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-newsletter">
            <img src="{{ asset('assets/img/LogoU.png') }}" style="width: 100px; height: auto;" alt="Logo U">
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong><span>Sipmainputvalue</span></strong>. Todos los derechos reservados.
      </div>
    </div>
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

 {{-- Inicializaciones + auto-abrir modal si hubo errores --}}
@php
    // Evita errores si $errors no está disponible en la vista
    $hasErrors = isset($errors) && method_exists($errors, 'any') ? $errors->any() : false;
    $openLoginModal = $hasErrors || session('open_login_modal', false);
@endphp
{{-- Inicializaciones + auto-abrir modal si hubo errores --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.AOS) AOS.init();

      // Auto-open login modal when server indicates it
      if ({!! json_encode($openLoginModal) !!}) {//pendiente por arreglar en html, 
        try {
          if (window.bootstrap && typeof bootstrap.Modal === 'function') {
            new bootstrap.Modal(document.getElementById('login-modal')).show();
          } else if (window.$ && typeof $('#login-modal').modal === 'function') {
            $('#login-modal').modal('show');
          }
        } catch (e) {}
      }
    });

    function validarFormulario() {
      var correo = document.getElementById("correo").value;
      var contrasena = document.getElementById("contrasena").value;
      if (correo.trim() === "" || contrasena.trim() === "") {
        alert("Por favor, complete todos los campos.");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
