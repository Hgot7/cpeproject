<?php
// เชื่อมต่อกับฐานข้อมูล MySQL
session_start();
require_once "../../connect.php";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "การเชื่อมต่อฐานข้อมูลสำเร็จ<br>";

    if (isset($_FILES["file"])) {
        $targetDir = "../../data/";
        $targetFile = $targetDir . basename($_FILES["file"]["name"]);
        $uploadOk = 1;                                                  //กำหนด boolean check upload หรือไหม
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
                foreach ($rows as $row) {

                    if (!empty($row)) {
                        $data = str_getcsv($row);
                        // ตรวจสอบข้อมูลในแต่ละคอลัมน์ว่าไม่ว่างก่อนที่จะดำเนินการ
                        $column1 = !empty($data[0]) ? $conn->quote($data[0]) : null;
                        $column2 = !empty($data[2]) ? $conn->quote($data[2]) : null;
                        $column3 = !empty($data[3]) ? $conn->quote($data[3]) : null;
                        $column4 = !empty($data[4]) ? $conn->quote($data[4]) : null;
                        $column5 = !empty($data[5]) ? $conn->quote($data[5]) : null;

                        // $column6 = !empty($data[6]) ? $conn->quote(str_replace('-', '', $data[6])) : null;
                        // $column7 = !empty($data[9]) ? $conn->quote(str_replace('-', '', $data[9])) : null;
                        // $column8 = !empty($data[12]) ? $conn->quote(str_replace('-', '', $data[12])) : null;

                        $column6 = !empty($data[6]) ? str_replace('-', '', $data[6]) : null;
                        $column7 = !empty($data[9]) ? (str_replace('-', '', $data[9])) : null;
                        $column8 = !empty($data[12]) ? (str_replace('-', '', $data[12])) : null;

                        $studentIdAll = array();
                        $studentIdAll[0] = $column6;
                        $studentIdAll[1] = $column7;
                        $studentIdAll[2] = $column8;
                        $column9 = !empty($data[15]) ? $conn->quote($data[15]) : null;
                        $column10 = !empty($data[16]) ? $conn->quote($data[16]) : null;
                        $column11 = !empty($data[18]) ? $conn->quote($data[18]) : null;
                        $column12 = !empty($data[19]) ? $conn->quote($data[19]) : null;
                        $column13 = !empty($data[20]) ? $conn->quote($data[20]) : null;

                        // $column14 = !empty($data[32]) ? $conn->quote($data[32]) : null;
                        // $column15 = !empty($data[33]) ? $conn->quote($data[33]) : null;
                        // $column16 = !empty($data[34]) ? $conn->quote($data[34]) : null;

                        $column14 = !empty($data[32]) ? $data[32] : null;
                        $column15 = !empty($data[33]) ? $data[33] : null;
                        $column16 = !empty($data[34]) ? $data[34] : null;


                       
                        $phoneAll = array();
                        $phoneAll[0] = $column14;
                        $phoneAll[1] = $column15;
                        $phoneAll[2] = $column16;

                        $columninputgroup = !empty($_POST["inputgroup"]) ? $_POST["inputgroup"] : null;
                        // echo  $column1 . " column1 " .  $column2  . " column2 " . $column3 . " column3 " . $column4  . " column4 " .$column5 . " column5 " . $column6  . " column6 " .$column7 . " column7 " . $column8 . " column8 " . $column9  . " column9 " .$column10 . " column10 " . $column11 . " column11 " . $column12 . " column12 " . $column13 ," column13 " ;
                        // echo  $column14 . " column14 " .  $column15  . " column15 " . $column16  . " column16 ";
                        echo "-----<br>";

                        if (empty($data[0])) {
                            // echo "<br>emptydata 0 continue<br>";
                            continue;
                        }
                        if ($data[0][0] != "2" || $data[0][1] != "5") {
                            // echo "<br>2 or 5 continue<br>";
                            continue;
                        }
                        //check ไฟล์ที่ซ้ำกัน จาก primary key             
                        $checkSql = "SELECT COUNT(*) FROM `project` WHERE project_id = $column1";
                        $checkStmt = $conn->prepare($checkSql);
                        $checkStmt->execute();
                        $count = $checkStmt->fetchColumn();

                        if ($count > 0) {
                            $_SESSION['error'] = 'มีไฟล์ข้อมูลอยู่ในฐานข้อมูลแล้ว';
                            header("Location: ../uploadCSV.php");
                            exit();
                        }
                        
                        foreach ($studentIdAll as $index => $studentID) {
                            $phone = $phoneAll[$index];
                            // ทำสิ่งที่คุณต้องการกับ $student และ $phone ในแต่ละลำดับ
                            if(!empty($studentID)){
                            $add_phone = $conn->prepare("UPDATE `student` SET phone = :phone WHERE student_id = :student_id");
                            $add_phone->bindParam(':phone', $phone);
                            $add_phone->bindParam(':student_id', $studentID);
                            $add_phone->execute();
                            // if ($add_phone->execute()) {
                            //     echo ' รหัสนักศึกษา'.$studentID .'เบอร์โทร'.$phone ."สำเร็จ ";
                            // } else {
                            //     echo "เกิดข้อผิดพลาดในการ execute คำสั่ง SQL";
                            // }
                        }
                           
                        }
                        // เตรียมคำสั่ง SQL
                        if (empty($data[9])) {
                            // project กลุ่ม1คน
                            if (empty($data[16])) {
                                //ไม่มีที่ปรึกษาร่วม
                                $sql = "INSERT INTO `project`(project_id, project_nameTH, project_nameENG, student_id1, teacher_id1, referee_id, referee_id1, referee_id2,  group_id  , year,  term) VALUES ($column1, $column4, $column5, $column6, $column9, $column11, $column12, $column13,  $columninputgroup  , $column3, $column2)";
                            } else {
                                //มีที่ปรึกษาร่วม
                                $sql = "INSERT INTO `project`(project_id, project_nameTH, project_nameENG, student_id1, teacher_id1, teacher_id2, referee_id, referee_id1, referee_id2,  group_id  , year,  term) VALUES ($column1, $column4, $column5, $column6, $column9, $column10, $column11, $column12, $column13,  $columninputgroup  , $column3, $column2)";
                            }
                        } elseif (empty($data[12])) {
                            //project กลุ่ม2คน
                            if (empty($data[16])) {
                                //ไม่มีที่ปรึกษาร่วม
                                $sql = "INSERT INTO `project`(project_id, project_nameTH, project_nameENG, student_id1, student_id2, teacher_id1, referee_id, referee_id1, referee_id2,  group_id  , year,  term) VALUES ($column1, $column4, $column5, $column6, $column7, $column9, $column11, $column12, $column13,  $columninputgroup  , $column3, $column2)";
                            } else {
                                //มีที่ปรึกษาร่วม
                                $sql = "INSERT INTO `project`(project_id, project_nameTH, project_nameENG, student_id1, student_id2, teacher_id1, teacher_id2, referee_id, referee_id1, referee_id2,  group_id  , year,  term) VALUES ($column1, $column4, $column5, $column6, $column7, $column9, $column10, $column11, $column12, $column13,  $columninputgroup  , $column3, $column2)";
                            }
                        } else {
                            //project กลุ่ม3คน
                            if (empty($data[16])) {
                                //ไม่มีที่ปรึกษาร่วม
                                $sql = "INSERT INTO `project`(project_id, project_nameTH, project_nameENG, student_id1, student_id2, student_id3, teacher_id1, referee_id, referee_id1, referee_id2,  group_id  , year,  term) VALUES ($column1, $column4, $column5, $column6, $column7, $column8, $column9, $column11, $column12, $column13,  $columninputgroup  , $column3, $column2)";
                            } else {
                                //มีที่ปรึกษาร่วม
                                $sql = "INSERT INTO `project`(project_id, project_nameTH, project_nameENG, student_id1, student_id2, student_id3, teacher_id1, teacher_id2, referee_id, referee_id1, referee_id2,  group_id  , year,  term) VALUES ($column1, $column4, $column5, $column6, $column7, $column8, $column9, $column10, $column11, $column12, $column13,  $columninputgroup  , $column3, $column2)";
                            }
                        }

                        // เรียกใช้คำสั่ง SQL
                        $stmt = $conn->prepare($sql);
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
                $_SESSION['success'] = 'บันทึกข้อมูลโปรเจคสำเร็จ';
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
