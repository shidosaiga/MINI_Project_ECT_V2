-- สร้างฐานข้อมูลใหม่ (ถ้ายังไม่มี)
CREATE DATABASE IF NOT EXISTS user_system;
USE user_system;

-- สร้างตาราง users สำหรับเก็บข้อมูลผู้ใช้
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY, -- รหัสผู้ใช้ (Primary Key)
    username VARCHAR(50) UNIQUE NOT NULL, -- ชื่อผู้ใช้ (ต้องไม่ซ้ำกัน)
    password VARCHAR(255) NOT NULL, -- รหัสผ่าน (เข้ารหัส)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- วันที่สร้างบัญชี
);

-- สร้างตาราง posts สำหรับเก็บข้อมูลโพสต์
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY, -- รหัสโพสต์ (Primary Key)
    user_id INT NOT NULL, -- รหัสผู้ใช้ที่เป็นเจ้าของโพสต์ (Foreign Key)
    content TEXT NOT NULL, -- เนื้อหาของโพสต์
    image LONGBLOB, -- รูปภาพที่เก็บในฐานข้อมูล (BLOB)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- วันที่โพสต์
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- ลบโพสต์เมื่อผู้ใช้ถูกลบ
);
