<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
}

if (isset($_POST['update'])) {
  $regulation_id = $_POST['id'];
  $regulationFile_path = $_POST['regulationFile_path'];

  if (isset($_FILES["regulationFile_path"]) && $_FILES["regulationFile_path"]["size"] > 0) {
    $targetDir = "uploadfileRegulation/";
    $regulationFile_path = basename($_FILES["regulationFile_path"]["name"]);
    $targetFile = $targetDir . $regulationFile_path;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // ตรวจสอบชื่อไฟล์ซ้ำ
    if ($uploadOk == 1) {
      $stmt = $conn->prepare("SELECT regulationFile_path FROM `regulation` WHERE regulationFile_path = :regulationFile_path");
      $stmt->bindParam(':regulationFile_path', $regulationFile_path);
      $stmt->execute();
      $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existingFile) {
        $filename = pathinfo($regulationFile_path, PATHINFO_FILENAME);
        $file_extension = pathinfo($regulationFile_path, PATHINFO_EXTENSION);

        $counter = 1;
        while (file_exists($targetFile)) {
          $regulationFile_path = $filename . '_' . $counter . '.' . $file_extension;
          $targetFile = $targetDir . $regulationFile_path;
          $counter++;
        }
      }

      if (move_uploaded_file($_FILES["regulationFile_path"]["tmp_name"], $targetFile)) {
        // ไฟล์ถูกอัปโหลดสำเร็จ
      } else {
        $_SESSION['error'] = 'ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด';
        header('location: editRegulation.php?id=' . $regulation_id);
        exit;
      }
    } else {
      $_SESSION['error'] = 'กรุณาเลือกไฟล์ใหม่ที่ต้องการอัปโหลด';
      header('location: editRegulation.php?id=' . $regulation_id);
      exit;
    }
  } else {
    $regulationFile_path = $_POST["existing_regulationFile_path"];
  }

  $New_regulation_text = $_POST['input_regulation_text'];
  $New_year = $_POST['input_year'];
  $New_term = $_POST['input_term'];

  try {
    if (!isset($_SESSION['error'])) {
      $stmt = $conn->prepare("SELECT * FROM `regulation` WHERE regulation_id = :regulation_id");
      $stmt->bindParam(':regulation_id', $regulation_id);
      $stmt->execute();
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!empty($regulationFile_path) && $regulationFile_path !== $data["regulationFile_path"]) {
        unlink("uploadfileRegulation/" . $data["regulationFile_path"]);
      }

      $regulationFile_path = empty($regulationFile_path) ? null : $regulationFile_path;
      $New_regulation_text = empty($New_regulation_text) ? null : $New_regulation_text;
      $New_year = empty($New_year) ? null : $New_year;
      $New_term = empty($New_term) ? null : $New_term;

      $sql = $conn->prepare("UPDATE `regulation` SET regulationFile_path = :regulationFile_path, regulation_text = :input_regulation_text, year = :input_year, term = :input_term WHERE regulation_id = :id");
      $sql->bindParam(':id', $regulation_id);
      $sql->bindParam(':regulationFile_path', $regulationFile_path);
      $sql->bindParam(':input_regulation_text', $New_regulation_text);
      $sql->bindParam(':input_year', $New_year);
      $sql->bindParam(':input_term', $New_term);

      $sql->execute();
      if ($sql) {
        $_SESSION['success'] = '<strong>ข้อมูลกฎข้อบังคับ </strong>' . $New_regulation_text . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
        header("location: ./regulationmanage.php");
        exit;
      } else {
        $_SESSION['error'] = 'ข้อมูลกฎข้อบังคับยังไม่ได้รับการแก้ไข';
        header("location: regulationmanage.php");
        exit;
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


  <title>หน้าแก้ไขข้อมูลกฎข้อบังคับของรายวิชา</title>

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

        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขข้อมูลกฎข้อบังคับของรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./regulationmanage.php">จัดการข้อมูลกฎข้อบังคับ</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลกฎข้อบังคับของรายวิชา</li>
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
            <form action="./editRegulation.php" method="post" enctype="multipart/form-data">
              <?php
              $data = [];
              if (isset($_GET['id'])) {
                $regulation_id = $_GET['id'];
                $stmt = $conn->prepare("SELECT * FROM `regulation` WHERE regulation_id = :regulation_id");
                $stmt->bindParam(':regulation_id', $regulation_id);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
              }
              ?>
              <input type="hidden" name="id" value="<?php echo $data['regulation_id'] ?? ''; ?>">

              <div id="regulationFile_path">
                                <label class="form-label">เอกสารในรายวิชา</label>
                                <input type="file" class="form-control" name="regulationFile_path" id="regulationFile_path" accept=".pdf">
                                <?php if (!empty($data['regulationFile_path'])) : ?>
                                    <div class="pt-3 mb-2 justify-content-center">
                                        <label class="form-label">เอกสารปัจจุบัน</label>
                                        <input type="hidden" name="existing_regulationFile_path" value="<?php echo $data['regulationFile_path']; ?>">
                                        <a class="fs-5" href="<?php echo './uploadfileRegulation/' . $data['regulationFile_path']; ?>" target="_blank"><?php echo $data['regulationFile_path']; ?></a>
                                        <a onclick="return confirm('Are you sure you want to delete File ?');" href="deleteFile_Regulation.php?id=<?php echo $data['regulation_id'] ?>" class="btn btn-danger">ลบเอกสาร</a>
                                    </div>
                                <?php endif; ?>
                            </div>

              <div id="input_regulation_text">
                <label class="form-label">กฎข้อบังคับในรายวิชา<span style="color: red;"> *</span></label>
                <input type="text" id="input_regulation_text" name="input_regulation_text" class="form-control"  placeholder="เนื้อหากฎข้อบังคับเพิ่มเติม" value="<?php echo $data['regulation_text'] ?? ''; ?>" required>
              </div>

              <div id="input_year">
                                <label class="form-label">ปีการศึกษาที่เริ่มใช้งาน<span style="color: red;"> *</span></label>
                                <input type="number" class="form-control" name="input_year" id="input_year" value="<?php echo isset($data['year']) ? $data['year'] : ''; ?>" placeholder="ปีการศึกษาที่ใช้งาน" required>
                            </div>

                            <div id="input_term">
                                <label class="form-label">ภาคการศึกษาที่เริ่มใช้งาน<span style="color: red;"> *</span></label>
                                <select name="input_term" class="form-select" required>
                                <option value="" <?php if ($data['term'] == "") echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                                    <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                                    <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                                    <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
                                </select>
                            </div>

          <div class="pt-3 justify-content-center">
          <button type="submit" name="update" id="update" class="btn btn-success">อัปเดต</button>
            <a type="button" href="./regulationmanage.php" class="btn btn-secondary ">กลับ</a>
            
          </div>
          </form>
        </div>
    </div>
    </main>

  </div>
  </div>

</body>

</html>