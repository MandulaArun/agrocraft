<?php
/**
 * Database Setup Script for AgroCraft Messages Table
 * 
 * Instructions:
 * 1. Make sure your database connection is correct in Includes/db.php
 * 2. Open this file in your browser: http://localhost/agrocraft/setup_database.php
 * 3. Click the "Create Messages Table" button
 * 4. You should see a success message
 * 
 * Note: This script will only create the table if it doesn't already exist.
 */

include("Includes/db.php");

$success = false;
$error = "";

if (isset($_POST['create_table'])) {
    $sql = "CREATE TABLE IF NOT EXISTS `messages` (
      `message_id` int(11) NOT NULL AUTO_INCREMENT,
      `sender_id` int(11) NOT NULL,
      `sender_type` varchar(20) NOT NULL COMMENT 'farmer or buyer',
      `receiver_id` int(11) NOT NULL,
      `receiver_type` varchar(20) NOT NULL COMMENT 'farmer or buyer',
      `message_text` text NOT NULL,
      `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `is_read` tinyint(1) NOT NULL DEFAULT 0,
      PRIMARY KEY (`message_id`),
      KEY `sender_id` (`sender_id`),
      KEY `receiver_id` (`receiver_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    
    if (mysqli_query($con, $sql)) {
        $success = true;
    } else {
        $error = "Error: " . mysqli_error($con);
    }
}

// Check if table exists
$table_exists = false;
$check_query = "SHOW TABLES LIKE 'messages'";
$result = mysqli_query($con, $check_query);
if (mysqli_num_rows($result) > 0) {
    $table_exists = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - AgroCraft</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .setup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .setup-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .status-box {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .btn-setup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-setup:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .info-box h5 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .info-box ul {
            margin-left: 20px;
        }
        .info-box li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h2><i class="fas fa-database"></i> Database Setup</h2>
            <p>AgroCraft Messages Table</p>
        </div>

        <?php if ($success): ?>
            <div class="status-box status-success">
                <strong>✓ Success!</strong> The messages table has been created successfully.
            </div>
        <?php elseif ($error): ?>
            <div class="status-box status-error">
                <strong>✗ Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($table_exists): ?>
            <div class="status-box status-info">
                <strong>ℹ Info:</strong> The messages table already exists in your database.
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <button type="submit" name="create_table" class="btn btn-setup" <?php echo $table_exists ? 'disabled' : ''; ?>>
                <?php echo $table_exists ? '✓ Table Already Exists' : 'Create Messages Table'; ?>
            </button>
        </form>

        <div class="info-box">
            <h5>Database Information:</h5>
            <ul>
                <li><strong>Database:</strong> <?php echo mysqli_get_server_info($con); ?></li>
                <li><strong>Current Database:</strong> impulse101</li>
                <li><strong>Table Name:</strong> messages</li>
                <li><strong>Status:</strong> <?php echo $table_exists ? '✓ Exists' : '✗ Not Found'; ?></li>
            </ul>
        </div>

        <div class="info-box" style="margin-top: 20px;">
            <h5>Next Steps:</h5>
            <ol>
                <li>If table was created successfully, you're done with database setup!</li>
                <li>Configure your OpenWeatherMap API key in:
                    <ul>
                        <li><code>FarmerPortal/Messages.php</code></li>
                        <li><code>BuyerPortal2/Messages.php</code></li>
                    </ul>
                </li>
                <li>Get your free API key from: <a href="https://openweathermap.org/api" target="_blank">openweathermap.org/api</a></li>
                <li>Test the messaging system by logging in as a farmer or buyer</li>
            </ol>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.html" class="btn btn-secondary">← Back to Home</a>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/c587fc1763.js" crossorigin="anonymous"></script>
</body>
</html>

