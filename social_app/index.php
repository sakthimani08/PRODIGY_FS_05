<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle new post submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $content = trim($_POST["content"]);
    $tags = trim($_POST["tags"]);
    $uploadDir = "uploads/";

    // Ensure uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $mediaPath = null;
    if (isset($_FILES["media"]) && $_FILES["media"]["name"] !== "") {
        $filename = time() . "_" . basename($_FILES["media"]["name"]);
        $targetFile = $uploadDir . $filename;
        move_uploaded_file($_FILES["media"]["tmp_name"], $targetFile);
        $mediaPath = $targetFile;
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, media, tags) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $content, $mediaPath, $tags);
    $stmt->execute();
    $stmt->close();
}

// Fetch feed: you could join followers to show only followed posts; here we show all posts
$feed = $conn->query("
    SELECT posts.*, users.username, users.profile_pic,
           (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) AS comment_count
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Home | SocialApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f4f6f9; font-family: Arial, sans-serif; }
    .post-card {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      margin-bottom: 20px;
      padding: 15px;
    }
    .post-header {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }
    .post-header img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
    }
    .post-actions button {
      border: none;
      background: none;
      cursor: pointer;
      margin-right: 15px;
    }
    .post-actions button:hover {
      color: #0d6efd;
    }
    .create-post-card {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      margin-bottom: 30px;
      padding: 20px;
    }
  </style>
</head>
<body class="container py-4">
  <?php include "navbar.php"; ?>

  <!-- Create New Post -->
  <div class="create-post-card">
    <h5>Create a Post</h5>
    <form method="post" action="index.php" enctype="multipart/form-data">
      <div class="mb-3">
        <textarea name="content" class="form-control" rows="3" placeholder="What's on your mind?" required></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <input type="text" name="tags" class="form-control" placeholder="Tags (comma-separated)">
        </div>
        <div class="col-md-6 mb-3">
          <input type="file" name="media" class="form-control">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Post</button>
    </form>
  </div>

  <!-- Feed -->
  <?php while ($post = $feed->fetch_assoc()): ?>
    <div class="post-card">
      <div class="post-header">
        <?php if ($post['profile_pic']): ?>
          <img src="<?= $post['profile_pic'] ?>" alt="Profile Pic">
        <?php else: ?>
          <img src="https://via.placeholder.com/40" alt="Profile Pic">
        <?php endif; ?>
        <div>
          <strong><?= htmlspecialchars($post['username']) ?></strong><br>
          <small class="text-muted"><?= date("d M Y, H:i", strtotime($post['created_at'])) ?></small>
        </div>
      </div>
      <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
      <?php if ($post['media']): ?>
        <img src="<?= $post['media'] ?>" class="img-fluid rounded mb-2" style="max-height:350px;">
      <?php endif; ?>
      <?php if ($post['tags']): ?>
        <p><small>Tags:
          <?php
            $tagList = explode(",", $post['tags']);
            foreach ($tagList as $t) {
              echo '<span class="badge bg-secondary me-1">'.htmlspecialchars(trim($t)).'</span>';
            }
          ?>
        </small></p>
      <?php endif; ?>

      <div class="post-actions d-flex align-items-center">
        <form method="post" action="like_comment.php" class="me-3">
          <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
          <button name="like" type="submit">
            👍 <?= $post['like_count'] ?>
          </button>
        </form>
        <button class="text-muted">
          💬 <?= $post['comment_count'] ?>
        </button>
      </div>

      <!-- Comment Form (collapsed by default) -->
      <div class="mt-3">
        <form method="post" action="like_comment.php" class="d-flex">
          <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
          <input type="text" name="comment" class="form-control me-2" placeholder="Write a comment..." required>
          <button name="commentBtn" type="submit" class="btn btn-outline-primary">Post</button>
        </form>
      </div>
    </div>
  <?php endwhile; ?>
</body>
</html>