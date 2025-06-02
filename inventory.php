<?php
session_start();
include 'config.php';
if (!isset($_SESSION['account_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch Teacher Info
$account_id = $_SESSION['account_id'];
$query_teacher = "SELECT teacher_id, CONCAT(fn, ' ', ln) AS full_name FROM teacher WHERE account_id = '$account_id'";
$result = $conn->query($query_teacher);
$teacher = $result->fetch_assoc();
$teacher_id = $teacher['teacher_id'];
$full_name = $teacher['full_name'];

$modal_message = "";
$modal_type = "";
$redirect = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item = $_POST['item'];
    $specs = $_POST['specs']; // New field
    $qty = $_POST['qty'];
    $unit = $_POST['unit'];
    $return = $_POST['return'];
    $remark = $_POST['remark'];

    $query = "INSERT INTO teacher_detail (teacher_id, item, specs, qty, unit, `return`, remark) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssiss", $teacher_id, $item, $specs, $qty, $unit, $return, $remark);
    
    if ($stmt->execute()) {
        $modal_message = "Inventory added successfully!";
        $modal_type = "success";
        $redirect = "dashboard.php";
    } else {
        $modal_message = "Error adding inventory!";
        $modal_type = "danger";
        $redirect = "inventory.php";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Inventory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="inventory.php">Manage Inventory</a>
        <a href="logout.php" class="text-danger">Logout</a>
    </div>
    
    <div class="content">
        <h2 class="text-center">Manage Inventory</h2>
        <form method="POST" class="border p-4 rounded shadow bg-light">
    <div class="mb-3">
        <label class="form-label">Teacher ID</label>
        <p class="form-control-plaintext fw-bold"> <?= $teacher_id; ?> </p>
    </div>
    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <p class="form-control-plaintext fw-bold"> <?= $full_name; ?> </p>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Item</label>
            <input type="text" name="item" class="form-control" placeholder="Enter item name" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Specs</label>
            <input type="text" name="specs" class="form-control" placeholder="Enter item specifications (optional)">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="qty" class="form-control" placeholder="Enter quantity" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Unit</label>
            <input type="text" name="unit" class="form-control" placeholder="Enter unit (e.g., pcs, kg)" required>
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Return</label>
        <input type="number" name="return" class="form-control" placeholder="Enter return quantity (if any)">
    </div>
    <div class="mb-3">
        <label class="form-label">Remark</label>
        <textarea name="remark" class="form-control" placeholder="Enter any remarks"></textarea>
    </div>
    
    <button type="submit" class="btn btn-success">Add Inventory</button>
</form>

    </div>

    <!-- Modal Notification -->
    <?php if (!empty($modal_message)) { ?>
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-<?= $modal_type; ?> text-white">
                    <h5 class="modal-title">Notification</h5>
                    <button type="button" class="btn-close" onclick="window.location='<?= $redirect ?>';"></button>
                </div>
                <div class="modal-body">
                    <p><?= $modal_message; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location='<?= $redirect ?>';">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</body>
</html>
