<?php
require_once 'config.php';

// Fetch all races
$stmt = $pdo->query("SELECT * FROM races ORDER BY race_date ASC");
$races = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Race Calendar - F1 Racing Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <h1>F1 Racing Hub</h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="calendar.php" class="active">Calendar</a></li>
                    <li><a href="news.php">News</a></li>
                    <li><a href="statistics.php">Statistics</a></li>
                    <li><a href="admin_login.php">Admin</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <h2 class="section-title">2024 Race Calendar</h2>
                
                <?php if (empty($races)): ?>
                    <p class="text-center">No races scheduled yet.</p>
                <?php else: ?>
                    <div class="races-grid">
                        <?php foreach ($races as $race): ?>
                            <div class="race-card">
                                <h4><?php echo htmlspecialchars($race['race_name']); ?></h4>
                                <p><strong>Circuit:</strong> <?php echo htmlspecialchars($race['circuit']); ?></p>
                                <p><strong>Country:</strong> <?php echo htmlspecialchars($race['country']); ?></p>
                                <p><strong>Date:</strong> <?php echo date('M j, Y', strtotime($race['race_date'])); ?></p>
                                <p><strong>Time:</strong> <?php echo date('H:i', strtotime($race['race_time'])); ?></p>
                                <span class="status <?php echo $race['status']; ?>"><?php echo ucfirst($race['status']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 F1 Racing Hub. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>