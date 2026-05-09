<?php
// index.php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Fetch a few services to display on the home page
$stmt = $pdo->query("SELECT * FROM SERVICES WHERE is_active = 1 LIMIT 3");
$services = $stmt->fetchAll();
?>

<section class="hero-section">
    <div class="container">
        <h1 class="display-3 fw-bold mb-4">Look Sharp. Feel Great.</h1>
        <p class="lead mb-5">Experience premium grooming at Lazy Barber. No waiting, just easy online booking.</p>
        <a href="book.php" class="btn btn-primary btn-lg px-5 py-3 fs-5 rounded-pill shadow">Book an Appointment Now</a>
    </div>
</section>

<section class="container my-5 py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">What Our Customers Say</h2>
        <p class="text-muted">Don't just take our word for it.</p>
    </div>
    
    <div class="row g-4">
        <!-- Review 1 -->
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 shadow-sm text-center">
                <div class="card-body">
                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=121212&color=fff&rounded=true" alt="Customer" class="mb-3" width="60" height="60">
                    <p class="card-text text-muted mb-4">"Best haircut I've had in years! The booking process is incredibly easy, and the barbers are true professionals."</p>
                    <h5 class="fw-bold mb-0">John Doe</h5>
                    <small class="text-warning">&#9733;&#9733;&#9733;&#9733;&#9733;</small>
                </div>
            </div>
        </div>
        <!-- Review 2 -->
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 shadow-sm text-center">
                <div class="card-body">
                    <img src="https://ui-avatars.com/api/?name=Michael+Smith&background=757575&color=fff&rounded=true" alt="Customer" class="mb-3" width="60" height="60">
                    <p class="card-text text-muted mb-4">"The Lazy Barber saves me so much time. I just pick my slot, walk in, and I'm instantly in the chair. Fantastic service."</p>
                    <h5 class="fw-bold mb-0">Michael Smith</h5>
                    <small class="text-warning">&#9733;&#9733;&#9733;&#9733;&#9733;</small>
                </div>
            </div>
        </div>
        <!-- Review 3 -->
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 shadow-sm text-center">
                <div class="card-body">
                    <img src="https://ui-avatars.com/api/?name=David+Jones&background=bdbdbd&color=121212&rounded=true" alt="Customer" class="mb-3" width="60" height="60">
                    <p class="card-text text-muted mb-4">"Great atmosphere and excellent beard trimming. Highly recommend the combo package!"</p>
                    <h5 class="fw-bold mb-0">David Jones</h5>
                    <small class="text-warning">&#9733;&#9733;&#9733;&#9733;&#9734;</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-5">
        <a href="services.php" class="btn btn-outline-primary rounded-pill px-4">View Our Services</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
