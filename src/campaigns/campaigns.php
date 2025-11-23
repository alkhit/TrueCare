<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/header.php';

// Get DB connection
$db = get_db();

// Get filter parameters
$search   = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$sort     = $_GET['sort'] ?? 'newest';

?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Browse Campaigns</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <select class="form-select form-select-sm" id="sort-select" style="min-width: 200px;">
            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Sort by: Newest First</option>
            <option value="most_funded" <?php echo $sort === 'most_funded' ? 'selected' : ''; ?>>Sort by: Most Funded</option>
            <option value="ending_soon" <?php echo $sort === 'ending_soon' ? 'selected' : ''; ?>>Sort by: Ending Soon</option>
            <option value="urgent" <?php echo $sort === 'urgent' ? 'selected' : ''; ?>>Sort by: Most Urgent</option>
        </select>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Campaigns</label>
                <input type="text" class="form-control" id="search" name="search"
                       value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Search by title or description...">
            </div>

            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    <option value="education" <?php echo $category === 'education' ? 'selected' : ''; ?>>Education</option>
                    <option value="food" <?php echo $category === 'food' ? 'selected' : ''; ?>>Food</option>
                    <option value="medical" <?php echo $category === 'medical' ? 'selected' : ''; ?>>Medical</option>
                    <option value="shelter" <?php echo $category === 'shelter' ? 'selected' : ''; ?>>Shelter</option>
                    <option value="clothing" <?php echo $category === 'clothing' ? 'selected' : ''; ?>>Clothing</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location"
                       value="<?php echo htmlspecialchars($location); ?>"
                       placeholder="Enter location...">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<!-- Campaigns Grid -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4 justify-content-center" id="campaigns-grid">

<?php
// Base query
$query = "SELECT c.campaign_id, c.title, c.description, c.category, c.target_amount, 
                 c.current_amount, c.deadline, c.created_at, o.location 
          FROM campaigns c 
          LEFT JOIN orphanages o ON c.orphanage_id = o.orphanage_id 
          WHERE c.status='active'";

$params = [];
$conditions = [];

// Search filter
if (!empty($search)) {
    $conditions[] = "(c.title LIKE ? OR c.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Category filter
if (!empty($category)) {
    $conditions[] = "c.category = ?";
    $params[] = $category;
}

// Location filter
if (!empty($location)) {
    $conditions[] = "o.location LIKE ?";
    $params[] = "%$location%";
}

if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

// Sorting
switch ($sort) {
    case 'most_funded':
        $query .= " ORDER BY (c.current_amount/c.target_amount) DESC";
        break;
    case 'ending_soon':
        $query .= " ORDER BY c.deadline ASC";
        break;
    case 'urgent':
        $query .= " ORDER BY c.deadline ASC, (c.current_amount/c.target_amount) ASC";
        break;
    default:
        $query .= " ORDER BY c.created_at DESC";
        break;
}

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($campaigns)) {
        echo '<div class="col-12"><div class="alert alert-info text-center py-4">
              No campaigns found. Try adjusting your filters.</div></div>';
    } else {
        foreach ($campaigns as $campaign) {

            $progress = $campaign['target_amount'] > 0
                ? ($campaign['current_amount'] / $campaign['target_amount']) * 100
                : 0;

            $days_left = ceil((strtotime($campaign['deadline']) - time()) / 86400);

            $image_key = strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $campaign['category']));
            // Browser path for <img src>
            $browser_path = "../../assets/images/campaigns/$image_key.jpg";
            // Absolute filesystem path for file_exists
            $absolute_path = __DIR__ . "/../../assets/images/campaigns/$image_key.jpg";
            // If image does not exist, fallback
            if (!file_exists($absolute_path)) {
                $browser_path = "../../assets/images/campaigns/default.jpg";
            }
?>
    <div class="col p-2">
        <div class="card shadow-sm">
            <img src="<?php echo $browser_path; ?>" class="card-img-top" style="height:180px; object-fit:cover;">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($campaign['title']); ?></h5>
                <p class="card-text text-muted"><?php echo htmlspecialchars($campaign['description']); ?></p>

                <div class="progress mb-2">
                    <div class="progress-bar" style="width: <?php echo min($progress, 100); ?>%;"></div>
                </div>

                <a href="campaign_detail.php?id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-primary w-100">
                    View Details
                </a>
            </div>
        </div>
    </div>
<?php
        }
    }

} catch (PDOException $e) {
    error_log("Campaigns error: " . $e->getMessage());
    echo '<div class="col-12"><div class="alert alert-danger">Error loading campaigns.</div></div>';
}
?>
</div>

<?php include '../../includes/footer.php'; ?>
