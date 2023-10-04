<?php
session_start();
require_once "../connect.php";

if (isset($_POST['update'])) {
  $project_id = $_POST['id'];
  $New_project_nameTH = $_POST['input_project_nameTH'];
  $New_project_nameENG = $_POST['input_project_nameENG'];
  $New_student_id1 = $_POST['input_student_id1'];
  $New_student_id2 = $_POST['input_student_id2'];
  $New_student_id3 = $_POST['input_student_id3'];
  $New_teacher_id1 = $_POST['input_teacher_id1'];
  $New_teacher_id2 = $_POST['input_teacher_id2'];
  $New_referee_id = $_POST['input_referee_id'];
  $New_referee_id1 = $_POST['input_referee_id1'];
  $New_referee_id2 = $_POST['input_referee_id2'];
  $New_group_id = $_POST['input_group_id'];

  if (isset($_FILES["input_boundary_path"]) && $_FILES["input_boundary_path"]["size"] > 0) {
    $targetDir = "uploadfileBoundary/";
    $New_boundary_path = basename($_FILES["input_boundary_path"]["name"]);
    $targetFile = $targetDir . $New_boundary_path;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($uploadOk == 1) {
      // ดำเนินการตรวจสอบชื่อไฟล์ซ้ำ
      $stmt = $conn->prepare("SELECT boundary_path FROM `project` WHERE boundary_path = :input_boundary_path");
      $stmt->bindParam(':input_boundary_path', $New_boundary_path);
      $stmt->execute();
      $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existingFile) {
        $_SESSION['error'] = 'ชื่อไฟล์ซ้ำกันในระบบ ไม่สามารถอัปโหลดได้';
        header('location: editproject.php?id='.$project_id);
        exit();
      } else {

        if (move_uploaded_file($_FILES["input_boundary_path"]["tmp_name"], $targetFile)) {
          // เพิ่มข้อมูลลงในฐานข้อมูล
        } else {
          $_SESSION['error'] = 'ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด';
          header('location: editproject.php?id='.$project_id);
          exit();
        }
      }
    } else {
      $_SESSION['error'] = 'กรุณาเลือกไฟล์ใหม่ที่ต้องการอัปโหลด';
      header('location: editproject.php?id='.$project_id);
      exit();
    }
  } else {
    $New_boundary_path = ""; // กำหนดค่าเป็นค่าว่างเมื่อไม่มีไฟล์ถูกอัปโหลด
  }

  // $New_grade = $_POST['input_grade'];
  $New_year = $_POST['input_year'];
  $New_term = $_POST['input_term'];

  try {
    if (!isset($_SESSION['error'])) {
      $New_project_id = $_POST['new_project_id'];

      $New_project_id = empty($New_project_id) ? null : $New_project_id;
      $New_project_nameTH = empty($New_project_nameTH) ? null : $New_project_nameTH;
      $New_project_nameENG = empty($New_project_nameENG) ? null : $New_project_nameENG;
      $New_student_id1 = empty($New_student_id1) ? null : $New_student_id1;
      $New_student_id2 = empty($New_student_id2) ? null : $New_student_id2;
      $New_student_id3 = empty($New_student_id3) ? null : $New_student_id3;
      $New_teacher_id1 = empty($New_teacher_id1) ? null : $New_teacher_id1;
      $New_teacher_id2 = empty($New_teacher_id2) ? null : $New_teacher_id2;
      $New_referee_id = empty($New_referee_id) ? null : $New_referee_id;
      $New_referee_id1 = empty($New_referee_id1) ? null : $New_referee_id1;
      $New_referee_id2 = empty($New_referee_id2) ? null : $New_referee_id2;
      $New_group_id = empty($New_group_id) ? null : $New_group_id;


      if (empty($New_boundary_path)) {
        $New_boundary_path = $_POST["existing_boundary_path"];
      } else {
        $stmt = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id");
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        unlink("uploadfileBoundary/" . $data["boundary_path"]);
      }

      // $New_grade = empty($New_grade) ? null : $New_grade;
      $New_year = empty($New_year) ? null : $New_year;
      $New_term = empty($New_term) ? null : $New_term;

      $sql = $conn->prepare("UPDATE `project` SET project_id = :new_project_id, project_nameTH = :input_project_nameTH, project_nameENG = :input_project_nameENG, student_id1 = :input_student_id1, 
      student_id2 = :input_student_id2, student_id3 = :input_student_id3 ,teacher_id1 = :input_teacher_id1, teacher_id2 = :input_teacher_id2, referee_id = :input_referee_id,
      referee_id1 = :input_referee_id1, referee_id2 = :input_referee_id2, group_id = :input_group_id, boundary_path = :input_boundary_path, year = :input_year,  term = :input_term WHERE project_id = :id");
      $sql->bindParam(':new_project_id', $New_project_id);
      $sql->bindParam(':id', $project_id);
      $sql->bindParam(':input_project_nameTH', $New_project_nameTH);
      $sql->bindParam(':input_project_nameENG', $New_project_nameENG);
      $sql->bindParam(':input_student_id1', $New_student_id1);
      $sql->bindParam(':input_student_id2', $New_student_id2);
      $sql->bindParam(':input_student_id3', $New_student_id3);
      $sql->bindParam(':input_teacher_id1', $New_teacher_id1);
      $sql->bindParam(':input_teacher_id2', $New_teacher_id2);
      $sql->bindParam(':input_referee_id', $New_referee_id);
      $sql->bindParam(':input_referee_id1', $New_referee_id1);
      $sql->bindParam(':input_referee_id2', $New_referee_id2);
      $sql->bindParam(':input_group_id', $New_group_id);
      $sql->bindParam(':input_boundary_path', $New_boundary_path);
      // $sql->bindParam(':input_grade', $New_grade);
      $sql->bindParam(':input_year', $New_year);
      $sql->bindParam(':input_term', $New_term);

      $sql->execute();
      if ($sql) {
        $_SESSION['success'] = '<strong>โครงงาน </strong>' . $project_id . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
        header("location: ./projectmanage.php");
        exit();
      } else {
        $_SESSION['error'] = 'ข้อมูลโครงงานยังไม่ได้รับการแก้ไข';
        header("location: projectmanage.php");
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


  <title>หน้าแก้ไขข้อมูลโปรเจคในรายวิชา</title>

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

        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขข้อมูลโปรเจคในรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./projectmanage.php">จัดการข้อมูลโครงงาน</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลโปรเจคในรายวิชา</li>
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
            <form action="./editproject.php" method="post" enctype="multipart/form-data">
              <?php
              $data = [];
              if (isset($_GET['id'])) {
                $project_id = $_GET['id'];
                $stmt = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id");
                $stmt->bindParam(':project_id', $project_id);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
              }
              ?>
              <input type="hidden" name="id" value="<?php echo $data['project_id'] ?? ''; ?>">

              <div class="pt-3 justify-content-center">
                <label class="form-label">รหัสกลุ่มโครงงาน</label>
                <input type="text" class="form-control" name="new_project_id" placeholder="25xx-xxx" required value="<?php echo $data['project_id'] ?? ''; ?>" placeholder="รหัสโครงงาน" readonly>
              </div>


              <label class="form-label">ชื่อโครงงานภาษาไทย</label>
              <div class="form-floating" id="input_project_nameTH">
                <textarea class="form-control" name="input_project_nameTH" id="input_project_nameTH" placeholder="เนื้อหากำหนดการ"><?php echo $data['project_nameTH'] ?? ''; ?></textarea>
                <!-- <label for="input_project_nameTH">ชื่อโครงงานภาษาไทยของนักศึกษา</label> -->
              </div>

              <label class="form-label">ชื่อโครงงานภาษาอังกฤษ</label>
              <div class="form-floating" id="input_project_nameENG">
                <textarea class="form-control" name="input_project_nameENG" id="input_project_nameENG" placeholder="เนื้อหากำหนดการ"><?php echo $data['project_nameENG'] ?? ''; ?></textarea>
                <!-- <label for="input_project_nameENG">ชื่อโครงงานภาษาอังกฤษของนักศึกษา</label> -->
              </div>


              <div id="input_student_id1">
                <label class="form-label">นักศึกษา 1</label>
                <input type="number" class="form-control" name="input_student_id1" id="input_student_id1" value="<?php echo $data['student_id1']; ?>" placeholder="รหัสรหัสประจำตัวนักศึกษา 1">
              </div>

              <div id="input_student_id2">
                <label class="form-label">นักศึกษา 2</label>
                <input type="number" class="form-control" name="input_student_id2" id="input_student_id2" value="<?php echo $data['student_id2']; ?>" placeholder="รหัสรหัสประจำตัวนักศึกษา 2">
              </div>

              <div id="input_student_id3">
                <label class="form-label">นักศึกษา 3</label>
                <input type="number" class="form-control" name="input_student_id3" id="input_student_id3" value="<?php echo $data['student_id3']; ?>" placeholder="รหัสรหัสประจำตัวนักศึกษา 3">
              </div>

              <div id="input_teacher_id1">
                <label class="form-label">อาจารย์ที่ปรึกษาหลัก</label>
                <select id="selectbox" name="input_teacher_id1" class="form-select">
                  <!-- <option value="">Select Teacher</option> -->
                  <?php
                  $teachers = $conn->query("SELECT * FROM `teacher`");
                  $teachers->execute();
                  while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                    // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                    if ($teacher['teacher_id'] == 1) {
                      continue;
                    }
                    $selected = ($teacher['teacher_id'] == $data['teacher_id1']) ? 'selected' : '';
                    echo '<option value="' . $teacher['teacher_id'] . '" ' . $selected . '>';
                    echo $teacher['position'] . ' ' . $teacher['firstname'];
                    echo '</option>';
                  }
                  ?>
                </select>
              </div>

              <div id="input_teacher_id2">
                <label class="form-label">อาจารย์ที่ปรึกษาร่วม</label>
                <select id="selectbox" name="input_teacher_id2" class="form-select">
                  <option value="">เลือกอาจารย์ที่ปรึกษาร่วม</option>
                  <?php
                  $teachers = $conn->query("SELECT * FROM `teacher`");
                  $teachers->execute();
                  while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                    // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                    if ($teacher['teacher_id'] == 1) {
                      continue;
                    }
                    $selected = ($teacher['teacher_id'] == $data['teacher_id2']) ? 'selected' : '';
                    echo '<option value="' . $teacher['teacher_id'] . '" ' . $selected . '>';
                    echo $teacher['position'] . ' ' . $teacher['firstname'];
                    echo '</option>';
                  }
                  ?>
                </select>
              </div>

              <div id="input_referee_id">
                <label class="form-label">ประธานกรรมการ</label>
                <select id="selectbox" name="input_referee_id" class="form-select">
                  <!-- <option value="">Select Teacher</option> -->
                  <?php
                  $teachers = $conn->query("SELECT * FROM `teacher`");
                  $teachers->execute();
                  while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                    // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                    if ($teacher['teacher_id'] == 1) {
                      continue;
                    }
                    $selected = ($teacher['teacher_id'] == $data['referee_id']) ? 'selected' : '';
                    echo '<option value="' . $teacher['teacher_id'] . '" ' . $selected . '>';
                    echo $teacher['position'] . ' ' . $teacher['firstname'];
                    echo '</option>';
                  }
                  ?>
                </select>
              </div>

              <div id="input_referee_id1">
                <label class="form-label">กรรมการ 1</label>
                <select id="selectbox" name="input_referee_id1" class="form-select">
                  <!-- <option value="">Select Teacher</option> -->
                  <?php
                  $teachers = $conn->query("SELECT * FROM `teacher`");
                  $teachers->execute();
                  while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                    // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                    if ($teacher['teacher_id'] == 1) {
                      continue;
                    }
                    $selected = ($teacher['teacher_id'] == $data['referee_id1']) ? 'selected' : '';
                    echo '<option value="' . $teacher['teacher_id'] . '" ' . $selected . '>';
                    echo $teacher['position'] . ' ' . $teacher['firstname'];
                    echo '</option>';
                  }
                  ?>
                </select>
              </div>

              <div id="input_referee_id2">
                <label class="form-label">กรรมการ 2</label>
                <select id="selectbox" name="input_referee_id2" class="form-select">
                  <!-- <option value="">Select Teacher</option> -->
                  <?php
                  $teachers = $conn->query("SELECT * FROM `teacher`");
                  $teachers->execute();
                  while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                    // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                    if ($teacher['teacher_id'] == 1) {
                      continue;
                    }
                    $selected = ($teacher['teacher_id'] == $data['referee_id2']) ? 'selected' : '';
                    echo '<option value="' . $teacher['teacher_id'] . '" ' . $selected . '>';
                    echo $teacher['position'] . ' ' . $teacher['firstname'];
                    echo '</option>';
                  }
                  ?>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label" for="selectbox">กลุ่มเรียน</label>
                <select id="selectbox" name="input_group_id" class="form-select">
                  <option value="<?php echo null ?>">เลือกกลุ่มเรียน</option>
                  <?php
                  $groups = $conn->query("SELECT * FROM `groups` ORDER BY group_id DESC");
                  $groups->execute();
                  while ($group = $groups->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($group['group_id'] == $data['group_id']) ? 'selected' : '';
                    echo '<option value="' . $group['group_id'] . '" ' . $selected . '>';
                    echo $group['group_name'];
                    echo '</option>';
                  }
                  ?>
                </select>
              </div>

              <div id="input_boundary_path">
                <label class="form-label">ขอบเขตโครงงาน</label>
                <input type="file" class="form-control" name="input_boundary_path" id="input_boundary_path" accept=".pdf">
                <?php if (!empty($data['boundary_path'])) : ?>
                  <input type="hidden" name="existing_boundary_path" value="<?php echo $data['boundary_path']; ?>">
                  <!-- <p class="fw-medium text-danger">ชื่อไฟล์ปัจจุบัน: <?php echo $data['boundary_path']; ?></p> -->
                  <a href="<?php echo './uploadfileBoundary/' . $data['boundary_path']; ?>" target="_blank"><?php echo $data['boundary_path']; ?></a>
                  <a onclick="return confirm('Are you sure you want to delete File boundary project?');" href="deleteFile_boundary.php?id=<?php echo $data['project_id'] ?>" class="btn btn-danger">Delete File</a>
                <?php endif; ?>
              </div>


              <div id="input_year">
                <label class="form-label">ปีการศึกษา</label>
                <input type="number" class="form-control" name="input_year" id="input_year" value="<?php echo $data['year']; ?>" placeholder="ปีการศึกษา 25XX">
              </div>


              <div id="input_term">
                <label class="form-label">ภาคการศึกษา</label>
                <select id="selectbox" name="input_term" class="form-select">
                  <option value="" <?php if (empty($data['term'])) echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                  <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                  <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                  <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
                </select>
              </div>

              <div class="pt-3 justify-content-center">
              <button type="submit" name="update" id="update" class="btn btn-success">อัปเดต</button>
                <a type="button" href="./projectmanage.php" class="btn btn-secondary ">กลับ</a>
                
              </div>

            </form>

          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Footer -->
  <div class="FooterBg">
    <div class="container">
      <footer_admin-component></footer_admin-component> <!-- component.js footer-->
    </div>
  </div>
  <!-- End Footer -->

  <script src="../component.js"></script> <!-- component.js -->
</body>

</html>