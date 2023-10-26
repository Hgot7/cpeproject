<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
  }

if (isset($_POST['updategroup'])) {
    $group_id = $_POST['id'];
    $group_name = $_POST['group_name'];

    if (empty($group_name)) {
        $_SESSION['error'] = 'Invalid news ID';
        header("location: groupmanage.php");
        exit();
      }

    try {
        if (!isset($_SESSION['error'])) {
            $new_group_id = $_POST['new_group_id']; // แก้ไขตามความเหมาะสม
            $sql = $conn->prepare("UPDATE `groups` SET group_name = :group_name WHERE group_id = :group_id");
            $sql->bindParam(':group_id', $group_id);
            $sql->bindParam(':group_name', $group_name);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $_SESSION['success'] = '<strong>ชื่อกลุ่มเรียน </strong>' . $group_name . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
                header("Location: ./groupmanage.php");
                exit();
            } else {
                $_SESSION['error'] = 'ข้อมูลยังไม่ได้รับการแก้ไข';
                header("Location: ./groupmanage.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: ./groupmanage.php');
        exit();
    }
}
?>


<!DOCTYPE html>

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

    <title>หน้าแก้ไขกลุ่มเรียนนักศึกษาในรายวิชา</title>

</head>

<body>

    <!-- -------------------------------------------------Header------------------------------------------------- -->
    <div class="HeaderBg shadow">
        <div class="container">
            <navbar_admin-component></navbar_admin-component> <!-- component.js Navber-->
        </div>
    </div>

    <div class="container-fluid justify-content-around">
        <div class="row">

            <sidebar_admin-component></sidebar_admin-component>


            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">

                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขกลุ่มเรียนนักศึกษาในรายวิชา</h1>
                <!-- <p>This is information groupID of admin interface</p> -->
                <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./groupmanage.php">จัดการข้อมูลกลุ่มเรียน</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขกลุ่มเรียนนักศึกษาในรายวิชา</li>
          </ol>
        </nav>
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
                        <form action="./editgroup.php" method="post" enctype="multipart/form-data">
                            <?php
                            if (isset($_GET['id'])) {
                                $std_id = $_GET['id'];
                                $stmt = $conn->prepare("SELECT * FROM `groups` WHERE group_id = :std_id");
                                $stmt->bindParam(':std_id', $std_id);
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

                            <input type="hidden" name="id" value="<?php echo $data['group_id']; ?>">
                            <label class="form-label">ชื่อกลุ่มเรียน<span style="color: red;"> *</span></label>
                            <input type="text" class="form-control" name="group_name" placeholder="กรุณากรอก ไฟ" required value="<?php echo $data['group_name']; ?>">
                            <div class="pt-3 justify-content-center">
                            <button type="submit" name="updategroup" class="btn btn-success">อัปเดต</button>
                                <a type="button" href="./groupmanage.php" class="btn btn-secondary">กลับ</a>
                               
                            </div>



                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>
</body>

</html>