<?php
// admin/calendar.php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'barber') {
    header("Location: ../login.php");
    exit;
}

// Fetch all appointments for the calendar
$stmt = $pdo->query("
    SELECT a.appointmentid, a.appointment_date, a.time_slot, a.status, 
           u.name as customer_name, s.name as service_name, s.duration_minutes
    FROM APPOINTMENTS a
    JOIN USERS u ON a.customerid = u.userid
    JOIN SERVICES s ON a.serviceid = s.serviceid
    WHERE a.status != 'cancelled'
");
$appointments = $stmt->fetchAll();

// Prepare events array for FullCalendar
$events = [];
foreach ($appointments as $app) {
    // Combine date and time
    $start_datetime = $app['appointment_date'] . 'T' . $app['time_slot'];
    
    // Calculate end time by adding duration
    $start_time = new DateTime($start_datetime);
    $end_time = clone $start_time;
    $end_time->add(new DateInterval('PT' . $app['duration_minutes'] . 'M'));
    
    // Determine color based on status
    $color = '#ffc107'; // Warning (yellow) for pending
    if ($app['status'] == 'confirmed') $color = '#198754'; // Success (green)
    if ($app['status'] == 'completed') $color = '#0dcaf0'; // Info (cyan)
    
    $events[] = [
        'id' => $app['appointmentid'],
        'title' => $app['customer_name'] . ' - ' . $app['service_name'],
        'start' => $start_time->format('Y-m-d\TH:i:s'),
        'end' => $end_time->format('Y-m-d\TH:i:s'),
        'color' => $color,
        'url' => 'manage_appointments.php?highlight=' . $app['appointmentid']
    ];
}
$events_json = json_encode($events);
?>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Booking Calendar</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill">Back to Dashboard</a>
    </div>
    
    <div class="card shadow-sm border-0 p-4">
        <div id='calendar'></div>
    </div>
</div>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            slotMinTime: '08:00:00',
            slotMaxTime: '19:00:00',
            allDaySlot: false,
            events: <?php echo $events_json; ?>
        });
        calendar.render();
    });
</script>

<?php require_once '../includes/footer.php'; ?>
