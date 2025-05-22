<?php
require 'db.php';

$errors = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add plan
    if (isset($_POST['add'])) {
        $plan_name = trim($_POST['plan_name']);
        $duration_months = intval($_POST['duration_months']);
        $price = floatval($_POST['price']);

        if (!$plan_name) {
            $errors[] = "Plan name is required.";
        } elseif ($duration_months <= 0) {
            $errors[] = "Duration must be positive.";
        } elseif ($price < 0) {
            $errors[] = "Price must be non-negative.";
        } else {
            $stmt = $conn->prepare("INSERT INTO plans (plan_name, duration_months, price) VALUES (?, ?, ?)");
            $stmt->bind_param("sid", $plan_name, $duration_months, $price);
            if (!$stmt->execute()) {
                $errors[] = "Error adding plan: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Update plan
    if (isset($_POST['update'])) {
        $id = intval($_POST['id']);
        $plan_name = trim($_POST['plan_name']);
        $duration_months = intval($_POST['duration_months']);
        $price = floatval($_POST['price']);

        if (!$plan_name) {
            $errors[] = "Plan name is required.";
        } elseif ($duration_months <= 0) {
            $errors[] = "Duration must be positive.";
        } elseif ($price < 0) {
            $errors[] = "Price must be non-negative.";
        } else {
            $stmt = $conn->prepare("UPDATE plans SET plan_name=?, duration_months=?, price=? WHERE id=?");
            $stmt->bind_param("sidi", $plan_name, $duration_months, $price, $id);
            if (!$stmt->execute()) {
                $errors[] = "Error updating plan: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Delete plan
    if (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM plans WHERE id=?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            $errors[] = "Error deleting plan: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all plans
$result = $conn->query("SELECT * FROM plans ORDER BY id DESC");
$plans = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }
    $result->free();
}

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Plans - Gym Management</title>
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Gym Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a href="members.php" class="nav-link">Members</a></li>
        <li class="nav-item"><a href="trainers.php" class="nav-link">Trainers</a></li>
        <li class="nav-item"><a href="plans.php" class="nav-link active">Plans</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="navbar-text me-3">Welcome, Guest</span>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h2>Plans</h2>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Add Plan Form -->
  <div class="card mb-4">
    <div class="card-header">Add New Plan</div>
    <div class="card-body">
      <form method="POST" class="row g-3">
        <div class="col-md-4">
          <input type="text" name="plan_name" class="form-control" placeholder="Plan Name" required />
        </div>
        <div class="col-md-4">
          <input type="number" name="duration_months" class="form-control" placeholder="Duration (months)" min="1" required />
        </div>
        <div class="col-md-4">
          <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" min="0" required />
        </div>
        <div class="col-12">
          <button type="submit" name="add" class="btn btn-success">Add Plan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Plans Table -->
  <table class="table table-bordered table-striped">
    <thead class="table-primary">
      <tr>
        <th>ID</th><th>Plan Name</th><th>Duration (months)</th><th>Price</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($plans): ?>
        <?php foreach ($plans as $plan): ?>
          <tr>
            <td><?php echo $plan['id']; ?></td>
            <td><?php echo htmlspecialchars($plan['plan_name']); ?></td>
            <td><?php echo $plan['duration_months']; ?></td>
            <td><?php echo number_format($plan['price'], 2); ?></td>
            <td>
              <!-- Edit Button triggers modal -->
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $plan['id']; ?>">Edit</button>

              <!-- Delete Form -->
              <form method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this plan?');">
                <input type="hidden" name="id" value="<?php echo $plan['id']; ?>" />
                <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?php echo $plan['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $plan['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
              <form method="POST" class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel<?php echo $plan['id']; ?>">Edit Plan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" value="<?php echo $plan['id']; ?>" />
                  <div class="mb-3">
                    <label class="form-label">Plan Name</label>
                    <input type="text" name="plan_name" class="form-control" value="<?php echo htmlspecialchars($plan['plan_name']); ?>" required />
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Duration (months)</label>
                    <input type="number" name="duration_months" class="form-control" value="<?php echo $plan['duration_months']; ?>" min="1" required />
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($plan['price']); ?>" min="0" required />
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                </div>
              </form>
            </div>
          </div>

        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center">No plans found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
