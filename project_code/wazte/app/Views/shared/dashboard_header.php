<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Wazte Dashboard</title>

  <!-- Favicon -->
  <link rel="icon" href="<?= base_url('public/front/assets/img/favicon/favicon.png') ?>" type="image/png">
  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
  <!-- Icons -->
  <link rel="stylesheet" href="<?= base_url('public/dashboard/assets/vendor/nucleo/css/nucleo.css') ?>" type="text/css">
  <link rel="stylesheet"
    href="<?= base_url('public/dashboard/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') ?>"
    type="text/css">
  <!-- Page plugins -->
  <link rel="stylesheet"
    href="<?= base_url('public/dashboard/assets/vendor/fullcalendar/dist/fullcalendar.min.css') ?>">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Argon CSS -->
  <link rel="stylesheet" href=" <?= base_url('public/dashboard/css/dashboard.css') ?>" type="text/css">

  <script src="<?= base_url('public/dashboard/assets/vendor/jquery/dist/jquery.min.js') ?>"></script>

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>



  <?php if (service('uri')->getSegment(2) == "facility"): ?>

    <style>
      /* ensure the autocomplete dropdown floats above the modal */
      .pac-container {
        z-index: 2000 !important;
      }

      /* 1) Target the BS5‑theme container */
      .select2-container--bootstrap-5 .select2-selection__choice {
        display: inline-flex;
        /* make it a flexbox */
        align-items: center;
        flex-direction: row-reverse;
        /* swap text & the “×” */
      }

      /* 2) Tweak spacing: move the × to the right with a left margin */
      .select2-container--bootstrap-5 .select2-selection__choice__remove {
        margin-left: .3em;
        /* space between text and icon */
        margin-right: 0;
        /* remove default right‑side gap */
      }
    </style>


    <!-- Google Maps -->
    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=<?= $googlekey ?>&libraries=places&callback=initMap"></script>


    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

  <?php endif; ?>

</head>


<body>