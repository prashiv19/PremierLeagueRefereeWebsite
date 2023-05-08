<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="FootballMatches.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("th").click(function () {
                var table = $(this).parents('table').eq(0);
                var rows = table.find('tr:gt(0)').toArray().sort(compare($(this).index()));
                this.asc = !this.asc;
                if (!this.asc) {
                    rows = rows.reverse();
                }
                for (var i = 0; i < rows.length; i++) {
                    table.append(rows[i]);
                }
            });

            function compare(index) {
                return function (a, b) {
                    var valA = getCellValue(a, index), valB = getCellValue(b, index);
                    return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
                }
            }

            function getCellValue(row, index) {
                return $(row).children('td').eq(index).text();
            }
        });
    </script>
</head>
<body>
<div class="title">
        <h1>Premier League Referee Website</h1>
    </div>
<nav>
        <ul class="nav-links">
        <li><a href="welcome.php" class="title">Home</li>
            <li><a href="refereedata.php">Referee Page</a></li>
            <li><a href="FootballMatches.php">Football Results </a></li>
            <li><a href="leagueTable.php">League Table</a></li>
            <li><a href="DiscussionForum.php">Forum Page</a></li>
            <li><a href="resetpassword.php">Reset Your Password</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="create_forum_form.php">Forums</a></li>
        </ul>
    </nav>


<?php
$matches = file_get_contents("https://api.footystats.org/league-matches?key=example&league_id=1625");
$matches = json_decode($matches, true);
$matches = $matches["data"];

// Define weights for each category
$weights = [
    'Yellow Cards' => 0.2,
    'Red Cards' => 0.3,
    'Offsides' => 0.1,
    'Corners' => 0.1,
    'Fouls' => 0.3
];

// Calculate overall referee match rating for each row
foreach ($matches as &$match) {
    $rating = 0;
    foreach ($weights as $category => $weight) {
        $homeCategoryCount = $match['team_a_' . strtolower(str_replace(' ', '_', $category))];
        $awayCategoryCount = $match['team_b_' . strtolower(str_replace(' ', '_', $category))];
        $categoryRating = (($homeCategoryCount + $awayCategoryCount) / 2) * $weight;
        $rating += $categoryRating;
    }
    $match['Rating'] = round($rating * 100/ array_sum($weights)/10) . '%';
}

// Determine the referee's bias based on the difference between home and away wins percentages
foreach ($matches as &$match) {
    // Calculate the total percentage of wins
    $home_team_yellows =$match['team_a_yellow_cards'];
    $home_team_reds =$match['team_a_red_cards'];
    $home_team_offsides=$match['team_a_offsides'];
    $home_team_corners=$match['team_a_corners'];
    $home_team_fouls =$match['team_a_fouls'];
    $away_team_yellows =$match['team_b_yellow_cards'];
    $away_team_red =$match['team_b_red_cards'];
    $away_team_offsides =$match['team_b_offsides'];
    $away_team_corners =$match['team_b_corners'];
    $away_team_fouls =$match['team_b_fouls'];

// Calculate the difference between the values of wins_home_percentage and wins_away_percentage
$match_difference = ($home_team_yellows+
$home_team_reds+
$away_team_offsides+
$home_team_corners+
$home_team_fouls)-
( $away_team_yellows+
$away_team_red+
$home_team_offsides+
$away_team_corners+
$away_team_fouls
);

// Define a threshold for bias detection
$bias_threshold = 5; 
if ($match_difference > $bias_threshold) {
  $bias = "Home bias";
} elseif ($match_difference < (-1 * $bias_threshold)) {
  $bias = "Away bias";
} else {
  $bias = "Neutral";
}
$match['Bias'] = $bias;
}
?>

<table>
    <thead>
        <tr>
            <th>Game Week</th>
            <th>Home Team (HT)</th>
            <th>Score</th>
            <th>Away Team (AT)</th>
            <th>Referee ID</th>
            <th>HT Yellow Cards</th>
            <th>AT Yellow Cards</th>
            <th>HT Red Cards</th>
            <th>AT Red Cards</th>
            <th>HT Offsides </th>
            <th>AT Offsides</th>
            <th>HT Corners </th>
            <th>AT Corners </th>
            <th>HT Fouls </th>
            <th>AT Fouls </th>
            <th>Referee Match Rating</th>
            <th>Referee Bias</th>


        </tr>
    </thead>
    <tbody>
        <?php foreach ($matches as $match): ?>
            <tr>
                <td><?php echo $match['game_week']; ?></td>
                <td><?php echo $match['home_name']; ?></td>
                <td><?php echo $match['homeGoalCount'] . ' - ' . $match['awayGoalCount']; ?></td>
                <td><?php echo $match['away_name']; ?></td>
                <td><?php echo $match['refereeID']; ?></td>
                <td><?php echo $match['team_a_yellow_cards']; ?></td>
                <td><?php echo $match['team_b_yellow_cards']; ?></td>
                <td><?php echo $match['team_a_red_cards']; ?></td>
                <td><?php echo $match['team_b_red_cards']; ?></td>
                <td><?php echo $match['team_a_offsides']; ?></td>
                <td><?php echo $match['team_b_offsides']; ?></td>
                <td><?php echo $match['team_a_corners']; ?></td>
                <td><?php echo $match['team_b_corners']; ?></td>
                <td><?php echo $match['team_a_fouls']; ?></td>
                <td><?php echo $match['team_b_fouls']; ?></td>
                <td><?php echo $match['Rating']; ?></td>
                <td><?php echo $match['Bias']; ?></td>




            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<footer class="footer">
    &copy; 2023 Premier League Analysis
</footer>
    </body>


