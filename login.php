<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        session_start();
        $_SESSION['user_id'] = $id;
        $_SESSION['role'] = $role;

        // Redirect based on user role
        if ($role === 'admin') {
            header("Location: admin_dashboard.php"); // Redirect to the admin dashboard
        } elseif ($role === 'user') {
            header("Location: shop.html"); // Redirect to the shop page for regular users
        } else {
            echo "Unknown user role.";
        }
        exit; // Ensure that no further code is executed after the redirect
    } else {
        echo "Invalid login credentials";
    }

    $stmt->close();
    $conn->close();
}
?>
