<?php
include "config.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_GET['user_id'];
$viewer_id = $_SESSION['user_id'];

$res = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $res->fetch_assoc();

// Count followers
$followers = $conn->query("SELECT COUNT(*) AS total FROM follows WHERE followed_id = $user_id")->fetch_assoc()['total'];

// Count following
$following = $conn->query("SELECT COUNT(*) AS total FROM follows WHERE follower_id = $user_id")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $user['username'] ?>'s Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
        background-color: #f4f6f9;
        font-family: Arial, sans-serif;
    }
    .profile-header {
        background-color: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        text-align: center;
    }
    .post-card {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.05);
    }
    .follow-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 25px;
        font-weight: bold;
    }
  </style>
</head>
<body class="container py-4">

  <div class="profile-header">
    <h2>@<?= $user['username'] ?></h2>
    <p><?= $user['email'] ?? '' ?></p>
    <p><strong>Followers:</strong> <?= $followers ?> &nbsp; | &nbsp; <strong>Following:</strong> <?= $following ?></p>

    <?php if ($user_id != $viewer_id): ?>
        <?php
        $check = $conn->query("SELECT * FROM follows WHERE follower_id = $viewer_id AND followed_id = $user_id");
        $isFollowing = $check->num_rows > 0;
        ?>
        <form method="post" action="follow.php">
            <input type="hidden" name="followed_id" value="<?= $user_id ?>">
            <button class="follow-btn btn <?= $isFollowing ? 'btn-outline-danger' : 'btn-primary' ?>" type="submit">
                <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
            </button>
        </form>
    <?php endif; ?>
  </div>

  <h4 class="mb-3">Posts</h4>

  <?php
  $posts = $conn->query("SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC");
  while ($post = $posts->fetch_assoc()):
  ?>
    <div class="post-card">
        <p><?= htmlspecialchars($post['content']) ?></p>
        <?php if ($post['media']): ?>
            <img src="<?= $post['media'] ?>" class="img-fluid rounded" style="max-height:300px;">
        <?php endif; ?>
        <div class="text-muted mt-2">
            Tags: <?= htmlspecialchars($post['tags']) ?> <br>
            <small>Posted on <?= date("d M Y, H:i", strtotime($post['created_at'])) ?></small>
        </div>
    </div>
  <?php endwhile; ?>

</body>
</html>