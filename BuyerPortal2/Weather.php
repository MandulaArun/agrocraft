<?php
session_name('agro_buyer');
session_start();
include("../Includes/db.php");
include("../Functions/functions.php");

if (!isset($_SESSION['phonenumber'])) {
    echo "<script>window.open('../auth/BuyerLogin.php','_self')</script>";
    exit;
}

$sess_phone_number = $_SESSION['phonenumber'];
$buyer_query = "SELECT buyer_id, buyer_name, buyer_addr FROM buyerregistration WHERE buyer_phone = $sess_phone_number";
$buyer_result = mysqli_query($con, $buyer_query);
$buyer_row = mysqli_fetch_array($buyer_result);
$buyer_id = $buyer_row['buyer_id'];
$buyer_name = $buyer_row['buyer_name'];
$buyer_address = $buyer_row['buyer_addr'];

// Determine location for weather API
$location = $buyer_address . ", India";
if (empty($buyer_address)) {
    $location = "India";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Information - Buyer</title>
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .weather-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .weather-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .weather-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .weather-header p {
            color: #666;
        }
        .weather-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .weather-main {
            text-align: center;
            padding: 20px 0;
        }
        .weather-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            color: #f5576c;
        }
        .temperature {
            font-size: 4rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        .weather-description {
            font-size: 1.5rem;
            color: #666;
            text-transform: capitalize;
            margin-bottom: 30px;
        }
        .weather-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1px));
            gap: 20px;
            margin-top: 30px;
        }
        .detail-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .detail-card h5 {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        .detail-card p {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .forecast-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .forecast-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        .forecast-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .forecast-item:last-child {
            border-bottom: none;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: white;
            font-size: 1.2rem;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .location-info {
            background: #fce4ec;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="weather-container">
        <div class="weather-header">
            <h2><i class="fas fa-cloud-sun"></i> Weather Information</h2>
            <p>Current weather conditions for your location</p>
        </div>

        <div class="location-info">
            <p><strong>Location:</strong> <?php echo htmlspecialchars($location); ?></p>
            <p><strong>Buyer:</strong> <?php echo htmlspecialchars($buyer_name); ?></p>
        </div>

        <div class="weather-card">
            <div id="weather-loading" class="loading">
                <i class="fas fa-spinner fa-spin"></i> Loading weather data...
            </div>
            <div id="weather-content" style="display: none;">
                <div class="weather-main">
                    <div class="weather-icon" id="weather-icon">
                        <i class="fas fa-sun"></i>
                    </div>
                    <div class="temperature" id="temperature">--°C</div>
                    <div class="weather-description" id="weather-description">Loading...</div>
                </div>
                <div class="weather-details">
                    <div class="detail-card">
                        <h5><i class="fas fa-thermometer-half"></i> Feels Like</h5>
                        <p id="feels-like">--°C</p>
                    </div>
                    <div class="detail-card">
                        <h5><i class="fas fa-tint"></i> Humidity</h5>
                        <p id="humidity">--%</p>
                    </div>
                    <div class="detail-card">
                        <h5><i class="fas fa-wind"></i> Wind Speed</h5>
                        <p id="wind-speed">-- km/h</p>
                    </div>
                    <div class="detail-card">
                        <h5><i class="fas fa-compress-arrows-alt"></i> Pressure</h5>
                        <p id="pressure">-- hPa</p>
                    </div>
                    <div class="detail-card">
                        <h5><i class="fas fa-eye"></i> Visibility</h5>
                        <p id="visibility">-- km</p>
                    </div>
                    <div class="detail-card">
                        <h5><i class="fas fa-cloud-rain"></i> Cloudiness</h5>
                        <p id="cloudiness">--%</p>
                    </div>
                </div>
            </div>
            <div id="weather-error" style="display: none;" class="error-message">
                <strong>Error:</strong> Unable to load weather data. Please check your API key configuration.
            </div>
        </div>

        <div class="forecast-section">
            <h3 class="forecast-title"><i class="fas fa-calendar-week"></i> 5-Day Forecast</h3>
            <div id="forecast-content">
                <div class="loading">Loading forecast...</div>
            </div>
        </div>
    </div>

    <script>
        var apiKey = '<?php echo htmlspecialchars(getenv('OPENWEATHER_KEY') ?: '', ENT_QUOTES); ?>';
        var location = '<?php echo addslashes($location); ?>';
        
        // Get weather icon based on condition
        function getWeatherIcon(condition) {
            var iconMap = {
                'clear sky': 'fa-sun',
                'few clouds': 'fa-cloud-sun',
                'scattered clouds': 'fa-cloud',
                'broken clouds': 'fa-cloud',
                'shower rain': 'fa-cloud-rain',
                'rain': 'fa-cloud-showers-heavy',
                'thunderstorm': 'fa-bolt',
                'snow': 'fa-snowflake',
                'mist': 'fa-smog',
                'fog': 'fa-smog',
                'haze': 'fa-smog'
            };
            
            var conditionLower = condition.toLowerCase();
            for (var key in iconMap) {
                if (conditionLower.includes(key)) {
                    return iconMap[key];
                }
            }
            return 'fa-cloud';
        }

        // Load current weather
        function loadWeather() {
            if (!apiKey) {
                showError();
                return;
            }
            var apiUrl = 'https://api.openweathermap.org/data/2.5/weather?q=' + encodeURIComponent(location) + '&appid=' + apiKey + '&units=metric';
            
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.cod === 200) {
                        document.getElementById('weather-loading').style.display = 'none';
                        document.getElementById('weather-content').style.display = 'block';
                        
                        // Update main weather info
                        document.getElementById('temperature').textContent = Math.round(data.main.temp) + '°C';
                        document.getElementById('weather-description').textContent = data.weather[0].description;
                        document.getElementById('feels-like').textContent = Math.round(data.main.feels_like) + '°C';
                        document.getElementById('humidity').textContent = data.main.humidity + '%';
                        document.getElementById('wind-speed').textContent = Math.round(data.wind.speed * 3.6) + ' km/h';
                        document.getElementById('pressure').textContent = data.main.pressure + ' hPa';
                        document.getElementById('visibility').textContent = (data.visibility / 1000).toFixed(1) + ' km';
                        document.getElementById('cloudiness').textContent = data.clouds.all + '%';
                        
                        // Update icon
                        var iconClass = getWeatherIcon(data.weather[0].description);
                        document.getElementById('weather-icon').innerHTML = '<i class="fas ' + iconClass + '"></i>';
                    } else {
                        showError();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError();
                });
        }

        // Load forecast
        function loadForecast() {
            if (!apiKey) {
                document.getElementById('forecast-content').innerHTML = '<div class="error-message">API key missing. Set OPENWEATHER_KEY.</div>';
                return;
            }
            var apiUrl = 'https://api.openweathermap.org/data/2.5/forecast?q=' + encodeURIComponent(location) + '&appid=' + apiKey + '&units=metric';
            
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.cod === '200') {
                        var forecastHtml = '';
                        var dailyData = {};
                        
                        // Group by day
                        data.list.forEach(function(item) {
                            var date = new Date(item.dt * 1000);
                            var dayKey = date.toDateString();
                            
                            if (!dailyData[dayKey]) {
                                dailyData[dayKey] = {
                                    date: date,
                                    temps: [],
                                    description: item.weather[0].description,
                                    icon: getWeatherIcon(item.weather[0].description)
                                };
                            }
                            dailyData[dayKey].temps.push(item.main.temp);
                        });
                        
                        // Display forecast (next 5 days)
                        var count = 0;
                        for (var dayKey in dailyData) {
                            if (count >= 5) break;
                            var day = dailyData[dayKey];
                            var minTemp = Math.round(Math.min(...day.temps));
                            var maxTemp = Math.round(Math.max(...day.temps));
                            var dayName = day.date.toLocaleDateString('en-US', { weekday: 'long' });
                            var dateStr = day.date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                            
                            forecastHtml += '<div class="forecast-item">';
                            forecastHtml += '<div><i class="fas ' + day.icon + '"></i> <strong>' + dayName + '</strong> <span style="color: #666;">' + dateStr + '</span></div>';
                            forecastHtml += '<div><span style="color: #666;">' + day.description + '</span> <strong style="margin-left: 15px;">' + minTemp + '°C / ' + maxTemp + '°C</strong></div>';
                            forecastHtml += '</div>';
                            count++;
                        }
                        
                        document.getElementById('forecast-content').innerHTML = forecastHtml;
                    }
                })
                .catch(error => {
                    console.error('Forecast Error:', error);
                    document.getElementById('forecast-content').innerHTML = '<div class="error-message">Unable to load forecast data.</div>';
                });
        }

        function showError() {
            document.getElementById('weather-loading').style.display = 'none';
            document.getElementById('weather-error').style.display = 'block';
        }

        // Load weather on page load
        loadWeather();
        loadForecast();
        
        // Auto-refresh every 30 minutes
        setInterval(function() {
            loadWeather();
            loadForecast();
        }, 1800000);
    </script>
</body>
</html>

