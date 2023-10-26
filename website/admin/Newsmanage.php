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

  // เตรียมคำสั่ง SQL ด้วย PDO
  $deletestmt = $conn->prepare("DELETE FROM `news` WHERE news_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);

  // รันคำสั่ง SQL
  if ($deletestmt->execute()) {
    echo "<script>alert('Data has been deleted successfully');</script>";
    $_SESSION['success'] = "ลบข้อมูลเสร็จสิ้น";
    header("refresh:1; url=./Newsmanage.php");
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

  <title>หน้าจัดการข้อมูลข่าวสาร</title>

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
            <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มข่าวสารในรายวิชา</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./add_datanews.php" method="post" enctype="multipart/form-data">
              <!-- <div id="inputnews_id">
                <label class="form-label">news_id</label>
                <input type="text" class="form-control" name="inputnews_id" id="inputnews_id" value="<?php if (isset($_POST['inputnews_id'])) {
                                                                                                        echo $_POST['inputnews_id'];
                                                                                                      } ?>" placeholder="n25xx-xxx">
              </div> -->
              <?php
              $defaultSystemId = 1;
              $stmt = $conn->prepare("SELECT * FROM `defaultsystem` WHERE default_system_id = :id");
              $stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
              $stmt->execute();
              $data = $stmt->fetch(PDO::FETCH_ASSOC);
              ?>
              <div id="inputnews_head">
                <label class="form-label">หัวข่าว<span style="color: red;"> *</span></label>
                <input type="text" class="form-control" name="inputnews_head" id="inputnews_head" value="<?php echo isset($_POST['inputnews_head']) ?  $_POST['inputnews_head'] : '' ?>" placeholder="หัวข้อข่าวสาร" required>
              </div>

              <label class="form-label">เนื้อหาข่าว<span style="color: red;"> *</span></label>
              <div class="form-floating" id="inputnews_text" >
                <textarea type="text" class="form-control" name="inputnews_text" id="inputnews_text" required value="<?php echo isset($_POST['inputnews_text']) ?  $_POST['inputnews_text'] : '' ?>" placeholder="เนื้อหากำหนดการ"></textarea>
                <label for="floatingTextarea2">รายละเอียด</label>
              </div>

              <div id="inputyear">
                <label class="form-label">ปีการศึกษา<span style="color: red;"> *</span></label>
                <input type="number" class="form-control" name="inputyear" id="inputyear" value="<?php echo isset($data['year']) ?  $data['year'] : '' ?>" placeholder="ปีการศึกษา" required>
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


        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลข่าวสารในรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-2 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลข่าวสาร</li>
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
              <form action="./Newsmanage.php" method="POST">
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
                      $years = $conn->query("SELECT DISTINCT year FROM `news` ORDER BY year DESC");
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
                      $terms = $conn->query("SELECT DISTINCT term FROM `news` ORDER BY term DESC");
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
                  <form action="./Newsmanage.php" method="POST" class="d-flex">
                    <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาหัวข้อข่าว" autocomplete="off" required>
                    <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                  </form>
                </div>
                <div class="col-auto">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal" data-bs-whatever="@mdo">เพิ่มข่าวสาร</button>
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
                      <th class="text-center" scope="col" style="width : 5em;">ลำดับที่</th>
                      <!-- <th scope="col">news_id</th> -->
                      <th scope="col">หัวข้อข่าว</th>
                      <th scope="col">เนื้อหาข่าว</th>
                      <th scope="col">วันเวลาที่ลงข่าว</th>
                      <th scope="col">ปีการศึกษา</th>
                      <th scope="col">ภาคการศึกษา</th>
                      <th scope="col">Actions</th>
                    </tr>
                  <tbody>
                    <?php
                    if (isset($_POST['submitfilter'])) {
                      $selectedYear = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                      $selectedTerm = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;

                      if (empty($selectedYear) && empty($selectedTerm)) {
                        $stmt = $conn->query("SELECT * FROM `news` ORDER BY year DESC , term DESC");
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } elseif (!empty($selectedYear) && !empty($selectedTerm)) {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `news`
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
                        FROM `news`
                        WHERE year LIKE :year
                        ORDER BY year DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':year', $selectedYear);
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } elseif (empty($selectedYear) && !empty($selectedTerm)) {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `news`
                        WHERE term LIKE :term
                        ORDER BY term DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':term', $selectedTerm);
                        $stmt->execute();
                        $filteredData = $stmt->fetchAll();
                      } else {
                        // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                        $sql = "SELECT *
                        FROM `news`
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
                        foreach ($filteredData as $new) {
                    ?><tr>
                    <th scope="row"><?php echo $index++; ?></th>
                    <!-- <th scope="row"><?php echo $new['news_id']; ?></th> -->
                    <td><?php echo $new['news_head']; ?></td>
                    <td><?php echo $new['news_text']; ?></td>
                    <td><?php echo $new['news_date']; ?></td>
                    <td><?php echo $new['year']; ?></td>
                    <td><?php echo $new['term']; ?></td>
                    <td>
                      <a href="editnews.php?id=<?php echo $new['news_id']; ?>" class="btn btn-warning mt-1">แก้ไขข้อมูล</a>
                      <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $new['news_id']; ?>" class="btn btn-danger mt-1">ลบข้อมูล</a>
                    </td>
                  </tr>
                        <?php }
                      }
                    } elseif (isset($_POST['submitsearch'])) {
                      $SearchText = $_POST['search'];
                      $sql = "SELECT * FROM `news`
                       WHERE news_head LIKE :inputText";
                      $stmt = $conn->prepare($sql);
                      $stmt->bindParam(':inputText', $SearchText);
                      $stmt->execute();
                      $searchData = $stmt->fetchAll();
                      $index = 1;
                      if (!$searchData) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($searchData as $new) { ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <!-- <th scope="row"><?php echo $new['news_id']; ?></th> -->
                            <td><?php echo $new['news_head']; ?></td>
                            <td><?php echo $new['news_text']; ?></td>
                            <td><?php echo $new['news_date']; ?></td>
                            <td><?php echo $new['year']; ?></td>
                            <td><?php echo $new['term']; ?></td>
                            <td>
                              <a href="editnews.php?id=<?php echo $new['news_id']; ?>" class="btn btn-warning mt-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $new['news_id']; ?>" class="btn btn-danger mt-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } elseif (isset($_POST['viewAll'])) {

                      $stmt = $conn->query("SELECT * FROM `news` ORDER BY news_date DESC");
                      $stmt->execute();
                      $news = $stmt->fetchAll();
                      $index = 1;
                      if (!$news) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($news as $new) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <!-- <th scope="row"><?php echo $new['news_id']; ?></th> -->
                            <td><?php echo $new['news_head']; ?></td>
                            <td><?php echo $new['news_text']; ?></td>
                            <td><?php echo $new['news_date']; ?></td>
                            <td><?php echo $new['year']; ?></td>
                            <td><?php echo $new['term']; ?></td>
                            <td>
                              <a href="editnews.php?id=<?php echo $new['news_id']; ?>" class="btn btn-warning mt-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $new['news_id']; ?>" class="btn btn-danger mt-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } else {
                      $stmt = $conn->query("SELECT * FROM `news` ORDER BY news_date DESC");
                      $stmt->execute();
                      $news = $stmt->fetchAll();
                      $index = 1;
                      if (!$news) {
                        echo "<p><td colspan='6' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($news as $new) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <!-- <th scope="row"><?php echo $new['news_id']; ?></th> -->
                            <td><?php echo $new['news_head']; ?></td>
                            <td><?php echo $new['news_text']; ?></td>
                            <td><?php echo $new['news_date']; ?></td>
                            <td><?php echo $new['year']; ?></td>
                            <td><?php echo $new['term']; ?></td>
                            <td>
                              <a href="editnews.php?id=<?php echo $new['news_id']; ?>" class="btn btn-warning mt-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $new['news_id']; ?>" class="btn btn-danger mt-1">ลบข้อมูล</a>
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
              <form action="./Newsmanage.php" method="POST">
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

<script src="./search_data/searchNews.js"></script>


</body>

</html>