<?php
session_start(); // ضروري في بداية الملف
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

// التحقق من وجود المستخدم
$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        // حفظ بيانات المستخدم في الجلسة
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        // تحويل المستخدم إلى لوحة التحكم
        header("Location: dashboard.php");
        exit();
    } else {
        echo "كلمة المرور غير صحيحة!";
    }
} else {
    echo "البريد الإلكتروني غير مسجل!";
}

$conn->close();
?>
