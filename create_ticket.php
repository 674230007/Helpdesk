<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'User') {
    header("Location: index.php");
    exit();
}

$success = '';
$error = '';
$user_id = $_SESSION['user_id'];

$stmt_user_info = $conn->prepare("SELECT email, full_name FROM users WHERE user_id = ?");
$stmt_user_info->execute([$user_id]);
$current_user_data = $stmt_user_info->fetch(PDO::FETCH_ASSOC);
$default_email = $current_user_data['email'] ?? '';

$stmt_cat = $conn->query("SELECT * FROM categories");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_ticket'])) {
    $category_id = $_POST['category_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $priority = $_POST['priority'];
    $target_email = trim($_POST['target_email']);

    if (!empty($title) && !empty($description) && !empty($location)) {
        try {
            $stmt = $conn->prepare("INSERT INTO tickets (user_id, category_id, title, description, location, priority) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $category_id, $title, $description, $location, $priority]);
            $ticket_id = $conn->lastInsertId();

            if (!empty($target_email)) {
                $stmt_category_name = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
                $stmt_category_name->execute([$category_id]);
                $cat_name = $stmt_category_name->fetchColumn();

                $to_email = $target_email;
                $to_name = $current_user_data['full_name'];
                $subject = "ระบบได้รับเรื่องแจ้งซ่อมของคุณแล้ว (Ticket #$ticket_id)";
                
                $html_body = "
                    <div style='font-family: sans-serif; color: #333; line-height: 1.6; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;'>
                        <h2 style='color: #0d6efd;'>สวัสดีคุณ {$to_name}</h2>
                        <p>ระบบ Helpdesk ได้รับเรื่องแจ้งซ่อมอุปกรณ์ <b>{$cat_name}</b> ของคุณเรียบร้อยแล้วครับ</p>
                        <hr style='border: none; border-top: 1px solid #eee; margin: 15px 0;'>
                        <p><b>เลขที่ใบงาน:</b> #{$ticket_id}</p>
                        <p><b>หัวข้อปัญหา:</b> {$title}</p>
                        <p><b>อาการเบื้องต้น:</b><br>{$description}</p>
                        <p><b>สถานที่:</b> {$location} | <b>ความเร่งด่วน:</b> {$priority}</p>
                        <hr style='border: none; border-top: 1px solid #eee; margin: 15px 0;'>
                        <p style='color: #555; font-size: 0.95em;'>เจ้าหน้าที่กำลังดำเนินการตรวจสอบและจะอัปเดตสถานะให้ทราบโดยเร็วที่สุด</p>
                        <p style='font-size: 0.85em; color: #888; margin-top: 20px;'>นี่คืออีเมลอัตโนมัติ กรุณาอย่าตอบกลับ</p>
                    </div>
                ";
                
                sendRealEmail($to_email, $to_name, $subject, $html_body);
            }

            $success = "แจ้งซ่อมสำเร็จ! บันทึกใบงานและส่งอีเมลแจ้งเตือนเรียบร้อยแล้ว";
        } catch (PDOException $e) {
            $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    }
}

$stmt_my_tickets = $conn->prepare("
    SELECT t.*, c.category_name, tech.full_name AS tech_name 
    FROM tickets t 
    JOIN categories c ON t.category_id = c.category_id 
    LEFT JOIN users tech ON t.technician_id = tech.user_id 
    WHERE t.user_id = ? 
    ORDER BY t.created_at DESC
");
$stmt_my_tickets->execute([$user_id]);
$my_tickets = $stmt_my_tickets->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบแจ้งซ่อมและติดตามสถานะ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Prompt', sans-serif; background-color: #f4f7f6; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .card-header { border-radius: 12px 12px 0 0 !important; font-weight: 500; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 15px; border: 1px solid #dee2e6; }
        .btn-custom { border-radius: 8px; padding: 10px 20px; font-weight: 500; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3 mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-tools text-warning me-2"></i> IT Helpdesk System</a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3"><i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <?php if($success): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> แจ้งซ่อมปัญหาใหม่</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">อีเมลสำหรับรับการแจ้งเตือน</label>
                                <input type="email" name="target_email" class="form-control" value="<?= htmlspecialchars($default_email); ?>" placeholder="example@email.com" required>
                                <div class="form-text">สามารถเปลี่ยนอีเมลเพื่อรับผลการแจ้งซ่อมได้ทันที</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">หมวดหมู่งานซ่อม</label>
                                <select name="category_id" class="form-select" required>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['category_id']; ?>"><?= htmlspecialchars($cat['category_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">หัวข้อปัญหา / อาการเสีย</label>
                                <input type="text" name="title" class="form-control" placeholder="เช่น แอร์ไม่เย็น, จอฟ้า" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">สถานที่เกิดเหตุ</label>
                                <input type="text" name="location" class="form-control" placeholder="เช่น อาคาร A ชั้น 2 ห้อง 201" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">ระดับความเร่งด่วน</label>
                                <select name="priority" class="form-select">
                                    <option value="Low">ปกติ (Low)</option>
                                    <option value="Medium" selected>ปานกลาง (Medium)</option>
                                    <option value="High">ด่วน (High)</option>
                                    <option value="Urgent">ด่วนที่สุด (Urgent)</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">รายละเอียดอาการเพิ่มเติม</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="ระบุรายละเอียด..." required></textarea>
                            </div>
                            <button type="submit" name="create_ticket" class="btn btn-primary w-100 btn-custom shadow-sm">
                                <i class="bi bi-send-fill me-2"></i> ส่งใบแจ้งซ่อมและส่งอีเมล
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-list-check me-2"></i> ติดตามสถานะงานซ่อมของฉัน</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>หัวข้อ / หมวดหมู่</th>
                                        <th>สถานะ</th>
                                        <th>ช่างผู้รับผิดชอบ</th>
                                        <th>วันที่แจ้ง</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($my_tickets) > 0): ?>
                                        <?php foreach($my_tickets as $row): ?>
                                        <tr>
                                            <td>#<?= $row['ticket_id']; ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($row['title']); ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($row['category_name']); ?></small>
                                            </td>
                                            <td>
                                                <?php 
                                                    $badge = 'warning text-dark';
                                                    if($row['status'] == 'Assigned') $badge = 'info text-dark';
                                                    elseif($row['status'] == 'In Progress') $badge = 'primary';
                                                    elseif($row['status'] == 'Completed') $badge = 'success';
                                                ?>
                                                <span class="badge bg-<?= $badge; ?>"><?= $row['status']; ?></span>
                                            </td>
                                            <td><?= $row['tech_name'] ? htmlspecialchars($row['tech_name']) : '<span class="text-muted small">รอจัดสรร</span>'; ?></td>
                                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'])); ?></small></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted py-5">ยังไม่มีประวัติการแจ้งซ่อม</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>