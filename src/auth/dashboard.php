<?php
require_once __DIR__ . '/../../includes/functions.php';
// Start session and include config at the very top
session_start();

// Define the path to config - adjust based on your directory structure
$config_path = __DIR__ . '/../../includes/config.php';
if (!file_exists($config_path)) {
    die('Configuration file not found. Please check the file path.');
}

require_once $config_path;

// Check authentication
checkAuth(); // This will redirect to login if not authenticated

$page_title = "Dashboard - TrueCare";

// Include header
$header_path = __DIR__ . '/../../includes/header.php';
if (!file_exists($header_path)) {
    die('Header file not found.');
}
include $header_path;

$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

// Fetch role-specific data
$dashboard_data = getDashboardData($user_role, $user_id, $db);

function getDashboardData($role, $user_id, $db) {
    switch ($role) {
        case 'donor':
            return getDonorData($user_id, $db);
        case 'orphanage':
            // Check if orphanage exists for this user
            $stmt = $db->prepare('SELECT orphanage_id FROM orphanages WHERE user_id = :user_id');
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$orphanage) {
                // Redirect to orphanage registration page
                header('Location: register_orphanage.php');
                exit;
            }
            return getOrphanageData($user_id, $db);
        case 'admin':
            return getAdminData($db);
        default:
            return [];
    }
}

function getDonorData($user_id, $db) {
    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) as total_donations,
                   COALESCE(SUM(amount), 0) as total_donated,
                   COUNT(DISTINCT campaign_id) as campaigns_supported
            FROM donations 
            WHERE user_id = :user_id AND status = 'completed'
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: ['total_donations' => 0, 'total_donated' => 0, 'campaigns_supported' => 0];
    } catch (Exception $e) {
        error_log("Donor data error: " . $e->getMessage());
        return ['total_donations' => 0, 'total_donated' => 0, 'campaigns_supported' => 0];
    }
}

function getOrphanageData($user_id, $db) {
    try {
        // Campaign stats
        $campaignStmt = $db->prepare("
            SELECT COUNT(*) as total_campaigns,
                   COUNT(CASE WHEN status = 'active' THEN 1 END) as active_campaigns,
                   COALESCE(SUM(target_amount), 0) as total_goal,
                   COALESCE(SUM(current_amount), 0) as total_raised
            FROM campaigns 
            WHERE orphanage_id IN (SELECT orphanage_id FROM orphanages WHERE user_id = :user_id)
        ");
        $campaignStmt->bindParam(':user_id', $user_id);
        $campaignStmt->execute();
        $data = $campaignStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            $data = ['total_campaigns' => 0, 'active_campaigns' => 0, 'total_goal' => 0, 'total_raised' => 0];
        }
        
        // Verification status
        $verificationStmt = $db->prepare("SELECT status FROM orphanages WHERE user_id = :user_id");
        $verificationStmt->bindParam(':user_id', $user_id);
        $verificationStmt->execute();
        $orphanage = $verificationStmt->fetch(PDO::FETCH_ASSOC);
        $data['verification_status'] = $orphanage['status'] ?? 'pending';
        
        return $data;
    } catch (Exception $e) {
        error_log("Orphanage data error: " . $e->getMessage());
        return ['total_campaigns' => 0, 'active_campaigns' => 0, 'total_goal' => 0, 'total_raised' => 0, 'verification_status' => 'pending'];
    }
}

function getAdminData($db) {
    try {
        // Real queries instead of mock data
        $usersStmt = $db->prepare("SELECT COUNT(*) as total_users FROM users");
        $usersStmt->execute();
        $users = $usersStmt->fetch(PDO::FETCH_ASSOC);
        
        $donationsStmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as total_donations FROM donations WHERE status = 'completed'");
        $donationsStmt->execute();
        $donations = $donationsStmt->fetch(PDO::FETCH_ASSOC);
        
        $campaignsStmt = $db->prepare("SELECT COUNT(*) as active_campaigns FROM campaigns WHERE status = 'active'");
        $campaignsStmt->execute();
        $campaigns = $campaignsStmt->fetch(PDO::FETCH_ASSOC);
        
        $verificationsStmt = $db->prepare("SELECT COUNT(*) as pending_verifications FROM orphanages WHERE status = 'pending'");
        $verificationsStmt->execute();
        $verifications = $verificationsStmt->fetch(PDO::FETCH_ASSOC);
        
        $orphanagesStmt = $db->prepare("SELECT COUNT(*) as total_orphanages FROM orphanages WHERE status = 'verified'");
        $orphanagesStmt->execute();
        $orphanages = $orphanagesStmt->fetch(PDO::FETCH_ASSOC);
        
        $donorsStmt = $db->prepare("SELECT COUNT(*) as total_donors FROM users WHERE role = 'donor'");
        $donorsStmt->execute();
        $donors = $donorsStmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total_users' => $users['total_users'] ?? 0,
            'total_donations' => $donations['total_donations'] ?? 0,
            'active_campaigns' => $campaigns['active_campaigns'] ?? 0,
            'pending_verifications' => $verifications['pending_verifications'] ?? 0,
            'total_orphanages' => $orphanages['total_orphanages'] ?? 0,
            'total_donors' => $donors['total_donors'] ?? 0
        ];
    } catch (Exception $e) {
        error_log("Admin data error: " . $e->getMessage());
        return [
            'total_users' => 0,
            'total_donations' => 0,
            'active_campaigns' => 0,
            'pending_verifications' => 0,
            'total_orphanages' => 0,
            'total_donors' => 0
        ];
    }
}

// Helper functions
function getWelcomeMessage($role) {
    $messages = [
        'donor' => 'Thank you for joining TrueCare! Start by exploring campaigns and making your first donation to support orphanages in need.',
        'orphanage' => 'Welcome to TrueCare! You can now create campaigns to receive support for your orphanage. Make sure to complete your profile verification.',
        'admin' => 'Welcome to the Admin Dashboard. You can manage users, verify orphanages, and monitor platform activity from here.'
    ];
    return $messages[$role] ?? 'Welcome to TrueCare!';
}

function getRoleTemplatePath($role) {
    $templates = [
        'donor' => 'donor_content.php',
        'orphanage' => 'orphanage_content.php',
        'admin' => 'admin_content.php'
    ];
    $filename = $templates[$role] ?? 'donor_content.php';
    $template_path = __DIR__ . '/dashboard_parts/' . $filename;
    
    if (!file_exists($template_path)) {
        error_log("Template not found: " . $template_path);
        return null;
    }
    
    return $template_path;
}
?>

<!-- Main content area - removed the container-fluid wrapper since header already handles the main content -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt me-2"></i>
        <?php echo ucfirst($user_role); ?> Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-calendar me-1"></i>
                <?php echo date('M j, Y'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Welcome Alert -->
<div class="alert alert-info">
    <h6><i class="fas fa-info-circle me-2"></i>Welcome to TrueCare!</h6>
    <p class="mb-0">
        <?php echo getWelcomeMessage($user_role); ?>
    </p>
</div>

<!-- Display any session messages -->
<?php
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>' . $_SESSION['success'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>' . $_SESSION['error'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['error']);
}
?>

<!-- Sidebar -->
<?php if (isset($_SESSION['user_id'])): ?>
<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <?php include __DIR__ . '/../auth/sidebar.php'; ?>
</nav>
<?php endif; ?>

<!-- Featured Campaigns Section -->
<section class="featured-campaigns py-5">
    <div class="container">
        <div class="row">
            <?php
            $featuredStmt = $db->query("SELECT campaign_id, title, description, category, target_amount, current_amount, deadline FROM campaigns WHERE status='active' ORDER BY created_at DESC LIMIT 3");
            $featuredCampaigns = $featuredStmt ? $featuredStmt->fetchAll(PDO::FETCH_ASSOC) : [];
            if (count($featuredCampaigns) === 0): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No campaigns available at the moment.</h5>
                </div>
            <?php else:
                foreach ($featuredCampaigns as $campaign): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <?php
                        $category = isset($campaign['category']) && !empty($campaign['category']) ? strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $campaign['category'])) : 'default';
                        $image_path = "assets/images/campaigns/{$category}.jpg";
                        if (!file_exists($image_path)) {
                            $image_path = "assets/images/campaigns/default.jpg";
                        }
                        ?>
                        <img src="<?php echo $image_path; ?>" class="card-img-top" alt="Campaign" style="height: 200px; object-fit: cover;" onerror="this.onerror=null;this.src='assets/images/campaigns/default.jpg';">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-success mb-2"><?php echo ucfirst($campaign['category']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($campaign['title']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($campaign['description']); ?></p>
                            <div class="mb-3">
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo ($campaign['current_amount'] / $campaign['target_amount']) * 100; ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span><?php echo number_format(($campaign['current_amount'] / $campaign['target_amount']) * 100, 1); ?>% funded</span>
                                    <span>Ksh <?php echo number_format($campaign['current_amount']); ?></span>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php
                                        $days_left = ceil((strtotime($campaign['deadline']) - time()) / (60 * 60 * 24));
                                        echo $days_left > 0 ? $days_left . ' days left' : 'Ended';
                                        ?>
                                    </small>
                                    <a href="src/campaigns/campaign_detail.php?id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>
</section>

<?php 
$footer_path = __DIR__ . '/../../includes/footer.php';
if (file_exists($footer_path)) {
    include $footer_path;
} else {
    echo '<!-- Footer not found -->';
}
?>