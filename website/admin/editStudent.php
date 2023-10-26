<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
}
if (isset($_POST['update'])) {
  $std_id = $_POST['id'];
  $Newpassword = $_POST['inputpassword'];
  $Newfirstname = $_POST['inputfirstname'];
  $Newlastname = $_POST['inputlastname'];
  $New_year = $_POST['inputyear'];
  $New_term = $_POST['inputterm'];
  $New_email = $_POST['email'];
  $New_phone = $_POST['inputphone'];
  $New_group = $_POST['inputgroup_id'];
  $New_grade = $_POST['inputgrade'];

  //นำมาใช่ในการ check password
  $stmt = $conn->query("SELECT * from `student` Where student_id = $std_id");
  $stmt->execute();
  $data = $stmt->fetch();

  try {
    if (!isset($_SESSION['error'])) {
      $NewstudentID = $_POST['new_student_id'];
      if (empty($NewstudentID)) {
        $NewstudentID = null;
      }
      // สร้าง password
      // if (empty($Newpassword)) {
      //   if (empty($data['student_password'])) {
      //     $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน 6 ตัวท้ายรหัสนักศึกษาของ ' . $std_id . '';
      //     header("Location: ./studentmanage.php");
      //     exit();
      //   }
      //   $Newpassword = null;
      // } elseif (substr($Newpassword, 0, 6) !== str_repeat('*', 6)) {
      //   $Newpassword = password_hash($Newpassword, PASSWORD_DEFAULT);
      // } else {
      //   $Newpassword = $data['student_password'];
      // }
      //  Null Coalescing Operator
      $Newfirstname = empty($Newfirstname) ? null : $Newfirstname;
      $Newlastname = empty($Newlastname) ? null : $Newlastname;
      $New_year = empty($New_year) ? null : $New_year;
      $New_term = empty($New_term) ? null : $New_term;
      $New_email = empty($New_email) ? null : $New_email;
      $New_phone = empty($New_phone) ? null : $New_phone;
      $New_group = empty($New_group) ? null : $New_group;
      $New_grade = empty($New_grade) ? null : $New_grade;

      if (empty($NewstudentID)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header("location: studentmanage.php");
        exit();
      }
      if (empty($Newfirstname)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header("location: studentmanage.php");
        exit();
      }
      if (empty($Newlastname)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header("location: studentmanage.php");
        exit();
      }
      if (empty($New_year)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header("location: studentmanage.php");
        exit();
      }
      if (empty($New_term)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header("location: studentmanage.php");
        exit();
      }
      if (empty($New_email)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header("location: studentmanage.php");
        exit();
      }
      if (empty($New_group)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header("location: studentmanage.php");
        exit();
      }
      $sql = $conn->prepare("UPDATE `student` SET student_id = :new_student_id, firstname = :inputfirstname, lastname = :inputlastname, year = :inputyear, term = :inputterm, email = :email
      , phone = :inputphone, group_id = :inputgroup_id, grade = :inputgrade WHERE student_id = :id");
      $sql->bindParam(':new_student_id', $NewstudentID);
      $sql->bindParam(':id', $std_id);
      // $sql->bindParam(':Newpassword', $Newpassword);
      $sql->bindParam(':inputfirstname', $Newfirstname);
      $sql->bindParam(':inputlastname', $Newlastname);
      $sql->bindParam(':inputyear', $New_year);
      $sql->bindParam(':inputterm', $New_term);
      $sql->bindParam(':email', $New_email);
      $sql->bindParam(':inputphone', $New_phone);
      $sql->bindParam(':inputgroup_id', $New_group);
      $sql->bindParam(':inputgrade', $New_grade);

      $sql->execute();
      if ($sql) {
        $_SESSION['success'] = '<strong>รหัสนักศึกษา </strong>' . $std_id . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
        header("location: ./studentmanage.php");
      } else {
        $_SESSION['error'] = 'ข้อมูลนักศึกษายังไม่ได้รับการแก้ไข';
        header("location: studentmanage.php");
      }
    }
  } catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header("location: ./studentmanage.php");
    exit();
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


  <title>หน้าแก้ไขข้อมูลนักศึกษาในรายวิชา</title>

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

        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขข้อมูลนักศึกษาในรายวิชา</h1>
        <!-- <p>This is information Student of admin interface</p> -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./studentmanage.php">จัดการข้อมูลนักศึกษา</a></li>
            <!-- <li class="breadcrumb-item active" aria-current="page"></li> -->
            <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลนักศึกษาในรายวิชา</li>
          </ol>
        </nav>

        <div class="row">
          <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
            <form action="./editStudent.php" method="post" enctype="multipart/form-data">
              <?php
              if (isset($_GET['id'])) {
                $std_id = $_GET['id'];
                $stmt = $conn->query("SELECT * from `student` Where student_id = $std_id");
                $stmt->execute();
                $data = $stmt->fetch();

                //ทำหน้ากาก password ใน name="inputpassword"
                $firstSixCharacters = substr($data['student_password'], 0, 6);
                $maskedPassword = str_repeat('*', strlen($firstSixCharacters));
              
              }
              ?>
              <input type="hidden" name="id" value="<?php echo $data['student_id']; ?>">

              <div class="pt-3 justify-content-center">
                <label class="form-label">รหัสประจำตัวนักศึกษา<span style="color: red;"> *</span></label>
                <input type="number" class="form-control" name="new_student_id" id="new_student_id" placeholder="รหัสรหัสประจำตัวนักศึกษาไม่เอา( - )" required value="<?php echo $data['student_id']; ?>">
              </div>


              <!-- <div id="inputpassword">
                <label class="form-label">รหัสผ่าน</label>
                <input type="text" class="form-control" name="inputpassword" id="inputpassword" value="<?php echo $maskedPassword; ?>" placeholder="รหัสผ่าน">
              </div> -->

              <div id="inputfirstname">
                <label class="form-label">ชื่อ<span style="color: red;"> *</span></label>
                <input type="text" class="form-control" name="inputfirstname" id="inputfirstname" value="<?php echo $data['firstname']; ?>"required placeholder="ชื่อ">
              </div>

              <div id="inputlastname">
                <label class="form-label">นามสกุล<span style="color: red;"> *</span></label>
                <input type="text" class="form-control" name="inputlastname" id="inputlastname" value="<?php echo $data['lastname']; ?>"required placeholder="นามสกุล">
              </div>

              <div id="inputyear">
                <label class="form-label">ปีการศึกษาที่ลงทะเบียน<span style="color: red;"> *</span></label>
                <input type="number" class="form-control" name="inputyear" id="inputyear" value="<?php echo $data['year']; ?>"required placeholder="ปีการศึกษาที่ลงทะเบียน">
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

              <div id="email" >
                <label class="form-label">อีเมล<span style="color: red;"> *</span></label>
                <input type="text" class="form-control" name="email" id="email" value="<?php echo $data['email']; ?>" required placeholder="@mail.rmutt.ac.th">
              </div>

              <div id="inputphone">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input type="text" class="form-control" name="inputphone" id="inputphone" value="<?php echo $data['phone']; ?>" placeholder="เบอร์โทรศัพท์ไม่เอา( - )">
              </div>




              <div class="col-md-4">
                <label class="form-label">กลุ่มเรียนนักศึกษา<span style="color: red;"> *</span></label>
                <select id="selectbox" name="inputgroup_id" class="form-select" required>
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

              <div id="inputgrade">
                <label class="form-label">เกรด</label>
                <select id="selectbox" name="inputgrade" class="form-select">
                  <option value="" <?php if (empty($data['grade'])) echo 'selected'; ?>>เลือกเกรด</option>
                  <option value="A" <?php if ($data['grade'] == "A") echo 'selected'; ?>>A</option>
                  <option value="B+" <?php if ($data['grade'] == "B+") echo 'selected'; ?>>B+</option>
                  <option value="B" <?php if ($data['grade'] == "B") echo 'selected'; ?>>B</option>
                  <option value="C+" <?php if ($data['grade'] == "C+") echo 'selected'; ?>>C+</option>
                  <option value="C" <?php if ($data['grade'] == "C") echo 'selected'; ?>>C</option>
                  <option value="D+" <?php if ($data['grade'] == "D+") echo 'selected'; ?>>D+</option>
                  <option value="D" <?php if ($data['grade'] == "D") echo 'selected'; ?>>D</option>
                  <option value="F" <?php if ($data['grade'] == "F") echo 'selected'; ?>>F</option>
                  <option value="W" <?php if ($data['grade'] == "W") echo 'selected'; ?>>W</option>
                  <option value="I" <?php if ($data['grade'] == "I") echo 'selected'; ?>>I</option>
                </select>
              </div>
            


              <div class="pt-3 justify-content-center">
              <button type="submit" name="update" id="update" class="btn btn-success">อัปเดต</button>
                <a type="button" href="./studentmanage.php" class="btn btn-secondary ">กลับ</a>
                
              </div>
            </form>
          </div>
        </div>
      </main>

    </div>
  </div>

</body>

</html>