<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Wazte - Navigate Facility</title>
  <!-- Favicon -->
  <link rel="icon" href="<?= base_url('public/wazte_logo_icon.png') ?>" type="image/png">


  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

  <!-- Select2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <!-- Axios -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <link rel="stylesheet" href=" <?= base_url('public/front/css/navigate.css') ?>" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/smooth-scrollbar/8.8.4/smooth-scrollbar.css" />

</head>

<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light p-2">
    <div class="container-fluid d-flex gap-3" id="navIcon">
      <div class="d-flex flex-row">
        <a class="navbar-brand p-0 me-auto" href="#">
          <img src="<?= base_url('public/wazte_logo.png') ?>" alt="Wazte" width="115"
            class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler mt-2" type="button" id="navToggler" data-bs-toggle="collapse"
          data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
          aria-label="Toggle navigation">
          <span><i class="fas fa-bars"></i></span>
        </button>
      </div>
      <div class="collapse navbar-collapse" id="navbarContent">
        <div class="d-flex gap-3 flex-wrap" id="filtersNav">
          <div class="search-bar">
            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
              <i class="fa-solid fa-sliders"></i>
            </button>
            <input type="text" id="facilitySearchInput" placeholder="Search e-waste facility ..." class="w-100" />
            <i class="fas fa-search text-muted flex-shrink-1"></i>
          </div>
          <div class="d-flex flex-wrap gap-2 category-scroll" id="material-buttons"></div>
        </div>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTAINER -->
  <div id="main-container">
    <div class="sidebar">
      <div class="d-flex align-items-center">
        <div class="me-auto">
          <div class="d-flex flex-column gap-2">
            <div>
              <img src="<?= base_url('public/user.png') ?>" onerror="" class="
                rounded-circle" height="34">
              <?= esc($loggedUser['name']) ?>
            </div>
          </div>
        </div>
        <a href="<?= base_url('login/logout') ?>" alt="Sign out" class="btn"><i class="fas fa-power-off"></i></a>
      </div>
      <div class="d-flex align-items-center">
        <div class="me-auto">
        </div>
        <div>
          <?php if ($role != '3'): ?>
            <a href="<?= base_url('login/chooseusertype') ?>" class="btn btn-warning btn-sm">Upgrade Account</a>
          <?php endif; ?>
        </div>
      </div>
      <hr>
      <div id="places-list" class="flex-grow-1 overflow-auto mt-2"></div>
    </div>
    <div id="map"></div>
  </div>

  <!-- Modal for Map Filters -->
  <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Configure Map Filters</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3 w-100">
            <select class="form-select" data-placeholder="Choose filters" id="small-select2-options-multiple-field"
              multiple>
              <option>Google Places</option>
              <option>Wazte Places</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php if ($role === '3'): ?>
    <!-- Modal for sending Inquiry -->
    <div class="modal fade" id="sendInquiry" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Send inquiry</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form method="post" id="inquiryForm">
            <div class="modal-body">
              <div class="mb-3">
                <label for="facilitator_email" class="form-label">Facilitator Email</label>
                <input type="email" class="form-control" name="facilitator_email" id="facilitator_email" disabled
                  required>
              </div>

              <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" name="subject" id="subject" required>
              </div>

              <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" name="message" id="message" rows="6" required></textarea>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Send</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery (required for select2) -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
  <!-- Google Maps -->
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= $googlekey ?>&libraries=places&callback=initMap"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/smooth-scrollbar/8.8.4/smooth-scrollbar.min.js"></script>
  <script>  var Scrollbar = window.Scrollbar;

    Scrollbar.init(document.querySelector('.sidebar')) </script>
  <script>
    const userRole = '<?= $role ?>';
    const baseurl = '<?= base_url() ?>';
  </script>

  <!-- Demo JS - remove this in your project -->
  <script src=" <?= base_url('public/front/js/navigate.js') ?>"></script>


</body>

</html>