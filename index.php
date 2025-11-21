<?php
include 'includes/config.php';
$page_title = "TrueCare - Support Orphanages in Need";
$show_navbar = true;
include 'includes/header.php';
?>

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
                <img src="assets/images/hero-children.png" alt="Children Smiling" class="img-fluid rounded-3" style="max-height: 400px;">
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <h2 class="display-4 text-primary fw-bold">1,248+</h2>
                    <p class="text-muted">Children Helped</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <h2 class="display-4 text-success fw-bold">Ksh 2.4M+</h2>
                    <p class="text-muted">Total Donations</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <h2 class="display-4 text-info fw-bold">156+</h2>
                    <p class="text-muted">Active Campaigns</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <h2 class="display-4 text-warning fw-bold">42+</h2>
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
            <?php for($i = 1; $i <= 3; $i++): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <img src="assets/images/campaign<?php echo $i; ?>.jpg" class="card-img-top" alt="Campaign" height="200" style="object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-success mb-2">Education</span>
                        <h5 class="card-title">Education Support #<?php echo $i; ?></h5>
                        <p class="card-text flex-grow-1">Providing quality education for orphans in need of support and opportunities.</p>
                        
                        <div class="mb-3">
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?php echo rand(30, 80); ?>%"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span><?php echo rand(30, 80); ?>% funded</span>
                                <span>Ksh <?php echo number_format(rand(30000, 80000)); ?></span>
                            </div>
                        </div>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo rand(5, 30); ?> days left
                                </small>
                                <a href="src/campaigns/campaign_detail.php?id=<?php echo $i; ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
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