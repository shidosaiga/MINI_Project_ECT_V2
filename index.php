<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MApost - Welcome</title>
    <!-- เพิ่ม Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5; /* สีพื้นหลังแบบอ่อน */
        }
        .welcome-container {
            height: 100vh; /* ให้เต็มหน้าจอ */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="container welcome-container">
    <div class="card text-center">
        <h1 class="display-4">MApost</h1>
        <p class="lead">ระบบการโพสต์ข้อความและรูปภาพที่สะดวกและรวดเร็ว</p>
        <div class="mt-4">
            <a href="login.php" class="btn btn-primary btn-lg">เข้าสู่ระบบ</a>
            <a href="register.php" class="btn btn-success btn-lg">สมัครสมาชิก</a>
        </div>
    </div>
</div>

<!-- เพิ่ม Bootstrap JS และ jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
