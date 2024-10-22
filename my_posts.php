<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // รหัสผู้ใช้ที่ล็อกอินอยู่

// ดึงเฉพาะโพสต์ของผู้ใช้ที่ล็อกอินอยู่
$query = "SELECT posts.id, posts.content, posts.image, posts.created_at, users.username, posts.user_id 
          FROM posts 
          JOIN users ON posts.user_id = users.id
          WHERE posts.user_id = ?
          ORDER BY posts.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts_result = $stmt->get_result();

// ฟังก์ชันสำหรับดึงความคิดเห็นที่เกี่ยวข้องกับโพสต์
function getComments($conn, $post_id) {
    $query = "SELECT comments.id AS comment_id, comments.content, comments.created_at, comments.user_id, users.username
              FROM comments
              JOIN users ON comments.user_id = users.id
              WHERE comments.post_id = ?
              ORDER BY comments.created_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    return $stmt->get_result();
}

// ถ้าผู้ใช้ส่งความคิดเห็นใหม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_content'], $_POST['post_id'])) {
    $comment_content = $_POST['comment_content'];
    $post_id = $_POST['post_id'];

    if (!empty($comment_content)) {
        $query = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $post_id, $user_id, $comment_content);
        $stmt->execute();
    }
}

// ลบความคิดเห็น
if (isset($_GET['delete_comment_id'])) {
    $comment_id = $_GET['delete_comment_id'];
    $query = "DELETE FROM comments WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();
}

// แก้ไขความคิดเห็น
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_comment_content'], $_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    $new_content = $_POST['edit_comment_content'];

    if (!empty($new_content)) {
        $query = "UPDATE comments SET content = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $new_content, $comment_id, $user_id);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โพสต์ของฉัน</title>
    <!-- เพิ่ม Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
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
        .comment-section {
            margin-top: 15px;
        }
        .comment {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <!-- Sidebar: เมนูทางซ้าย -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="dashboard.php" class="list-group-item list-group-item-action">หน้าหลัก</a>
                <a href="my_posts.php" class="list-group-item list-group-item-action active">โพสต์ของฉัน</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger">ออกจากระบบ</a>
            </div>
        </div>

        <!-- Main Content: ส่วนกลางแสดงโพสต์ -->
        <div class="col-md-6">
            <h4 class="mb-3">โพสต์ของฉัน</h4>

            <?php
            while ($post = $posts_result->fetch_assoc()) {
                ?>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <!-- แสดงรูปโปรไฟล์ -->
                            <img src="https://via.placeholder.com/40" class="profile-img" alt="Profile Image">
                            <div class="ml-3">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($post['username']); ?></h5>
                                <p class="text-muted small"><?php echo htmlspecialchars($post['created_at']); ?></p>
                            </div>
                        </div>
                        <p class="card-text mt-2"><?php echo htmlspecialchars($post['content']); ?></p>

                        <!-- แสดงรูปภาพจากฐานข้อมูล (ถ้ามี) -->
                        <?php if (!empty($post['image'])): ?>
                            <?php $imageData = base64_encode($post['image']); ?>
                            <img src="data:image/jpeg;base64,<?php echo $imageData; ?>" class="img-fluid" alt="Post Image">
                        <?php endif; ?>

                        <!-- ส่วนแสดงความคิดเห็น -->
                        <div class="comment-section">
                            <h6>ความคิดเห็น:</h6>

                            <!-- ดึงความคิดเห็นจากฐานข้อมูล -->
                            <?php
                            $comments_result = getComments($conn, $post['id']);
                            while ($comment = $comments_result->fetch_assoc()) {
                                ?>
                                <div class="comment">
                                    <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                                    <small class="text-muted"><?php echo htmlspecialchars($comment['created_at']); ?></small>
                                    
                                    <!-- ปุ่มแก้ไข/ลบความคิดเห็น -->
                                    <?php if ($comment['user_id'] == $user_id): ?>
                                        <a href="?delete_comment_id=<?php echo $comment['comment_id']; ?>" class="btn btn-sm btn-danger">ลบ</a>
                                        
                                        <!-- ฟอร์มแก้ไขความคิดเห็น -->
                                        <form method="POST" action="my_posts.php" class="mt-2">
                                            <div class="form-group">
                                                <input type="text" name="edit_comment_content" class="form-control" value="<?php echo htmlspecialchars($comment['content']); ?>" required>
                                                <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-warning">บันทึก</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <?php
                            }
                            ?>

                            <!-- ฟอร์มเพิ่มความคิดเห็น -->
                            <form method="POST" action="my_posts.php">
                                <div class="form-group">
                                    <input type="text" name="comment_content" class="form-control" placeholder="เขียนความคิดเห็น..." required>
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">ส่งความคิดเห็น</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                        <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger">ลบ</a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<!-- เพิ่ม Bootstrap JS และ jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.amazonaws.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
