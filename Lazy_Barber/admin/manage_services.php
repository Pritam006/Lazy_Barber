<?php
// admin/manage_services.php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'barber') {
    header("Location: ../login.php");
    exit;
}

$success = '';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $service_id = $_POST['serviceid'] ?? '';
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $duration = $_POST['duration'] ?? 0;
        
        if ($service_id) {
            // Update
            $stmt = $pdo->prepare("UPDATE SERVICES SET name=?, description=?, price_aud=?, duration_minutes=? WHERE serviceid=?");
            $stmt->execute([$name, $description, $price, $duration, $service_id]);
            $success = "Service updated successfully.";
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO SERVICES (name, description, price_aud, duration_minutes) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $duration]);
            $success = "New service added.";
        }
    } elseif ($_POST['action'] === 'toggle') {
        $service_id = $_POST['serviceid'];
        $stmt = $pdo->prepare("UPDATE SERVICES SET is_active = NOT is_active WHERE serviceid = ?");
        $stmt->execute([$service_id]);
        $success = "Service visibility toggled.";
    }
}

// Fetch Services
$stmt = $pdo->query("SELECT * FROM SERVICES ORDER BY name");
$services = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manage Services</h2>
        <div>
            <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="resetForm()">+ Add New Service</button>
            <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill">Back to Dashboard</a>
        </div>
    </div>

    <?php if($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach($services as $service): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 <?php echo $service['is_active'] ? '' : 'opacity-50'; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold"><?php echo htmlspecialchars($service['name']); ?></h5>
                        <span class="badge bg-<?php echo $service['is_active'] ? 'success' : 'secondary'; ?> rounded-pill h-100">
                            <?php echo $service['is_active'] ? 'Active' : 'Hidden'; ?>
                        </span>
                    </div>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($service['description']); ?></p>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>$<?php echo htmlspecialchars($service['price_aud']); ?></span>
                        <span><?php echo htmlspecialchars($service['duration_minutes']); ?> mins</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-dark rounded-pill flex-grow-1" 
                            onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)">Edit</button>
                    <form method="POST" class="flex-grow-1 m-0">
                        <input type="hidden" name="serviceid" value="<?php echo $service['serviceid']; ?>">
                        <button type="submit" name="action" value="toggle" class="btn btn-sm btn-outline-secondary rounded-pill w-100">
                            <?php echo $service['is_active'] ? 'Hide' : 'Show'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold" id="modalTitle">Add Service</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="serviceid" id="serviceid">
            
            <div class="mb-3">
                <label class="form-label fw-semibold">Service Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Price ($)</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Duration (mins)</label>
                    <input type="number" name="duration" id="duration" class="form-control" required>
                </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Save Service</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
function editService(service) {
    document.getElementById('modalTitle').innerText = 'Edit Service';
    document.getElementById('serviceid').value = service.serviceid;
    document.getElementById('name').value = service.name;
    document.getElementById('description').value = service.description;
    document.getElementById('price').value = service.price_aud;
    document.getElementById('duration').value = service.duration_minutes;
    var modal = new bootstrap.Modal(document.getElementById('serviceModal'));
    modal.show();
}

function resetForm() {
    document.getElementById('modalTitle').innerText = 'Add Service';
    document.getElementById('serviceid').value = '';
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
    document.getElementById('price').value = '';
    document.getElementById('duration').value = '';
}
</script>

<?php require_once '../includes/footer.php'; ?>
