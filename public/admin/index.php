<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../../config/config.php';
require_once 'admin_model.php';
require_once 'admin_render.php'; // Include the new render file
$model = new AdminModel($conn);

$total_users = $model->getTotalUsers();
$total_requests = $model->getTotalRequests();
$request_status = $model->getRequestStatusCounts();
$total_donations = $model->getTotalDonations();
$donation_status = $model->getDonationStatusCounts();
$total_admins = $model->getTotalAdmins();

$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_user_id'])) {
    $verify_id = intval($_POST['verify_user_id']);
    $model->verifyUser($verify_id);
    $flash = '<div class="flash-message">User has been verified successfully.</div>';
}

$unverified_users = $model->getUnverifiedUsers(10);
$recent_donations = $model->getRecentAvailableDonations(5);

// Remove duplicate renderUnverifiedUsers and renderRecentDonations function definitions from this file
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Shilpa Sawiya</title>
    <link rel="stylesheet" href="../css/site-styles.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .dashboard-stats {
            display: flex;
            gap: 2rem;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            text-align: center;
            min-width: 160px;
        }
        .stat-card i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #007bff;
        }
        .site-footer {
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            text-align: center;
            padding: 1.2rem 0;
            margin-top: 3rem;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
            position:fixed;
            left:0;
            bottom:0;
            width:100%;
            z-index:100;
        }
        .admin-welcome {
            text-align: center;
            margin-bottom: 2rem;
        }
        nav.admin-nav {
            text-align: center;
            margin-bottom: 2rem;
        }
        nav.admin-nav a {
            margin: 0 1rem;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        nav.admin-nav a:hover {
            text-decoration: underline;
        }
        .flash-message {
            background: #d1e7dd;
            color: #0f5132;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            margin: 1rem 0 0.5rem 0;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <header class="site-header" style="background-color: #007bff; color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; justify-content: space-between; max-width: 1200px; margin: 0 auto; padding: 1rem 2rem;">
            <a class="navbar-brand" href="index.php" style="display: flex; align-items: center; text-decoration: none; color: #fff; font-size: 1.7rem; gap: 0.75rem;">
                <i class="fas fa-book-reader" style="font-size: 2rem;"></i>
                <span class="brand-text" style="font-weight: bold; letter-spacing: 1px; font-size: 1.3rem;">Shilpa Sawiya</span>
                <span style="font-size: 1rem; font-weight: 400; margin-left: 1.2rem; opacity: 0.85;">Admin Dashboard</span>
            </a>
            <button onclick="window.location.href='logout.php'" class="btn btn-primary" style="background: #fff; color: #007bff; border: none; font-weight: bold; padding: 0.5rem 1.2rem; border-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); transition: background 0.2s; margin-left: 2rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </header>
    <nav class="admin-nav" style="display: flex; justify-content: center; gap: 1.5rem; margin: 2rem 0 2.5rem 0;">
        <a href="index.php" class="btn btn-secondary" style="background: #fff; color: #007bff; border: 1px solid #007bff; font-weight: bold; padding: 0.5rem 1.2rem; border-radius: 4px; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s;">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="manage_users.php" class="btn btn-secondary" style="background: #fff; color: #007bff; border: 1px solid #007bff; font-weight: bold; padding: 0.5rem 1.2rem; border-radius: 4px; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s;">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="manage_content.php" class="btn btn-secondary" style="background: #fff; color: #007bff; border: 1px solid #007bff; font-weight: bold; padding: 0.5rem 1.2rem; border-radius: 4px; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s;">
            <i class="fas fa-file-alt"></i> Content
        </a>
        <a href="settings.php" class="btn btn-secondary" style="background: #fff; color: #007bff; border: 1px solid #007bff; font-weight: bold; padding: 0.5rem 1.2rem; border-radius: 4px; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s;">
            <i class="fas fa-cog"></i> Settings
        </a>
    </nav>

    <div class="admin-welcome">
        <h2>Welcome, Admin!</h2>
        <p>Manage users, content, and site settings for <strong>Shilpa Sawiya</strong>.</p>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div><strong><?php echo $total_users; ?></strong></div>
            <div>Users</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-hourglass-half"></i>
            <div><strong><?php echo $request_status['pending']; ?></strong></div>
            <div>Requests Pending</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-double"></i>
            <div><strong><?php echo $request_status['fulfilled']; ?></strong></div>
            <div>Requests Fulfilled</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-gift"></i>
            <div><strong><?php echo $donation_status['available']; ?></strong></div>
            <div>Donations Available</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <div><strong><?php echo $donation_status['completed']; ?></strong></div>
            <div>Donations Completed</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-user-shield"></i>
            <div><strong><?php echo $total_admins; ?></strong></div>
            <div>Admins</div>
        </div>
    </div>

    <main class="container">
        <section class="card">
            <div style="margin-top: 1.5rem;">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem; color: #007bff;">Unverified Users</h2>
                <?php if ($flash) echo $flash; ?>
                <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; background:#fff;">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th style="padding:0.5rem 1rem; border-bottom:1px solid #eee; text-align:left;">Name</th>
                            <th style="padding:0.5rem 1rem; border-bottom:1px solid #eee; text-align:left;">Email</th>
                            <th style="padding:0.5rem 1rem; border-bottom:1px solid #eee; text-align:left;">Mobile</th>
                            <th style="padding:0.5rem 1rem; border-bottom:1px solid #eee; text-align:left;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
<?php renderUnverifiedUsers($unverified_users); ?>
                    </tbody>
                </table>
                </div>
                <!-- User Modal Popup -->
                <div id="userModal" class="modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; overflow:auto; background:rgba(0,0,0,0.35);">
                    <div class="modal-content" style="background:#fff; margin:7% auto; padding:2rem 2.5rem; border-radius:8px; max-width:420px; position:relative; box-shadow:0 4px 24px rgba(0,0,0,0.13);">
                        <span class="close-user-modal" style="position:absolute; top:1rem; right:1.2rem; font-size:1.5rem; color:#888; cursor:pointer;">&times;</span>
                        <div id="userModalProfilePicContainer" style="text-align:center; margin-bottom:1rem;"></div>
                        <h3 id="userModalName" style="margin-bottom:0.7rem; color:#007bff;"></h3>
                        <div style="margin-bottom:0.5rem;"><strong>Email:</strong> <span id="userModalEmail"></span></div>
                        <div style="margin-bottom:0.5rem;"><strong>Mobile:</strong> <span id="userModalMobile"></span></div>
                    </div>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var userModal = document.getElementById('userModal');
                    var closeUserBtn = document.querySelector('.close-user-modal');
                    var userName = document.getElementById('userModalName');
                    var userEmail = document.getElementById('userModalEmail');
                    var userMobile = document.getElementById('userModalMobile');
                    var userProfilePicContainer = document.getElementById('userModalProfilePicContainer');
                    document.querySelectorAll('.view-user-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            userName.textContent = btn.getAttribute('data-name');
                            userEmail.textContent = btn.getAttribute('data-email');
                            userMobile.textContent = btn.getAttribute('data-mobile');
                            var pic = btn.getAttribute('data-picture');
                            if (pic) {
                                // Check if the file exists before displaying
                                var filename = pic.split(/[/\\]/).pop();
                                var imgSrc = '/uploads/profile_pictures/' + filename;
                                var testImg = new Image();
                                testImg.onload = function() {
                                    userProfilePicContainer.innerHTML = '<img src="' + imgSrc + '" alt="Profile Picture" style="width:90px; height:90px; object-fit:cover; border-radius:50%; border:2px solid #eee; margin-bottom:0.5rem;">';
                                };
                                testImg.onerror = function() {
                                    userProfilePicContainer.innerHTML = '<div style="width:90px; height:90px; background:#f1f1f1; border-radius:50%; display:inline-block; line-height:90px; color:#bbb; font-size:2.5rem; border:2px solid #eee; margin-bottom:0.5rem;"><i class="fas fa-user"></i></div>';
                                };
                                testImg.src = imgSrc;
                            } else {
                                userProfilePicContainer.innerHTML = '<div style="width:90px; height:90px; background:#f1f1f1; border-radius:50%; display:inline-block; line-height:90px; color:#bbb; font-size:2.5rem; border:2px solid #eee; margin-bottom:0.5rem;"><i class="fas fa-user"></i></div>';
                            }
                            userModal.style.display = 'block';
                        });
                    });
                    closeUserBtn.onclick = function() {
                        userModal.style.display = 'none';
                    };
                    window.onclick = function(event) {
                        if (event.target == userModal) {
                            userModal.style.display = 'none';
                        }
                    };
                });
                </script>
            </div>
        </section>
        <section class="card">
            <h2 style="margin-bottom: 1rem; font-size: 1.1rem; color: #007bff;">Available Donations</h2>
            <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; background:#fff;">
                <thead>
                    <tr style="background:#f8f9fa;">
                        <th style="padding:0.5rem 1rem; border-bottom:1px solid #eee; text-align:left;">Title</th>
                        <th style="padding:0.5rem 1rem; border-bottom:1px solid #eee; text-align:left;">Category</th>
                        <th style="padding:0.5rem 1rem; border-bottom:1px solid #eee; text-align:left;">Date</th>
                        <th style="padding:0.5rem 1rem; border-bottom:1px solid #eee; text-align:left;">Action</th>
                    </tr>
                </thead>
                <tbody>
<?php renderRecentDonations($recent_donations); ?>
                </tbody>
            </table>
            </div>
            <!-- Modal Popup -->
            <div id="donationModal" class="modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; overflow:auto; background:rgba(0,0,0,0.35);">
                <div class="modal-content" style="background:#fff; margin:7% auto; padding:2rem 2.5rem; border-radius:8px; max-width:480px; position:relative; box-shadow:0 4px 24px rgba(0,0,0,0.13);">
                    <span class="close-modal" style="position:absolute; top:1rem; right:1.2rem; font-size:1.5rem; color:#888; cursor:pointer;">&times;</span>
                    <h3 id="modalTitle" style="margin-bottom:0.7rem; color:#007bff;"></h3>
                    <div style="margin-bottom:0.5rem;"><strong>Category:</strong> <span id="modalCategory"></span></div>
                    <div style="margin-bottom:0.5rem;"><strong>Date:</strong> <span id="modalDate"></span></div>
                    <div style="margin-bottom:0.5rem;"><strong>Description:</strong></div>
                    <div id="modalDescription" style="white-space:pre-line; color:#444;"></div>
                </div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = document.getElementById('donationModal');
                var closeBtn = document.querySelector('.close-modal');
                var title = document.getElementById('modalTitle');
                var category = document.getElementById('modalCategory');
                var date = document.getElementById('modalDate');
                var description = document.getElementById('modalDescription');
                document.querySelectorAll('.view-donation-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        title.textContent = btn.getAttribute('data-title');
                        category.textContent = btn.getAttribute('data-category');
                        date.textContent = btn.getAttribute('data-date');
                        description.textContent = btn.getAttribute('data-description');
                        modal.style.display = 'block';
                    });
                });
                closeBtn.onclick = function() {
                    modal.style.display = 'none';
                };
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = 'none';
                    }
                };
            });
            </script>
        </section>
    </main>
    <footer class="site-footer" style="background: linear-gradient(90deg, #007bff 0%, #0056b3 100%); color: #fff; text-align: center; padding: 1.2rem 0 1.2rem 0; margin-top: 3rem; box-shadow: 0 -2px 8px rgba(0,0,0,0.04); position:fixed; left:0; bottom:0; width:100%; z-index:100;">
        <div style="max-width: 900px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.5rem;">
            <div style="font-size: 1.1rem; font-weight: 500; letter-spacing: 0.5px;">
                &copy; <?php echo date('Y'); ?> <span style="font-weight:bold;">Shilpa Sawiya</span> Admin Panel
            </div>
            <div style="font-size: 0.98rem; opacity: 0.85;">
                <a href="mailto:info@shilpasawiya.lk" style="color: #fff; text-decoration: underline; margin-right: 1.2rem;"><i class="fas fa-envelope"></i> Contact Support</a>
                <a href="../" style="color: #fff; text-decoration: underline;"><i class="fas fa-globe"></i> Main Site</a>
            </div>
            <div style="font-size: 0.95rem; opacity: 0.7;">
                Made with <i class="fas fa-heart" style="color:#ffb3b3;"></i> for Education &amp; Community
            </div>
        </div>
    </footer>
</body>
</html>
