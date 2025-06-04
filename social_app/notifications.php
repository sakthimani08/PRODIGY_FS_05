<?php
include "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all notifications (most recent first)
$notifStmt = $conn->prepare("
    SELECT id, message, seen, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$notifStmt->bind_param("i", $user_id);
$notifStmt->execute();
$notifications = $notifStmt->get_result();
$notifStmt->close();

// Mark all as seen
$markStmt = $conn->prepare("UPDATE notifications SET seen = 1 WHERE user_id = ?");
$markStmt->bind_param("i", $user_id);
$markStmt->execute();
$markStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Notifications | SocialApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background-color: #f4f6f9; font-family: Arial, sans-serif; }
    .notif-card {
      background-color: #fff;
      border-radius: 6px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
      margin-bottom: 15px;
      padding: 12px 15px;
    }
    .unread {
      background-color: #e3f2fd;
      border-left: 4px solid #0d6efd;
    }
  </style>
</head>
<body class="container py-4">
  <?php include "navbar.php"; ?>

  <h4 class="mb-4">Notifications</h4>

  <?php if ($notifications->num_rows === 0): ?>
    <div class="alert alert-info">You have no notifications.</div>
  <?php else: ?>
    <?php while ($row = $notifications->fetch_assoc()): ?>
      <div class="notif-card <?= $row['seen'] ? '' : 'unread' ?>">
        <p><?= htmlspecialchars($row['message']) ?></p>
        <small class="text-muted"><?= date("d M Y, H:i", strtotime($row['created_at'])) ?></small>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</body>
</html>