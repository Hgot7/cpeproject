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
                $prefix = array("นางสาว", "นาง", "นาย");
                foreach ($rows as $row) {
                    $i++;
                    // $column5 เก็บ year
                    $column5 = !empty($_POST["numberYear"]) ? $_POST["numberYear"] : null;
                    // $column6 เก็บ term
                    $column6 = !empty($_POST["numberTerm"]) ? $_POST["numberTerm"] : null;
                    // $columninputgroup เก็บ group_id
                    $columninputgroup = !empty($_POST["inputgroup"]) ? $_POST["inputgroup"] : null;

                    if ($i >= 8) {
                        $data = str_getcsv($row);
                        // $column1 เก็บ student_id
                        $column1 = !empty($data[1]) ? str_replace('-', '', $data[1]) : null;   // แก้ไขตามคอลัมน์ที่ต้องการ
                        // $column2 เก็บ student_pass
                        $column2 = !empty($column1) ? $column1[7] . $column1[8] . $column1[9] . $column1[10] . $column1[11] . $column1[12] : null;
                        // Full name เก็บ Full name
                        $fullName = $data[2];
                        $fullNames = explode(" ", $fullName);
                        // $column3 เก็บ name
                        $column3 = $fullNames[0];
                        // $column4 เก็บ lastname
                        $column4 = !empty($fullNames[1]) ? $fullNames[1] : null;
                        // Full name เก็บ Full name

                        foreach ($prefix as $word) {                         //check ชื่อที่ติดกับ นาย.นางสาว.นาง

                            if (stripos($column3, $word) !== false) {
                                $column3 = !empty($column3) ? str_replace($word, "", $column3) : null;
                                continue;
                            }
                        }
                        // $column7 เก็บ email
                        $column7 = !empty($column1) ? $column1 . "@mail.rmutt.ac.th" : null;
                        // echo $fullNames[1];
                        if (empty($column1)) {
                            continue;
                        }

                        // เตรียมคำสั่ง SQL
                        $passwordHash = password_hash($column2, PASSWORD_DEFAULT);
                        $sql = "INSERT INTO `student` (student_id, student_password, firstname, lastname, year, term, email, group_id)  
                                VALUES (:column1, :passwordHash, :column3, :column4, :column5, :column6, :column7, :columninputgroup)";
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':column1', $column1);
                        $stmt->bindParam(':passwordHash', $passwordHash);
                        $stmt->bindParam(':column3', $column3);
                        $stmt->bindParam(':column4', $column4);
                        $stmt->bindParam(':column5', $column5);
                        $stmt->bindParam(':column6', $column6);
                        $stmt->bindParam(':column7', $column7);
                        $stmt->bindParam(':columninputgroup', $columninputgroup);

                        if ($stmt->execute()) {
                            echo "success <br>";
                        } else {
                            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
                            header("Location: ../uploadCSV.php");
                            exit();
                        }
                    }
                }
                // ลบไฟล์ CSV หลังจากเสร็จสิ้นการอัปโหลด
                unlink($targetFile);
                $_SESSION['success'] = "บันทึกข้อมูลนักศึกษาสำเร็จ";
                header("Location: ../uploadCSV.php");
                exit();
            }
        }
    }
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../uploadCSV.php");
    exit();
}
