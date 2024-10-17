<?php
session_start(); // เริ่มการใช้งาน session
session_unset(); // ลบตัวแปร session ทั้งหมด
session_destroy(); // ทำลาย session ปัจจุบัน
header("Location: index.php"); // เปลี่ยนเส้นทางไปที่หน้า index.php
exit();
?>
