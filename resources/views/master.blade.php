<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sogo Hotel by Colorlib.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />

    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto+Sans:400,700|Playfair+Display:400,700">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/jquery.timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/fonts/ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/fonts/fontawesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/style.css') }}">
  </head>

  <body>
    <!-- Header -->
    <header class="site-header js-site-header">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-6 col-lg-4 site-logo" data-aos="fade">
            <a href="{{ url('/') }}">Sogo Hotel</a>
          </div>
          <div class="col-6 col-lg-8">
            <div class="site-menu-toggle js-site-menu-toggle" data-aos="fade">
              <span></span><span></span><span></span>
            </div>

            <div class="site-navbar js-site-navbar">
              <nav role="navigation">
                <div class="container">
                  <div class="row full-height align-items-center">
                    <div class="col-md-6 mx-auto">
                      <ul class="list-unstyled menu">
                        <li class="active"><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ url('/rooms') }}">Rooms</a></li>
                        <li><a href="{{ url('/about') }}">About</a></li>
                        <li><a href="{{ url('/events') }}">Events</a></li>
                        <li><a href="{{ url('/contact') }}">Contact</a></li>
                        <li><a href="{{ url('/reservation') }}">Reservation</a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </header>
    <!-- END header -->

    <section class="site-hero overlay" style="background-image: url({{ asset('frontend_assets/images/hero_4.jpg') }})" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row site-hero-inner justify-content-center align-items-center">
          <div class="col-md-10 text-center" data-aos="fade-up">
            <span class="custom-caption text-uppercase text-white d-block mb-3">
              Welcome To 5 <span class="fa fa-star text-primary"></span> Hotel
            </span>
            <h1 class="heading">A Best Place To Stay</h1>
          </div>
        </div>
      </div>
      <a class="mouse smoothscroll" href="#next">
        <div class="mouse-icon"><span class="mouse-wheel"></span></div>
      </a>
    </section>
    @yield('checkAvailabilityForm')
    @yield('content')

    <!-- Footer -->
    <footer class="section footer-section">
      <div class="container">
        <div class="row mb-4">
          <div class="col-md-3 mb-5">
            <ul class="list-unstyled link">
              <li><a href="{{ url('/about') }}">About Us</a></li>
              <li><a href="#">Terms &amp; Conditions</a></li>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="{{ url('/rooms') }}">Rooms</a></li>
            </ul>
          </div>
          <div class="col-md-3 mb-5">
            <ul class="list-unstyled link">
              <li><a href="{{ url('/rooms') }}">The Rooms &amp; Suites</a></li>
              <li><a href="{{ url('/about') }}">About Us</a></li>
              <li><a href="{{ url('/contact') }}">Contact Us</a></li>
              <li><a href="#">Restaurant</a></li>
            </ul>
          </div>
          <div class="col-md-3 mb-5 pr-md-5 contact-info">
            <p><span class="d-block"><span class="ion-ios-location h5 mr-3 text-primary"></span>Address:</span>
              <span>198 West 21th Street, Suite 721 New York NY 10016</span></p>
            <p><span class="d-block"><span class="ion-ios-telephone h5 mr-3 text-primary"></span>Phone:</span>
              <span>(+1) 435 3533</span></p>
            <p><span class="d-block"><span class="ion-ios-email h5 mr-3 text-primary"></span>Email:</span>
              <span>info@domain.com</span></p>
          </div>
          <div class="col-md-3 mb-5">
            <p>Sign up for our newsletter</p>
            <form action="#" class="footer-newsletter">
              <div class="form-group">
                <input type="email" class="form-control" placeholder="Email...">
                <button type="submit" class="btn"><span class="fa fa-paper-plane"></span></button>
              </div>
            </form>
          </div>
        </div>
        <div class="row pt-5">
          <p class="col-md-6 text-left">
            Copyright &copy;<script>document.write(new Date().getFullYear());</script>
            All rights reserved | Template by <a href="https://colorlib.com" target="_blank">Colorlib</a>
          </p>

          <p class="col-md-6 text-right social">
            <a href="#"><span class="fa fa-tripadvisor"></span></a>
            <a href="#"><span class="fa fa-facebook"></span></a>
            <a href="#"><span class="fa fa-twitter"></span></a>
            <a href="#"><span class="fa fa-linkedin"></span></a>
            <a href="#"><span class="fa fa-vimeo"></span></a>
          </p>
        </div>
      </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('frontend_assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/jquery-migrate-3.0.1.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/jquery.fancybox.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/aos.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/jquery.timepicker.min.js') }}"></script>
    <script src="{{ asset('frontend_assets/js/main.js') }}"></script>

    <!-- Datepicker Logic -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
          const today = new Date();
          const yyyy = today.getFullYear();
          const mm = ('0' + (today.getMonth() + 1)).slice(-2);
          const dd = ('0' + today.getDate()).slice(-2);
          const todayStr = `${yyyy}-${mm}-${dd}`;

          let disabledDates = [];

          fetch("{{ route('booking.fullyBookedDates') }}")
            .then(res => res.json())
            .then(data => {
              disabledDates = data.map(date => new Date(date));
              initDatePickers();
            })
            .catch(() => initDatePickers());

          function initDatePickers() {
            $('.datepicker').datepicker({
              format: 'yyyy-mm-dd',
              startDate: todayStr, // Disable past dates
              autoclose: true,
              datesDisabled: disabledDates,
              todayHighlight: true
            });
          }
      });
    </script>
  </body>
</html>
