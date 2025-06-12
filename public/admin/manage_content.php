<?php
// manage_content.php - Admin page for managing donations and requests
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Content | Shilpa Sawiya</title>
    <link rel="stylesheet" href="../css/site-styles.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .dashboard-stats { display: flex; gap: 2rem; justify-content: center; margin-bottom: 2rem; }
        .stat-card { background: #f8f9fa; border-radius: 8px; padding: 1.5rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.07); text-align: center; min-width: 160px; }
        .stat-card i { font-size: 2rem; margin-bottom: 0.5rem; color: #007bff; }
        .site-footer { background: linear-gradient(90deg, #007bff 0%, #0056b3 100%); color: #fff; text-align: center; padding: 1.2rem 0; margin-top: 3rem; box-shadow: 0 -2px 8px rgba(0,0,0,0.04); position:fixed; left:0; bottom:0; width:100%; z-index:100; }
        .admin-welcome { text-align: center; margin-bottom: 2rem; }
        nav.admin-nav { text-align: center; margin-bottom: 2rem; }
        nav.admin-nav a { margin: 0 1rem; color: #007bff; text-decoration: none; font-weight: bold; }
        nav.admin-nav a:hover { text-decoration: underline; }
        .flash-message { background: #d1e7dd; color: #0f5132; padding: 0.75rem 1.5rem; border-radius: 5px; margin: 1rem 0 0.5rem 0; font-weight: 500; }
        .action-btn { background: #fff; color: #007bff; border: 1px solid #007bff; font-weight: bold; padding: 0.3rem 0.8rem; border-radius: 4px; text-decoration: none; margin-right: 0.5rem; cursor: pointer; transition: background 0.2s; }
        .action-btn:hover { background: #007bff; color: #fff; }
        .user-table th, .user-table td { padding: 0.6rem 1rem; border-bottom: 1px solid #eee; }
        .user-table th { background: #f8f9fa; }
        .user-table { width: 100%; border-collapse: collapse; background: #fff; }
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; overflow: auto; background: rgba(0,0,0,0.35); }
        .modal-content { background: #fff; margin: 7% auto; padding: 2rem 2.5rem; border-radius: 8px; max-width: 480px; position: relative; box-shadow: 0 4px 24px rgba(0,0,0,0.13); }
        .close-modal { position: absolute; top: 1rem; right: 1.2rem; font-size: 1.5rem; color: #888; cursor: pointer; }
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
    <main class="container">
        <h2 style="margin-bottom: 1.5rem; color: #007bff; text-align:center;">Manage Content</h2>
        <div id="flash-message"></div>
        <section class="card">
            <h3 style="color:#007bff;">Donations</h3>
            <div style="overflow-x:auto;">
                <table class="user-table" id="donation-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Donor</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="donation-list">
                        <tr><td colspan="5" style="text-align:center; color:#888;">Loading donations...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="card">
            <h3 style="color:#007bff;">Requests</h3>
            <div style="overflow-x:auto;">
                <table class="user-table" id="request-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Requester</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="request-list">
                        <tr><td colspan="5" style="text-align:center; color:#888;">Loading requests...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
        <!-- Donation Modal -->
        <div id="donationModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h3 id="donationModalTitle" style="margin-bottom:0.7rem; color:#007bff;"></h3>
                <div style="margin-bottom:0.5rem;"><strong>Category:</strong> <span id="donationModalCategory"></span></div>
                <div style="margin-bottom:0.5rem;"><strong>Date:</strong> <span id="donationModalDate"></span></div>
                <div style="margin-bottom:0.5rem;"><strong>Description:</strong></div>
                <div id="donationModalDescription" style="white-space:pre-line; color:#444;"></div>
                <div style="margin-top:1.2rem;">
                    <button id="editDonationBtn" class="action-btn"><i class="fas fa-edit"></i> Edit</button>
                    <button id="deleteDonationBtn" class="action-btn" style="color:#dc3545; border-color:#dc3545;"><i class="fas fa-trash"></i> Delete</button>
                    <button id="viewDonorBtn" class="action-btn"><i class="fas fa-user"></i> View Donor</button>
                </div>
                <div id="editDonationFormContainer" style="display:none; margin-top:1.2rem;">
                    <form id="editDonationForm">
                        <input type="hidden" name="id" id="editDonationId">
                        <div style="margin-bottom:0.7rem;">
                            <label>Title:</label><br>
                            <input type="text" name="title" id="editDonationTitle" style="width:100%; padding:0.4rem;">
                        </div>
                        <div style="margin-bottom:0.7rem;">
                            <label>Category:</label><br>
                            <input type="text" name="category" id="editDonationCategory" style="width:100%; padding:0.4rem;">
                        </div>
                        <div style="margin-bottom:0.7rem;">
                            <label>Description:</label><br>
                            <textarea name="description" id="editDonationDescription" style="width:100%; padding:0.4rem;"></textarea>
                        </div>
                        <button type="submit" class="action-btn" style="background:#007bff; color:#fff;">Save Changes</button>
                        <button type="button" id="cancelEditDonationBtn" class="action-btn">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Request Modal -->
        <div id="requestModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h3 id="requestModalTitle" style="margin-bottom:0.7rem; color:#007bff;"></h3>
                <div style="margin-bottom:0.5rem;"><strong>Category:</strong> <span id="requestModalCategory"></span></div>
                <div style="margin-bottom:0.5rem;"><strong>Date:</strong> <span id="requestModalDate"></span></div>
                <div style="margin-bottom:0.5rem;"><strong>Description:</strong></div>
                <div id="requestModalDescription" style="white-space:pre-line; color:#444;"></div>
                <div style="margin-top:1.2rem;">
                    <button id="editRequestBtn" class="action-btn"><i class="fas fa-edit"></i> Edit</button>
                    <button id="deleteRequestBtn" class="action-btn" style="color:#dc3545; border-color:#dc3545;"><i class="fas fa-trash"></i> Delete</button>
                    <button id="viewRequesterBtn" class="action-btn"><i class="fas fa-user"></i> View Requester</button>
                </div>
                <div id="editRequestFormContainer" style="display:none; margin-top:1.2rem;">
                    <form id="editRequestForm">
                        <input type="hidden" name="id" id="editRequestId">
                        <div style="margin-bottom:0.7rem;">
                            <label>Title:</label><br>
                            <input type="text" name="title" id="editRequestTitle" style="width:100%; padding:0.4rem;">
                        </div>
                        <div style="margin-bottom:0.7rem;">
                            <label>Category:</label><br>
                            <input type="text" name="category" id="editRequestCategory" style="width:100%; padding:0.4rem;">
                        </div>
                        <div style="margin-bottom:0.7rem;">
                            <label>Description:</label><br>
                            <textarea name="description" id="editRequestDescription" style="width:100%; padding:0.4rem;"></textarea>
                        </div>
                        <button type="submit" class="action-btn" style="background:#007bff; color:#fff;">Save Changes</button>
                        <button type="button" id="cancelEditRequestBtn" class="action-btn">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- User Modal (for donor/requester) -->
        <div id="userModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div id="userModalProfilePicContainer" style="text-align:center; margin-bottom:1rem;"></div>
                <h3 id="userModalName" style="margin-bottom:0.7rem; color:#007bff;"></h3>
                <div style="margin-bottom:0.5rem;"><strong>Email:</strong> <span id="userModalEmail"></span></div>
                <div style="margin-bottom:0.5rem;"><strong>Mobile:</strong> <span id="userModalMobile"></span></div>
                <div style="margin-bottom:0.5rem;"><strong>Registered:</strong> <span id="userModalRegistered"></span></div>
            </div>
        </div>
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
    <script src="manage_content.js"></script>
</body>
</html>
