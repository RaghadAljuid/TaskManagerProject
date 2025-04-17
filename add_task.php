<?php
session_start();

// تحقق من الجلسة
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db.php';

// تأكد من وجود البيانات المطلوبة
if (isset($_POST['title'], $_POST['description'], $_POST['due_date'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $user_id = $_SESSION['user_id']; // ربط المهمة بالمستخدم

    // استخدم prepared statement لحماية من SQL Injection
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, due_date, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $description, $due_date, $user_id);

    if ($stmt->execute()) {
        echo "✅ تمت إضافة المهمة بنجاح!";
    } else {
        echo "❌ خطأ: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "❌ يرجى تعبئة جميع الحقول المطلوبة.";
}

$conn->close();
?>

