<?php

session_start();
require_once "../connect.php";


if (!isset($_SESSION['teacher_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
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


  <title>หน้าหลักอาจารย์</title>

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



        <div class="row mb-4">
          <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">หน้าหลัก</h1>
          <p>This is homepage of teacher interface</p>
          <div class="col-12 me-3 mb-4 mb-lg-0">
            <div class="card shadow-sm">
              <h5 class="card-header">กฎข้อบังคับของรายวิชา</h5>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col" style="width: 5em;">ลำดับที่</th>
                        <th scope="col">กฎข้อบังคับ</th>
                        <th scope="col">เนื้อหากฎข้อบังคับเพิ่มเติม</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      try {
                        $sql = "SELECT * FROM `regulation`
                        WHERE year = (SELECT year FROM `defaultsystem` WHERE default_system_id = :id) 
                        AND term = (SELECT term FROM `defaultsystem` WHERE default_system_id = :id)
                        ORDER BY regulation_text ";

                        $stmt = $conn->prepare($sql);
                        $defaultSystemId = 1;
                        $stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
                        $stmt->execute();
                        $datas = $stmt->fetchAll();

                        if (empty($datas)) {
                          echo "<tr><td colspan='6' class='text-center'>No data available</td></tr>";
                        } else {
                          $index = 1;
                          foreach ($datas as $data) {
                      ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <td><a href="<?php echo '.././admin/uploadfileRegulation/' . $data['regulationFile_path']; ?>" target="_blank"><?php echo $data['regulationFile_path']; ?></a></td>
                              <td><?php echo ($data['regulation_text']); ?></td>
                            </tr>
                      <?php
                          }
                        }
                      } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                      }
                      ?>
                    </tbody>


                  </table>
                </div>

              </div>
            </div>
          </div>
        </div>


        <div class="row mb-4">
          <div class="col-12 me-3 mb-4 mb-lg-0">
            <div class="card shadow-sm">
              <h5 class="card-header">ข่าวสารของวิชา</h5>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col" style="width: 5em;">ลำดับที่</th>
                        <th scope="col">วันเวลาที่ลงข่าว</th>
                        <th scope="col">หัวข้อข่าว</th>
                        <th scope="col">เนื้อหาข่าว</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      try {
                        $sql = "SELECT * FROM `news` WHERE year = (SELECT year FROM `defaultsystem` WHERE default_system_id = :id) and term = (SELECT term FROM `defaultsystem` WHERE default_system_id = :id)
                        ORDER BY news_date DESC";
                        $stmt = $conn->prepare($sql);
                        $defaultSystemId = 1;
                        $stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
                        $stmt->execute();
                        $datas = $stmt->fetchAll();

                        if (empty($datas)) {
                          echo "<tr><td colspan='6' class='text-center'>No data available</td></tr>";
                        } else {
                          $index = 1;
                          foreach ($datas as $data) {
                      ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <td><?php echo $data['news_date']; ?></td>
                              <td><?php echo ($data['news_head']); ?></td>
                              <td><?php echo ($data['news_text']); ?></td>
                            </tr>
                      <?php
                          }
                        }
                      } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                      }
                      ?>
                    </tbody>


                  </table>
                </div>
                <!-- <a href="#" class="btn btn-block btn-light d-grid gap-2">View All</a> -->
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-12 me-3 mb-4 mb-lg-0">
            <div class="card shadow-sm">
              <h5 class="card-header">เวลาสอบโปรเจค</h5>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col" style="width: 5em;">ลำดับที่</th>
                        <th scope="col">รหัสโปรเจคที่สอบ</th>
                        <th scope="col">ชื่อโปรเจคที่สอบ</th>
                        <th scope="col">นักศึกษา 1</th>
                        <th scope="col">นักศึกษา 2</th>
                        <th scope="col">นักศึกษา 3</th>
                        <th scope="col">อาจารย์ที่ปรึกษาหลัก</th>
                        <th scope="col">อาจารย์ที่ปรึกษาร่วม</th>
                        <th scope="col">ประธานกรรมการ</th>
                        <th scope="col">กรรมการ 1</th>
                        <th scope="col">กรรมการ 2</th>
                        <th scope="col">วันที่สอบ</th>
                        <th scope="col">เริ่มสอบ</th>
                        <th scope="col">หมดเวลาสอบ</th>
                        <th scope="col">ห้องสอบ</th>
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

                      function giveGroupById($conn, $group_id)
                      {
                        $sql = "SELECT * FROM `groups` WHERE group_id = :group_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':group_id', $group_id);
                        $stmt->execute();
                        return $stmt->fetch();
                      }

                      try {
                        $sql = "SELECT * FROM `timetest`
                WHERE DATE_FORMAT(timetest_date, '%Y%m%d') >= CONCAT(YEAR(CURDATE()) + 543, LPAD(MONTH(CURDATE()), 2, '0'), LPAD(DAY(CURDATE()), 2, '0'))
                ORDER BY timetest_date ASC, start_time ASC";
                        $stmt = $conn->query($sql);
                        $datas = $stmt->fetchAll();

                        if (empty($datas)) {
                          echo "<tr><td colspan='6' class='text-center'>No data available</td></tr>";
                        } else {
                          $index = 1;
                          foreach ($datas as $data) {
                            $projects = giveProjectById($conn, $data['project_id']);
                            //นักศึกษา 1
                            $student1 = giveStudentById($conn, $projects['student_id1']);
                            //นักศึกษา 2
                            $student2 = ($projects['student_id2']) ? giveStudentById($conn, $projects['student_id2']) : null;
                            //นักศึกษา 3
                            $student3 = ($projects['student_id3']) ? giveStudentById($conn, $projects['student_id3']) : null;
                            //อาจารย์ที่ปรึกษาหลัก
                            $teacher1 = giveTeacherById($conn, $projects['teacher_id1']);
                            //อาจารย์ที่ปรึกษาร่วม
                            $teacher2 = ($projects['teacher_id2']) ? giveTeacherById($conn, $projects['teacher_id2']) : null;
                            //ประธานกรรมการ
                            $referee_id = giveTeacherById($conn, $projects['referee_id']);
                            //กรรมการ 1
                            $referee_id1 = giveTeacherById($conn, $projects['referee_id1']);
                            //กรรมการ 2
                            $referee_id2 = giveTeacherById($conn, $projects['referee_id2']);
                      ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <td><?php echo $projects['project_id']; ?></td>
                              <td><?php echo $projects['project_nameTH']; ?></td>
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
                              <td><?php echo $data['timeTest_date']; ?></td>
                              <td><?php echo $data['start_time']; ?></td>
                              <td><?php echo $data['stop_time']; ?></td>
                              <td><?php echo $data['room_number']; ?></td>
                            </tr>
                      <?php
                          }
                        }
                      } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                      }
                      ?>
                    </tbody>


                  </table>
                </div>
                <!-- <a href="#" class="btn btn-block btn-light d-grid gap-2">View All</a> -->
              </div>
            </div>
          </div>
        </div>


        <div class="row mb-4">
          <div class="col-12 me-3 mb-4 mb-lg-0">
            <div class="card shadow-sm">
              <h5 class="card-header">กำหนดการในรายวิชา</h5>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col" style="width: 5em;">ลำดับที่</th>
                        <th scope="col">หัวข้อกำหนดการ</th>
                        <th scope="col">เนื้อหากำหนดการ</th>
                        <th scope="col">วันเวลาที่สิ้นสุดกำหนดการ</th>
                        <th scope="col">กลุ่มเรียน</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      try {
                        $sql = "SELECT * FROM `appoint` WHERE (DATE_FORMAT(appoint_date, '%Y%m%d') >= CONCAT(YEAR(CURDATE()) + 543, LPAD(MONTH(CURDATE()), 2, '0'), LPAD(DAY(CURDATE()), 2, '0')))  
                        ORDER BY appoint_date ASC, group_id ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $datas = $stmt->fetchAll();

                        if (empty($datas)) {
                          echo "<tr><td colspan='6' class='text-center'>No data available</td></tr>";
                        } else {
                          $index = 1;
                          foreach ($datas as $data) {
                            $group_id = ($data['group_id']) ? giveGroupById($conn, $data['group_id']) : null;
                      ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <td><?php echo $data['title']; ?></td>
                              <td><?php echo ($data['description']); ?></td>
                              <td><?php echo ($data['appoint_date']); ?></td>
                              <td><?php echo $group_id ? $group_id['group_name'] : 'ทุกกลุ่มเรียน'; ?></td>
                            </tr>
                      <?php
                          }
                        }
                      } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                      }
                      ?>
                    </tbody>


                  </table>
                </div>
                <!-- <a href="#" class="btn btn-block btn-light d-grid gap-2">View All</a> -->
              </div>
            </div>
          </div>
        </div>


      </main>
    </div>
  </div>

</body>

</html>