
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - Gym Management</title>
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

<div class="container mt-5">
  <h1>Dashboard</h1>
  <p>Welcome to your Gym Management System dashboard.</p>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
