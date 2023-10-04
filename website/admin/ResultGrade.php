<?php
session_start();
require_once "../connect.php";


if (!isset($_SESSION['admin_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
}

//                                   delete data
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];

  // เตรียมคำสั่ง SQL ด้วย PDO
  $deletestmt = $conn->prepare("DELETE FROM `student` WHERE student_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);

  // รันคำสั่ง SQL
  if ($deletestmt->execute()) {
    echo "<script>alert('Data has been deleted successfully');</script>";
    $_SESSION['success'] = "Data has been deleted successfully";
    header("refresh:1; url=./ResultGrade.php");
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

  <title>หน้ารายงานสรุปเกรดวิชาโครงงานของนักศึกษาแต่ละภาคการศึกษา</title>

</head>

<body></body>

<!-- -------------------------------------------------Header------------------------------------------------- -->
<div class="HeaderBg shadow">
  <div class="container">
    <navbar_admin-component></navbar_admin-component> <!-- component.js navbar_admin-->
  </div>
</div>
<div class="container-fluid justify-content-around">
  <div class="row">

    <sidebar_admin-component></sidebar_admin-component> <!-- component.js sidebar_admin-->

    <div class="row">
      <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">

        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลรายงานสรุปเกรดวิชาโครงงานของนักศึกษาแต่ละภาคการศึกษา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./Reportpage.php">รายงานสรุป</a></li>
            <li class="breadcrumb-item active" aria-current="page">รายงานสรุปเกรดวิชาโครงงานของนักศึกษาแต่ละภาคการศึกษา</li>
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
        <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
          <div class="card shadow-sm">
            <div class="card-header justify-content-between align-items-center">

              <form action="./ResultGrade.php" method="POST">
                <div class="row g-3 mb-2">

                  <div class="col-md-2">
                    <label for="filterYear" class="form-label">ฟิลเตอร์ปีการศึกษา</label>
                    <select class="form-select" name="filteryear">
                      <?php
                      $years = $conn->query("SELECT DISTINCT year FROM `student` ORDER BY year DESC");
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
                      $terms = $conn->query("SELECT DISTINCT term FROM `student` ORDER BY term DESC");
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
                        LEFT JOIN `student` ON groups.group_id = student.group_id
                        GROUP BY groups.group_id, groups.group_name
                        HAVING COUNT(student.group_id) >= 1
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

            </div>


            <div class="card-body">

              <div class="col-md-5">
                <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
              </div>

              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">ลำดับที่</th>
                      <th scope="col">รหัสนักศึกษา</th>
                      <th scope="col">ชื่อ</th>
                      <th scope="col">นามสกุล</th>
                      <th scope="col">ปีการศึกษา</th>
                      <th scope="col">ภาคการศึกษา</th>
                      <th scope="col">ชื่อกลุ่มเรียน</th>
                      <th scope="col">เกรด</th>

                    </tr>
                  <tbody>
                    <?php
                    $selected = '';
                    //select groups
                    function giveGroupById($conn, $group_id)
                    {
                      $sql = "SELECT * FROM `groups` WHERE group_id = :group_id";
                      $stmt = $conn->prepare($sql);
                      $stmt->bindParam(':group_id', $group_id);
                      $stmt->execute();
                      return $stmt->fetch();
                    }
                    //select search 
                    function giveStudentSearch($conn, $SearchText)
                    {
                      $sql = "SELECT student.*, groups.group_name 
                       FROM `student` LEFT JOIN `groups` ON student.group_id = groups.group_id
                       WHERE student.student_id LIKE :inputText
                          OR student.firstname LIKE :inputText
                          OR student.lastname LIKE :inputText
                          OR student.email LIKE :inputText
                          OR student.phone LIKE :inputText
                          OR student.group_id LIKE :inputText
                          OR groups.group_name LIKE :inputText"; // เพิ่มเงื่อนไขในการค้นหา group_name
                      $stmt = $conn->prepare($sql);
                      $stmt->bindParam(':inputText', $SearchText);
                      $stmt->execute();
                      return $stmt->fetchAll();
                    }
                    //start
                    // filter term and year
                    if (isset($_POST['submitfilter'])) {
                      $selectedYear = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                      $selectedTerm = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;
                      $selectedGroup = isset($_POST['filtergroup']) ? $_POST['filtergroup'] : null;

                      if (empty($selectedYear) && empty($selectedTerm) && empty($selectedGroup)) {
                        $sql = "SELECT * FROM `student`";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        $selected = 'เกรดนักศึกษาทั้งหมด';
                      } elseif (empty($selectedYear) && !empty($selectedTerm) && !empty($selectedGroup)) {   //ใส่ selectedTerm และ selectedGroup

                        $sql = "SELECT student.*
                        FROM `student`
                        LEFT JOIN `groups` ON student.group_id = groups.group_id
                        WHERE term LIKE :term
                        AND (groups.group_name LIKE :group_name AND :group_name <> '')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':term', $selectedTerm);
                        $stmt->bindParam(':group_name', $selectedGroup);
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        $selected = 'เกรดนักศึกษาทั้งหมดในภาคการศึกษาที่ ' . $selectedTerm . ' กลุ่ม ' . $selectedGroup;
                      } elseif (empty($selectedYear) && empty($selectedTerm) && !empty($selectedGroup)) {   //ใส่ selectedTerm และ selectedGroup

                        $sql = "SELECT student.*
                        FROM `student`
                        LEFT JOIN `groups` ON student.group_id = groups.group_id
                        WHERE (groups.group_name LIKE :group_name AND :group_name <> '')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':group_name', $selectedGroup);
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        $selected = 'เกรดนักศึกษาทั้งหมดกลุ่ม ' . $selectedGroup;
                      } elseif (!empty($selectedYear) && !empty($selectedTerm) && empty($selectedGroup)) {   //ใส่ selectedYear และ selectedTerm
                        $sql = "SELECT student.*
                        FROM `student`
                        LEFT JOIN `groups` ON student.group_id = groups.group_id
                        WHERE term LIKE :term
                        AND year LIKE :year";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':year', $selectedYear);
                        $stmt->bindParam(':term', $selectedTerm);
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        $selected = 'เกรดนักศึกษาทั้งหมดในปีการศึกษา ' . $selectedYear . ' ภาคการศึกษาที่ ' . $selectedTerm;
                      } elseif (!empty($selectedYear) && !empty($selectedTerm) && !empty($selectedGroup)) {  //ใส่ selectedYear และ selectedTerm และ selectedGroup
                        $sql = "SELECT student.*
                        FROM `student`
                        LEFT JOIN `groups` ON student.group_id = groups.group_id
                        WHERE year LIKE :year
                        AND term LIKE :term
                        AND (groups.group_name LIKE :group_name AND :group_name <> '')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':year', $selectedYear);
                        $stmt->bindParam(':term', $selectedTerm);
                        $stmt->bindParam(':group_name', $selectedGroup);
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        $selected = 'เกรดนักศึกษาทั้งหมดในปีการศึกษา ' . $selectedYear . ' ภาคการศึกษาที่ ' . $selectedTerm . ' กลุ่ม ' . $selectedGroup;
                      } else {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT student.*
                        FROM `student`
                        LEFT JOIN `groups` ON student.group_id = groups.group_id
                        WHERE year LIKE :year
                        OR term LIKE :term
                        OR (groups.group_name LIKE :group_name AND :group_name <> '')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':year', $selectedYear);
                        $stmt->bindParam(':term', $selectedTerm);
                        $stmt->bindParam(':group_name', $selectedGroup);
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        $selected = 'เกรดนักศึกษาทั้งหมด';
                      }

                      $index = 1;
                      if (empty($users)) {
                        echo "<tr><td colspan='20' class='text-center'>No data available</td></tr>";
                      } else {
                        foreach ($users as $user) {
                          $group_id = ($user['group_id']) ? giveGroupById($conn, $user['group_id']) : null;
                    ?>
                          <tr>
                            <td><?php echo $index++; ?></td>
                            <td><?php echo $user['student_id']; ?></td>

                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['year']; ?></td>
                            <td><?php echo $user['term']; ?></td>
                            <td><?php echo $group_id ? $group_id['group_name'] : ''; ?></td>
                            <td><?php echo $user['grade']; ?></td>

                          </tr>
                        <?php
                        }
                      }
                    } elseif (isset($_POST['viewAll'])) {

                      $stmt = $conn->query("SELECT * FROM `student`");
                      $stmt->execute();
                      $users = $stmt->fetchAll();
                      $index = 1;
                      $selected = 'เกรดนักศึกษาทั้งหมด';
                      if (!$users) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($users as $user) {
                        ?>
                          <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $user['student_id']; ?></td>

                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['year']; ?></td>
                            <td><?php echo $user['term']; ?></td>
                            <td><?php echo $user['group_id']; ?></td>
                            <td><?php echo $user['grade']; ?></td>
                            <td>
                              <a href="editStudent.php?id=<?php echo $user['student_id']; ?>" class="btn btn-warning">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $user['student_id']; ?>" class="btn btn-danger">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } else {                                                     //show nomal
                      $stmt = $conn->query("SELECT * FROM `student`");
                      $stmt->execute();
                      $users = $stmt->fetchAll();
                      $index = 1;
                      $selected = 'เกรดนักศึกษาทั้งหมด';
                      if (!$users) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($users as $user) {
                          $group_id = ($user['group_id']) ? giveGroupById($conn, $user['group_id']) : null;
                        ?>
                          <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $user['student_id']; ?></td>
                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['year']; ?></td>
                            <td><?php echo $user['term']; ?></td>
                            <td><?php echo $group_id ? $group_id['group_name'] : ''; ?></td>
                            <td><?php echo $user['grade']; ?></td>

                          </tr>
                    <?php }
                      }
                    }

                    ?>
                  </tbody>
                  </thead>
                </table>
              </div>
              <form action="./ResultGrade.php" method="POST">
                <div class="d-grid gap-2">
                  <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" name="viewall" class="btn btn-secondary">View All</button>
                </div>
              </form>
              <?php
              if (!empty($users)) { ?>
                <form action="./PDF_Result/PDF_ResultGrade.php" method="post" target="_blank">
                  <input type="hidden" name="data" value='<?php echo json_encode($users); ?>'>
                  <input type="hidden" name="select" value='<?php echo json_encode($selected); ?>'>
                  <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-info text-white mt-2">ดาว์โหลดเป็นไฟล์ PDF</button>
                  </div>
                </form> <?php } ?>
            </div>
          </div>
        </div>
    </div>
    </main>

  </div>
</div>




<!-- Link to jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="./search_data/searchStudent.js"></script>


</body>

</html>