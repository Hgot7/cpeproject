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


  $filestmt = $conn->prepare("SELECT regulationFile_path FROM `regulation` WHERE regulation_id = :delete_id");
  $filestmt->bindParam(':delete_id', $delete_id);
  $filestmt->execute();
  $fileData = $filestmt->fetch(PDO::FETCH_ASSOC);
  $regulationFile_path = $fileData['regulationFile_path'];

  if ($regulationFile_path && $regulationFile_path !== false) {
    $fileToDelete = "uploadfileRegulation/" . $regulationFile_path;
    if (is_file($fileToDelete)) {
      if (unlink($fileToDelete)) {
        // $_SESSION['success'] = "ลบไฟล์เสร็จสมบูรณ์";
        header("location: ./regulationmanage.php");
      } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบไฟล์";
        header("location: ./regulationmanage.php");
        exit;
      }
    } else {
      $_SESSION['error'] = "ไม่พบไฟล์ที่ต้องการลบ";
      header("location: ./regulationmanage.php");
      exit;
    }
  }



  // เตรียมคำสั่ง SQL ด้วย PDO
  $deletestmt = $conn->prepare("DELETE FROM `regulation` WHERE regulation_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);

  // รันคำสั่ง SQL
  if ($deletestmt->execute()) {
    echo "<script>alert('Data has been deleted successfully');</script>";
    $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
    header("refresh:1; url=./regulationmanage.php");
    exit;
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

  <title>หน้าจัดการข้อมูลกฎข้อบังคับ</title>

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
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มข้อมูลกฎข้อบังคับในรายวิชา</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./add_dataRegulation.php" method="post" enctype="multipart/form-data">
              <!-- <div id="inputnew_id">
                <label class="form-label">new_id</label>
                <input type="text" class="form-control" name="inputnew_id" id="inputnew_id" value="<?php if (isset($_POST['inputnew_id'])) {
                                                                                                      echo $_POST['inputnew_id'];
                                                                                                    } ?>" placeholder="n25xx-xxx">
              </div> -->
              <?php
              $defaultSystemId = 1;
              $stmt = $conn->prepare("SELECT * FROM `defaultsystem` WHERE default_system_id = :id");
              $stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
              $stmt->execute();
              $data = $stmt->fetch(PDO::FETCH_ASSOC);
              ?>
              <div id="regulationFile_path">
                <label class="form-label">เอกสารกฎข้อบังคับ</label>
                <input type="file" class="form-control" name="regulationFile_path" id="regulationFile_path" placeholder="ที่อยู่ไฟล์" accept=".pdf">
              </div>

              <div id="inputnew_text">
                <label class="form-label">กฎข้อบังคับในรายวิชา</label>
                <input type="text" id="inputregulation_text" name="inputregulation_text" class="form-control" placeholder="กฎข้อบังคับของรายวิชา">
              </div>

              <div id="year">
                <label class="form-label">ปีการศึกษาที่ใช้งาน</label>
                <input type="number" class="form-control" name="inputyear" id="inputyear" value="<?php echo isset($data['year']) ?  $data['year'] : '' ?>" placeholder="25xx">
              </div>

              <div id="term">
                <label class="form-label">ภาคการศึกษาที่ใช้งาน</label>
                <select name="inputterm" class="form-select">
                  <option value="" <?php if ($data['term'] == "") echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                  <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                  <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                  <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
                </select>
              </div>
              <!-- <div id="inputnew_date">
                <label class="form-label">new_date</label>
                <input type="datetime-local" class="form-control" name="inputnew_date" id="inputnew_date" placeholder="วันเวลาที่ลงข่าว">
              </div>  -->

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


        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลกฎข้อบังคับในรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-2 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลกฎข้อบังคับ</li>
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

              <form action="./regulationmanage.php" method="POST">
                <div class="row g-3 mb-2">

                  <div class="col-md-2">
                    <label for="filterYear" class="form-label">ฟิลเตอร์ปีการศึกษา</label>
                    <select class="form-select" name="filteryear">
                      <?php
                      $years = $conn->query("SELECT DISTINCT year FROM `regulation` ORDER BY year DESC");
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
                      $terms = $conn->query("SELECT DISTINCT term FROM `regulation` ORDER BY term DESC");
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

              <div class="row pb-2">
                <div class="col">
                  <form action="./regulationmanage.php" method="POST" class="d-flex">
                    <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหากฎข้อบังคับ" autocomplete="off" required>
                    <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                  </form>
                </div>
                <div class="col-auto">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal" data-bs-whatever="@mdo">เพิ่มกฎข้อบังคับ</button>
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
                      <th class="text-center" scope="col" style="width : 6%;">ลำดับที่</th>
                      <th scope="col">เอกสารกฎข้อบังคับ</th>
                      <th scope="col">กฎข้อบังคับในรายวิชา</th>
                      <th scope="col">ปีการศึกษาที่ใช้งาน</th>
                      <th scope="col">ภาคการศึกษาที่ใช้งาน</th>
                      <th scope="col">Actions</th>
                    </tr>
                  <tbody>
                    <?php
                    if (isset($_POST['submitfilter'])) {
                      $selectedYear = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                      $selectedTerm = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;

                      if (empty($selectedYear) && empty($selectedTerm)) {
                        $stmt = $conn->query("SELECT * FROM `regulation` ORDER BY year DESC , term DESC");
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } elseif (!empty($selectedYear) && !empty($selectedTerm)) {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `regulation`
                        WHERE term LIKE :term
                           AND year LIKE :year
                        ORDER BY year DESC, term DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':year', $selectedYear);
                        $stmt->bindParam(':term', $selectedTerm);
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } elseif (!empty($selectedYear) && empty($selectedTerm)) {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `regulation`
                        WHERE year LIKE :year
                        ORDER BY year DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':year', $selectedYear);
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } elseif (empty($selectedYear) && !empty($selectedTerm)) {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `regulation`
                        WHERE term LIKE :term
                        ORDER BY term DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':term', $selectedTerm);
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } else {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `regulation`
                        WHERE term LIKE :term
                           OR year LIKE :year
                        ORDER BY year DESC, term DESC;";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':year', $selectedYear);
                        $stmt->bindParam(':term', $selectedTerm);
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
                            <td><a href="<?php echo './uploadfileRegulation/' . $data['regulationFile_path']; ?>" target="_blank"><?php echo $data['regulationFile_path']; ?></a></td>
                            <td><?php echo $data['regulation_text']; ?></td>
                            <td><?php echo $data['year']; ?></td>
                            <td><?php echo $data['term']; ?></td>
                            <td>

                              <a href="editRegulation.php?id=<?php echo $data['regulation_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $data['regulation_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>

                            </td>
                          </tr>
                        <?php }
                      }
                    }elseif (isset($_POST['submitsearch'])) {
                      $SearchText = $_POST['search'];

                      $sql = "SELECT * FROM `regulation`
                      WHERE regulation_text LIKE :inputText";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':inputText', $SearchText);
                                    $stmt->execute();
                      $searchData = $stmt->fetchAll();

                      $index = 1;
                      if (!$searchData) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($searchData as $data) {
                        ?>
                         <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <td><a href="<?php echo './uploadfileRegulation/' . $data['regulationFile_path']; ?>" target="_blank"><?php echo $data['regulationFile_path']; ?></a></td>
                            <td><?php echo $data['regulation_text']; ?></td>
                            <td><?php echo $data['year']; ?></td>
                            <td><?php echo $data['term']; ?></td>
                            <td>

                              <a href="editRegulation.php?id=<?php echo $data['regulation_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $data['regulation_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>

                            </td>
                          </tr>
                        <?php }
                      }
                    }elseif (isset($_POST['viewAll'])) {

                      $stmt = $conn->query("SELECT * FROM `regulation` ORDER BY year DESC , term DESC");
                      $stmt->execute();
                      $datas = $stmt->fetchAll();
                      $index = 1;
                      if (!$datas) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($datas as $data) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <td><a href="<?php echo './uploadfileRegulation/' . $data['regulationFile_path']; ?>" target="_blank"><?php echo $data['regulationFile_path']; ?></a></td>
                            <td><?php echo $data['regulation_text']; ?></td>
                            <td><?php echo $data['year']; ?></td>
                            <td><?php echo $data['term']; ?></td>
                            <td>

                              <a href="editRegulation.php?id=<?php echo $data['regulation_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $data['regulation_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>

                            </td>
                          </tr>
                        <?php }
                      }
                    } else {
                      $stmt = $conn->query("SELECT * FROM `regulation` ORDER BY year DESC , term DESC");
                      $stmt->execute();
                      $datas = $stmt->fetchAll();
                      $index = 1;
                      if (!$datas) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($datas as $data) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <td><a href="<?php echo './uploadfileRegulation/' . $data['regulationFile_path']; ?>" target="_blank"><?php echo $data['regulationFile_path']; ?></a></td>
                            <td><?php echo $data['regulation_text']; ?></td>
                            <td><?php echo $data['year']; ?></td>
                            <td><?php echo $data['term']; ?></td>
                            <td>

                              <a href="editRegulation.php?id=<?php echo $data['regulation_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $data['regulation_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>

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
              <form action="./regulationmanage.php" method="POST">
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

<script src="./search_data/searchRegulation.js"></script>





</body>

</html>