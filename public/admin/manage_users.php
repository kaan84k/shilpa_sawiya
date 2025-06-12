<?php
// filepath: c:/xampp/htdocs/shilpa-sawiya/admin/manage_users.php
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
    <title>Manage Users | Shilpa Sawiya</title>
    <link rel="stylesheet" href="../css/site-styles.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .user-table th, .user-table td { padding: 0.6rem 1rem; border-bottom: 1px solid #eee; }
        .user-table th { background: #f8f9fa; }
        .user-table { width: 100%; border-collapse: collapse; background: #fff; }
        .action-btn { background: #fff; color: #007bff; border: 1px solid #007bff; font-weight: bold; padding: 0.3rem 0.8rem; border-radius: 4px; text-decoration: none; margin-right: 0.5rem; cursor: pointer; transition: background 0.2s; }
        .action-btn:hover { background: #007bff; color: #fff; }
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; overflow: auto; background: rgba(0,0,0,0.35); }
        .modal-content { background: #fff; margin: 7% auto; padding: 2rem 2.5rem; border-radius: 8px; max-width: 420px; position: relative; box-shadow: 0 4px 24px rgba(0,0,0,0.13); }
        .close-modal { position: absolute; top: 1rem; right: 1.2rem; font-size: 1.5rem; color: #888; cursor: pointer; }
        .flash-message { background: #d1e7dd; color: #0f5132; padding: 0.75rem 1.5rem; border-radius: 5px; margin: 1rem 0 0.5rem 0; font-weight: 500; text-align: center; }
        footer.site-footer {
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            text-align: center;
            padding: 1.2rem 0;
            margin-top: 3rem;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            z-index: 100;
        }
        footer.site-footer div {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
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
    <main class="container">
        <h2 style="margin-bottom: 1.5rem; color: #007bff; text-align:center;">Manage Users</h2>
        <div id="flash-message"></div>
        <div style="overflow-x:auto;">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="user-list">
                    <tr><td colspan="5" style="text-align:center; color:#888;">Loading users...</td></tr>
                </tbody>
            </table>
        </div>
        <!-- User View/Edit Modal -->
        <div id="userModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div id="userModalProfilePicContainer" style="text-align:center; margin-bottom:1rem;"></div>
                <h3 id="userModalName" style="margin-bottom:0.7rem; color:#007bff;"></h3>
                <div style="margin-bottom:0.5rem;"><strong>Email:</strong> <span id="userModalEmail"></span></div>
                <div style="margin-bottom:0.5rem;"><strong>Mobile:</strong> <span id="userModalMobile"></span></div>
                <div style="margin-bottom:0.5rem;"><strong>Registered:</strong> <span id="userModalRegistered"></span></div>
                <div id="editUserFormContainer" style="display:none; margin-top:1.2rem;">
                    <form id="editUserForm">
                        <input type="hidden" name="id" id="editUserId">
                        <div style="margin-bottom:0.7rem;">
                            <label>Name:</label><br>
                            <input type="text" name="name" id="editUserName" style="width:100%; padding:0.4rem;">
                        </div>
                        <div style="margin-bottom:0.7rem;">
                            <label>Email:</label><br>
                            <input type="email" name="email" id="editUserEmail" style="width:100%; padding:0.4rem;">
                        </div>
                        <div style="margin-bottom:0.7rem;">
                            <label>Mobile:</label><br>
                            <input type="text" name="mobile" id="editUserMobile" style="width:100%; padding:0.4rem;">
                        </div>
                        <button type="submit" class="action-btn" style="background:#007bff; color:#fff;">Save Changes</button>
                        <button type="button" id="cancelEditBtn" class="action-btn">Cancel</button>
                    </form>
                </div>
                <div id="modalActions" style="margin-top:1.2rem; text-align:center;">
                    <button id="editUserBtn" class="action-btn"><i class="fas fa-edit"></i> Edit</button>
                    <button id="deleteUserBtn" class="action-btn" style="color:#dc3545; border-color:#dc3545;"><i class="fas fa-trash"></i> Delete</button>
                </div>
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
    <script>
    // Fetch and render users
    function fetchUsers() {
        fetch('user/UserController.php?action=list')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('user-list');
                tbody.innerHTML = '';
                if (data.success && data.users.length) {
                    data.users.forEach(user => {
                        tbody.innerHTML += `<tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.mobile}</td>
                            <td>${user.created_at ? user.created_at : ''}</td>
                            <td>
                                <button class="action-btn view-user-btn" data-id="${user.id}"><i class="fas fa-eye"></i> View</button>
                            </td>
                        </tr>`;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#888;">No users found.</td></tr>';
                }
            });
    }
    fetchUsers();

    // Modal logic
    let currentUser = null;
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-user-btn')) {
            const id = e.target.getAttribute('data-id');
            fetch(`user/UserController.php?action=get&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        currentUser = data.user;
                        showUserModal(data.user);
                    }
                });
        }
    });
    function showUserModal(user) {
        document.getElementById('userModalName').textContent = user.name;
        document.getElementById('userModalEmail').textContent = user.email;
        document.getElementById('userModalMobile').textContent = user.mobile;
        document.getElementById('userModalRegistered').textContent = user.created_at || '';
        document.getElementById('editUserFormContainer').style.display = 'none';
        document.getElementById('modalActions').style.display = 'block';
        // Profile pic
        const picContainer = document.getElementById('userModalProfilePicContainer');
        if (user.profile_picture) {
            // Use correct relative path for subdirectory admin pages
            const filename = user.profile_picture.split(/[\\/]/).pop();
            // If on /admin/*, use '../uploads/profile_pictures/'
            const imgSrc = '../uploads/profile_pictures/' + filename;
            const testImg = new Image();
            testImg.onload = function() {
                picContainer.innerHTML = `<img src="${imgSrc}" alt="Profile Picture" style="width:160px; height:160px; object-fit:cover; border-radius:50%; border:3px solid #eee; margin-bottom:0.5rem;">`;
            };
            testImg.onerror = function() {
                picContainer.innerHTML = '<div style="width:160px; height:160px; background:#f1f1f1; border-radius:50%; display:inline-block; line-height:160px; color:#bbb; font-size:4rem; border:3px solid #eee; margin-bottom:0.5rem;"><i class="fas fa-user"></i></div>';
            };
            testImg.src = imgSrc;
        } else {
            picContainer.innerHTML = '<div style="width:160px; height:160px; background:#f1f1f1; border-radius:50%; display:inline-block; line-height:160px; color:#bbb; font-size:4rem; border:3px solid #eee; margin-bottom:0.5rem;"><i class="fas fa-user"></i></div>';
        }
        document.getElementById('userModal').style.display = 'block';
    }
    document.querySelector('.close-modal').onclick = function() {
        document.getElementById('userModal').style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == document.getElementById('userModal')) {
            document.getElementById('userModal').style.display = 'none';
        }
    };
    // Edit logic
    document.getElementById('editUserBtn').onclick = function() {
        if (!currentUser) return;
        document.getElementById('editUserId').value = currentUser.id;
        document.getElementById('editUserName').value = currentUser.name;
        document.getElementById('editUserEmail').value = currentUser.email;
        document.getElementById('editUserMobile').value = currentUser.mobile;
        document.getElementById('editUserFormContainer').style.display = 'block';
        document.getElementById('modalActions').style.display = 'none';
    };
    document.getElementById('cancelEditBtn').onclick = function() {
        document.getElementById('editUserFormContainer').style.display = 'none';
        document.getElementById('modalActions').style.display = 'block';
    };
    document.getElementById('editUserForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'update');
        fetch('user/UserController.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showFlash('User updated successfully.');
                fetchUsers();
                document.getElementById('userModal').style.display = 'none';
            } else {
                showFlash('Failed to update user.', true);
            }
        });
    };
    // Delete logic
    document.getElementById('deleteUserBtn').onclick = function() {
        if (!currentUser) return;
        if (!confirm('Are you sure you want to delete this user?')) return;
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', currentUser.id);
        fetch('user/UserController.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showFlash('User deleted successfully.');
                fetchUsers();
                document.getElementById('userModal').style.display = 'none';
            } else {
                showFlash('Failed to delete user.', true);
            }
        });
    };
    function showFlash(msg, error) {
        const el = document.getElementById('flash-message');
        el.textContent = msg;
        el.className = 'flash-message' + (error ? ' error' : '');
        setTimeout(() => { el.textContent = ''; }, 3000);
    }
    </script>
</body>
</html>
