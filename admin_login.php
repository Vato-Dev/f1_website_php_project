<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

$error = '';

if ($_POST) {
    // Убираем пробелы по краям
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Отладочный вывод — удалить после теста
            var_dump('Entered password:', $password);
            var_dump('Password hash from DB:', $user['password']);
            var_dump('password_verify result:', password_verify($password, $user['password']));
        }

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please fill in all fields';
    }
}

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: admin_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login - F1 Racing Hub</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><h1>F1 Racing Hub</h1></div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="calendar.php">Calendar</a></li>
                <li><a href="news.php">News</a></li>
                <li><a href="statistics.php">Statistics</a></li>
                <li><a href="admin_login.php" class="active">Admin</a></li>
            </ul>
        </div>
    </nav>
</header>

<main>
    <section class="section">
        <div class="container">
            <div style="max-width: 400px; margin: 0 auto;">
                <div class="admin-card">
                    <h2 style="text-align: center; margin-bottom: 2rem; color: #dc2626;">Admin Login</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required />
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                    </form>
                    <div style="margin-top: 2rem; padding: 1rem; background-color: #f3f4f6; border-radius: 5px; font-size: 0.9rem;">
                        <strong>Default Login:</strong><br />
                        Username: admin<br />
                        Password: admin123
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