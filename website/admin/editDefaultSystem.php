<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
  }

  

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $year = $_POST['year'];
    $term = $_POST['term'];

    if (empty($year)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header("location: editDefaultSystem.php");
        exit();
      }
    
      if (empty($term)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header("location: editDefaultSystem.php");
        exit();
      }

    try {
        $sql = $conn->prepare("UPDATE `defaultsystem` SET year = :year, term = :term WHERE default_system_id = :id");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->bindParam(':year', $year, PDO::PARAM_INT);
        $sql->bindParam(':term', $term, PDO::PARAM_INT);

        if ($sql->execute()) {
            $_SESSION['success'] = 'แก้ไขข้อมูลปีการศึกษาที่ ' . $year . " และ ภาคการศึกษาที่ " . $term . ' สำเร็จ';
            header("Location: ./editDefaultSystem.php");
            exit();
        } else {
            $_SESSION['error'] = 'ไม่สามารถแก้ไขข้อมูล ID ' . $id;
            header("Location: editDefaultSystem.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        header("Location: editDefaultSystem.php");
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

    <title>หน้าจัดการค่าพื้นฐานของระบบ</title>

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
                <div class="col-md-7 col-lg-8">

                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลค่าพื้นฐานของระบบ</h1>             
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb fs-5 mt-3 ms-3">
                    <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active" aria-current="page">ค่าพื้นฐานของระบบ</li>
                    </ol>
                </nav>
                <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?></div>
                <?php  } ?>
                <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?></div>
                <?php  } ?>
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
                        <form action="./editDefaultSystem.php" method="post" enctype="multipart/form-data">
                            <?php
                            if (1) {
                                $id = 1;
                                $stmt = $conn->prepare("SELECT * FROM `defaultsystem` WHERE default_system_id = :id");
                                $stmt->bindParam(':id', $id);
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

                            <input type="hidden" name="id" value="<?php echo $data['default_system_id']; ?>">

                            <div class="pt-3 justify-content-center">
                            <label class="form-label">ปีการศึกษาพื้นฐานของระบบ<span style="color: red;"> *</span></label>
                                <input type="number" class="form-control" name="year" style="width:auto;"placeholder="ปีการศึกษาพื้นฐานของระบบ" required value="<?php echo $data['year']; ?>">
                            </div>

                            <div id="term">
                                <label class="form-label">ภาคการศึกษาพื้นฐานของระบบ<span style="color: red;"> *</span></label>
                                <select id="selectbox" name="term" class="form-select" required>
                                <option value="" <?php if ($data['term'] == "") echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                                    <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                                    <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                                    <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
                                </select>
                            </div>

                            <div class="pt-3 justify-content-center">
                                <button type="submit" name="update" class="btn btn-success">อัปเดต</button>
                            </div>

                        </form>
                    </div>
                </div>
                </div>
            </main>

        </div>
    </div>
</body>

</html>