<?php
require_once 'config.php';

// Fetch driver standings (sum points by driver)
$drivers_stmt = $pdo->query("
    SELECT driver_name, team, SUM(points) as total_points, COUNT(*) as races_completed
    FROM race_results rr
    JOIN races r ON rr.race_id = r.id
    WHERE r.status = 'completed'
    GROUP BY driver_name, team
    ORDER BY total_points DESC
");
$driver_standings = $drivers_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch constructor standings (sum points by team)
$constructors_stmt = $pdo->query("
    SELECT team, SUM(points) as total_points, COUNT(DISTINCT driver_name) as drivers_count
    FROM race_results rr
    JOIN races r ON rr.race_id = r.id
    WHERE r.status = 'completed'
    GROUP BY team
    ORDER BY total_points DESC
");
$constructor_standings = $constructors_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch race results with details
$results_stmt = $pdo->query("
    SELECT r.race_name, r.country, r.race_date, rr.position, rr.driver_name, rr.team, rr.points
    FROM race_results rr
    JOIN races r ON rr.race_id = r.id
    WHERE r.status = 'completed'
    ORDER BY r.race_date DESC, rr.position ASC
");
$race_results = $results_stmt->fetchAll(PDO::FETCH_ASSOC);

// Group results by race
$grouped_results = [];
foreach ($race_results as $result) {
    $race_key = $result['race_name'] . ' - ' . $result['country'];
    if (!isset($grouped_results[$race_key])) {
        $grouped_results[$race_key] = [
            'race_info' => [
                'name' => $result['race_name'],
                'country' => $result['country'],
                'date' => $result['race_date']
            ],
            'results' => []
        ];
    }
    $grouped_results[$race_key]['results'][] = $result;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - F1 Racing Hub</title>
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
                    <li><a href="news.php">News</a></li>
                    <li><a href="statistics.php" class="active">Statistics</a></li>
                    <li><a href="admin_login.php">Admin</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <!-- Driver Standings -->
        <section class="section">
            <div class="container">
                <h2 class="section-title">Driver Standings</h2>
                
                <?php if (!empty($driver_standings)): ?>
                    <div class="results-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Driver</th>
                                    <th>Team</th>
                                    <th>Points</th>
                                    <th>Races</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($driver_standings as $index => $driver): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($driver['driver_name']); ?></td>
                                    <td><?php echo htmlspecialchars($driver['team']); ?></td>
                                    <td><strong><?php echo $driver['total_points']; ?></strong></td>
                                    <td><?php echo $driver['races_completed']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No driver standings available yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Constructor Standings -->
        <section class="section bg-gray">
            <div class="container">
                <h2 class="section-title">Constructor Standings</h2>
                
                <?php if (!empty($constructor_standings)): ?>
                    <div class="results-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Team</th>
                                    <th>Points</th>
                                    <th>Drivers</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($constructor_standings as $index => $constructor): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($constructor['team']); ?></td>
                                    <td><strong><?php echo $constructor['total_points']; ?></strong></td>
                                    <td><?php echo $constructor['drivers_count']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No constructor standings available yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Race Results -->
        <section class="section">
            <div class="container">
                <h2 class="section-title">Race Results</h2>
                
                <?php if (!empty($grouped_results)): ?>
                    <?php foreach ($grouped_results as $race_key => $race_data): ?>
                        <div class="admin-card">
                            <h3><?php echo htmlspecialchars($race_data['race_info']['name']); ?> - <?php echo htmlspecialchars($race_data['race_info']['country']); ?></h3>
                            <p><strong>Date:</strong> <?php echo date('M j, Y', strtotime($race_data['race_info']['date'])); ?></p>
                            
                            <div class="results-table" style="margin-top: 1rem;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Driver</th>
                                            <th>Team</th>
                                            <th>Points</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($race_data['results'] as $result): ?>
                                        <tr>
                                            <td><?php echo $result['position']; ?></td>
                                            <td><?php echo htmlspecialchars($result['driver_name']); ?></td>
                                            <td><?php echo htmlspecialchars($result['team']); ?></td>
                                            <td><?php echo $result['points']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No race results available yet.</p>
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