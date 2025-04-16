<?php
// Get the current URI segments.
$uri      = service('uri');
$segment1 = $uri->getSegment(1); // e.g., "admin" or "facilitator"
$segment2 = $uri->getSegment(2); // e.g., "index", "user", "map", etc.

// Initialize variables for active classes.
$dashboardActive = '';
$usersActive     = '';
$mapActive       = '';



// If $role is provided and equals "1" then we force the Users nav as active.
if (isset($role) && $role == "1" && $segment1 == 'admin' && (strtolower($segment2) == 'users'))  {
    $usersActive = ' active';
} else {
    // Determine the active nav item based on the URI segments.

    // Dashboard is active when route is "admin", "admin/index",
    // "facilitator" or "facilitator/index".
    if (($segment1 == 'admin' || $segment1 == 'facilitator') && ($segment2 == '' || strtolower($segment2) == 'index')) {
        $dashboardActive = ' active';
    }
    // Users is active when the route is "admin/user".
    elseif ($segment1 == 'admin' && strtolower($segment2) == 'user') {
        $usersActive = ' active';
    }
    // Map is active when the route is "admin/map" or "facilitator/map".
    elseif (($segment1 == 'admin' || $segment1 == 'facilitator') && strtolower($segment2) == 'facility') {
        $mapActive = ' active';
    }
    // Users is active when the route is "admin/user".
    elseif ($segment1 == 'facilitator' && strtolower($segment2) == 'inquiries') {
        $usersActive = ' active';
    }
}
?>

<!-- Sidenav -->
<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
  <div class="scrollbar-inner">
    <!-- Brand -->
    <div class="sidenav-header d-flex align-items-center">
      <a class="navbar-brand" href="<?= base_url('index'); ?>">
        <img src="<?= base_url('public/front/assets/img/wazte_logo.png') ?>" height="90"
             class="navbar-brand-img" alt="Wazte Logo" style="max-height: 3.5rem !important;">
      </a>
      <div class="ml-auto">
        <!-- Sidenav toggler -->
        <div class="sidenav-toggler d-none d-xl-block" data-action="sidenav-unpin" data-target="#sidenav-main">
          <div class="sidenav-toggler-inner">
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="navbar-inner">
      <!-- Collapse -->
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <!-- Nav items -->
        <ul class="navbar-nav">
          <!-- Dashboard Link -->
          <li class="nav-item">
            <a class="nav-link<?= $dashboardActive ?>" href="<?= base_url(($segment1 == 'admin' || $segment1 == 'facilitator') ? $segment1 : 'admin'); ?>">
              <i class="ni ni-chart-pie-35 text-primary"></i>
              <span class="nav-link-text">Dashboard</span>
            </a>
          </li>

          <!-- Users Link: Display only if $role equals "1" -->
          <?php if (isset($role) && $role == "1") : ?>
          <li class="nav-item">
            <a class="nav-link<?= $usersActive ?>" href="<?= base_url('admin/users'); ?>">
              <i class="ni ni-user-run text-default"></i>
              <span class="nav-link-text">Users</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- Map Link -->
          <li class="nav-item">
            <a class="nav-link<?= $mapActive ?>" href="<?= base_url(($segment1 == 'admin' || $segment1 == 'facilitator') ? $segment1 . '/facility' : 'admin/facility'); ?>">
              <i class="ni ni-map-big text-warning"></i>
              <span class="nav-link-text">Facility List</span>
            </a>
          </li>
          
          <!-- Users Link: Display only if $role equals "1" -->
          <?php if (isset($role) && $role == "2") : ?>
          <li class="nav-item">
            <a class="nav-link<?= $usersActive ?>" href="<?= base_url('facilitator/inquiries'); ?>">
              <i class="ni ni-email-83 text-default"></i>
              <span class="nav-link-text">Inquiries</span>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</nav>


  <!-- Main content -->
  <div class="main-content" id="panel">
    <!-- Topnav -->
    <nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom">
      <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
       
          <!-- Navbar links -->
          <ul class="navbar-nav align-items-center  ml-md-auto ">
            <li class="nav-item d-xl-none">
              <!-- Sidenav toggler -->
              <div class="pr-3 sidenav-toggler sidenav-toggler-dark" data-action="sidenav-pin" data-target="#sidenav-main">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </div>
            </li>
           
         
          </ul>
          <ul class="navbar-nav align-items-center  ml-auto ml-md-0 ">
            <li class="nav-item dropdown">
              <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="media align-items-center">
                  <span class="avatar avatar-sm rounded-circle">
                 <img alt="Image placeholder" src="<?= base_url('public/user.png') ?>">
                  </span>
                  <div class="media-body  ml-2  d-none d-lg-block">
                    <span class="mb-0 text-sm  font-weight-bold"><?= $current_name ?></span>
                  </div>
                </div>
              </a>
              <div class="dropdown-menu  dropdown-menu-right ">
                <div class="dropdown-header noti-title">
                  <h6 class="text-overflow m-0">Welcome <?= $rolename; ?>!</h6>
                </div>
             
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('login/logout'); ?>" class="dropdown-item">
                  <i class="ni ni-user-run"></i> 
                  <span>Logout</span>
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- Header -->
    <div class="header bg-primary pb-6">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0 text-capitalize"><?= ($segment2 == '' || $segment2 == 'index' ) ? 'Dashboard' : $segment2; ?></h6>
            </div>
           
          </div>
         
        </div>
      </div>
    </div>