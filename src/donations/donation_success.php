<?php
session_start();

// Validate user and donation session
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_SESSION['donation_success'])) {
    header("Location: ../campaigns/campaigns.php");
    exit;
}

$donation = $_SESSION['donation_success'];

require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/header.php';

// Start DB if needed
$db = get_db();

// Clear success session so refresh doesn't duplicate
unset($_SESSION['donation_success']);

// Detect sidebar file based on user role
$role = getUserRole();
$sidebar_path = "../auth/sidebar.php";

?>
<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <?php include $sidebar_path; ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="card shadow-lg border-0 rounded-lg mt-5">

                        <div class="card-header bg-success text-white text-center py-4">
                            <i class="fas fa-check-circle fa-4x mb-3"></i>
                            <h2 class="fw-light">Donation Successful!</h2>
                        </div>

                        <div class="card-body text-center p-5">

                            <h4 class="text-success mb-3">Thank You for Your Generosity!</h4>
                            <p class="lead">
                                Your donation of 
                                <strong>Ksh <?php echo number_format($donation['amount'] ?? 0); ?></strong> 
                                has been successfully processed.
                            </p>

                            <!-- Transaction Details -->
                            <div class="row mb-4">
                                <div class="col-md-8 mx-auto">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Transaction Details</h6>

                                            <div class="row text-start">

                                                <div class="col-6">
                                                    <small class="text-muted">Amount:</small>
                                                    <p class="mb-1">
                                                        <strong>Ksh <?php echo number_format($donation['amount'] ?? 0); ?></strong>
                                                    </p>
                                                </div>

                                                <div class="col-6">
                                                    <small class="text-muted">Payment Method:</small>
                                                    <p class="mb-1">
                                                        <strong><?php echo htmlspecialchars(strtoupper($donation['payment_method'] ?? 'N/A')); ?></strong>
                                                    </p>
                                                </div>

                                                <div class="col-6">
                                                    <small class="text-muted">Transaction ID:</small>
                                                    <p class="mb-1">
                                                        <strong><?php echo htmlspecialchars($donation['transaction_id'] ?? 'N/A'); ?></strong>
                                                    </p>
                                                </div>

                                                <div class="col-6">
                                                    <small class="text-muted">Date:</small>
                                                    <p class="mb-0">
                                                        <strong><?php echo date('M d, Y H:i'); ?></strong>
                                                    </p>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="text-muted">
                                A confirmation email has been sent to your registered email address.<br>
                                You can track your donation in your dashboard.
                            </p>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">

                                <a href="../auth/dashboard.php" 
                                   class="btn btn-primary btn-lg me-md-2">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Go to Dashboard
                                </a>

                                <a href="../campaigns/campaigns.php" 
                                   class="btn btn-outline-success btn-lg">
                                    <i class="fas fa-hand-holding-heart me-2"></i>
                                    Browse More Campaigns
                                </a>

                            </div>

                            <div class="mt-4">
                                <small class="text-muted">
                                    Need help? 
                                    <a href="#" class="text-decoration-none">Contact Support</a>
                                </small>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </main>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>
