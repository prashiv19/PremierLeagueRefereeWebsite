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
        </ul>
    </nav>


<?php
$referee = file_get_contents("https://api.footystats.org/league-referees?key=example&league_id=1625");
$referee = json_decode($referee, true);
$referee= $referee["data"];

// Remove duplicate entries from $referee array while keeping first occurrence
$uniqueReferees = array_values(array_unique($referee, SORT_REGULAR));

// Define weights for each category
$weights = [
    //PENALTY WEIGHTS
    'Penalties Given Home' => 0.25,
    'Penalties Given Away' => 0.2,
    // 'Penalties Given Overall' => 0.3,
    'Penalties Given Per Match Home' => 0.25,
    'Penalties Given Per Match Away' => 0.1,
    // 'Penalties Given Per Match Overall' => 0.1,
    'Penalties Given Percentage Home' => 0.15,
    'Penalties Given Percentage Away' => 0.05,
    // 'Penalties Given Percentage Overall' => 0.05,


];

// Calculate overall referee rating for each row
foreach ($uniqueReferees as &$ref) {
    $rating = 0;
    foreach ($weights as $category => $weight) {
        $categoryValue = $ref[strtolower(str_replace(' ', '_', $category))];
        $categoryRating = $categoryValue * $weight;
        $rating += $categoryRating;
    }
    $ref['Penalty Rating'] = round($rating * 100/ array_sum($weights)/10) . '%';
}

// Determine the referee's bias based on the difference between home and away penalty percentages
foreach ($uniqueReferees as &$ref) {
    // Calculate the total percentage of penalties
    $penalties_per_home = $ref['penalties_given_percentage_home'];
    $penalties_per_away = $ref['penalties_given_percentage_away'];

// Calculate the difference between the values of home Penalty_percentage and away penalty percentage
$penalty_difference = $penalties_per_home - $penalties_per_away;

// Define a threshold for bias detection
$bias_threshold = 5; 
if ($penalty_difference > $bias_threshold) {
  $bias = "Home bias";
} elseif ($penalty_difference < (-1 * $bias_threshold)) {
  $bias = "Away bias";
} else {
  $bias = "Neutral";
}
$ref['Bias'] = $bias;
}

?>
<!-- Penalty Table -->
<div class="table-title">
        <h3>Referee Penalty Table Stats</h3></div>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Penalties Given (H)</th>
            <th>Penalties Given (A)</th>
            <th>Penalties Given (OVR)</th>
            <th>Penalties Given Per Match (H)</th>
            <th>Penalties Given Per Match (A)</th>
            <th>Penalties Given Per Match (OVR)</th>
            <th>Penalities Given (%) (H)</th>
            <th>Penalities Given (%) (A) </th>
            <th>Penalities Given (%) (OVR)</th>
            <th>Penalty Rating (OVR)</th>
            <th>Penalty Bias</th>


        </tr>
    </thead>
    <tbody>
        <?php foreach ($uniqueReferees as $ref): ?>
            <tr>
                <td><?php echo $ref['id']; ?></td>
                <td><?php echo $ref['full_name']; ?></td>
                <td><?php echo $ref['penalties_given_home']; ?></td>
                <td><?php echo $ref['penalties_given_away']; ?></td>
                <td><?php echo $ref['penalties_given_overall']; ?></td>
                <td><?php echo $ref['penalties_given_per_match_home']; ?></td>
                <td><?php echo $ref['penalties_given_per_match_away']; ?></td>
                <td><?php echo $ref['penalties_given_per_match_overall']; ?></td>
                <td><?php echo $ref['penalties_given_percentage_home']; ?></td>
                <td><?php echo $ref['penalties_given_percentage_away']; ?></td>
                <td><?php echo $ref['penalties_given_percentage_overall']; ?></td>
                <td><?php echo $ref['Penalty Rating']; ?></td>
                <td><?php echo $ref['Bias']; ?></td>
                



            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php

// Remove duplicate entries from $referee array while keeping first occurrence.
$uniqueReferees = array_values(array_unique($referee, SORT_REGULAR));

$weights = [
//CARD WEIGHTS
    "cards_overall" => 0.1,	
    // "cards_home" => 0.1,	
    // "cards_away" => 0.2,	
    "cards_per_match_overall" => 0.2,
    // "cards_per_match_home" => 0.05,	
    // "cards_per_match_away" => 0.1,	
    "yellow_cards_overall" => 0.2,
    "red_cards_overall" => 0.4,	
    "min_per_card_overall" => 0.1
];

// Calculate overall referee rating for each row
foreach ($uniqueReferees as &$ref) {
    $rating = 0;
    foreach ($weights as $category => $weight) {
        $categoryValue = $ref[strtolower(str_replace(' ', '_', $category))];
        $categoryRating = $categoryValue * $weight;
        $rating += $categoryRating;
    }
    $ref['Card Rating'] = round($rating * 100/ array_sum($weights)/100) . '%';
}

// Determine the referee's bias based on the difference between home and away card percentages
foreach ($uniqueReferees as &$ref) {
    // Calculate the total percentage of cards
    $cards_per_home = $ref['cards_home'];
    $cards_per_away = $ref['cards_away'];

// Calculate the difference between the values of home Penalty_percentage and away penalty percentage
$card_difference = $cards_per_home - $cards_per_away;

// Define a threshold for bias detection
$bias_threshold = 5; 
if ($card_difference > $bias_threshold) {
  $bias = "Home bias";
} elseif ($card_difference < (-1 * $bias_threshold)) {
  $bias = "Away bias";
} else {
  $bias = "Neutral";
}
$ref['Bias'] = $bias;
}

?>
<!-- Card table -->
<div class="table-title">
        <h3>Referee Card Table Stats</h3></div>
<table>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Cards Given (H)</th>
            <th>Cards Given (A)</th>
            <th>Cards Given (OVR)</th>
            <th>Cards Given Per Match (H)</th>
            <th>Cards Given Per Match (A)</th>
            <th>Cards Given Per Match (OVR)</th>
            <th>Yellow Cards (OVR)</th>
            <th>Red Cards (OVR)</th>
            <th>Min Per Card (OVR)</th>
            <th>Card Rating (OVR)</th>
            <th>Card Bias</th>


        </tr>
    </thead>
    <tbody>
        <?php foreach ($uniqueReferees as $ref): ?>
            <tr>
                <td><?php echo $ref['id']; ?></td>
                <td><?php echo $ref['full_name']; ?></td>
                <td><?php echo $ref['cards_home']; ?></td>
                <td><?php echo $ref['cards_away']; ?></td>
                <td><?php echo $ref['cards_overall']; ?></td>
                <td><?php echo $ref['cards_per_match_home']; ?></td>
                <td><?php echo $ref['cards_per_match_away']; ?></td>
                <td><?php echo $ref['cards_per_match_overall']; ?></td>
                <td><?php echo $ref['yellow_cards_overall']; ?></td>
                <td><?php echo $ref['red_cards_overall']; ?></td>
                <td><?php echo $ref['min_per_card_overall']; ?></td>
                <td><?php echo $ref['Card Rating']; ?></td>
                <td><?php echo $ref['Bias']; ?></td>



            </tr>
        <?php endforeach; ?>
    </tbody>
</table>



<?php

// Remove duplicate entries from $referee array while keeping first occurrence
$uniqueReferees = array_values(array_unique($referee, SORT_REGULAR));


$weights = [
//Goals WEIGHTS
    // "draws_per_goals_overall" => 0.2,	
    "goals_home" => 0.25,	
    "goals_away" => 0.1,	
    // "goals_overall" => 0.3,
    "goals_per_match_home" => 0.25,	
    "goals_per_match_away" => 0.1,	
    // "goals_per_match_overall" => 0.3,	
    "min_per_goal_overall" => 0.1,
    "btts_overall" => 0.2
    


];

// Calculate overall referee rating for each row
foreach ($uniqueReferees as &$ref) {
    $rating = 0;
    foreach ($weights as $category => $weight) {
        $categoryValue = $ref[strtolower(str_replace(' ', '_', $category))];
        $categoryRating = $categoryValue * $weight;
        $rating += $categoryRating;
    }
    $ref['Goal Rating'] = round($rating * 100/ array_sum($weights)/100) . '%';
}

?>
<!-- Goals Table -->
<div class="table-title">
        <h3>Referee Goals Table Stats</h3></div>
<table>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Goals(H)</th>
            <th>Goals(A)</th>
            <th>Goals (OVR)</th>
            <th>Goals Per Match (H)</th>
            <th>Goals Per Match (A)</th>
            <th>Goals Per Match (OVR)</th>
            <th>Min Per Goals (OVR)</th>
            <th>BTTS (OVR)</th>
            <th>BTTS (%)</th>
            <th>Goals Rating (OVR)</th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($uniqueReferees as $ref): ?>
            <tr>
                <td><?php echo $ref['id']; ?></td>
                <td><?php echo $ref['full_name']; ?></td>
                <td><?php echo $ref['goals_home']; ?></td>
                <td><?php echo $ref['goals_away']; ?></td>
                <td><?php echo $ref['goals_overall']; ?></td>
                <td><?php echo $ref['goals_per_match_home']; ?></td>
                <td><?php echo $ref['goals_per_match_away']; ?></td>
                <td><?php echo $ref['goals_per_match_overall']; ?></td>
                <td><?php echo $ref['min_per_goal_overall']; ?></td>
                <td><?php echo $ref['btts_overall']; ?></td>
                <td><?php echo $ref['btts_percentage']; ?></td>
                <td><?php echo $ref['Goal Rating']; ?></td>



            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php

// Remove duplicate entries from $referee array while keeping first occurrence
$uniqueReferees = array_values(array_unique($referee, SORT_REGULAR));


$weights = [
//Game WEIGHTS
"appearances_overall"=>0.1,
"wins_home"=>0.2,
"wins_away"=>0.1,
"draws_overall"=>0.05,
"draws_per"=>0.05,	
"wins_per_home"=>0.3,
"wins_per_away"=>0.2,
    


];



// Calculate overall referee rating for each row
foreach ($uniqueReferees as &$ref) {
    $rating = 0;
    foreach ($weights as $category => $weight) {
        $categoryValue = $ref[strtolower(str_replace(' ', '_', $category))];
        $categoryRating = $categoryValue * $weight;
        $rating += $categoryRating;
    }
    $ref['Game Rating'] = round($rating * 100/ array_sum($weights)/100) . '%';
}


// Determine the referee's bias based on the difference between home and away wins percentages
foreach ($uniqueReferees as &$ref) {
    // Calculate the total percentage of wins
    $wins_per_home = $ref['wins_per_home'];
    $wins_per_away = $ref['wins_per_away'];

// Calculate the difference between the values of wins_home_percentage and wins_away_percentage
$wins_difference = $wins_per_home - $wins_per_away;

// Define a threshold for bias detection
$bias_threshold = 5; 
if ($wins_difference > $bias_threshold) {
  $bias = "Home bias";
} elseif ($wins_difference < (-1 * $bias_threshold)) {
  $bias = "Away bias";
} else {
  $bias = "Neutral";
}
$ref['Bias'] = $bias;
}

?>
<!-- GamesTable -->
<div class="table-title">
        <h3>Referee Games Table Stats</h3></div>
<table>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Appearances</th>
            <th>Wins(H)</th>
            <th>Wins(A)</th>
            <th>Draws(OVR)</th>
            <th>Wins(%)(H)</th>
            <th>Wins(%)(A)</th>
            <th>Draws(%)</th>
            <th>BTTS (OVR)</th>
            <th>BTTS (%)</th>
            <th>Game Rating (OVR)</th>
            <th>Game Bias</th>


        </tr>
    </thead>
    <tbody>
        <?php foreach ($uniqueReferees as $ref): ?>
            <tr>
                <td><?php echo $ref['id']; ?></td>
                <td><?php echo $ref['full_name']; ?></td>
                <td><?php echo $ref['appearances_overall']; ?></td>
                <td><?php echo $ref['wins_home']; ?></td>
                <td><?php echo $ref['wins_away']; ?></td>
                <td><?php echo $ref['draws_overall']; ?></td>
                <td><?php echo $ref['wins_per_home']; ?></td>
                <td><?php echo $ref['wins_per_away']; ?></td>
                <td><?php echo $ref['draws_per']; ?></td>
                <td><?php echo $ref['btts_overall']; ?></td>
                <td><?php echo $ref['btts_percentage']; ?></td>
                <td><?php echo $ref['Game Rating']; ?></td>
                <td><?php echo $ref['Bias']; ?></td>




            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<footer class="footer">
    &copy; 2023 Premier League Analysis
</footer>
</body>
    
</html>
