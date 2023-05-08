<?php


$leagueTable = file_get_contents("https://api.footystats.org/league-tables?key=example&league_id=1625");
	$leagueTable = json_decode($leagueTable, true);
	$leagueTable = $leagueTable['data']["league_table"];

    // $leagueTable = str_replace(['[', ']', '"', ','], '', $leagueTable);

$teamStats = file_get_contents("https://api.football-data-api.com/league-teams?key=example&season_id=1625&include=stats");
$teamStats = json_decode($teamStats,true);
$teamStats = $teamStats['data'];


// Create a lookup array to match team names between leagueTable and teamStats
$teamLookup = [];
foreach ($teamStats as $team) {
    $teamLookup[$team['name']] = $team;
}

    ?>


<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="leagueTable.css">
</head>
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



	<h4 class="mb2"> League Table
<small class="text-muted">EPL 2018/2019</small>
</h4>

<table class="table table-bordered">

	<thead>
		<tr>
            <th>Position</th>
			<th>Team Name</th>
			<th>Played</th>
			<th>Won</th>
			<th>Home Wins</th>
			<th>Away Wins</th>
			<th>Drawn</th>
			<th>Home Draws</th>
			<th>Away Draws</th>
			<th>Lost</th>
			<th>Home Losses</th>
			<th>Away Losses</th>
			<th>F</th>
			<th>A</th>
			<th>GD</th>
            <th>Points</th>
            
			
		</tr>
	</thead>

	<tbody>
		<?php foreach ($leagueTable as $key => $team): ?>
            <?php
            // Get the team stats from the lookup array
            $teamStats = $teamLookup[$team['name']];
            ?>
			<tr>
                <td><?php echo $team['position'] ?></td>
				<td><?php echo $team['name'] ?></td>
				<td><?php echo $team['matchesPlayed'] ?></td>
				<td><?php echo $team['seasonWins_overall'] ?></td>
				<td><?php echo $team['seasonWins_home'] ?></td>
				<td><?php echo $team['seasonWins_away'] ?></td>
				<td><?php echo $team['seasonDraws_overall'] ?></td>
				<td><?php echo $team['seasonDraws_home'] ?></td>
				<td><?php echo $team['seasonDraws_away'] ?></td>
				<td><?php echo $team['seasonLosses_away'] ?></td>
				<td><?php echo $team['seasonLosses_home'] ?></td>
				<td><?php echo $team['seasonLosses_away'] ?></td>
                <td><?php echo $team['seasonGoals'] ?></td>
				<td><?php echo $team['seasonConceded'] ?></td>
				<td><?php echo $team['seasonGoalDifference'] ?></td>
				<td><?php echo $team['points'] ?></td>
				

                
            </tr>

			</tr>
            
		<?php endforeach ?>
            

	</tbody>
</table>

</div>
<footer class="footer">
    &copy; 2023 Premier League Analysis
</footer>
</body>
</html>

