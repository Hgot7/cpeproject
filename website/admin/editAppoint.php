<?php
session_start();
require_once "../connect.php";
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
  }
function convertToBuddhistEra($datetime)
{
    $date = date('Y-m-d', strtotime($datetime));

    if ((((int)date('Y', strtotime($date))) - 543) < 2000) {
        // แปลงวันที่ในรูปแบบคริสต์ศักราช (AD) เป็นวันที่ในรูปแบบพุทธศักราช (พ.ศ.)
        $buddhistYear = (int)date('Y', strtotime($date)) + 543;
        $buddhistDate = $buddhistYear . date('-m-d', strtotime($date));
        return $buddhistDate . ' ' . date('H:i:s', strtotime($datetime));
    } else {
        return $datetime;
    }
}
if (isset($_POST['update'])) {
    $appoint_id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $appoint_date = convertToBuddhistEra($_POST['appoint_date']);
    $group_id = $_POST['group_id'];

    try {
        if (!isset($_SESSION['error'])) {
            // $new_appoint_id = $_POST['new_appoint_id'];

            // $new_appoint_id = empty($new_appoint_id) ? null : $new_appoint_id;
            $appoint_id = empty($appoint_id) ? null : $appoint_id;        
            $title = empty($title) ? null : $title;
            $description = empty($description) ? null : $description;
            $appoint_date = empty($appoint_date) ? null : $appoint_date;
            $group_id = empty($group_id) ? null : $group_id;
            
            $sql = $conn->prepare("UPDATE `appoint` SET 
                title = :title,
                description = :description,
                appoint_date = :appoint_date,
                group_id = :group_id
                WHERE appoint_id = :appoint_id");
            // $sql->bindParam(':new_appoint_id', $new_appoint_id);
            $sql->bindParam(':title', $title);
            $sql->bindParam(':description', $description);
            $sql->bindParam(':appoint_date', $appoint_date);
            $sql->bindParam(':group_id', $group_id);
            $sql->bindParam(':appoint_id', $appoint_id);
            $sql->execute();

            if ($sql->rowCount() > 0) {
                $_SESSION['success'] = '<strong>หัวข้อกำหนดการ </strong>'.$title . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
                header("Location: ./Appointmanage.php");
                exit();
            } else {
                $_SESSION['error'] = 'ข้อมูลยังไม่ได้รับการแก้ไข';
                header("Location: Appointmanage.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
        header("location: ./Appointmanage.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link to custom Bootstrap CSS -->
    <link rel="stylesheet" href="../css/custom.css">
    <script src="../component.js"></script>
    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <!-- Link to icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">

    <title>หน้าแก้ไขกำหนดการในรายวิชา</title>

</head>

<body>

    <!-- -------------------------------------------------Header------------------------------------------------- -->
    <div class="HeaderBg shadow">
        <div class="container">
            <navbar_admin-component></navbar_admin-component> <!-- component.js navbar_admin-->
        </div>
    </div>
    <div class="container-fluid justify-content-around">
        <div class="row">

            <sidebar_admin-component></sidebar_admin-component>

            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">
                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขกำหนดการในรายวิชา</h1>
                <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./Appointmanage.php">จัดการข้อมูลกำหนดการในรายวิชา</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขกำหนดการในรายวิชา</li>
          </ol>
        </nav>

                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
                        <form action="./editAppoint.php" method="post" enctype="multipart/form-data">
                            <?php
                            if (isset($_GET['id'])) {
                                $appoint_id = $_GET['id'];
                                $stmt = $conn->prepare("SELECT * FROM `appoint` WHERE appoint_id = :appoint_id");
                                $stmt->bindParam(':appoint_id', $appoint_id);
                                if ($stmt->execute()) {
                                    $data = $stmt->fetch();
                                    if ($data) {
                                        // ดำเนินการต่อไป
                                    } else {
                                        echo "ไม่พบข้อมูลกลุ่มที่ระบุ";
                                    }
                                } else {
                                    echo "เกิดข้อผิดพลาดในการเรียกข้อมูล";
                                }
                            } else {
                                echo "ไม่พบค่า id";
                            }
                            ?>

                            <input type="hidden" name="id" value="<?php echo $data['appoint_id']; ?>">

                            <!-- <div class="pt-3 justify-content-center">
                                <label class="form-label">appoint_id</label>
                                <input type="text" class="form-control" name="new_appoint_id" placeholder="Enter new group ID" required value="<?php echo $data['appoint_id']; ?>">
                            </div> -->

                            <div id="title">
                                <label class="form-label">หัวข้อกำหนดการ<span style="color: red;"> *</span></label>
                                <input type="text" class="form-control" name="title" id="title" value="<?php echo isset($data['title']) ? $data['title'] : ''; ?>" placeholder="หัวข้อกำหนดการ" required>
                            </div>

                            <label class="form-label">เนื้อหากำหนดการ</label>
                            <div class="form-floating" id="description">
                                <textarea class="form-control" name="description" id="description" placeholder="เนื้อหากำหนดการ"><?php echo isset($data['description']) ? $data['description'] : ''; ?></textarea>
                                <label for="description">รายละเอียด</label>
                            </div>

                            <div id="title">
                                <label class="form-label">วันเวลาที่สิ้นสุดกำหนดการ<span style="color: red;"> *</span></label>
                                <input type="datetime-local" class="form-control" name="appoint_date" id="appoint_date" value="<?php echo isset($data['appoint_date']) ? $data['appoint_date'] : ''; ?>" placeholder="หัวข้อกำหนดการ" required>
                            </div>


                            <div class="col-md-4" id="group_id">
                                <label class="form-label">กลุ่มเรียน<span style="color: red;"> *</span></label>
                                <select id="selectbox" name="group_id" class="form-select">
                                    <?php
                                    $columnValue = null;
                                    $groups = $conn->query("SELECT * FROM `groups` ORDER BY group_id DESC");
                                    $groups->execute();
                                    ?>

                                    <option value="<?php echo null ?>">ทุกกลุ่มเรียน</option>
                                    <?php

                                    while ($group = $groups->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <option value="<?php echo $group['group_id']; ?>" <?php if ($group['group_id'] == $data['group_id']) echo "selected"; ?>>
                                            <?php echo $group['group_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="pt-3 justify-content-center">
                            <button type="submit" name="update" class="btn btn-success">อัปเดต</button>
                                <a type="button" href="./Appointmanage.php" class="btn btn-secondary ">กลับ</a>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>
</body>

</html>