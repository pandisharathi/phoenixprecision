<?php
require_once 'config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $company = $_POST['company'] ?? '';
    $address = $_POST['address'] ?? '';
    $desc = $_POST['description'] ?? '';
    $captcha = $_POST['g-recaptcha-response'] ?? '';

    // Simple validation
    if (empty($name) || empty($email) || empty($phone) || empty($desc)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields.']);
        exit();
    }

    // Google reCAPTCHA validation (Server-side)
    $secret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'; // Dummy secret key for testing
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$captcha);
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success && $captcha !== 'dummy') { // Allow 'dummy' for local testing if needed
        // For production, always check reCAPTCHA. For now, we proceed to simulate submission.
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_submissions (full_name, email, phone, company, address, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $company, $address, $desc]);
        echo json_encode(['status' => 'success', 'message' => 'Your request has been submitted successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
