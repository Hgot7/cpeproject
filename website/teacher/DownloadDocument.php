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

  <title>เอกสารในรายวิชา</title>

</head>

<body></body>

<!-- -------------------------------------------------Header------------------------------------------------- -->
<div class="HeaderBg shadow">
  <div class="container">
  <navbar_teacher-component></navbar_teacher-component>
  </div>
</div>
<div class="container-fluid justify-content-around">
  <div class="row">

   <?php include("sidebarTeacherComponent.php");?> 

    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Add information topicSection</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./add_dataDocument.php" method="post" enctype="multipart/form-data">

          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="submit" class="btn btn-primary">Add info</button>
          </div>
          </form>
        </div>

      </div>

    </div>

    <div class="row">
      <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">

        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลเอกสารในรายวิชาโครงงาน</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
            <li class="breadcrumb-item"><a href="./Teacherpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">เอกสารในรายวิชา</li>
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
            <div class="card-body">
              <div class="col-12 col-md-5">
                <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
              </div>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 5em;">ลำดับที่</th>
                      <th scope="col">ชื่อเอกสารในรายวิชา</th>
                      <th scope="col">ที่อยู่เอกสารในรายวิชา</th>
                      <th scope="col">วันเวลาที่อัปโหลดไฟล์เอกสาร</th>
                      
                  
                    </tr>
                  <tbody>
                    <?php

                      $stmt = $conn->query("SELECT * FROM `document` ORDER BY document_path ASC" );
                      $stmt->execute();
                      $documents = $stmt->fetchAll();
                      $index = 1;
                      if (!$documents) {
                        echo "<p><td colspan='6' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($documents as $document) {
                        ?>
                          <tr>
                            <th scope="row"><?php echo $index++; ?></th>
                            <td แสำ scope="row"><?php echo $document['document_name']; ?></td>
                            <td><a href="<?php echo '.././admin/uploadfileDocument/' . $document['document_path']; ?>" target="_blank"><?php echo $document['document_path']; ?></a></td>
                            <td scope="row"><?php echo $document['document_date']; ?></td>

                          </tr>
                    <?php }
                      }
                    
                    ?>
                  </tbody>
                  </thead>
                </table>
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

<script src="./search_data/search.js"></script>


</body>

</html>