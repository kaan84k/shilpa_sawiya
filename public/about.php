<?php
session_start();
require_once '../config/config.php';
use App\Models\Donation;

// Get completed donations for success stories
$donation = new Donation($conn);
$completed_donations = $donation->getCompletedDonationsWithUsers(6); // Get 6 most recent completed donations
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .vision-card, .mission-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
        .vision-card:hover, .mission-card:hover {
            transform: translateY(-10px);
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        .success-story-card {
            overflow: hidden;
        .success-story-card:hover {
            transform: translateY(-5px);
        .story-img-container {
            height: 200px;
        .story-img {
            width: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        .success-story-card:hover .story-img {
            transform: scale(1.05);
        .donor-avatar {
            width: 60px;
            height: 60px;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    </style>
</head>
<body>
    <?php include '../src/Views/header.php'; ?>
    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">About Shilpa Sawiya</h1>
            <p class="lead">Bridging the gap between generosity and need through the power of education</p>
        </div>
    </section>
    <!-- About Us Section -->
    <section class="py-5">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-4">Our Story</h2>
                    <p class="lead text-muted">Shilpa Sawiya was born from a simple idea: to make educational resources accessible to everyone, regardless of their economic background. What started as a small initiative has grown into a community of thousands of donors and recipients, all working together to spread the joy of learning.</p>
                    <p>We believe that every book, every notebook, and every educational tool has the power to change lives. By connecting those who have with those who need, we're building a more educated and empowered society, one donation at a time.</p>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/about-education.jpg" alt="Education for all" class="img-fluid rounded-3 shadow">
            </div>
    <!-- Vision & Mission -->
    <section class="py-5 bg-light">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Our Vision & Mission</h2>
                <p class="lead text-muted">Guiding principles that drive our work</p>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card vision-card p-4 h-100">
                        <div class="icon-circle bg-primary">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="card-body text-center">
                            <h3 class="h4 card-title mb-3">Our Vision</h3>
                            <p class="card-text">To create a world where every individual has access to the educational resources they need to learn, grow, and achieve their full potential, regardless of their economic circumstances.</p>
                    </div>
                    <div class="card mission-card p-4 h-100">
                        <div class="icon-circle bg-success">
                            <i class="fas fa-bullseye"></i>
                            <h3 class="h4 card-title mb-3">Our Mission</h3>
                            <p class="card-text">To connect generous donors with students and educators in need, creating a sustainable cycle of giving that empowers individuals and strengthens communities through education.</p>
    <!-- Success Stories -->
                <h2 class="fw-bold">Success Stories</h2>
                <p class="lead text-muted">See how your donations are making a difference</p>
            
            <?php if (!empty($completed_donations)): ?>
                <div class="row g-4">
                    <?php foreach ($completed_donations as $donation): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="success-story-card">
                                <div class="story-img-container">
                                    <?php if (!empty($donation['image'])): ?>
                                        <img src="uploads/donations/<?php echo htmlspecialchars($donation['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($donation['title']); ?>" 
                                             class="story-img">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-book fa-4x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($donation['title']); ?></h5>
                                    <p class="card-text text-muted"><?php echo substr(htmlspecialchars($donation['description']), 0, 100); ?>...</p>
                                    <div class="d-flex align-items-center mt-3">
                                        <?php 
                                        $profilePic = !empty($donation['profile_picture']) 
                                            ? 'uploads/profile_pictures/' . htmlspecialchars($donation['profile_picture'])
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($donation['user_name']) . '&size=100';
                                        ?>
                                        <img src="<?php echo $profilePic; ?>"
                                             alt="<?php echo htmlspecialchars($donation['user_name']); ?>"
                                             class="donor-avatar me-3 img-cover w-40 h-40 rounded-circle">
                                        <div>
                                            <h6 class="mb-0">Donated by <?php echo htmlspecialchars($donation['user_name']); ?></h6>
                                            <small class="text-muted"><?php echo date('F j, Y', strtotime($donation['updated_at'])); ?></small>
                                    </div>
                            </div>
                    <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-book-open-reader fa-4x text-muted mb-3"></i>
                    <h4>No success stories yet</h4>
                    <p class="text-muted">Be the first to create a success story by donating or requesting books!</p>
                    <a href="donate.php" class="btn btn-primary mt-3">Donate Now</a>
            <?php endif; ?>
    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">Join Our Community</h2>
            <p class="lead mb-4">Be part of our mission to make education accessible to everyone</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="donate.php" class="btn btn-light btn-lg px-4">Donate Items</a>
                <a href="donations.php" class="btn btn-outline-light btn-lg px-4">Find Donations</a>
    <?php include '../src/Views/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
