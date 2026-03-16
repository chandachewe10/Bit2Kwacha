<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{env('APP_NAME')}} - Instant Bitcoin & Kwacha Exchange</title>
    <meta content="Convert Bitcoin to Kwacha instantly or buy Bitcoin with Kwacha. Fast, secure, no account required."
        name="description">
    <meta name="keywords"
        content="{{env('APP_NAME')}}, Bitcoin Zambia, Lightning Network, crypto payments, Bitcoin exchange Zambia">

    <!-- Favicons -->
    <link href="{{asset('ui/css/assets/img/fav.png')}}" rel="icon" type="image/png">
    <link href="{{asset('ui/css/assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon" sizes="180x180">
    <link href="{{asset('ui/css/assets/img/fav.png')}}" rel="icon" type="image/png" sizes="32x32">
    <link href="{{asset('ui/css/assets/img/fav.png')}}" rel="icon" type="image/png" sizes="16x16">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{url('/')}}">
    <meta property="og:title" content="{{env('APP_NAME')}} - Instant Bitcoin & Kwacha Exchange">
    <meta property="og:description"
        content="Convert Bitcoin to Kwacha instantly or buy Bitcoin with Kwacha. Fast, secure, no account required.">
    <meta property="og:image" content="{{url(asset('ui/css/assets/img/logo.png'))}}">
    <meta property="og:image:secure_url" content="{{url(asset('ui/css/assets/img/logo.png'))}}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:site_name" content="{{env('APP_NAME')}}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{url('/')}}">
    <meta name="twitter:title" content="{{env('APP_NAME')}} - Instant Bitcoin & Kwacha Exchange">
    <meta name="twitter:description"
        content="Convert Bitcoin to Kwacha instantly or buy Bitcoin with Kwacha. Fast, secure, no account required.">
    <meta name="twitter:image" content="{{url(asset('ui/css/assets/img/logo.png'))}}">

    <!-- LinkedIn -->
    <meta property="og:locale" content="en_US">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap -->
    <link href="{{asset('ui/css/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('ui/css/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">

    <style>
        :root {
            --primary: #F7931A;
            --primary-dark: #E8841A;
            --primary-light: #FFA64D;
            --secondary: #FFB84D;
            --success: #10b981;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-dark);
            background: var(--bg-light);
            line-height: 1.6;
        }

        /* Header */
        .navbar {
            background: var(--white);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-dark);
        }

        .logo img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #F7931A 0%, #FFA64D 100%);
            color: white;
            padding: 100px 0 60px;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.25rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        /* Conversion Section */
        .conversion-section {
            padding: 60px 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        .conversion-tabs {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 1rem 2rem;
            background: white;
            border: 2px solid var(--border);
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .tab-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 6px rgba(247, 147, 26, 0.3);
        }

        .tab-btn:hover:not(.active) {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(247, 147, 26, 0.1);
        }

        .conversion-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            display: none;
        }

        .conversion-card.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .card-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .card-header p {
            color: var(--text-gray);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 0.75rem;
            font-size: 1.125rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .input-suffix {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
            font-weight: 600;
        }

        .swap-icon {
            text-align: center;
            margin: 1rem 0;
            font-size: 1.5rem;
            color: var(--primary);
            opacity: 0.8;
        }

        .calculation-box {
            background: var(--bg-light);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .calc-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .calc-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--primary);
            margin-top: 0.5rem;
            padding-top: 1rem;
            border-top: 2px solid var(--primary);
        }

        .calc-row:last-child span:last-child {
            color: var(--primary);
        }

        .btn-primary {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1.125rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1.5rem;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(247, 147, 26, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .info-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(247, 147, 26, 0.1);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        /* Features Section */
        .features {
            padding: 60px 0;
            background: white;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .feature-item {
            text-align: center;
            padding: 1.5rem;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .feature-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .feature-item h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .feature-item p {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        /* Footer */
        .footer {
            background: var(--text-dark);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            text-align: center;
        }

        .footer p {
            opacity: 0.8;
            margin-top: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .conversion-card {
                padding: 1.5rem;
            }

            .tab-btn {
                padding: 0.75rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        .loading {
            display: none;
            text-align: center;
            padding: 1rem;
        }

        .loading.active {
            display: block;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalSlideIn 0.3s;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-gray);
            padding: 0.5rem;
            line-height: 1;
        }

        .modal-close:hover {
            color: var(--text-dark);
        }

        .qr-code-container {
            text-align: center;
            margin: 1.5rem 0;
        }

        .qr-code-container img {
            max-width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .invoice-address {
            background: var(--bg-light);
            padding: 1rem;
            border-radius: 0.5rem;
            word-break: break-all;
            font-family: monospace;
            font-size: 0.875rem;
            margin: 1rem 0;
            border: 2px solid var(--border);
        }

        .copy-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            margin-top: 0.5rem;
            width: 100%;
        }

        .copy-btn:hover {
            background: var(--primary-dark);
        }

        .btn-loading {
            opacity: 0.7;
            cursor: not-allowed;
            position: relative;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .success-icon {
            width: 60px;
            height: 60px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .success-icon i {
            font-size: 2rem;
            color: white;
        }
    </style>
</head>

<body>
    @include('sweetalert::alert')

    <!-- Header -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">
                <img src="{{asset('ui/css/assets/img/logo.png')}}" alt="{{env('APP_NAME')}} Logo">
                <span>{{env('APP_NAME')}}</span>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Instant Bitcoin ↔ Kwacha Exchange</h1>
            <p>Convert between Bitcoin and Kwacha instantly. Fast, secure, no account required.</p>
        </div>
    </section>

    <!-- Conversion Section -->
    <section class="conversion-section">
        <div class="container">
            <div class="conversion-tabs">
                <button class="tab-btn active" onclick="switchTab('buy')">
                    <i class="bi bi-arrow-down-circle"></i> Buy Bitcoin (ZMW → BTC)
                </button>
                <button class="tab-btn" onclick="switchTab('sell')">
                    <i class="bi bi-arrow-up-circle"></i> Sell Bitcoin (BTC → ZMW)
                </button>
            </div>

            <!-- Buy Bitcoin Card -->
            <div id="buy-card" class="conversion-card active">
                <div class="card-header">
                    <h2>Buy Bitcoin with Kwacha</h2>
                    <p>Pay with mobile money or card, receive Bitcoin instantly</p>
                </div>

                <form id="buy-form" onsubmit="handleBuy(event)" action="{{ route('subscription.lenco') }}"
                    method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" id="buy-phone" class="form-control" placeholder="09XXXXXXXX"
                            required>

                    </div>

                    <div class="form-group">
                        <label class="form-label">Amount in Kwacha (ZMW)</label>
                        <div class="input-wrapper">
                            <input type="number" id="buy-amount" name="amount_kwacha" class="form-control"
                                placeholder="Enter amount" min="20" step="0.01" required oninput="calculateBuy()">
                            <span class="input-suffix">ZMW</span>
                        </div>
                    </div>

                    <div class="swap-icon">
                        <i class="bi bi-arrow-down"></i>
                    </div>

                    <div class="form-group">
                        <label class="form-label">You'll Receive</label>
                        <div class="input-wrapper">
                            <input type="text" id="buy-btc" class="form-control" readonly value="0.00000000">
                            <span class="input-suffix">BTC</span>
                        </div>
                        <div class="input-wrapper" style="margin-top: 0.75rem; opacity: 0.8;">
                            <input type="text" id="buy-sats-display" class="form-control" readonly value="0"
                                style="font-size: 0.95rem;">
                            <span class="input-suffix" style="font-size: 0.9rem;">SATS</span>
                        </div>
                    </div>

                    <div class="calculation-box" id="buy-calc" style="display: none;">
                        <div class="calc-row">
                            <span>Amount (ZMW):</span>
                            <span id="buy-amount-display">0.00</span>
                        </div>
                        <div class="calc-row">
                            <span>Service Fee:</span>
                            <span id="buy-fee-display">0.00 ZMW</span>
                        </div>
                        <div class="calc-row">
                            <span>Network Fee:</span>
                            <span>5.00 ZMW</span>
                        </div>
                        <div class="calc-row">
                            <span>Total to Pay:</span>
                            <span id="buy-total-display">0.00 ZMW</span>
                        </div>
                    </div>

                    <div class="info-badge">
                        <i class="bi bi-info-circle"></i>
                        <span>Rate: 1 ZMW = ~{{ number_format(1 / config('services.bitcoin.conversion_rate'), 2) }} SATS
                            | Min: 20 ZMW</span>
                    </div>

                    <input type="hidden" name="total_amount" id="buy-total">
                    <input type="hidden" name="amount_sats" id="buy-sats">
                    <input type="hidden" name="amount_btc" id="buy-btc-hidden">
                    <input type="hidden" name="conversion_fee" id="buy-fee">
                    <input type="hidden" name="network_fee" value="5">
                    <input type="hidden" name="type" value="buy">

                    <button type="submit" class="btn-primary" id="buy-btn">
                        Proceed to Payment <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>

            <!-- Sell Bitcoin Card -->
            <div id="sell-card" class="conversion-card">
                <div class="card-header">
                    <h2>Sell Bitcoin for Kwacha</h2>
                    <p>Send Bitcoin, receive Kwacha to your mobile money</p>
                </div>

                <form id="sell-form" onsubmit="handleSell(event)">
                    <div class="form-group">
                        <label class="form-label">Mobile Money Number</label>
                        <input type="tel" name="phone" class="form-control" placeholder="09XXXXXXXX" required>
                        <small
                            style="color: var(--text-gray); font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                            Phone number to recieve the mobile money payments
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Amount in Satoshis</label>
                        <div class="input-wrapper">
                            <input type="number" id="sell-sats" class="form-control" placeholder="Enter amount"
                                min="200" step="1" required oninput="calculateSell()">
                            <span class="input-suffix">SATS</span>
                        </div>
                    </div>

                    <div class="swap-icon">
                        <i class="bi bi-arrow-down"></i>
                    </div>

                    <div class="form-group">
                        <label class="form-label">You'll Receive</label>
                        <div class="input-wrapper">
                            <input type="text" id="sell-kwacha" class="form-control" readonly value="0.00">
                            <span class="input-suffix">ZMW</span>
                        </div>
                    </div>

                    <div class="calculation-box" id="sell-calc" style="display: none;">
                        <div class="calc-row">
                            <span>Amount (SATS):</span>
                            <span id="sell-sats-display">0</span>
                        </div>
                        <div class="calc-row">
                            <span>Service Fee:</span>
                            <span id="sell-fee-display">0 SATS</span>
                        </div>
                        <div class="calc-row">
                            <span>Network Fee:</span>
                            <span>400 SATS</span>
                        </div>
                        <div class="calc-row">
                            <span>Total SATS:</span>
                            <span id="sell-total-display">0 SATS</span>
                        </div>
                        <div class="calc-row">
                            <span>You'll Receive:</span>
                            <span id="sell-receive-display">0.00 ZMW</span>
                        </div>
                    </div>

                    <div class="info-badge">
                        <i class="bi bi-info-circle"></i>
                        <span>Rate: 1 SAT = {{ config('services.bitcoin.conversion_rate') }} ZMW | Min: 200 SATS</span>
                    </div>

                    <input type="hidden" name="amount_sats" id="sell-sats-hidden">
                    <input type="hidden" name="amount_btc" id="sell-btc">
                    <input type="hidden" name="amount_kwacha" id="sell-kwacha-hidden">
                    <input type="hidden" name="conversion_fee" id="sell-fee">
                    <input type="hidden" name="total_sats" id="sell-total">
                    <input type="hidden" name="network_fee" value="400">
                    <input type="hidden" name="type" value="sell">

                    <button type="submit" class="btn-primary" id="sell-btn">
                        Generate Lightning Invoice <i class="bi bi-lightning-charge"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <h3>Lightning Fast</h3>
                <p>Instant transactions using Lightning Network</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <h3>Secure</h3>
                <p>Enterprise-grade security and encryption</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-person-x"></i>
                </div>
                <h3>No Account Needed</h3>
                <p>Start trading immediately, no registration</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-currency-exchange"></i>
                </div>
                <h3>Low Fees</h3>
                <p>Transparent pricing with minimal fees</p>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div id="invoiceModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Lightning Invoice</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div id="modalBody">
                <!-- Content will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="logo" style="justify-content: center; color: white;">
                <img src="{{asset('ui/css/assets/img/logo.png')}}" alt="{{env('APP_NAME')}} Logo">
                <span>{{env('APP_NAME')}}</span>
            </div>
            <p>&copy; {{ date('Y') }} {{env('APP_NAME')}}. All Rights Reserved</p>
            <p style="margin-top: 0.5rem;">Olympia, 14 Zambezi road, Lusaka, LSK 10101 | info@bit2kwacha.info</p>
        </div>
    </footer>

    <script>
        // Global variables for exchange rates
        let liveConversionRate = {{ config('services.bitcoin.conversion_rate') }}; // Default fallback rate (SAT to ZMW)
        let btcUsdRate = null;
        const usdToZmwRate = 24; // Approximate USD to ZMW rate (can be made configurable)
        const useOpenNodeExchangeRate = @json(config('services.bitcoin.use_open_node_exchange_rate'));

        // Fetch live exchange rates on page load
        async function fetchExchangeRates() {
            try {
                const response = await fetch('{{ route("exchange.rates") }}');
                const result = await response.json();

                if (result.status === 'success') {
                    // OpenNode returns BTCZMW with ZMW value (amount of ZMW per 1 BTC)
                    if (result.btc_zmw) {
                        // 1 BTC = result.btc_zmw ZMW
                        // 1 SAT = result.btc_zmw / 100,000,000 ZMW
                        liveConversionRate = result.btc_zmw / 100000000;
                        btcUsdRate = result.btc_usd;
                    } else if (result.btc_usd) {
                        // Fallback: convert via USD if ZMW not available
                        // 1 SAT = (BTC_USD * USD_ZMW) / 100,000,000
                        btcUsdRate = result.btc_usd;
                        liveConversionRate = (btcUsdRate * usdToZmwRate) / 100000000;
                    }

                    // Update rate display
                    updateRateDisplay();

                    // Recalculate if user has already entered amounts
                    const buyAmount = document.getElementById('buy-amount').value;
                    const sellAmount = document.getElementById('sell-sats').value;
                    if (buyAmount && parseFloat(buyAmount) >= 20) {
                        calculateBuy();
                    }
                    if (sellAmount && parseFloat(sellAmount) >= 200) {
                        calculateSell();
                    }
                } else {
                    console.warn('Failed to fetch exchange rates, using default rate');
                }
            } catch (error) {
                console.error('Error fetching exchange rates:', error);
                // Continue with default rate
            }
        }

        // Update rate display with live rates
        function updateRateDisplay() {
            const buyRateText = `Rate: 1 ZMW = ~${(1 / liveConversionRate).toFixed(2)} SATS | Min: 20 ZMW`;
            const sellRateText = `Rate: 1 SAT = ${liveConversionRate.toFixed(4)} ZMW | Min: 200 SATS`;

            const buyInfoBadge = document.querySelector('#buy-card .info-badge span');
            const sellInfoBadge = document.querySelector('#sell-card .info-badge span');

            if (buyInfoBadge) {
                buyInfoBadge.textContent = buyRateText;
            }
            if (sellInfoBadge) {
                sellInfoBadge.textContent = sellRateText;
            }
        }

        // Fetch and display current balance
        async function fetchBalance() {
            try {
                const response = await fetch('{{ route("get.balance") }}');
                const result = await response.json();

                const balanceText = document.getElementById('balance-text');

                if (result.status === 'success') {
                    const balanceBtc = result.balance_btc || 0;
                    const balanceSats = Math.round(result.balance_sats || 0);

                    balanceText.innerHTML = `Available Balance: <strong>${balanceBtc.toFixed(8)} BTC</strong> (${balanceSats.toLocaleString()} SATS)`;
                    balanceText.style.color = '#10b981';

                    // Log raw response for debugging
                    console.log('Balance fetched successfully:', {
                        balance_btc: balanceBtc,
                        balance_sats: balanceSats,
                        raw_response: result.raw_response
                    });
                } else {
                    balanceText.innerHTML = `Balance: <strong>Error loading balance</strong> - ${result.message || 'Unknown error'}`;
                    balanceText.style.color = '#ef4444';

                    // Log error for debugging
                    console.error('Balance fetch error:', result);
                    if (result.raw_response) {
                        console.error('Raw API response:', result.raw_response);
                    }
                }
            } catch (error) {
                console.error('Error fetching balance:', error);
                const balanceText = document.getElementById('balance-text');
                balanceText.innerHTML = `Balance: <strong>Error loading balance</strong> - ${error.message}`;
                balanceText.style.color = '#ef4444';
            }
        }

        // Fetch rates when page loads (only if enabled via config/ENV)
        if (useOpenNodeExchangeRate) {
            fetchExchangeRates();
        }

        // Tab switching
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.conversion-card').forEach(card => card.classList.remove('active'));

            if (tab === 'buy') {
                document.querySelector('.tab-btn:first-child').classList.add('active');
                document.getElementById('buy-card').classList.add('active');
            } else {
                document.querySelector('.tab-btn:last-child').classList.add('active');
                document.getElementById('sell-card').classList.add('active');
            }
        }

        // Buy Bitcoin calculations
        function calculateBuy() {
            const amountKwacha = parseFloat(document.getElementById('buy-amount').value) || 0;

            if (amountKwacha < 20) {
                document.getElementById('buy-calc').style.display = 'none';
                return;
            }

            const conversionRate = liveConversionRate; // Use live SAT to ZMW rate
            const serviceFeeRate = {{ config('services.bitcoin.service_fee_rate') }}; // Service fee rate (buy)
            const networkFee = {{ config('services.bitcoin.buy_network_fee') }}; // Network fee for buying

            const amountSats = amountKwacha / conversionRate;
            const amountBtc = amountSats / 100000000;
            const conversionFee = amountKwacha * serviceFeeRate;
            const totalAmount = amountKwacha + conversionFee + networkFee;

            // Update display
            document.getElementById('buy-amount-display').textContent = amountKwacha.toFixed(2) + ' ZMW';
            document.getElementById('buy-fee-display').textContent = conversionFee.toFixed(2) + ' ZMW';
            document.getElementById('buy-total-display').textContent = totalAmount.toFixed(2) + ' ZMW';
            document.getElementById('buy-btc').value = amountBtc.toFixed(8);
            document.getElementById('buy-sats-display').value = Math.round(amountSats).toLocaleString();

            // Update hidden fields
            document.getElementById('buy-total').value = totalAmount.toFixed(2);
            document.getElementById('buy-sats').value = amountSats.toFixed(8);
            document.getElementById('buy-btc-hidden').value = amountBtc.toFixed(8);
            document.getElementById('buy-fee').value = conversionFee.toFixed(2);

            document.getElementById('buy-calc').style.display = 'block';
        }

        // Sell Bitcoin calculations
        function calculateSell() {
            const amountSats = parseFloat(document.getElementById('sell-sats').value) || 0;

            if (amountSats < 200) {
                document.getElementById('sell-calc').style.display = 'none';
                return;
            }

            const conversionRate = liveConversionRate; // Use live SAT to ZMW rate
            const serviceFeeRate = {{ config('services.bitcoin.sell_service_fee_rate') }}; // Service fee rate (sell)
            const networkFee = {{ config('services.bitcoin.sell_network_fee') }}; // Network fee for selling

            const amountBtc = amountSats / 100000000;
            const conversionFee = amountSats * serviceFeeRate;
            const totalSats = amountSats + conversionFee + networkFee;
            const receiveSats = amountSats - conversionFee;
            const receiveKwacha = receiveSats * conversionRate;

            // Update display
            document.getElementById('sell-sats-display').textContent = amountSats.toLocaleString() + ' SATS';
            document.getElementById('sell-fee-display').textContent = Math.round(conversionFee).toLocaleString() + ' SATS';
            document.getElementById('sell-total-display').textContent = Math.round(totalSats).toLocaleString() + ' SATS';
            document.getElementById('sell-receive-display').textContent = receiveKwacha.toFixed(2) + ' ZMW';
            document.getElementById('sell-kwacha').value = receiveKwacha.toFixed(2);

            // Update hidden fields
            document.getElementById('sell-sats-hidden').value = amountSats;
            document.getElementById('sell-btc').value = amountBtc.toFixed(8);
            document.getElementById('sell-kwacha-hidden').value = receiveKwacha.toFixed(2);
            document.getElementById('sell-fee').value = Math.round(conversionFee);
            document.getElementById('sell-total').value = Math.round(totalSats);

            document.getElementById('sell-calc').style.display = 'block';
        }

        // Handle Buy form submission
        async function handleBuy(event) {
            event.preventDefault();
            const form = event.target;
            const btn = document.getElementById('buy-btn');

            // Ensure all hidden fields are set
            const amountKwacha = document.getElementById('buy-amount').value;
            if (!amountKwacha || parseFloat(amountKwacha) < 20) {
                showErrorModal('Validation Error', 'Please enter an amount of at least 20 ZMW');
                return;
            }

            // Ensure calculations are done
            calculateBuy();

            // Get the amount of SATS they want to buy
            const amountSats = parseFloat(document.getElementById('buy-sats').value) || 0;
            if (!amountSats || amountSats < 1) {
                showErrorModal('Calculation Error', 'Please wait for the calculation to complete or enter a valid amount.');
                return;
            }

            // Show loading state
            btn.disabled = true;
            btn.classList.add('btn-loading');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Checking Balance...';
            btn.style.pointerEvents = 'none';

            try {
                // Check balance before proceeding
                const balanceResponse = await fetch('{{ route("check.balance") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount_sats: amountSats
                    })
                });

                const balanceResult = await balanceResponse.json();

                // Log balance check result for debugging
                console.log('Balance check result:', {
                    status: balanceResult.status,
                    sufficient: balanceResult.sufficient,
                    balance_btc: balanceResult.balance_btc,
                    balance_sats: balanceResult.balance_sats,
                    required_btc: balanceResult.required_btc,
                    required_sats: balanceResult.required_sats,
                    amount_sats_requested: amountSats
                });

                // Reset button
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                btn.style.pointerEvents = '';
                btn.innerHTML = originalText;

                if (balanceResult.status === 'error') {
                    console.error('Balance check error:', balanceResult);
                    showErrorModal('Balance Check Failed', balanceResult.message || 'Unable to verify account balance. Please try again later.');
                    return;
                }

                if (!balanceResult.sufficient) {
                    const balanceSats = Math.round(balanceResult.balance_sats || 0).toLocaleString();
                    const requiredSats = Math.round(balanceResult.required_sats || 0).toLocaleString();
                    showErrorModal(
                        'Insufficient Balance',
                        `We currently don't have enough Bitcoin in stock to fulfill your order. You requested ${requiredSats} SATS, but we only have few SATS available. Please try again later after some few minutes.`
                    );
                    return;
                }

                // Balance is sufficient, proceed with payment
                // Update amount_kwacha field
                if (!form.querySelector('[name="amount_kwacha"]')) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'amount_kwacha';
                    hiddenInput.value = amountKwacha;
                    form.appendChild(hiddenInput);
                } else {
                    form.querySelector('[name="amount_kwacha"]').value = amountKwacha;
                }

                // Add CSRF token
                if (!form.querySelector('[name="_token"]')) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                }

                // Submit form
                form.action = '{{ route("subscription.lenco") }}';
                form.method = 'POST';
                form.submit();

            } catch (error) {
                console.error('Balance check error:', error);
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                btn.style.pointerEvents = '';
                btn.innerHTML = originalText;
                showErrorModal('Network Error', 'Failed to check balance. Please check your internet connection and try again.');
            }
        }

        // Handle Sell form submission
        async function handleSell(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const btn = document.getElementById('sell-btn');

            // Validate amount
            const amountSats = document.getElementById('sell-sats').value;
            if (!amountSats || parseFloat(amountSats) < 500) {
                alert('Please enter an amount of at least 500 SATS');
                return;
            }

            // Ensure calculations are done
            calculateSell();

            // Get values from hidden fields (they should be populated by calculateSell)
            const amountSatsValue = parseFloat(document.getElementById('sell-sats-hidden').value) || parseFloat(amountSats);
            const amountBtcValue = parseFloat(document.getElementById('sell-btc').value) || 0;
            const amountKwachaValue = parseFloat(document.getElementById('sell-kwacha-hidden').value) || 0;
            const conversionFeeValue = parseFloat(document.getElementById('sell-fee').value) || 0;
            const totalSatsValue = parseFloat(document.getElementById('sell-total').value) || amountSatsValue;

            // Prepare data - ensure all values are properly set
            const data = {
                phone: formData.get('phone') || '',
                amount_sats: amountSatsValue,
                amount_btc: amountBtcValue,
                amount_kwacha: amountKwachaValue,
                conversion_fee: conversionFeeValue,
                total_sats: totalSatsValue,
                network_fee: parseFloat(formData.get('network_fee')) || 400,
            };

            // Validate required fields
            if (!data.phone || data.amount_sats < 500 || !data.total_sats) {
                alert('Please fill in phone number and ensure amount is at least 500 SATS. Make sure to click outside the amount field to calculate.');
                return;
            }

            // Show loading state
            btn.disabled = true;
            btn.classList.add('btn-loading');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Checking float availability...';
            btn.style.pointerEvents = 'none';

            try {
                // ── Step 1: Check Lenco float balance ──
                const floatResponse = await fetch('{{ route("check.lenco.balance") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount_kwacha: amountKwachaValue
                    })
                });

                const floatResult = await floatResponse.json();
                console.log('Lenco float check result:', floatResult);

                if (floatResult.status === 'error') {
                    btn.disabled = false;
                    btn.classList.remove('btn-loading');
                    btn.style.pointerEvents = '';
                    btn.innerHTML = originalText;
                    showErrorModal('Float Check Failed', floatResult.message || 'Unable to verify available float. Please try again later.');
                    return;
                }

                if (floatResult.sufficient === false) {
                    btn.disabled = false;
                    btn.classList.remove('btn-loading');
                    btn.style.pointerEvents = '';
                    btn.innerHTML = originalText;
                    showErrorModal(
                        'Temporarily Out of Float',
                        `We've temporarily run out of float to process your sell order of ${amountKwachaValue.toFixed(2)} ZMW. Please try again after a few minutes.`
                    );
                    return;
                }

                // ── Step 2: Float is sufficient, generate the invoice ──
                btn.innerHTML = 'Generating Invoice...';

                console.log('Sending data:', data);

                const invoiceResponse = await fetch('{{ route("generate.invoice") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await invoiceResponse.json();

                // Reset button
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                btn.style.pointerEvents = '';
                btn.innerHTML = originalText;

                if (!invoiceResponse.ok || result.status === 'error') {
                    // Check if it's an insufficient float error from the server-side double-check
                    if (result.insufficient_float) {
                        showErrorModal(
                            'Temporarily Out of Float',
                            result.message || "We've temporarily run out of float. Please try again later after some few minutes."
                        );
                    } else {
                        showErrorModal(result.message || 'Failed to generate invoice', result.error || result.details || 'Unknown error');
                    }
                    console.error('API Error:', result);
                    return;
                }

                if (result.status === 'success') {
                    showInvoiceModal(result);
                } else {
                    showErrorModal('Failed to generate invoice', result.message || 'Unknown error');
                }
            } catch (error) {
                console.error('Error:', error);
                // Reset button
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                btn.style.pointerEvents = '';
                btn.innerHTML = originalText;
                showErrorModal('Network Error', error.message || 'Failed to connect to server. Please check your internet connection and try again.');
            }
        }

        // Show error modal
        function showErrorModal(title, details) {
            const modal = document.getElementById('invoiceModal');
            const modalBody = document.getElementById('modalBody');

            modalBody.innerHTML = `
                <div style="text-align: center;">
                    <div style="width: 60px; height: 60px; background: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="bi bi-x-circle-fill" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h4 style="color: #ef4444; margin-bottom: 1rem;">${title}</h4>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem; margin-top: 1rem; text-align: left;">
                        <p style="color: #991b1b; margin: 0; font-size: 0.9rem; word-break: break-word;">${details}</p>
                    </div>
                    <button onclick="closeModal()" class="btn-primary" style="margin-top: 1.5rem; background: #ef4444;">
                        Close
                    </button>
                </div>
            `;

            modal.classList.add('active');
        }

        // Show invoice modal
        function showInvoiceModal(data) {
            const modal = document.getElementById('invoiceModal');
            const modalBody = document.getElementById('modalBody');

            modalBody.innerHTML = `
                <div class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
            </div>
                <h4 style="text-align: center; margin-bottom: 1rem;">Invoice Generated Successfully!</h4>
                <p style="text-align: center; color: var(--text-gray); margin-bottom: 1.5rem;">
                    Scan the QR code with your Lightning wallet to pay. You'll receive ${data.amount_kwacha} ZMW to ${document.querySelector('#sell-form [name="phone"]').value}
                </p>
                <div class="qr-code-container">
                    <img src="${window.location.protocol}//${window.location.host}/images/qrcodes/${data.qr_code_path}" alt="Lightning Invoice QR Code">
        </div>
                <div>
                    <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Lightning Invoice:</label>
                    <div class="invoice-address" id="invoiceAddress">${data.bolt11}</div>
                    <button class="copy-btn" onclick="copyInvoice(this)">
                        <i class="bi bi-clipboard"></i> Copy Invoice
                    </button>
            </div>
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                    <p style="font-size: 0.875rem; color: var(--text-gray); text-align: center;">
                        <i class="bi bi-info-circle"></i> This invoice expires in 10 minutes. After payment, you'll receive ${data.amount_kwacha} ZMW to your mobile money.
                    </p>
        </div>
            `;

            modal.classList.add('active');
        }

        // Close modal
        function closeModal() {
            document.getElementById('invoiceModal').classList.remove('active');
        }

        // Copy invoice to clipboard
        function copyInvoice(btnElement) {
            const invoiceText = document.getElementById('invoiceAddress').textContent;

            // Fallback method for older browsers or non-HTTPS
            const fallbackCopy = () => {
                const textArea = document.createElement('textarea');
                textArea.value = invoiceText;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        const originalText = btnElement.innerHTML;
                        btnElement.innerHTML = '<i class="bi bi-check"></i> Copied!';
                        btnElement.style.background = 'var(--success)';
                        setTimeout(() => {
                            btnElement.innerHTML = originalText;
                            btnElement.style.background = '';
                        }, 2000);
                    } else {
                        throw new Error('execCommand failed');
                    }
                } catch (err) {
                    // Select text for manual copy
                    const range = document.createRange();
                    range.selectNodeContents(document.getElementById('invoiceAddress'));
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                    alert('Please press Ctrl+C (or Cmd+C on Mac) to copy the invoice.');
                }
                document.body.removeChild(textArea);
            };

            // Try modern clipboard API first
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(invoiceText).then(() => {
                    const originalText = btnElement.innerHTML;
                    btnElement.innerHTML = '<i class="bi bi-check"></i> Copied!';
                    btnElement.style.background = 'var(--success)';
                    setTimeout(() => {
                        btnElement.innerHTML = originalText;
                        btnElement.style.background = '';
                    }, 2000);
                }).catch(err => {
                    // Fallback if clipboard API fails
                    fallbackCopy();
                });
            } else {
                // Use fallback for older browsers
                fallbackCopy();
            }
        }

        // Close modal when clicking outside
        document.getElementById('invoiceModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>

</html>