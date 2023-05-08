<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // check if email and password match
    $conn = mysqli_connect('localhost', 'root', '', 'refereesystem');
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
        // login successful
        $_SESSION['user_id'] = $user['id'];
        header('Location: DiscussionForum.php');
        exit;
    } else {
        // login failed
        $error_message = 'Invalid username or password';
    }
}

?>

<?php if (isset($error_message)): ?>
    <p><?php echo $error_message; ?></p>
<?php endif; ?>

<head>
<link rel="stylesheet" type="text/css" href="forumlogin.css" />
</head>
<body class="login-background">
    <h1>
        Please Login Again to use forums!
</h1>

</body>
<form method="post">
    <label>Username:</label><br>
    <input type="username" name="username"><br>

    <label>Password:</label><br>
    <input type="password" name="password"><br>

    <input type="submit" value="Login">
</form>
</div>
<footer class="footer">
    &copy; 2023 Premier League Analysis
</footer>