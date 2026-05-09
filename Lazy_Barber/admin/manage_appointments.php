<?php
// admin/manage_appointments.php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'barber') {
    header("Location: ../login.php");
    exit;
}

$success = '';

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['action']; // 'confirmed', 'completed', or 'cancelled'
    
    $stmt = $pdo->prepare("UPDATE APPOINTMENTS SET status = ? WHERE appointmentid = ?");
    if ($stmt->execute([$new_status, $appointment_id])) {
        // Send notification
        $nStmt = $pdo->prepare("SELECT customerid FROM APPOINTMENTS WHERE appointmentid = ?");
        $nStmt->execute([$appointment_id]);
        $customer = $nStmt->fetch();
        
        if ($customer) {
            $type = 'update';
            if ($new_status == 'confirmed') $type = 'booking_confirmation';
            if ($new_status == 'cancelled') $type = 'cancellation';
            
            $pdo->prepare("INSERT INTO NOTIFICATIONS (customerid, barberid, appointmentid, type) VALUES (?, ?, ?, ?)")
                ->execute([$customer['customerid'], $_SESSION['userid'], $appointment_id, $type]);
        }
        $success = "Appointment marked as " . ucfirst($new_status);
    }
}

// Fetch all appointments
$stmt = $pdo->query("
    SELECT a.*, u.name as customer_name, u.phone as customer_phone, s.name as service_name 
    FROM APPOINTMENTS a
    JOIN USERS u ON a.customerid = u.userid
    JOIN SERVICES s ON a.serviceid = s.serviceid
    ORDER BY a.appointment_date DESC, a.time_slot DESC
");
$appointments = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manage Appointments</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill">Back to Dashboard</a>
    </div>

    <?php if($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($appointments as $app): ?>
                        <tr id="app-<?php echo $app['appointmentid']; ?>">
                            <td>#<?php echo $app['appointmentid']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($app['customer_name']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($app['customer_phone']); ?></small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($app['service_name']); ?><br>
                                <small class="text-muted">$<?php echo htmlspecialchars($app['total_price']); ?></small>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($app['appointment_date'])); ?><br>
                                <?php echo date('h:i A', strtotime($app['time_slot'])); ?>
                            </td>
                            <td>
                                <?php 
                                $badge = 'bg-secondary';
                                if ($app['status'] == 'confirmed') $badge = 'bg-success';
                                if ($app['status'] == 'pending') $badge = 'bg-warning text-dark';
                                if ($app['status'] == 'cancelled') $badge = 'bg-danger';
                                if ($app['status'] == 'completed') $badge = 'bg-info text-dark';
                                ?>
                                <span class="badge <?php echo $badge; ?> rounded-pill text-uppercase">
                                    <?php echo htmlspecialchars($app['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($app['status'] == 'pending'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="appointment_id" value="<?php echo $app['appointmentid']; ?>">
                                        <button type="submit" name="action" value="confirmed" class="btn btn-sm btn-success rounded-pill px-3">Confirm</button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if($app['status'] == 'confirmed'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="appointment_id" value="<?php echo $app['appointmentid']; ?>">
                                        <button type="submit" name="action" value="completed" class="btn btn-sm btn-info rounded-pill px-3 text-white">Mark Done</button>
                                    </form>
                                <?php endif; ?>

                                <?php if($app['status'] == 'pending' || $app['status'] == 'confirmed'): ?>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                        <input type="hidden" name="appointment_id" value="<?php echo $app['appointmentid']; ?>">
                                        <button type="submit" name="action" value="cancelled" class="btn btn-sm btn-outline-danger rounded-pill px-3">Cancel</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Simple highlight script if coming from calendar
$highlight = $_GET['highlight'] ?? '';
if ($highlight) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var row = document.getElementById('app-$highlight');
            if(row) {
                row.classList.add('table-warning');
                row.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        });
    </script>";
}
?>

<?php require_once '../includes/footer.php'; ?>
