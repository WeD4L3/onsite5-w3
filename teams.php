<?php
$pageTitle = "Teams Management";
include 'includes/header.php';
?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h2>Teams Management</h2>
    </div>
    <div class="card-body">
        <?php
        // Handle form submission 
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['add_team'])
        ) {
            $stmt = $pdo->prepare("INSERT INTO teams (class_name) VALUES (?)");
            $stmt->execute([$_POST['class_name']]);
            echo '<div class="alert alert-success">Team added successfully!</div>';
        }
        if (isset($_GET['delete_team'])) {
            $stmt = $pdo->prepare("UPDATE teams  SET is_visible = 0 WHERE id = ?");
            $stmt->execute([filter_var($_GET['delete_team'],FILTER_VALIDATE_INT)]);
            echo '<div class="alert alert-success">Team deleted successfully!</div>';
        }

        ?>
        <form method="post" class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" class="form-control" name="class_name" placeholder="Class Name" required>
            </div>
            <div class="col-md-6">
                <button type="submit" name="add_team" class="btn btn-success">Add Team</button>
            </div>

        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Points</th>
                    <th>Goals For</th>
                    <th>Goals Against</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $teams = $pdo->query("SELECT * FROM teams WHERE is_visible")->fetchAll();
                foreach ($teams as $team): ?>
                    <tr>
                        <td><?= htmlspecialchars($team['class_name']) ?></td>
                        <td><?= $team['points'] ?></td>
                        <td><?= $team['goals_scored'] ?></td>
                        <td><?= $team['goals_conceded'] ?></td>
                        <td>
                            <a href="" class="btn btn-sm btn-warning">Edit</a>
                            <a href="teams.php?delete_team=<?=$team['id']?>" onclick="return confirm('are sure do want to delete this team?')" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>