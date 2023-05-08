<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: forumlogin.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // retrieve form data
    if (isset($_POST['comment_id'])) {
        // reply form submitted
        $comment_id = $_POST['comment_id'];
        $reply = trim($_POST['reply']); // trim the reply to remove whitespaces from the beginning and end
        $user_id = $_SESSION['user_id'];

        if (empty($reply)) {
            // reply is empty, show error message
            echo '<p style="color: red;">Error: reply cannot be empty!</p>';
        } else {
            // insert data into database
            $conn = mysqli_connect('localhost', 'root', '', 'refereesystem');
            $sql = "INSERT INTO replies (comment_id, user_id, reply) VALUES ('$comment_id', '$user_id', '$reply')";
            mysqli_query($conn, $sql);
            mysqli_close($conn);
        }
    } else if (isset($_POST['reply_id'])) {
        // reply vote form submitted
        $reply_id = $_POST['reply_id'];
        $vote_type = $_POST['vote_type'];
        $user_id = $_SESSION['user_id'];

        // check if user has already voted on this reply
        $conn = mysqli_connect('localhost', 'root', '', 'refereesystem');
        $sql = "SELECT * FROM reply_votes WHERE user_id = '$user_id' AND reply_id = '$reply_id'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 0) {
            // user has not yet voted, insert vote into database
            $sql = "INSERT INTO reply_votes (reply_id, user_id, vote_type) VALUES ('$reply_id', '$user_id', '$vote_type')";
            mysqli_query($conn, $sql);
        } else {
            // user has already voted, update vote in database
            $sql = "UPDATE reply_votes SET vote_type = '$vote_type' WHERE user_id = '$user_id' AND reply_id = '$reply_id'";
            mysqli_query($conn, $sql);
        }

        mysqli_close($conn);
    } else {
        // comment form submitted
        $comment = trim($_POST['comment']);// trim the comment to remove whitespaces from the beginning and end
        $user_id = $_SESSION['user_id'];
        // $user_id = $_SESSION['user_id'];

        if (empty($comment)) {
            // comment is empty, show error message
            echo '<p style="color: red;">Error: comment cannot be empty!</p>';
        } else {
            // insert data into database
            $conn = mysqli_connect('localhost', 'root', '', 'refereesystem');
            $sql = "INSERT INTO comments (comment, user_id) VALUES ('$comment', '$user_id')";
            mysqli_query($conn, $sql);
            mysqli_close($conn);
        }
    }
}   

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="DiscussionForum.css">
    <title>Forum</title>
<header>
    <h1>Chat/Forum Page</h1>
    <nav>
        <ul class="nav-links">
        <li><a href="welcome.php" class="title">Home</li>
            <li><a href="refereedata.php">Referee Page</a></li>
            <li><a href="FootballMatches.php">Football Results </a></li>
            <li><a href="leagueTable.php">League Table</a></li>
            <li><a href="DiscussionForum.php">Forum Page</a></li>
            <li><a href="resetpassword.php">Reset Your Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
</head>
<body>

<div style="text-align: center;">
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
</div>


<form method="post" class="comment-form">
    <label>Write a comment:</label><br>
    <textarea name="comment"></textarea><br>

    <input type="submit" value="Submit">
</form>


<?php

// retrieve comments from database
$conn = mysqli_connect('localhost', 'root', '', 'refereesystem');
$sql = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id ORDER BY comments.created_at DESC";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $comment_id = $row['id'];
    $comment = $row['comment'];
    $username = $row['username'];
    $created_at = $row['created_at'];

    // retrieve comment votes from database
    $sql_votes = "SELECT * FROM comment_votes WHERE comment_id = '$comment_id'";
    $result_votes = mysqli_query($conn, $sql_votes);
    $upvotes = 0;
    $downvotes = 0;
    $user_vote = null;

while ($row_votes = mysqli_fetch_assoc($result_votes)) {
    if ($row_votes['vote_type'] == 'upvote') {
        $upvotes++;
    } else {
        $downvotes++;
    }

    if ($row_votes['user_id'] == $_SESSION['user_id']) {
        $user_vote = $row_votes['vote_type'];
    }
}

// output comment and vote buttons
echo '<div>';
echo '<p>' . $comment . '</p>';
echo '<p>Posted by ' . $username . ' on ' . $created_at . '</p>';
echo '<form method="post">';
echo '<input type="hidden" name="comment_id" value="' . $comment_id . '">';
echo '<textarea name="reply"></textarea><br>';
echo '<input type="submit" value="Reply">';
echo '</form>';
echo '<div>';
echo '<form method="post">';
echo '<input type="hidden" name="comment_id" value="' . $comment_id . '">';
echo '<input type="hidden" name="vote_type" value="upvote">';
echo '<button type="submit">Upvote (' . $upvotes . ')</button>';
echo '</form>';
echo '<form method="post">';
echo '<input type="hidden" name="comment_id" value="' . $comment_id . '">';
echo '<input type="hidden" name="vote_type" value="downvote">';
echo '<button type="submit">Downvote (' . $downvotes . ')</button>';
echo '<span style="font-size: 12px; color: grey;">Posted on ' . $created_at . '</span>';
echo '</form>';

echo '</div>';



// retrieve replies from database
$sql_replies = "SELECT replies.*, users.username FROM replies JOIN users ON replies.user_id = users.id WHERE replies.comment_id = '$comment_id' ORDER BY replies.created_at ASC";
$result_replies = mysqli_query($conn, $sql_replies);

while ($row_replies = mysqli_fetch_assoc($result_replies)) {
    $reply_id = $row_replies['id'];
    $reply = $row_replies['reply'];
    $username = $row_replies['username'];
    $created_at = $row_replies['created_at'];

    // retrieve reply votes from database
    $sql_reply_votes = "SELECT * FROM reply_votes WHERE reply_id = '$reply_id'";
    $result_reply_votes = mysqli_query($conn, $sql_reply_votes);

    $upvotes = 0;
    $downvotes = 0;
    $user_vote = null;

    while ($row_reply_votes = mysqli_fetch_assoc($result_reply_votes)) {
        if ($row_reply_votes['vote_type'] == 'upvote') {
            $upvotes++;
        } else {
            $downvotes++;
        }

        if ($row_reply_votes['user_id'] == $_SESSION['user_id']) {
            $user_vote = $row_reply_votes['vote_type'];
        }
    }

    // output reply and vote buttons
    echo '<div style="margin-left: 20px">';
    echo '<p>' . $reply . '</p>';
    echo '<form method="post">';
    echo '<input type="hidden" name="reply_id" value="' . $reply_id . '">';
    echo '<input type="hidden" name="vote_type" value="upvote">';
    echo '<button type="submit">Upvote (' . $upvotes . ')</button>';
    echo '</form>';
    echo '<form method="post">';
    echo '<input type="hidden" name="reply_id" value="' . $reply_id . '">';
    echo '<input type="hidden" name="vote_type" value="downvote">';
    echo '<button type="submit">Downvote (' . $downvotes . ')</button>';
    echo '<span style="font-size: 12px; color: grey;">Posted on ' . $created_at . '</span>';
    echo '</form>';

    echo '</div>';
}

echo '</div>';
}

mysqli_close($conn); //close connection

?>


