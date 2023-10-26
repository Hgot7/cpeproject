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
            $_SESSION['error'] = "ขออภัย อนุญาตเฉพาะไฟล์ CSV เท่านั้น";
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

                $news_head = isset($data[0]) ? $conn->quote($data[0]) : "";
                $news_text = isset($data[1]) ? $conn->quote($data[1]) : "";
                $year = !empty($_POST["numberYear"]) ? $_POST["numberYear"] : null;
                $term = !empty($_POST["numberTerm"]) ? $_POST["numberTerm"] : null;

                if (empty($data[0])) {
                    break;
                }
                if (!empty($news_head)) {
                    $sql = "INSERT INTO `news` (news_head, news_text, news_date, year, term)
                            VALUES ($news_head, $news_text, CONCAT(YEAR(NOW()) + 543, DATE_FORMAT(NOW(), '-%m-%d %H:%i:%s')), :year, :term)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':year', $year);
                    $stmt->bindParam(':term', $term);

                    if ($stmt->execute()) {
                        echo "บันทึกข้อมูลสำเร็จ<br>";
                    } else {
                        $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
                    }
                }
            }

            // ลบไฟล์ CSV หลังจากเสร็จสิ้นการอัปโหลด
            unlink($targetFile);
            $_SESSION['success'] = "บันทึกข้อมูลข่าวสารสำเร็จ";
            echo "<script>hideLoading();</script>"; // เรียกใช้ฟังก์ชันเพื่อซ่อน Popup Loading
        } else {
            $_SESSION['error'] = "ขออภัย ไม่สามารถอัปโหลดไฟล์ได้";
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
