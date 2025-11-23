<?php
include 'includes/config.php';
$page_title = "TrueCare - Support Orphanages in Need";
$show_navbar = true;
include 'includes/header.php';
?>
<div class="row">
    <!-- Sidebar -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
        <?php include 'src/auth/sidebar.php'; ?>
    </nav>
    <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
        Toggle Sidebar
    </button>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php else: ?>
    <main class="col-12 px-md-4">
    <?php endif; ?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Make a Difference in a Child's Life</h1>
                <p class="lead mb-4">TrueCare connects compassionate donors with orphanages in need. Your support provides education, healthcare, and hope for children who need it most.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="register.php" class="btn btn-success btn-lg">
                        <i class="fas fa-hand-holding-heart me-2"></i>Start Helping
                    </a>
                    <a href="src/campaigns/campaigns.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-search me-2"></i>Browse Campaigns
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="bg-light rounded-3 p-5 text-primary">
                    <i class="fas fa-hands-helping fa-8x"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <?php
            // Fetch stats from database
            $childrenStmt = $db->query("SELECT COUNT(*) FROM orphanages");
            if (!$childrenStmt) {
                error_log("Database error: " . implode(", ", $db->errorInfo()));
            }
            $childrenHelped = $childrenStmt ? $childrenStmt->fetchColumn() : 0;

            $donationsStmt = $db->query("SELECT SUM(amount) FROM donations WHERE status='completed'");
            $totalDonations = $donationsStmt ? $donationsStmt->fetchColumn() : 0;
            $totalDonations = $totalDonations ?? 0; // Ensure $totalDonations is not null

            $campaignsStmt = $db->query("SELECT COUNT(*) FROM campaigns WHERE status='active'");
            $activeCampaigns = $campaignsStmt ? $campaignsStmt->fetchColumn() : 0;

            $orphanagesStmt = $db->query("SELECT COUNT(*) FROM orphanages WHERE status='verified'");
            $partnerOrphanages = $orphanagesStmt ? $orphanagesStmt->fetchColumn() : 0;
            ?>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <h2 class="display-4 text-primary fw-bold"><?php echo $childrenHelped; ?></h2>
                    <p class="text-muted">Children Helped</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <h2 class="display-4 text-success fw-bold">Ksh <?php echo number_format($totalDonations); ?></h2>
                    <p class="text-muted">Total Donations</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <h2 class="display-4 text-info fw-bold"><?php echo $activeCampaigns; ?></h2>
                    <p class="text-muted">Active Campaigns</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <h2 class="display-4 text-warning fw-bold"><?php echo $partnerOrphanages; ?></h2>
                    <p class="text-muted">Partner Orphanages</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Campaigns -->
<section class="featured-campaigns py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="section-title">Featured Campaigns</h2>
                <p class="text-muted">Urgent campaigns that need your support</p>
            </div>
        </div>
        <div class="row">
            <?php
            $featuredStmt = $db->query("SELECT campaign_id, title, description, category, target_amount, current_amount, deadline FROM campaigns WHERE status='active' ORDER BY created_at DESC LIMIT 3");
            $featuredCampaigns = $featuredStmt ? $featuredStmt->fetchAll(PDO::FETCH_ASSOC) : [];
            if (count($featuredCampaigns) === 0): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No campaigns available at the moment.</h5>
                </div>
            <?php else:
                foreach ($featuredCampaigns as $campaign): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <?php
                        $category = isset($campaign['category']) && !empty($campaign['category']) ? strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $campaign['category'])) : 'default';
                        // Fallback logic for campaign images
                        $image_path = "assets/images/campaigns/{$category}.jpg";
                        if (!file_exists($image_path)) {
                            $image_path = "assets/images/campaigns/default.jpg";
                        }
                        ?>
                        <img src="<?php echo $image_path; ?>" class="card-img-top" alt="Campaign" style="height: 200px; object-fit: cover;" onerror="this.onerror=null;this.src='assets/images/campaigns/default.jpg';">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-success mb-2"><?php echo ucfirst($campaign['category']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($campaign['title']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($campaign['description']); ?></p>
                            <div class="mb-3">
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo ($campaign['current_amount'] / $campaign['target_amount']) * 100; ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span><?php echo number_format(($campaign['current_amount'] / $campaign['target_amount']) * 100, 1); ?>% funded</span>
                                    <span>Ksh <?php echo number_format($campaign['current_amount']); ?></span>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php
                                        $days_left = ceil((strtotime($campaign['deadline']) - time()) / (60 * 60 * 24));
                                        echo $days_left > 0 ? $days_left . ' days left' : 'Ended';
                                        ?>
                                    </small>
                                    <a href="src/campaigns/campaign_detail.php?id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;
            endif; ?>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="src/campaigns/campaigns.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-hand-holding-heart me-2"></i>View All Campaigns
                </a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="section-title">How TrueCare Works</h2>
                <p class="text-muted">Simple steps to make a difference</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 text-center">
                <div class="step-card">
                    <div class="step-icon bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                    <h5>1. Create Account</h5>
                    <p class="text-muted">Sign up as a donor or register your orphanage</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 text-center">
                <div class="step-card">
                    <div class="step-icon bg-success text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-search fa-2x"></i>
                    </div>
                    <h5>2. Browse Campaigns</h5>
                    <p class="text-muted">Find causes that resonate with you</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 text-center">
                <div class="step-card">
                    <div class="step-icon bg-info text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-donate fa-2x"></i>
                    </div>
                    <h5>3. Make a Donation</h5>
                    <p class="text-muted">Support campaigns securely via M-Pesa, card, or PayPal</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 text-center">
                <div class="step-card">
                    <div class="step-icon bg-warning text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-heart fa-2x"></i>
                    </div>
                    <h5>4. Track Impact</h5>
                    <p class="text-muted">See how your donation makes a difference</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">Ready to Make a Difference?</h2>
        <p class="lead mb-4">Join thousands of donors who are transforming children's lives through TrueCare.</p>
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <a href="register.php" class="btn btn-success btn-lg">
                <i class="fas fa-user-plus me-2"></i>Join Now
            </a>
            <a href="src/campaigns/campaigns.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-hand-holding-heart me-2"></i>Support a Campaign
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

html, body {
    margin: 0;
    padding: 0;
}
