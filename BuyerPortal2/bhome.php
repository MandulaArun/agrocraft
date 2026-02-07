<?php
include("../Functions/functions.php");

$sessionPhone = $_SESSION['phonenumber'] ?? null;
$isLoggedIn = isset($sessionPhone);
$buyerName = $isLoggedIn ? "Buyer" : "Guest Buyer";
$buyerAddress = "";
$weatherLocation = "India";
$locationNote = "";
$favoriteProduct = "All produce";
$buyerStats = [
    'orders' => 0,
    'cart' => 0,
    'spend' => 0.0,
    'farmers' => 0
];
$recentPurchases = [];
$weatherApiKey = getenv('OPENWEATHER_KEY') ?: 'ba3b175573bb03c751fd5c95f9f08f99';
$buyerBaseUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

if ($isLoggedIn) {
    $sessPhone = intval($sessionPhone);
    $buyerQuery = "SELECT buyer_name, buyer_addr FROM buyerregistration WHERE buyer_phone = $sessPhone LIMIT 1";

    if ($buyerResult = mysqli_query($con, $buyerQuery)) {
        if ($buyerRow = mysqli_fetch_assoc($buyerResult)) {
            $buyerName = $buyerRow['buyer_name'];
            $buyerAddress = trim((string) $buyerRow['buyer_addr']);
            if (!empty($buyerAddress)) {
                $weatherLocation = $buyerAddress;
            } else {
                $locationNote = "Add your address in profile to tailor live weather to your delivery location.";
            }
        }
        mysqli_free_result($buyerResult);
    }

    $cartQuery = "SELECT COALESCE(SUM(qty), COUNT(*)) AS total_qty FROM cart WHERE phonenumber = $sessPhone";
    if ($cartResult = mysqli_query($con, $cartQuery)) {
        if ($cartRow = mysqli_fetch_assoc($cartResult)) {
            $buyerStats['cart'] = (int) $cartRow['total_qty'];
        }
        mysqli_free_result($cartResult);
    }

    $ordersQuery = "SELECT COUNT(*) AS orders, COALESCE(SUM(total),0) AS spend FROM orders WHERE buyer_phonenumber = $sessPhone";
    if ($ordersResult = mysqli_query($con, $ordersQuery)) {
        if ($ordersRow = mysqli_fetch_assoc($ordersResult)) {
            $buyerStats['orders'] = (int) $ordersRow['orders'];
            $buyerStats['spend'] = (float) $ordersRow['spend'];
        }
        mysqli_free_result($ordersResult);
    }

    $farmersQuery = "SELECT COUNT(DISTINCT p.farmer_fk) AS farmers FROM orders o JOIN products p ON o.product_id = p.product_id WHERE o.buyer_phonenumber = $sessPhone";
    if ($farmersResult = mysqli_query($con, $farmersQuery)) {
        if ($farmersRow = mysqli_fetch_assoc($farmersResult)) {
            $buyerStats['farmers'] = (int) $farmersRow['farmers'];
        }
        mysqli_free_result($farmersResult);
    }

    $favoriteQuery = "SELECT p.product_type, COUNT(*) AS total FROM orders o JOIN products p ON o.product_id = p.product_id WHERE o.buyer_phonenumber = $sessPhone GROUP BY p.product_type ORDER BY total DESC LIMIT 1";
    if ($favoriteResult = mysqli_query($con, $favoriteQuery)) {
        if ($favoriteRow = mysqli_fetch_assoc($favoriteResult)) {
            $favoriteProduct = $favoriteRow['product_type'];
        }
        mysqli_free_result($favoriteResult);
    }

    $recentPurchasesQuery = "SELECT p.product_title, p.product_type, o.qty, o.total, o.order_id FROM orders o JOIN products p ON o.product_id = p.product_id WHERE o.buyer_phonenumber = $sessPhone ORDER BY o.order_id DESC LIMIT 3";
    if ($recentResult = mysqli_query($con, $recentPurchasesQuery)) {
        while ($purchase = mysqli_fetch_assoc($recentResult)) {
            $recentPurchases[] = $purchase;
        }
        mysqli_free_result($recentResult);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Marketplace | AgroCraft</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-green: #1f8a5c;
            --brand-blue: #2563eb;
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

        .text-link {
            color: var(--brand-blue);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        header.dashboard-header {
            padding: 24px clamp(16px, 4vw, 64px);
            background: linear-gradient(135deg, #e0f7ff, #f5fff9);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand img {
            width: 68px;
            height: 68px;
            border-radius: 16px;
            padding: 8px;
            background: white;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.15);
        }

        .brand h1 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2rem);
            color: var(--brand-green);
        }

        .search-bar {
            display: flex;
            align-items: stretch;
            background: #fff;
            border-radius: 999px;
            padding: 6px;
            border: 1px solid var(--card-border);
        }

        .search-bar input {
            flex: 1;
            border: none;
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 1rem;
        }

        .search-bar button {
            border: none;
            background: var(--brand-blue);
            color: #fff;
            border-radius: 999px;
            padding: 0 20px;
            font-weight: 600;
            cursor: pointer;
        }

        .header-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .icon-btn {
            position: relative;
            padding: 12px 16px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--card-border);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .badge {
            background: var(--brand-blue);
            color: #fff;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 0.85rem;
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
        }

        .btn.primary {
            background: var(--brand-green);
            color: #fff;
        }

        .btn.ghost {
            color: var(--brand-green);
            border-color: var(--brand-green);
        }

        nav.quick-nav {
            background: #fff;
            border-bottom: 1px solid var(--card-border);
            padding: 0 clamp(16px, 4vw, 64px);
            display: flex;
            overflow-x: auto;
            gap: 18px;
        }

        nav.quick-nav a {
            padding: 16px 0;
            font-weight: 500;
            color: var(--muted);
            border-bottom: 3px solid transparent;
            white-space: nowrap;
        }

        nav.quick-nav a.active {
            color: var(--brand-blue);
            border-color: var(--brand-blue);
        }

        main {
            padding: clamp(16px, 4vw, 64px);
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .hero-card {
            background: linear-gradient(135deg, #2563eb, #1f8a5c);
            color: #fff;
            border-radius: 24px;
            padding: clamp(24px, 4vw, 40px);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            box-shadow: 0 25px 60px rgba(37, 99, 235, 0.35);
        }

        .hero-card .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.85rem;
        }

        .hero-card h2 {
            margin: 10px 0;
            font-size: clamp(1.6rem, 3vw, 2.4rem);
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
            margin: 8px 0 4px;
            font-size: 1.8rem;
            color: #fff;
        }

        .hero-glance p {
            margin: 0;
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-glance {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 18px;
            padding: 20px;
        }

        .hero-glance h3 {
            margin: 8px 0;
            font-size: 1.7rem;
        }

        .hero-glance p {
            margin: 0;
            color: rgba(255, 255, 255, 0.9);
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
            color: var(--brand-blue);
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

        .note {
            background: #fff7e5;
            border-radius: 12px;
            padding: 12px;
            font-size: 0.9rem;
            color: #92400e;
            margin-bottom: 12px;
        }

        .weather-grid {
            display: grid;
            gap: 16px;
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
            margin: 12px 0;
            color: var(--brand-blue);
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

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .activity-item {
            padding: 12px 14px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px dashed #d3dae6;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        details.filter-chip {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            padding: 8px 16px;
            position: relative;
        }

        details.filter-chip summary {
            cursor: pointer;
            list-style: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        details.filter-chip[open] .filter-menu {
            display: flex;
        }

        .filter-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            background: #fff;
            border: 1px solid var(--card-border);
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.15);
            border-radius: 16px;
            padding: 16px;
            display: none;
            flex-direction: column;
            gap: 8px;
            max-height: 250px;
            overflow-y: auto;
            min-width: 220px;
            z-index: 10;
        }

        .filter-menu a {
            color: var(--brand-green);
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 18px;
        }

        .section-head h3 {
            margin: 0;
        }

        .product-gallery {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 24px;
            border: 1px solid var(--card-border);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .column.kolum .img-thumbnail {
            border: none;
            padding: 0;
            background: transparent;
        }

        .column.kolum img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 18px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            transition: transform 150ms ease;
        }

        .column.kolum img:hover {
            transform: translateY(-4px);
        }

        .best-products {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 24px;
            border: 1px solid var(--card-border);
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

        @media (max-width: 640px) {
            header.dashboard-header {
                grid-template-columns: 1fr;
            }

            .header-actions {
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
                <p class="eyebrow">Buyer workspace</p>
                <h1>Welcome, <?php echo htmlspecialchars($buyerName); ?>!</h1>
                <small>Curated marketplace for fresh produce</small>
            </div>
        </div>
        <form class="search-bar" action="<?php echo htmlspecialchars($buyerBaseUrl); ?>/SearchResult.php" method="get">
            <input type="text" name="search" placeholder="Search for fruits, vegetables or crops">
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
        <div class="header-actions">
            <a class="icon-btn" href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/CartPage.php">
                <i class="fas fa-shopping-cart"></i> Cart
                <span class="badge"><?php echo number_format($buyerStats['cart']); ?></span>
            </a>
            <?php if ($isLoggedIn) { ?>
                <a class="icon-btn" href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/BuyerProfile.php"><i class="fas fa-user"></i> Profile</a>
                <a class="btn primary" href="../Includes/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php } else { ?>
                <a class="btn primary" href="../auth/BuyerLogin.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php } ?>
        </div>
    </header>

    <nav class="quick-nav">
        <a href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/bhome.php" class="active"><i class="fas fa-home"></i> Marketplace</a>
        <a href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/Categories.php"><i class="fas fa-tags"></i> Categories</a>
        <a href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/Transaction.php"><i class="fas fa-receipt"></i> Transactions</a>
        <a href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/farmers.php"><i class="fas fa-user-friends"></i> Farmers</a>
    </nav>

    <main>
        <section class="hero-card">
            <div>
                <p class="eyebrow">Hi <?php echo htmlspecialchars($buyerName); ?>,</p>
                <h2>Source fresh produce directly from trusted farmers.</h2>
                <p>Track orders, manage procurement and stay in sync with real-time farm weather.</p>
                <div class="hero-buttons">
                    <a class="btn primary" href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/Categories.php"><i class="fas fa-leaf"></i> Explore products</a>
                    <a class="btn ghost" href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/farmers.php"><i class="fas fa-users"></i> Meet farmers</a>
                </div>
            </div>
            <div class="hero-glance">
                <p>Favourite product</p>
                <h3><?php echo htmlspecialchars($favoriteProduct); ?></h3>
                <p>You have sourced from <?php echo number_format($buyerStats['farmers']); ?> different farmers.</p>
            </div>
        </section>

        <section class="stats-grid">
            <article class="stat-card">
                <p class="label">Orders placed</p>
                <h3><?php echo number_format($buyerStats['orders']); ?></h3>
                <p class="trend">Keep the momentum going</p>
            </article>
            <article class="stat-card">
                <p class="label">Items in cart</p>
                <h3><?php echo number_format($buyerStats['cart']); ?></h3>
                <p class="trend">Finalize checkout anytime</p>
            </article>
            <article class="stat-card">
                <p class="label">Spend to date</p>
                <h3>₹<?php echo number_format($buyerStats['spend'], 2); ?></h3>
                <p class="trend">Across confirmed orders</p>
            </article>
            <article class="stat-card">
                <p class="label">Farmer partners</p>
                <h3><?php echo number_format($buyerStats['farmers']); ?></h3>
                <p class="trend">Growing network</p>
            </article>
        </section>

        <section class="dual-grid">
            <article class="card">
                <div class="section-head">
                    <h3><i class="fas fa-cloud-sun"></i> Weather insights</h3>
                    <span><?php echo htmlspecialchars($weatherLocation); ?></span>
                </div>
                <?php if ($locationNote) { ?>
                    <p class="note"><?php echo htmlspecialchars($locationNote); ?></p>
                <?php } ?>
                <div id="buyer-weather-loading" class="loading">Fetching location weather...</div>
                <div id="buyer-weather-body" class="weather-grid" hidden>
                    <div class="weather-main">
                        <i id="buyer-icon" class="fas fa-cloud"></i>
                        <div class="temp" id="buyer-temp">--°C</div>
                        <div id="buyer-description">---</div>
                    </div>
                    <div class="weather-meta">
                        <div>
                            <strong id="buyer-feels">--°C</strong>
                            <span>Feels like</span>
                        </div>
                        <div>
                            <strong id="buyer-humidity">--%</strong>
                            <span>Humidity</span>
                        </div>
                        <div>
                            <strong id="buyer-wind">-- km/h</strong>
                            <span>Wind</span>
                        </div>
                        <div>
                            <strong id="buyer-pressure">-- hPa</strong>
                            <span>Pressure</span>
                        </div>
                    </div>
                </div>
                <div id="buyer-weather-error" class="error" hidden>
                    Unable to load weather right now. Please refresh later.
                </div>
            </article>
            <article class="card">
                <div class="section-head">
                    <h3><i class="fas fa-stream"></i> Recent activity</h3>
                    <span>Latest 3 orders</span>
                </div>
                <?php if (!empty($recentPurchases)) { ?>
                    <ul class="activity-list">
                        <?php foreach ($recentPurchases as $purchase) { ?>
                            <li class="activity-item">
                                <strong><?php echo htmlspecialchars($purchase['product_title']); ?></strong>
                                <div><?php echo htmlspecialchars($purchase['product_type']); ?> • Qty: <?php echo number_format($purchase['qty']); ?> • ₹<?php echo number_format($purchase['total']); ?></div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p class="loading">Place your first order to see updates here.</p>
                <?php } ?>
            </article>
        </section>

        <section class="filters">
            <details class="filter-chip">
                <summary><i class="fas fa-apple-alt"></i> Fruits</summary>
                <div class="filter-menu">
                    <?php getFruits(); ?>
                </div>
            </details>
            <details class="filter-chip">
                <summary><i class="fas fa-carrot"></i> Vegetables</summary>
                <div class="filter-menu">
                    <?php getVegetables(); ?>
                </div>
            </details>
            <details class="filter-chip">
                <summary><i class="fas fa-seedling"></i> Crops</summary>
                <div class="filter-menu">
                    <?php getCrops(); ?>
                </div>
            </details>
        </section>

        <section class="product-gallery">
            <div class="section-head">
                <h3>Fresh fruits</h3>
                <a class="text-link" href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/Categories.php"><i class="fas fa-chevron-right"></i> View all</a>
            </div>
            <div class="gallery-grid">
                <?php getFruitsHomepage(); ?>
            </div>
        </section>

        <section class="product-gallery">
            <div class="section-head">
                <h3>Fresh vegetables</h3>
                <a class="text-link" href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/Categories.php"><i class="fas fa-chevron-right"></i> View all</a>
            </div>
            <div class="gallery-grid">
                <?php getVegetablesHomepage(); ?>
            </div>
        </section>

        <section class="product-gallery">
            <div class="section-head">
                <h3>Fresh crops</h3>
                <a class="text-link" href="<?php echo htmlspecialchars($buyerBaseUrl); ?>/Categories.php"><i class="fas fa-chevron-right"></i> View all</a>
            </div>
            <div class="gallery-grid">
                <?php getCropsHomepage(); ?>
            </div>
        </section>

        
    </main>
    <script>
        const buyerWeatherConfig = {
            prefix: "buyer",
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
            initWeatherWidget(buyerWeatherConfig);
        });
    </script>
    <footer>
        <div>
            <strong>Need help?</strong><br>
            <a href="mailto:agrocraft6@gmail.com"><i class="fas fa-envelope"></i> agrocraft6@gmail.com</a>
        </div>
        <div>
            <strong>Buyer hotline</strong><br>
            <a href="tel:+919008951931"><i class="fas fa-phone"></i> +91 90089 51931</a>
        </div>
        <div>
            <strong>Stay updated</strong><br>
            <a href="#"><i class="fab fa-instagram"></i> @AgroCraft</a>
        </div>
    </footer>

    
</body>

</html>

