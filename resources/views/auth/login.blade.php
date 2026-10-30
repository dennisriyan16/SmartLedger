<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - SmartLedger</title>
    <!-- Bootstrap 5 CSS & Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, html {
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.84rem;
            color: #334155;
            background-color: #ffffff;
            overflow-x: hidden;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            width: 100%;
        }

        /* Left Side: Visual Hero Showcase */
        .hero-banner {
            flex: 1 1 58%;
            position: relative;
            background: url("{{ asset('images/finance-login-bg.jpg') }}") center center / cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3.5rem;
            color: #ffffff;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(30, 41, 59, 0.78) 50%, rgba(15, 23, 42, 0.92) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .hero-brand i {
            font-size: 1.5rem;
            color: #60a5fa;
        }

        .hero-headline {
            max-width: 540px;
            margin-top: auto;
            margin-bottom: auto;
            padding: 3rem 0;
        }

        .hero-headline h1 {
            font-size: 2.15rem;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -0.5px;
            margin-bottom: 1rem;
            color: #f8fafc;
        }

        .hero-headline p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #cbd5e1;
            margin-bottom: 2rem;
        }

        .hero-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            max-width: 500px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .feature-item i {
            color: #60a5fa;
            font-size: 1.1rem;
            margin-top: 0.1rem;
        }

        .feature-item-text strong {
            display: block;
            font-size: 0.85rem;
            color: #f1f5f9;
        }

        .feature-item-text span {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .hero-footer {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Right Side: Clean Login Form */
        .form-side {
            flex: 0 0 42%;
            max-width: 560px;
            min-width: 380px;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3.5rem 3.5rem;
            position: relative;
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.04);
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .form-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.4px;
            margin-bottom: 0.45rem;
        }

        .form-subtitle {
            font-size: 0.92rem;
            color: #64748b;
            margin-bottom: 1.85rem;
        }

        .form-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .input-group-text {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
            font-size: 0.95rem;
            padding: 0.55rem 0.75rem;
        }

        .form-control {
            font-size: 0.92rem;
            padding: 0.55rem 0.75rem;
            border-color: #cbd5e1;
            border-radius: 0.375rem;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-check-label {
            font-size: 0.86rem !important;
            color: #475569;
        }

        .btn-submit {
            background-color: #2563eb;
            color: #ffffff;
            font-size: 0.92rem;
            font-weight: 600;
            padding: 0.65rem 1rem;
            border-radius: 0.375rem;
            border: none;
            width: 100%;
            transition: background-color 0.15s ease;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }

        .demo-pill {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 0.45rem;
            padding: 0.85rem 1rem;
            margin-top: 1.85rem;
            font-size: 0.84rem;
            color: #475569;
            line-height: 1.6;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .hero-banner {
                display: none;
            }
            .form-side {
                flex: 1 1 100%;
                max-width: 100%;
                padding: 2rem 1.5rem;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Left Side: Hero Banner with Finance Illustration Background -->
        <div class="hero-banner">
            <!-- Brand header -->
            <div class="hero-content hero-brand">
                <i class="bi bi-wallet2"></i>
                <span>Smart<span style="color: #60a5fa;">Ledger</span></span>
            </div>

            <!-- Central Hero Message -->
            <div class="hero-content hero-headline">
                <h1>Financial control, invoices & budgets in one place.</h1>
                <p>
                    Streamline your billing workflows, maintain precise departmental budget caps, and monitor cashflow analytics in real time.
                </p>

                <div class="hero-features">
                    <div class="feature-item">
                        <i class="bi bi-receipt-cutoff"></i>
                        <div class="feature-item-text">
                            <strong>Dual Invoicing</strong>
                            <span>Sales & purchase bills with live tax preview</span>
                        </div>
                    </div>

                    <div class="feature-item">
                        <i class="bi bi-pie-chart"></i>
                        <div class="feature-item-text">
                            <strong>Budget Controls</strong>
                            <span>Monthly & annual limits with overspend alerts</span>
                        </div>
                    </div>

                    <div class="feature-item">
                        <i class="bi bi-calculator"></i>
                        <div class="feature-item-text">
                            <strong>Exact Arithmetic</strong>
                            <span>BCMath precision for zero rounding discrepancies</span>
                        </div>
                    </div>

                    <div class="feature-item">
                        <i class="bi bi-currency-exchange"></i>
                        <div class="feature-item-text">
                            <strong>Live Exchange Rates</strong>
                            <span>ECB rates via Frankfurter API integration</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer note -->
            <div class="hero-content hero-footer">
                <span>SmartLedger Enterprise System &bull; Powered by Laravel 12 & MySQL</span>
            </div>
        </div>

        <!-- Right Side: Clean Login Form -->
        <div class="form-side">
            <div class="form-container">
                <div class="d-lg-none mb-4 text-center">
                    <div class="d-inline-flex align-items-center gap-2 fs-5 fw-bold text-dark">
                        <i class="bi bi-wallet2 text-primary"></i>
                        <span>Smart<span class="text-primary">Ledger</span></span>
                    </div>
                </div>

                <h2 class="form-title">Sign in to your account</h2>
                <p class="form-subtitle">Enter your credentials below to access the administrative portal.</p>

                <x-alert />

                <form action="{{ route('login.post') }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', 'admin@example.com') }}" placeholder="admin@example.com" required autofocus>
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="password" class="form-label">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" value="password" placeholder="••••••••" required>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                        <label class="form-check-label text-muted" for="remember" style="font-size: 0.78rem;">
                            Keep me signed in for 30 days
                        </label>
                    </div>

                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to SmartLedger
                    </button>
                </form>

                <div class="demo-pill">
                    <div class="fw-semibold text-dark mb-0.5"><i class="bi bi-key me-1 text-primary"></i> Demo Access:</div>
                    <div>Email: <strong class="text-dark font-monospace">admin@example.com</strong></div>
                    <div>Password: <strong class="text-dark font-monospace">password</strong></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
