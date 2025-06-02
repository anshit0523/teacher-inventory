<?php
session_start();
include 'config.php';
if (!isset($_SESSION['account_id'])) {
    header('Location: login.php');
    exit();
}

// Prevent users from leaving the dashboard unless they log out
$_SESSION['locked_in'] = true;

// Fetch inventory records for the logged-in teacher
$account_id = $_SESSION['account_id'];
$query_teacher = "SELECT teacher_id, CONCAT(fn, ' ', ln) AS full_name FROM teacher WHERE account_id = '$account_id'";
$result = $conn->query($query_teacher);
$teacher = $result->fetch_assoc();
$teacher_id = $teacher['teacher_id'];
$full_name = $teacher['full_name'];

$inventory_query = "SELECT * FROM teacher_detail WHERE teacher_id = '$teacher_id'";
$inventory_result = $conn->query($inventory_query);

// Handle form submission for adding inventory
$modal_message = "";
$modal_type = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_inventory'])) {
    $item = $_POST['item'];
    $specs = $_POST['specs'];
    $qty = $_POST['qty'];
    $unit = $_POST['unit'];
    $return = $_POST['return'];
    $remark = $_POST['remark'];

    $query = "INSERT INTO teacher_detail (teacher_id, item, specs, qty, unit, `return`, remark) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ississs", $teacher_id, $item, $specs, $qty, $unit, $return, $remark);

    if ($stmt->execute()) {
        header("Location: dashboard.php?success=added");
        exit();
    } else {
        header("Location: dashboard.php?error=add_failed");
        exit();
    }
}

// Handle edit request
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_query = "SELECT * FROM teacher_detail WHERE teacher_detail_id = '$edit_id'";
    $edit_result = $conn->query($edit_query);
    if ($edit_result->num_rows > 0) {
        $edit_data = $edit_result->fetch_assoc();
    }
}

// Handle form submission for editing inventory
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_inventory'])) {
    $id = $_POST['id'];
    $item = $_POST['item'];
    $specs = $_POST['specs'];
    $qty = $_POST['qty'];
    $unit = $_POST['unit'];
    $return = $_POST['return'];
    $remark = $_POST['remark'];

    $update_query = "UPDATE teacher_detail SET item=?, specs=?, qty=?, unit=?, `return`=?, remark=? WHERE teacher_detail_id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssisssi", $item, $specs, $qty, $unit, $return, $remark, $id);
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
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
    <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="inventory.php" class="<?= ($current_page == 'inventory.php') ? 'active' : '' ?>">Manage Inventory</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>
    
    <div class="content">
    <h2 class="text-center">Welcome, <?= $full_name; ?>!</h2>
        <h3>Inventory History</h3>
        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success">Inventory successfully <?= $_GET['success'] == 'added' ? 'added' : 'edited' ?>!</div>
        <?php } elseif (isset($_GET['error'])) { ?>
            <div class="alert alert-danger">Failed to <?= $_GET['error'] == 'add_failed' ? 'add' : 'edit' ?> inventory!</div>
        <?php } ?>
        
        <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th>Item</th>
                <th>Specs</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Return</th>
                <th>Remark</th>
                <th>Action</th>
            </tr>
            </thead>
            <?php while ($row = $inventory_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['item'] ?></td>
                    <td><?= $row['specs'] ?></td>
                    <td><?= $row['qty'] ?></td>
                    <td><?= $row['unit'] ?></td>
                    <td><?= $row['return'] ?></td>
                    <td><?= $row['remark'] ?></td>
                    <td>
                        <a href="dashboard.php?edit_id=<?= $row['teacher_detail_id'] ?>" class="btn btn-warning">Edit</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Edit Modal -->
    <?php if ($edit_data) { ?>
    <div class="modal fade show" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Inventory Record</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                  
                   
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $edit_data['teacher_detail_id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Item</label>
                            <input type="text" name="item" class="form-control" value="<?= $edit_data['item'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specs</label>
                            <input type="text" name="specs" class="form-control" value="<?= $edit_data['specs'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="qty" class="form-control" value="<?= $edit_data['qty'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" value="<?= $edit_data['unit'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Return</label>
                            <input type="number" name="return" class="form-control" value="<?= $edit_data['return'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <input type="text" name="remark" class="form-control" value="<?= $edit_data['remark'] ?>" required>
                        </div>
                        <button type="submit" name="update_inventory" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</body>
<script>
    function closeModal() {
        document.querySelector(".modal").style.display = "none";
        document.body.style.overflow = "auto"; 
    }
</script>
</html>
