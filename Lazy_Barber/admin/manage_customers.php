<?php
// admin/manage_customers.php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'barber') {
    header("Location: ../login.php");
    exit;
}

// Fetch Customers and their appointment counts
$stmt = $pdo->query("
    SELECT u.userid, u.name, u.email, u.phone, u.created_at,
           COUNT(a.appointmentid) as total_appointments,
           MAX(a.appointment_date) as last_appointment
    FROM USERS u
    LEFT JOIN APPOINTMENTS a ON u.userid = a.customerid
    WHERE u.role = 'customer'
    GROUP BY u.userid
    ORDER BY u.name ASC
");
$customers = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Customer Directory</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill">Back to Dashboard</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Contact Info</th>
                            <th>Joined Date</th>
                            <th>Total Bookings</th>
                            <th>Last Visit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($customers as $customer): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle text-center me-3 d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px; font-size:1.2rem;">
                                        <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
                                    </div>
                                    <strong class="mb-0"><?php echo htmlspecialchars($customer['name']); ?></strong>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($customer['email']); ?>" class="text-decoration-none text-dark d-block">
                                    <?php echo htmlspecialchars($customer['email']); ?>
                                </a>
                                <small class="text-muted"><?php echo htmlspecialchars($customer['phone'] ?: 'No phone provided'); ?></small>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                            <td>
                                <span class="badge bg-secondary rounded-pill px-3 py-2"><?php echo $customer['total_appointments']; ?></span>
                            </td>
                            <td>
                                <?php 
                                if ($customer['last_appointment']) {
                                    echo date('M d, Y', strtotime($customer['last_appointment']));
                                } else {
                                    echo '<span class="text-muted">Never</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(count($customers) == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No customers registered yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
