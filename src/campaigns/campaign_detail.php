<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$campaign_id = $_GET['id'] ?? 1;
include '../../includes/config.php';
include '../../includes/header.php';

// Mock campaign data
$campaigns = [
    1 => [
        'title' => 'Education for Orphans', 
        'description' => 'Help provide quality education for 50 orphans in Nairobi. This campaign aims to cover school fees, educational materials, uniforms, and other essential learning resources for one academic year.',
        'target' => 100000,
        'raised' => 65000,
        'image' => 'campaign1.jpg',
        'category' => 'Education',
        'orphanage' => 'Hope Children Center',
        'location' => 'Nairobi, Kenya',
        'deadline' => '2024-12-31',
        'created' => '2024-01-15',
        'donors' => 24
    ],
    2 => [
        'title' => 'Medical Supplies', 
        'description' => 'Urgent need for medical supplies and healthcare services for orphans. This will cover vaccinations, routine checkups, and essential medications.',
        'target' => 150000,
        'raised' => 45000,
        'image' => 'campaign2.jpg',
        'category' => 'Medical',
        'orphanage' => 'Grace Orphanage',
        'location' => 'Mombasa, Kenya',
        'deadline' => '2024-11-30',
        'created' => '2024-02-01',
        'donors' => 18
    ]
];

$campaign = $campaigns[$campaign_id] ?? $campaigns[1];
$progress = ($campaign['raised'] / $campaign['target']) * 100;
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
                        <img src="../../assets/images/<?php echo $campaign['image']; ?>" class="card-img-top" alt="Campaign" style="max-height: 400px; object-fit: cover;">
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

                    <!-- Updates -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h5 class="m-0 font-weight-bold text-primary">Campaign Updates</h5>
                            <span class="badge bg-primary">2 Updates</span>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item mb-4">
                                    <div class="timeline-badge bg-success"></div>
                                    <div class="timeline-panel">
                                        <div class="timeline-heading">
                                            <h6 class="timeline-title">Campaign Launched</h6>
                                            <small class="text-muted"><i class="fas fa-clock me-1"></i><?php echo $campaign['created']; ?></small>
                                        </div>
                                        <div class="timeline-body">
                                            <p>We've officially launched our campaign to support education for orphans. Thank you for considering to be part of this journey!</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-badge bg-info"></div>
                                    <div class="timeline-panel">
                                        <div class="timeline-heading">
                                            <h6 class="timeline-title">First Milestone Reached</h6>
                                            <small class="text-muted"><i class="fas fa-clock me-1"></i>2 weeks ago</small>
                                        </div>
                                        <div class="timeline-body">
                                            <p>We've reached 30% of our goal! Thank you to all our early supporters. Your contributions are already making a difference.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <strong>Ksh <?php echo number_format($campaign['raised']); ?></strong>
                                    <strong>Ksh <?php echo number_format($campaign['target']); ?></strong>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <h6 class="mb-0 text-success"><?php echo $campaign['donors']; ?></h6>
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
                                    <div class="border rounded p-2">
                                        <h6 class="mb-0 text-info"><?php echo $campaign['category']; ?></h6>
                                        <small class="text-muted">Category</small>
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
                                    <h6 class="mb-0"><?php echo $campaign['orphanage']; ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo $campaign['location']; ?>
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