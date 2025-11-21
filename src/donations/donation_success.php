<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['donation_success'])) {
    header("Location: ../../login.php");
    exit;
}

$donation = $_SESSION['donation_success'];
include '../../includes/config.php';
include '../../includes/header.php';

// Clear the session data after displaying
unset($_SESSION['donation_success']);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <?php include '../auth/sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0 rounded-lg mt-5">
                        <div class="card-header bg-success text-white text-center py-4">
                            <i class="fas fa-check-circle fa-4x mb-3"></i>
                            <h2 class="font-weight-light">Donation Successful!</h2>
                        </div>
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <h4 class="text-success mb-3">Thank You for Your Generosity!</h4>
                                <p class="lead">Your donation of <strong>Ksh <?php echo number_format($donation['amount']); ?></strong> has been successfully processed.</p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-8 mx-auto">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Transaction Details</h6>
                                            <div class="row text-start">
                                                <div class="col-6">
                                                    <small class="text-muted">Amount:</small>
                                                    <p class="mb-1"><strong>Ksh <?php echo number_format($donation['amount']); ?></strong></p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Payment Method:</small>
                                                    <p class="mb-1"><strong><?php echo strtoupper($donation['payment_method']); ?></strong></p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Transaction ID:</small>
                                                    <p class="mb-1"><strong><?php echo $donation['transaction_id']; ?></strong></p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Date:</small>
                                                    <p class="mb-0"><strong><?php echo date('M d, Y H:i'); ?></strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-muted">
                                    A confirmation email has been sent to your registered email address. 
                                    You can track your donation in your dashboard.
                                </p>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="../auth/dashboard.php" class="btn btn-primary btn-lg me-md-2">
                                    <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                                </a>
                                <a href="../campaigns/campaigns.php" class="btn btn-outline-success btn-lg">
                                    <i class="fas fa-hand-holding-heart me-2"></i>Browse More Campaigns
                                </a>
                            </div>

                            <div class="mt-4">
                                <small class="text-muted">
                                    Need help? <a href="#" class="text-decoration-none">Contact Support</a>
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