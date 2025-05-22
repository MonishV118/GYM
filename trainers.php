<?php
require 'db.php';

$errors = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add trainer
    if (isset($_POST['add'])) {
        $name = trim($_POST['name']);
        $specialty = trim($_POST['specialty']);
        $phone = trim($_POST['phone']);

        if (!$name) {
            $errors[] = "Name is required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO trainers (name, specialty, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $specialty, $phone);
            if (!$stmt->execute()) {
                $errors[] = "Error adding trainer: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Update trainer
    if (isset($_POST['update'])) {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $specialty = trim($_POST['specialty']);
        $phone = trim($_POST['phone']);

        if (!$name) {
            $errors[] = "Name is required.";
        } else {
            $stmt = $conn->prepare("UPDATE trainers SET name=?, specialty=?, phone=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $specialty, $phone, $id);
            if (!$stmt->execute()) {
                $errors[] = "Error updating trainer: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Delete trainer
    if (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM trainers WHERE id=?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            $errors[] = "Error deleting trainer: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all trainers
$result = $conn->query("SELECT * FROM trainers ORDER BY id DESC");
$trainers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $trainers[] = $row;
    }
    $result->free();
}

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Trainers - Gym Management</title>
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
        <li class="nav-item"><a href="trainers.php" class="nav-link active">Trainers</a></li>
        <li class="nav-item"><a href="plans.php" class="nav-link">Plans</a></li>
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
  <h2>Trainers</h2>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Add Trainer Form -->
  <div class="card mb-4">
    <div class="card-header">Add New Trainer</div>
    <div class="card-body">
      <form method="POST" class="row g-3">
        <div class="col-md-4">
          <input type="text" name="name" class="form-control" placeholder="Name" required />
        </div>
        <div class="col-md-4">
          <input type="text" name="specialty" class="form-control" placeholder="Specialty" />
        </div>
        <div class="col-md-4">
          <input type="text" name="phone" class="form-control" placeholder="Phone" />
        </div>
        <div class="col-12">
          <button type="submit" name="add" class="btn btn-success">Add Trainer</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Trainers Table -->
  <table class="table table-bordered table-striped">
    <thead class="table-primary">
      <tr>
        <th>ID</th><th>Name</th><th>Specialty</th><th>Phone</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($trainers): ?>
        <?php foreach ($trainers as $trainer): ?>
          <tr>
            <td><?php echo $trainer['id']; ?></td>
            <td><?php echo htmlspecialchars($trainer['name']); ?></td>
            <td><?php echo htmlspecialchars($trainer['specialty']); ?></td>
            <td><?php echo htmlspecialchars($trainer['phone']); ?></td>
            <td>
              <!-- Edit Button triggers modal -->
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $trainer['id']; ?>">Edit</button>

              <!-- Delete Form -->
              <form method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this trainer?');">
                <input type="hidden" name="id" value="<?php echo $trainer['id']; ?>" />
                <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?php echo $trainer['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $trainer['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
              <form method="POST" class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel<?php echo $trainer['id']; ?>">Edit Trainer</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" value="<?php echo $trainer['id']; ?>" />
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($trainer['name']); ?>" required />
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Specialty</label>
                    <input type="text" name="specialty" class="form-control" value="<?php echo htmlspecialchars($trainer['specialty']); ?>" />
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($trainer['phone']); ?>" />
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
        <tr><td colspan="5" class="text-center">No trainers found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
