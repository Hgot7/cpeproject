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
  try {
    // เตรียมคำสั่ง SQL ด้วย PDO
    $deletestmt = $conn->prepare("DELETE FROM `groups` WHERE group_id = :delete_id");
    $deletestmt->bindParam(':delete_id', $delete_id);

    // รันคำสั่ง SQL
    if ($deletestmt->execute()) {
      echo "<script>alert('Data has been deleted successfully');</script>";
      $_SESSION['success'] = "ลบข้อมูลเสร็จสิ้น";
      header("refresh:1; url=./groupmanage.php");
    }
  } catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ./groupmanage.php');
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

  <title>หน้าจัดข้อมูลกลุ่มเรียน</title>

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
    <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มกลุ่มเรียนในรายวิชา</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./add_dataGroup.php" method="post" enctype="multipart/form-data">
              <div id="inputgroup_id">
                <label class="form-label">ชื่อกลุ่มเรียน<span style="color: red;"> *</span></label>
                <input type="text" class="form-control" name="inputgroup_name" id="inputgroup_name" value="<?php if (isset($_POST['inputgroup_name'])) {
                                                                                                              echo $_POST['inputgroup_name'];
                                                                                                            } ?>" placeholder="ชื่อกลุ่มเรียน" required>
              </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="submit" name="submitgroup" class="btn btn-primary">เพิ่มข้อมูล</button>
          </div>
          </form>
        </div>

      </div>

    </div>
    <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">
      <div class="row">


        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลกลุ่มเรียนของนักศึกษาในรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-2 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลกลุ่มเรียน</li>
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
                <form action="./groupmanage.php" method="POST" class="d-flex">
                  <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาชื่อกลุ่มเรียน" autocomplete="off" required>
                  <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                </form>
              </div>
              <button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#groupModal" data-bs-whatever="@mdo">เพิ่มกลุ่มเรียน</button>

            </div>

            <div class="card-body">
              <div class="col-md-5">
                <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
              </div>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th class="text-center" scope="col" style="width : 10%;">ลำดับที่</th>
                      <th scope="col">ชื่อกลุ่มเรียน</th>

                      <!-- <th scope="col">urole</th> -->
                      <th scope="col">Actions</th>
                    </tr>
                  <tbody>
                    <?php
                    function giveGroupSearch($conn, $SearchText)
                    {
                      $sql = "SELECT * FROM `groups`
        WHERE group_name LIKE :inputText";
                      $stmt = $conn->prepare($sql);
                      $stmt->bindParam(':inputText', $SearchText);
                      $stmt->execute();
                      return $stmt->fetchAll();
                    }

                    if (isset($_POST['submitsearch'])) {
                      $SearchText = $_POST['search'];
                      $searchData = giveGroupSearch($conn, $SearchText);
                      $index = 1;
                      if (!$searchData) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($searchData as $group) { ?>
                           <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $group['group_name']; ?></td>
                            <td>
                              <a href="editgroup.php?id=<?php echo $group['group_id']; ?>" class="btn btn-warning">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $group['group_id']; ?>" class="btn btn-danger">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } elseif (isset($_POST['View All'])) {

                      $stmt = $conn->query("SELECT * FROM `groups` ORDER BY group_name DESC");
                      $stmt->execute();
                      $groups = $stmt->fetchAll();
                      $index = 1;
                      if (!$groups) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($groups as $group) {
                        ?>
                          <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $group['group_name']; ?></td>
                            <td>
                              <a href="editgroup.php?id=<?php echo $group['group_id']; ?>" class="btn btn-warning">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $group['group_id']; ?>" class="btn btn-danger">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } else {
                      $stmt = $conn->query("SELECT * FROM `groups` ORDER BY group_name DESC");
                      $stmt->execute();
                      $groups = $stmt->fetchAll();
                      $index = 1;
                      if (!$groups) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($groups as $group) {
                        ?>
                          <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $group['group_name']; ?></td>
                            <td>
                              <a href="editgroup.php?id=<?php echo $group['group_id']; ?>" class="btn btn-warning">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $group['group_id']; ?>" class="btn btn-danger">ลบข้อมูล</a>
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
              <form action="./groupmanage.php" method="POST">
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

<script src="./search_data/searchGroup.js"></script>


</body>

</html>