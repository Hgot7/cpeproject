<?php
session_start();
require_once "../connect.php";

// if (!isset($_SESSION['admin_login'])) {
//   $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
//   header('Location: ../index.php');
// }

//                                   delete data
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];
  $deletestmt = $conn->prepare("DELETE FROM `timetest` WHERE timeTest_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);
  $deletestmt->execute();
  if ($deletestmt) {
    echo "<script>alert('Data has been deleted successfully');</script>";
    $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
    header("refresh:1; url=./TimeTestmanage.php");
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

  <title>หน้าจัดการข้อมูลเวลาสอบโครงงาน</title>

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
            <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มเวลาสอบโครงงานในรายวิชา</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./add_TimeTest.php" method="post" enctype="multipart/form-data">


              <div id="timeTest_date">
                <label class="form-label">วันที่สอบ</label>
                <input type="date" class="form-control" name="timeTest_date" id="timeTest_date" placeholder="yyyy-mm-dd">
              </div>




              <div id="start_time">
                <label class="form-label">เริ่มสอบ</label>
                <input type="time" class="form-control" name="start_time" id="start_time" value="<?php echo isset($_POST['start_time']) ?  $_POST['start_time'] : '' ?>" placeholder="xx:xx">
              </div>

              <div id="stop_time">
                <label class="form-label">หมดเวลาสอบ</label>
                <input type="time" class="form-control" name="stop_time" id="stop_time" placeholder="xx:xx">
              </div>

              <div id="room_number">
                <label class="form-label">ห้องสอบ</label>
                <input type="text" class="form-control" name="room_number" id="room_number" placeholder="xxxxx">
              </div>

              <div id="project_id">
                <label class="form-label">รหัสโปรเจค</label>
                <input type="text" class="form-control" name="project_id" id="project_id" placeholder="25xx-xxx">
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


        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">เวลาสอบโครงงานของนักศึกษาในรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-2 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลเวลาสอบโครงงาน</li>
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
              <div>

              </div>
              <div class="flex-grow-1">
                <form action="./TimeTestmanage.php" method="POST" class="d-flex">
                  <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหารหัสโครงงาน" autocomplete="off" required>
                  <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                </form>
              </div>
              <button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#userModal" data-bs-whatever="@mdo">เพิ่มเวลาสอบโครงงาน</button>
            </div>

            <div class="card-body">
              <div class="col-md-5">
                <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
              </div>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th class="text-center" scope="col" style="width : 5em;">ลำดับที่</th>
                      <!-- <th scope="col">timeTest_id</th> -->
                      <th scope="col">รหัสโปรเจค</th>
                      <th scope="col">ห้องสอบ</th>
                      <th scope="col">วันที่สอบ</th>
                      <th scope="col">เริ่มสอบ</th>
                      <th scope="col">หมดเวลาสอบ</th>
                      <th scope="col">Actions</th>
                    </tr>
                  <tbody>
                    <?php
                    if (isset($_POST['submitsearch'])) {
                      $SearchText = $_POST['search'];
                      $sql = "SELECT * FROM `timetest`
                        WHERE project_id LIKE :inputText";
                      $stmt = $conn->prepare($sql);
                      $stmt->execute(['inputText' => '%' . $SearchText . '%']);
                      $searchData = $stmt->fetchAll();
                      $index = 1;
                      foreach ($searchData as $timetest) {
                    ?>
                        <tr>
                          <th scope="row"><?php echo $index++; ?></th>
                          <!-- <th scope="row"><?php echo $timetest['timeTest_id']; ?></th> -->
                          <td><?php echo $timetest['project_id']; ?></td>
                          <td><?php echo $timetest['room_number']; ?></td>
                          <td><?php echo $timetest['timeTest_date']; ?></td>
                          <td><?php echo $timetest['start_time']; ?></td>
                          <td><?php echo $timetest['stop_time']; ?></td>
                          <td>
                            <a href="editTimeTest.php?id=<?php echo $timetest['timeTest_id']; ?>" class="btn btn-warning">แก้ไขข้อมูล</a>
                            <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $timetest['timeTest_id']; ?>" class="btn btn-danger">ลบข้อมูล</a>
                          </td>
                        </tr>
                        <?php }
                    } elseif (isset($_POST['viewAll'])) {

                      $stmt = $conn->query("SELECT * FROM `timetest`");
                      $stmt->execute();
                      $time = $stmt->fetchAll();
                      $index = 1;
                      if (!$time) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($time as $timetest) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <!-- <th scope="row"><?php echo $timetest['timeTest_id']; ?></th> -->
                            <td><?php echo $timetest['project_id']; ?></td>
                            <td><?php echo $timetest['room_number']; ?></td>
                            <td><?php echo $timetest['timeTest_date']; ?></td>
                            <td><?php echo $timetest['start_time']; ?></td>
                            <td><?php echo $timetest['stop_time']; ?></td>

                            <td>
                              <a href="editTimeTest.php?id=<?php echo $timetest['timeTest_id']; ?>" class="btn btn-warning">Edit</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $timetest['timeTest_id']; ?>" class="btn btn-danger">Delete</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } else {
                      $stmt = $conn->query("SELECT * FROM `timetest` ORDER BY timeTest_date DESC , start_time DESC");
                      $stmt->execute();
                      $time = $stmt->fetchAll();
                      $index = 1;
                      if (!$time) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($time as $timetest) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <!-- <th scope="row"><?php echo $timetest['timeTest_id']; ?></th> -->
                            <td><?php echo $timetest['project_id']; ?></td>
                            <td><?php echo $timetest['room_number']; ?></td>
                            <td><?php echo $timetest['timeTest_date']; ?></td>
                            <td><?php echo $timetest['start_time']; ?></td>
                            <td><?php echo $timetest['stop_time']; ?></td>
                            <td>
                              <a href="editTimeTest.php?id=<?php echo $timetest['timeTest_id']; ?>" class="btn btn-warning">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $timetest['timeTest_id']; ?>" class="btn btn-danger">ลบข้อมูล</a>
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
              <form action="./TimeTestmanage.php" method="POST">
                <div class="d-grid gap-2">
                  <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" class="btn btn-secondary">View All</button>
                </div>

            </div>
          </div>
        </div>
      </div>
    </main>

  </div>
</div>

<!-- Link to jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="./search_data/searchTimetest.js"></script>


</body>

</html>