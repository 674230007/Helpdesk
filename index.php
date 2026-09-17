<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'Admin') {
            header("Location: admin_dashboard.php");
            exit();
        } elseif ($user['role'] == 'Technician') {
            header("Location: tech_dashboard.php");
            exit();
        } else {
            header("Location: create_ticket.php");
            exit();
        }
    } else {
        $error = "ไม่พบชื่อผู้ใช้งานนี้ในระบบ!";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - IT Helpdesk Service System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Prompt', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .form-control { border-radius: 8px; padding: 12px; }
        .btn-primary { border-radius: 8px; padding: 12px; font-weight: 500; }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center">
    <div class="card p-4" style="width: 420px;">
        <div class="text-center mb-4">
            <i class="bi bi-tools text-primary display-4"></i>
            <h3 class="fw-bold mt-2">IT Helpdesk</h3>
            <p class="text-muted small">ระบบแจ้งซ่อมและบริการสารสนเทศ</p>
        </div>
        <?php if($error): ?>
            <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Username เข้าสู่ระบบ</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="เช่น admin01, tech01, user01" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน (กรอกอะไรก็ได้)" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-2"></i> เข้าสู่ระบบ
            </button>
        </form>
        <div class="text-center mt-3 text-muted small">
            <span>💡 Test Account: admin01 / tech01 / user01</span>
        </div>
    </div>
</body>
</html>