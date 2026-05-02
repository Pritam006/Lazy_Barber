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
        <h2 class="fw-bold">Our Top Services</h2>
        <p class="text-muted">Quality cuts and shaves tailored to your style.</p>
    </div>
    
    <div class="row g-4">
        <?php foreach($services as $service): ?>
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="card-title fw-bold"><?php echo htmlspecialchars($service['name']); ?></h4>
                    <p class="card-text text-muted my-3"><?php echo htmlspecialchars($service['description']); ?></p>
                    <h5 class="fw-bold">$<?php echo htmlspecialchars($service['price_aud']); ?> <small class="text-muted fs-6">/ <?php echo htmlspecialchars($service['duration_minutes']); ?> mins</small></h5>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="book.php?service=<?php echo $service['serviceid']; ?>" class="btn btn-outline-primary rounded-pill w-100">Select</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-5">
        <a href="services.php" class="text-decoration-none text-dark fw-bold border-bottom border-dark pb-1">View All Services &rarr;</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
