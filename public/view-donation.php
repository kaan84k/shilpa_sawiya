<?php
session_start();
require_once '../config/config.php';
use App\Models\Donation;
use App\Models\UserAuth;

// Check if donation ID is provided
if (!isset($_GET['id'])) {
    header("Location: donations.php");
    exit();
}
$donation_id = $_GET['id'];
$donation = new Donation($conn);
$donation_details = $donation->getDonationById($donation_id);
// Check if donation exists
if (!$donation_details) {
// Get user details
$userAuth = new UserAuth($conn);
$donor = $userAuth->getUserById($donation_details['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($donation_details['title']); ?> - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .editable-field {
            cursor: pointer;
            border-bottom: 1px dashed #6c757d;
            padding: 0.2em 0;
        }
        .editable-field:hover {
            background-color: #f8f9fa;
        #donationImagePreview {
            max-height: 300px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
    </style>
</head>
<body>
    <?php include '../src/Views/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if(!empty($donation_details['image'])): ?>
                            <img src="uploads/donations/<?php echo htmlspecialchars($donation_details['image']); ?>" 
                                 class="img-fluid mb-4" alt="<?php echo htmlspecialchars($donation_details['title']); ?>">
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="card-title mb-0" id="donationTitle"><?php echo htmlspecialchars($donation_details['title']); ?></h2>
                            <?php if(isset($_SESSION['user_id']) && $donation_details['user_id'] == $_SESSION['user_id']): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="editDonationBtn">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                            <?php endif; ?>
                        </div>
                        <p class="card-text" id="donationDescription"><?php echo nl2br(htmlspecialchars($donation_details['description'])); ?></p>
                        <div class="mt-4">
                            <h5>Donation Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong>Category:</strong> 
                                        <span class="editable-field" data-field="category"><?php echo htmlspecialchars($donation_details['category']); ?></span>
                                    </p>
                                        <strong>Condition:</strong> 
                                        <span class="editable-field" data-field="condition"><?php echo htmlspecialchars($donation_details['condition']); ?></span>
                                        <strong>Location:</strong> 
                                        <span class="editable-field" data-field="location"><?php echo htmlspecialchars($donation_details['location']); ?></span>
                                </div>
                                    <p><strong>Posted By:</strong> <?php echo htmlspecialchars($donor['name']); ?></p>
                                    <p><strong>Posted On:</strong> <?php echo date('F j, Y', strtotime($donation_details['created_at'])); ?></p>
                                    <p><strong>Status:</strong> <span class="badge bg-<?php echo $donation_details['status'] === 'available' ? 'success' : ($donation_details['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst($donation_details['status']); ?>
                                    </span></p>
                            </div>
                        <?php if(isset($_SESSION['user_id']) && $donation_details['user_id'] != $_SESSION['user_id']): ?>
                            <form id="requestDonationForm" class="mt-4">
                                <input type="hidden" name="donation_id" value="<?php echo $donation_id; ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-hand-holding-heart me-1"></i> Request Item
                            </form>
                            <div id="requestAlert" class="mt-2"></div>
                        <?php if(isset($_SESSION['user_id']) && $donation_details['user_id'] == $_SESSION['user_id']): ?>
                            <div class="mt-4">
                                <button type="button" class="btn btn-danger" id="deleteDonationBtn" data-id="<?php echo $donation_id; ?>">
                                    <i class="fas fa-trash me-1"></i> Delete Donation
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                        <h5 class="card-title">Donor Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($donor['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($donor['email']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($donor['location']); ?></p>
                        <p><strong>Member Since:</strong> <?php echo date('F Y', strtotime($donor['created_at'])); ?></p>
        </div>
    </div>
    <!-- Edit Donation Modal -->
    <div class="modal fade" id="editDonationModal" tabindex="-1" aria-labelledby="editDonationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editDonationModalLabel">Edit Donation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                <form id="editDonationForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $donation_id; ?>">
                    <div class="modal-body">
                        <div id="editDonationAlert" class="alert d-none"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editTitle" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="editTitle" name="title" value="<?php echo htmlspecialchars($donation_details['title']); ?>" required>
                                
                                    <label for="editCategory" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="editCategory" name="category" required>
                                        <option value="" disabled>Select a category</option>
                                        <option value="Books" <?php echo $donation_details['category'] === 'Books' ? 'selected' : ''; ?>>Books</option>
                                        <option value="Stationery" <?php echo $donation_details['category'] === 'Stationery' ? 'selected' : ''; ?>>Stationery</option>
                                        <option value="Uniforms" <?php echo $donation_details['category'] === 'Uniforms' ? 'selected' : ''; ?>>Uniforms</option>
                                        <option value="Bags" <?php echo $donation_details['category'] === 'Bags' ? 'selected' : ''; ?>>Bags</option>
                                        <option value="Electronics" <?php echo $donation_details['category'] === 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                                    </select>
                                    <label for="editCondition" class="form-label">Condition <span class="text-danger">*</span></label>
                                    <select class="form-select" id="editCondition" name="condition" required>
                                        <option value="New" <?php echo $donation_details['condition'] === 'New' ? 'selected' : ''; ?>>New</option>
                                        <option value="Like New" <?php echo $donation_details['condition'] === 'Like New' ? 'selected' : ''; ?>>Like New</option>
                                        <option value="Very Good" <?php echo $donation_details['condition'] === 'Very Good' ? 'selected' : ''; ?>>Very Good</option>
                                        <option value="Good" <?php echo $donation_details['condition'] === 'Good' ? 'selected' : ''; ?>>Good</option>
                                        <option value="Acceptable" <?php echo $donation_details['condition'] === 'Acceptable' ? 'selected' : ''; ?>>Acceptable</option>
                                    <label for="editLocation" class="form-label">Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="editLocation" name="location" value="<?php echo htmlspecialchars($donation_details['location']); ?>" required>
                                    <label for="editStatus" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="editStatus" name="status" required>
                                        <option value="available" <?php echo $donation_details['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                        <option value="pending" <?php echo $donation_details['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="completed" <?php echo $donation_details['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <label for="editDescription" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="editDescription" name="description" rows="8" required><?php echo htmlspecialchars($donation_details['description']); ?></textarea>
                                    <label for="editImage" class="form-label">Update Image (Optional)</label>
                                    <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                                    <div class="form-text">Leave empty to keep current image</div>
                                    <?php if(!empty($donation_details['image'])): ?>
                                        <div class="mt-2">
                                            <p class="mb-1">Current Image:</p>
                                            <img src="uploads/donations/<?php echo htmlspecialchars($donation_details['image']); ?>" 
                                                 class="img-thumbnail" style="max-height: 150px;" alt="Current Image">
                                        </div>
                                    <?php endif; ?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="saveDonationBtn">
                            <i class="fas fa-save me-1"></i> Save Changes
                </form>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                <div class="modal-body">
                    <p>Are you sure you want to delete this donation? This action cannot be undone.</p>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
    <?php include '../src/Views/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        // Handle donation request form submission
        $('#requestDonationForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const alertDiv = $('#requestAlert');
            // Show loading state
            $(this).find('button[type="submit"]')
                .prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...');
            // Clear previous alerts
            alertDiv.removeClass('alert-success alert-danger').addClass('d-none').text('');
            // Submit the request
            $.ajax({
                url: 'request-donation.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message and redirect to dashboard's My Requests section
                        alertDiv.removeClass('d-none alert-danger').addClass('alert-success')
                               .html('<i class="fas fa-check-circle me-2"></i>' + response.message);
                        // Redirect to dashboard's My Requests section after a short delay
                        setTimeout(function() {
                            window.location.href = 'dashboard.php#requests';
                        }, 1500);
                    } else {
                        // Show error message
                        alertDiv.removeClass('d-none').addClass('alert-danger')
                               .html('<i class="fas fa-exclamation-circle me-2"></i>' + response.message);
                        // Re-enable the submit button
                        $('#requestDonationForm button[type="submit"]')
                            .prop('disabled', false)
                            .html('<i class="fas fa-hand-holding-heart me-1"></i> Request Item');
                    }
                },
                error: function() {
                    // Show error message on AJAX failure
                    alertDiv.removeClass('d-none').addClass('alert-danger')
                           .html('<i class="fas fa-exclamation-circle me-2"></i> An error occurred. Please try again.');
                    
                    // Re-enable the submit button
                    $('#requestDonationForm button[type="submit"]')
                        .prop('disabled', false)
                        .html('<i class="fas fa-hand-holding-heart me-1"></i> Request Item');
                }
            });
        });
        
        // Rest of the document ready code
        // Initialize Select2 for better dropdowns
        $('#editCategory, #editCondition, #editStatus').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#editDonationModal')
        // Handle edit button click
        $('#editDonationBtn').click(function() {
            $('#editDonationModal').modal('show');
        // Handle delete button click
        $('#deleteDonationBtn').click(function() {
            $('#deleteConfirmationModal').modal('show');
            // Handle delete confirmation
            $('#confirmDeleteBtn').off('click').on('click', function() {
                const donationId = $('#deleteDonationBtn').data('id');
                const deleteBtn = $(this);
                const originalBtnText = deleteBtn.html();
                
                // Show loading state
                deleteBtn.prop('disabled', true)
                       .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Deleting...');
                // Send AJAX request to delete the donation
                $.ajax({
                    url: 'delete-donation.php',
                    type: 'POST',
                    data: { donation_id: donationId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            const alertHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i> ${response.message || 'Donation deleted successfully!'}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`;
                            
                            // Insert alert at the top of the container
                            $('.container').prepend(alertHtml);
                            // Redirect to dashboard after a short delay
                            setTimeout(() => {
                                window.location.href = 'dashboard.php';
                            }, 1500);
                        } else {
                            throw new Error(response.message || 'Failed to delete donation');
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'An error occurred while deleting the donation';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        const alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i> ${errorMessage}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`;
                        // Insert alert at the top of the container
                        $('.container').prepend(alertHtml);
                    complete: function() {
                        // Re-enable the button and restore original text
                        deleteBtn.prop('disabled', false).html(originalBtnText);
                        // Hide the modal
                        $('#deleteConfirmationModal').modal('hide');
                });
        // Handle image preview when a new image is selected
        $('#editImage').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#currentImagePreview').attr('src', e.target.result);
                reader.readAsDataURL(file);
            }
        // Handle form submission
        $('#editDonationForm').submit(function(e) {
            const formData = new FormData(this);
            const saveBtn = $('#saveDonationBtn');
            const alertDiv = $('#editDonationAlert');
            const originalBtnText = saveBtn.html();
            saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');
            alertDiv.addClass('d-none').removeClass('alert-success alert-danger').text('');
            // Send AJAX request
                url: 'update-donation.php',
                processData: false,
                contentType: false,
                        // Update the page with new data
                        $('#donationTitle').text($('#editTitle').val());
                        $('#donationDescription').html($('#editDescription').val().replace(/\n/g, '<br>'));
                        // Update other fields if needed
                        $('[data-field="category"]').text($('#editCategory').val());
                        $('[data-field="condition"]').text($('#editCondition').val());
                        $('[data-field="location"]').text($('#editLocation').val());
                        // Update status badge
                        const statusText = $('#editStatus option:selected').text();
                        const statusClass = $('#editStatus').val() === 'available' ? 'success' : 
                                           ($('#editStatus').val() === 'pending' ? 'warning' : 'danger');
                        $('.badge').removeClass('bg-success bg-warning bg-danger')
                                 .addClass('bg-' + statusClass)
                                 .text(statusText);
                        // Show success message
                        showAlert('Donation updated successfully!', 'success');
                        // Close the modal after a short delay
                        setTimeout(() => {
                            $('#editDonationModal').modal('hide');
                        }, 1000);
                        throw new Error(response.message || 'Failed to update donation');
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    let errorMessage = 'An error occurred while updating the donation';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response && response.message) {
                            errorMessage = response.message;
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        errorMessage = 'Server returned an invalid response. Please check console for details.';
                    showAlert(errorMessage, 'danger');
                complete: function() {
                    saveBtn.prop('disabled', false).html(originalBtnText);
        // Show alert function
        function showAlert(message, type) {
            alertDiv.removeClass('d-none alert-success alert-danger')
                   .addClass('alert-' + type)
                   .html(`
                       <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                       ${message}
                       <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                   `);
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    alertDiv.fadeOut(500, function() {
                        $(this).addClass('d-none').removeClass('alert-success');
                    });
                }, 5000);
    });
    </script>
</body>
</html>
