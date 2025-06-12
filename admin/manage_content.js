// manage_content.js - Handles AJAX and modal logic for manage_content.php
// Donations
function fetchDonations() {
    fetch('content/DonationController.php?action=list')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('donation-list');
            tbody.innerHTML = '';
            if (data.success && data.donations.length) {
                data.donations.forEach(donation => {
                    // Defensive: fallback if donor is missing
                    const donor = donation.donor || { name: '', email: '', mobile: '', profile_picture: null, created_at: '' };
                    tbody.innerHTML += `<tr>
                        <td>${donation.title}</td>
                        <td>${donation.category}</td>
                        <td>${donation.date || ''}</td>
                        <td>${donor.name || ''}</td>
                        <td>
                            <button class="action-btn view-donation-btn" data-id="${donation.id}"><i class="fas fa-eye"></i> View</button>
                        </td>
                    </tr>`;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#888;">No donations found.</td></tr>';
            }
        });
}
function fetchRequests() {
    fetch('content/RequestController.php?action=list')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('request-list');
            tbody.innerHTML = '';
            if (data.success && data.requests.length) {
                data.requests.forEach(request => {
                    // Defensive: fallback if requester is missing
                    const requester = request.requester || { name: '', email: '', mobile: '', profile_picture: null, created_at: '' };
                    tbody.innerHTML += `<tr>
                        <td>${request.title}</td>
                        <td>${request.category}</td>
                        <td>${request.date || ''}</td>
                        <td>${requester.name || ''}</td>
                        <td>
                            <button class="action-btn view-request-btn" data-id="${request.id}"><i class="fas fa-eye"></i> View</button>
                        </td>
                    </tr>`;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#888;">No requests found.</td></tr>';
            }
        });
}
fetchDonations();
fetchRequests();

// Modal logic for donations
let currentDonation = null;
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('view-donation-btn')) {
        const id = e.target.getAttribute('data-id');
        fetch(`content/DonationController.php?action=get&id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    currentDonation = data.donation;
                    showDonationModal(data.donation);
                }
            });
    }
    if (e.target.classList.contains('view-request-btn')) {
        const id = e.target.getAttribute('data-id');
        // Show the request modal
        fetch('content/RequestController.php?action=get&id=' + id)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    currentRequest = data.request;
                    showRequestModal(data.request);
                } else {
                    showFlash('Failed to load request details.', true);
                }
            })
            .catch(() => showFlash('Failed to load request details.', true));
    }
});
function showDonationModal(donation) {
    document.getElementById('donationModalTitle').textContent = donation.title;
    document.getElementById('donationModalCategory').textContent = donation.category;
    document.getElementById('donationModalDate').textContent = donation.date;
    document.getElementById('donationModalDescription').textContent = donation.description;
    document.getElementById('donationModal').style.display = 'block';
    // Setup edit/delete/view donor buttons
    document.getElementById('editDonationBtn').onclick = function() {
        showEditDonationForm(donation);
    };
    document.getElementById('deleteDonationBtn').onclick = function() {
        if (!confirm('Are you sure you want to delete this donation?')) return;
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', donation.id);
        fetch('content/DonationController.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showFlash('Donation deleted successfully.');
                    fetchDonations();
                    document.getElementById('donationModal').style.display = 'none';
                } else {
                    showFlash('Failed to delete donation.', true);
                }
            });
    };
    document.getElementById('viewDonorBtn').onclick = function() {
        showUserModal(donation.donor);
    };
    document.getElementById('editDonationFormContainer').style.display = 'none';
}
function showEditDonationForm(donation) {
    document.getElementById('editDonationId').value = donation.id;
    document.getElementById('editDonationTitle').value = donation.title;
    document.getElementById('editDonationCategory').value = donation.category;
    document.getElementById('editDonationDescription').value = donation.description;
    document.getElementById('editDonationFormContainer').style.display = 'block';
}
document.getElementById('cancelEditDonationBtn').onclick = function() {
    document.getElementById('editDonationFormContainer').style.display = 'none';
};
document.getElementById('editDonationForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'update');
    fetch('content/DonationController.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showFlash('Donation updated successfully.');
                fetchDonations();
                document.getElementById('donationModal').style.display = 'none';
            } else {
                showFlash('Failed to update donation.', true);
            }
        });
};
// Modal logic for requests
let currentRequest = null;
function showRequestModal(request) {
    document.getElementById('requestModalTitle').textContent = request.title;
    document.getElementById('requestModalCategory').textContent = request.category;
    document.getElementById('requestModalDate').textContent = request.date || '';
    document.getElementById('requestModalDescription').textContent = request.description;
    document.getElementById('requestModal').style.display = 'block';
    document.getElementById('editRequestFormContainer').style.display = 'none';
    document.getElementById('editRequestBtn').onclick = function() {
        showEditRequestForm(request);
    };
    document.getElementById('deleteRequestBtn').onclick = function() {
        if (!currentRequest) return;
        if (!confirm('Are you sure you want to delete this request?')) return;
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', currentRequest.id);
        fetch('content/RequestController.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showFlash('Request deleted successfully.');
                    fetchRequests();
                    document.getElementById('requestModal').style.display = 'none';
                } else {
                    showFlash('Failed to delete request.', true);
                }
            });
    };
    document.getElementById('viewRequesterBtn').onclick = function() {
        showUserModal(request.requester);
    };
}
function showEditRequestForm(request) {
    document.getElementById('editRequestId').value = request.id;
    document.getElementById('editRequestTitle').value = request.title;
    document.getElementById('editRequestCategory').value = request.category;
    document.getElementById('editRequestDescription').value = request.description;
    document.getElementById('editRequestFormContainer').style.display = 'block';
}
document.getElementById('cancelEditRequestBtn').onclick = function() {
    document.getElementById('editRequestFormContainer').style.display = 'none';
};
document.getElementById('editRequestForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'update');
    fetch('content/RequestController.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showFlash('Request updated successfully.');
                fetchRequests();
                document.getElementById('requestModal').style.display = 'none';
            } else {
                showFlash('Failed to update request.', true);
            }
        });
};
// User modal logic
function showUserModal(user) {
    document.getElementById('userModalName').textContent = user.name;
    document.getElementById('userModalEmail').textContent = user.email;
    document.getElementById('userModalMobile').textContent = user.mobile;
    document.getElementById('userModalRegistered').textContent = user.created_at || '';
    const picContainer = document.getElementById('userModalProfilePicContainer');
    if (user.profile_picture) {
        const filename = user.profile_picture.split(/[\\/]/).pop();
        const imgSrc = '../uploads/profile_pictures/' + filename;
        const testImg = new Image();
        testImg.onload = function() {
            picContainer.innerHTML = `<img src="${imgSrc}" alt="Profile Picture" style="width:120px; height:120px; object-fit:cover; border-radius:50%; border:2px solid #eee; margin-bottom:0.5rem;">`;
        };
        testImg.onerror = function() {
            picContainer.innerHTML = '<div style="width:120px; height:120px; background:#f1f1f1; border-radius:50%; display:inline-block; line-height:120px; color:#bbb; font-size:3rem; border:2px solid #eee; margin-bottom:0.5rem;"><i class="fas fa-user"></i></div>';
        };
        testImg.src = imgSrc;
    } else {
        picContainer.innerHTML = '<div style="width:120px; height:120px; background:#f1f1f1; border-radius:50%; display:inline-block; line-height:120px; color:#bbb; font-size:3rem; border:2px solid #eee; margin-bottom:0.5rem;"><i class="fas fa-user"></i></div>';
    }
    document.getElementById('userModal').style.display = 'block';
}
// Close modals
Array.from(document.querySelectorAll('.close-modal')).forEach(btn => {
    btn.onclick = function() {
        btn.closest('.modal').style.display = 'none';
    };
});
window.onclick = function(event) {
    document.querySelectorAll('.modal').forEach(function(modal) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
};
function showFlash(msg, error) {
    const el = document.getElementById('flash-message');
    el.textContent = msg;
    el.className = 'flash-message' + (error ? ' error' : '');
    setTimeout(() => { el.textContent = ''; }, 3000);
}
