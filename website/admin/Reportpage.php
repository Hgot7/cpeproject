<?php

session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
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

  <title>หน้ารายงานสรุป</title>

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

        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">รายงานสรุป</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">รายงานสรุป</li>
          </ol>
        </nav>

        <div class="row my-4">
          <div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="card border-left-info shadow">
              <h4 class="card-header text-info">อาจารย์ที่ยังไม่ประเมินการสอบโครงงาน</h4>
              <div class="card-body ps-3 mb-1 mt-1">
                <h5 class="card-title">รายงานสรุปอาจารย์ที่ไม่ประเมินผลการสอบแต่ละภาคการศึกษา</h5>

                <a href="./ResultTeacherAssessment.php" class="card-text text-info">View All</a>
              </div>
            </div>
          </div>



          <div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="card border-left-primary shadow">
              <h4 class="card-header text-primary">นักศึกษาที่ลงทะเบียนเรียน</h4>
              <div class="card-body ps-3 mb-1 mt-1">
                <h5 class="card-title">รายงานสรุปนักศึกษาที่ลงทะเบียนเรียนในแต่ละภาคการศึกษา </h5>
                <a href="./ResultStudent.php" class="card-text text-primary">View All</a>
              </div>
            </div>
          </div>



          <div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="card border-left-success shadow">
              <h4 class="card-header text-success">จำนวนโครงงานของอาจารย์แต่ละคน</h4>
              <div class="card-body ps-3 mb-1 mt-1">
                <h5 class="card-title">รายงานสรุปจำนวนหัวข้อโครงงานที่อาจารย์แต่ละคนรับเป็นที่ปรึกษาในแต่ละภาคการศึกษา</h5>

                <a href="./ResultTeacherproject.php" class="card-text text-success">View All</a>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="card border-left-danger shadow">
              <h4 class="card-header text-danger">เกรดนักศึกษาแต่ละภาคการศึกษา</h4>
              <div class="card-body ps-3 mb-1 mt-1">
                <h5 class="card-title">รายงานสรุปเกรดวิชาโครงงานของนักศึกษาแต่ละภาคการศึกษา</h5>
                <a href="./ResultGrade.php" class="card-text text-danger">View All</a>
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-12 col-xl-12 mb-4 mb-lg-0">
            <div class="card shadow-sm">
              <h4 class="card-header font-weight-bold text-primary">สถานะโครงงานที่กำลังดำเนินการในปัจจุบัน</h4>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="text-center" scope="col" style="width : 10%;">ลำดับที่</th>
                        <th class="text-center" scope="col" style="width : 30%;">ชื่อโครงงาน</th>
                        <th class="text-center" scope="col">อาจารย์ที่ปรึกษาหลัก</th>
                        <th class="text-center" scope="col">อาจารย์ที่ปรึกษาร่วม</th>
                        <th class="text-center" scope="col">สถานะ</th>
                        <th class="text-center" scope="col">ปีการศึกษา</th>
                        <th class="text-center" scope="col">ภาคการศึกษา</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      function giveProjectById($conn, $project_id)
                      {
                        $sql = "SELECT * FROM `project` WHERE project_id = :project_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':project_id', $project_id);
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
                      $sql = "SELECT * FROM `project` WHERE year = (SELECT year FROM `defaultsystem` WHERE default_system_id = 1 ) 
                    AND term = (SELECT term FROM `defaultsystem` WHERE default_system_id = 1 ) ORDER BY year DESC, term DESC";
                      $stmt = $conn->prepare($sql);
                      $stmt->execute();
                      $datas = $stmt->fetchAll();
                      $index = 1;
                      foreach ($datas as $project) {
                        //อาจารย์ที่ปรึกษาหลัก
                        $teacher1 = ($project['teacher_id1']) ? giveTeacherById($conn, $project['teacher_id1']) : null;
                        //อาจารย์ที่ปรึกษาร่วม
                        $teacher2 = ($project['teacher_id2']) ? giveTeacherById($conn, $project['teacher_id2']) : null;
                      ?>
                        <tr>
                          <th scope="row"><?php echo $index++; ?></th>
                          <td><?php echo $project['project_nameTH']; ?></td>
                          <td><?php echo giveTeacherPositionById($teacher1['position']) . $teacher1['firstname']; ?></td>
                          <td><?php if (empty($teacher2)) {
                                echo "";
                              } else {
                                echo giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'];
                              } ?></td>
                          <?php
                          $sql = "SELECT count(distinct file_chapter) FROM `file` WHERE project_id = :project_id";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':project_id', $project['project_id']);
                          $stmt->execute();
                          $chapter = $stmt->fetchColumn(); // Use fetchColumn to get the count directly

                          ?>
                          <td><?php echo 'ส่งเอกสารความคืบหน้า ' . $chapter . ' จาก 14'; ?></td>
                          <td><?php echo ($project['year']); ?></td>
                          <td><?php echo ($project['term']); ?></td>
                          <td>
                        </tr>
                      <?php
                      } ?>
                    </tbody>

                  </table>
                </div>
                <a href="./ResultProjectprogress.php" class="btn btn-block btn-secondary d-grid gap-2">View All</a>
              </div>
            </div>
          </div>

        </div>
      </main>

    </div>
  </div>

</body>

</html>