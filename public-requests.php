<?php
session_start();
require_once 'config/database.php';
require_once 'includes/Request.php';

$request = new Request($conn);

// Get filter parameters from URL
$category = isset($_GET['category']) ? $_GET['category'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : '';

$public_requests = $request->getPublicRequests($category, $location, $date_range);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Requests - Shilpa Sawiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container py-5">
        <!-- Page Header -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-dark mb-3">Public Requests</h1>
            <p class="lead text-muted">Find requests from people in need of educational resources</p>
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
                        <a href="public-requests.php" class="btn btn-secondary btn mt-2">Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Requests Grid -->
        <div class="row g-4">
            <?php if(empty($public_requests)): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="fa-solid fa-book" style="font-size: 4rem; opacity: 0.5;"></i>
                            </div>
                            <h4 class="text-muted mb-3">No Requests Found</h4>
                            <p class="text-muted mb-4">We couldn't find any requests matching your criteria. Try adjusting your filters or check back later.</p>
                            <a href="post-request.php" class="btn btn-primary px-4">
                                <i class="fas fa-plus-circle me-2"></i> Post a Request
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($public_requests as $req): 
                    $category_icons = [
                        'Books' => ['icon' => 'book', 'color' => 'info'],
                        'Stationery' => ['icon' => 'pencil-alt', 'color' => 'success'],
                        'Uniforms' => ['icon' => 'tshirt', 'color' => 'primary'],
                        'Bags' => ['icon' => 'briefcase', 'color' => 'warning'],
                        'Electronics' => ['icon' => 'laptop', 'color' => 'danger']
                    ];
                    $category_icon = $category_icons[$req['category']] ?? ['icon' => 'box', 'color' => 'secondary'];
                    $status_colors = [
                        'open' => 'primary',
                        'fulfilled' => 'success',
                        'cancelled' => 'secondary'
                    ];
                    $status_color = $status_colors[strtolower($req['status'])] ?? 'secondary';
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden transition-all hover-shadow">
                        <div class="position-relative">
                            <?php if(!empty($req['image'])): ?>
                                <img src="uploads/requests/<?php echo htmlspecialchars($req['image']); ?>" 
                                     class="card-img-top object-fit-cover" 
                                     alt="<?php echo htmlspecialchars($req['title']); ?>"
                                     style="height: 200px; width: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-<?php echo $category_icon['icon']; ?> fa-4x text-<?php echo $category_icon['color']; ?>-subtle"></i>
                                </div>
                            <?php endif; ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-<?php echo $status_color; ?> bg-opacity-90 px-3 py-2 rounded-pill">
                                    <?php echo ucfirst($req['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-bold mb-1 text-truncate" title="<?php echo htmlspecialchars($req['title']); ?>">
                                    <?php echo htmlspecialchars($req['title']); ?>
                                </h5>
                                <span class="badge bg-<?php echo $category_icon['color']; ?>-subtle text-<?php echo $category_icon['color']; ?> ms-2">
                                    <?php echo ucfirst($req['category']); ?>
                                </span>
                            </div>
                            
                            <p class="card-text text-muted mb-3">
                                <?php echo htmlspecialchars(substr($req['description'], 0, 150)); ?><?php echo strlen($req['description']) > 150 ? '...' : ''; ?>
                            </p>
                            
                            <div class="d-flex align-items-center text-muted mb-3">
                                <i class="fas fa-user-circle me-2"></i>
                                <small>Requested by <?php echo htmlspecialchars($req['user_name']); ?></small>
                                <span class="mx-2">•</span>
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <small><?php echo htmlspecialchars($req['location'] ?? 'Not specified'); ?></small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <?php if(isset($_SESSION['user_id']) && $req['user_id'] != $_SESSION['user_id']): ?>
                                    <a href="offer-donation.php?request_id=<?php echo $req['id']; ?>" class="btn btn-primary flex-grow-1">
                                        <i class="fa-solid fa-book"></i> Offer Donation
                                    </a>
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
            <nav aria-label="Requests pagination">
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
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
    
    <script>
    $(document).ready(function() {
        // Initialize date range picker
        $('input[name="date_range"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear'
            }
        });

        $('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('input[name="date_range"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
    </script>
</body>
</html>
