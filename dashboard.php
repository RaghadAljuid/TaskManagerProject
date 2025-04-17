<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$conn = new mysqli("localhost", "root", "", "taskmanager");

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// منع الوصول بدون تسجيل دخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$update_message = "";

// إضافة مهمة
if (isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("INSERT INTO tasks (title, status, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $status, $user_id);
    $stmt->execute();
}

// تحديث مهمة
if (isset($_POST['update_task'])) {
    $task_id = $_POST['task_id'];
    $title = $_POST['title'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE tasks SET title=?, status=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssii", $title, $status, $task_id, $user_id);
    $stmt->execute();
    $update_message = "✔️ تم تحديث المهمة بنجاح.";
}

// حذف مهمة
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>لوحة المهام</title>
    <style>
        body { font-family: Tahoma; padding: 30px; background: #f7f7f7; direction: rtl; }
        h1 { color: #333; }
        form { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 10px; text-align: center; border-bottom: 1px solid #ccc; }
        input[type="text"], select { padding: 5px; width: 90%; }
        button { padding: 5px 10px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
        a { color: red; text-decoration: none; }
        .logout { float: left; }
        .msg { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>

    <h1>مرحبًا، <?php echo htmlspecialchars($username); ?>! 📝</h1>
    <a class="logout" href="logout.php">تسجيل الخروج</a>

    <?php if (!empty($update_message)) echo "<p class='msg'>$update_message</p>"; ?>

    <h2>أضف مهمة جديدة</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="عنوان المهمة" required>
        <select name="status" required>
            <option value="pending">قيد التنفيذ</option>
            <option value="completed">مكتملة</option>
        </select>
        <button type="submit" name="add_task">إضافة</button>
    </form>

    <h2>مهامك:</h2>
    <table>
        <tr>
            <th>العنوان</th>
            <th>الحالة</th>
            <th>إجراءات</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<form method='POST'>";
            echo "<td><input type='text' name='title' value='" . htmlspecialchars($row['title']) . "'></td>";
            echo "<td>
                    <select name='status'>
                        <option value='pending'" . ($row['status'] == 'pending' ? ' selected' : '') . ">قيد التنفيذ</option>
                        <option value='completed'" . ($row['status'] == 'completed' ? ' selected' : '') . ">مكتملة</option>
                    </select>
                </td>";
            echo "<td>
                    <input type='hidden' name='task_id' value='{$row['id']}'>
                    <button type='submit' name='update_task'>تحديث</button>
                    <a href='?delete={$row['id']}'>حذف</a>
                </td>";
            echo "</form>";
            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>
