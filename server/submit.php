<?php
$host = "localhost";
$dbname = "internship_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = $_POST['Full_Name'];
    $email = $_POST['Email'];
    $phone = $_POST['Phone'];
    $position = $_POST['Position_Applied'];
    $start_date = $_POST['Start_Date'];
    $cover_letter = $_POST['Cover_Letter'];

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $resume_name = basename($_FILES['attachment']['name']);
        $resume_tmp = $_FILES['attachment']['tmp_name'];
        $target_dir = "uploads/";
        $resume_path = $target_dir . time() . "_" . $resume_name;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($resume_tmp, $resume_path)) {
            $stmt = $pdo->prepare("INSERT INTO applications 
                (full_name, email, phone, position_applied, start_date, cover_letter, resume_name, resume_path) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $full_name, $email, $phone, $position,
                $start_date, $cover_letter, $resume_name, $resume_path
            ]);

            header("Location: thankyou.html");
            exit;
        } else {
            echo "❌ File upload failed.";
        }
    } else {
        echo "❌ Resume is required.";
    }
}
?>
