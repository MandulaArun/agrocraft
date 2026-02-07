<?php
session_name('agro_farmer');
session_start();
include("../Includes/db.php");
include("../Functions/functions.php");

if (!isset($_SESSION['phonenumber'])) {
    echo "<script>window.open('../auth/FarmerLogin.php','_self')</script>";
    exit;
}

$sess_phone_number = $_SESSION['phonenumber'];
$farmer_query = "SELECT farmer_id FROM farmerregistration WHERE farmer_phone = $sess_phone_number";
$farmer_result = mysqli_query($con, $farmer_query);
$farmer_row = mysqli_fetch_array($farmer_result);
$farmer_id = $farmer_row['farmer_id'];

// Handle sending messages
if (isset($_POST['send_message'])) {
    $receiver_id = mysqli_real_escape_string($con, $_POST['receiver_id']);
    $receiver_type = mysqli_real_escape_string($con, $_POST['receiver_type']);
    $message_text = mysqli_real_escape_string($con, $_POST['message_text']);
    
    $insert_message = "INSERT INTO messages (sender_id, sender_type, receiver_id, receiver_type, message_text) 
                       VALUES ('$farmer_id', 'farmer', '$receiver_id', '$receiver_type', '$message_text')";
    mysqli_query($con, $insert_message);
}

// Get all conversations
$conversations_query = "SELECT DISTINCT 
    CASE 
        WHEN sender_id = $farmer_id AND sender_type = 'farmer' THEN receiver_id
        WHEN receiver_id = $farmer_id AND receiver_type = 'farmer' THEN sender_id
    END as other_user_id,
    CASE 
        WHEN sender_id = $farmer_id AND sender_type = 'farmer' THEN receiver_type
        WHEN receiver_id = $farmer_id AND receiver_type = 'farmer' THEN sender_type
    END as other_user_type
FROM messages 
WHERE (sender_id = $farmer_id AND sender_type = 'farmer') 
   OR (receiver_id = $farmer_id AND receiver_type = 'farmer')
ORDER BY timestamp DESC";

$conversations_result = mysqli_query($con, $conversations_query);

// Get selected conversation
$selected_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$selected_user_type = isset($_GET['user_type']) ? $_GET['user_type'] : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Farmer</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/c587fc1763.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
        }
        .messages-container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .messages-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .conversations-list {
            height: 600px;
            overflow-y: auto;
            border-right: 1px solid #e0e0e0;
        }
        .conversation-item {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
            transition: background 0.3s;
        }
        .conversation-item:hover {
            background: #f5f7fa;
        }
        .conversation-item.active {
            background: #e8eaf6;
        }
        .chat-area {
            height: 600px;
            display: flex;
            flex-direction: column;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9f9f9;
        }
        .message {
            margin-bottom: 15px;
            padding: 12px 15px;
            border-radius: 15px;
            max-width: 70%;
            word-wrap: break-word;
        }
        .message.sent {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-left: auto;
        }
        .message.received {
            background: white;
            color: #333;
            border: 1px solid #e0e0e0;
        }
        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 5px;
        }
        .chat-input-area {
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            background: white;
        }
        .weather-widget {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .weather-widget h5 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="messages-container">
        <div class="messages-header">
            <h3><i class="fas fa-comments mr-2"></i>Messages</h3>
        </div>
        <div class="row no-gutters">
            <div class="col-md-4">
                <div class="conversations-list">
                    <?php
                    while ($conv = mysqli_fetch_array($conversations_result)) {
                        $other_id = $conv['other_user_id'];
                        $other_type = $conv['other_user_type'];
                        
                        if ($other_type == 'buyer') {
                            $user_query = "SELECT buyer_name FROM buyerregistration WHERE buyer_id = $other_id";
                        } else {
                            $user_query = "SELECT farmer_name FROM farmerregistration WHERE farmer_id = $other_id";
                        }
                        $user_result = mysqli_query($con, $user_query);
                        $user_row = mysqli_fetch_array($user_result);
                        $user_name = $other_type == 'buyer' ? $user_row['buyer_name'] : $user_row['farmer_name'];
                        
                        $active_class = ($selected_user_id == $other_id && $selected_user_type == $other_type) ? 'active' : '';
                        echo "<div class='conversation-item $active_class' onclick=\"window.location.href='Messages.php?user_id=$other_id&user_type=$other_type'\">";
                        echo "<strong>$user_name</strong> ($other_type)";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-8">
                <?php if ($selected_user_id): ?>
                    <div class="chat-area">
                        <div class="weather-widget">
                            <h5><i class="fas fa-cloud-sun mr-2"></i>Weather Information</h5>
                            <div id="weather-info">Loading weather data...</div>
                        </div>
                        <div class="chat-messages" id="chat-messages">
                            <?php
                            $messages_query = "SELECT * FROM messages 
                                            WHERE ((sender_id = $farmer_id AND sender_type = 'farmer' AND receiver_id = $selected_user_id AND receiver_type = '$selected_user_type')
                                                OR (sender_id = $selected_user_id AND sender_type = '$selected_user_type' AND receiver_id = $farmer_id AND receiver_type = 'farmer'))
                                            ORDER BY timestamp ASC";
                            $messages_result = mysqli_query($con, $messages_query);
                            
                            while ($msg = mysqli_fetch_array($messages_result)) {
                                $is_sent = ($msg['sender_id'] == $farmer_id && $msg['sender_type'] == 'farmer');
                                $class = $is_sent ? 'sent' : 'received';
                                $time = date('H:i', strtotime($msg['timestamp']));
                                
                                echo "<div class='message $class'>";
                                echo "<div>" . htmlspecialchars($msg['message_text']) . "</div>";
                                echo "<div class='message-time'>$time</div>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                        <div class="chat-input-area">
                            <form method="POST" action="">
                                <input type="hidden" name="receiver_id" value="<?php echo $selected_user_id; ?>">
                                <input type="hidden" name="receiver_type" value="<?php echo $selected_user_type; ?>">
                                <div class="input-group">
                                    <input type="text" name="message_text" class="form-control" placeholder="Type your message..." required>
                                    <div class="input-group-append">
                                        <button type="submit" name="send_message" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="chat-area" style="display: flex; align-items: center; justify-content: center;">
                        <div class="text-center text-muted">
                            <i class="fas fa-comments" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
                            <h4>Select a conversation to start messaging</h4>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-scroll to bottom
        var chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Weather API (using OpenWeatherMap - you'll need to get a free API key)
        function loadWeather() {
            // Get farmer's location from database
            <?php
            $location_query = "SELECT farmer_state, farmer_district FROM farmerregistration WHERE farmer_id = $farmer_id";
            $location_result = mysqli_query($con, $location_query);
            $location_row = mysqli_fetch_array($location_result);
            $state = $location_row['farmer_state'];
            $district = $location_row['farmer_district'];
            ?>
            
            var city = '<?php echo $district . ", " . $state; ?>';
            var apiKey = 'ba3b175573bb03c751fd5c95f9f08f99'; // Replace with your API key
            var apiUrl = 'https://api.openweathermap.org/data/2.5/weather?q=' + city + ',IN&appid=' + apiKey + '&units=metric';
            
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.cod === 200) {
                        var weatherHtml = '<p><strong>Location:</strong> ' + data.name + '</p>';
                        weatherHtml += '<p><strong>Temperature:</strong> ' + Math.round(data.main.temp) + '°C</p>';
                        weatherHtml += '<p><strong>Condition:</strong> ' + data.weather[0].description + '</p>';
                        weatherHtml += '<p><strong>Humidity:</strong> ' + data.main.humidity + '%</p>';
                        document.getElementById('weather-info').innerHTML = weatherHtml;
                    } else {
                        document.getElementById('weather-info').innerHTML = '<p>Weather data unavailable. Please configure API key.</p>';
                    }
                })
                .catch(error => {
                    document.getElementById('weather-info').innerHTML = '<p>Weather data unavailable. Please configure API key.</p>';
                });
        }
        
        // Load weather on page load
        loadWeather();
        
        // Auto-refresh messages every 5 seconds
        setInterval(function() {
            if (<?php echo $selected_user_id ? 'true' : 'false'; ?>) {
                location.reload();
            }
        }, 5000);
    </script>
</body>
</html>

