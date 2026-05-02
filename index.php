<?php
session_start();

$conn = new mysqli("localhost", "root", "", "ecommerce_projecct");

if ($conn->connect_error) {
    die("Connection failed: " .
    $conn->connect_error);
}

$msg = "";
$class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user = $_POST['email'];
    $pass = $_POST['password'];

    if (strlen($user) >= 3 && strlen($pass) >= 6) {

        $sql = "SELECT * FROM customer WHERE email='$user'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $_SESSION["email"] = $user;
            header("Location: main.html");
            exit();
        } else {
            $msg = "You dont have an account, please sign up!";
            $class = "error";
        }

    } else {
        $msg = "Please enter valid email and password!";
        $class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<div id="login-form">
    <h1>Login</h1>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" placeholder="enter your email" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" placeholder="enter your password" required><br><br>

        <button type="submit">Login</button>
    </form>

    <?php if (!empty($msg)) { ?>
        <p class="<?php echo $class; ?>"><?php echo $msg; ?></p>
    <?php } ?>

    <p>Don't have an account? <a href="signup.php">Sign up</a>.</p>
</div>

</body>
</html>