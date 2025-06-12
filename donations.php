<?php
session_start();
require_once 'config/database.php';
require_once 'includes/Donation.php';

$donation = new Donation($conn);

// Get filter parameters from URL
$category = isset($_GET['category']) ? $_GET['category'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : '';

$available_donations = $donation->getAvailableDonations($category, $location, $date_range);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Donations - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container py-5">
        <!-- Page Header -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-dark mb-3">Available Donations</h1>
            <p class="lead text-muted">Find educational resources shared by our community</p>
        </div>
        
        <!-- Search and Filter Card -->
        <div class="card shadow-sm border-0 rounded-3 mb-5">
            <div class="card-body p-4">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label for="category" class="form-label fw-medium text-muted mb-1">Category</label>
                        <select class="form-select form-select-lg" id="category" name="category" style="height: 50px;">
                            <option value="">All Categories</option>
                            <option value="Books" <?php echo $category === 'Books' ? 'selected' : ''; ?>>📚 Books</option>
                            <option value="Stationery" <?php echo $category === 'Stationery' ? 'selected' : ''; ?>>✏️ Stationery</option>
                            <option value="Uniforms" <?php echo $category === 'Uniforms' ? 'selected' : ''; ?>>👔 Uniforms</option>
                            <option value="Bags" <?php echo $category === 'Bags' ? 'selected' : ''; ?>>🎒 Bags</option>
                            <option value="Electronics" <?php echo $category === 'Electronics' ? 'selected' : ''; ?>>💻 Electronics</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="location" class="form-label fw-medium text-muted mb-1">Location</label>
                        <input type="text" class="form-control form-control-lg" id="location" name="location" placeholder="Enter city or district" value="<?php echo htmlspecialchars($location); ?>">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="date_range" class="form-label fw-medium text-muted mb-1">Date Range</label>
                        <input type="text" class="form-control form-control-lg" id="date_range" name="date_range" placeholder="YYYY-MM-DD - YYYY-MM-DD" value="<?php echo htmlspecialchars($date_range); ?>">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <button type="submit" class="btn btn-primary btn mt-2">Filter</button>
                        <a href="donations.php" class="btn btn-secondary btn mt-2">Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Donations Grid -->
        <div class="row g-4">
            <?php if(empty($available_donations)): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="fas fa-box-open text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                            </div>
                            <h4 class="text-muted mb-3">No Donations Found</h4>
                            <p class="text-muted mb-4">We couldn't find any donations matching your criteria. Try adjusting your filters or check back later.</p>
                            <a href="donate.php" class="btn btn-primary px-4">
                                <i class="fas fa-plus-circle me-2"></i> Post a Donation
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($available_donations as $donation): 
                    $category_icons = [
                        'Books' => ['icon' => 'book', 'color' => 'info'],
                        'Stationery' => ['icon' => 'pencil-alt', 'color' => 'success'],
                        'Uniforms' => ['icon' => 'tshirt', 'color' => 'primary'],
                        'Bags' => ['icon' => 'briefcase', 'color' => 'warning'],
                        'Electronics' => ['icon' => 'laptop', 'color' => 'danger']
                    ];
                    $icon = $category_icons[$donation['category']] ?? ['icon' => 'box', 'color' => 'secondary'];
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden transition-all hover-shadow">
                        <div class="position-relative">
                            <?php if(!empty($donation['image'])): ?>
                                <img src="uploads/donations/<?php echo htmlspecialchars($donation['image']); ?>" 
                                     class="card-img-top object-fit-cover" 
                                     alt="<?php echo htmlspecialchars($donation['title']); ?>"
                                     style="height: 200px; width: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-<?php echo $category_icon['icon']; ?> fa-4x text-<?php echo $category_icon['color']; ?>-subtle"></i>
                                </div>
                            <?php endif; ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-<?php echo $donation['status'] === 'available' ? 'success' : 'secondary'; ?> bg-opacity-90 px-3 py-2 rounded-pill">
                                    <?php echo ucfirst($donation['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-bold mb-1 text-truncate" title="<?php echo htmlspecialchars($donation['title']); ?>">
                                    <?php echo htmlspecialchars($donation['title']); ?>
                                </h5>
                                <span class="badge bg-<?php echo $category_icon['color']; ?>-subtle text-<?php echo $category_icon['color']; ?> ms-2">
                                    <?php echo ucfirst($donation['category']); ?>
                                </span>
                            </div>
                            
                            <p class="card-text text-muted mb-3">
                                <?php echo htmlspecialchars(substr($donation['description'], 0, 100)); ?><?php echo strlen($donation['description']) > 100 ? '...' : ''; ?>
                            </p>
                            
                            <div class="d-flex align-items-center text-muted mb-3">
                                <i class="fas fa-user-circle me-2"></i>
                                <small>Posted by <?php echo htmlspecialchars($donation['user_name']); ?></small>
                                <span class="mx-2">•</span>
                                <i class="fas fa-tag me-1"></i>
                                <small><?php echo ucfirst($donation['condition']); ?></small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="view-donation.php?id=<?php echo $donation['id']; ?>" class="btn btn-outline-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </a>
                                <?php if(isset($_SESSION['user_id']) && $donation['user_id'] != $_SESSION['user_id']): ?>
                                    <button type="button" 
                                            class="btn btn-<?php echo $donation['status'] === 'available' ? 'primary' : 'secondary'; ?> request-item-btn" 
                                            data-donation-id="<?php echo $donation['id']; ?>" 
                                            <?php echo $donation['status'] !== 'available' ? 'disabled' : ''; ?>>
                                        <i class="fas fa-hand-holding-heart me-1"></i>
                                        <?php echo $donation['status'] !== 'available' ? 'Not Available' : 'Request'; ?>
                                    </button>
                                    <div id="requestStatus-<?php echo $donation['id']; ?>" class="alert alert-info mt-3 mb-0 d-none"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            <nav aria-label="Donations pagination">
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Request Confirmation Modal -->
    <div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="requestModalLabel">
                        <i class="fas fa-hand-holding-heart text-primary me-2"></i>Request Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center mb-4">
                        <div class="icon-box bg-light-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-question text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Confirm Your Request</h5>
                        <p class="text-muted mb-0">Are you sure you want to request this item? The donor will be notified of your interest.</p>
                    </div>
                    
                    <div id="requestProcessing" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                        <h6 class="fw-bold text-primary mb-1">Processing Your Request</h6>
                        <p class="text-muted small mb-0">Please wait while we process your request...</p>
                    </div>
                    
                    <div id="requestSuccess" class="text-center py-4 d-none">
                        <div class="icon-box bg-light-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-check text-success" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="fw-bold text-success mb-2">Request Sent!</h5>
                        <p class="text-muted mb-0">Your request has been sent to the donor. You'll be notified when they respond.</p>
                        <button type="button" class="btn btn-success mt-3 px-4" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-3 py-3 d-flex justify-content-center" id="requestModalFooter">
                    <button type="button" class="btn btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary px-4" id="confirmRequest">
                        <i class="fas fa-paper-plane me-1"></i> Send Request
                    </button>
                </div>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('requestModal'));
    const modalBody = document.querySelector('#requestModal .modal-body');
    const processingDiv = document.getElementById('requestProcessing');
    let currentDonationId = null;

    // Show modal when request button is clicked
    document.querySelectorAll('.request-item-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentDonationId = this.dataset.donationId;
            processingDiv.classList.add('d-none');
            modal.show();
        });
    });

    // Handle confirm request
    document.getElementById('confirmRequest').addEventListener('click', function() {
        if (!currentDonationId) return;

        // Show processing state
        processingDiv.classList.remove('d-none');
        this.disabled = true;
        document.querySelector('#requestModal .btn-secondary').disabled = true;

        fetch('request-donation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'donation_id=' + encodeURIComponent(currentDonationId)
        })
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById(`requestStatus-${currentDonationId}`);
            statusDiv.classList.remove('alert-danger', 'alert-success');
            
            if (data.success) {
                statusDiv.classList.add('alert-success');
                statusDiv.textContent = data.message;
            } else {
                statusDiv.classList.add('alert-danger');
                statusDiv.textContent = data.message;
            }
            
            statusDiv.classList.remove('d-none');
            
            // Update the button state
            const requestButton = document.querySelector(`[data-donation-id="${currentDonationId}"]`);
            if (requestButton) {
                requestButton.textContent = 'Not Available';
                requestButton.disabled = true;
            }
            
            // Hide modal after processing
            modal.hide();
        })
        .catch(error => {
            console.error('Error:', error);
            const statusDiv = document.getElementById(`requestStatus-${currentDonationId}`);
            statusDiv.classList.add('alert-danger');
            statusDiv.textContent = 'An error occurred while processing your request.';
            statusDiv.classList.remove('d-none');
            
            // Hide processing state and enable buttons
            processingDiv.classList.add('d-none');
            this.disabled = false;
            document.querySelector('#requestModal .btn-secondary').disabled = false;
        });
    });
});
</script>
    <script src="assets/js/search.js"></script>
    <script>
        $(document).ready(function() {
            // Get Sri Lanka districts from JSON file
            $.getJSON('data/districts.json', function(data) {
                $("#location").autocomplete({
                    source: data.districts,
                    minLength: 1,
                    select: function(event, ui) {
                        $(this).val(ui.item.value);
                        return false;
                    }
                });
            });
        });
    </script>
</body>
</html>
