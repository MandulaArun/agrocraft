<?php
include("../Functions/functions.php");

$sessionPhone = $_SESSION['phonenumber'] ?? null;
$isLoggedIn = isset($sessionPhone);
$farmerId = null;
$farmerName = $isLoggedIn ? "Farmer" : "Guest Farmer";
$locationLabel = "India";
$weatherLocation = "India";
$locationNote = "";
$farmerStats = [
    'listings' => 0,
    'stock' => 0,
    'orders' => 0,
    'revenue' => 0.0
];
$recentProducts = [];
$weatherApiKey = getenv('OPENWEATHER_KEY') ?: 'ba3b175573bb03c751fd5c95f9f08f99';
$farmerBaseUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

if ($isLoggedIn) {
    $sessPhone = intval($sessionPhone);
    $farmerQuery = "SELECT farmer_id, farmer_name, farmer_state, farmer_district FROM farmerregistration WHERE farmer_phone = $sessPhone LIMIT 1";

    if ($farmerResult = mysqli_query($con, $farmerQuery)) {
        if ($farmerRow = mysqli_fetch_assoc($farmerResult)) {
            $farmerId = (int) $farmerRow['farmer_id'];
            $farmerName = $farmerRow['farmer_name'];
            $state = trim((string) $farmerRow['farmer_state']);
            $district = trim((string) $farmerRow['farmer_district']);

            $locationParts = array_filter(
                [$district, $state, "India"],
                function ($value) {
                    $value = strtolower((string) $value);
                    return !empty($value) && $value !== "select district" && $value !== "0";
                }
            );

            if (!empty($locationParts)) {
                $locationLabel = implode(", ", array_unique(array_map('ucwords', $locationParts)));
                $weatherLocation = $locationLabel;
            } else {
                $locationNote = "Add your state and district in your profile to get hyper-local weather updates.";
            }
        }
        mysqli_free_result($farmerResult);
    }

    if ($farmerId) {
        $productStatsQuery = "SELECT COUNT(*) AS total_products, COALESCE(SUM(product_stock),0) AS total_stock FROM products WHERE farmer_fk = $farmerId";
        if ($productStatsResult = mysqli_query($con, $productStatsQuery)) {
            if ($productStats = mysqli_fetch_assoc($productStatsResult)) {
                $farmerStats['listings'] = (int) $productStats['total_products'];
                $farmerStats['stock'] = (int) $productStats['total_stock'];
            }
            mysqli_free_result($productStatsResult);
        }

        $orderStatsQuery = "SELECT COUNT(*) AS order_count, COALESCE(SUM(total),0) AS revenue FROM orders WHERE product_id IN (SELECT product_id FROM products WHERE farmer_fk = $farmerId)";
        if ($orderStatsResult = mysqli_query($con, $orderStatsQuery)) {
            if ($orderStats = mysqli_fetch_assoc($orderStatsResult)) {
                $farmerStats['orders'] = (int) $orderStats['order_count'];
                $farmerStats['revenue'] = (float) $orderStats['revenue'];
            }
            mysqli_free_result($orderStatsResult);
        }

        $recentProductsQuery = "SELECT product_id, product_title, product_type, product_price, product_stock, product_delivery, product_image FROM products WHERE farmer_fk = $farmerId ORDER BY product_id DESC LIMIT 4";
        if ($recentProductsResult = mysqli_query($con, $recentProductsQuery)) {
            while ($productRow = mysqli_fetch_assoc($recentProductsResult)) {
                $recentProducts[] = $productRow;
            }
            mysqli_free_result($recentProductsResult);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard | AgroCraft</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-green: #1f8a5c;
            --brand-green-dark: #126140;
            --brand-amber: #f4b000;
            --muted: #6b7280;
            --bg: #f5f7fb;
            --card-bg: #ffffff;
            --card-border: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: #0f172a;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .dashboard-header {
            padding: 24px clamp(16px, 4vw, 64px);
            background: linear-gradient(135deg, #dff7ec, #f0fff7);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .brand img {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            object-fit: contain;
            background: white;
            padding: 10px;
            box-shadow: 0 8px 20px rgba(31, 138, 92, 0.15);
        }

        .brand h1 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2.25rem);
            color: var(--brand-green-dark);
        }

        .brand .eyebrow {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .text-link {
            font-weight: 500;
            color: var(--brand-green);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 999px;
            font-weight: 600;
            border: 2px solid transparent;
            transition: transform 150ms ease, box-shadow 150ms ease;
        }

        .btn.primary {
            background: var(--brand-green);
            color: #fff;
            box-shadow: 0 10px 20px rgba(31, 138, 92, 0.25);
        }

        .btn.ghost {
            border-color: var(--brand-green);
            color: var(--brand-green);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .quick-nav {
            background: #fff;
            border-bottom: 1px solid var(--card-border);
            padding: 0 clamp(16px, 4vw, 64px);
            display: flex;
            overflow-x: auto;
            gap: 18px;
        }

        .quick-nav a {
            padding: 16px 0;
            font-weight: 500;
            color: var(--muted);
            border-bottom: 3px solid transparent;
            white-space: nowrap;
        }

        .quick-nav a.active {
            color: var(--brand-green-dark);
            border-color: var(--brand-green);
        }

        main {
            padding: clamp(16px, 4vw, 64px);
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .hero-card {
            background: linear-gradient(135deg, #1f8a5c, #1a6f4a);
            color: #fff;
            border-radius: 24px;
            padding: clamp(24px, 4vw, 40px);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            box-shadow: 0 25px 60px rgba(18, 97, 64, 0.35);
        }

        .hero-card h2 {
            margin: 8px 0 12px;
            font-size: clamp(1.5rem, 3vw, 2.5rem);
        }

        .hero-card p {
            margin: 0;
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 16px;
        }

        .hero-glance {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 18px;
            padding: 20px;
        }

        .hero-glance h3 {
            margin: 8px 0 0;
            font-size: 1.6rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }

        .stat-card .label {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .stat-card h3 {
            margin: 10px 0;
            font-size: 2rem;
            color: var(--brand-green-dark);
        }

        .stat-card .trend {
            color: var(--brand-green);
            font-weight: 600;
        }

        .dual-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 24px;
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }

        .card h3 {
            margin: 0 0 12px;
        }

        .weather-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .weather-main {
            text-align: center;
        }

        .weather-main i {
            font-size: 2.5rem;
            color: var(--brand-amber);
        }

        .weather-main .temp {
            font-size: 3rem;
            margin: 12px 0 4px;
            color: var(--brand-green-dark);
        }

        .weather-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(120px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .weather-meta span {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .note {
            background: #fff7e5;
            border-radius: 12px;
            padding: 12px;
            font-size: 0.9rem;
            color: #92400e;
            margin-bottom: 12px;
        }

        .updates-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .updates-card li {
            padding: 12px 14px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px dashed #d3dae6;
        }

        .recent-products .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 18px;
        }

        .recent-products .section-head h3 {
            margin: 0;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
        }

        .product-card {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--card-border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .product-card .content {
            padding: 18px;
            flex: 1;
        }

        .product-card h4 {
            margin: 0 0 6px;
            font-size: 1.1rem;
        }

        .product-card .meta {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .product-card .price {
            font-weight: 700;
            margin: 12px 0;
            color: var(--brand-green-dark);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .feature-card {
            background: #0f172a;
            color: #fff;
            border-radius: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.4);
        }

        .feature-card i {
            font-size: 1.5rem;
            color: var(--brand-amber);
        }

        footer {
            padding: 32px clamp(16px, 4vw, 64px);
            background: #0f172a;
            color: #cbd5f5;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: space-between;
        }

        footer a {
            color: #fff;
            font-weight: 600;
        }

        .loading,
        .error {
            font-size: 0.95rem;
            color: var(--muted);
        }

        .error {
            color: #b91c1c;
            background: #fee2e2;
            padding: 10px 14px;
            border-radius: 12px;
        }

        .empty-state {
            padding: 24px;
            border-radius: 18px;
            border: 1px dashed var(--card-border);
            text-align: center;
            background: #fff;
        }

        @media (max-width: 640px) {
            .header-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    <header class="dashboard-header">
        <div class="brand">
            <img src="agro.png" alt="AgroCraft logo">
            <div>
                <p class="eyebrow">Farmer Workspace</p>
                <h1>Welcome back, <?php echo htmlspecialchars($farmerName); ?>!</h1>
                <p>Track performance, understand demand and ship confidently.</p>
            </div>
        </div>
        <div class="header-actions">
            <?php if ($isLoggedIn) { ?>
                <a class="text-link" href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/FarmerProfile.php"><i class="fas fa-user"></i> Profile</a>
                <a class="btn primary" href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php } else { ?>
                <a class="btn primary" href="../auth/FarmerLogin.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php } ?>
        </div>
    </header>

    <nav class="quick-nav">
        <a href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/farmerHomepage.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/MyProducts.php"><i class="fas fa-leaf"></i> My Products</a>
        <a href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/InsertProduct.php"><i class="fas fa-plus-circle"></i> Add Product</a>
        <a href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/Transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a>
        <a href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/Orders.php"><i class="fas fa-shopping-basket"></i> Orders</a>
        
        <a href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/CallCenter.php"><i class="fas fa-phone"></i> Call Center</a>
    </nav>

    <main>
        <section class="hero-card">
            <div>
                <p class="eyebrow">Today</p>
                <h2>Sell smarter with actionable insights.</h2>
                <p>Keep your catalogue fresh, respond to buyer messages faster and plan harvests with confidence.</p>
                <div class="hero-buttons">
                    <a class="btn primary" href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/InsertProduct.php"><i class="fas fa-plus"></i> Add New Product</a>
                    <a class="btn ghost" href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/MyProducts.php"><i class="fas fa-boxes"></i> Manage Listings</a>
                </div>
            </div>
            <div class="hero-glance">
                <p>Serving region</p>
                <h3><?php echo htmlspecialchars($locationLabel); ?></h3>
                <div class="hero-buttons" style="margin-top:12px">
                    <a class="btn ghost" href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/Transactions.php"><i class="fas fa-exchange-alt"></i> View Transactions</a>
                </div>
            </div>
        </section>

        <section class="stats-grid">
            <article class="stat-card">
                <p class="label">Active Listings</p>
                <h3><?php echo number_format($farmerStats['listings']); ?></h3>
                <p class="trend"><?php echo number_format($farmerStats['stock']); ?> kg in stock</p>
            </article>
            <article class="stat-card">
                <p class="label">Orders fulfilled</p>
                <h3><?php echo number_format($farmerStats['orders']); ?></h3>
                <p class="trend">Keep your promise times consistent</p>
            </article>
            <article class="stat-card">
                <p class="label">Revenue generated</p>
                <h3>₹<?php echo number_format($farmerStats['revenue'], 2); ?></h3>
                <p class="trend">Across confirmed transactions</p>
            </article>
            <article class="stat-card">
                <p class="label">Quick tip</p>
                <h3><i class="fas fa-bolt"></i> 24h</h3>
                <p class="trend">Respond to enquiries within a day to win repeat buyers.</p>
            </article>
        </section>

        <section class="dual-grid">
            <article class="card weather-card">
                <div class="section-head">
                    <h3><i class="fas fa-cloud-sun"></i> Weather outlook</h3>
                    <span><?php echo htmlspecialchars($locationLabel); ?></span>
                </div>
                <?php if ($locationNote) { ?>
                    <p class="note"><?php echo htmlspecialchars($locationNote); ?></p>
                <?php } ?>
                <div id="farmer-weather-loading" class="loading">Fetching live weather data...</div>
                <div id="farmer-weather-body" class="weather-body" hidden>
                    <div class="weather-main">
                        <i id="farmer-icon" class="fas fa-cloud"></i>
                        <div class="temp" id="farmer-temp">--°C</div>
                        <div id="farmer-description">---</div>
                    </div>
                    <div class="weather-meta">
                        <div>
                            <strong id="farmer-feels">--°C</strong>
                            <span>Feels like</span>
                        </div>
                        <div>
                            <strong id="farmer-humidity">--%</strong>
                            <span>Humidity</span>
                        </div>
                        <div>
                            <strong id="farmer-wind">-- km/h</strong>
                            <span>Wind</span>
                        </div>
                        <div>
                            <strong id="farmer-pressure">-- hPa</strong>
                            <span>Pressure</span>
                        </div>
                    </div>
                </div>
                <div id="farmer-weather-error" class="error" hidden>
                    Unable to load weather right now. Please retry in a minute.
                </div>
            </article>
            <article class="card updates-card">
                <h3><i class="fas fa-seedling"></i> Next best actions</h3>
                <ul>
                    <li><strong>Keep catalog fresh:</strong> aim for at least 3 live products to stay in buyer recommendations.</li>
                    <li><strong>Boost response rate:</strong> acknowledge enquiries within 2 hours for priority placement.</li>
                    <li><strong>Highlight delivery options:</strong> update each listing with accurate logistics info.</li>
                </ul>
            </article>
        </section>
    <script>
        const farmerWeatherConfig = {
            prefix: "farmer",
            location: "<?php echo addslashes($weatherLocation); ?>",
            apiKey: "<?php echo htmlspecialchars($weatherApiKey, ENT_QUOTES); ?>",
            fallback: "Delhi, India"
        };

        const weatherIcons = {
            "clear": "fa-sun",
            "cloud": "fa-cloud",
            "rain": "fa-cloud-showers-heavy",
            "drizzle": "fa-cloud-rain",
            "thunderstorm": "fa-bolt",
            "snow": "fa-snowflake",
            "mist": "fa-smog",
            "fog": "fa-smog",
            "haze": "fa-smog"
        };

        function pickIcon(description) {
            const key = description.toLowerCase();
            for (const fragment in weatherIcons) {
                if (key.includes(fragment)) {
                    return weatherIcons[fragment];
                }
            }
            return weatherIcons.cloud;
        }

        function initWeatherWidget(config) {
            if (!config.apiKey) {
                showWeatherError(config.prefix);
                return;
            }
            const loading = document.getElementById(`${config.prefix}-weather-loading`);
            const body = document.getElementById(`${config.prefix}-weather-body`);
            const error = document.getElementById(`${config.prefix}-weather-error`);
            const base = `https://api.openweathermap.org/data/2.5/weather?appid=${config.apiKey}&units=metric`;
            const primaryUrl = `${base}&q=${encodeURIComponent(config.location)}`;
            const fallbackUrl = `${base}&q=${encodeURIComponent(config.fallback || 'India')}`;

            fetch(primaryUrl)
                .then((response) => response.json())
                .then((data) => {
                    if (data.cod !== 200) {
                        return fetch(fallbackUrl).then((r) => r.json());
                    }
                    return data;
                })
                .then((data) => {
                    if (data && data.cod === 200) {
                        loading.hidden = true;
                        body.hidden = false;
                        error.hidden = true;
                        document.getElementById(`${config.prefix}-temp`).textContent = `${Math.round(data.main.temp)}°C`;
                        document.getElementById(`${config.prefix}-description`).textContent = data.weather[0].description;
                        document.getElementById(`${config.prefix}-icon`).className = `fas ${pickIcon(data.weather[0].description)}`;
                        document.getElementById(`${config.prefix}-feels`).textContent = `${Math.round(data.main.feels_like)}°C`;
                        document.getElementById(`${config.prefix}-humidity`).textContent = `${data.main.humidity}%`;
                        document.getElementById(`${config.prefix}-wind`).textContent = `${Math.round(data.wind.speed * 3.6)} km/h`;
                        document.getElementById(`${config.prefix}-pressure`).textContent = `${data.main.pressure} hPa`;
                    } else {
                        showWeatherError(config.prefix);
                    }
                })
                .catch(() => {
                    showWeatherError(config.prefix);
                });
        }

        function showWeatherError(prefix) {
            const loading = document.getElementById(`${prefix}-weather-loading`);
            const body = document.getElementById(`${prefix}-weather-body`);
            const error = document.getElementById(`${prefix}-weather-error`);
            if (loading) loading.hidden = true;
            if (body) body.hidden = true;
            if (error) error.hidden = false;
        }

        document.addEventListener("DOMContentLoaded", () => {
            initWeatherWidget(farmerWeatherConfig);
        });
    </script>
        <section class="recent-products">
            <div class="section-head">
                <h3>Recent listings</h3>
                <?php if ($isLoggedIn) { ?>
                    <a class="text-link" href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/MyProducts.php">View all</a>
                <?php } ?>
            </div>

            <?php if (!empty($recentProducts)) { ?>
                <div class="product-grid">
                    <?php foreach ($recentProducts as $product) { ?>
                        <article class="product-card">
                            <img src="<?php echo "../Admin/product_images/" . htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_title']); ?>" onerror="this.src='../Images/Website/noimage.jpg';">
                            <div class="content">
                                <h4><?php echo htmlspecialchars($product['product_title']); ?></h4>
                                <div class="meta"><?php echo htmlspecialchars($product['product_type']); ?> • Stock: <?php echo number_format($product['product_stock']); ?> kg</div>
                                <div class="price">₹<?php echo number_format($product['product_price']); ?>/kg</div>
                                <a class="text-link" href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/EditProduct.php?id=<?php echo (int) $product['product_id']; ?>"><i class="fas fa-edit"></i> Update listing</a>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="empty-state">
                    <p>No listings yet. Add your first product to start receiving enquiries.</p>
                    <a class="btn primary" href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/InsertProduct.php">Create a listing</a>
                </div>
            <?php } ?>
        </section>

        <section class="feature-grid">
            <article class="feature-card">
                <i class="fas fa-sms"></i>
                <h4>SMS Product Updates</h4>
                <p>Update prices or inventory via text for quick changes on low bandwidth days.</p>
            </article>
            <article class="feature-card">
                <i class="fas fa-users"></i>
                <h4>Buyer Network</h4>
                <p>Connect directly with verified buyers, share harvest plans and lock-in pre-orders.</p>
            </article>
            <article class="feature-card">
                <i class="fas fa-hand-holding-heart"></i>
                <h4>Community Support</h4>
                <p>Access tutorials, call center support and agronomy tips curated for your region.</p>
            </article>
        </section>
    </main>

    <footer>
        <div>
            <strong>Need help?</strong><br>
            <a href="<?php echo htmlspecialchars($farmerBaseUrl); ?>/CallCenter.php"><i class="fas fa-phone"></i> Reach call center</a>
        </div>
        <div>
            <strong>Write to us</strong><br>
            <a href="mailto:agrocraft6@gmail.com"><i class="fas fa-envelope"></i> agrocraft6@gmail.com</a>
        </div>
        <div>
            <strong>Follow AgroCraft</strong><br>
            <a href="#"><i class="fab fa-instagram"></i> @AgroCraft</a>
        </div>
    </footer>

    
</body>

</html>

