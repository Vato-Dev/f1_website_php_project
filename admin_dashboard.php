<?php
require_once 'config.php';
requireLogin();

// Get some statistics for dashboard
$races_count = $pdo->query("SELECT COUNT(*) FROM races")->fetchColumn();
$news_count = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$upcoming_races = $pdo->query("SELECT COUNT(*) FROM races WHERE race_date >= CURDATE()")->fetchColumn();
$completed_races = $pdo->query("SELECT COUNT(*) FROM races WHERE status = 'completed'")->fetchColumn();

// Get recent races
$recent_races = $pdo->query("SELECT * FROM races ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get recent news
$recent_news = $pdo->query("SELECT * FROM news ORDER BY published_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - F1 Racing Hub</title>
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
                <a href="admin_dashboard.php" class="active">Dashboard</a>
                <a href="admin_races.php">Manage Races</a>
                <a href="admin_news.php">Manage News</a>
                <a href="admin_results.php">Race Results</a>
                <a href="index.php">View Site</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <h2 class="section-title">Dashboard Overview</h2>
                
                <!-- Statistics Cards -->
                <div class="races-grid" style="margin-bottom: 3rem;">
                    <div class="admin-card text-center">
                        <h3 style="color: #dc2626; font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $races_count; ?></h3>
                        <p>Total Races</p>
                    </div>
                    <div class="admin-card text-center">
                        <h3 style="color: #059669; font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $upcoming_races; ?></h3>
                        <p>Upcoming Races</p>
                    </div>
                    <div class="admin-card text-center">
                        <h3 style="color: #7c3aed; font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $completed_races; ?></h3>
                        <p>Completed Races</p>
                    </div>
                    <div class="admin-card text-center">
                        <h3 style="color: #dc2626; font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $news_count; ?></h3>
                        <p>News Articles</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Recent Races -->
                    <div class="admin-card">
                        <h3 style="margin-bottom: 1.5rem;">Recent Races</h3>
                        <?php if (empty($recent_races)): ?>
                            <p>No races added yet.</p>
                        <?php else: ?>
                            <div class="results-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Race</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_races as $race): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($race['race_name']); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($race['race_date'])); ?></td>
                                            <td><span class="status <?php echo $race['status']; ?>"><?php echo ucfirst($race['status']); ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        <div style="margin-top: 1rem;">
                            <a href="admin_races.php" class="btn btn-primary">Manage Races</a>
                        </div>
                    </div>

                    <!-- Recent News -->
                    <div class="admin-card">
                        <h3 style="margin-bottom: 1.5rem;">Recent News</h3>
                        <?php if (empty($recent_news)): ?>
                            <p>No news articles yet.</p>
                        <?php else: ?>
                            <div class="results-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Published</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_news as $article): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(substr($article['title'], 0, 30)) . '...'; ?></td>
                                            <td><?php echo date('M j, Y', strtotime($article['published_at'])); ?></td>
                                            <td><span class="status <?php echo $article['status']; ?>"><?php echo ucfirst($article['status']); ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        <div style="margin-top: 1rem;">
                            <a href="admin_news.php" class="btn btn-primary">Manage News</a>
                        </div>
                    </div>
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