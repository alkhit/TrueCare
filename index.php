<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section bg-gradient">
    <div class="container">
        <div class="row align-items-center min-vh-80">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-white mb-4">
                    Transforming Lives Through <span class="text-warning">Transparent Giving</span>
                </h1>
                <p class="lead text-white mb-4">
                    TrueCare connects compassionate donors with verified orphanages in Kenya. 
                    Every donation makes a real difference in a child's life.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?php echo BASE_URL; ?>/src/campaigns/campaigns.php" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-heart me-2"></i>Support a Campaign
                    </a>
                    <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-home me-2"></i>Register Orphanage
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image">
                    <i class="fas fa-hands-helping text-warning" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h2 class="text-primary fw-bold" id="stat-children">0</h2>
                    <p class="text-muted">Children Supported</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h2 class="text-primary fw-bold" id="stat-donations">KES 0</h2>
                    <p class="text-muted">Total Donations</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h2 class="text-primary fw-bold" id="stat-orphanages">0</h2>
                    <p class="text-muted">Verified Orphanages</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h2 class="text-primary fw-bold" id="stat-donors">0</h2>
                    <p class="text-muted">Generous Donors</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Campaigns -->
<section id="featured-campaigns" class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="section-title">Active Campaigns</h2>
                <p class="text-muted">Make a difference today</p>
            </div>
        </div>
        
        <div class="row" id="campaigns-container">
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading campaigns...</span>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="<?php echo BASE_URL; ?>/src/campaigns/campaigns.php" class="btn btn-outline-primary">
                    View All Campaigns <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="mb-3">Ready to Make a Difference?</h3>
                <p class="mb-0">Join our community of donors and help transform children's lives across Kenya.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-light btn-lg">
                    Get Started <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// Load campaigns and statistics
document.addEventListener('DOMContentLoaded', function() {
    loadCampaigns();
    loadStatistics();
});

async function loadCampaigns() {
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/src/campaigns/get_campaigns.php');
        const campaigns = await response.json();
        
        const container = document.getElementById('campaigns-container');
        
        if (campaigns.error) {
            container.innerHTML = `
                <div class="col-12 text-center">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        ${campaigns.error}
                    </div>
                </div>
            `;
            return;
        }
        
        if (campaigns.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No active campaigns at the moment. Check back soon!
                    </div>
                </div>
            `;
            return;
        }
        
        // Show only first 6 campaigns
        const featuredCampaigns = campaigns.slice(0, 6);
        
        container.innerHTML = featuredCampaigns.map(campaign => `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card campaign-card h-100">
                    <div class="card-img-top campaign-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 160px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-${getCategoryIcon(campaign.category)} text-white" style="font-size: 3rem;"></i>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-${getCategoryColor(campaign.category)}">${campaign.category}</span>
                            <small class="text-muted">${formatDate(campaign.deadline)}</small>
                        </div>
                        <h5 class="card-title">${campaign.title}</h5>
                        <p class="card-text flex-grow-1">${campaign.description ? campaign.description.substring(0, 120) + '...' : 'No description available.'}</p>
                        
                        <div class="campaign-progress mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Raised: KES ${formatCurrency(campaign.current_amount)}</small>
                                <small>Goal: KES ${formatCurrency(campaign.target_amount)}</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" 
                                     style="width: ${Math.min((campaign.current_amount / campaign.target_amount) * 100, 100)}%">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-auto">
                            <a href="<?php echo BASE_URL; ?>/src/campaigns/campaign_detail.php?id=${campaign.campaign_id}" 
                               class="btn btn-primary w-100">
                                <i class="fas fa-donate me-2"></i>Support Campaign
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Error loading campaigns:', error);
        document.getElementById('campaigns-container').innerHTML = `
            <div class="col-12 text-center">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading campaigns. Please try again later.
                </div>
            </div>
        `;
    }
}

async function loadStatistics() {
    try {
        // In a real implementation, you would fetch these from an API endpoint
        // For now, we'll simulate loading
        setTimeout(() => {
            document.getElementById('stat-children').textContent = '0';
            document.getElementById('stat-donations').textContent = 'KES 0';
            document.getElementById('stat-orphanages').textContent = '0';
            document.getElementById('stat-donors').textContent = '0';
        }, 1000);
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Utility functions
function getCategoryIcon(category) {
    const icons = {
        'education': 'graduation-cap',
        'medical': 'heartbeat',
        'food': 'utensils',
        'shelter': 'home',
        'clothing': 'tshirt',
        'other': 'hands-helping'
    };
    return icons[category] || 'hands-helping';
}

function getCategoryColor(category) {
    const colors = {
        'education': 'info',
        'medical': 'danger',
        'food': 'warning',
        'shelter': 'success',
        'clothing': 'primary',
        'other': 'secondary'
    };
    return colors[category] || 'secondary';
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-KE').format(amount);
}

function formatDate(dateString) {
    if (!dateString) return 'No deadline';
    return new Date(dateString).toLocaleDateString('en-KE');
}
</script>