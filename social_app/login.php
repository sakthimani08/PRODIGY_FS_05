<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Social App</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('uploads/8b049340-8bf7-4139-8566-46a651ba4c1b.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 350px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            background: linear-gradient(to right, #fc6076, #ff9a44);
            color: white;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-btn:hover {
            background: linear-gradient(to right, #ff9a44, #fc6076);
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        @media screen and (max-width: 400px) {
            .login-container {
                width: 90%;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>User Login</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <input name="email" type="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input name="password" type="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>
