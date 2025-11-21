<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}
include '../../includes/config.php';
include '../../includes/header.php';
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
                <h1 class="h2">Browse Campaigns</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Education</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Medical</button>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search campaigns by title, description, or orphanage...">
                        <button class="btn btn-outline-primary" type="button">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select">
                        <option>Sort by: Newest First</option>
                        <option>Sort by: Most Funded</option>
                        <option>Sort by: Ending Soon</option>
                        <option>Sort by: Most Urgent</option>
                    </select>
                </div>
            </div>

            <!-- Campaigns Grid -->
            <div class="row" id="campaigns-grid">
                <?php for($i = 1; $i <= 6; $i++): ?>
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card campaign-card h-100">
                        <div class="position-relative">
                            <img src="../../assets/images/campaign<?php echo $i; ?>.jpg" class="card-img-top" alt="Campaign" height="200" style="object-fit: cover;">
                            <span class="position-absolute top-0 start-0 m-2 badge bg-success">Education</span>
                            <span class="position-absolute top-0 end-0 m-2 badge bg-warning">
                                <i class="fas fa-clock me-1"></i><?php echo rand(5, 30); ?> days left
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Education Support for Orphans #<?php echo $i; ?></h5>
                            <p class="card-text flex-grow-1">Providing educational materials, school fees, and learning resources for orphans in need of quality education.</p>
                            
                            <div class="mb-3">
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo rand(20, 90); ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span><?php echo rand(20, 90); ?>% funded</span>
                                    <span>Ksh <?php echo number_format(rand(20000, 80000)); ?> of Ksh <?php echo number_format(rand(100000, 200000)); ?></span>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        Nairobi, Kenya
                                    </small>
                                    <a href="campaign_detail.php?id=<?php echo $i; ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Pagination -->
            <nav aria-label="Campaign pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </main>
    </div>
</div>

<style>
.campaign-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.campaign-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>

<?php include '../../includes/footer.php'; ?>