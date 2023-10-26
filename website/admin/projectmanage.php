<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
}

if (isset($_SESSION['selectedYear'])) {
  unset($_SESSION['selectedYear']);
}
if (isset($_SESSION['selectedTerm'])) {
  unset($_SESSION['selectedTerm']);
}
if (isset($_SESSION['selectedGroup'])) {
  unset($_SESSION['selectedGroup']);
}

if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];

  // ดึงข้อมูลไฟล์ที่ต้องการลบ
  $filestmt = $conn->prepare("SELECT boundary_path FROM `project` WHERE project_id = :delete_id");
  $filestmt->bindParam(':delete_id', $delete_id);
  $filestmt->execute();
  $fileData = $filestmt->fetch(PDO::FETCH_ASSOC);
  $boundary_path = $fileData['boundary_path'];


  // ลบข้อมูลจากฐานข้อมูล
  $deletestmt = $conn->prepare("DELETE FROM `project` WHERE project_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);
  if ($deletestmt->execute()) {
    $_SESSION['success'] = "ลบข้อมูลรหัสกลุ่มโครงงาน " . $delete_id . " เสร็จสิ้น";
    if ($boundary_path && $boundary_path !== false) {
      $fileToDelete = "uploadfileBoundary/" . $boundary_path;
      if (is_file($fileToDelete)) {
        echo "<script>hideLoading();</script>"; // เรียกใช้ฟังก์ชันเพื่อซ่อน Popup Loading
        if (unlink($fileToDelete)) {
          // $_SESSION['success'] = "ลบไฟล์เสร็จสมบูรณ์";
        } else {
          $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบไฟล์";
          header("location: ./projectmanage.php");
          exit;
        }
      } else {
        $_SESSION['error'] = "ไม่พบไฟล์ที่ต้องการลบ";
        header("location: ./projectmanage.php");
        exit;
      }
    }
    header("location: ./projectmanage.php");
    exit;
  } else {
    $_SESSION['success'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    header("location: ./projectmanage.php");
    exit;
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

  <title>หน้าจัดการข้อมูลโครงงาน</title>

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

      <sidebar_admin-component></sidebar_admin-component> <!-- component.js sidebar_admin-->

      <!-- Modal -->
      <div class="modal fade" id="projectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มโครงงานในรายวิชา</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="./add_dataproject.php" method="post" enctype="multipart/form-data" onsubmit="showLoading('form1')">
                <?php
                $defaultSystemId = 1;
                $stmt = $conn->prepare("SELECT * FROM `defaultsystem` WHERE default_system_id = :id");
                $stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <div id="inputproject_id">
                  <label class="form-label">รหัสกลุ่มโครงงานนักศึกษา<span style="color: red;"> *</span></label>
                  <input type="text" class="form-control" name="inputproject_id" id="inputproject_id" value="<?php echo generateNewProjectId($conn, $data); ?>" placeholder="รหัสโครงงาน" required>
                  <?php
                  function generateNewProjectId($conn, $data)
                  {

                    // $currentYear = date("Y");
                    $year = $data['year'];

                    $sql = "SELECT project_id FROM `project` ORDER BY project_id DESC LIMIT 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $project_idMax = $stmt->fetchColumn();

                    if (empty($project_idMax)) {
                      // ถ้าไม่มีรหัสโครงงานในฐานข้อมูล
                      $project_idMax = $year . "-" . "001";
                    } else {
                      $project_idMaxs = explode("-", $project_idMax);

                      if ($project_idMaxs[0] < $year) {
                        // กรณีปีปัจจุบันไม่มีรหัสโครงงานหรือมากกว่ารหัสโครงงานล่าสุดในฐานข้อมูล
                        $project_idMax = $year . "-" . "001";
                      } else if ($project_idMaxs[0] == $year) {
                        // กรณีปีปัจจุบันเป็นปีเดียวกับรหัสโครงงานล่าสุดในฐานข้อมูล
                        $project_idMaxs[1]++;
                        $project_idMax = $project_idMaxs[0] . "-" . str_pad($project_idMaxs[1], 3, "0", STR_PAD_LEFT);
                      }
                    }

                    return $project_idMax;
                  }
                  ?>

                </div>

                <label class="form-label">ชื่อโครงงานภาษาไทย<span style="color: red;"> *</span></label>
                <div class="form-floating" id="inputproject_nameTH">
                  <textarea class="form-control" name="inputproject_nameTH" id="inputproject_nameTH" placeholder="เนื้อหากำหนดการ" required><?php echo $data['project_nameTH'] ?? ''; ?></textarea>
                  <label for="inputproject_nameTH">ชื่อโครงงานภาษาไทยของนักศึกษา</label>
                </div>

                <label class="form-label">ชื่อโครงงานภาษาอังกฤษ<span style="color: red;"> *</span></label>
                <div class="form-floating" id="inputproject_nameENG">
                  <textarea class="form-control" name="inputproject_nameENG" id="inputproject_nameENG" placeholder="เนื้อหากำหนดการ" required><?php echo $data['project_nameENG'] ?? ''; ?></textarea>
                  <label for="inputproject_nameENG">ชื่อโครงงานภาษาอังกฤษของนักศึกษา</label>
                </div>

                <div id="inputstudent_id1">
                  <label class="form-label">นักศึกษาคนที่ 1<span style="color: red;"> *</span></label>
                  <input type="number" class="form-control" name="inputstudent_id1" id="inputstudent_id1" placeholder="หัสประจำตัวนักศึกษาคนที่ 1 (ไม่มีขีด)" required>
                </div>

                <div id="inputstudent_id2">
                  <label class="form-label">นักศึกษาคนที่ 2</label>
                  <input type="number" class="form-control" name="inputstudent_id2" id="inputstudent_id2" placeholder="รหัสประจำตัวนักศึกษาคนที่ 2 (ไม่มีขีด)">
                </div>

                <div id="student_id3">
                  <label class="form-label">นักศึกษาคนที่ 3</label>
                  <input type="number" class="form-control" name="inputstudent_id3" id="inputstudent_id3" placeholder="รหัสประจำตัวนักศึกษาคนที่ 3 (ไม่มีขีด)">
                </div>

                <div id="inputteacher_id1">
                  <label class="form-label">อาจารย์ที่ปรึกษาหลัก<span style="color: red;"> *</span></label>
                  <select id="selectbox" name="inputteacher_id1" class="form-select" required>
                    <option value="">เลือกอาจารย์ที่ปรึกษาหลัก</option>
                    <?php
                    $teachers = $conn->query("SELECT * FROM `teacher`");
                    $teachers->execute();
                    while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                      // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                      if ($teacher['teacher_id'] == 1) {
                        continue;
                      }
                      echo '<option value="' . $teacher['teacher_id'] . '">';
                      echo $teacher['position'] . ' ' . $teacher['firstname'];
                      echo '</option>';
                    }
                    ?>
                  </select>

                </div>

                <div id="inputteacher_id2">
                  <label class="form-label">อาจารย์ที่ปรึกษาร่วม</label>
                  <select id="selectbox" name="inputteacher_id2" class="form-select">
                    <option value="">เลือกอาจารย์ที่ปรึกษาร่วม</option>
                    <?php
                    $teachers = $conn->query("SELECT * FROM `teacher`");
                    $teachers->execute();
                    while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                      // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                      if ($teacher['teacher_id'] == 1) {
                        continue;
                      }
                      echo '<option value="' . $teacher['teacher_id'] . '">';
                      echo $teacher['position'] . ' ' . $teacher['firstname'];
                      echo '</option>';
                    }
                    ?>
                  </select>
                </div>

                <div id="inputreferee_id">
                  <label class="form-label">ประธานกรรมการ<span style="color: red;"> *</span></label>
                  <select id="selectbox" name="inputreferee_id" class="form-select" required>
                    <option value="">เลือกอาจารย์ประธานกรรมการ</option>
                    <?php
                    $teachers = $conn->query("SELECT * FROM `teacher`");
                    $teachers->execute();
                    while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                      // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                      if ($teacher['teacher_id'] == 1) {
                        continue;
                      }
                      echo '<option value="' . $teacher['teacher_id'] . '">';
                      echo $teacher['position'] . ' ' . $teacher['firstname'];
                      echo '</option>';
                    }
                    ?>
                  </select>
                </div>

                <div id="inputreferee_id1">
                  <label class="form-label">กรรมการ 1<span style="color: red;"> *</span></label>
                  <select id="selectbox" name="inputreferee_id1" class="form-select" required>
                    <option value="">เลือกอาจารย์กรรมการ 1</option>
                    <?php
                    $teachers = $conn->query("SELECT * FROM `teacher`");
                    $teachers->execute();
                    while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                      // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                      if ($teacher['teacher_id'] == 1) {
                        continue;
                      }
                      echo '<option value="' . $teacher['teacher_id'] . '">';
                      echo $teacher['position'] . ' ' . $teacher['firstname'];
                      echo '</option>';
                    }
                    ?>
                  </select>
                </div>

                <div id="inputreferee_id2">
                  <label class="form-label">กรรมการ 2<span style="color: red;"> *</span></label>
                  <select id="selectbox" name="inputreferee_id2" class="form-select" required>
                    <option value="">เลือกอาจารย์กรรมการ 2</option>
                    <?php
                    $teachers = $conn->query("SELECT * FROM `teacher`");
                    $teachers->execute();
                    while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                      // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                      if ($teacher['teacher_id'] == 1) {
                        continue;
                      }
                      echo '<option value="' . $teacher['teacher_id'] . '">';
                      echo $teacher['position'] . ' ' . $teacher['firstname'];
                      echo '</option>';
                    }
                    ?>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label" for="selectbox">กลุ่มเรียน<span style="color: red;"> *</span></label>
                  <select id="selectbox" name="input_group_id" class="form-select" required>
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

                <div id="inputboundary_path">
                  <label class="form-label">ขอบเขตโครงงาน</label>
                  <input type="file" class="form-control" name="inputboundary_path" id="inputboundary_path" placeholder="ที่อยู่ไฟล์" accept=".pdf">
                </div>

                <div class="loading-overlay mt-2 mb-2" id="form1-loadingOverlay" style="display: none;">
                  <div class="d-flex align-items-center text-center">
                    <strong class="text-primary" role="status">กำลังอัปโหลดไฟล์...</strong>
                    <div class="spinner-border text-primary ms-3" role="status"></div>
                  </div>
                </div>

                <!-- <div id="inputgrade">
                  <label class="form-label">เกรด</label>
                  <select id="selectbox" name="inputgrade" class="form-select">
                    <option value="">NULL</option>
                    <option value="A">A</option>
                    <option value="B+">B+</option>
                    <option value="B">B</option>
                    <option value="C+">C+</option>
                    <option value="C">C</option>
                    <option value="D+">D+</option>
                    <option value="D">D</option>
                    <option value="F">F</option>
                    <option value="W">W</option>
                    <option value="I">i</option>
                  </select>
                </div> -->

                <div id="inputyear">
                  <label class="form-label">ปีการศึกษา<span style="color: red;"> *</span></label>
                  <input type="number" class="form-control" name="inputyear" id="inputyear" value="<?php echo isset($data['year']) ?  $data['year'] : '' ?>" placeholder="ปีการศึกษาที่ลงทะเบียน" required>
                </div>

                <div id="inputterm">
                  <label class="form-label">ภาคการศึกษา<span style="color: red;"> *</span></label>
                  <select id="selectbox" name="inputterm" class="form-select" required>
                    <option value="" <?php if ($data['term'] == "") echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                    <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                    <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                    <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
                  </select>
                </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
              <button type="submit" name="submit" class="btn btn-primary">เพิ่มข้อมูล</button>
            </div>
            </form>
          </div>

        </div>

      </div>
      <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">
        <div class="row">

          <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลโครงงานในรายวิชา</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb fs-5 mt-2 ms-3">
              <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
              <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลโครงงาน</li>
            </ol>
          </nav>
          <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
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
            <div class="card shadow-sm">
              <div class="card-header justify-content-between align-items-center">
                <form action="./projectmanage.php" method="POST">
                  <div class="row g-3 mb-2">

                    <div class="col-md-2">
                      <label for="filterYear" class="form-label">ฟิลเตอร์ปีการศึกษา</label>
                      <select class="form-select" name="filteryear">
                        <?php
                        if (isset($_POST['resetfilter'])) {
                          unset($_SESSION['selectedYear']);
                          unset($_SESSION['selectedTerm']);
                          unset($_SESSION['selectedGroup']);
                        }
                        if (isset($_POST['submitfilter'])) {
                          $_SESSION['selectedYear'] = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                          $_SESSION['selectedTerm'] = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;
                          $_SESSION['selectedGroup'] = isset($_POST['filtergroup']) ? $_POST['filtergroup'] : null;
                          $selectedYear =  $_SESSION['selectedYear'];
                          $selectedTerm =   $_SESSION['selectedTerm'];
                          $selectedGroup = $_SESSION['selectedGroup'];
                        }
                        $years = $conn->query("SELECT DISTINCT year FROM `project` ORDER BY year DESC");
                        $years->execute();
                        $selectedYear = isset($_SESSION['selectedYear']) ? $_SESSION['selectedYear'] : null;
                        ?>
                        <option value="">เลือกปีการศึกษา</option>
                        <?php
                        while ($datayear = $years->fetch(PDO::FETCH_ASSOC)) {
                          $yearValue = $datayear['year'];
                          $isYearSelected = ($selectedYear == $yearValue) ? 'selected' : ''; // เพิ่มเงื่อนไขเช็คค่า selected
                        ?>
                          <option value="<?php echo $yearValue; ?>" <?php echo $isYearSelected; ?>>
                            <?php echo $yearValue; ?>
                          </option>
                        <?php } ?>

                      </select>
                    </div>

                    <div class="col-md-3">
                      <label for="filterTerm" class="form-label">ฟิลเตอร์ภาคการศึกษา</label>
                      <select class="form-select" name="filterterm">
                        <?php
                        $terms = $conn->query("SELECT DISTINCT term FROM `project` ORDER BY term DESC");
                        $terms->execute();
                        $selectedTerm = isset($_SESSION['selectedTerm']) ? $_SESSION['selectedTerm'] : null; // ดึงค่าที่ถูกเลือกจาก Session Variables

                        ?>
                        <option value="">เลือกภาคการศึกษา</option>
                        <?php
                        while ($dataterm = $terms->fetch(PDO::FETCH_ASSOC)) {
                          $termValue = $dataterm['term'];
                          $isTermSelected = ($selectedTerm == $termValue) ? 'selected' : ''; // เพิ่มเงื่อนไขเช็คค่า selected
                        ?>
                          <option value="<?php echo $termValue; ?>" <?php echo $isTermSelected; ?>>
                            <?php echo $termValue; ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>

                    <div class="col-md-2">
                      <label for="filtergroup" class="form-label">ฟิลเตอร์กลุ่มเรียน</label>
                      <select class="form-select" name="filtergroup">
                        <?php
                        $groups = $conn->prepare("SELECT groups.group_name 
     FROM `groups`
     LEFT JOIN `project` ON groups.group_id = project.group_id
     GROUP BY groups.group_id, groups.group_name
     HAVING COUNT(project.group_id) >= 1
     ORDER BY groups.group_name DESC");

                        $groups->execute();
                        $selectedGroup = isset($_SESSION['selectedGroup']) ? $_SESSION['selectedGroup'] : null; // ดึงค่าที่ถูกเลือกจาก Session Variables

                        ?>
                        <option value="">เลือกกลุ่มเรียน</option>
                        <?php
                        while ($datagroup = $groups->fetch(PDO::FETCH_ASSOC)) {
                          $groupValue = $datagroup['group_name'];
                          $isGroupSelected = ($selectedGroup == $groupValue) ? 'selected' : ''; // เพิ่มเงื่อนไขเช็คค่า selected
                        ?>
                          <option value="<?php echo $groupValue; ?>" <?php echo $isGroupSelected; ?>>
                            <?php echo $groupValue; ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>


                    <div class="col-auto d-flex align-items-end justify-content-start">
                      <button type="submit" id="submitfilter" name="submitfilter" class="btn btn-success">ฟิลเตอร์</button>
                    </div>

                    <div class="col-auto d-flex align-items-end justify-content-start">
                      <button type="submit" id="resetfilter" name="resetfilter" class="btn btn-warning">รีเซ็ตฟิลเตอร์</button>
                    </div>


                  </div>
                </form>
                <div class="row pb-2">
                  <div class="col">
                    <form action="./projectmanage.php" method="POST" class="d-flex">
                      <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาชื่อโครงงาน,ชื่อนักศึกษา,ชื่ออาจารย์ที่ปรึกษา,ชื่อกรรมการ" autocomplete="off" required>
                      <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                    </form>
                  </div>
                  <div class="col-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#projectModal" data-bs-whatever="@mdo">เพิ่มโครงงาน</button>
                  </div>
                </div>
              </div>

              <div class="card-body">
                <div class="col-md-5">
                  <div class="list-group" style="position: absolute; width: 27em;" id="show-list"></div>
                </div>

                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="text-center" scope="col" style="width : 4%;">ลำดับที่</th>
                        <th scope="col">รหัสกลุ่มโครงงาน</th>
                        <th scope="col">ชื่อโครงงานภาษาไทย</th>
                        <th scope="col">ชื่อโครงงานภาษาอังกฤษ</th>
                        <th scope="col">นักศึกษา 1</th>
                        <th scope="col">นักศึกษา 2</th>
                        <th scope="col">นักศึกษา 3</th>
                        <th scope="col">อาจารย์ที่ปรึกษาหลัก</th>
                        <th scope="col">อาจารย์ที่ปรึกษาร่วม</th>
                        <th scope="col">ประธานกรรมการ</th>
                        <th scope="col">กรรมการ 1</th>
                        <th scope="col">กรรมการ 2</th>
                        <th scope="col">กลุ่มเรียน</th>
                        <th scope="col">ขอบเขตโครงงาน</th>
                        <th scope="col">เอกสารโปสเตอร์</th>
                        <th scope="col">เอกสารรูปเล่มฉบับเต็ม</th>
                        <th scope="col">ปีการศึกษา</th>
                        <th scope="col">ภาคการศึกษา</th>
                        <th scope="col">Actions</th>
                      </tr>
                    <tbody>
                      <?php

                      function giveFileById($conn, $project_id, $file_chapter)
                      {
                        $sql = "SELECT file_path FROM `file` WHERE (project_id = :project_id and (file_chapter = :file_chapter and file_status = 1))";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':project_id', $project_id);
                        $stmt->bindParam(':file_chapter', $file_chapter);
                        $stmt->execute();

                        $result = $stmt->fetch(); // รับผลลัพธ์จากคำสั่ง SQL

                        if ($result !== false) { // ตรวจสอบว่ามีข้อมูลหรือไม่
                          return $result['file_path']; // ส่งกลับข้อมูลไฟล์
                        }

                        return null; // หรือส่งค่าอื่นที่เหมาะสมตามสถานการณ์
                      }


                      function giveGroupById($conn, $group_id)
                      {
                        $sql = "SELECT * FROM `groups` WHERE group_id = :group_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':group_id', $group_id);
                        $stmt->execute();
                        return $stmt->fetch();
                      }

                      function giveStudentById($conn, $student_id)
                      {
                        $sql = "SELECT * FROM `student` WHERE student_id = :student_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':student_id', $student_id);
                        $stmt->execute();
                        return $stmt->fetch();
                      }

                      function giveTeacherById($conn, $teacher_id)
                      {
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $teacher_id);
                        $stmt->execute();
                        return $stmt->fetch();
                      }

                      function giveTeacherPositionById($Position)
                      {
                        switch ($Position) {
                          case "ศาสตราจารย์":
                            return $Position = "ศ.";
                            break;
                          case "ศาสตราจารย์ ดร.":
                            return $Position = "ศ.ดร.";
                            break;
                          case "รองศาสตราจารย์":
                            return $Position = "รศ.";
                            break;
                          case "รองศาสตราจารย์ ดร.":
                            return $Position = "รศ.ดร.";
                            break;
                          case "ผู้ช่วยศาสตราจารย์":
                            return $Position = "ผศ.";
                            break;
                          case "ผู้ช่วยศาสตราจารย์ ดร.":
                            return $Position = "ผศ.ดร.";
                            break;
                          case "อาจารย์":
                            return $Position = "อ.";
                            break;
                          case "อาจารย์ ดร.":
                            return $Position = "อ.ดร.";
                            break;
                          case "ดร.":
                            return $Position = "ดร.";
                            break;
                          default:
                            return $Position = $Position;
                        }
                      }
                      //start
                      // filter term and year
                      if (isset($_POST['submitfilter'])) {
                        // $selectedYear = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                        // $selectedTerm = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;
                        // $selectedGroup = isset($_POST['filtergroup']) ? $_POST['filtergroup'] : null;

                        if (empty($selectedYear) && empty($selectedTerm) && empty($selectedGroup)) {
                          $sql = "SELECT * FROM `project`";
                          $stmt = $conn->prepare($sql);
                          $stmt->execute();
                          $filteredData = $stmt->fetchAll();
                        } elseif (empty($selectedYear) && !empty($selectedTerm) && !empty($selectedGroup)) {
                          // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                          $sql = "SELECT project.*
                          FROM `project`
                          LEFT JOIN `groups` ON project.group_id = groups.group_id
                          WHERE term LIKE :term
                          AND (groups.group_name LIKE :group_name AND :group_name <> '')";

                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':term', $selectedTerm);
                          $stmt->bindParam(':group_name', $selectedGroup);
                          $stmt->execute();
                          $filteredData = $stmt->fetchAll();
                        } elseif (!empty($selectedYear) && !empty($selectedTerm) && empty($selectedGroup)) {
                          // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                          $sql = "SELECT project.*
                          FROM `project`
                          LEFT JOIN `groups` ON project.group_id = groups.group_id
                          WHERE year LIKE :year
                          AND term LIKE :term";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':year', $selectedYear);
                          $stmt->bindParam(':term', $selectedTerm);
                          $stmt->execute();
                          $filteredData = $stmt->fetchAll();
                        } elseif (!empty($selectedYear) && empty($selectedTerm) && !empty($selectedGroup)) {
                          // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                          $sql = "SELECT project.*
                          FROM `project`
                          LEFT JOIN `groups` ON project.group_id = groups.group_id
                          WHERE year LIKE :year
                          AND (groups.group_name LIKE :group_name AND :group_name <> '')";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':year', $selectedYear);
                          $stmt->bindParam(':group_name', $selectedGroup);

                          $stmt->execute();
                          $filteredData = $stmt->fetchAll();
                        } elseif (!empty($selectedYear) && !empty($selectedTerm) && !empty($selectedGroup)) {
                          // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                          $sql = "SELECT project.*
                          FROM `project`
                          LEFT JOIN `groups` ON project.group_id = groups.group_id
                          WHERE year LIKE :year
                          AND term LIKE :term
                          AND (groups.group_name LIKE :group_name AND :group_name <> '')";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':year', $selectedYear);
                          $stmt->bindParam(':term', $selectedTerm);
                          $stmt->bindParam(':group_name', $selectedGroup);

                          $stmt->execute();
                          $filteredData = $stmt->fetchAll();
                        } else {
                          // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                          $sql = "SELECT project.*
                          FROM `project`
                          LEFT JOIN `groups` ON project.group_id = groups.group_id
                          WHERE year LIKE :year
                          OR term LIKE :term
                          OR (groups.group_name LIKE :group_name AND :group_name <> '')";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':year', $selectedYear);
                          $stmt->bindParam(':term', $selectedTerm);
                          $stmt->bindParam(':group_name', $selectedGroup);

                          $stmt->execute();
                          $filteredData = $stmt->fetchAll();
                        }
                        $index = 1;
                        if (!$filteredData) {
                          echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                        } else {
                          foreach ($filteredData as $project) {
                            //นักศึกษา 1
                            $student1 = giveStudentById($conn, $project['student_id1']);
                            //นักศึกษา 2
                            $student2 = ($project['student_id2']) ? giveStudentById($conn, $project['student_id2']) : null;
                            //นักศึกษา 3
                            $student3 = ($project['student_id3']) ? giveStudentById($conn, $project['student_id3']) : null;
                            //อาจารย์ที่ปรึกษาหลัก
                            $teacher1 = giveTeacherById($conn, $project['teacher_id1']);
                            //อาจารย์ที่ปรึกษาร่วม
                            $teacher2 = ($project['teacher_id2']) ? giveTeacherById($conn, $project['teacher_id2']) : null;
                            //ประธานกรรมการ
                            $referee_id = giveTeacherById($conn, $project['referee_id']);
                            //กรรมการ 1
                            $referee_id1 = giveTeacherById($conn, $project['referee_id1']);
                            //กรรมการ 2
                            $referee_id2 = giveTeacherById($conn, $project['referee_id2']);
                            //เอกสารโปสเตอร์
                            $fullDocument1 = giveFileById($conn, $project['project_id'], 13);
                            //เอกสารรูปเล่มฉบับเต็ม
                            $fullDocument2 = giveFileById($conn, $project['project_id'], 14);
                            //กลุ่มเรียน
                            $group_id = ($project['group_id']) ? giveGroupById($conn, $project['group_id']) : null;
                      ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <th scope="row"><?php echo $project['project_id']; ?></th>
                              <td><?php echo $project['project_nameTH']; ?></td>
                              <td><?php echo $project['project_nameENG']; ?></td>
                              <td><?php echo $student1['firstname']; ?></td>
                              <td><?php echo $student2 ? $student2['firstname'] : ''; ?></td>
                              <td><?php echo $student3 ? $student3['firstname'] : ''; ?></td>
                              <td><?php echo giveTeacherPositionById($teacher1['position']) . $teacher1['firstname']; ?></td>
                              <td><?php if (empty($teacher2)) {
                                    echo "";
                                  } else {
                                    echo giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'];
                                  } ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id['position']) . $referee_id['firstname']; ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id1['position']) . $referee_id1['firstname']; ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id2['position']) . $referee_id2['firstname']; ?></td>
                              <td><?php echo $group_id ? $group_id['group_name'] : ''; ?></td>

                              <td><a href="<?php echo './uploadfileBoundary/' . $project['boundary_path']; ?>" target="_blank"><?php echo $project['boundary_path']; ?></a></td>
                              <td><a href="<?php echo '.././student/fileUpload/' . $fullDocument1; ?>" target="_blank"><?php echo $fullDocument1; ?></a></td>
                              <td><a href="<?php echo '.././student/fileUpload/' . $fullDocument2; ?>" target="_blank"><?php echo $fullDocument2; ?></a></td>


                              <td><?php echo $project['year']; ?></td>
                              <td><?php echo $project['term']; ?></td>
                              <td>
                                <a href="Stduploadfile.php?id=<?php echo $project['project_id']; ?>" class="btn btn-info text-white">ความคืบหน้า</a>
                                <a href="editproject.php?id=<?php echo $project['project_id']; ?>" class="btn btn-warning mt-1">แก้ไขข้อมูล</a>
                                <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $project['project_id']; ?>" class="btn btn-danger mt-1">ลบข้อมูล</a>
                              </td>
                            </tr>
                          <?php }
                        }
                      } elseif (isset($_POST['submitsearch'])) {
                        $SearchText = $_POST['search'];
                        $isStudent = strpos($SearchText, '(นักศึกษา)') !== false;
                        $isTeacherAdvisor = strpos($SearchText, '(เป็นอาจารย์ที่ปรึกษาหลัก)') !== false;
                        $isTeacherAdvisorCoop = strpos($SearchText, '(เป็นอาจารย์ที่ปรึกษาร่วม)') !== false;
                        $isReferee = strpos($SearchText, '(เป็นกรรมการ)') !== false;

                        $Project = "SELECT project.*, 
                                          student1.firstname AS student1_firstname,            
                                          student2.firstname AS student2_firstname,             
                                          student3.firstname AS student3_firstname
                                          FROM `project`
                                          LEFT JOIN `student` AS student1 ON project.student_id1 = student1.student_id
                                          LEFT JOIN `student` AS student2 ON project.student_id2 = student2.student_id
                                          LEFT JOIN `student` AS student3 ON project.student_id3 = student3.student_id
                                          WHERE project.project_nameTH LIKE :inputText
                                            OR project.project_nameENG LIKE :inputText
                                            OR CONCAT(student1.firstname, ' ', student1.lastname,' (นักศึกษา)') LIKE :inputText
                                            OR CONCAT(student2.firstname, ' ', student2.lastname,' (นักศึกษา)') LIKE :inputText
                                            OR CONCAT(student3.firstname, ' ', student3.lastname,' (นักศึกษา)') LIKE :inputText";


                        $Teacher1name = "SELECT project.*, 
                                           
                                            CONCAT(teacher1.position, ' ', teacher1.firstname) AS teacher1_name,
                                            CONCAT(teacher2.position, ' ', teacher2.firstname) AS teacher2_name,
                                            CONCAT(referee.position, ' ', referee.firstname) AS referee_name,
                                            CONCAT(referee1.position, ' ', referee1.firstname) AS referee1_name,
                                            CONCAT(referee2.position, ' ', referee2.firstname) AS referee2_name
                                        FROM `project`                                      
                                        LEFT JOIN `teacher` AS teacher1 ON project.teacher_id1 = teacher1.teacher_id
                                        LEFT JOIN `teacher` AS teacher2 ON project.teacher_id2 = teacher2.teacher_id
                                        LEFT JOIN `teacher` AS referee ON project.referee_id = referee.teacher_id
                                        LEFT JOIN `teacher` AS referee1 ON project.referee_id1 = referee1.teacher_id
                                        LEFT JOIN `teacher` AS referee2 ON project.referee_id2 = referee2.teacher_id
                                        WHERE CONCAT(teacher1.position, ' ', teacher1.firstname, ' ', teacher1.lastname, ' (เป็นอาจารย์ที่ปรึกษาหลัก)') LIKE :inputText
                                        ";

                        $Teacher2name = "SELECT project.*,         
                                            CONCAT(teacher1.position, ' ', teacher1.firstname) AS teacher1_name,
                                            CONCAT(teacher2.position, ' ', teacher2.firstname) AS teacher2_name,
                                            CONCAT(referee.position, ' ', referee.firstname) AS referee_name,
                                            CONCAT(referee1.position, ' ', referee1.firstname) AS referee1_name,
                                            CONCAT(referee2.position, ' ', referee2.firstname) AS referee2_name
                                        FROM `project`  
                                        LEFT JOIN `teacher` AS teacher1 ON project.teacher_id1 = teacher1.teacher_id
                                        LEFT JOIN `teacher` AS teacher2 ON project.teacher_id2 = teacher2.teacher_id
                                        LEFT JOIN `teacher` AS referee ON project.referee_id = referee.teacher_id
                                        LEFT JOIN `teacher` AS referee1 ON project.referee_id1 = referee1.teacher_id
                                        LEFT JOIN `teacher` AS referee2 ON project.referee_id2 = referee2.teacher_id
                                        WHERE CONCAT(teacher2.position, ' ', teacher2.firstname, ' ', teacher2.lastname, ' (เป็นอาจารย์ที่ปรึกษาร่วม)') LIKE :inputText
                                        ";

                        $Refereename = "SELECT project.*,                                             
                                            CONCAT(teacher1.position, ' ', teacher1.firstname) AS teacher1_name,
                                            CONCAT(teacher2.position, ' ', teacher2.firstname) AS teacher2_name,
                                            CONCAT(referee.position, ' ', referee.firstname) AS referee_name,
                                            CONCAT(referee1.position, ' ', referee1.firstname) AS referee1_name,
                                            CONCAT(referee2.position, ' ', referee2.firstname) AS referee2_name
                                        FROM `project`                  
                                        LEFT JOIN `teacher` AS teacher1 ON project.teacher_id1 = teacher1.teacher_id
                                        LEFT JOIN `teacher` AS teacher2 ON project.teacher_id2 = teacher2.teacher_id
                                        LEFT JOIN `teacher` AS referee ON project.referee_id = referee.teacher_id
                                        LEFT JOIN `teacher` AS referee1 ON project.referee_id1 = referee1.teacher_id
                                        LEFT JOIN `teacher` AS referee2 ON project.referee_id2 = referee2.teacher_id
                                        WHERE CONCAT(referee.position, ' ', referee.firstname, ' ', referee.lastname, ' (เป็นกรรมการ)') LIKE :inputText
                                             OR CONCAT(referee1.position, ' ', referee1.firstname, ' ', referee1.lastname, ' (เป็นกรรมการ)') LIKE :inputText
                                             OR CONCAT(referee2.position, ' ', referee2.firstname, ' ', referee2.lastname, ' (เป็นกรรมการ)') LIKE :inputText";

                        // check การพร้อม sql search
                        if (isset($Teacher1name) &&  $isTeacherAdvisor) {
                          $stmt = $conn->prepare($Teacher1name);
                          $inputText = '%' . $SearchText . '%';
                          $stmt->execute(['inputText' => $inputText]);
                          $projects = $stmt->fetchAll();
                        } else if (isset($Teacher2name) && $isTeacherAdvisorCoop) {
                          $stmt = $conn->prepare($Teacher2name);
                          $inputText = '%' . $SearchText . '%';
                          $stmt->execute(['inputText' => $inputText]);
                          $projects = $stmt->fetchAll();
                        } else if (isset($Refereename) && $isReferee) {
                          $stmt = $conn->prepare($Refereename);
                          $inputText = '%' . $SearchText . '%';
                          $stmt->execute(['inputText' => $inputText]);
                          $projects = $stmt->fetchAll();
                        } else if (isset($Project) || $isStudent) {
                          $stmt = $conn->prepare($Project);
                          $inputText = '%' . $SearchText . '%';
                          $stmt->execute(['inputText' => $inputText]);
                          $projects = $stmt->fetchAll();
                        }

                        $index = 1;
                        if (!$projects) {
                          echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                        } else {
                          foreach ($projects as $project) {
                            //นักศึกษา 1
                            $student1 = giveStudentById($conn, $project['student_id1']);
                            //นักศึกษา 2
                            $student2 = ($project['student_id2']) ? giveStudentById($conn, $project['student_id2']) : null;
                            //นักศึกษา 3
                            $student3 = ($project['student_id3']) ? giveStudentById($conn, $project['student_id3']) : null;
                            //อาจารย์ที่ปรึกษาหลัก
                            $teacher1 = giveTeacherById($conn, $project['teacher_id1']);
                            //อาจารย์ที่ปรึกษาร่วม
                            $teacher2 = ($project['teacher_id2']) ? giveTeacherById($conn, $project['teacher_id2']) : null;
                            //ประธานกรรมการ
                            $referee_id = giveTeacherById($conn, $project['referee_id']);
                            //กรรมการ 1
                            $referee_id1 = giveTeacherById($conn, $project['referee_id1']);
                            //กรรมการ 2
                            $referee_id2 = giveTeacherById($conn, $project['referee_id2']);
                            //กลุ่มเรียน
                            //เอกสารโปสเตอร์
                            $fullDocument1 = giveFileById($conn, $project['project_id'], 13);
                            //เอกสารรูปเล่มฉบับเต็ม
                            $fullDocument2 = giveFileById($conn, $project['project_id'], 14);
                            $group_id = ($project['group_id']) ? giveGroupById($conn, $project['group_id']) : null;
                          ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <th scope="row"><?php echo $project['project_id']; ?></th>
                              <td><?php echo $project['project_nameTH']; ?></td>
                              <td><?php echo $project['project_nameENG']; ?></td>
                              <td><?php echo $student1['firstname']; ?></td>
                              <td><?php echo $student2 ? $student2['firstname'] : ''; ?></td>
                              <td><?php echo $student3 ? $student3['firstname'] : ''; ?></td>
                              <td><?php echo giveTeacherPositionById($teacher1['position']) . $teacher1['firstname']; ?></td>
                              <td><?php if (empty($teacher2)) {
                                    echo "";
                                  } else {
                                    echo giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'];
                                  } ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id['position']) . $referee_id['firstname']; ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id1['position']) . $referee_id1['firstname']; ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id2['position']) . $referee_id2['firstname']; ?></td>
                              <td><?php echo $group_id ? $group_id['group_name'] : ''; ?></td>

                              <td><a href="<?php echo './uploadfileBoundary/' . $project['boundary_path']; ?>" target="_blank"><?php echo $project['boundary_path']; ?></a></td>
                              <td><a href="<?php echo '.././student/fileUpload/' . $fullDocument1; ?>" target="_blank"><?php echo $fullDocument1; ?></a></td>
                              <td><a href="<?php echo '.././student/fileUpload/' . $fullDocument2; ?>" target="_blank"><?php echo $fullDocument2; ?></a></td>


                              <td><?php echo $project['year']; ?></td>
                              <td><?php echo $project['term']; ?></td>
                              <td>
                                <a href="Stduploadfile.php?id=<?php echo $project['project_id']; ?>" class="btn btn-info text-white">ความคืบหน้า</a>
                                <a href="editproject.php?id=<?php echo $project['project_id']; ?>" class="btn btn-warning mt-1">แก้ไขข้อมูล</a>
                                <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $project['project_id']; ?>" class="btn btn-danger mt-1">ลบข้อมูล</a>
                              </td>
                            </tr>
                          <?php }
                        }
                      } elseif (isset($_POST['viewAll'])) {

                        $stmt = $conn->query("SELECT * FROM `project`");
                        $stmt->execute();
                        $projects = $stmt->fetchAll();
                        $index = 1;
                        if (!$projects) {
                          echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                        } else {

                          foreach ($projects as $project) {
                            //นักศึกษา 1
                            $student1 = giveStudentById($conn, $project['student_id1']);
                            //นักศึกษา 2
                            $student2 = ($project['student_id2']) ? giveStudentById($conn, $project['student_id2']) : null;
                            //นักศึกษา 3
                            $student3 = ($project['student_id3']) ? giveStudentById($conn, $project['student_id3']) : null;
                            //อาจารย์ที่ปรึกษาหลัก
                            $teacher1 = giveTeacherById($conn, $project['teacher_id1']);
                            //อาจารย์ที่ปรึกษาร่วม
                            $teacher2 = ($project['teacher_id2']) ? giveTeacherById($conn, $project['teacher_id2']) : null;
                            //ประธานกรรมการ
                            $referee_id = giveTeacherById($conn, $project['referee_id']);
                            //กรรมการ 1
                            $referee_id1 = giveTeacherById($conn, $project['referee_id1']);
                            //กรรมการ 2
                            $referee_id2 = giveTeacherById($conn, $project['referee_id2']);
                            //เอกสารโปสเตอร์
                            $fullDocument1 = giveFileById($conn, $project['project_id'], 13);
                            //เอกสารรูปเล่มฉบับเต็ม
                            $fullDocument2 = giveFileById($conn, $project['project_id'], 14);
                            //กลุ่มเรียน
                            $group_id = ($project['group_id']) ? giveGroupById($conn, $project['group_id']) : null;
                          ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <th scope="row"><?php echo $project['project_id']; ?></th>
                              <td><?php echo $project['project_nameTH']; ?></td>
                              <td><?php echo $project['project_nameENG']; ?></td>
                              <td><?php echo $student1['firstname']; ?></td>
                              <td><?php echo $student2 ? $student2['firstname'] : ''; ?></td>
                              <td><?php echo $student3 ? $student3['firstname'] : ''; ?></td>
                              <td><?php echo giveTeacherPositionById($teacher1['position']) . $teacher1['firstname']; ?></td>
                              <td><?php if (empty($teacher2)) {
                                    echo "";
                                  } else {
                                    echo giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'];
                                  } ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id['position']) . $referee_id['firstname']; ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id1['position']) . $referee_id1['firstname']; ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id2['position']) . $referee_id2['firstname']; ?></td>
                              <td><?php echo $group_id ? $group_id['group_name'] : ''; ?></td>

                              <td><a href="<?php echo './uploadfileBoundary/' . $project['boundary_path']; ?>" target="_blank"><?php echo $project['boundary_path']; ?></a></td>
                              <td><a href="<?php echo '.././student/fileUpload/' . $fullDocument1; ?>" target="_blank"><?php echo $fullDocument1; ?></a></td>
                              <td><a href="<?php echo '.././student/fileUpload/' . $fullDocument2; ?>" target="_blank"><?php echo $fullDocument2; ?></a></td>


                              <td><?php echo $project['year']; ?></td>
                              <td><?php echo $project['term']; ?></td>
                              <td>
                                <a href="Stduploadfile.php?id=<?php echo $project['project_id']; ?>" class="btn btn-info text-white">ความคืบหน้า</a>
                                <a href="editproject.php?id=<?php echo $project['project_id']; ?>" class="btn btn-warning mt-1">แก้ไขข้อมูล</a>
                                <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $project['project_id']; ?>" class="btn btn-danger mt-1">ลบข้อมูล</a>
                              </td>
                            </tr>
                          <?php }
                        }
                      } else {
                        $stmt = $conn->query("SELECT * FROM `project`");
                        $stmt->execute();
                        $projects = $stmt->fetchAll();
                        $index = 1;
                        if (!$projects) {
                          echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                        } else {
                          foreach ($projects as $project) {
                            //นักศึกษา 1
                            $student1 = giveStudentById($conn, $project['student_id1']);
                            //นักศึกษา 2
                            $student2 = ($project['student_id2']) ? giveStudentById($conn, $project['student_id2']) : null;
                            //นักศึกษา 3
                            $student3 = ($project['student_id3']) ? giveStudentById($conn, $project['student_id3']) : null;
                            //อาจารย์ที่ปรึกษาหลัก
                            $teacher1 = ($project['teacher_id1']) ? giveTeacherById($conn, $project['teacher_id1']) : null;
                            //อาจารย์ที่ปรึกษาร่วม
                            $teacher2 = ($project['teacher_id2']) ? giveTeacherById($conn, $project['teacher_id2']) : null;
                            //ประธานกรรมการ
                            $referee_id = giveTeacherById($conn, $project['referee_id']);
                            //กรรมการ 1
                            $referee_id1 = giveTeacherById($conn, $project['referee_id1']);
                            //กรรมการ 2
                            $referee_id2 = giveTeacherById($conn, $project['referee_id2']);
                            //เอกสารโปสเตอร์
                            $fullDocument1 = giveFileById($conn, $project['project_id'], 13);
                            //เอกสารรูปเล่มฉบับเต็ม
                            $fullDocument2 = giveFileById($conn, $project['project_id'], 14);

                            //กลุ่มเรียน
                            $group_id = ($project['group_id']) ? giveGroupById($conn, $project['group_id']) : null;
                          ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <th scope="row"><?php echo $project['project_id']; ?></th>
                              <td><?php echo $project['project_nameTH']; ?></td>
                              <td><?php echo $project['project_nameENG']; ?></td>
                              <td><?php echo $student1 ? $student1['firstname'] : ''; ?></td>
                              <td><?php echo $student2 ? $student2['firstname'] : ''; ?></td>
                              <td><?php echo $student3 ? $student3['firstname'] : ''; ?></td>
                              <td><?php echo giveTeacherPositionById($teacher1['position']) . $teacher1['firstname']; ?></td>
                              <td><?php if (empty($teacher2)) {
                                    echo "";
                                  } else {
                                    echo giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'];
                                  } ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id['position']) . $referee_id['firstname']; ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id1['position']) . $referee_id1['firstname']; ?></td>
                              <td><?php echo giveTeacherPositionById($referee_id2['position']) . $referee_id2['firstname']; ?></td>
                              <td><?php echo $group_id ? $group_id['group_name'] : ''; ?></td>

                              <td><a href="<?php echo './uploadfileBoundary/' . $project['boundary_path']; ?>" target="_blank"><?php echo $project['boundary_path']; ?></a></td>
                              <td><a href="<?php echo '.././student/fileUpload/' . $fullDocument1; ?>" target="_blank"><?php echo $fullDocument1; ?></a></td>
                              <td><a href="<?php echo '.././student/fileUpload/' . $fullDocument2; ?>" target="_blank"><?php echo $fullDocument2; ?></a></td>


                              <td><?php echo $project['year']; ?></td>
                              <td><?php echo $project['term']; ?></td>
                              <td>
                                <a href="Stduploadfile.php?id=<?php echo $project['project_id']; ?>" class="btn btn-info text-white">ความคืบหน้า</a>
                                <a href="editproject.php?id=<?php echo $project['project_id']; ?>" class="btn btn-warning mt-1">แก้ไขข้อมูล</a>
                                <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $project['project_id']; ?>" class="btn btn-danger mt-1">ลบข้อมูล</a>
                              </td>
                            </tr>
                      <?php }
                        }
                      }
                      ?>
                    </tbody>
                    </thead>
                  </table>
                </div>
                <form action="./projectmanage.php" method="POST">
                  <div class="d-grid gap-2">
                    <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" class="btn btn-secondary">View All</button>
                  </div>
                </form>
                <a onclick="return confirm('Are you sure you want to delete all file progress?');" href="deleteFileAllAll_Stdupload.php?projects=<?php echo urlencode(json_encode($projects)); ?>" class="btn btn-danger mt-2">ลบเอกสารความคืบหน้าทั้งหมดที่ฟิลเตอร์</a>
              </div>
            </div>
          </div>
        </div>
      </main>

    </div>
  </div>

  <!-- Link to jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <script src="./search_data/searchProjectAdmin.js"></script>

  <script>
    // animetion uploading    
    function showLoading(formId) {
      // แสดง Popup Loading เฉพาะฟอร์มที่ถูกส่ง
      document.getElementById(formId + "-loadingOverlay").style.display = "block";
      return true; // ต้อง return true เพื่อให้ฟอร์มส่งข้อมูลไปยัง action
    }

    function hideLoading(formId) {
      // ซ่อน Popup Loading เมื่ออัปโหลดสำเร็จ
      document.getElementById(formId + "-loadingOverlay").style.display = "none";
    }
  </script>

</body>

</html>