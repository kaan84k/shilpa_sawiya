<footer class="bg-white mt-5 pt-5 pb-4 border-top">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-book-reader me-2 text-primary" style="font-size: 1.8rem;"></i>
                    <h5 class="mb-0 fw-bold text-dark">Shilpa Sawiya</h5>
                </div>
                <p class="text-muted mb-3">
                    A platform connecting donors with those in need, making it easier to give and receive educational resources.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-decoration-none text-muted hover-primary">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-decoration-none text-muted hover-primary">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-decoration-none text-muted hover-primary">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-decoration-none text-muted hover-primary">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="index.php" class="text-muted text-decoration-none d-flex align-items-center">
                            <i class="fas fa-chevron-right me-2 text-primary" style="font-size: 0.6rem;"></i> Home
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="donations.php" class="text-muted text-decoration-none d-flex align-items-center">
                            <i class="fas fa-chevron-right me-2 text-primary" style="font-size: 0.6rem;"></i> Donations
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="public-requests.php" class="text-muted text-decoration-none d-flex align-items-center">
                            <i class="fas fa-chevron-right me-2 text-primary" style="font-size: 0.6rem;"></i> Requests
                        </a>
                    </li>
                    <li>
                        <a href="about.php" class="text-muted text-decoration-none d-flex align-items-center">
                            <i class="fas fa-chevron-right me-2 text-primary" style="font-size: 0.6rem;"></i> About Us
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3">Contact Us</h6>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2 d-flex align-items-center">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <span>info@shilpasawiya.com</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="fas fa-phone me-2 text-primary"></i>
                        <span>+91 XXXXXXXXXX</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3">Newsletter</h6>
                <p class="text-muted small mb-3">Subscribe to get updates about new donations and requests.</p>
                <form class="mb-3">
                    <div class="input-group">
                        <input type="email" class="form-control form-control-sm" placeholder="Your email" required>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <hr class="my-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0 text-muted small">&copy; <?php echo date('Y'); ?> Shilpa Sawiya. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="#" class="text-muted small text-decoration-none me-3 hover-primary">Privacy Policy</a>
                <a href="#" class="text-muted small text-decoration-none hover-primary">Terms & Conditions</a>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Hover Effects */
.hover-primary {
    transition: color 0.2s ease;
}

.hover-primary:hover {
    color: #0d6efd !important;
}

/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}
</style>

<style>
/* Footer Styles */
.footer {
    background: linear-gradient(145deg, #2c3e50, #1a252f);
    color: #ecf0f1;
    padding: 4rem 0 0;
    position: relative;
    overflow: hidden;
}

.footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #0d6efd, #6610f2);
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 2rem;
    margin-bottom: 3rem;
}

.footer-section {
    flex: 1;
    min-width: 250px;
    padding: 0 1rem;
}

.footer-title {
    color: #fff;
    font-size: 1.4rem;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.8rem;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 2px;
    background: linear-gradient(90deg, #0d6efd, #6610f2);
}

.footer-about {
    line-height: 1.7;
    color: #bdc3c7;
    margin-bottom: 1.5rem;
}

.contact span {
    display: block;
    margin-bottom: 0.8rem;
    color: #bdc3c7;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
}

.contact i {
    margin-right: 0.8rem;
    color: #0d6efd;
    width: 16px;
    text-align: center;
}

.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 0.8rem;
    transition: transform 0.3s ease;
}

.footer-links li:hover {
    transform: translateX(5px);
}

.footer-links a {
    color: #bdc3c7;
    text-decoration: none;
    transition: color 0.3s ease;
    display: flex;
    align-items: center;
}

.footer-links a:hover {
    color: #fff;
}

.footer-links i {
    margin-right: 8px;
    font-size: 0.7rem;
    color: #0d6efd;
    width: 16px;
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.social-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.social-icon:hover {
    background: #0d6efd;
    transform: translateY(-3px);
}

.newsletter h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: #ecf0f1;
}

.newsletter-form {
    display: flex;
    gap: 0.5rem;
}

.newsletter-form input {
    flex: 1;
    padding: 0.6rem 1rem;
    border: none;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    outline: none;
    transition: all 0.3s ease;
}

.newsletter-form input::placeholder {
    color: #bdc3c7;
}

.newsletter-form input:focus {
    background: rgba(255, 255, 255, 0.15);
}

.newsletter-form button {
    background: linear-gradient(90deg, #0d6efd, #6610f2);
    color: white;
    border: none;
    border-radius: 4px;
    padding: 0 1.2rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.newsletter-form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
}

.footer-bottom {
    background: rgba(0, 0, 0, 0.2);
    padding: 1.5rem 0;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.footer-bottom p {
    margin: 0;
    color: #95a5a6;
    font-size: 0.9rem;
}

.footer-link {
    color: #95a5a6;
    text-decoration: none;
    transition: color 0.3s ease;
    font-size: 0.9rem;
}

.footer-link:hover {
    color: #0d6efd;
    text-decoration: none;
}

/* Responsive Design */
@media (max-width: 992px) {
    .footer-content {
        flex-direction: column;
        gap: 2.5rem;
    }
    
    .footer-section {
        width: 100%;
        padding: 0;
    }
    
    .footer-bottom .row {
        text-align: center;
    }
    
    .footer-bottom .text-md-end {
        text-align: center !important;
        margin-top: 1rem;
    }
}
</style>
