<?php
// services.php
require_once 'includes/db.php';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM SERVICES WHERE is_active = 1");
$services = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Our Services</h1>
        <p class="text-muted">Premium grooming services tailored for you.</p>
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
                    <a href="book.php?service=<?php echo $service['serviceid']; ?>" class="btn btn-outline-primary rounded-pill w-100">Book Now</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
