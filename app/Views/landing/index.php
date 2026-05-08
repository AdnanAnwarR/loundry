<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LaundrySwift - Premium Laundry Service</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CDN (Dijamin langsung jalan tanpa pusing folder!) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Desain Premium Kustom */
        :root {
            --primary-color: #2563EB; /* Blue */
            --secondary-color: #60A5FA; /* Light Blue */
            --text-dark: #1E293B;
            --text-light: #64748B;
            --bg-light: #F8FAFC;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Navbar Styling */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 28px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .btn-outline-custom {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 50px;
            padding: 8px 26px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            padding: 160px 0 100px;
            position: relative;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 24px;
            background: linear-gradient(135deg, var(--text-dark), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-img-wrapper {
            position: relative;
            z-index: 1;
        }

        .hero-img-wrapper::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #E0E7FF, #C7D2FE);
            border-radius: 24px;
            z-index: -1;
            transform: rotate(3deg);
        }

        .hero-img {
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        /* Floating Cards */
        .floating-card {
            background: white;
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            position: absolute;
            animation: float 6s ease-in-out infinite;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .fc-1 { top: 10%; right: -10%; animation-delay: 0s; }
        .fc-2 { bottom: 10%; left: -10%; animation-delay: 3s; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        /* Services Section */
        .service-card {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 30px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
        }

        .service-icon {
            width: 70px;
            height: 70px;
            background: #EEF2FF;
            color: var(--primary-color);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .service-card:hover .service-icon {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1) rotate(5deg);
        }

        /* Process Steps */
        .process-step {
            position: relative;
            text-align: center;
            padding: 20px;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: white;
            border: 3px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
            position: relative;
            z-index: 2;
        }

        @media (min-width: 992px) {
            .process-step:not(:last-child)::after {
                content: '';
                position: absolute;
                top: 50px;
                right: -50%;
                width: 100%;
                height: 3px;
                background: dashed 3px #CBD5E1;
                z-index: 1;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url() ?>">
                <i class="bi bi-droplet-fill"></i>
                LaundrySwift
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link px-3" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#process">Process</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#services">Services</a></li>
                </ul>
                <div class="d-flex gap-3 mt-3 mt-lg-0 align-items-center">
                    <a href="<?= base_url('login') ?>" class="nav-link fw-bold">Login</a>
                    <a href="<?= base_url('register') ?>" class="btn-custom text-decoration-none">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-center text-lg-start">
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-4 fw-semibold border border-primary-subtle">
                        <i class="bi bi-stars me-1"></i> Premium Care For Your Clothes
                    </span>
                    <h1 class="hero-title">
                        Fast, Clean & <br> Reliable Laundry
                    </h1>
                    <p class="lead mb-5 text-secondary" style="font-weight: 400;">
                        We wash, iron, pack, and deliver your clothes with absolute care. Reclaim your time and experience the ultimate convenience.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="<?= base_url('register') ?>" class="btn-custom text-decoration-none text-center">Order Now <i class="bi bi-arrow-right ms-2"></i></a>
                        <a href="#services" class="btn-outline-custom text-decoration-none text-center">View Services</a>
                    </div>
                    
                    <div class="row mt-5 pt-4 border-top text-center text-lg-start">
                        <div class="col-6 col-md-4">
                            <h2 class="fw-bolder text-dark mb-0">24h</h2>
                            <span class="text-muted small fw-semibold text-uppercase">Turnaround</span>
                        </div>
                        <div class="col-6 col-md-4">
                            <h2 class="fw-bolder text-dark mb-0">10k+</h2>
                            <span class="text-muted small fw-semibold text-uppercase">Happy Clients</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 position-relative d-none d-lg-block">
                    <div class="hero-img-wrapper mx-auto" style="max-width: 500px;">
                        <!-- Menggunakan gambar online agar pasti muncul tanpa masalah path folder -->
                        <img src="https://images.unsplash.com/photo-1517677208171-0bc6725a3e60?q=80&w=1000&auto=format&fit=crop" alt="Laundry Service" class="img-fluid hero-img">
                        
                        <div class="floating-card fc-1">
                            <div class="text-primary fs-3"><i class="bi bi-flower1"></i></div>
                            <div>
                                <h6 class="mb-0 fw-bold">Eco-Friendly</h6>
                                <small class="text-muted">Premium Soap</small>
                            </div>
                        </div>

                        <div class="floating-card fc-2">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-check2 fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 10px; font-weight: 700; letter-spacing: 1px;">STATUS</small>
                                <span class="fw-bold text-dark">Order Delivered</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works Section -->
    <section id="process" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h6 class="text-primary fw-bold text-uppercase tracking-wide">How It Works</h6>
                <h2 class="fw-bolder">Three Simple Steps</h2>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <h4 class="fw-bold mb-3">Place Order</h4>
                        <p class="text-muted">Select your required services and schedule a convenient pickup time through our portal.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <h4 class="fw-bold mb-3">We Process</h4>
                        <p class="text-muted">Our experts handle your garments with premium, eco-friendly cleaning methods.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <h4 class="fw-bold mb-3">We Deliver</h4>
                        <p class="text-muted">Receive your freshly laundered and neatly packed clothes right at your doorstep.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h6 class="text-primary fw-bold text-uppercase tracking-wide">Our Services</h6>
                <h2 class="fw-bolder">Transparent Pricing</h2>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-water"></i></div>
                        <h4 class="fw-bold mb-3">Cuci Bersih</h4>
                        <p class="text-muted mb-4">Professional washing and sanitizing to maintain your fabric quality.</p>
                        <div class="d-flex justify-content-between align-items-end mt-auto pt-3 border-top">
                            <span class="text-muted small fw-bold text-uppercase">Starting At</span>
                            <span class="fs-4 fw-bolder text-primary">Rp 5.000<span class="fs-6 text-muted fw-normal">/kg</span></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-wind"></i></div>
                        <h4 class="fw-bold mb-3">Setrika Rapi</h4>
                        <p class="text-muted mb-4">Expert ironing for crisp, wrinkle-free garments ready to wear.</p>
                        <div class="d-flex justify-content-between align-items-end mt-auto pt-3 border-top">
                            <span class="text-muted small fw-bold text-uppercase">Starting At</span>
                            <span class="fs-4 fw-bolder text-primary">Rp 5.000<span class="fs-6 text-muted fw-normal">/kg</span></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-box2-heart"></i></div>
                        <h4 class="fw-bold mb-3">Premium Packing</h4>
                        <p class="text-muted mb-4">Secure, eco-friendly packaging for safe travel and storage.</p>
                        <div class="d-flex justify-content-between align-items-end mt-auto pt-3 border-top">
                            <span class="text-muted small fw-bold text-uppercase">Starting At</span>
                            <span class="fs-4 fw-bolder text-primary">Rp 3.000<span class="fs-6 text-muted fw-normal">/kg</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container text-center">
            <h3 class="fw-bold mb-4 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-droplet-fill text-primary"></i> LaundrySwift
            </h3>
            <p class="text-secondary mb-4 mx-auto" style="max-width: 400px;">
                Making your life easier, one clean shirt at a time. Experience the best laundry service in town.
            </p>
            <div class="d-flex justify-content-center gap-4 mb-4">
                <a href="#" class="text-secondary text-decoration-none hover-white">Privacy Policy</a>
                <a href="#" class="text-secondary text-decoration-none hover-white">Terms of Service</a>
            </div>
            <p class="text-secondary small mb-0">&copy; 2026 LaundrySwift. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
