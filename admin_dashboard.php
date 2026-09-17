<?php
session_start();
include 'db.php';

// ตรวจสอบสิทธิ์ ต้องเป็น Admin เท่านั้น
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: index.php");
    exit();
}

$message = '';

// จัดการเมื่อแอดมินกดปุ่มมอบหมายงานให้ช่าง
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_ticket'])) {
    $ticket_id = $_POST['ticket_id'];
    $technician_id = $_POST['technician_id'];

    $stmt = $conn->prepare("UPDATE tickets SET technician_id = ?, status = 'Assigned' WHERE ticket_id = ?");
    $stmt->execute([$technician_id, $ticket_id]);
    $message = "มอบหมายงานเรียบร้อยแล้ว!";
}

// ดึงรายการแจ้งซ่อมทั้งหมดมารวมกับข้อมูลผู้ใช้และหมวดหมู่
$stmt_tickets = $conn->query("
    SELECT t.*, u.full_name AS user_name, c.category_name, tech.full_name AS tech_name 
    FROM tickets t 
    JOIN users u ON t.user_id = u.user_id 
    JOIN categories c ON t.category_id = c.category_id 
    LEFT JOIN users tech ON t.technician_id = tech.user_id 
    ORDER BY t.created_at DESC
");
$tickets = $stmt_tickets->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายชื่อช่างทั้งหมดมาแสดงใน Dropdown สำหรับมอบหมายงาน
$stmt_tech = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'Technician'");
$technicians = $stmt_tech->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">🛠️ ระบบแจ้งซ่อม (Admin Panel)</a>
            <span class="navbar-text text-white">
                ผู้ดูแลระบบ: <?= htmlspecialchars($_SESSION['full_name']); ?> | 
                <a href="index.php" class="btn btn-outline-light btn-sm ms-2">ออกจากระบบ</a>
            </span>
        </div>
    </nav>

    <div class="container">
        <?php if($message): ?>
            <div class="alert alert-success"><?= $message; ?></div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">📋 รายการแจ้งซ่อมทั้งหมดในระบบ</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>ผู้แจ้ง</th>
                                <th>หมวดหมู่</th>
                                <th>หัวข้อ / อาการ</th>
                                <th>สถานที่</th>
                                <th>สถานะ</th>
                                <th>ช่างผู้รับผิดชอบ</th>
                                <th>จัดการ (มอบหมายงาน)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($tickets) > 0): ?>
                                <?php foreach($tickets as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $row['ticket_id']; ?></td>
                                    <td><?= htmlspecialchars($row['user_name']); ?></td>
                                    <td><?= htmlspecialchars($row['category_name']); ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['title']); ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($row['description']); ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['location']); ?></td>
                                    <td class="text-center">
                                        <?php 
                                            $badgeBg = 'secondary';
                                            if($row['status'] == 'Pending') $badgeBg = 'warning text-dark';
                                            elseif($row['status'] == 'Assigned') $badgeBg = 'info text-dark';
                                            elseif($row['status'] == 'In Progress') $badgeBg = 'primary';
                                            elseif($row['status'] == 'Completed') $badgeBg = 'success';
                                        ?>
                                        <span class="badge bg-<?= $badgeBg; ?>"><?= $row['status']; ?></span>
                                    </td>
                                    <td><?= $row['tech_name'] ? htmlspecialchars($row['tech_name']) : '<span class="text-danger">ยังไม่มอบหมาย</span>'; ?></td>
                                    <td>
                                        <form method="POST" class="d-flex gap-1">
                                            <input type="hidden" name="ticket_id" value="<?= $row['ticket_id']; ?>">
                                            <select name="technician_id" class="form-select form-select-sm" required>
                                                <option value="">-- เลือกช่าง --</option>
                                                <?php foreach($technicians as $tech): ?>
                                                    <option value="<?= $tech['user_id']; ?>" <?= ($row['technician_id'] == $tech['user_id']) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($tech['full_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" name="assign_ticket" class="btn btn-sm btn-success text-nowrap">บันทึก</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">ยังไม่มีรายการแจ้งซ่อมในระบบ</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>