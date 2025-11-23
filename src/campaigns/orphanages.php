<?php 
include '../../includes/config.php';
require_once '../../includes/functions.php';

// Ensure $db is defined
if (!isset($db)) {
    $db = get_db();
}

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(BASE_URL . '/login.php');
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Get filter parameters
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';

try {
    // Build query
    $query = "SELECT o.*, u.email, u.phone,
                     COUNT(c.campaign_id) as campaign_count,
                     SUM(c.current_amount) as total_raised
              FROM orphanages o 
              JOIN users u ON o.user_id = u.user_id
              LEFT JOIN campaigns c ON o.orphanage_id = c.orphanage_id AND c.status = 'active'
              WHERE o.status = 'verified'";
    
    $params = [];

    // Apply search filter
    if (!empty($search)) {
        $query .= " AND (o.name LIKE :search OR o.description LIKE :search)";
        $params[':search'] = "%$search%";
    }

    // Apply location filter
    if (!empty($location)) {
        $query .= " AND o.location LIKE :location";
        $params[':location'] = "%$location%";
    }

    $query .= " GROUP BY o.orphanage_id ORDER BY o.name ASC";

    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $orphanages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get unique locations for filter
    $locations_query = "SELECT DISTINCT location FROM orphanages WHERE location IS NOT NULL AND location != '' ORDER BY location";
    $locations_stmt = $db->query($locations_query);
    $locations = $locations_stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("Orphanages page error: " . $e->getMessage());
    $error = "Error loading orphanages. Please try again.";
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-home me-2 text-primary"></i>
                        Verified Orphanages
                    </h1>
                    <p class="text-muted mb-0">
                        Discover and support verified orphanages across Kenya
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Orphanages</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search by name or description...">
                        </div>
                        <div class="col-md-3">
                            <label for="location" class="form-label">Location</label>
                            <select class="form-select" id="location" name="location">
                                <option value="">All Locations</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?php echo $loc; ?>" 
                                        <?php echo $location === $loc ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($loc); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Apply Filters
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <a href="<?php echo BASE_URL; ?>/src/campaigns/orphanages.php" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Orphanages Grid -->
    <div class="row">
        <?php if (!empty($orphanages)): ?>
            <?php foreach ($orphanages as $orphanage): ?>
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card orphanage-card h-100">
                        <div class="card-img-top orphanage-image" 
                             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                    height: 200px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-home text-white" style="font-size: 4rem;"></i>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <!-- Orphanage Header -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Verified
                                </span>
                                <?php if ($orphanage['location']): ?>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($orphanage['location']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <!-- Orphanage Name & Description -->
                            <h5 class="card-title"><?php echo htmlspecialchars($orphanage['name']); ?></h5>
                            <p class="card-text flex-grow-1 text-muted">
                                <?php 
                                $description = $orphanage['description'] ?: 'No description available.';
                                echo strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                                ?>
                            </p>

                            <!-- Orphanage Stats -->
                            <div class="orphanage-stats mb-3">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h6 class="text-primary mb-1"><?php echo $orphanage['campaign_count']; ?></h6>
                                            <small class="text-muted">Campaigns</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div>
                                            <h6 class="text-success mb-1">KES <?php echo number_format($orphanage['total_raised'] ?? 0); ?></h6>
                                            <small class="text-muted">Total Raised</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Registration Info -->
                            <?php if ($orphanage['registration_number']): ?>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-id-card me-1"></i>
                                        Reg: <?php echo htmlspecialchars($orphanage['registration_number']); ?>
                                    </small>
                                </div>
                            <?php endif; ?>

                            <!-- Action Buttons -->
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <a href="<?php echo BASE_URL; ?>/src/campaigns/campaigns.php?search=<?php echo urlencode($orphanage['name']); ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-hand-holding-heart me-2"></i>View Campaigns
                                    </a>
                                    <?php if ($orphanage['contact_info']): ?>
                                        <button class="btn btn-outline-secondary btn-sm" 
                                                onclick="alert('Contact: <?php echo htmlspecialchars($orphanage['contact_info']); ?>')">
                                            <i class="fas fa-phone me-2"></i>Contact
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-home fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No Orphanages Found</h4>
                        <p class="text-muted mb-4">
                            No orphanages match your search criteria.
                        </p>
                        <a href="<?php echo BASE_URL; ?>/src/campaigns/orphanages.php" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>View All Orphanages
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>