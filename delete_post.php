<?php
include 'config.php'; // เรียกใช้ไฟล์ config.php เพื่อเชื่อมต่อฐานข้อมูลและ session

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามีการส่ง ID โพสต์ที่ต้องการลบมาหรือไม่
if (isset($_GET['id'])) {
    $post_id = $_GET['id']; // เก็บค่า id ของโพสต์ที่จะลบ
    $user_id = $_SESSION['user_id']; // เก็บค่า id ของผู้ใช้ที่ล็อกอินอยู่

    // ตรวจสอบว่าผู้ใช้เป็นเจ้าของโพสต์นั้นหรือไม่ก่อนลบ
    $query = "DELETE FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query); // เตรียม statement
    $stmt->bind_param("ii", $post_id, $user_id); // กำหนดค่า ID ของโพสต์และผู้ใช้
    if ($stmt->execute()) {
        // หากลบสำเร็จ ให้กลับไปที่หน้าหลัก
        header("Location: dashboard.php");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการลบโพสต์!";
    }
} else {
    echo "ไม่มีโพสต์ที่เลือกเพื่อลบ!";
}
?>
