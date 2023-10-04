<?php
session_start();
require_once "../connect.php";

if (isset($_POST['update'])) {
    $timeTest_id = $_POST['id'];
    $timeTest_date = $_POST['timeTest_date'];
    $start_time = $_POST['start_time'];
    $stop_time = $_POST['stop_time'];
    $room_number = $_POST['room_number'];
    $project_id = $_POST['project_id'];

    try {
        if (!isset($_SESSION['error'])) {
            $new_timeTest_id = $_POST['new_timeTest_id'];
            
            // check empty is null
            // $new_timeTest_id = empty($new_timeTest_id) ? null : $new_timeTest_id;
            $timeTest_id = empty($timeTest_id) ? null : $timeTest_id;
            $timeTest_date = empty($timeTest_date) ? null : $timeTest_date;
            $start_time = empty($start_time) ? null : $start_time;
            $stop_time = empty($stop_time) ? null : $stop_time;
            $room_number = empty($room_number) ? null : $room_number;
            $project_id = empty($project_id) ? null : $project_id;

            $sql = $conn->prepare("UPDATE `timetest` SET 
                timeTest_date = :timeTest_date,
                start_time = :start_time,
                stop_time = :stop_time,
                room_number = :room_number,
                project_id = :project_id
                WHERE timeTest_id = :timeTest_id");
            // $sql->bindParam(':new_timeTest_id', $new_timeTest_id);
            $sql->bindParam(':timeTest_date', $timeTest_date);
            $sql->bindParam(':start_time', $start_time);
            $sql->bindParam(':stop_time', $stop_time);
            $sql->bindParam(':room_number', $room_number);
            $sql->bindParam(':project_id', $project_id);
            $sql->bindParam(':timeTest_id', $timeTest_id);
            $sql->execute();

            if ($sql->rowCount() > 0) {
                $_SESSION['success'] = '<strong>รหัสเวลาสอบ </strong>'. $timeTest_id . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
                header("Location: ./TimeTestmanage.php");
                exit();
            } else {
                $_SESSION['error'] = 'ข้อมูลเวลาสอบยังไม่ได้รับการแก้ไข';
                header("Location: TimeTestmanage.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
        header("location: TimeTestmanage.php");
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

    <title>หน้าแก้ไขเวลาสอบโครงงานในรายวิชา</title>

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
                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขเวลาสอบโครงงานในรายวิชา</h1>
                <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./TimeTestmanage.php">จัดการข้อมูลเวลาสอบโครงงาน</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขเวลาสอบโครงงานในรายวิชา</li>
          </ol>
        </nav>

                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
                        <form action="./editTimeTest.php" method="post" enctype="multipart/form-data">
                            <?php
                            if (isset($_GET['id'])) {
                                $timeTest_id = $_GET['id'];
                                $stmt = $conn->prepare("SELECT * FROM `timetest` WHERE timeTest_id = :timeTest_id");
                                $stmt->bindParam(':timeTest_id', $timeTest_id);
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

                            <input type="hidden" name="id" value="<?php echo $data['timeTest_id']; ?>">

                            <!-- <div class="pt-3 justify-content-center">
                                <label class="form-label">timeTest_id</label>
                                <input type="text" class="form-control" name="new_timeTest_id" placeholder="Enter new group ID" required value="<?php echo $data['timeTest_id']; ?>" placeholder="yyyy-mm-dd">
                            </div> -->
                            <div id="project_id">
                                <label class="form-label">รหัสโปรเจค</label>
                                <input type="text" class="form-control" name="project_id" id="project_id" value="<?php echo isset($data['project_id']) ? $data['project_id'] : ''; ?>" placeholder="25xx-xxx">
                            </div>

                            <div id="room_number">
                                <label class="form-label">ห้องสอบ</label>
                                <input type="text" class="form-control" name="room_number" id="room_number" value="<?php echo isset($data['room_number']) ? $data['room_number'] : ''; ?>" placeholder="xxxxx">
                            </div>

                            <div id="timeTest_date">
                                <label class="form-label">วันที่สอบ</label>
                                <input type="date" class="form-control" name="timeTest_date" id="timeTest_date" value="<?php echo isset($data['timeTest_date']) ? $data['timeTest_date'] : ''; ?>" placeholder="yyyy-mm-dd">
                            </div>

                            <div id="start_time">
                                <label class="form-label">เริ่มสอบ</label>
                                <input type="time" class="form-control" name="start_time" id="start_time" value="<?php echo isset($data['start_time']) ? $data['start_time'] : ''; ?>" placeholder="xx:xx">
                            </div>

                            <div id="stop_time">
                                <label class="form-label">หมดเวลาสอบ</label>
                                <input type="time" class="form-control" name="stop_time" id="stop_time" value="<?php echo isset($data['stop_time']) ? $data['stop_time'] : ''; ?>" placeholder="xx:xx">
                            </div>

                            <div class="pt-3 justify-content-center">
                            <button type="submit" name="update" class="btn btn-success">อัปเดต</button>
                                <a type="button" href="./TimeTestmanage.php" class="btn btn-secondary ">กลับ</a>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>
</body>

</html>