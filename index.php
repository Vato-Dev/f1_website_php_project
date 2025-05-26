<?php
require_once 'config.php';

// Fetch latest news
$news_stmt = $pdo->query("SELECT * FROM news WHERE status = 'published' ORDER BY published_at DESC LIMIT 3");
$news = $news_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch upcoming races
$races_stmt = $pdo->query("SELECT * FROM races WHERE race_date >= CURDATE() ORDER BY race_date ASC LIMIT 3");
$upcoming_races = $races_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch latest race results
$results_stmt = $pdo->query("
    SELECT r.race_name, r.country, rr.position, rr.driver_name, rr.team, rr.points 
    FROM race_results rr 
    JOIN races r ON rr.race_id = r.id 
    WHERE r.status = 'completed' 
    ORDER BY r.race_date DESC, rr.position ASC 
    LIMIT 6
");
$latest_results = $results_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 Racing Hub</title>
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="calendar.php">Calendar</a></li>
                    <li><a href="news.php">News</a></li>
                    <li><a href="statistics.php">Statistics</a></li>
                    <li><a href="admin_login.php">Admin</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h2>Welcome to F1 Racing Hub</h2>
                <p>Your ultimate destination for Formula 1 news, race calendar, and statistics</p>
            </div>
        </section>

        <!-- Latest News Section -->
        <section class="section">
            <div class="container">
                <h3 class="section-title">Latest News</h3>
                <div class="news-grid">
                    <?php foreach ($news as $article): ?>
                    <div class="news-card">
                        <?php if ($article['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <?php endif; ?>
                        <div class="news-content">
                            <h4><?php echo htmlspecialchars($article['title']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($article['content'], 0, 150)) . '...'; ?></p>
                            <small>Published: <?php echo date('M j, Y', strtotime($article['published_at'])); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center">
                    <a href="news.php" class="btn btn-primary">View All News</a>
                </div>
            </div>
        </section>

        <!-- Upcoming Races Section -->
        <section class="section bg-gray">
            <div class="container">
                <h3 class="section-title">Upcoming Races</h3>
                <div class="races-grid">
                    <?php foreach ($upcoming_races as $race): ?>
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
                <div class="text-center">
                    <a href="calendar.php" class="btn btn-secondary">View Full Calendar</a>
                </div>
            </div>
        </section>

        <!-- Latest Results Section -->
        <section class="section">
            <div class="container">
                <h3 class="section-title">Latest Race Results</h3>
                <?php if ($latest_results): ?>
                <div class="results-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Race</th>
                                <th>Position</th>
                                <th>Driver</th>
                                <th>Team</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latest_results as $result): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['race_name']); ?></td>
                                <td><?php echo $result['position']; ?></td>
                                <td><?php echo htmlspecialchars($result['driver_name']); ?></td>
                                <td><?php echo htmlspecialchars($result['team']); ?></td>
                                <td><?php echo $result['points']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No race results available yet.</p>
                <?php endif; ?>
                <div class="text-center">
                    <a href="statistics.php" class="btn btn-primary">View All Statistics</a>
                </div>
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