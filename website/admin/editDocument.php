<?php
session_start();
require_once "../connect.php";

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    if (isset($_FILES["document_path"]) && $_FILES["document_path"]["size"] > 0) {
        $targetDir = "uploadfileDocument/";
        $document_path = basename($_FILES["document_path"]["name"]);
        $targetFile = $targetDir . $document_path;
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($uploadOk == 1) {
            // ดำเนินการตรวจสอบชื่อไฟล์ซ้ำ
            $stmt = $conn->prepare("SELECT document_path FROM `document` WHERE document_path = :document_path");
            $stmt->bindParam(':document_path', $document_path);
            $stmt->execute();
            $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingFile) {
                $_SESSION['error'] = 'ชื่อไฟล์ซ้ำกันในระบบ ไม่สามารถอัปโหลดได้';
                header('location: editDocument.php?id='.$id);
                exit;
            } else {
                if (move_uploaded_file($_FILES["document_path"]["tmp_name"], $targetFile)) {
                    // เพิ่มข้อมูลลงในฐานข้อมูล
                } else {
                    $_SESSION['error'] = 'ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด';
                    header('location: editDocument.php?id='.$id);
                    exit;
                }
            }
        } else {
            $_SESSION['error'] = 'กรุณาเลือกไฟล์ใหม่ที่ต้องการอัปโหลด';
            header('location: editDocument.php?id='.$id);
            exit;
        }
    } else {
        $document_path = ""; // กำหนดค่าเป็นค่าว่างเมื่อไม่มีไฟล์ถูกอัปโหลด
    }

    $document_name = $_POST['document_name'];
    $year = $_POST['year'];
    $term = $_POST['term'];

    try {
        if (!isset($_SESSION['error'])) {
            if (empty($document_path)) {
                $document_path = $_POST["existing_document_path"];
            } else {
                $stmt = $conn->prepare("SELECT * FROM `document` WHERE document_id = :document_id");
                $stmt->bindParam(':document_id', $id);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                unlink("uploadfileDocument/" . $data["document_path"]);
            }

            $document_name = empty($document_name) ? null : $document_name;
            $year = empty($year) ? null : $year;
            $term = empty($term) ? null : $term;


            $sql = $conn->prepare("UPDATE `document` SET document_path = :document_path, document_name = :document_name, document_date = CONCAT(YEAR(NOW()) + 543, DATE_FORMAT(NOW(), '-%m-%d %H:%i:%s')), year = :year,  term = :term WHERE document_id = :id");
            $sql->bindParam(':id', $id);
            $sql->bindParam(':document_path', $document_path);
            $sql->bindParam(':document_name', $document_name);
            $sql->bindParam(':year', $year);
            $sql->bindParam(':term', $term);
            $sql->execute();
            if ($sql) {
                $_SESSION['success'] = '<strong>ชื่อเอกสาร </strong> '.$document_name . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
                header("Location: ./documentmanage.php");
                exit();
            } else {
                $_SESSION['error'] = 'ข้อมูลเอสารยังไม่ได้รับการแก้ไข';
                header("Location: documentmanage.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
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

    <title>หน้าแก้ไขข้อมูลเอกสารในรายวิชา</title>

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

                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขข้อมูลเอกสารในรายวิชา</h1>
                <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./documentmanage.php">จัดการข้อมูลเอกสารในรายวิชา</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลเอกสารในรายวิชา</li>
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
                        <form action="./editDocument.php" method="post" enctype="multipart/form-data">
                            <?php
                            if (isset($_GET['id'])) {
                                $id = $_GET['id'];
                                $stmt = $conn->prepare("SELECT * FROM `document` WHERE document_id = :id");
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

                            <input type="hidden" name="id" value="<?php echo $data['document_id']; ?>">

                            <div id="document_path">
                                <label class="form-label">เอกสารในรายวิชา</label>
                                <input type="file" class="form-control" name="document_path" id="document_path" accept=".pdf,.docx">
                                <?php if (!empty($data['document_path'])) : ?>
                                    <div class="pt-3 justify-content-center">
                                        <label class="form-label">เอกสารปัจจุบัน</label>
                                        <input type="hidden" name="existing_document_path" value="<?php echo $data['document_path']; ?>">
                                        <a class="fs-5" href="<?php echo './uploadfileDocument/' . $data['document_path']; ?>" target="_blank"><?php echo $data['document_path']; ?></a>
                                        <a onclick="return confirm('Are you sure you want to delete File ?');" href="deleteFile_Document.php?id=<?php echo $data['document_id'] ?>" class="btn btn-danger">ลบเอกสาร</a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="pt-3 justify-content-center">
                                <label class="form-label">ชื่อเอกสารในรายวิชา</label>
                                <input type="text" class="form-control" name="document_name" placeholder="Enter new group ID" required value="<?php echo $data['document_name']; ?>">


                                <div id="year">
                                    <label class="form-label">ปีการศึกษาที่เริ่มใช้งาน</label>
                                    <input type="number" class="form-control" name="year" id="year" value="<?php echo isset($data['year']) ? $data['year'] : ''; ?>" placeholder="25xx">
                                </div>

                                <div id="term">
                                    <label class="form-label">ภาคการศึกษาที่เริ่มใช้งาน</label>
                                    <select name="term" class="form-select">
                                        <option value=""  <?php if (empty($data['term'])) echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                                        <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                                        <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                                        <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
                                    </select>
                                </div>

                                <div class="pt-3 justify-content-center">
                                    <button type="submit" name="update" class="btn btn-success">อัปเดต</button>
                                    <a type="button" href="./documentmanage.php" class="btn btn-secondary">กลับ</a>
                                </div>



                            </div>



                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>
</body>

</html>