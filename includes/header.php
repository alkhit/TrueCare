<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
// Fallbacks if functions are not loaded
if (!function_exists('e')) {
  function e($string) { return htmlspecialchars($string, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('abs_path')) {
  function abs_path($path = '') { return $path; }
}
if (!function_exists('isLoggedIn')) {
  function isLoggedIn() { return isset($_SESSION['user_id']); }
}
// includes/header.php
// expects: $page_title (optional), $show_navbar (optional)
$page_title = $page_title ?? 'TrueCare';
$show_navbar = $show_navbar ?? false;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo e($page_title); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="<?php echo abs_path('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body>
<?php if ($show_navbar): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/">
      <i class="fas fa-hands-helping me-2"></i><strong>TrueCare</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?php echo abs_path('index.php'); ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo abs_path('src/campaigns/campaigns.php'); ?>">Campaigns</a></li>
        <?php if (isLoggedIn()): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-user me-1"></i><?php echo e($_SESSION['user_name']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?php echo abs_path('src/auth/dashboard.php'); ?>">Dashboard</a></li>
              <li><a class="dropdown-item" href="<?php echo abs_path('src/auth/profile.php'); ?>">Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?php echo abs_path('src/auth/logout.php'); ?>">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo abs_path('login.php'); ?>">Login</a></li>
          <li class="nav-item"><a class="btn btn-success ms-2" href="<?php echo abs_path('register.php'); ?>">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<button class="sidebar-toggle-btn d-xl-none" id="sidebarToggle" aria-label="Toggle menu">
  <i class="fas fa-bars"></i>
</button>
<main class="py-4 container-fluid">
