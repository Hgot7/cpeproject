<?php
session_start();

// เชื่อมต่อกับฐานข้อมูล MySQL
require_once "../../connect.php";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_FILES["file"])) {
        $targetDir = "../../data/";
        $targetFile = $targetDir . basename($_FILES["file"]["name"]);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // เช็คประเภทไฟล์
        if ($fileType != "csv") {
            $_SESSION['error'] = "ขออภัย, อนุญาตเฉพาะไฟล์ CSV เท่านั้น";
            header("Location: ../uploadCSV.php");
            exit();
        }

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            // อ่านข้อมูลจากไฟล์ CSV
            $csvData = file_get_contents($targetFile);
            $rows = explode("\n", $csvData);

            // วนลูปเพื่อบันทึกข้อมูลลงในฐานข้อมูล
            for ($i = 1; $i < count($rows); $i++) {
                $data = str_getcsv($rows[$i]);

                if (count($data) >= 3) {
                    $title = isset($data[0]) ? $data[0] : "";
                    $description = isset($data[1]) ? $data[1] : "";
                    $appoint_date = isset($data[2]) ? $data[2] : "";
                    $group_id = !empty($_POST["inputgroup"]) ? $_POST["inputgroup"] : null;

                    if (!empty($title)) {
                        $sql = "INSERT INTO `appoint` (title, description, appoint_date, group_id) VALUES (:title, :description, STR_TO_DATE(:appoint_date, '%d/%m/%Y %H:%i:%s'), :group_id)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':title', $title);
                        $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':appoint_date', $appoint_date);
                        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);

                        if ($stmt->execute()) {
                            echo "บันทึกข้อมูลสำเร็จ<br>";
                        } else {
                            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
                        }
                    }
                } else {
                    echo "ข้อมูลไม่ครบถ้วน: " . implode(", ", $data) . "<br>";
                }
            }

            // ลบไฟล์ CSV หลังจากเสร็จสิ้นการอัปโหลด
            unlink($targetFile);
            $_SESSION['success'] = "บันทึกข้อมูลกำหนดการในรายวิชาสำเร็จ";
            echo "<script>hideLoading();</script>"; // เรียกใช้ฟังก์ชันเพื่อซ่อน Popup Loading
        } else {
            $_SESSION['error'] = "ขออภัย, ไม่สามารถอัปโหลดไฟล์ได้";
        }

        header("Location: ../uploadCSV.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../uploadCSV.php");
    exit();
}
?>
