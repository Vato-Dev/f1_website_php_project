<?php
require_once 'config.php';

// Fetch all published news
$stmt = $pdo->query("SELECT * FROM news WHERE status = 'published' ORDER BY published_at DESC");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 News - F1 Racing Hub</title>
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
                    <li><a href="calendar.php">Calendar</a></li>
                    <li><a href="news.php" class="active">News</a></li>
                    <li><a href="statistics.php">Statistics</a></li>
                    <li><a href="admin_login.php">Admin</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <h2 class="section-title">Latest F1 News</h2>
                
                <?php if (empty($news)): ?>
                    <p class="text-center">No news articles available yet.</p>
                <?php else: ?>
                    <div class="news-grid">
                        <?php foreach ($news as $article): ?>
                            <div class="news-card">
                                <?php if ($article['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                <?php endif; ?>
                                <div class="news-content">
                                    <h4><?php echo htmlspecialchars($article['title']); ?></h4>
                                    <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                                    <small>Published: <?php echo date('M j, Y \a\t H:i', strtotime($article['published_at'])); ?></small>
                                </div>
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