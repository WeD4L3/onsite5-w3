<?php
$pageTitle = "Matches Management";
include 'includes/header.php';
// Handle form submission 
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['add_match'])
) {
    try {
        $pdo->beginTransaction();
        // Insert match 
        $stmt = $pdo->prepare("INSERT INTO matches  (team1_id, team2_id, team1_goals, team2_goals, match_date)  VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['team1'],
            $_POST['team2'],
            $_POST['team1_goals'],
            $_POST['team2_goals'],
            $_POST['match_date']
        ]);

        updateTeamStats(
            $pdo,
            $_POST['team1'],
            $_POST['team1_goals'],
            $_POST['team2_goals']
        );


        updateTeamStats(
            $pdo,
            $_POST['team2'],
            $_POST['team2_goals'],
            $_POST['team1_goals']
        );

        $pdo->commit();

        echo '<div class="alert alert-success">Match added successfully!</div>';
    } catch (Exception $e) {
        $pdo->rollBack();
        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() .'</div>';
    }
}
function updateTeamStats($pdo, $teamId, $goalsFor, $goalsAgainst)
{

    $points = 0;
    if ($goalsFor > $goalsAgainst) {
        $points = 3; 
    } elseif ($goalsFor == $goalsAgainst) {
        $points = 1; 
    }

    $stmt = $pdo->prepare("UPDATE teams SET  
                            points = points + ?, 
                            goals_scored = goals_scored + ?, 
                            goals_conceded = goals_conceded + ? 
                            WHERE id = ?");
    $stmt->execute([$points, $goalsFor, $goalsAgainst, $teamId]);
}
$teams = $pdo->query("SELECT * FROM teams")->fetchAll();
?>
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h2>Add Match Result</h2>
    </div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <div class="col-md-3">
                <select class="form-select" name="team1" required>
                    <option value="">Select Team 1</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['class_name']) ?></option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="col-md-1">
                <input type="number" class="form-control"
                    name="team1_goals"  min="0">
            </div>

            <div class="col-md-1">
                <input type="number" class="form-control"
                    name="team2_goals" min="0">
            </div>

            <div class="col-md-3">
                <select class="form-select" name="team2" required>
                    <option value="">Select Team 2</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['class_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <input type="datetime-local" class="form-control" name="match_date" required>
            </div>
            <div class="col-md-1">
                <button type="submit" name="add_match" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
</div>
<h3 class="mb-3">Match History</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Match</th>
            <th>Result</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $matches = $pdo->query(" 
            SELECT m.*,  
            t1.class_name as 
            team1_name,  
            t2.class_name as team2_name 
            FROM matches m 
            JOIN teams t1 ON m.team1_id = t1.id 
            JOIN teams t2 ON m.team2_id = t2.id 
            ORDER BY m.match_date DESC 
        ")->fetchAll();

        foreach ($matches as $match): ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($match['match_date'])) ?></td>
                <td><?= htmlspecialchars($match['team1_name']) ?> vs <?= htmlspecialchars($match['team2_name']) ?></td>
                <?php if($match['team1_goals'] != NULL && $match['team2_goals'] != NULL): ?>
                <td><?= $match['team1_goals'] ?> - <?= $match['team2_goals'] ?></td>
                <?php else:?>
                    <td><span class="badge bg-success">Upcoming</span></td>
                <?php endif;?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>