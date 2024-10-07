<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "ชื่อผู้ใช้นี้มีอยู่แล้ว!";
    } else {
        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $success = "สมัครสมาชิกสำเร็จ!";
        } else {
            $error = "มีข้อผิดพลาด: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- เพิ่ม Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container" style="max-width: 500px; margin-top: 50px;">
    <h2 class="text-center">สมัครสมาชิก</h2>
    
    <!-- แสดงข้อความ Error หรือ Success -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <div class="form-group">
            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">รหัสผ่าน</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">สมัครสมาชิก</button>
        <p class="text-center mt-3">มีบัญชีผู้ใช้แล้ว? <a href="login.php">เข้าสู่ระบบที่นี่</a></p>
    </form>
</div>

<!-- เพิ่ม Bootstrap JS และ jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
