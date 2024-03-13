<?php
require '../models/User.php';
session_start();

$user = new User();
$users = $user->getUsers();

if (isset($_GET['search'])) {
    $users = $user->searchUsers($_GET['search']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Admin :: List Users</title>
</head>
<body>
    <header>
        <!-- Dynamic links based on user's authentication status -->
        <?php if (isset($_SESSION['id'])): ?>
            <p>Welcome <?= htmlspecialchars($_SESSION['name']) ?> | <a href="logout.php">Logout</a> | <a href="../../index.php">Page</a></p>
        <?php else: ?>
            <p><a href="register.php">Register</a> | <a href="login.php">Login</a></p>
        <?php endif; ?>
    </header>

    <h1>List users</h1>
    <form id="search" method="GET">
        <input type="text" name="search" placeholder="Enter {Name} or {Email} to search">
        <input type="submit" value="Search">
    </form>
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row["id"]) ?></td>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["admin"]) ? 'Yes' : 'No' ?></td>
                    <td><a href="edit.php?id=<?= $row['id'] ?>">Edit</a> | <a href="delete.php?id=<?= $row['id'] ?>">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: center;"><?= count($users) ?> users</td>
                <td colspan="3" style="text-align: center;"><a href="add.php">Add User</a></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>