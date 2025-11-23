<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}
include '../../includes/config.php';
include '../../includes/header.php';

// Get filter parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
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
                <div class="btn-toolbar mb-2 mb-md-0 w-100" style="max-width: 400px;">
                    <select class="form-select form-select-lg" id="sort-select" style="min-width: 250px;">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Sort by: Newest First</option>
                        <option value="most_funded" <?php echo $sort === 'most_funded' ? 'selected' : ''; ?>>Sort by: Most Funded</option>
                        <option value="ending_soon" <?php echo $sort === 'ending_soon' ? 'selected' : ''; ?>>Sort by: Ending Soon</option>
                        <option value="urgent" <?php echo $sort === 'urgent' ? 'selected' : ''; ?>>Sort by: Most Urgent</option>
                    </select>
                </div>
            </div>

            <!-- Campaigns Grid -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4 justify-content-center" id="campaigns-grid" style="margin-left:0;margin-right:0;">
                <?php
                // Fetch campaigns from database with all necessary fields
                    $query = "SELECT campaign_id, title, description, category, target_amount, current_amount, deadline, created_at FROM campaigns WHERE status='active'";
                
                // Apply filtering with prepared statements
                $params = [];
                $conditions = [];
                
                if (!empty($search)) {
                    $conditions[] = "(title LIKE ? OR description LIKE ?)";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                }
                
                if (!empty($category) && $category !== 'all') {
                    $conditions[] = "category = ?";
                    $params[] = $category;
                }
                
                if (!empty($location)) {
                    $conditions[] = "location LIKE ?";
                    $params[] = "%$location%";
                }
                
                if (!empty($conditions)) {
                    $query .= " AND " . implode(" AND ", $conditions);
                }
                
                // Add sorting
                switch ($sort) {
                    case 'most_funded':
                        $query .= " ORDER BY (current_amount/target_amount) DESC";
                        break;
                    case 'ending_soon':
                        $query .= " ORDER BY deadline ASC";
                        break;
                    case 'urgent':
                        $query .= " ORDER BY deadline ASC";
                        break;
                    default: // newest
                        $query .= " ORDER BY created_at DESC";
                        break;
                }
                
                try {
                    $stmt = $db->prepare($query);
                    $stmt->execute($params);
                    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Apply urgent sorting in PHP (needs date calculation)
                    if ($sort === 'urgent') {
                        usort($campaigns, function($a, $b) {
                            $a_days = ceil((strtotime($a['deadline']) - time()) / (60 * 60 * 24));
                            $b_days = ceil((strtotime($b['deadline']) - time()) / (60 * 60 * 24));
                            $a_urgent = $a_days <= 7 ? -1 : 1;
                            $b_urgent = $b_days <= 7 ? -1 : 1;
                            return $a_urgent <=> $b_urgent;
                        });
                    }

                    if (empty($campaigns)) {
                ?>
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No Campaigns Found</h4>
                            <p class="text-muted">No campaigns match your search criteria. Try different filters.</p>
                            <a href="campaigns.php" class="btn btn-primary">Clear Filters</a>
                        </div>
                    </div>
                </div>
                <?php 
                    } else {
                        foreach ($campaigns as $campaign) {
                            $progress = $campaign['target_amount'] > 0 ? ($campaign['current_amount'] / $campaign['target_amount']) * 100 : 0;
                            $progress_class = $progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-info' : 'bg-warning');
                            $days_left = ceil((strtotime($campaign['deadline']) - time()) / (60 * 60 * 24));
                            
                            // Determine campaign image
                            $category = isset($campaign['category']) && !empty($campaign['category']) ? strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $campaign['category'])) : 'default';
                            $campaign_image = '../../assets/images/campaigns/' . $category . '.jpg';
                            if (!file_exists($campaign_image)) {
                                $campaign_image = '../../assets/images/campaigns/default.jpg';
                            }
                ?>
                    <div class="col campaign-item d-flex align-items-stretch p-2" data-category="<?php echo htmlspecialchars($campaign['category']); ?>">
                        <div class="card campaign-card h-100 d-flex flex-column border-0 shadow-sm amazon-card">
                            <div class="position-relative text-center bg-white" style="padding-top:16px;">
                                <img src="<?php echo $campaign_image; ?>" class="card-img-top mx-auto" alt="<?php echo htmlspecialchars($campaign['title']); ?>" style="height: 180px; width: 90%; object-fit: contain; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);" onerror="this.onerror=null;this.src='../../assets/images/campaigns/default.jpg';">
                                <span class="badge bg-success text-capitalize position-absolute top-0 start-0 m-2" style="font-size:0.9em;"> <?php echo htmlspecialchars(ucfirst($campaign['category'])); ?> </span>
                                <span class="badge <?php echo $days_left <= 7 ? 'bg-danger' : 'bg-warning'; ?> position-absolute top-0 end-0 m-2" style="font-size:0.9em;"> <i class="fas fa-clock me-1"></i><?php echo $days_left > 0 ? $days_left . ' days left' : 'Ended'; ?> </span>
                            </div>
                            <div class="card-body d-flex flex-column px-3 pb-3 pt-2">
                                <h6 class="card-title fw-bold mb-1 text-dark text-truncate" title="<?php echo htmlspecialchars($campaign['title']); ?>"><?php echo htmlspecialchars($campaign['title']); ?></h6>
                                <p class="card-text text-muted flex-grow-1 mb-2" style="min-height: 40px; font-size:0.97em;"> <?php echo htmlspecialchars($campaign['description']); ?> </p>
                                <div class="mb-2">
                                    <div class="progress mb-1" style="height: 7px;">
                                        <div class="progress-bar <?php echo $progress_class; ?>" style="width: <?php echo min($progress, 100); ?>%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted" style="font-size:0.95em;">
                                        <span><?php echo number_format($progress, 1); ?>% funded</span>
                                        <span>Ksh <?php echo number_format($campaign['current_amount']); ?> of Ksh <?php echo number_format($campaign['target_amount']); ?></span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-muted" style="font-size:0.95em;"><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($campaign['location'] ?? ''); ?></span>
                                    <a href="campaign_detail.php?id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-warning btn-sm px-3" style="font-weight:500;">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                        }
                    }
                } catch (PDOException $e) {
                    echo '<div class="col-12"><div class="alert alert-danger">Error loading campaigns: ' . htmlspecialchars($e->getMessage()) . '</div></div>';
                }
                ?>
            </div>

            <!-- Pagination - Only show if there are campaigns -->
            <?php if (!empty($campaigns)): ?>
            <div class="row">
                <div class="col-12">
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
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sort select
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }

    // Search form functionality
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchInput = this.querySelector('input[name="search"]');
            if (searchInput.value.trim()) {
                this.submit();
            }
        });
    }

    // Campaign card hover effects
    const campaignCards = document.querySelectorAll('.campaign-card');
    campaignCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
        });
    });
});
</script>

<style>

.amazon-card {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.09);
    background: #fff;
    transition: box-shadow 0.2s, transform 0.2s;
    display: flex;
    flex-direction: column;
    height: 100%;
    border: 1px solid #f2f2f2;
}
.amazon-card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    transform: translateY(-3px) scale(1.02);
    z-index: 2;
}

.btn-group .btn.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.card-img-top {
    border-radius: 12px 12px 0 0;
}

.progress {
    border-radius: 4px;
}
</style>

<?php include '../../includes/footer.php'; ?>