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
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // เช็คประเภทไฟล์
        if ($fileType != "csv") {
            $uploadOk = 0;
            $_SESSION['error'] = "ขออภัย, อนุญาตเฉพาะไฟล์ CSV เท่านั้น";
            header("Location: ../uploadCSV.php");
            exit();
        }

        // เช็คค่าตัวแปร $uploadOk ว่ายังคงเป็น 1 หรือไม่
        if ($uploadOk == 0) {
            $_SESSION['error'] = "ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด";
            header("Location: ../uploadCSV.php");
            exit();
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                // อ่านข้อมูลจากไฟล์ CSV

                $csvData = file_get_contents($targetFile);
                $rows = explode("\n", $csvData);
                $rows = preg_replace("/\r\n|\r|\n/", ' ', $rows);
                // วนลูปเพื่อบันทึกข้อมูลลงในฐานข้อมูล
                $i = 0;
                foreach ($rows as $row) {
                    $i++;
                    if ($i >= 2) {
                        $data = str_getcsv($row);
                        // $column1 เก็บ timeTest_date
                        $column1 = $data[0];
                        $testdate = explode("/", $column1);
                        $column1 = $testdate[2] . "-" . $testdate[1] . "-" . $testdate[0];
                        // $column2 เก็บ start_time
                        $column2 = $data[1];
                        // $column3 เก็บ stop_time
                        $column3 = $data[2];
                        // $column4 เก็บ room_number
                        $column4 = $data[3];
                        // $column5 เก็บ project_id
                        $column5 = $data[4];

                        if (empty($column1) || empty($column2)) {
                            continue; // หยุดการทำงานของลูปและข้ามไปยังรอบถัดไป
                        }

                        $stmt = $conn->prepare("SELECT * FROM `timetest` WHERE project_id = :project_id");
                        $stmt->bindParam(':project_id', $column5);
                        $stmt->execute();
                        $project_iddata = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (!empty($project_iddata)) {
                            continue; // หยุดการทำงานของลูปและข้ามไปยังรอบถัดไป
                        }

                        //เตรียมคำสั่ง SQL
                        $sql = "INSERT INTO `timetest`(timetest_date, start_time, stop_time, room_number, project_id) VALUES (:timetest_date,:start_time,:stop_time,:room_number,:project_id)";

                        // เรียกใช้คำสั่ง SQL
                        $stmt = $conn->prepare($sql);


                        $stmt->bindParam(':timetest_date', $column1);
                        $stmt->bindParam(':start_time', $column2);
                        $stmt->bindParam(':stop_time', $column3);
                        $stmt->bindParam(':room_number', $column4);
                        $stmt->bindParam(':project_id', $column5);

                        if ($stmt->execute()) {
                            echo "บันทึกข้อมูลสำเร็จ<br>";
                        } else {
                            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
                            header("Location: ../uploadCSV.php");
                            exit();
                        }
                    }
                }
            }
            // ลบไฟล์หลังจากบันทึกเสร็จ
            unlink($targetFile);
            $_SESSION['success'] = "บันทึกข้อมูลการสอบในรายวิชาสำเร็จ";
            echo "<script>hideLoading();</script>"; // เรียกใช้ฟังก์ชันเพื่อซ่อน Popup Loading
            header("Location: ../uploadCSV.php");
            exit();
        }
    }
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../uploadCSV.php");
    exit();
}
