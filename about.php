<?php
session_start();
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - FoodExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- About Hero Section -->
    <section class="py-5" style="margin-top: 80px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">About FoodExpress</h1>
                    <p class="lead text-white mb-4">
                        We're passionate about connecting you with the best food in Nairobi. 
                        Our mission is to deliver delicious meals right to your doorstep with speed, 
                        quality, and convenience.
                    </p>
                    <a href="menu.php" class="btn btn-light btn-lg">
                        <i class="fas fa-utensils me-2"></i>Explore Our Menu
                    </a>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/about-hero.jpg" alt="FoodExpress Team" class="img-fluid rounded-3 shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">Our Story</h2>
                    <p class="lead text-muted">How it all began</p>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-4">From Kitchen to Your Doorstep</h3>
                    <p class="mb-4">
                        FoodExpress was born from a simple idea: making great food accessible to everyone. 
                        Founded in 2020, we started as a small team passionate about connecting local restaurants 
                        with food lovers across Nairobi.
                    </p>
                    <p class="mb-4">
                        Today, we partner with over 50+ restaurants and serve thousands of customers daily. 
                        Our commitment to quality, speed, and customer satisfaction remains at the heart of everything we do.
                    </p>
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="fw-bold text-primary">50+</h4>
                            <p class="text-muted">Restaurant Partners</p>
                        </div>
                        <div class="col-4">
                            <h4 class="fw-bold text-primary">10,000+</h4>
                            <p class="text-muted">Happy Customers</p>
                        </div>
                        <div class="col-4">
                            <h4 class="fw-bold text-primary">30min</h4>
                            <p class="text-muted">Average Delivery</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/our-story.jpg" alt="Our Story" class="img-fluid rounded-3 shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold">Our Values</h2>
                    <p class="lead text-muted">What drives us forward</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-heart fa-3x text-danger"></i>
                            </div>
                            <h5 class="card-title">Quality First</h5>
                            <p class="card-text">We partner only with the best restaurants and ensure every meal meets our high quality standards.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-clock fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Speed & Efficiency</h5>
                            <p class="card-text">We understand that time is precious. That's why we deliver your food within 30 minutes or it's free.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-users fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title">Customer Focus</h5>
                            <p class="card-text">Your satisfaction is our priority. We're here to make your food ordering experience seamless and enjoyable.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold">Meet Our Team</h2>
                    <p class="lead text-muted">The people behind FoodExpress</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <img src="assets/images/team-1.jpg" alt="CEO" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            <h5 class="card-title">John Doe</h5>
                            <p class="text-muted">CEO & Founder</p>
                            <p class="card-text">Passionate about revolutionizing food delivery in Kenya.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <img src="assets/images/team-2.jpg" alt="CTO" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            <h5 class="card-title">Jane Smith</h5>
                            <p class="text-muted">CTO</p>
                            <p class="card-text">Leading our technology innovation and platform development.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <img src="assets/images/team-3.jpg" alt="Operations" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            <h5 class="card-title">Mike Johnson</h5>
                            <p class="text-muted">Head of Operations</p>
                            <p class="card-text">Ensuring smooth operations and excellent customer service.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Ready to Order?</h2>
            <p class="lead mb-4">Join thousands of satisfied customers and experience the best food delivery in Nairobi.</p>
            <a href="menu.php" class="btn btn-light btn-lg me-3">
                <i class="fas fa-utensils me-2"></i>Order Now
            </a>
            <a href="contact.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-phone me-2"></i>Contact Us
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 