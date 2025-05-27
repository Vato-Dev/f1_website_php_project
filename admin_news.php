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
                $title = $_POST['title'] ?? '';
                $content = $_POST['content'] ?? '';
                $image_url = $_POST['image_url'] ?? '';
                $status = $_POST['status'] ?? 'published';

                if ($title && $content) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO news (title, content, image_url, status) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$title, $content, $image_url, $status]);
                        $message = 'News article added successfully!';
                    } catch (PDOException $e) {
                        $error = 'Error adding news article: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Please fill in title and content fields';
                }
                break;

            case 'edit':
                $id = $_POST['id'] ?? 0;
                $title = $_POST['title'] ?? '';
                $content = $_POST['content'] ?? '';
                $image_url = $_POST['image_url'] ?? '';
                $status = $_POST['status'] ?? 'published';

                if ($id && $title && $content) {
                    try {
                        $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, image_url = ?, status = ? WHERE id = ?");
                        $stmt->execute([$title, $content, $image_url, $status, $id]);
                        $message = 'News article updated successfully!';
                    } catch (PDOException $e) {
                        $error = 'Error updating news article: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Please fill in title and content fields';
                }
                break;

            case 'delete':
                $id = $_POST['id'] ?? 0;
                if ($id) {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
                        $stmt->execute([$id]);
                        $message = 'News article deleted successfully!';
                    } catch (PDOException $e) {
                        $error = 'Error deleting news article: ' . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Get news article to edit if edit_id is provided
$edit_news = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_news = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all news articles
$stmt = $pdo->query("SELECT * FROM news ORDER BY published_at DESC");
$news_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News - F1 Racing Hub Admin</title>
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
            <a href="admin_races.php">Manage Races</a>
            <a href="admin_news.php" class="active">Manage News</a>
            <a href="admin_results.php">Race Results</a>
            <a href="index.php">View Site</a>
        </nav>
    </div>
</header>

<main>
    <section class="section">
        <div class="container">
            <h2 class="section-title"><?php echo $edit_news ? 'Edit' : 'Add'; ?> News Article</h2>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Add/Edit News Form -->
            <div class="admin-card">
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $edit_news ? 'edit' : 'add'; ?>">
                    <?php if ($edit_news): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_news['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" id="title" name="title" class="form-control"
                               value="<?php echo $edit_news ? htmlspecialchars($edit_news['title']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="content">Content *</label>
                        <textarea id="content" name="content" class="form-control" rows="8" required><?php echo $edit_news ? htmlspecialchars($edit_news['content']) : ''; ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="image_url">Image URL</label>
                            <input type="url" id="image_url" name="image_url" class="form-control"
                                   value="<?php echo $edit_news ? htmlspecialchars($edit_news['image_url']) : ''; ?>"
                                   placeholder="https://example.com/image.jpg">
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="published" <?php echo ($edit_news && $edit_news['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                                <option value="draft" <?php echo ($edit_news && $edit_news['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><?php echo $edit_news ? 'Update' : 'Add'; ?> Article</button>
                        <?php if ($edit_news): ?>
                            <a href="admin_news.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- News Articles List -->
            <div class="admin-card">
                <h3>All News Articles</h3>
                <?php if (empty($news_articles)): ?>
                    <p>No news articles found.</p>
                <?php else: ?>
                    <div class="results-table">
                        <table>
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Published Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($news_articles as $article): ?>
                                <tr>
                                    <td style="max-width: 300px;">
                                        <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                        <br>
                                        <small style="color: #666;"><?php echo htmlspecialchars(substr($article['content'], 0, 100)) . '...'; ?></small>
                                    </td>
                                    <td><?php echo date('M j, Y H:i', strtotime($article['published_at'])); ?></td>
                                    <td><span class="status <?php echo $article['status']; ?>"><?php echo ucfirst($article['status']); ?></span></td>
                                    <td>
                                        <a href="admin_news.php?edit=<?php echo $article['id']; ?>" class="btn btn-small">Edit</a>
                                        <form method="POST" action="admin_news.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
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