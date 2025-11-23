<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$campaign_id = $_GET['id'] ?? 1;
include '../../includes/config.php';
include '../../includes/header.php';

// Fetch campaign details from database
$stmt = $db->prepare("SELECT c.*, o.name AS orphanage_name, o.location AS orphanage_location FROM campaigns c LEFT JOIN orphanages o ON c.orphanage_id = o.orphanage_id WHERE c.campaign_id = :id");
$stmt->bindParam(':id', $campaign_id);
$stmt->execute();
$campaign = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$campaign) {
    echo '<div class="container py-5"><div class="alert alert-danger">Campaign not found.</div></div>';
    include '../../includes/footer.php';
    exit;
}
$progress = ($campaign['current_amount'] / $campaign['target_amount']) * 100;
$days_left = ceil((strtotime($campaign['deadline']) - time()) / (60 * 60 * 24));
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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../auth/dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="campaigns.php">Campaigns</a></li>
                        <li class="breadcrumb-item active"><?php echo $campaign['title']; ?></li>
                    </ol>
                </nav>
                <a href="campaigns.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to Campaigns
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Campaign Image -->
                    <div class="card shadow mb-4">
                        <?php
                        $category = isset($campaign['category']) && !empty($campaign['category']) ? strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $campaign['category'])) : 'default';
                        $image_path = "../../assets/images/campaigns/{$category}.jpg";
                        if (!file_exists($image_path)) {
                            $image_path = "../../assets/images/campaigns/default.jpg";
                        }
                        ?>
                        <img src="<?php echo $image_path; ?>" class="card-img-top" alt="Campaign" style="max-height: 400px; object-fit: cover;">
                    </div>

                    <!-- Campaign Details -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h5 class="m-0 font-weight-bold text-primary">About This Campaign</h5>
                        </div>
                        <div class="card-body">
                            <p><?php echo $campaign['description']; ?></p>
                            
                            <h6 class="mt-4">How Your Donation Will Help:</h6>
                            <ul>
                                <li>Provide essential educational materials and resources</li>
                                <li>Cover school fees and related expenses</li>
                                <li>Support extracurricular activities and development</li>
                                <li>Ensure proper nutrition during school days</li>
                                <li>Provide transportation to and from school</li>
                            </ul>

                            <h6 class="mt-4">Impact Story:</h6>
                            <p>With your support, we can transform the lives of these children by giving them access to quality education. Education is the key to breaking the cycle of poverty and giving these orphans a chance at a better future.</p>
                        </div>
                    </div>

                    <!-- Updates section removed -->
                </div>

                <div class="col-lg-4">
                    <!-- Donation Card -->
                    <div class="card shadow sticky-top" style="top: 20px;">
                        <div class="card-header py-3 bg-success text-white">
                            <h5 class="m-0 font-weight-bold">Support This Campaign</h5>
                        </div>
                        <div class="card-body">
                            <!-- Progress -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Raised</span>
                                    <span class="text-muted"><?php echo number_format($progress, 1); ?>%</span>
                                </div>
                                <div class="progress mb-2" style="height: 12px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo $progress; ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <strong>Ksh <?php echo number_format(isset($campaign['raised']) ? $campaign['raised'] : 0); ?></strong>
                                    <strong>Ksh <?php echo number_format(isset($campaign['target']) ? $campaign['target'] : 0); ?></strong>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <h6 class="mb-0 text-success"><?php echo isset($campaign['donors']) ? $campaign['donors'] : 0; ?></h6>
                                        <small class="text-muted">Donors</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <h6 class="mb-0 text-warning"><?php echo $days_left; ?></h6>
                                        <small class="text-muted">Days Left</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2 bg-light d-flex flex-column align-items-center justify-content-center" style="min-height: 70px;">
                                        <span class="badge bg-primary text-uppercase px-2 py-1" style="font-size: 0.85em; letter-spacing: 0.5px; white-space: nowrap;">
                                            <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($campaign['category']); ?>
                                        </span>
                                        <small class="d-block mt-1 text-muted" style="font-size: 0.85em;">Category</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Donate Buttons -->
                            <div class="mb-3">
                                <label class="form-label">Quick Donate</label>
                                <div class="row g-2 mb-3">
                                    <?php 
                                    $quick_amounts = [500, 1000, 2000, 5000];
                                    foreach ($quick_amounts as $amount): 
                                    ?>
                                    <div class="col-6">
                                        <a href="../donations/donate.php?campaign_id=<?php echo $campaign_id; ?>&amount=<?php echo $amount; ?>" 
                                           class="btn btn-outline-success w-100 btn-sm">
                                            Ksh <?php echo number_format($amount); ?>
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Donate Button -->
                            <a href="../donations/donate.php?campaign_id=<?php echo $campaign_id; ?>" 
                               class="btn btn-success btn-lg w-100 py-3">
                                <i class="fas fa-donate me-2"></i>Donate Now
                            </a>

                            <!-- Share -->
                            <div class="text-center mt-3">
                                <small class="text-muted">Share this campaign</small>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fab fa-facebook-f"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info me-1">
                                        <i class="fab fa-twitter"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success me-1">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-link"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Organizer Info -->
                    <div class="card shadow mt-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Organizer</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <img src="../../assets/images/orphanage.png" alt="Orphanage" class="rounded-circle me-3" width="50">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($campaign['orphanage_name']); ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($campaign['orphanage_location']); ?>
                                    </small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-certificate me-1 text-success"></i>
                                    Registered orphanage
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
}
.timeline-badge {
    position: absolute;
    left: -30px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
}
.timeline-panel {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}
.sticky-top {
    position: -webkit-sticky;
    position: sticky;
}
</style>

<?php include '../../includes/footer.php'; ?>