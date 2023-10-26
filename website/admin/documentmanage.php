<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
}
if (isset($_SESSION['selectedYear'])) {
  unset($_SESSION['selectedYear']);
}
if (isset($_SESSION['selectedTerm'])) {
  unset($_SESSION['selectedTerm']);
}
//                                   delete data
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];

  // ดึงข้อมูลไฟล์ที่ต้องการลบ
  $filestmt = $conn->prepare("SELECT document_path FROM `document` WHERE document_id = :delete_id");
  $filestmt->bindParam(':delete_id', $delete_id);
  $filestmt->execute();
  $fileData = $filestmt->fetch(PDO::FETCH_ASSOC);
  $document_path = $fileData['document_path'];

  if ($document_path && $document_path !== false) {
    $fileToDelete = "uploadfileDocument/" . $document_path;
    if (is_file($fileToDelete)) {
      if (unlink($fileToDelete)) {
        // $_SESSION['success'] = "ลบไฟล์เสร็จสมบูรณ์";
        header("location: ./documentmanage.php");
      } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบไฟล์";
        header("location: ./documentmanage.php");
        exit;
      }
    } else {
      $_SESSION['error'] = "ไม่พบไฟล์ที่ต้องการลบ";
      header("location: ./documentmanage.php");
      exit;
    }
  }

  // ลบข้อมูลจากฐานข้อมูล
  $deletestmt = $conn->prepare("DELETE FROM `document` WHERE document_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);
  if ($deletestmt->execute()) {
    $_SESSION['success'] = "ลบข้อมูลเสร็จสิ้น";
    header("location: ./documentmanage.php");
    exit;
  } else {
    $_SESSION['success'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    header("location: ./documentmanage.php");
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

  <title>หน้าจัดการข้อมูลเอกสารในรายวิชา</title>

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
            <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มข้อมูลเอกสารในรายวิชา</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./add_dataDocument.php" method="post" enctype="multipart/form-data">
              <?php
              $defaultSystemId = 1;
              $stmt = $conn->prepare("SELECT * FROM `defaultsystem` WHERE default_system_id = :id");
              $stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
              $stmt->execute();
              $data = $stmt->fetch(PDO::FETCH_ASSOC);
              ?>
              <div id="document_path">
                <label class="form-label">เอกสารในรายวิชา<span style="color: red;"> *</span></label>
                <input type="file" class="form-control" name="document_path" id="idocument_path" placeholder="ไฟล์เอกสารในรายวิชา" accept=".pdf,.docx" required>
              </div>

              <div id="topic_section">
                <label class="form-label">ชื่อเอกสารในรายวิชา<span style="color: red;"> *</span></label>
                <input type="text" class="form-control" name="document_name" id="document_name" value="<?php if (isset($_POST['document_name'])) {
                                                                                                          echo $_POST['document_name'];
                                                                                                        } ?>" placeholder="ชื่อเอกสารในรายวิชา" required>
              </div>

              <div id="year">
                <label class="form-label">ปีการศึกษาที่เริ่มใช้งาน<span style="color: red;"> *</span></label>
                <input type="number" class="form-control" name="year" id="year" value="<?php echo isset($data['year']) ?  $data['year'] : '' ?>" placeholder="ปีการศึกษาที่เริ่มใช้งาน" required>
              </div>

              <div id="term">
                <label class="form-label">ภาคการศึกษาที่เริ่มใช้งาน<span style="color: red;"> *</span></label>
                <select name="term" class="form-select" required>
                  <option value="" <?php if ($data['term'] == "") echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                  <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                  <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                  <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
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


        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลเอกสารในรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-2 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลเอกสารในรายวิชา</li>
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

              <form action="./documentmanage.php" method="POST">
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
                      $years = $conn->query("SELECT DISTINCT year FROM `document` ORDER BY year DESC");
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
                      $terms = $conn->query("SELECT DISTINCT term FROM `document` ORDER BY term DESC");
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
                  <form action="./documentmanage.php" method="POST" class="d-flex">
                    <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหากฎข้อบังคับ" autocomplete="off" required>
                    <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                  </form>
                </div>
                <div class="col-auto">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" data-bs-whatever="@mdo">เพิ่มข้อมูลเอกสารในรายวิชา</button>
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
                      <th scope="col">เอกสารในรายวิชา</th>
                      <th scope="col">ชื่อเอกสารในรายวิชา</th>
                      <th scope="col">วันเวลาที่อัปโหลดไฟล์เอกสาร</th>
                      <th scope="col">ปีการศึกษาที่เริ่มใช้งาน</th>
                      <th scope="col">ภาคการศึกษาที่เริ่มใช้งาน</th>
                      <th scope="col">Actions</th>
                    </tr>
                  <tbody>
                    <?php
                    if (isset($_POST['submitfilter'])) {
                      $selectedYear = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                      $selectedTerm = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;

                      if (empty($selectedYear) && empty($selectedTerm)) {
                        $stmt = $conn->query("SELECT * FROM `document` ORDER BY document_path ASC");
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } elseif (!empty($selectedYear) && !empty($selectedTerm)) {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `document`
                        WHERE term LIKE :term
                           AND year LIKE :year
                           ORDER BY document_path ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':year', $selectedYear);
                        $stmt->bindParam(':term', $selectedTerm);
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } elseif (!empty($selectedYear) && empty($selectedTerm)) {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `document`
                        WHERE year LIKE :year
                        ORDER BY document_path ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':year', $selectedYear);
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } elseif (empty($selectedYear) && !empty($selectedTerm)) {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `document`
                        WHERE term LIKE :term
                        ORDER BY document_path ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':term', $selectedTerm);
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } else {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `document`
                        WHERE term LIKE :term
                           OR year LIKE :year
                           ORDER BY document_path ASC";
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
                        foreach ($filteredData as $document) { ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <!-- <th scope="row"><?php echo $document['document_path']; ?></th> -->
                            <td><a href="<?php echo './uploadfileDocument/' . $document['document_path']; ?>" target="_blank"><?php echo $document['document_path']; ?></a></td>
                            <td scope="row"><?php echo $document['document_name']; ?></th>
                            <td scope="row"><?php echo $document['document_date']; ?></th>
                            <td scope="row"><?php echo $document['year']; ?></th>
                            <td scope="row"><?php echo $document['term']; ?></th>
                            <td>
                              <a href="editDocument.php?id=<?php echo $document['document_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $document['document_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } elseif (isset($_POST['submitsearch'])) {
                      $SearchText = $_POST['search'];
                      $sql = "SELECT * FROM `document`
                      WHERE document_name LIKE :inputText";
                      $stmt = $conn->prepare($sql);
                      $stmt->bindParam(':inputText', $SearchText);
                      $stmt->execute();
                      $searchData = $stmt->fetchAll();
                      $index = 1;
                      if (!$searchData) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($searchData as $document) { ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <!-- <th scope="row"><?php echo $document['document_path']; ?></th> -->
                            <td><a href="<?php echo './uploadfileDocument/' . $document['document_path']; ?>" target="_blank"><?php echo $document['document_path']; ?></a></td>
                            <td scope="row"><?php echo $document['document_name']; ?></th>
                            <td scope="row"><?php echo $document['document_date']; ?></th>
                            <td scope="row"><?php echo $document['year']; ?></th>
                            <td scope="row"><?php echo $document['term']; ?></th>
                            <td>
                              <a href="editDocument.php?id=<?php echo $document['document_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $document['document_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } elseif (isset($_POST['viewAll'])) {

                      $stmt = $conn->query("SELECT * FROM `document` ORDER BY document_path ASC");
                      $stmt->execute();
                      $documents = $stmt->fetchAll();
                      $index = 1;
                      if (!$documents) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($documents as $document) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <!-- <th scope="row"><?php echo $document['document_path']; ?></th> -->
                            <td><a href="<?php echo './uploadfileDocument/' . $document['document_path']; ?>" target="_blank"><?php echo $document['document_path']; ?></a></td>
                            <td scope="row"><?php echo $document['document_name']; ?></th>
                            <td scope="row"><?php echo $document['document_date']; ?></th>
                            <td scope="row"><?php echo $document['year']; ?></th>
                            <td scope="row"><?php echo $document['term']; ?></th>
                            <td>
                              <a href="editDocument.php?id=<?php echo $document['document_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $document['document_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } else {
                      $stmt = $conn->query("SELECT * FROM `document` ORDER BY document_path ASC");
                      $stmt->execute();
                      $documents = $stmt->fetchAll();
                      $index = 1;
                      if (!$documents) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($documents as $document) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <!-- <th scope="row"><?php echo $document['document_path']; ?></th> -->
                            <td><a href="<?php echo './uploadfileDocument/' . $document['document_path']; ?>" target="_blank"><?php echo $document['document_path']; ?></a></td>
                            <td scope="row"><?php echo $document['document_name']; ?></th>
                            <td scope="row"><?php echo $document['document_date']; ?></th>
                            <td scope="row"><?php echo $document['year']; ?></th>
                            <td scope="row"><?php echo $document['term']; ?></th>
                            <td>
                              <a href="editDocument.php?id=<?php echo $document['document_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $document['document_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
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
              <form action="./documentmanage.php" method="POST">
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

<script src="./search_data/searchDocument.js"></script>

</body>

</html>