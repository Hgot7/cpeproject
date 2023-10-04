<?php
session_start();
require_once "./connect.php";

if (!isset($_SESSION['teacher_login']) && !isset($_SESSION['student_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
}

if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];

  // ดึงข้อมูลไฟล์ที่ต้องการลบ
  $filestmt = $conn->prepare("SELECT boundary_path FROM `project` WHERE project_id = :delete_id");
  $filestmt->bindParam(':delete_id', $delete_id);
  $filestmt->execute();
  $fileData = $filestmt->fetch(PDO::FETCH_ASSOC);
  $boundary_path = $fileData['boundary_path'];

  if ($boundary_path && $boundary_path !== false) {
    $fileToDelete = "uploadfileBoundary/" . $boundary_path;
    if (is_file($fileToDelete)) {
      if (unlink($fileToDelete)) {
        // $_SESSION['success'] = "ลบไฟล์เสร็จสมบูรณ์";
        header("location: ./projectSearchAll.php");
      } else {
        $_SESSION['success'] = "เกิดข้อผิดพลาดในการลบไฟล์";
        header("location: ./projectSearchAll.php");
      }
    } else {
      $_SESSION['success'] = "ไม่พบไฟล์ที่ต้องการลบ";
      header("location: ./projectSearchAll.php");
    }
  }

  // ลบข้อมูลจากฐานข้อมูล
  $deletestmt = $conn->prepare("DELETE FROM `project` WHERE project_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);
  if ($deletestmt->execute()) {
    $_SESSION['success'] = "ลบข้อมูลเสร็จสมบูรณ์";
    header("location: ./projectSearchAll.php");
  } else {
    $_SESSION['success'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    header("location: ./projectSearchAll.php");
  }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Link to custom Bootstrap CSS -->
  <link rel="stylesheet" href="./css/custom.css">
  <script src="./component.js"></script>
  <!-- Link to Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

  <!-- Link to icon -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">

  <title>ค้นหาข้อมูลโครงงาน</title>

</head>

<body>

  <!-- -------------------------------------------------Header------------------------------------------------- -->
  <div class="HeaderBg shadow">
    <div class="container">
      <?php if (isset($_SESSION['student_login'])) { ?>
        <navbar_std-component></navbar_std-component> <!-- component.js Navber-->
      <?php } elseif (isset($_SESSION['teacher_login'])) { ?>
        <navbar_teacher-component></navbar_teacher-component>
      <?php } ?>
    </div>
  </div>
  <div class="container-fluid justify-content-around">
    <div class="row">
      <?php if (isset($_SESSION['student_login'])) { ?>
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse shadow-lg ">
  <div class="position-sticky">
    <ul class="nav flex-column">

      <li class="nav-item pb-2">
        <a href="#" class="nav-link text-primary disabled" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-person-workspace h3"></i>
            </div>
            <span class="ps-2 fs-5 fw-bold"><?php echo $_SESSION['student_login']; ?></span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <hr class="sidebar-divider my-0">
      </li>

      <li class="nav-item pb-2">
        <a href="./student/Stdpage.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-house-door h4"></i>
            </div>
            <span class="ps-2">หน้าหลัก</span></span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="./student/profileStd.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-person-lines-fill h4"></i>
            </div>
            <span class="ps-2">โปรไฟล์ของนักศึกษา</span></span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="./student/Stduploadfile.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-file-earmark-arrow-up h4"></i>
            </div>
            <span class="ps-2">อัปโหลดไฟล์</span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="./student/DownloadDocument.php" id="upload" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-file-earmark-arrow-down h4"></i>
            </div>
            <span class="ps-2">ดาวน์โหลดไฟล์เอกสาร</span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="./student/statusproject.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-card-checklist h4"></i>
            </div>
            <span class="ps-2">สถานะโครงงาน</span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="../projectSearchAll.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-search h4"></i>
            </div>
            <span class="ps-2">ค้นหาข้อมูลโครงงาน</span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="./logout_Std.php" id="logout" class="nav-link active">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-box-arrow-right h4"></i>
            </div>
            <span class="ps-2">ออกจากระบบ</span>
          </div>
        </a>
      </li>
    </ul>
  </div>

</nav>
      <?php } elseif (isset($_SESSION['teacher_login'])) { ?>

        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse shadow-lg ">
    <div class="position-sticky">
      <ul class="nav flex-column">
        <a href="#" class="nav-link text-primary disabled" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-person-workspace h3"></i>
              </div>
              <span class="ps-2 fs-5 fw-bold"><?php echo $_SESSION['teacher_login'];?></span>
            </div>
          </a>
         
        </li>
       
        <li class="nav-item pb-2">
        <hr class="sidebar-divider my-0">
    </li>

        <li class="nav-item pb-2">
          <a href="./teacher/Teacherpage.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-house-door h4"></i>
              </div>
              <span class="ps-2">หน้าหลัก</span>
            </div>
          </a>
        </li>

        <li class="nav-item pb-2">
        <a href="./teacher/profileTeacher.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-person-lines-fill h4"></i>
            </div>
            <span class="ps-2">โปรไฟล์ของอาจารย์</span></span>
          </div>
        </a>
      </li>

        
        <li class="nav-item pb-2">
        <a href="./teacher/DownloadDocument.php" id="upload" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-file-earmark-text h4"></i>
            </div>
            <span class="ps-2">เอกสารในรายวิชา</span>
          </div>
        </a>
      </li>

        <li class="nav-item pb-2">
          <a href="./teacher/Teacheryourproject.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-clipboard h4"></i>
              </div>
              <span class="ps-2">โครงงานที่รับเป็นที่ปรึกษา</span>
            </div>
          </a>
        </li>

        <li class="nav-item pb-2">
          <a href="./teacher/viewpointTest.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-clipboard2-check h4"></i>
              </div>
              <span class="ps-2">ประเมินโครงงาน</span>
            </div>
          </a>
        </li>

        <li class="nav-item pb-2">
        <a href="../projectSearchAll.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-search h4"></i>
            </div>
            <span class="ps-2">ค้นหาข้อมูลโครงงาน</span>
          </div>
        </a>
      </li>

        <li class="nav-item pb-2">
          <a href="./logout_Teacher.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-box-arrow-right h4"></i>
              </div>
              <span class="ps-2">ออกจากระบบ</span>
            </div>
          </a>
        </li>
      </ul>
    </div>

  </nav>
      <?php } ?>
      <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">
      <div class="row">
      
          <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลโครงงานในรายวิชา</h1>
          <?php if (isset($_SESSION['student_login'])) { ?>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb fs-5 mt-2 ms-3">
                <li class="breadcrumb-item"><a href="./student/Stdpage.php">หน้าหลัก</a></li>
                <li class="breadcrumb-item active" aria-current="page">ค้นหาข้อมูลโครงงาน</li>
              </ol>
            </nav>
          <?php } elseif (isset($_SESSION['teacher_login'])) { ?>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb fs-5 mt-2 ms-3">
                <li class="breadcrumb-item"><a href="./teacher/Teacherpage.php">หน้าหลัก</a></li>
                <li class="breadcrumb-item active" aria-current="page">ค้นหาข้อมูลโครงงาน</li>
              </ol>
            </nav>

          <?php } ?>
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
          <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
            <div class="card shadow-sm">
              <div class="card-header justify-content-between align-items-center">
                <form action="./projectSearchAll.php" method="POST">
                  <div class="row g-3 mb-2">

                    <div class="col-md-2">
                      <label for="filterYear" class="form-label">ฟิลเตอร์ปีการศึกษา</label>
                      <select class="form-select" name="filteryear">
                        <?php
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
                        ?>
                        <option value="">เลือกภาคการศึกษา</option>
                        <?php
                        while ($dataterm = $terms->fetch(PDO::FETCH_ASSOC)) { ?>
                          <option value="<?php echo $dataterm['term']; ?>">
                            <?php echo $dataterm['term']; ?>
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
                        ?>
                        <option value="">เลือกกลุ่มเรียน</option>
                        <?php
                        while ($datagroup = $groups->fetch(PDO::FETCH_ASSOC)) { ?>
                          <option value="<?php echo $datagroup['group_name']; ?>">
                            <?php echo $datagroup['group_name']; ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>


                    <div class="col-auto d-flex align-items-end justify-content-start">
                      <button type="submit" id="submitfilter" name="submitfilter" class="btn btn-success">ฟิลเตอร์</button>
                    </div>


                  </div>
                </form>
                <div class="row pb-2">
                  <div class="col">
                    <form action="./projectSearchAll.php" method="POST" class="d-flex">
                      <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาชื่อโครงงาน,ชื่อนักศึกษา,ชื่ออาจารย์ที่ปรึกษา,ชื่อกรรมการ" autocomplete="off" required>
                      <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                    </form>
                  </div>
                  <div class="col-auto">
                  </div>
                </div>
              </div>

              <div class="card-body">

                <div class="col-md-5">
                  <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
                </div>

                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                      <th scope="col" style="width: 5em;">ลำดับที่</th>
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
                        <th scope="col">เทอม</th>

                      </tr>
                    <tbody>
                      <?php
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
                          case "ดร.":
                            return $Position = "ดร.";
                            break;
                          default:
                            return $Position = $Position;
                        }
                      }
                      function giveFileById($conn, $project_id, $file_chapter)
                      {
                        $sql = "SELECT file_path FROM file WHERE (project_id = :project_id and (file_chapter = :file_chapter and file_status = 1))";
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
                            <!-- <td><a href="<?php echo './uploadfileDocument/' . $document['document_path']; ?>" target="_blank"><?php echo $document['document_path']; ?></a></td> -->
                            <td><a href="<?php echo './student/fileUpload/' . $fullDocument1; ?>" target="_blank"><?php echo $fullDocument1; ?></a></td>
                            <td><a href="<?php echo './student/fileUpload/' . $fullDocument2; ?>" target="_blank"><?php echo $fullDocument2; ?></a></td>

                            <td><?php echo $project['year']; ?></td>
                            <td><?php echo $project['term']; ?></td>

                          </tr>
                        <?php }
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
                                            OR CONCAT(student1.firstname,' (นักศึกษา)') LIKE :inputText
                                            OR CONCAT(student2.firstname,' (นักศึกษา)') LIKE :inputText
                                            OR CONCAT(student3.firstname,' (นักศึกษา)') LIKE :inputText";


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
                                        WHERE CONCAT(teacher1.position, ' ', teacher1.firstname, ' (เป็นอาจารย์ที่ปรึกษาหลัก)') LIKE :inputText
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
                                        WHERE CONCAT(teacher2.position, ' ', teacher2.firstname, ' (เป็นอาจารย์ที่ปรึกษาร่วม)') LIKE :inputText
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
                                        WHERE CONCAT(referee.position, ' ', referee.firstname, ' (เป็นกรรมการ)') LIKE :inputText
                                             OR CONCAT(referee1.position, ' ', referee1.firstname, ' (เป็นกรรมการ)') LIKE :inputText
                                             OR CONCAT(referee2.position, ' ', referee2.firstname, ' (เป็นกรรมการ)') LIKE :inputText";

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
                            <!-- <td><a href="<?php echo './uploadfileDocument/' . $document['document_path']; ?>" target="_blank"><?php echo $document['document_path']; ?></a></td> -->
                            <td><a href="<?php echo './student/fileUpload/' . $fullDocument1; ?>" target="_blank"><?php echo $fullDocument1; ?></a></td>
                            <td><a href="<?php echo './student/fileUpload/' . $fullDocument2; ?>" target="_blank"><?php echo $fullDocument2; ?></a></td>

                            <td><?php echo $project['year']; ?></td>
                            <td><?php echo $project['term']; ?></td>

                          </tr>
                          <?php }
                        }
                      } elseif (isset($_POST['viewAll'])) {

                        $stmt = $conn->query("SELECT * FROM `project`");
                        $stmt->execute();
                        $projects = $stmt->fetchAll();
                        $index = 1;
                        if (!$projects) {
                          echo "<p><td colspan='10' class='text-center'>No data available</td></p>";
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
                              <!-- <td><a href="<?php echo './uploadfileDocument/' . $document['document_path']; ?>" target="_blank"><?php echo $document['document_path']; ?></a></td> -->
                              <td><a href="<?php echo './student/fileUpload/' . $fullDocument1; ?>" target="_blank"><?php echo $fullDocument1; ?></a></td>
                              <td><a href="<?php echo './student/fileUpload/' . $fullDocument2; ?>" target="_blank"><?php echo $fullDocument2; ?></a></td>

                              <td><?php echo $project['year']; ?></td>
                              <td><?php echo $project['term']; ?></td>

                            </tr>
                          <?php }
                        }
                      } else {
                        $stmt = $conn->query("SELECT * FROM `project`");
                        $stmt->execute();
                        $projects = $stmt->fetchAll();
                        $index = 1;
                        if (!$projects) {
                          echo "<p><td colspan='10' class='text-center'>No data available</td></p>";
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
                              <!-- <td><a href="<?php echo './uploadfileDocument/' . $document['document_path']; ?>" target="_blank"><?php echo $document['document_path']; ?></a></td> -->
                              <td><a href="<?php echo './student/fileUpload/' . $fullDocument1; ?>" target="_blank"><?php echo $fullDocument1; ?></a></td>
                              <td><a href="<?php echo './student/fileUpload/' . $fullDocument2; ?>" target="_blank"><?php echo $fullDocument2; ?></a></td>

                              <td><?php echo $project['year']; ?></td>
                              <td><?php echo $project['term']; ?></td>

                            </tr>
                      <?php }
                        }
                      }
                      ?>
                    </tbody>
                    </thead>
                  </table>
                </div>
                <form action="./projectSearchAll.php" method="POST">
                  <div class="d-grid gap-2">
                    <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" class="btn btn-secondary">View All</button>
                  </div>
                </form>


              </div>
            </div>
          </div>
      </div>
      </main>

    </div>
  </div>

  <!-- Link to jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <script src="./searchProjectSearchAll.js"></script>


</body>

</html>