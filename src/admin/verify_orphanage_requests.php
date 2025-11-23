<?php
require_once '../../includes/functions.php';
require_once '../../includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
checkAuth('admin');

// Handle approval/rejection
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    // Approve registration: move to orphanages table
    $stmt = $db->prepare('SELECT * FROM orphanage_registrations WHERE registration_id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $reg = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($reg) {
        $stmt2 = $db->prepare('INSERT INTO orphanages (user_id, name, location, description, status) VALUES (:user_id, :name, :location, :description, "verified")');
        $stmt2->bindParam(':user_id', $reg['user_id']);
        $stmt2->bindParam(':name', $reg['name']);
        $stmt2->bindParam(':location', $reg['location']);
        $stmt2->bindParam(':description', $reg['description']);
        $stmt2->execute();
        $db->prepare('UPDATE orphanage_registrations SET status = "approved" WHERE registration_id = :id')->execute([':id' => $id]);
    }
}
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $db->prepare('UPDATE orphanage_registrations SET status = "rejected" WHERE registration_id = :id')->execute([':id' => $id]);
}
if (isset($_GET['approve_change'])) {
    $cid = intval($_GET['approve_change']);
    $stmt = $db->prepare('SELECT * FROM pending_orphanage_changes WHERE change_id = :cid');
    $stmt->bindParam(':cid', $cid);
    $stmt->execute();
    $chg = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($chg) {
        $stmt2 = $db->prepare('UPDATE orphanages SET name = :name, location = :location, description = :description WHERE orphanage_id = :oid');
        $stmt2->bindParam(':name', $chg['name']);
        $stmt2->bindParam(':location', $chg['location']);
        $stmt2->bindParam(':description', $chg['description']);
        $stmt2->bindParam(':oid', $chg['orphanage_id']);
        $stmt2->execute();
        $db->prepare('UPDATE pending_orphanage_changes SET status = "approved" WHERE change_id = :cid')->execute([':cid' => $cid]);
    }
}
if (isset($_GET['reject_change'])) {
    $cid = intval($_GET['reject_change']);
    $db->prepare('UPDATE pending_orphanage_changes SET status = "rejected" WHERE change_id = :cid')->execute([':cid' => $cid]);
}

// Fetch pending registrations and changes
$regs = $db->query('SELECT * FROM orphanage_registrations WHERE status = "pending"')->fetchAll(PDO::FETCH_ASSOC);
$changes = $db->query('SELECT * FROM pending_orphanage_changes WHERE status = "pending"')->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>
<div class="container mt-5">
    <h2>Orphanage Registration & Update Requests</h2>
    <h4>New Registrations</h4>
    <table class="table table-bordered">
        <thead><tr><th>Name</th><th>Location</th><th>Description</th><th>User</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($regs as $r): ?>
            <tr>
                <td><?php echo e($r['name']); ?></td>
                <td><?php echo e($r['location']); ?></td>
                <td><?php echo e($r['description']); ?></td>
                <td><?php echo $r['user_id']; ?></td>
                <td>
                    <a href="?approve=<?php echo $r['registration_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                    <a href="?reject=<?php echo $r['registration_id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h4>Pending Updates</h4>
    <table class="table table-bordered">
        <thead><tr><th>Name</th><th>Location</th><th>Description</th><th>User</th><th>Orphanage</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($changes as $c): ?>
            <tr>
                <td><?php echo e($c['name']); ?></td>
                <td><?php echo e($c['location']); ?></td>
                <td><?php echo e($c['description']); ?></td>
                <td><?php echo $c['user_id']; ?></td>
                <td><?php echo $c['orphanage_id']; ?></td>
                <td>
                    <a href="?approve_change=<?php echo $c['change_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                    <a href="?reject_change=<?php echo $c['change_id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../../includes/footer.php'; ?>
