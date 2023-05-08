<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: logins.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Premier League Referee Analysis</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="welcome.css" />
    <!-- <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style> -->

</head>

<body class="login-background">
<div class="title">
        <h1>Premier League Referee Website</h1>
    </div>

    <nav>
        <ul class="nav-links">
        <li><a href="welcome.php">Home</li>
            <li><a href="refereedata.php">Referee Page</a></li>
            <li><a href="FootballMatches.php">Football Results </a></li>
            <li><a href="leagueTable.php">League Table</a></li>
            <li><a href="DiscussionForum.php">Forum Page</a></li>
            <li><a href="resetpassword.php">Reset Your Password</a></li>
            <li><a href="logout.php">Logout</a></li>
            
        </ul>
    </nav>
   
<div id ="About-Section">
<h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to the Number 1 Football Page.</h1>
   

<h1>About Us: </h1>
<h2>Welcome to our football website, your ultimate destination for all things related to the beautiful game. Here, you'll find an array of features that will keep you up to date on the latest developments in the world of football. Whether you're interested in referee data, match stats, league tables, or even engaging in lively debates with other fans, we have everything you need to fuel your passion for the sport. Our platform is designed to provide you with comprehensive information on matches, teams, players, and more.</h2>
<p> We take pride in providing accurate and up-to-date information on the Premier League's 2018/19 season. As our website is still in development, we are focused on delivering comprehensive data for this particular season. Our team is dedicated to ensuring that the information we provide is reliable and useful for fans who are interested in analyzing the performance of their favorite teams and players during this season. So, if you're looking for detailed statistics and insights from the Premier League's 2018/19 season, you've come to the right place. </p>

</div>
<footer class="footer">
    &copy; 2023 Premier League Analysis
</footer>
</body>
</html>