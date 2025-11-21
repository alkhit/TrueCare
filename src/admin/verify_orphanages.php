<?php
session_start();
checkAuth('admin');

include '../../includes/config.php';
$page_title = "Verify Orphanages - TrueCare Admin";
include '../../includes/header.php';

// Fetch pending orphanages
try {
    $pendingOrphanages = $db->prepare("
        SELECT o.*, u.email, u.phone, u.created_at as user_created
        FROM orphanages o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.status = 'pending'
        ORDER BY o.created_at DESC
    ");
    $pendingOrphanages->execute();
    $orphanages = $pendingOrphanages->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $orphanages = [];
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <?php include '../auth/sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-clipboard-check me-2"></i>Verify Orphanages
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-warning fs-6"><?php echo count($orphanages); ?> Pending</span>
                </div>
            </div>

            <!-- Pending Orphanages -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Verification</h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary active">All</button>
                        <button class="btn btn-sm btn-outline-secondary">New Today</button>
                        <button class="btn btn-sm btn-outline-secondary">This Week</button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($orphanages)): ?>
                    <div class="row">
                        <?php foreach ($orphanages as $orphanage): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 border-left-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title"><?php echo htmlspecialchars($orphanage['name']); ?></h5>
                                        <span class="badge bg-warning">Pending</span>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Contact Email</small>
                                            <p class="mb-1"><?php echo htmlspecialchars($orphanage['email']); ?></p>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Phone</small>
                                            <p class="mb-1"><?php echo htmlspecialchars($orphanage['phone'] ?? 'Not provided'); ?></p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Location</small>
                                            <p class="mb-1"><?php echo htmlspecialchars($orphanage['location'] ?? 'Not specified'); ?></p>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Registered</small>
                                            <p class="mb-1"><?php echo date('M j, Y', strtotime($orphanage['user_created'])); ?></p>
                                        </div>
                                    </div>

                                    <?php if (!empty($orphanage['description'])): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Description</small>
                                        <p class="mb-0 small"><?php echo htmlspecialchars($orphanage['description']); ?></p>
                                    </div>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            ID: <?php echo $orphanage['orphanage_id']; ?>
                                        </small>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-success verify-btn" 
                                                    data-id="<?php echo $orphanage['orphanage_id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($orphanage['name']); ?>">
                                                <i class="fas fa-check me-1"></i>Approve
                                            </button>
                                            <button class="btn btn-sm btn-danger reject-btn" 
                                                    data-id="<?php echo $orphanage['orphanage_id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($orphanage['name']); ?>">
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary view-btn"
                                                    data-id="<?php echo $orphanage['orphanage_id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-check fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Pending Verifications</h4>
                        <p class="text-muted">All orphanages have been verified. Great job!</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recently Verified -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recently Verified</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Orphanage Name</th>
                                    <th>Location</th>
                                    <th>Verified Date</th>
                                    <th>Status</th>
                                    <th>Verified By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Hope Children Center</td>
                                    <td>Nairobi, Kenya</td>
                                    <td><?php echo date('M j, Y', strtotime('-1 day')); ?></td>
                                    <td><span class="badge bg-success">Verified</span></td>
                                    <td>System Admin</td>
                                </tr>
                                <tr>
                                    <td>Grace Orphanage</td>
                                    <td>Mombasa, Kenya</td>
                                    <td><?php echo date('M j, Y', strtotime('-3 days')); ?></td>
                                    <td><span class="badge bg-success">Verified</span></td>
                                    <td>System Admin</td>
                                </tr>
                                <tr>
                                    <td>Sunshine Home</td>
                                    <td>Kisumu, Kenya</td>
                                    <td><?php echo date('M j, Y', strtotime('-5 days')); ?></td>
                                    <td><span class="badge bg-success">Verified</span></td>
                                    <td>System Admin</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Verify Orphanage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="modalMessage">Are you sure you want to verify this orphanage?</p>
                <div class="mb-3">
                    <label for="verificationNotes" class="form-label">Verification Notes (Optional)</label>
                    <textarea class="form-control" id="verificationNotes" rows="3" placeholder="Add any notes about the verification..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmVerify">Verify Orphanage</button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Orphanage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="rejectionMessage">Are you sure you want to reject this orphanage?</p>
                <div class="mb-3">
                    <label for="rejectionReason" class="form-label">Reason for Rejection *</label>
                    <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmReject">Reject Orphanage</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentOrphanageId = null;
    let currentOrphanageName = null;
    let isVerifying = true;

    const verificationModal = new bootstrap.Modal(document.getElementById('verificationModal'));
    const rejectionModal = new bootstrap.Modal(document.getElementById('rejectionModal'));

    // Verify button handler
    document.querySelectorAll('.verify-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentOrphanageId = this.dataset.id;
            currentOrphanageName = this.dataset.name;
            isVerifying = true;
            
            document.getElementById('modalTitle').textContent = 'Verify ' + currentOrphanageName;
            document.getElementById('modalMessage').textContent = 'Are you sure you want to verify ' + currentOrphanageName + '?';
            verificationModal.show();
        });
    });

    // Reject button handler
    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentOrphanageId = this.dataset.id;
            currentOrphanageName = this.dataset.name;
            isVerifying = false;
            
            document.getElementById('rejectionMessage').textContent = 'Are you sure you want to reject ' + currentOrphanageName + '?';
            rejectionModal.show();
        });
    });

    // Confirm verification
    document.getElementById('confirmVerify').addEventListener('click', function() {
        const notes = document.getElementById('verificationNotes').value;
        
        // Simulate API call
        fetch('verify_orphanage_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                orphanage_id: currentOrphanageId,
                action: 'verify',
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.trueCareApp.showToast('Orphanage verified successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                window.trueCareApp.showToast(data.message || 'Verification failed', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.trueCareApp.showToast('Network error. Please try again.', 'error');
        });
        
        verificationModal.hide();
    });

    // Confirm rejection
    document.getElementById('confirmReject').addEventListener('click', function() {
        const reason = document.getElementById('rejectionReason').value;
        
        if (!reason.trim()) {
            alert('Please provide a reason for rejection.');
            return;
        }

        // Simulate API call
        fetch('verify_orphanage_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                orphanage_id: currentOrphanageId,
                action: 'reject',
                reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.trueCareApp.showToast('Orphanage rejected successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                window.trueCareApp.showToast(data.message || 'Rejection failed', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.trueCareApp.showToast('Network error. Please try again.', 'error');
        });
        
        rejectionModal.hide();
    });
});
</script>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}
.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}
</style>

<?php include '../../includes/footer.php'; ?>