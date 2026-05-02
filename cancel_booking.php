<?php
// cancel_booking.php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['userid']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$appointment_id = $_POST['appointment_id'] ?? '';
$userid = $_SESSION['userid'];

if ($appointment_id) {
    // Only allow customer to cancel their own booking, and only if pending or confirmed
    $stmt = $pdo->prepare("UPDATE APPOINTMENTS SET status = 'cancelled' WHERE appointmentid = ? AND customerid = ? AND status IN ('pending', 'confirmed')");
    $stmt->execute([$appointment_id, $userid]);
    
    if ($stmt->rowCount() > 0) {
        // Fetch barberid for notification
        $bStmt = $pdo->prepare("SELECT barberid FROM APPOINTMENTS WHERE appointmentid = ?");
        $bStmt->execute([$appointment_id]);
        $barber = $bStmt->fetch();
        
        if ($barber) {
            // Add cancellation notification
            $notifStmt = $pdo->prepare("INSERT INTO NOTIFICATIONS (customerid, barberid, appointmentid, type) VALUES (?, ?, ?, 'cancellation')");
            $notifStmt->execute([$userid, $barber['barberid'], $appointment_id]);
        }
    }
}

header("Location: my_bookings.php");
exit;
?>
