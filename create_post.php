<?php
include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $image = null;

    // ตรวจสอบว่าเนื้อหาโพสต์ไม่ว่างเปล่า
    if (empty($content)) {
        echo "เนื้อหาโพสต์ต้องไม่ว่างเปล่า!";
        exit();
    }

    // ตรวจสอบการอัปโหลดรูปภาพ
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // อ่านข้อมูลไฟล์รูปภาพและแปลงเป็นไบต์
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    // บันทึกโพสต์ลงในฐานข้อมูล (รวมรูปภาพเป็น BLOB ด้วย)
    $query = "INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("iss", $user_id, $content, $image);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "มีข้อผิดพลาดในการโพสต์: " . htmlspecialchars($stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างโพสต์ใหม่</title>
    <!-- เพิ่ม Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container" style="max-width: 600px; margin-top: 50px;">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h4>สร้างโพสต์ใหม่</h4>
        </div>
        <div class="card-body">
            <!-- ฟอร์มสร้างโพสต์ -->
            <form method="POST" action="create_post.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="content">เนื้อหาโพสต์</label>
                    <textarea name="content" id="content" class="form-control" rows="4" placeholder="ใส่เนื้อหาที่คุณต้องการโพสต์..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="image">เลือกรูปภาพ (ถ้าต้องการ)</label>
                    <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                </div>
                <button type="submit" class="btn btn-success btn-block">โพสต์</button>
                <a href="dashboard.php" class="btn btn-secondary btn-block">กลับไปที่หน้าหลัก</a>
            </form>
        </div>
    </div>
</div>

<!-- เพิ่ม Bootstrap JS และ jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
