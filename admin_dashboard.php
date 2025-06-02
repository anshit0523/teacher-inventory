<?php
session_start();
include 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['account_type_id'] != 2) {
    header('Location: login.php');
    exit();
}

// ✅ Handle search filters
$search_query = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) { 
    $search = trim($_GET['search']);
    $search_query = "AND (CONCAT(teacher.fn, ' ', teacher.ln) LIKE '%$search%' OR teacher_detail.item LIKE '%$search%' OR teacher_detail.specs LIKE '%$search%')";
}

// ✅ Fetch teacher inventory records (No filter if search is empty)
$inventory_query = "SELECT CONCAT(teacher.fn, ' ', teacher.ln) AS full_name, 
                    teacher_detail.item, teacher_detail.specs, teacher_detail.qty, 
                    teacher_detail.unit, teacher_detail.return, teacher_detail.remark 
                    FROM teacher_detail 
                    JOIN teacher ON teacher_detail.teacher_id = teacher.teacher_id 
                    WHERE 1 $search_query";
$inventory_result = $conn->query($inventory_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Sidebar */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2c3e50;
            padding-top: 20px;
            transition: all 0.3s;
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 16px;
            color: white;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar .active {
            background-color: #1a252f;
            font-weight: bold;
        }
        
        /* Content */
        .content {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s;
        }

        /* Navbar */
        .navbar {
            background-color: #ffffff;
            padding: 15px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dashboard-card i {
            font-size: 32px;
            color: #3498db;
        }

        /* Table */
        .table-container {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .content {
                margin-left: 210px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="admin_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="user_table.php"><i class="fas fa-users"></i> User Table</a>
        <a href="return_report.php"><i class="fas fa-file-alt"></i> Return Report</a>
        <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
        <nav class="navbar">
            <div class="container-fluid">
                <h3 class="mb-0">Admin Dashboard</h3>
                <div>
                    <span class="me-3">Welcome, Admin</span>
                    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                </div>
            </div>
        </nav>

        <!-- Dashboard Summary -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div>
                        <h5>Total Users</h5>
                        <p>120</p>
                    </div>
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div>
                        <h5>Total Inventory</h5>
                        <p>500 Items</p>
                    </div>
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div>
                        <h5>Returned Items</h5>
                        <p>30 Items</p>
                    </div>
                    <i class="fas fa-undo-alt"></i>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="mt-4">
            <form method="GET" class="mb-3 d-flex">
                <div class="input-group" style="width: 350px;">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" id="searchInput">
                    <button type="submit" class="btn btn-primary">🔍</button>
                </div>
            </form>
        </div>

        <!-- Inventory Table -->
        <div class="table-container">
            <h4>Teacher Inventory Record</h4>
            <table class="table table-bordered text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Full Name</th>
                        <th>Item</th>
                        <th>Specs</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Return</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $inventory_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['full_name'] ?></td>
                            <td><?= $row['item'] ?></td>
                            <td><?= $row['specs'] ?></td>
                            <td><?= $row['qty'] ?></td>
                            <td><?= $row['unit'] ?></td>
                            <td><?= $row['return'] ?></td>
                            <td><?= $row['remark'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById("searchInput").addEventListener("input", function () {
            if (this.value.trim() === "") {
                window.location.href = "admin_dashboard.php"; // ✅ Reload the page when search is cleared
            }
        });
    </script>

</body>
</html>
