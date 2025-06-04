<?php
// Include this at the top of every page after session_start()
// (Assumes config.php has already been included)
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">SocialApp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <?php if (isset($_SESSION['user_id'])): ?>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="profile.php?user_id=<?= $_SESSION['user_id'] ?>">My Profile</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="notifications.php">Notifications
              <?php
                // Count unseen notifications
                $nid = $_SESSION['user_id'];
                $unseen = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = $nid AND seen = 0")
                                ->fetch_assoc()['cnt'];
                if ($unseen > 0) {
                    echo " <span class='badge bg-danger'>$unseen</span>";
                }
              ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="trending.php">Trending</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</nav>