<?php
include 'connect.php';
include 'session.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

// Handle export to Excel
if (isset($_GET['export_users'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="users_export_' . date('Y-m-d') . '.xls"');
    
    $query = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
    $result = mysqli_query($connect, $query);
    
    echo "ID\tUsername\tEmail\tRole\tCreated At\n";
    
    while($row = mysqli_fetch_assoc($result)) {
        echo $row['id'] . "\t";
        echo $row['username'] . "\t";
        echo $row['email'] . "\t";
        echo $row['role'] . "\t";
        echo $row['created_at'] . "\n";
    }
    exit();
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($connect, $_GET['delete_id']);
    
    // Prevent admin from deleting themselves
    if ($delete_id != $_SESSION['user_id']) {
        $deleteQuery = "DELETE FROM users WHERE id = '$delete_id'";
        mysqli_query($connect, $deleteQuery);
    }
    
    header("Location: users.php");
    exit();
}

// Handle role change
if (isset($_GET['change_role'])) {
    $user_id = mysqli_real_escape_string($connect, $_GET['change_role']);
    $new_role = $_GET['role'];
    
    // Prevent admin from changing their own role
    if ($user_id != $_SESSION['user_id']) {
        $updateQuery = "UPDATE users SET role = '$new_role' WHERE id = '$user_id'";
        mysqli_query($connect, $updateQuery);
    }
    
    header("Location: users.php");
    exit();
}

$query = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Coffee Shop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleDashboard.css">
</head>
<body>
    <div class="container">
    <div class="header">
        <h1>User Management</h1>
        <div class="user-info">
            Welcome, <?= htmlspecialchars($_SESSION['username'])?> 
            <a href="index.php">Back to Main</a> |<a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="navigation">
        <a href="dashboard.php">Dashboard</a>
        <a href="index.php">Orders</a>
        <?php if (isAdmin()): ?>
            <a href="users.php">Manage Users</a>
            <a href="create.php">Create Order</a>
        <?php endif; ?>
    </div>

    <!-- Export Button -->
    <div style="margin: 20px 0;">
        <a href="users.php?export_users=1" class="export-btn">
            Export to Excel
        </a>
    </div>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
                <?php if ($row['id'] == $_SESSION['user_id']): ?>
                    <?= $row['role'] ?> (You)
                <?php else: ?>
                    <form method="get" style="display: inline;">
                        <input type="hidden" name="change_role" value="<?= $row['id'] ?>">
                        <select name="role" onchange="this.form.submit()">
                            <option value="user" <?= $row['role'] == 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </form>
                <?php endif; ?>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <?php if ($row['id'] != $_SESSION['user_id']): ?>
                    <a href="users.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                <?php else: ?>
                    <span style="color: #999;">Current User</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</body>
</html>