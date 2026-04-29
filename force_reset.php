<?php
// Without Login / Force Reset Option
require_once 'config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['force_reset'])) {
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new === $confirm) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        // Assuming single admin or resetting 'admin' specifically
        $update = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
        if ($update->execute([$hashed])) {
            $message = "Admin password has been force reset! <a href='admin/login.php'>Login here</a>";
        } else {
            $error = "Failed to reset password.";
        }
    } else {
        $error = "Passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Force Password Reset - Phoenix Precision Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0d2137; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; }
        .reset-card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="reset-card">
        <h2 class="h4 fw-bold mb-3 text-center">Force Reset Admin</h2>
        <p class="small text-muted text-center mb-4">Set a new password for the 'admin' account directly.</p>

        <?php if ($message): ?>
            <div class="alert alert-success small"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">New Password</label>
                <input type="password" name="new_password" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required minlength="6">
            </div>
            <button type="submit" name="force_reset" class="btn btn-primary w-100">Reset Password</button>
        </form>
        <div class="text-center mt-3">
            <a href="index.php" class="small text-decoration-none">Back to Site</a>
        </div>
    </div>
</body>
</html>
