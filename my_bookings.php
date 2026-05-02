<?php
// my_bookings.php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['userid'];

// Fetch appointments for this user
$stmt = $pdo->prepare("
    SELECT a.*, s.name as service_name, s.duration_minutes
    FROM APPOINTMENTS a
    JOIN SERVICES s ON a.serviceid = s.serviceid
    WHERE a.customerid = ?
    ORDER BY a.appointment_date DESC, a.time_slot DESC
");
$stmt->execute([$userid]);
$appointments = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">My Bookings</h2>
        <a href="book.php" class="btn btn-primary rounded-pill">New Booking</a>
    </div>

    <?php if(count($appointments) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-sm rounded overflow-hidden">
                <thead class="table-dark">
                    <tr>
                        <th>Date & Time</th>
                        <th>Service</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($appointments as $app): ?>
                    <tr>
                        <td>
                            <strong><?php echo date('M d, Y', strtotime($app['appointment_date'])); ?></strong><br>
                            <span class="text-muted"><?php echo date('h:i A', strtotime($app['time_slot'])); ?> (<?php echo htmlspecialchars($app['duration_minutes']); ?> mins)</span>
                        </td>
                        <td><?php echo htmlspecialchars($app['service_name']); ?></td>
                        <td>$<?php echo htmlspecialchars($app['total_price']); ?></td>
                        <td>
                            <?php 
                            $statusClass = 'bg-secondary';
                            if ($app['status'] == 'confirmed') $statusClass = 'bg-success';
                            if ($app['status'] == 'pending') $statusClass = 'bg-warning text-dark';
                            if ($app['status'] == 'cancelled') $statusClass = 'bg-danger';
                            if ($app['status'] == 'completed') $statusClass = 'bg-info text-dark';
                            ?>
                            <span class="badge <?php echo $statusClass; ?> rounded-pill px-3 py-2 text-uppercase">
                                <?php echo htmlspecialchars($app['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if($app['status'] == 'pending' || $app['status'] == 'confirmed'): ?>
                                <form method="POST" action="cancel_booking.php" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                    <input type="hidden" name="appointment_id" value="<?php echo $app['appointmentid']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <h4>No bookings found.</h4>
            <p>You haven't made any appointments yet.</p>
            <a href="book.php" class="btn btn-primary mt-3 rounded-pill">Book Now</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
