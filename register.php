<?php
include 'db.php';

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// التحقق من تطابق كلمتي المرور
if ($password !== $confirm_password) {
    die("كلمتا المرور غير متطابقتين!");
}

// تأمين كلمة المرور
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// استعلام الإدخال
$sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "تم التسجيل بنجاح!";
} else {
    echo "خطأ: " . $conn->error;
}

$conn->close();
?>
