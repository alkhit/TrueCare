<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Check if campaign_id is provided
$campaign_id = $_GET['campaign_id'] ?? 1; // Default to 1 if not provided

include '../../includes/config.php';
include '../../includes/header.php';

// Mock campaign data - in real app, fetch from database
$campaigns = [
    1 => ['title' => 'Education for Orphans', 'target' => 100000, 'raised' => 65000],
    2 => ['title' => 'Medical Supplies', 'target' => 150000, 'raised' => 45000],
    3 => ['title' => 'Food and Shelter', 'target' => 200000, 'raised' => 120000]
];

$campaign = $campaigns[$campaign_id] ?? $campaigns[1];
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <?php include '../auth/sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Make a Donation</h1>
                <a href="../campaigns/campaigns.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to Campaigns
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h5 class="m-0 font-weight-bold text-primary">Donation Details</h5>
                        </div>
                        <div class="card-body">
                            <!-- Campaign Info -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <img src="../../assets/images/<?php echo $campaign['image']; ?>" class="img-fluid rounded" alt="Campaign">
                                </div>
                                <div class="col-md-9">
                                    <h5><?php echo $campaign['title']; ?></h5>
                                    <div class="progress mb-2" style="height: 10px;">
                                        <div class="progress-bar bg-success" style="width: <?php echo ($campaign['raised'] / $campaign['target']) * 100; ?>%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between text-sm text-muted">
                                        <span>Ksh <?php echo number_format($campaign['raised']); ?> raised</span>
                                        <span>Ksh <?php echo number_format($campaign['target']); ?> goal</span>
                                        <span><?php echo number_format(($campaign['raised'] / $campaign['target']) * 100, 1); ?>% funded</span>
                                    </div>
                                </div>
                            </div>

                            <form id="donation-form" action="process_donation.php" method="POST">
                                <input type="hidden" name="campaign_id" value="<?php echo $campaign_id; ?>">
                                
                                <!-- Amount Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Donation Amount (Ksh)</label>
                                    <div class="row g-2 mb-3">
                                        <?php 
                                        $amounts = [500, 1000, 2000, 5000];
                                        foreach ($amounts as $amount): 
                                        ?>
                                        <div class="col-6 col-sm-3">
                                            <button type="button" class="btn btn-outline-primary w-100 amount-btn" data-amount="<?php echo $amount; ?>">
                                                Ksh <?php echo number_format($amount); ?>
                                            </button>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text">Ksh</span>
                                        <input type="number" class="form-control" id="custom-amount" name="amount" 
                                               placeholder="Enter custom amount" min="100" required>
                                    </div>
                                    <div class="form-text">Minimum donation: Ksh 100</div>
                                </div>

                                <!-- Payment Method -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Payment Method</label>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-check card payment-method-card">
                                                <input class="form-check-input" type="radio" name="payment_method" id="mpesa" value="mpesa" checked>
                                                <label class="form-check-label card-body text-center" for="mpesa">
                                                    <i class="fas fa-mobile-alt fa-3x text-success mb-2"></i>
                                                    <h6>M-Pesa</h6>
                                                    <small class="text-muted">Mobile Money</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check card payment-method-card">
                                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                                                <label class="form-check-label card-body text-center" for="card">
                                                    <i class="fas fa-credit-card fa-3x text-primary mb-2"></i>
                                                    <h6>Card</h6>
                                                    <small class="text-muted">Credit/Debit</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check card payment-method-card">
                                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                                <label class="form-check-label card-body text-center" for="paypal">
                                                    <i class="fab fa-paypal fa-3x text-info mb-2"></i>
                                                    <h6>PayPal</h6>
                                                    <small class="text-muted">Online Payment</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details -->
                                <div id="payment-details">
                                    <!-- M-Pesa Details -->
                                    <div id="mpesa-details" class="payment-detail-section">
                                        <div class="mb-3">
                                            <label for="mpesa-phone" class="form-label">M-Pesa Phone Number</label>
                                            <input type="tel" class="form-control" id="mpesa-phone" name="mpesa_phone" 
                                                   placeholder="07XXXXXXXX" pattern="[0-9]{10}">
                                            <div class="form-text">Enter your Safaricom M-Pesa number</div>
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            You will receive a prompt on your phone to confirm the payment.
                                        </div>
                                    </div>

                                    <!-- Card Details -->
                                    <div id="card-details" class="payment-detail-section" style="display: none;">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="card-number" class="form-label">Card Number</label>
                                                <input type="text" class="form-control" id="card-number" name="card_number" 
                                                       placeholder="1234 5678 9012 3456" maxlength="19">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="expiry" class="form-label">Expiry Date</label>
                                                <input type="text" class="form-control" id="expiry" name="expiry" 
                                                       placeholder="MM/YY" maxlength="5">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="cvv" class="form-label">CVV</label>
                                                <input type="text" class="form-control" id="cvv" name="cvv" 
                                                       placeholder="123" maxlength="3">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="card-name" class="form-label">Name on Card</label>
                                                <input type="text" class="form-control" id="card-name" name="card_name" 
                                                       placeholder="John Doe">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PayPal Details -->
                                    <div id="paypal-details" class="payment-detail-section" style="display: none;">
                                        <div class="alert alert-info">
                                            <i class="fab fa-paypal me-2"></i>
                                            You will be redirected to PayPal to complete your payment securely.
                                        </div>
                                    </div>
                                </div>

                                <!-- Donation Message -->
                                <div class="mb-4">
                                    <label for="message" class="form-label">Donation Message (Optional)</label>
                                    <textarea class="form-control" id="message" name="message" rows="3" 
                                              placeholder="Add a message of support or encouragement..."></textarea>
                                    <div class="form-text">This message will be visible to the orphanage</div>
                                </div>

                                <!-- Anonymous Donation -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="anonymous" name="anonymous">
                                        <label class="form-check-label" for="anonymous">
                                            Make this donation anonymous
                                        </label>
                                    </div>
                                    <div class="form-text">Your name will not be shown publicly</div>
                                </div>

                                <!-- Terms -->
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" target="_blank">Terms of Service</a> and 
                                        <a href="#" target="_blank">Privacy Policy</a>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100 py-3">
                                    <i class="fas fa-donate me-2"></i>Complete Donation
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card shadow sticky-top" style="top: 20px;">
                        <div class="card-header py-3">
                            <h5 class="m-0 font-weight-bold text-primary">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Donation Amount:</span>
                                <strong id="summary-amount">Ksh 0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Processing Fee (1.5%):</span>
                                <span id="summary-fee">Ksh 0</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span><strong>Total Amount:</strong></span>
                                <strong id="summary-total">Ksh 0</strong>
                            </div>
                            <div class="alert alert-success">
                                <small>
                                    <i class="fas fa-check-circle me-2"></i>
                                    Your donation is tax deductible
                                </small>
                            </div>
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-shield-alt me-2"></i>
                                    Secure SSL encrypted payment
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Amount selection
    const amountBtns = document.querySelectorAll('.amount-btn');
    const customAmount = document.getElementById('custom-amount');
    const summaryAmount = document.getElementById('summary-amount');
    const summaryFee = document.getElementById('summary-fee');
    const summaryTotal = document.getElementById('summary-total');

    function updateSummary(amount) {
        const fee = Math.max(10, amount * 0.015); // 1.5% fee, min 10 Ksh
        const total = amount + fee;
        
        summaryAmount.textContent = `Ksh ${amount.toLocaleString()}`;
        summaryFee.textContent = `Ksh ${fee.toLocaleString()}`;
        summaryTotal.textContent = `Ksh ${total.toLocaleString()}`;
        
        // Update the hidden amount field
        customAmount.value = amount;
    }

    amountBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            amountBtns.forEach(b => b.classList.remove('btn-primary', 'active'));
            this.classList.add('btn-primary', 'active');
            customAmount.value = '';
            updateSummary(parseInt(this.dataset.amount));
        });
    });

    customAmount.addEventListener('input', function() {
        amountBtns.forEach(b => b.classList.remove('btn-primary', 'active'));
        if (this.value) {
            updateSummary(parseInt(this.value));
        }
    });

    // Payment method switching
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentSections = document.querySelectorAll('.payment-detail-section');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            paymentSections.forEach(section => section.style.display = 'none');
            document.getElementById(this.value + '-details').style.display = 'block';
        });
    });

    // Initialize with first amount
    if (amountBtns.length > 0) {
        amountBtns[0].click();
    }
});
</script>

<style>
.payment-method-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.payment-method-card:hover {
    border-color: #007bff;
}
.payment-method-card .form-check-input {
    position: absolute;
    top: 10px;
    left: 10px;
}
.payment-method-card.active {
    border-color: #007bff;
    background-color: #f8f9fa;
}
.sticky-top {
    position: -webkit-sticky;
    position: sticky;
}
</style>

<?php include '../../includes/footer.php'; ?>