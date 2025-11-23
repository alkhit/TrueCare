<?php
if (!isset(
    $_SESSION
)) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['index.php', 'login.php', 'register.php', ''];
$show_navbar = (!isset($_SESSION['user_id']) && in_array($current_page, $public_pages));
$show_sidebar = (isset($_SESSION['user_id']) && !in_array($current_page, $public_pages));
$page_title = $page_title ?? 'TrueCare';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?></title>
<link rel="stylesheet" href="<?php echo abs_path('assets/css/style.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="<?php echo $show_sidebar ? 'has-sidebar' : ''; ?>">

<?php if ($show_sidebar): ?>
<button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="tc-sidebar" id="mainSidebar">
    <div class="sidebar-title"><i class="fas fa-hands-helping me-2"></i>TrueCare</div>
    <nav class="nav flex-column">
        <a class="nav-link<?php echo ($current_page == 'dashboard.php') ? ' active' : ''; ?>" href="<?php echo abs_path('src/auth/dashboard.php'); ?>">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>
        <a class="nav-link<?php echo (in_array($current_page, ['campaigns.php','my_campaigns.php'])) ? ' active' : ''; ?>" href="<?php echo abs_path('src/campaigns/campaigns.php'); ?>">
            <i class="fas fa-hand-holding-heart me-2"></i>Campaigns
        </a>
        <a class="nav-link<?php echo ($current_page == 'profile.php') ? ' active' : ''; ?>" href="<?php echo abs_path('src/auth/profile.php'); ?>">
            <i class="fas fa-user me-2"></i>Profile
        </a>
        <div class="mt-4 pt-3 border-top">
            <a class="nav-link text-warning" href="<?php echo abs_path('src/auth/logout.php'); ?>">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </nav>
</div>
<?php endif; ?>

<?php if ($show_navbar): ?>
<nav class="navbar navbar-expand-lg navbar-dark public-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?php echo abs_path('index.php'); ?>"><i class="fas fa-hands-helping me-2"></i>TrueCare</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo abs_path('index.php'); ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo abs_path('src/campaigns/campaigns.php'); ?>">Campaigns</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo abs_path('login.php'); ?>">Login</a></li>
                <li class="nav-item"><a class="nav-link btn btn-success btn-sm ms-2" href="<?php echo abs_path('register.php'); ?>">Register</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<!-- Main Content Area -->
<main class="main-content" id="mainContent">
