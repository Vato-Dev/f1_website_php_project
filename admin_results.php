<?php
require_once 'config.php';
requireLogin();

$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $race_id = $_POST['race_id'] ?? 0;
                $position = $_POST['position'] ?? 0;
                $driver_name = $_POST['driver_name'] ?? '';
                $team = $_POST['team'] ?? '';
                $points = $_POST['points'] ?? 0;

                if ($race_id && $position && $driver_name && $team) {
                    try {
                        // Check if position already exists for this race
                        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM race_results WHERE race_id = ? AND position = ?");
                        $check_stmt->execute([$race_id, $position]);
                        
                        if ($check_stmt->fetchColumn() > 0) {
                            $error = 'Position ' . $position . ' already exists for this race.';
                        } else {
                            $stmt = $pdo->prepare("INSERT INTO race_results (race_id, position, driver_name, team, points) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$race_id, $position, $driver_name, $team, $points]);
                            $message = 'Race result added successfully!';
                        }
                    } catch (PDOException $e) {
                        $error = 'Error adding race result: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Please fill in all required fields';
                }
                break;

            case 'edit':
                $id = $_POST['id'] ?? 0;
                $race_id = $_POST['race_id'] ?? 0;
                $position = $_POST['position'] ?? 0;
                $driver_name = $_POST['driver_name'] ?? '';
                $team = $_POST['team'] ?? '';
                $points = $_POST['points'] ?? 0;

                if ($id && $race_id && $position && $driver_name && $team) {
                    try {
                        // Check if position already exists for this race (excluding current record)
                        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM race_results WHERE race_id = ? AND position = ? AND id != ?");
                        $check_stmt->execute([$race_id, $position, $id]);
                        
                        if ($check_stmt->fetchColumn() > 0) {
                            $error = 'Position ' . $position . ' already exists for this race.';
                        } else {
                            $stmt = $pdo->prepare("UPDATE race_results SET race_id = ?, position = ?, driver_name = ?, team = ?, points = ? WHERE id = ?");
                            $stmt->execute([$race_id, $position, $driver_name, $team, $points, $id]);
                            $message = 'Race result updated successfully!';
                        }
                    } catch (PDOException $e) {
                        $error = 'Error updating race result: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Please fill in all required fields';
                }
                break;

            case 'delete':
                $id = $_POST['id'] ?? 0;
                if ($id) {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM race_results WHERE id = ?");
                        $stmt->execute([$id]);
                        $message = 'Race result deleted successfully!';
                    } catch (PDOException $e) {
                        $error = 'Error deleting race result: ' . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Get result to edit if edit_id is provided
$edit_result = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $stmt = $pdo->prepare("SELECT * FROM race_results WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_result = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get selected race for filtering
$selected_race_id = $_GET['race_id'] ?? '';

// Fetch all races for dropdown
$races_stmt = $pdo->query("SELECT * FROM races ORDER BY race_date DESC");
$races = $races_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch race results with race information
$results_query = "SELECT rr.*, r.race_name, r.race_date 
                  FROM race_results rr 
                  JOIN races r ON rr.race_id = r.id";

if ($selected_race_id) {
    $results_query .= " WHERE rr.race_id = " . intval($selected_race_id);
}

$results_query .= " ORDER BY r.race_date DESC, rr.position ASC";

$results_stmt = $pdo->query($results_query);
$results = $results_stmt->fetchAll(PDO::FETCH_ASSOC);

// Points system for F1
$points_system = [
    1 => 25, 2 => 18, 3 => 15, 4 => 12, 5 => 10,
    6 => 8, 7 => 6, 8 => 4, 9 => 2, 10 => 1
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Race Results - F1 Racing Hub Admin</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function updatePoints() {
            const position = document.getElementById('position').value;
            const pointsField = document.getElementById('points');
            const pointsSystem = {
                1: 25, 2: 18, 3: 15, 4: 12, 5: 10,
                6: 8, 7: 6, 8: 4, 9: 2, 10: 1
            };
            
            if (pointsSystem[position]) {
                pointsField.value = pointsSystem[position];
            } else {
                pointsField.value = 0;
            }
        }
    </script>
</head>
<body>
<header class="admin-header">
    <div class="container">
        <div class="nav-container">
            <div class="logo">
                <h1>F1 Racing Hub - Admin</h1>
            </div>
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="admin_logout.php" class="btn btn-secondary" style="margin-left: 1rem;">Logout</a>
            </div>
        </div>
        <nav class="admin-nav">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_races.php">Manage Races</a>
            <a href="admin_news.php">Manage News</a>
            <a href="admin_results.php" class="active">Race Results</a>
            <a href="index.php">View Site</a>
        </nav>
    </div>
</header>

<main>
    <section class="section">
        <div class="container">
            <h2 class="section-title"><?php echo $edit_result ? 'Edit' : 'Add'; ?> Race Result</h2>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Add/Edit Result Form -->
            <div class="admin-card">
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $edit_result ? 'edit' : 'add'; ?>">
                    <?php if ($edit_result): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_result['id']; ?>">
                    <?php endif; ?>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="race_id">Race *</label>
                            <select id="race_id" name="race_id" class="form-control" required>
                                <option value="">Select Race</option>
                                <?php foreach ($races as $race): ?>
                                    <option value="<?php echo $race['id']; ?>" 
                                            <?php echo ($edit_result && $edit_result['race_id'] == $race['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($race['race_name']) . ' - ' . date('M j, Y', strtotime($race['race_date'])); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="position">Position *</label>
                            <input type="number" id="position" name="position" class="form-control" min="1" max="20"
                                   value="<?php echo $edit_result ? $edit_result['position'] : ''; ?>" 
                                   onchange="updatePoints()" required>
                        </div>

                        <div class="form-group">
                            <label for="points">Points</label>
                            <input type="number" id="points" name="points" class="form-control" min="0"
                                   value="<?php echo $edit_result ? $edit_result['points'] : ''; ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="driver_name">Driver Name *</label>
                            <input type="text" id="driver_name" name="driver_name" class="form-control"
                                   value="<?php echo $edit_result ? htmlspecialchars($edit_result['driver_name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="team">Team *</label>
                            <input type="text" id="team" name="team" class="form-control"
                                   value="<?php echo $edit_result ? htmlspecialchars($edit_result['team']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><?php echo $edit_result ? 'Update' : 'Add'; ?> Result</button>
                        <?php if ($edit_result): ?>
                            <a href="admin_results.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Filter and Results List -->
            <div class="admin-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3>Race Results</h3>
                    <form method="GET" style="display: flex; gap: 1rem; align-items: center;">
                        <select name="race_id" class="form-control" style="width: auto;" onchange="this.form.submit()">
                            <option value="">All Races</option>
                            <?php foreach ($races as $race): ?>
                                <option value="<?php echo $race['id']; ?>" <?php echo ($selected_race_id == $race['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($race['race_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($selected_race_id): ?>
                            <a href="admin_results.php" class="btn btn-small">Clear Filter</a>
                        <?php endif; ?>
                    </form>
                </div>

                <?php if (empty($results)): ?>
                    <p>No race results found.</p>
                <?php else: ?>
                    <div class="results-table">
                        <table>
                            <thead>
                            <tr>
                                <th>Race</th>
                                <th>Position</th>
                                <th>Driver</th>
                                <th>Team</th>
                                <th>Points</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $current_race = '';
                            foreach ($results as $result): 
                                $race_header = $result['race_name'] . ' - ' . date('M j, Y', strtotime($result['race_date']));
                                $show_race_name = ($current_race !== $race_header);
                                $current_race = $race_header;
                            ?>
                                <tr>
                                    <td><?php echo $show_race_name ? htmlspecialchars($race_header) : ''; ?></td>
                                    <td>
                                        <strong style="<?php echo $result['position'] <= 3 ? 'color: #dc2626;' : ''; ?>">
                                            <?php echo $result['position']; ?>
                                            <?php 
                                            if ($result['position'] == 1) echo ' 🥇';
                                            elseif ($result['position'] == 2) echo ' 🥈';
                                            elseif ($result['position'] == 3) echo ' 🥉';
                                            ?>
                                        </strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($result['driver_name']); ?></td>
                                    <td><?php echo htmlspecialchars($result['team']); ?></td>
                                    <td><strong><?php echo $result['points']; ?></strong></td>
                                    <td>
                                        <a href="admin_results.php?edit=<?php echo $result['id']; ?>" class="btn btn-small">Edit</a>
                                        <form method="POST" action="admin_results.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this result?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $result['id']; ?>">
                                            <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<footer class="admin-footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> F1 Racing Hub. All rights reserved.</p>
    </div>
</footer>
</body>
</html>