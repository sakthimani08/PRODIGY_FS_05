<?php
include "config.php";
if (!isset($_SESSION['user_id']) || !isset($_POST['followed_id'])) {
    header("Location: login.php");
    exit;
}

$follower_id = $_SESSION['user_id'];
$followed_id = intval($_POST['followed_id']);

// Fetch the actor’s username (the one who is following/unfollowing)
$actorStmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$actorStmt->bind_param("i", $follower_id);
$actorStmt->execute();
$actorName = $actorStmt->get_result()->fetch_assoc()['username'];
$actorStmt->close();

// Check if already following
$checkStmt = $conn->prepare("SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?");
$checkStmt->bind_param("ii", $follower_id, $followed_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Unfollow
    $delStmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
    $delStmt->bind_param("ii", $follower_id, $followed_id);
    $delStmt->execute();
    $delStmt->close();
} else {
    // Follow
    $insStmt = $conn->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
    $insStmt->bind_param("ii", $follower_id, $followed_id);
    $insStmt->execute();
    $insStmt->close();

    // Create notification for the followed user
    if ($followed_id != $follower_id) {
        $message = $conn->real_escape_string("$actorName started following you.");
        $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notifStmt->bind_param("is", $followed_id, $message);
        $notifStmt->execute();
        $notifStmt->close();
    }
}

$checkStmt->close();
header("Location: profile.php?user_id=$followed_id");
exit;
?>