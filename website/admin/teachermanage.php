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
  $deletestmt = $conn->prepare("DELETE FROM `teacher` WHERE teacher_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);

  // รันคำสั่ง SQL
  if ($deletestmt->execute()) {
    echo "<script>alert('Data has been deleted successfully');</script>";
    $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
    header("refresh:1; url=./teachermanage.php");
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

  <title>หน้าจัดการข้อมูลผู้ดูแลระบบและอาจารย์</title>

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
            <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มข้อมูลผู้ดูแลระบบและอาจารย์</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./add_dataTeacher.php" method="post" enctype="multipart/form-data">
              <!-- <div id="inputteacher_id">
                <label class="form-label">รหัสประจำตัวอาจารย์</label>
                <input type="text" class="form-control" name="inputteacher_id" id="inputteacher_id" value="<?php if (isset($_POST['inputteacher_id'])) {
                                                                                                              echo $_POST['inputteacher_id'];
                                                                                                            } ?>" placeholder="รหัสประจำตัวอาจารย์">
              </div> -->

              <div id="inputteacher_username">
                <label class="form-label">ชื่อผู้ใช้งานระบบ</label>
                <input type="text" class="form-control" name="inputteacher_username" id="inputteacher_username" value="<?php echo isset($_POST['inputteacher_username']) ?  $_POST['inputteacher_username'] : '' ?>" placeholder="ชื่อผู้ใช้งานระบบ">
              </div>

              <div id="inputteacher_password">
                <label class="form-label">รหัสผ่าน</label>
                <input type="text" class="form-control" name="inputteacher_password" id="inputteacher_password" value="<?php echo isset($_POST['inputteacher_password']) ?  $_POST['inputteacher_password'] : '' ?>" placeholder="รหัสผ่าน">
              </div>


              <div id="inputposition">
                <label class="form-label">ตำแหน่งทางวิชาการ</label>
                <input type="text" id="inputposition" name="inputposition" class="form-control" list="position_options" placeholder="ตำแหน่งทางวิชาการ">
                <datalist id="position_options">
                  <option value="ศาสตราจารย์"></option>
                  <option value="ศาสตราจารย์ ดร."></option>
                  <option value="รองศาสตราจารย์"></option>
                  <option value="รองศาสตราจารย์ ดร."></option>
                  <option value="ผู้ช่วยศาสตราจารย์"></option>
                  <option value="ผู้ช่วยศาสตราจารย์ ดร."></option>
                  <option value="ดร."></option>
                  <option value="อาจารย์"></option>
                </datalist>
              </div>


              <div id="inputfirstname">
                <label class="form-label">ชื่อ</label>
                <input type="text" class="form-control" name="inputfirstname" id="inputfirstname" placeholder="firstname">
              </div>

              <div id="inputlastname">
                <label class="form-label">นามสกุล</label>
                <input type="text" class="form-control" name="inputlastname" id="inputlastname" placeholder="lastname">
              </div>

              <div id="email">
                <label class="form-label">อีเมล</label>
                <input type="text" class="form-control" name="inputemail" id="inputemail" placeholder="@mail.rmutt.ac.th">
              </div>

              <div id="inputphone">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input type="text" class="form-control" name="inputphone" id="inputphone" placeholder="092xxxxxxx">
              </div>

              <div id="inputlevel_id">
                <label class="form-label">สิทธิ์ผู้ใช้งาน</label>
                <select name="inputlevel_id" class="form-select">
                  <option value="0">ผู้ดูแลระบบ</option>
                  <option value="1">อาจารย์</option>

                  <!-- เพิ่มตัวเลือกเพิ่มเติมตามต้องการ -->
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


        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลผู้ดูแลระบบและอาจารย์</h1>
        <!-- <p>This is information Teacher of admin interface</p> -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-2 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลผู้ดูแลระบบและอาจารย์</li>
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
            <div class="card-header d-flex justify-content-between align-items-center">

              <div class="flex-grow-1">
                <form action="./teachermanage.php" method="POST" class="d-flex">
                  <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาชื่ออาจารย์" autocomplete="off" required>
                  <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                </form>
              </div>
              <button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#userModal" data-bs-whatever="@mdo">เพิ่มข้อมูลผู้ดูแลระบบและอาจารย์</button>
            </div>

            <div class="card-body">

              <div class="col-md-5">
                <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
              </div>

              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th class="text-center" scope="col" style="width : 6%;">ลำดับที่</th>
                      <th scope="col">รหัสประจำตัว</th>
                      <th scope="col">ชื่อผู้ใช้งานระบบ</th>

                      <th scope="col">ตำแหน่งทางวิชาการ</th>
                      <th scope="col">ชื่อ</th>
                      <th scope="col">นามสกุล</th>
                      <th scope="col">อีเมล</th>
                      <th scope="col">เบอร์โทรศัพท์</th>
                      <th scope="col">สิทธิ์ผู้ใช้งาน</th>
                      <th scope="col">Actions</th>
                    </tr>
                  <tbody>
                    <?php
                    function giveTeacherById($conn, $teacher_id)
                    {
                      $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                      $stmt = $conn->prepare($sql);
                      $stmt->bindParam(':teacher_id', $teacher_id);
                      $stmt->execute();
                      return $stmt->fetch();
                    }
                    if (isset($_POST['submitsearch'])) {
                      $SearchText = $_POST['search'];
                      $sql = "SELECT *
                      FROM `teacher`
                      WHERE teacher_id LIKE :inputText
                         OR CONCAT(position, ' ', firstname, ' ', lastname) LIKE :inputText
                         OR email LIKE :inputText
                         OR phone LIKE :inputText";
                      $stmt = $conn->prepare($sql);
                      $stmt->bindParam(':inputText', $SearchText);
                      $stmt->execute();
                      $searchData = $stmt->fetchAll();
                      $index = 1;
                      if (!$searchData) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($searchData as $user) {
                    ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <th scope="row"><?php echo $user['teacher_id']; ?></th>
                            <td><?php echo $user['teacher_username']; ?></td>
                            <td><?php echo $user['position']; ?></td>
                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td><?php if ($user['level_id'] == 0) {
                                  echo "Admin";
                                } else {
                                  echo "Teacher";
                                } ?>
                            </td>
                            <td>
                              <a href="editTeacher.php?id=<?php echo $user['teacher_id']; ?>" class="btn btn-warning">Edit</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $user['teacher_id']; ?>" class="btn btn-danger">Delete</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } elseif (isset($_POST['viewAll'])) {
                      $stmt = $conn->query("SELECT * FROM `teacher`");
                      $stmt->execute();
                      $users = $stmt->fetchAll();
                      $index = 1;
                      if (!$users) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($users as $user) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <th scope="row"><?php echo $user['teacher_id']; ?></th>
                            <td><?php echo $user['teacher_username']; ?></td>
                            <td><?php echo $user['position']; ?></td>
                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td><?php if ($user['level_id'] == 0) {
                                  echo "Admin";
                                } else {
                                  echo "Teacher";
                                } ?>
                            </td>
                            <td>
                              <a href="editTeacher.php?id=<?php echo $user['teacher_id']; ?>" class="btn btn-warning">Edit</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $user['teacher_id']; ?>" class="btn btn-danger">Delete</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } else {
                      $stmt = $conn->query("SELECT * FROM teacher");
                      $stmt->execute();
                      $teachers = $stmt->fetchAll();
                      $index = 1;
                      if (!$teachers) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($teachers as $user) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <th scope="row"><?php echo $user['teacher_id']; ?></th>
                            <td><?php echo $user['teacher_username']; ?></td>
                            <td><?php echo $user['position']; ?></td>
                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td><?php if ($user['level_id'] == 0) {
                                  echo "ผู้ดูแลระบบ";
                                } else {
                                  echo "อาจารย์";
                                } ?></td>
                            <td>
                              <a href="editTeacher.php?id=<?php echo $user['teacher_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $user['teacher_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
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
              <form action="./teachermanage.php" method="POST">
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

<script src="./search_data/searchTeacher.js"></script>




</body>

</html>