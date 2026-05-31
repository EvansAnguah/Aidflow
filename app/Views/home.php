<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidFlow — Modern Welfare & Fund Management System</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
            --accent-gradient: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
            --text-main: #0f172a;
            --text-sub: #475569;
            --bg-page: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            overflow-x: hidden;
        }

        .font-header {
            font-family: 'Outfit', sans-serif;
        }

        /* Hero Background Grid */
        .bg-grid {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 900px;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(236, 72, 153, 0.05) 0%, transparent 40%);
            z-index: -1;
        }

        /* Navbar Styling */
        .navbar-custom {
            backdrop-filter: blur(15px);
            background-color: rgba(255, 255, 255, 0.75);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s;
            padding: 18px 0;
        }

        .navbar-brand-custom {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.65rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .navbar-brand-custom i {
            margin-right: 8px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link-custom {
            color: var(--text-sub) !important;
            font-weight: 600;
            transition: color 0.2s;
            padding: 8px 16px;
        }

        .nav-link-custom:hover {
            color: #3b82f6 !important;
        }

        /* Premium Buttons */
        .btn-premium-primary {
            background: var(--primary-gradient);
            color: #fff !important;
            font-weight: 600;
            border: none;
            padding: 12px 28px;
            border-radius: 50px;
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
            transition: all 0.3s;
        }

        .btn-premium-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -3px rgba(59, 130, 246, 0.5);
        }

        .btn-premium-secondary {
            background: #fff;
            color: #3b82f6 !important;
            font-weight: 600;
            border: 1px solid rgba(59, 130, 246, 0.3);
            padding: 12px 28px;
            border-radius: 50px;
            box-shadow: 0 10px 20px -10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .btn-premium-secondary:hover {
            background: rgba(59, 130, 246, 0.03);
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            padding: 160px 0 100px 0;
            position: relative;
        }

        .hero-badge {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1.5px;
            margin-bottom: 25px;
        }

        .gradient-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-text-alt {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.25rem;
            color: var(--text-sub);
            line-height: 1.7;
            margin-bottom: 40px;
            max-width: 580px;
        }

        /* Interactive Card Glassmorphism */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 35px;
            height: 100%;
        }

        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.12);
            border-color: rgba(99, 102, 241, 0.25);
        }

        .icon-box {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            font-size: 1.6rem;
            color: #6366f1;
            transition: all 0.3s;
        }

        .glass-card:hover .icon-box {
            background: var(--primary-gradient);
            color: #fff;
            transform: scale(1.05);
        }

        /* Features Section */
        .section-padding {
            padding: 100px 0;
        }

        .section-tag {
            color: #6366f1;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 2px;
            display: block;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 2.75rem;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 20px;
        }

        .section-desc {
            color: var(--text-sub);
            font-size: 1.15rem;
            max-width: 600px;
            margin: 0 auto 60px auto;
            line-height: 1.6;
        }

        /* Status Pills */
        .stats-wrapper {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 32px;
            padding: 60px 40px;
            color: #fff;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.3);
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }

        .stat-num {
            font-size: 3.25rem;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Footer */
        .footer-premium {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 80px 0 30px 0;
            border-top: 1px solid #1e293b;
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 500;
        }

        .footer-link:hover {
            color: #3b82f6;
        }

        .footer-social-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #1e293b;
            color: #f8fafc;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .footer-social-icon:hover {
            background: var(--primary-gradient);
            transform: translateY(-3px);
            color: #fff;
        }

        /* Dashboard Preview Mockup */
        .mockup-container {
            position: relative;
            z-index: 2;
        }

        .mockup-wrapper {
            background: #fff;
            border-radius: 20px;
            padding: 10px;
            box-shadow: 0 30px 60px -15px rgba(0,0,0,0.15);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
            transition: all 0.5s ease;
        }

        .mockup-wrapper:hover {
            transform: perspective(1000px) rotateY(0deg) rotateX(0deg);
        }

        .mockup-header {
            height: 24px;
            background-color: #f1f5f9;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            padding: 0 15px;
            gap: 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        .mockup-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .mockup-content {
            border-radius: 0 0 12px 12px;
            overflow: hidden;
            background: #f8fafc;
            height: 380px;
            padding: 25px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .mockup-widget {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
        }

        /* Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .floating {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>
<body>

    <!-- Background Grids -->
    <div class="bg-grid"></div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand-custom" href="<?= BASE_URL ?>/">
                <i class="fa-solid fa-layer-group"></i>AidFlow
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#roles">Role Ecosystem</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#about">About</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= BASE_URL ?>/auth/login" class="btn btn-premium-secondary">Sign In</a>
                    <a href="<?= BASE_URL ?>/auth/register" class="btn btn-premium-primary">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="hero-badge">
                        <i class="fa-solid fa-shield-halved"></i> Enterprise Welfare Management
                    </span>
                    <h1 class="hero-title font-header">
                        Empowering <span class="gradient-text">Communities</span>, Managing <span class="gradient-text-alt">Welfare</span>.
                    </h1>
                    <p class="hero-desc">
                        A premium, fully audited system for schools, churches, clubs, and welfare organizations to streamline members, automate contributions, handle welfare requests, and track real-time disbursements.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="<?= BASE_URL ?>/auth/register" class="btn btn-premium-primary btn-lg px-4 py-3"><i class="fa-solid fa-user-plus me-2"></i>Create Free Account</a>
                        <a href="#features" class="btn btn-premium-secondary btn-lg px-4 py-3"><i class="fa-solid fa-circle-play me-2"></i>Explore Features</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mockup-container">
                        <!-- Premium Interactive Mockup representing the Dashboard -->
                        <div class="mockup-wrapper floating">
                            <div class="mockup-header">
                                <div class="mockup-dot" style="background-color: #ef4444;"></div>
                                <div class="mockup-dot" style="background-color: #f59e0b;"></div>
                                <div class="mockup-dot" style="background-color: #10b981;"></div>
                                <span class="ms-2 text-muted font-monospace" style="font-size: 0.65rem;">localhost/AidFlow/dashboard</span>
                            </div>
                            <div class="mockup-content">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="mockup-widget">
                                            <span class="text-muted d-block font-header" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Welfare Balance</span>
                                            <span class="font-header" style="font-size: 1.6rem; font-weight: 800; color: #10b981;">$10,240.00</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mockup-widget">
                                            <span class="text-muted d-block font-header" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Active Members</span>
                                            <span class="font-header" style="font-size: 1.6rem; font-weight: 800; color: #6366f1;">438 Active</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mockup-widget flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="font-header d-block mb-3" style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: #475569;">Welfare Payout Request</span>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span style="font-size: 0.8rem; font-weight: 600;">Medical Support — John Doe</span>
                                            <span class="badge bg-warning rounded-pill" style="font-size: 0.65rem;">Under Review</span>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 10px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                        <span class="text-muted" style="font-size: 0.7rem;"><i class="fa-solid fa-clock me-1"></i>Last updated 2 mins ago</span>
                                        <span style="font-size: 0.75rem; font-weight: 700; color: #3b82f6;">Go to Panel <i class="fa-solid fa-arrow-right"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Panel -->
    <section class="container mb-5">
        <div class="stats-wrapper">
            <div class="row text-center g-4">
                <div class="col-md-3 col-6">
                    <div class="stat-num">$10,000+</div>
                    <div class="stat-label">Initial Seed Fund</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-num">4 Roles</div>
                    <div class="stat-label">Permissions Ecosystem</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-num">100%</div>
                    <div class="stat-label">Audit Record Logs</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-num">AJAX</div>
                    <div class="stat-label">Real-time Responses</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Features Grid -->
    <section id="features" class="section-padding">
        <div class="container">
            <div class="text-center">
                <span class="section-tag">System Core Features</span>
                <h2 class="section-title font-header">High Fidelity Welfare Operations</h2>
                <p class="section-desc">
                    AidFlow is built with security first, performance standard MVC, prepared PDO interfaces, CSRF, and fully role-based custom operations.
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>
                        <h4 class="font-header fw-700 mb-3">Sleek Contributions Ledger</h4>
                        <p class="text-muted">
                            Treasurers record monthly payments effortlessly. Members view transaction statements and download printable receipt vouchers instantly.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <h4 class="font-header fw-700 mb-3">Transparent Welfare Workflows</h4>
                        <p class="text-muted">
                            Members submit requests and upload documents. Officers evaluate and recommend. Admins make final approvals. Treasurers disburse funds securely.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-shield-check"></i>
                        </div>
                        <h4 class="font-header fw-700 mb-3">Comprehensive Auditing</h4>
                        <p class="text-muted">
                            Every sensitive action is tracked. Live audit trails log IP addresses, times, and activities to guarantee high-integrity organizational management.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Role Ecosystem Section -->
    <section id="roles" class="section-padding" style="background-color: rgba(241, 245, 249, 0.5);">
        <div class="container">
            <div class="text-center">
                <span class="section-tag">User Personas</span>
                <h2 class="section-title font-header">Unified Organization Workspaces</h2>
                <p class="section-desc">
                    Four distinct, highly targeted user roles optimized to ensure secure governance and efficient fund operations.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 18px;">
                        <span class="badge bg-danger rounded-pill align-self-start mb-3" style="padding: 6px 12px; font-weight: 700;">Administrator</span>
                        <h5 class="font-header fw-bold mb-3">Governance & Policies</h5>
                        <p class="text-muted small">
                            Set up welfare categories, define payment requirements, adjust system configurations, audit user action trails, and make final approvals.
                        </p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 18px;">
                        <span class="badge bg-success rounded-pill align-self-start mb-3" style="padding: 6px 12px; font-weight: 700;">Treasurer</span>
                        <h5 class="font-header fw-bold mb-3">Financial Oversight</h5>
                        <p class="text-muted small">
                            Log and verify member contributions, execute approved fund payouts, monitor balances, and generate analytical accounting ledgers.
                        </p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 18px;">
                        <span class="badge bg-primary rounded-pill align-self-start mb-3" style="padding: 6px 12px; font-weight: 700;">Welfare Officer</span>
                        <h5 class="font-header fw-bold mb-3">Evaluation & Trust</h5>
                        <p class="text-muted small">
                            Review member claims, verify uploaded files, post comments on timeline streams, and forward recommendations for final clearance.
                        </p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 18px;">
                        <span class="badge bg-secondary rounded-pill align-self-start mb-3" style="padding: 6px 12px; font-weight: 700;">Member</span>
                        <h5 class="font-header fw-bold mb-3">Self-Service Portal</h5>
                        <p class="text-muted small">
                            Register profiles, track individual monthly payment statements, submit requests, and watch timeline status transitions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-premium">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <a class="navbar-brand-custom mb-3 d-inline-block" href="#" style="text-decoration: none;">
                        <i class="fa-solid fa-layer-group"></i>AidFlow
                    </a>
                    <p class="small mb-4 text-muted">
                        A state-of-the-art community welfare system designed to bring accountability, ease-of-use, and premium interactions to local and enterprise communities.
                    </p>
                    <div class="d-flex">
                        <a href="#" class="footer-social-icon"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="footer-social-icon"><i class="fa-brands fa-github"></i></a>
                        <a href="#" class="footer-social-icon"><i class="fa-brands fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <h6 class="text-white font-header mb-4">Core Modules</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="#" class="footer-link">Contributions</a></li>
                        <li><a href="#" class="footer-link">Welfare Board</a></li>
                        <li><a href="#" class="footer-link">Disbursements</a></li>
                        <li><a href="#" class="footer-link">Audit Trail</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <h6 class="text-white font-header mb-4">Resources</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="#" class="footer-link">Integrations</a></li>
                        <li><a href="#" class="footer-link">PHP Autoloader</a></li>
                        <li><a href="#" class="footer-link">Security Policies</a></li>
                        <li><a href="#" class="footer-link">Admin Credentials</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h6 class="text-white font-header mb-4">Premium Engineering</h6>
                    <p class="small text-muted mb-3">
                        AidFlow is built on standard vanilla CSS and modern Bootstrap 5 elements, bypassing heavy tailwind bundles for maximum local performance.
                    </p>
                    <span class="badge bg-dark border border-secondary text-secondary small py-2 px-3">
                        <i class="fa-brands fa-php me-1 text-primary"></i> Powered by PHP 8 & PDO
                    </span>
                </div>
            </div>
            <div class="border-top border-secondary mt-5 pt-4 d-flex flex-wrap justify-content-between align-items-center small">
                <span>&copy; 2026 AidFlow. Engineered with precision. All rights reserved.</span>
                <span class="d-flex gap-4 mt-2 mt-md-0">
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                </span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
