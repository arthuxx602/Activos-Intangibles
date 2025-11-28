<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Equipo - Sipmainputvalue</title>

  <link href="{{ asset('assets/css/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/css/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style-index.css') }}" rel="stylesheet">
  <style>
    .team .member .pic{
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .team .member .pic img.team-avatar{
      width: 160px;
      height: 169px;
      border-radius: 50%;
      border: 5px solid #0d6efd;
      object-fit: cover;
      box-shadow: 0 8px 20px rgba(13,110,253,0.25);
    }
  </style>
</head>

<body>
  <header id="header" class="fixed-top d-flex align-items-center">
    <div class="container d-flex align-items-center">
      <div class="logo me-auto">
        <h1><a href="{{ route('landing') }}">Sipmainputvalue</a></h1>
      </div>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link" href="{{ route('landing') }}#hero">Inicio</a></li>
          <li><a class="nav-link" href="{{ route('landing') }}#about">Acerca de</a></li>
          <li><a class="nav-link active" href="#team">Nuestro equipo</a></li>
          <li><a class="nav-link" href="{{ route('landing') }}#services">Servicios</a></li>
          <li><a class="nav-link" href="{{ route('landing') }}#contact">Contacto</a></li>
          <li>
            <a class="nav-link" href="{{ route('landing') }}">Volver al inicio</a>
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>

  <main id="main" class="mt-5 pt-5">
    <section id="team" class="team">
      <div class="container">
        <div class="section-title" data-aos="fade-up">
          <h2>Nuestro equipo</h2>
          <p>Profesionales en desarrollo de software y gestión de inversiones.</p>
        </div>

        @php
          $team = [
            ['img' => 'team/Cristhian Raul.jpg',               'name' => 'Cristhian Raúl Mora Angulo',        'role' => 'Desarrollador'],
            ['img' => 'team/Diana Karina Lopez Carreño.jpg',   'name' => 'Diana Karina López Carreño',        'role' => 'Docente'],
            ['img' => 'team/Diego Alejandro Penagos Rojas.jpg','name' => 'Diego Alejandro Penagos Rojas',     'role' => 'Desarrollador'],
            ['img' => 'team/Jonatan Riveros.jpg',              'name' => 'Jonatan Mateo Riveros Méndez',      'role' => 'Desarrollador'],
            ['img' => 'team/Kevin Alexander Pena Conejo.jpg',  'name' => 'Kevin Alexander Peña Conejo',       'role' => 'CTO'],
            ['img' => 'team/Paula Cantor Caballero.jpg',       'name' => 'Paula Andrea Cantor Caballero',     'role' => ''],
            ['img' => 'team/Arturo Andres Marquez.jpg',        'name' => 'Arturo Andres Marquez',             'role' => 'Desarrollador'],
            ['img' => 'team/Juan Sebastian Gonzalez.jpg',      'name' => 'Juan Sebastián González',           'role' => 'Desarrollador'],
            ['img' => 'team/team-1.jpg',                       'name' => 'Micher Alexander Gonzales Monroy',  'role' => 'Docente'],
            ['img' => 'team/team-1.jpg',                       'name' => 'Campo Eli Castillo Eraso',          'role' => 'Docente'],
          ];
        @endphp

        <div class="row">
          @foreach ($team as $p)
            <div class="col-lg-4 col-md-6">
              <div class="member" data-aos="zoom-in">
                <div class="pic">
                  <img src="{{ asset('assets/img/'.$p['img']) }}" class="img-fluid team-avatar" alt="{{ $p['name'] }}">
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
  </main>

  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">
          <div class="col-lg-3 col-md-6">
            <div class="footer-info">
              <h3>Colombia</h3>
              <p>
                Fusagasuga Cundinamarca<br>
                Diagonal 18 No. 20-29<br><br>
                <strong>Celular:</strong> +57 3228237214<br>
                <strong>Correo:</strong> unicundi@ucundinamarca.edu.co<br>
              </p>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 footer-links">
            <h4>Navegación</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="{{ route('landing') }}">Inicio</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="{{ route('landing') }}#about">Acerca de</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="{{ route('landing.team') }}">Equipo</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Nuestros servicios</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="{{ route('landing') }}#services">Documentación inversiones</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="{{ route('landing') }}#services">Distribución accionaria</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="{{ route('landing') }}#services">Soporte del software</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="{{ route('landing') }}#services">Soporte técnico</a></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-newsletter">
            <img src="{{ asset('assets/css/img/LogoU.png') }}" style="width: 140px; max-width: 100%; height: auto; object-fit: contain;" alt="Logo U">
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

  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
