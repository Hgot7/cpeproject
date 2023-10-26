<?php
session_start();
require_once "../connect.php";


if (!isset($_SESSION['admin_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
}
if(isset($_SESSION['selectedYear'])){
  unset($_SESSION['selectedYear']);
}
if(isset($_SESSION['selectedTerm'])){
  unset($_SESSION['selectedTerm']);
}
if(isset($_SESSION['selectedGroup'])){
  unset($_SESSION['selectedGroup']);
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
    $_SESSION['success'] = "ลบข้อมูลเสร็จสิ้น";
    header("refresh:1; url=./studentmanage.php");
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

  <title>หน้าจัดการข้อมูลนักศึกษา</title>

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

    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มข้อมูลนักศึกษาในรายวิชา</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./add_dataStudent.php" method="post" enctype="multipart/form-data">
              <?php
              $defaultSystemId = 1;
              $stmt = $conn->prepare("SELECT * FROM `defaultsystem` WHERE default_system_id = :id");
              $stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
              $stmt->execute();
              $data = $stmt->fetch(PDO::FETCH_ASSOC);
              ?>
              <div id="inputstd_id">
                <label class="form-label">รหัสประจำตัวนักศึกษา<span style="color: red;"> *</span></label>
                <input type="number" class="form-control" name="inputstd_id" id="inputstd_id" value="<?php if (isset($_POST['inputstd_id'])) {
                                                                                                        echo $_POST['inputstd_id'];
                                                                                                      } ?>"required placeholder="รหัสประจำตัวนักศึกษาไม่มี( - )">
              </div>


              <div id="inputname">
                <label class="form-label">ชื่อ<span style="color: red;"> *</span></label>
                <input type="text" class="form-control" name="inputname" id="inputname" value="<?php echo isset($_POST['inputname']) ?  $_POST['inputname'] : '' ?>"required placeholder="ชื่อ">
              </div>

              <div id="inputlastname">
                <label class="form-label">นามสกุล<span style="color: red;"> *</span></label>
                <input type="text" class="form-control" name="inputlastname" id="inputlastname"required placeholder="นามสกุล">
              </div>

              <div id="inputyear">
                <label class="form-label">ปีการศึกษาที่ลงทะเบียน<span style="color: red;"> *</span></label>
                <input type="number" class="form-control" name="inputyear" id="inputyear" value="<?php echo isset($data['year']) ?  $data['year'] : '' ?>"required placeholder="ปีการศึกษาที่ลงทะเบียน">
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

              <!-- <div id="email">
                <label class="form-label">อีเมล</label>
                <input type="text" class="form-control" name="email" id="email" placeholder="@mail.rmutt.ac.th">
              </div> -->

              <div id="inputphone">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input type="text" class="form-control" name="inputphone" id="inputphone" placeholder="เบอร์โทรศัพท์ไม่เอา( - )">
              </div>


              <div  id="inputgroup">
                <label class="form-label">กลุ่มเรียนนักศึกษา<span style="color: red;"> *</span></label>
                <select id="selectbox" name="inputgroup" class="form-select" required>
                  <?php
                  $groups = $conn->query("SELECT * FROM `groups` ORDER BY group_id DESC");
                  $groups->execute();
                  ?>
                  <option value="<?php echo null ?>">เลือกกลุ่มเรียน</option>
                  <?php
                  while ($group = $groups->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?php echo $group['group_id']; ?>">
                      <?php echo $group['group_name']; ?>
                    </option>
                  <?php } ?>
                </select>

              </div>


              <div id="inputgrade">
                <label class="form-label">เกรด</label>
                <select id="selectbox" name="inputgrade" class="form-select">
                  <option value="">เลือกเกรด</option>
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
        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลนักศึกษาในรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-2 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลนักศึกษา</li>
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

              <form action="./studentmanage.php" method="POST">
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
                      $years = $conn->query("SELECT DISTINCT year FROM `student` ORDER BY year DESC");
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
                      $terms = $conn->query("SELECT DISTINCT term FROM `student` ORDER BY term DESC");
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
                        LEFT JOIN `student` ON groups.group_id = student.group_id
                        GROUP BY groups.group_id, groups.group_name
                        HAVING COUNT(student.group_id) >= 1
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
                  <form action="./studentmanage.php" method="POST" class="d-flex">
                    <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหารหัสนักศึกษา,ชื่อนักศึกษา" autocomplete="off" required>
                    <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                  </form>
                </div>
                <div class="col-auto">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" data-bs-whatever="@mdo">เพิ่มนักศึกษา</button>
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
                      <th class="text-center" scope="col" style="width : 6em;">ลำดับที่</th>
                      <th scope="col">รหัสนักศึกษา</th>
                      <!-- <th scope="col">รหัสผ่าน</th> -->
                      <th scope="col">ชื่อ</th>
                      <th scope="col">นามสกุล</th>
                      <th scope="col">ปีการศึกษา</th>
                      <th scope="col">ภาคการศึกษา</th>
                      <th scope="col">อีเมล</th>
                      <th scope="col">เบอร์โทรศัพท์</th>
                      <th scope="col">ชื่อกลุ่มเรียน</th>
                      <th scope="col">เกรด</th>
                      <th scope="col">คะแนน</th>
                      <th scope="col">Actions</th>
                    </tr>
                  <tbody>
                    <?php
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
                                   OR CONCAT(student.firstname, ' ', student.lastname) LIKE :inputText
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
                      // $selectedYear = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                      // $selectedTerm = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;
                      // $selectedGroup = isset($_POST['filtergroup']) ? $_POST['filtergroup'] : null;

                      if (empty($selectedYear) && empty($selectedTerm) && empty($selectedGroup)) {
                        $sql = "SELECT * FROM student";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
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
                        $filteredData = $stmt->fetchAll();
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
                        $filteredData = $stmt->fetchAll();
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
                        $filteredData = $stmt->fetchAll();
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
                        $filteredData = $stmt->fetchAll();
                      }
                      $index = 1;
                      if (!$filteredData) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($filteredData as $user) {
                          $group_id = ($user['group_id']) ? giveGroupById($conn, $user['group_id']) : null;
                    ?>
                          <tr>
                            <td><?php echo $index++; ?></td>
                            <td><?php echo $user['student_id']; ?></td>
                            <!-- <td><?php echo substr($user['student_password'], 0, 6); ?></td> -->
                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['year']; ?></td>
                            <td><?php echo $user['term']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td><?php echo $group_id ? $group_id['group_name'] : ''; ?></td>
                            <td><?php echo $user['grade']; ?></td>
                            <td><?php if (round($user['score']) != 0) {
                                  echo round($user['score']);
                                } else {
                                  echo '';
                                }; ?></td>
                            <td>
                              <a href="editStudent.php?id=<?php echo $user['student_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $user['student_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php
                        }
                      }
                    } elseif (isset($_POST['submitsearch'])) {
                      $SearchText = $_POST['search'];
                      $searchData = giveStudentSearch($conn, $SearchText);
                      $index = 1;
                      if (!$searchData) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($searchData as $user) {
                          $group_id = ($user['group_id']) ? giveGroupById($conn, $user['group_id']) : null; //แสดง group
                        ?>
                          <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $user['student_id']; ?></td>
                            <!-- <td><?php echo substr($user['student_password'], 0, 6); ?></td> -->
                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['year']; ?></td>
                            <td><?php echo $user['term']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td><?php echo $group_id ? $group_id['group_name'] : ''; ?></td>
                            <td><?php echo $user['grade']; ?></td>
                            <td><?php if (round($user['score']) != 0) {
                                  echo round($user['score']);
                                } else {
                                  echo '';
                                } ?></td>
                            <td>
                              <a href="editStudent.php?id=<?php echo $user['student_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $user['student_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } elseif (isset($_POST['viewAll'])) {

                      $stmt = $conn->query("SELECT * FROM `student`");
                      $stmt->execute();
                      $users = $stmt->fetchAll();
                      $index = 1;
                      if (!$users) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($users as $user) {
                        ?>
                          <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $user['student_id']; ?></td>
                            <!-- <td><?php echo substr($user['student_password'], 0, 6); ?></td> -->
                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['year']; ?></td>
                            <td><?php echo $user['term']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td><?php echo $user['group_id']; ?></td>
                            <td><?php echo $user['grade']; ?></td>
                            <td><?php if (round($user['score']) != 0) {
                                  echo round($user['score']);
                                } else {
                                  echo '';
                                } ?></td>
                            <td>
                              <a href="editStudent.php?id=<?php echo $user['student_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $user['student_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } else {                                                     //show nomal
                      $stmt = $conn->query("SELECT * FROM `student`");
                      $stmt->execute();
                      $users = $stmt->fetchAll();
                      $index = 1;
                      if (!$users) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($users as $user) {
                          $group_id = ($user['group_id']) ? giveGroupById($conn, $user['group_id']) : null;
                        ?>
                          <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $user['student_id']; ?></td>
                            <!-- <td><?php echo substr($user['student_password'], 0, 6); ?></td> -->
                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['year']; ?></td>
                            <td><?php echo $user['term']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td><?php echo $group_id ? $group_id['group_name'] : ''; ?></td>
                            <td><?php echo $user['grade']; ?></td>
                            <td><?php if (round($user['score']) != 0) {
                                  echo round($user['score']);
                                } else {
                                  echo '';
                                } ?></td>
                            <td>
                              <a href="editStudent.php?id=<?php echo $user['student_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $user['student_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
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
              <form action="./studentmanage.php" method="POST">
                <div class="d-grid gap-2">
                  <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" name="viewall" class="btn btn-secondary">View All</button>
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

<script src="./search_data/searchStudent.js"></script>


</body>

</html>