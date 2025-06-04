<?php
include "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST["post_id"]);

// Fetch the actor’s username (the one who is liking/commenting)
$actorStmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$actorStmt->bind_param("i", $user_id);
$actorStmt->execute();
$actorName = $actorStmt->get_result()->fetch_assoc()['username'];
$actorStmt->close();

// Find the post owner
$postOwnerStmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
$postOwnerStmt->bind_param("i", $post_id);
$postOwnerStmt->execute();
$postOwner = $postOwnerStmt->get_result()->fetch_assoc()['user_id'];
$postOwnerStmt->close();

// 1) Handle LIKE
if (isset($_POST["like"])) {
    // Prevent duplicate likes
    $exists = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
    $exists->bind_param("ii", $user_id, $post_id);
    $exists->execute();
    $exists->store_result();
    if ($exists->num_rows === 0) {
        // Insert like
        $insertLike = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $insertLike->bind_param("ii", $user_id, $post_id);
        $insertLike->execute();
        $insertLike->close();

        // Create notification for post owner
        if ($postOwner != $user_id) {
            $message = $conn->real_escape_string("$actorName liked your post.");
            $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $notifStmt->bind_param("is", $postOwner, $message);
            $notifStmt->execute();
            $notifStmt->close();
        }
    }
    $exists->close();
}

// 2) Handle COMMENT
if (isset($_POST["commentBtn"])) {
    $commentText = trim($_POST["comment"]);
    if ($commentText !== "") {
        // Insert comment
        $escapedComment = $conn->real_escape_string($commentText);
        $insertComment = $conn->prepare("INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)");
        $insertComment->bind_param("iis", $user_id, $post_id, $escapedComment);
        $insertComment->execute();
        $insertComment->close();

        // Create notification for post owner
        if ($postOwner != $user_id) {
            $commentPreview = strlen($commentText) > 50
                            ? substr($commentText, 0, 50) . "…"
                            : $commentText;
            $message = $conn->real_escape_string("$actorName commented: “$commentPreview”");
            $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $notifStmt->bind_param("is", $postOwner, $message);
            $notifStmt->execute();
            $notifStmt->close();
        }
    }
}

header("Location: index.php");
exit;
?>