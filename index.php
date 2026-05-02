
<?php
session_start();// Start the session to access session variables
$conn = new mysqli("localhost", "root", "", "ecommerce_projecct");// Connect to the database
if ($conn->connect_error) {
    die("Connection failed: " .
  $conn->connect_error);
}
$msg="";
$class="";
if ($_SERVER["REQUEST_METHOD"] == "POST") {//check if the form is submitted using POST method
    $user = $_POST['email'];
    $pass = $_POST['password'];
    if(
      
      strlen($user)>=3 &&
      strlen($pass)>=6 

    ){
        $sql = "SELECT * FROM customer WHERE email='$user' ";// Prepare SQL query to check if the user exists in the database
        $result = $conn->query($sql);// Execute the query
        if ($result->num_rows > 0) {// Check if a matching user is found
            $_SESSION["email"] = $user;// Store the user's email in the session to keep them logged in
            $_SESSION["password"] = $pass;// Store the user's password in the session (Note: In a real application, you should never store passwords in plain text)

            $msg="Login successful!";//save success message in msg variable
            $class="success";
            header("Location: main.html");//redirect to main.html if login is successful
            exit();
        } }else {
        $msg="Invalid username or password.";//save error message in msg variable
        $class="error";
    }
}
?> 

<!--html code-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <div id="login-form">
        <h1>Login</h1>
         <form method="POST">

           <label for="email">Email:</label>
           <input type="email" id="email" name="email" placeholder="enter your email"><br><br>

           <label for="password">Password:</label>
           <input type="password" id="password" name="password" placeholder="enter your password" required><br><br>

           <button type="submit">Login</button>
           <p>Don't have an account? <a href="signup.php">Sign in</a>.</p>
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {?>
        <p class="<?php echo $class; ?>"><?php echo $msg; ?></p>
        <?php } ?>
    </div>
    
</body>
</html>