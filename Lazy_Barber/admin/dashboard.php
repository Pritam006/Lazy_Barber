<?php
// admin/dashboard.php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Check if user is logged in and is a barber
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'barber') {
    header("Location: ../login.php");
    exit;
}

// Fetch some quick stats
$today = date('Y-m-d');

// Today's appointments count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM APPOINTMENTS WHERE appointment_date = ? AND status != 'cancelled'");
$stmt->execute([$today]);
$today_count = $stmt->fetchColumn();

// Pending appointments count
$stmt = $pdo->query("SELECT COUNT(*) FROM APPOINTMENTS WHERE status = 'pending'");
$pending_count = $stmt->fetchColumn();

// Total Revenue this month
$month_start = date('Y-m-01');
$stmt = $pdo->prepare("SELECT SUM(total_price) FROM APPOINTMENTS WHERE appointment_date >= ? AND status = 'completed'");
$stmt->execute([$month_start]);
$revenue = $stmt->fetchColumn() ?: 0;

?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Barber Dashboard</h2>
        <div>
            <a href="calendar.php" class="btn btn-outline-primary rounded-pill me-2">View Calendar</a>
            <a href="manage_appointments.php" class="btn btn-primary rounded-pill">Manage Appointments</a>
        </div>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card bg-primary text-white border-0 shadow-sm h-100 p-3">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fw-bold opacity-75">Today's Appointments</h5>
                    <h1 class="display-4 fw-bold mb-0"><?php echo $today_count; ?></h1>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark border-0 shadow-sm h-100 p-3">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fw-bold opacity-75">Pending Requests</h5>
                    <h1 class="display-4 fw-bold mb-0"><?php echo $pending_count; ?></h1>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white border-0 shadow-sm h-100 p-3">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fw-bold opacity-75">Revenue (This Month)</h5>
                    <h1 class="display-4 fw-bold mb-0">$<?php echo number_format($revenue, 2); ?></h1>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold">Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="manage_services.php" class="list-group-item list-group-item-action py-3">
                            <strong>Manage Services</strong><br>
                            <small class="text-muted">Add, edit, or remove services offered.</small>
                        </a>
                        <a href="manage_customers.php" class="list-group-item list-group-item-action py-3">
                            <strong>Customer Directory</strong><br>
                            <small class="text-muted">View registered customers and their history.</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
