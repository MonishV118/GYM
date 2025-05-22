<?php
require 'db.php';

$errors = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add member
    if (isset($_POST['add'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $join_date = trim($_POST['join_date']);

        if (!$name || !$email || !$join_date) {
            $errors[] = "Name, Email, and Join Date are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO members (name, email, phone, join_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $join_date);
            if (!$stmt->execute()) {
                $errors[] = "Error adding member: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Update member
    if (isset($_POST['update'])) {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $join_date = trim($_POST['join_date']);

        if (!$name || !$email || !$join_date) {
            $errors[] = "Name, Email, and Join Date are required.";
        } else {
            $stmt = $conn->prepare("UPDATE members SET name=?, email=?, phone=?, join_date=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $join_date, $id);
            if (!$stmt->execute()) {
                $errors[] = "Error updating member: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Delete member
    if (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM members WHERE id=?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            $errors[] = "Error deleting member: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all members
$result = $conn->query("SELECT * FROM members ORDER BY id DESC");
$members = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
    $result->free();
}

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Members - Gym Management</title>
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
        <li class="nav-item"><a href="members.php" class="nav-link active">Members</a></li>
        <li class="nav-item"><a href="trainers.php" class="nav-link">Trainers</a></li>
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
  <h2>Members</h2>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Add Member Form -->
  <div class="card mb-4">
    <div class="card-header">Add New Member</div>
    <div class="card-body">
      <form method="POST" class="row g-3">
        <div class="col-md-4">
          <input type="text" name="name" class="form-control" placeholder="Name" required />
        </div>
        <div class="col-md-4">
          <input type="email" name="email" class="form-control" placeholder="Email" required />
        </div>
        <div class="col-md-2">
          <input type="text" name="phone" class="form-control" placeholder="Phone" />
        </div>
        <div class="col-md-2">
          <input type="date" name="join_date" class="form-control" required />
        </div>
        <div class="col-12">
          <button type="submit" name="add" class="btn btn-success">Add Member</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Members Table -->
  <table class="table table-bordered table-striped">
    <thead class="table-primary">
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Join Date</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($members): ?>
        <?php foreach ($members as $member): ?>
          <tr>
            <td><?php echo $member['id']; ?></td>
            <td><?php echo htmlspecialchars($member['name']); ?></td>
            <td><?php echo htmlspecialchars($member['email']); ?></td>
            <td><?php echo htmlspecialchars($member['phone']); ?></td>
            <td><?php echo $member['join_date']; ?></td>
            <td>
              <!-- Edit Button triggers modal -->
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $member['id']; ?>">Edit</button>

              <!-- Delete Form -->
              <form method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this member?');">
                <input type="hidden" name="id" value="<?php echo $member['id']; ?>" />
                <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?php echo $member['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $member['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
              <form method="POST" class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel<?php echo $member['id']; ?>">Edit Member</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" value="<?php echo $member['id']; ?>" />
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($member['name']); ?>" required />
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($member['email']); ?>" required />
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($member['phone']); ?>" />
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Join Date</label>
                    <input type="date" name="join_date" class="form-control" value="<?php echo $member['join_date']; ?>" required />
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
        <tr><td colspan="6" class="text-center">No members found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
