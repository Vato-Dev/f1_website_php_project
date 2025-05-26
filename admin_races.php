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
                $race_name = $_POST['race_name'] ?? '';
                $circuit = $_POST['circuit'] ?? '';
                $country = $_POST['country'] ?? '';
                $race_date = $_POST['race_date'] ?? '';
                $race_time = $_POST['race_time'] ?? '';
                $status = $_POST['status'] ?? 'scheduled';

                if ($race_name && $circuit && $country && $race_date && $race_time) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO races (race_name, circuit, country, race_date, race_time, status) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$race_name, $circuit, $country, $race_date, $race_time, $status]);
                        $message = 'Race added successfully!';
                    } catch (PDOException $e) {
                        $error = 'Error adding race: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Please fill in all required fields';
                }
                break;

            case 'edit':
                $id = $_POST['id'] ?? 0;
                $race_name = $_POST['race_name'] ?? '';
                $circuit = $_POST['circuit'] ?? '';
                $country = $_POST['country'] ?? '';
                $race_date = $_POST['race_date'] ?? '';
                $race_time = $_POST['race_time'] ?? '';
                $status = $_POST['status'] ?? 'scheduled';

                if ($id && $race_name && $circuit && $country && $race_date && $race_time) {
                    try {
                        $stmt = $pdo->prepare("UPDATE races SET race_name = ?, circuit = ?, country = ?, race_date = ?, race_time = ?, status = ? WHERE id = ?");
                        $stmt->execute([$race_name, $circuit, $country, $race_date, $race_time, $status, $id]);
                        $message = 'Race updated successfully!';
                    } catch (PDOException $e) {
                        $error = 'Error updating race: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Please fill in all required fields';
                }
                break;

            case 'delete':
                $id = $_POST['id'] ?? 0;
                if ($id) {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM races WHERE id = ?");
                        $stmt->execute([$id]);
                        $message = 'Race deleted successfully!';
                    } catch (PDOException $e) {
                        $error = 'Error deleting race: ' . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Get race to edit if edit_id is provided
$edit_race = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $stmt = $pdo->prepare("SELECT * FROM races WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_race = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all races
$stmt = $pdo->query("SELECT * FROM races ORDER BY race_date ASC");
$races = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Races - F1 Racing Hub Admin</title>
    <link rel="stylesheet" href="style.css">
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
            <a href="admin_races.php" class="active">Manage Races</a>
            <a href="admin_news.php">Manage News</a>
            <a href="admin_results.php">Race Results</a>
            <a href="index.php">View Site</a>
        </nav>
    </div>
</header>

<main>
    <section class="section">
        <div class="container">
            <h2 class="section-title"><?php echo $edit_race ? 'Edit' : 'Add'; ?> Race</h2>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Add/Edit Race Form -->
            <div class="admin-card">
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $edit_race ? 'edit' : 'add'; ?>">
                    <?php if ($edit_race): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_race['id']; ?>">
                    <?php endif; ?>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="race_name">Race Name *</label>
                            <input type="text" id="race_name" name="race_name" class="form-control"
                                   value="<?php echo $edit_race ? htmlspecialchars($edit_race['race_name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="circuit">Circuit *</label>
                            <input type="text" id="circuit" name="circuit" class="form-control"
                                   value="<?php echo $edit_race ? htmlspecialchars($edit_race['circuit']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="country">Country *</label>
                            <input type="text" id="country" name="country" class="form-control"
                                   value="<?php echo $edit_race ? htmlspecialchars($edit_race['country']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="race_date">Race Date *</label>
                            <input type="date" id="race_date" name="race_date" class="form-control"
                                   value="<?php echo $edit_race ? $edit_race['race_date'] : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="race_time">Race Time *</label>
                            <input type="time" id="race_time" name="race_time" class="form-control"
                                   value="<?php echo $edit_race ? $edit_race['race_time'] : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="scheduled" <?php echo ($edit_race && $edit_race['status'] == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                <option value="completed" <?php echo ($edit_race && $edit_race['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo ($edit_race && $edit_race['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><?php echo $edit_race ? 'Update' : 'Add'; ?> Race</button>
                        <?php if ($edit_race): ?>
                            <a href="admin_races.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Races List -->
            <div class="admin-card">
                <h3>All Races</h3>
                <?php if (empty($races)): ?>
                    <p>No races found.</p>
                <?php else: ?>
                    <div class="results-table">
                        <table>
                            <thead>
                            <tr>
                                <th>Race Name</th>
                                <th>Circuit</th>
                                <th>Country</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($races as $race): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($race['race_name']); ?></td>
                                    <td><?php echo htmlspecialchars($race['circuit']); ?></td>
                                    <td><?php echo htmlspecialchars($race['country']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($race['race_date'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($race['race_time'])); ?></td>
                                    <td><span class="status <?php echo $race['status']; ?>"><?php echo ucfirst($race['status']); ?></span></td>
                                    <td>
                                        <a href="admin_races.php?edit=<?php echo $race['id']; ?>" class="btn btn-small">Edit</a>
                                        <form method="POST" action="admin_races.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this race?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $race['id']; ?>">
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
