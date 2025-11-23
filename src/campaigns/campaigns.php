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
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" data-filter="all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="education">Education</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="medical">Medical</button>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <form method="GET" id="search-form" class="d-flex">
                        <input type="text" class="form-control me-2" name="search" placeholder="Search campaigns..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </form>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="sort-select">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Sort by: Newest First</option>
                        <option value="most_funded" <?php echo $sort === 'most_funded' ? 'selected' : ''; ?>>Sort by: Most Funded</option>
                        <option value="ending_soon" <?php echo $sort === 'ending_soon' ? 'selected' : ''; ?>>Sort by: Ending Soon</option>
                        <option value="urgent" <?php echo $sort === 'urgent' ? 'selected' : ''; ?>>Sort by: Most Urgent</option>
                    </select>
                </div>
            </div>

            <!-- Campaigns Grid -->
            <div class="row" id="campaigns-grid">
                <?php 
                // Mock campaign data - in real app, this would come from database with filters
                $campaigns = [];
                for($i = 1; $i <= 6; $i++) {
                    $categories = ['education', 'medical', 'food', 'shelter'];
                    $locations = ['Nairobi, Kenya', 'Mombasa, Kenya', 'Kisumu, Kenya', 'Nakuru, Kenya'];
                    
                    $campaigns[] = [
                        'id' => $i,
                        'title' => 'Education Support for Orphans #' . $i,
                        'description' => 'Providing educational materials, school fees, and learning resources for orphans in need of quality education.',
                        'category' => $categories[array_rand($categories)],
                        'target' => rand(100000, 200000),
                        'raised' => rand(20000, 80000),
                        'location' => $locations[array_rand($locations)],
                        'days_left' => rand(5, 30),
                        'donors' => rand(5, 50)
                    ];
                }

                // Apply filters
                $filtered_campaigns = array_filter($campaigns, function($campaign) use ($search, $category) {
                    $matches = true;
                    
                    if ($search) {
                        $matches = $matches && (stripos($campaign['title'], $search) !== false || 
                                               stripos($campaign['description'], $search) !== false);
                    }
                    
                    if ($category && $category !== 'all') {
                        $matches = $matches && ($campaign['category'] === $category);
                    }
                    
                    return $matches;
                });

                // Apply sorting
                usort($filtered_campaigns, function($a, $b) use ($sort) {
                    switch ($sort) {
                        case 'most_funded':
                            return ($b['raised'] / $b['target']) <=> ($a['raised'] / $a['target']);
                        case 'ending_soon':
                            return $a['days_left'] <=> $b['days_left'];
                        case 'urgent':
                            return ($a['days_left'] <= 7 ? -1 : 1) <=> ($b['days_left'] <= 7 ? -1 : 1);
                        default: // newest
                            return $b['id'] <=> $a['id'];
                    }
                });

                if (empty($filtered_campaigns)): 
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
                <?php else: ?>
                    <?php foreach ($filtered_campaigns as $campaign): 
                    $progress = ($campaign['raised'] / $campaign['target']) * 100;
                    $progress_class = $progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-info' : 'bg-warning');
                    ?>
                    <div class="col-xl-4 col-lg-6 mb-4 campaign-item" data-category="<?php echo $campaign['category']; ?>">
                        <div class="card campaign-card h-100">
                            <div class="position-relative">
                                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-hand-holding-heart fa-4x"></i>
                                </div>
                                <span class="position-absolute top-0 start-0 m-2 badge bg-success text-capitalize">
                                    <?php echo $campaign['category']; ?>
                                </span>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-warning">
                                    <i class="fas fa-clock me-1"></i><?php echo $campaign['days_left']; ?> days left
                                </span>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($campaign['title']); ?></h5>
                                <p class="card-text flex-grow-1"><?php echo htmlspecialchars($campaign['description']); ?></p>
                                
                                <div class="mb-3">
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar <?php echo $progress_class; ?>" style="width: <?php echo $progress; ?>%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span><?php echo number_format($progress, 1); ?>% funded</span>
                                        <span>Ksh <?php echo number_format($campaign['raised']); ?> of Ksh <?php echo number_format($campaign['target']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($campaign['location']); ?>
                                        </small>
                                        <a href="campaign_detail.php?id=<?php echo $campaign['id']; ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category filter buttons
    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            filterCampaigns(filter);
        });
    });

    // Sort select
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }

    // Search form
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

    function filterCampaigns(category) {
        const campaignItems = document.querySelectorAll('.campaign-item');
        campaignItems.forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
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
.campaign-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.campaign-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn-group .btn.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.card-img-top {
    border-radius: 12px 12px 0 0;
}
</style>

<?php include '../../includes/footer.php'; ?>