<?php
// book.php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

$servicesStmt = $pdo->query("SELECT * FROM SERVICES WHERE is_active = 1");
$services = $servicesStmt->fetchAll();

$selected_service = $_GET['service'] ?? '';
$selected_date = $_POST['date'] ?? '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $time_slot = $_POST['time_slot'] ?? '';
    
    if (empty($service_id) || empty($date) || empty($time_slot)) {
        $error = "Please fill in all fields.";
    } else {
        // Fetch service details
        $sStmt = $pdo->prepare("SELECT * FROM SERVICES WHERE serviceid = ?");
        $sStmt->execute([$service_id]);
        $serviceInfo = $sStmt->fetch();
        
        // Basic check for double booking
        $checkStmt = $pdo->prepare("SELECT * FROM APPOINTMENTS WHERE appointment_date = ? AND time_slot = ? AND status != 'cancelled'");
        $checkStmt->execute([$date, $time_slot]);
        if ($checkStmt->fetch()) {
            $error = "This time slot is already booked. Please choose another one.";
        } else {
            // Default barberid to 1 (Admin Barber) for this prototype
            $barber_id = 1;
            
            $insertStmt = $pdo->prepare("INSERT INTO APPOINTMENTS (customerid, barberid, serviceid, appointment_date, time_slot, total_price, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            if ($insertStmt->execute([$_SESSION['userid'], $barber_id, $service_id, $date, $time_slot, $serviceInfo['price_aud']])) {
                $appointment_id = $pdo->lastInsertId();
                // Add Notification
                $notifStmt = $pdo->prepare("INSERT INTO NOTIFICATIONS (customerid, barberid, appointmentid, type) VALUES (?, ?, ?, 'booking_confirmation')");
                $notifStmt->execute([$_SESSION['userid'], $barber_id, $appointment_id]);
                
                $success = "Appointment booked successfully!";
            } else {
                $error = "Failed to book appointment.";
            }
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4 border-0">
                <h2 class="fw-bold mb-4">Book an Appointment</h2>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?> <br> <a href="my_bookings.php" class="alert-link">View My Bookings</a></div>
                <?php endif; ?>

                <?php if(!$success): ?>
                <form method="POST" action="book.php">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Service</label>
                        <select name="service_id" class="form-select" required>
                            <option value="">-- Choose a Service --</option>
                            <?php foreach($services as $s): ?>
                                <option value="<?php echo $s['serviceid']; ?>" <?php echo ($selected_service == $s['serviceid']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['name']); ?> - $<?php echo htmlspecialchars($s['price_aud']); ?> (<?php echo htmlspecialchars($s['duration_minutes']); ?> mins)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Time</label>
                            <!-- Hardcoded time slots for prototype, ideally fetched dynamically based on AVAILABILITY -->
                            <select name="time_slot" class="form-select" required>
                                <option value="">-- Choose Time --</option>
                                <option value="09:00:00">09:00 AM</option>
                                <option value="10:00:00">10:00 AM</option>
                                <option value="11:00:00">11:00 AM</option>
                                <option value="13:00:00">01:00 PM</option>
                                <option value="14:00:00">02:00 PM</option>
                                <option value="15:00:00">03:00 PM</option>
                                <option value="16:00:00">04:00 PM</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 mt-3 rounded-pill">Confirm Booking</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
