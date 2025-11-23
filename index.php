<?php
// index.php (public homepage)
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Ensure $db exists
if (!isset($db) && function_exists('get_db')) {
    $db = get_db();
}

$page_title = "TrueCare - Support Orphanages in Need";
$show_navbar = true;
include __DIR__ . '/includes/header.php';
?>

<!-- If user is logged in the header already renders sidebar; main is open -->
<div class="page-fullscreen w-100" style="min-height:100vh;">
    <section class="hero-section bg-primary text-white d-flex align-items-center" style="min-height:70vh;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="display-4 fw-bold">Make a Difference in a Child's Life</h1>
                    <p class="lead mb-4">TrueCare connects compassionate donors with orphanages in need. Your support provides education, healthcare, and hope for children who need it most.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="register.php" class="btn btn-success btn-lg"><i class="fas fa-hand-holding-heart me-2"></i>Start Helping</a>
                        <a href="src/campaigns/campaigns.php" class="btn btn-outline-light btn-lg"><i class="fas fa-search me-2"></i>Browse Campaigns</a>
                    </div>
                </div>

                <div class="col-lg-6 text-center">
                    <div class="bg-light rounded-3 p-5 text-primary" style="display:inline-block;">
                        <i class="fas fa-hands-helping fa-6x"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick stats -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <?php
                // safe DB queries (silently handle errors)
                $childrenHelped = 0;
                $totalDonations = 0;
                $activeCampaigns = 0;
                $partnerOrphanages = 0;

                try {
                    $stmt = $db->query("SELECT COUNT(*) FROM orphanages");
                    $childrenHelped = $stmt ? (int)$stmt->fetchColumn() : 0;
                } catch (Exception $e) {}

                try {
                    $stmt = $db->query("SELECT COALESCE(SUM(amount),0) FROM donations WHERE status='completed'");
                    $totalDonations = $stmt ? (float)$stmt->fetchColumn() : 0;
                } catch (Exception $e) {}

                try {
                    $stmt = $db->query("SELECT COUNT(*) FROM campaigns WHERE status='active'");
                    $activeCampaigns = $stmt ? (int)$stmt->fetchColumn() : 0;
                } catch (Exception $e) {}

                try {
                    $stmt = $db->query("SELECT COUNT(*) FROM orphanages WHERE status='verified'");
                    $partnerOrphanages = $stmt ? (int)$stmt->fetchColumn() : 0;
                } catch (Exception $e) {}
                ?>

                <div class="col-md-3 mb-4">
                    <h2 class="display-5 text-primary fw-bold"><?php echo $childrenHelped; ?></h2>
                    <p class="text-muted">Children Helped</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h2 class="display-5 text-success fw-bold">Ksh <?php echo number_format($totalDonations); ?></h2>
                    <p class="text-muted">Total Donations</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h2 class="display-5 text-info fw-bold"><?php echo $activeCampaigns; ?></h2>
                    <p class="text-muted">Active Campaigns</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h2 class="display-5 text-warning fw-bold"><?php echo $partnerOrphanages; ?></h2>
                    <p class="text-muted">Partner Orphanages</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
