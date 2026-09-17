<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Technician') {
    header("Location: index.php");
    exit();
}

$tech_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $ticket_id = $_POST['ticket_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE tickets SET status = ? WHERE ticket_id = ? AND technician_id = ?");
    $stmt->execute([$new_status, $ticket_id, $tech_id]);

    // 📧 ส่งอีเมลแจ้งเตือนอัตโนมัติเมื่อช่างเปลี่ยนสถานะเป็น Completed (ซ่อมเสร็จสิ้น)
    if ($new_status == 'Completed') {
        $stmt_info = $conn->prepare("
            SELECT t.title, u.email, u.full_name 
            FROM tickets t 
            JOIN users u ON t.user_id = u.user_id 
            WHERE t.ticket_id = ?
        ");
        $stmt_info->execute([$ticket_id]);
        $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

        if ($info && !empty($info['email'])) {
            $to_email = $info['email'];
            $to_name = $info['full_name'];
            $subject = "งานซ่อมของคุณเสร็จเรียบร้อยแล้ว (Ticket #$ticket_id)";
            $html_body = "
                <div style='font-family: sans-serif; color: #333; line-height: 1.6; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;'>
                    <h2 style='color: #198754;'>สวัสดีคุณ {$to_name}</h2>
                    <p>ช่างได้ดำเนินการซ่อมแซมใบงาน <b>#{$ticket_id} : {$info['title']}</b> ของคุณเสร็จเรียบร้อยแล้วครับ</p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 15px 0;'>
                    <p>คุณสามารถเข้าไปตรวจสอบอุปกรณ์และสถานะได้ผ่านทางเว็บไซต์ระบบ Helpdesk</p>
                    <p style='font-size: 0.85em; color: #888; margin-top: 20px;'>นี่คืออีเมลอัตโนมัติ กรุณาอย่าตอบกลับ</p>
                </div>
            ";
            sendRealEmail($to_email, $to_name, $subject, $html_body);
        }
    }
    $message = "อัปเดตสถานะใบงานและส่งอีเมลแจ้งเตือนสำเร็จ!";
}

$stmt_tickets = $conn->prepare("
    SELECT t.*, u.full_name AS user_name, u.phone AS user_phone, c.category_name 
    FROM tickets t 
    JOIN users u ON t.user_id = u.user_id 
    JOIN categories c ON t.category_id = c.category_id 
    WHERE t.technician_id = ? 
    ORDER BY FIELD(t.status, 'Assigned', 'In Progress', 'Completed'), t.created_at DESC
");
$stmt_tickets->execute([$tech_id]);
$tickets = $stmt_tickets->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Technician Dashboard - IT Helpdesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Prompt', sans-serif; background-color: #f4f7f6; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-wrench text-success me-2"></i> Technician Panel</a>
            <span class="text-light">ช่างผู้ใช้งาน: <?= htmlspecialchars($_SESSION['full_name']); ?> | <a href="index.php" class="btn btn-outline-light btn-sm ms-2 rounded-pill px-3">ออกจากระบบ</a></span>
        </div>
    </nav>

    <div class="container mb-5">
        <?php if($message): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white py-3">
                <h4 class="mb-0"><i class="bi bi-list-task me-2"></i> งานซ่อมที่ได้รับมอบหมายทั้งหมดของคุณ</h4>
            </div>
            <div class="card-body p-4">
                <?php if(count($tickets) > 0): ?>
                    <?php foreach($tickets as $row): ?>
                        <div class="card border mb-3 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-white">
                                <span><strong>ใบงาน #<?= $row['ticket_id']; ?></strong> : <?= htmlspecialchars($row['title']); ?></span>
                                <span class="badge bg-<?= ($row['status'] == 'Completed') ? 'success' : 'warning text-dark'; ?> fs-6">สถานะ: <?= $row['status']; ?></span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-1"><strong>หมวดหมู่:</strong> <?= htmlspecialchars($row['category_name']); ?></p>
                                        <p class="mb-1"><strong>รายละเอียด:</strong> <?= htmlspecialchars($row['description']); ?></p>
                                        <p class="mb-1"><strong>สถานที่:</strong> <?= htmlspecialchars($row['location']); ?></p>
                                        <p class="mb-1 text-primary"><strong>ผู้แจ้ง:</strong> <?= htmlspecialchars($row['user_name']); ?> (เบอร์โทร: <?= htmlspecialchars($row['user_phone']); ?>)</p>
                                        <small class="text-muted">วันที่แจ้ง: <?= $row['created_at']; ?></small>
                                    </div>
                                    <div class="col-md-4 border-start d-flex flex-column justify-content-center">
                                        <form method="POST">
                                            <input type="hidden" name="ticket_id" value="<?= $row['ticket_id']; ?>">
                                            <label class="form-label fw-bold small">อัปเดตสถานะงาน:</label>
                                            <select name="status" class="form-select form-select-sm mb-2" required>
                                                <option value="Assigned" <?= ($row['status'] == 'Assigned') ? 'selected' : ''; ?>>รับทราบงาน</option>
                                                <option value="In Progress" <?= ($row['status'] == 'In Progress') ? 'selected' : ''; ?>>กำลังดำเนินการ</option>
                                                <option value="Completed" <?= ($row['status'] == 'Completed') ? 'selected' : ''; ?>>ซ่อมเสร็จสิ้น (ส่งเมลอัตโนมัติ)</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-sm btn-primary w-100 shadow-sm">💾 บันทึกสถานะ</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-5">ยังไม่มีงานซ่อมที่ได้รับมอบหมายในขณะนี้</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>