<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // รหัสผู้ใช้ที่ล็อกอินอยู่

// ตรวจสอบว่าเป็นการแก้ไขโพสต์จากแบบฟอร์ม (POST) หรือการเรียกหน้าฟอร์ม (GET)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $post_id = $_POST['id'];
    $content = $_POST['content'];

    // ตรวจสอบว่าโพสต์นี้เป็นของผู้ใช้ที่ล็อกอินอยู่หรือไม่
    $query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $current_image = $row['image']; // เก็บรูปภาพเดิม

        // ตรวจสอบว่าอัปโหลดรูปภาพใหม่หรือไม่
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // อ่านข้อมูลรูปภาพใหม่และแปลงเป็นไบต์
            $current_image = file_get_contents($_FILES['image']['tmp_name']);
        }

        // อัปเดตข้อมูลโพสต์ในฐานข้อมูล
        $query = "UPDATE posts SET content = ?, image = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $content, $current_image, $post_id, $user_id);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "แก้ไขโพสต์ล้มเหลว: " . htmlspecialchars($stmt->error);
        }
    } else {
        echo "ไม่มีสิทธิ์แก้ไขโพสต์นี้!";
    }
} elseif (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // ดึงข้อมูลโพสต์มาแสดงในฟอร์ม
    $query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>แก้ไขโพสต์</title>
            <!-- เพิ่ม Bootstrap CSS -->
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        </head>
        <body>
        <div class="container" style="max-width: 600px; margin-top: 50px;">
            <div class="card">
                <div class="card-header bg-warning text-white text-center">
                    <h4>แก้ไขโพสต์</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="edit_post.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="content">เนื้อหาโพสต์</label>
                            <textarea name="content" id="content" class="form-control" rows="4" required><?php echo htmlspecialchars($row['content']); ?></textarea>
                        </div>
                        
                        <!-- แสดงรูปภาพปัจจุบัน (ถ้ามี) -->
                        <?php if (!empty($row['image'])): ?>
                            <?php $imageData = base64_encode($row['image']); ?>
                            <div class="form-group">
                                <label>รูปภาพปัจจุบัน:</label><br>
                                <img src="data:image/jpeg;base64,<?php echo $imageData; ?>" class="img-thumbnail" style="max-width: 300px;">
                            </div>
                        <?php endif; ?>

                        <!-- ฟอร์มเลือกไฟล์รูปภาพใหม่ -->
                        <div class="form-group">
                            <label for="image">เลือกรูปภาพใหม่ (ถ้าต้องการเปลี่ยน)</label>
                            <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                        </div>

                        <!-- ส่งค่า id ของโพสต์ไปในฟอร์ม -->
                        <input type="hidden" name="id" value="<?php echo $post_id; ?>">
                        <button type="submit" class="btn btn-primary btn-block">บันทึกการแก้ไข</button>
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
        <?php
    } else {
        echo "ไม่มีโพสต์ที่เลือก!";
    }
}
?>
