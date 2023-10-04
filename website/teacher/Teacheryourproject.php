<?php

session_start();
require_once "../connect.php";

if (!isset($_SESSION['teacher_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
}

function giveTeacherById($conn, $project_id)
{
  $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_status = :file_status";
  $stmt = $conn->prepare($sql);
  $file_status = 1; // แก้ไขค่าตามที่ต้องการ
  $stmt->bindParam(':project_id', $project_id);
  $stmt->bindParam(':file_status', $file_status, PDO::PARAM_INT);
  $stmt->execute();
  $rowCount = $stmt->rowCount();
  
  $sql = "SELECT grade FROM `student` WHERE student_id = (SELECT student_id1 FROM `project` WHERE project_id = :project_id)";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':project_id', $project_id);
  $stmt->execute();
  $studentGrade = $stmt->fetchColumn();


  // ดึงจำนวนแถวทั้งหมดที่ผลลัพธ์จากคำสั่ง SQL
  
  $rowCountNum = 100 / 14;
  if(isset($studentGrade)){return 100;}else{return intval($rowCount * $rowCountNum);}
  
}



?>
<!DOCTYPE html>
<html lang="en">

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


  <title>โครงงานที่รับเป็นที่ปรึกษา</title>

</head>

<body>
  <!-- -------------------------------------------------Header------------------------------------------------- -->
  <div class="HeaderBg shadow">
    <div class="container">
      <navbar_teacher-component></navbar_teacher-component>
    </div>
  </div>

  <!-- -------------------------------------------------Material------------------------------------------------- -->
  <div class="container-fluid justify-content-around">
    <div class="row">
      <?php include("sidebarTeacherComponent.php"); ?>

      <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">

        <h1 class="fs-2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลรายชื่อโครงงานที่รับเป็นที่ปรึกษา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
            <li class="breadcrumb-item"><a href="./Teacherpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">โครงงานที่รับเป็นที่ปรึกษา</li>
          </ol>
        </nav>
        <div class="col me-3 mb-4 mb-lg-0">
          <div class="card shadow-sm">
            <div class="card-header justify-content-between align-items-center">
              <form action="./Teacheryourproject.php" method="POST">
                <div class="row g-3 mb-2">

                  <div class="col-md-2">
                    <label for="filterYear" class="form-label">ฟิลเตอร์ปีการศึกษา</label>
                    <select class="form-select" name="filteryear">
                      <?php
                      $years = $conn->query("SELECT DISTINCT year FROM `project` ORDER BY year DESC");
                      $years->execute();
                      ?>
                      <option value="">เลือกปีการศึกษา</option>
                      <?php
                      while ($datayear = $years->fetch(PDO::FETCH_ASSOC)) { ?>
                        <option value="<?php echo $datayear['year']; ?>">
                          <?php echo $datayear['year']; ?>
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

                  <div class="col-auto d-flex align-items-end justify-content-start">
                    <button type="submit" id="submitfilter" name="submitfilter" class="btn btn-success">ฟิลเตอร์</button>
                  </div>

                </div>
              </form>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 5em;">ลำดับที่</th>
                      <th class="text-center" scope="col">รหัสกลุ่มโครงงาน</th>
                      <th scope="col" style="width : 30%;">ชื่อโครงงาน</th>
                      <th class="text-center" scope="col">ปีการศึกษา</th>
                      <th class="text-center" scope="col">เทอมการศึกษา</th>
                      <th class="text-center" scope="col">ความคืบหน้า</th>
                      <th class="text-center" scope="col" style="width: 15%;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    try {
                      if (isset($_POST['submitfilter'])) {
                        $selectedYear = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                        $selectedTerm = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;

                        if (empty($selectedYear) && empty($selectedTerm)) {
                          $sql = "SELECT * FROM `project` WHERE teacher_id1 = :id or teacher_id2 = :id
                        ORDER BY year DESC, term DESC";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':id', $_SESSION['teacher_id']);
                          $stmt->execute();
                          $filteredData = $stmt->fetchAll();
                        } elseif (!empty($selectedYear) && !empty($selectedTerm)) {
                          // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                          $sql = "SELECT * FROM `project` WHERE (teacher_id1 = :id OR teacher_id2 = :id)
                                  AND( term = :term AND year = :year )
                                  ORDER BY year DESC, term DESC";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':id', $_SESSION['teacher_id']);
                          $stmt->bindParam(':term', $selectedTerm);
                          $stmt->bindParam(':year', $selectedYear);
                          $stmt->execute();
                          $filteredData = $stmt->fetchAll();
                        } elseif (!empty($selectedYear) && empty($selectedTerm)) {
                          // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ

                          $sql = "SELECT * FROM `project` WHERE (teacher_id1 = :id OR teacher_id2 = :id)
                           AND( year = :year )
                           ORDER BY year DESC, term DESC";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':year', $selectedYear);
                          $stmt->bindParam(':id', $_SESSION['teacher_id']);
                          $stmt->execute();
                          $filteredData = $stmt->fetchAll();
                        } elseif (empty($selectedYear) && !empty($selectedTerm)) {
                          // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ

                          $sql = "SELECT * FROM `project` WHERE (teacher_id1 = :id OR teacher_id2 = :id)
                          AND( term = :term ) 
                          ORDER BY year DESC, term DESC";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(':id', $_SESSION['teacher_id']);
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
                        if (empty($filteredData)) {
                          echo "<tr><td colspan='20' class='text-center'>No data available</td></tr>";
                        } else {
                        $index = 1;
                        foreach ($filteredData as $data) {
                    ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <td><?php echo $data['project_id']; ?></td>
                            <td><?php echo ($data['project_nameTH']); ?></td>
                            <td><?php echo ($data['year']); ?></td>
                            <td><?php echo ($data['term']); ?></td>
                            <td>
                              <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="<?php echo giveTeacherById($conn, $data['project_id']) ?>%" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: <?php echo giveTeacherById($conn, $data['project_id']) ?>%"> <?php echo giveTeacherById($conn, $data['project_id']) ?>% </div>
                              </div>
                            </td>
                            <td><a href="STDUploadfile.php?id=<?php echo $data['project_id']; ?>" class="btn btn-info text-white mb-1">ความคืบหน้า</a></td>
                          </tr>
                          <?php }
                        }
                      }elseif (isset($_POST['viewAll'])) {
                        $sql = "SELECT * FROM `project` WHERE teacher_id1 = :id or teacher_id2 = :id
                        ORDER BY year DESC, term DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':id', $_SESSION['teacher_id']);
                        $stmt->execute();
                        $datas = $stmt->fetchAll();

                        if (empty($datas)) {
                          echo "<tr><td colspan='20' class='text-center'>No data available</td></tr>";
                        } else {
                          $index = 1;
                          foreach ($datas as $data) {
                          ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <td><?php echo $data['project_id']; ?></td>
                              <td><?php echo ($data['project_nameTH']); ?></td>
                              <td><?php echo ($data['year']); ?></td>
                              <td><?php echo ($data['term']); ?></td>
                              <td>
                                <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="<?php echo giveTeacherById($conn, $data['project_id']) ?>%" aria-valuemin="0" aria-valuemax="100">
                                  <div class="progress-bar" style="width: <?php echo giveTeacherById($conn, $data['project_id']) ?>%"> <?php echo giveTeacherById($conn, $data['project_id']) ?>% </div>
                                </div>
                              </td>
                              <td><a href="STDUploadfile.php?id=<?php echo $data['project_id']; ?>" class="btn btn-info text-white mb-1">ความคืบหน้า</a>
                                <a href="statusproject.php?id=<?php echo $data['project_id']; ?>" class="btn btn-info text-white mb-1">สถานะโครงงาน</a>
                              </td>
                            </tr>
                    <?php
                          }
                        }

                      }else {
                        $sql = "SELECT * FROM `project` WHERE teacher_id1 = :id or teacher_id2 = :id
                        ORDER BY year DESC, term DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':id', $_SESSION['teacher_id']);
                        $stmt->execute();
                        $datas = $stmt->fetchAll();

                        if (empty($datas)) {
                          echo "<tr><td colspan='20' class='text-center'>No data available</td></tr>";
                        } else {
                          $index = 1;
                          foreach ($datas as $data) {
                          ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <td><?php echo $data['project_id']; ?></td>
                              <td><?php echo ($data['project_nameTH']); ?></td>
                              <td><?php echo ($data['year']); ?></td>
                              <td><?php echo ($data['term']); ?></td>
                              <td>
                                <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="<?php echo giveTeacherById($conn, $data['project_id']) ?>%" aria-valuemin="0" aria-valuemax="100">
                                  <div class="progress-bar" style="width: <?php echo giveTeacherById($conn, $data['project_id']) ?>%"> <?php echo giveTeacherById($conn, $data['project_id']) ?>% </div>
                                </div>
                              </td>
                              <td><a href="STDUploadfile.php?id=<?php echo $data['project_id']; ?>" class="btn btn-info text-white mb-1">ความคืบหน้า</a>
                                <a href="statusproject.php?id=<?php echo $data['project_id']; ?>" class="btn btn-info text-white mb-1">สถานะโครงงาน</a>
                              </td>
                            </tr>
                    <?php
                          }
                        }
                      }
                    } catch (PDOException $e) {
                      echo "Error: " . $e->getMessage();
                    }
                    ?>
                  </tbody>
                </table>
              </div>
              <form action="./Teacheryourproject.php" method="POST">
                <div class="d-grid gap-2">
                  <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" class="btn btn-secondary">View All</button>
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