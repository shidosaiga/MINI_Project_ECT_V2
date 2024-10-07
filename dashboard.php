<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // รหัสผู้ใช้ที่ล็อกอินอยู่

// ดึงข้อมูลโพสต์ทั้งหมดจากฐานข้อมูล พร้อมข้อมูลผู้ใช้งาน
$query = "SELECT posts.id, posts.content, posts.image, posts.created_at, users.username, posts.user_id 
          FROM posts 
          JOIN users ON posts.user_id = users.id
          ORDER BY posts.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- เพิ่ม Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5; /* สีพื้นหลังแบบอ่อน */
        }
        .profile-img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-footer {
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .post-img {
            cursor: pointer; /* เปลี่ยน cursor เมื่อวางเมาส์บนรูปภาพ */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <!-- Sidebar: เมนูทางซ้าย -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="dashboard.php" class="list-group-item list-group-item-action active">หน้าหลัก</a>
                <a href="create_post.php" class="list-group-item list-group-item-action">สร้างโพสต์ใหม่</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger">ออกจากระบบ</a>
            </div>
        </div>

        <!-- Main Content: ส่วนกลางแสดงโพสต์ -->
        <div class="col-md-6">
            <h4 class="mb-3">โพสต์ทั้งหมด</h4>

            <?php
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <!-- แสดงรูปโปรไฟล์ -->
                            <img src="https://via.placeholder.com/40" class="profile-img" alt="Profile Image">
                            <div class="ml-3">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($row['username']); ?></h5>
                                <p class="text-muted small"><?php echo htmlspecialchars($row['created_at']); ?></p>
                            </div>
                        </div>
                        <p class="card-text mt-2"><?php echo htmlspecialchars($row['content']); ?></p>

                        <!-- แสดงรูปภาพจากฐานข้อมูล (ถ้ามี) -->
                        <?php if (!empty($row['image'])): ?>
                            <?php $imageData = base64_encode($row['image']); ?>
                            <img src="data:image/jpeg;base64,<?php echo $imageData; ?>" class="img-fluid post-img" alt="Post Image" onclick="openModal('data:image/jpeg;base64,<?php echo $imageData; ?>')">
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-right">
                        <?php if ($row['user_id'] == $user_id): ?>
                            <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                            <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">ลบ</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<!-- Bootstrap Modal สำหรับแสดงรูปภาพ -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ดูรูปภาพ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="modalImage" src="" class="img-fluid" alt="Image Preview">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- เพิ่ม Bootstrap JS และ jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- สคริปต์สำหรับจัดการการแสดงผลรูปภาพ -->
<script>
    function openModal(imageSrc) {
        // กำหนด src ให้กับรูปภาพใน Modal
        document.getElementById('modalImage').src = imageSrc;
        // เปิด Modal
        $('#imageModal').modal('show');
    }
</script>
</body>
</html>
