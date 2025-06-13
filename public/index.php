<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shilpa Sawiya - Bridging Educational Gaps Through Donation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-reader me-2"></i>
                <span class="brand-text">Shilpa Sawiya</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="donations.php">
                        <i class="fa-solid fa-book"></i>Donations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="public-requests.php">
                        <i class="fa-solid fa-book-open"></i>Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                        <i class="fa-solid fa-address-card"></i>About
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-user me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Empowering Education Through Donation</h1>
                    <p class="lead mb-4">
                        Shilpa Sawiya is a community-driven platform connecting donors with educational needs. 
                        Join us in making education accessible to everyone.
                    </p>
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <div class="d-flex gap-3">
                            <a href="register.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Join Our Community
                            </a>
                            <a href="login.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/education-hero.svg" alt="Education illustration" class="img-fluid">
                </div>
            </div>
        </div>
    </header>

    <section class="features-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3">Why Choose Shilpa Sawiya?</h2>
                <p class="lead text-muted">Discover how our platform makes education more accessible</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-hover rounded-4 hover-translate-y-n3">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-hand-holding-usd fa-3x text-primary"></i>
                            </div>
                            <h3 class="h5 mb-3">Easy Donation</h3>
                            <p class="text-muted mb-0">
                                Donate your unused educational materials with just a few clicks. 
                                Our platform makes it simple to help others.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-hover rounded-4 hover-translate-y-n3">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-search fa-3x text-primary"></i>
                            </div>
                            <h3 class="h5 mb-3">Find What You Need</h3>
                            <p class="text-muted mb-0">
                                Browse through our extensive catalog of available donations 
                                and find the educational materials you need.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-hover rounded-4 hover-translate-y-n3">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-users fa-3x text-primary"></i>
                            </div>
                            <h3 class="h5 mb-3">Community Support</h3>
                            <p class="text-muted mb-0">
                                Join a community of educators, students, and donors 
                                working together to make education accessible.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>

    <section class="how-it-works py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3">How It Works</h2>
                <p class="lead text-muted">Simple steps to share and receive educational materials</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-hover rounded-4 hover-translate-y-n3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number bg-primary text-white p-3 rounded-circle me-3">
                                    <span class="h4 mb-0">1</span>
                                </div>
                                <h3 class="h5 mb-0">List Your Items</h3>
                            </div>
                            <p class="text-muted mb-0">
                                Easily list your unused educational materials for donation. 
                                Add photos and detailed descriptions to help others find what they need.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-hover rounded-4 hover-translate-y-n3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number bg-success text-white p-3 rounded-circle me-3">
                                    <span class="h4 mb-0">2</span>
                                </div>
                                <h3 class="h5 mb-0">Find Donations</h3>
                            </div>
                            <p class="text-muted mb-0">
                                Browse through our catalog of available donations 
                                and request items that match your educational needs.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-hover rounded-4 hover-translate-y-n3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number bg-info text-white p-3 rounded-circle me-3">
                                    <span class="h4 mb-0">3</span>
                                </div>
                                <h3 class="h5 mb-0">Connect & Exchange</h3>
                            </div>
                            <p class="text-muted mb-0">
                                Connect with donors directly to arrange pickup or delivery 
                                of the educational materials you need.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="display-4 fw-bold mb-3">Ready to Make a Difference?</h2>
                    <p class="lead mb-4">Join our community of educators and donors today!</p>
                </div>
                <div class="col-lg-4 text-center">
                    <a href="register.php" class="btn btn-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Start Donating
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3">What Our Community Says</h2>
                <p class="lead text-muted">Real stories from our users</p>
            </div>
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="testimonial-card h-100 border-0 shadow-hover rounded-4 hover-translate-y-n3">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="https://via.placeholder.com/50" alt="User 1" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h5 class="mb-0">Priya Sharma</h5>
                                                <small class="text-muted">Teacher</small>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-0">
                                            "Shilpa Sawiya has been a game-changer for our school. We've received so many valuable educational materials that would have otherwise gone to waste."
                                        </p>
                                        <div class="mt-3">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="testimonial-card h-100 border-0 shadow-hover rounded-4 hover-translate-y-n3">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="https://via.placeholder.com/50" alt="User 2" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h5 class="mb-0">Rahul Kumar</h5>
                                                <small class="text-muted">Student</small>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-0">
                                            "I was able to get all the textbooks I needed for my studies through Shilpa Sawiya. It saved me a lot of money and helped me focus on my education."
                                        </p>
                                        <div class="mt-3">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="testimonial-card h-100 border-0 shadow-hover rounded-4 hover-translate-y-n3">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="https://via.placeholder.com/50" alt="User 3" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h5 class="mb-0">Amita Desai</h5>
                                                <small class="text-muted">Parent</small>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-0">
                                            "As a parent, I'm grateful for Shilpa Sawiya. It's helping our children get the educational resources they need while teaching them about giving back."
                                        </p>
                                        <div class="mt-3">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add more carousel items here -->
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <footer class="footer-section py-5 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h4 class="h5 mb-4">About Shilpa Sawiya</h4>
                    <p class="text-muted mb-4">
                        Shilpa Sawiya is a community-driven platform dedicated to making education accessible by connecting donors with educational needs.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h4 class="h5 mb-4">Quick Links</h4>
                    <ul class="list-unstyled">
                        <li class="mb-3"><a href="about.php" class="text-muted text-decoration-none">About Us</a></li>
                        <li class="mb-3"><a href="how-it-works.php" class="text-muted text-decoration-none">How It Works</a></li>
                        <li class="mb-3"><a href="faq.php" class="text-muted text-decoration-none">FAQ</a></li>
                        <li class="mb-3"><a href="contact.php" class="text-muted text-decoration-none">Contact Us</a></li>
                        <li class="mb-3"><a href="privacy.php" class="text-muted text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h4 class="h5 mb-4">Contact Us</h4>
                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <span>123 Education Street, Knowledge City, 123456</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:contact@shilpasawiya.com" class="text-muted text-decoration-none">contact@shilpasawiya.com</a>
                    </div>
                    <div>
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:+911234567890" class="text-muted text-decoration-none">+91 123 456 7890</a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> Shilpa Sawiya. All rights reserved.</p>
                </div>
            </div>
        </div>
</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
