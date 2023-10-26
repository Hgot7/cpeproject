<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
}

// delete data
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];
  $deletestmt = $conn->prepare("DELETE FROM `topic` WHERE topic_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);
  $deletestmt->execute();
  if ($deletestmt) {
    echo "<script>alert('Data has been deleted successfully');</script>";
    $_SESSION['success'] = "ลบข้อมูลเสร็จสิ้น";
    header("refresh:1; url=./topicmanage.php");
  }
}
?>

<!DOCTYPE html>
<html>

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

  <title>หน้าจัดการข้อมูลหัวข้อการประเมินโครงงาน</title>
</head>

<body>
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
              <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มหัวข้อการประเมินโครงงานในรายวิชา</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="./add_dataTopic.php" method="post" enctype="multipart/form-data">
                <!-- <div id="topic_id">
                  <label class="form-label">topic_id</label>
                  <input type="text" class="form-control" name="topic_id" id="topic_id" value="<?php if (isset($_POST['topic_id'])) {
                                                                                                  echo $_POST['topic_id'];
                                                                                                } ?>" placeholder="tp25xx-xxx">
                </div> -->

                <div id="topic_name">
                  <label class="form-label">ชื่อหัวข้อการประเมิน<span style="color: red;"> *</span></label>
                  <input type="text" class="form-control" name="topic_name" id="topic_name" value="<?php echo isset($_POST['topic_name']) ?  $_POST['topic_name'] : '' ?>" placeholder="ชื่อหัวข้อการประเมินโครงงาน" required>
                </div>

                <div id="topic_section_id">
                  <label class="form-label">ส่วนของการประเมิน<span style="color: red;"> *</span></label>
                  <select id="selectbox" name="topic_section_id" class="form-select" required>

                    <?php
                    $groups = $conn->query("SELECT * FROM `topicsection` ORDER BY topic_section_id DESC");
                    $groups->execute();
                    while ($group = $groups->fetch(PDO::FETCH_ASSOC)) {
                      $selected = ($group['topic_section_id'] == $data['topic_section_id']) ? 'selected' : '';
                      echo '<option value="' . $group['topic_section_id'] . '" ' . $selected . '>';
                      echo $group['topic_section'];
                      echo '</option>';
                    }
                    ?>
                  </select>
                </div>


                <!-- <div id="topic_level">
                  <label class="form-label">รูปแบบการประเมิน</label>
                  <select name="topic_level" class="form-select">
                    <option value="0">ประเมินรายบุคคล</option>
                    <option value="1">ประเมินรายกลุ่มโครงงาน</option>
                  </select>
                </div> -->


                <div id="topic_status">
                  <label class="form-label">สถานะการใช้งาน<span style="color: red;"> *</span></label>
                  <select name="topic_status" class="form-select" required>
                    <option value="0">ปิดการใช้งาน</option>
                    <option value="1">เปิดการใช้งาน</option>
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

          <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลหัวข้อการประเมินโครงงาน</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb fs-5 mt-2 ms-3">
              <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
              <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลหัวข้อการประเมินโครงงาน</li>
            </ol>
          </nav>

          <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
          <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger" role="alert">
              <?php
              echo $_SESSION['error'];
              unset($_SESSION['error']);
              ?>
            </div>
          <?php } ?>
          <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-success" role="alert">
              <?php
              echo $_SESSION['success'];
              unset($_SESSION['success']);
              ?>
            </div>
          <?php } ?>
            <div class="card shadow-sm">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div></div>
                <div class="flex-grow-1">
                  <form action="./topicmanage.php" method="POST" class="d-flex">
                    <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาชื่อหัวข้อการประเมิน" autocomplete="off" required>
                    <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                  </form>
                </div>
                <button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#userModal" data-bs-whatever="@mdo">เพิ่มหัวข้อการประเมิน</button>
                <a href="./topicSectionmanage.php" class="btn btn-warning text-white ms-3">ส่วนของการประเมิน</a>
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
                        <!-- <th scope="col">topic_id</th> -->
                        <th scope="col">ชื่อหัวข้อการประเมิน</th>
                        <th scope="col">ส่วนของการประเมิน</th>
                        <!-- <th scope="col">รูปแบบการประเมิน</th> -->
                        <th scope="col">สถานะการใช้งาน</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- check submitsearch มาหรือไม่ -->
                      <?php

                      function giveTopicSectionById($conn, $topic_section_id)
                      {
                        $sql = "SELECT * FROM `topicsection` WHERE topic_section_id = :topic_section_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':topic_section_id', $topic_section_id);
                        $stmt->execute();
                        return $stmt->fetch();
                      }
                      if (isset($_POST['submitsearch'])) {
                        $SearchText = $_POST['search'];
                        $sql = "SELECT * FROM `topic`
                         WHERE topic_name LIKE :inputText";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':inputText', $SearchText);
                        $stmt->execute();
                        $searchData = $stmt->fetchAll();
                        $index = 1;
                        if (!$searchData) {
                          echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                        } else {
                          foreach ($searchData as $topic) { ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <td><?php echo $topic['topic_name']; ?></td>
                              <td><?php if (empty($topicSection['topic_section'])) {
                                    echo "";
                                  } else {
                                    echo $topicSection['topic_section'];
                                  }; ?></td>
                              <!-- <td><?php if ($topic['topic_level'] == 1) {
                                          echo "ประเมินรายกลุ่มโครงงาน";
                                        } else {
                                          echo "ประเมินรายบุคคล";
                                        };  ?></td>     -->
                              <td><?php if ($topic['topic_status'] == 1) {
                                    echo "เปิดการใช้งาน";
                                  } else {
                                    echo "ปิดการใช้งาน";
                                  };  ?></td>
                              <td>
                                <a href="editTopic.php?id=<?php echo $topic['topic_id']; ?>" class="btn btn-warning">แก้ไขข้อมูล</a>
                                <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $topic['topic_id']; ?>" class="btn btn-danger">ลบข้อมูล</a>
                              </td>
                            </tr>
                          <?php }
                        }
                      }elseif (isset($_POST['viewAll'])) {
                        $stmt = $conn->query("SELECT * FROM `topic` ORDER BY topic_status DESC , topic_section_id");
                        $stmt->execute();
                        $topics = $stmt->fetchAll();
                        $index = 1;
                        if (!$topics) {
                          echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                        } else {
                          foreach ($topics as $topic) {
                            $topicSection = giveTopicSectionById($conn, $topic['topic_section_id']);
                      ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <td><?php echo $topic['topic_name']; ?></td>
                              <td><?php if (empty($topicSection['topic_section'])) {
                                    echo "";
                                  } else {
                                    echo $topicSection['topic_section'];
                                  }; ?></td>
                              <!-- <td><?php if ($topic['topic_level'] == 1) {
                                          echo "ประเมินรายกลุ่มโครงงาน";
                                        } else {
                                          echo "ประเมินรายบุคคล";
                                        };  ?></td>     -->
                              <td><?php if ($topic['topic_status'] == 1) {
                                    echo "เปิดการใช้งาน";
                                  } else {
                                    echo "ปิดการใช้งาน";
                                  };  ?></td>
                              <td>
                                <a href="editTopic.php?id=<?php echo $topic['topic_id']; ?>" class="btn btn-warning">แก้ไขข้อมูล</a>
                                <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $topic['topic_id']; ?>" class="btn btn-danger">ลบข้อมูล</a>
                              </td>
                            </tr>
                          <?php }
                        }
                      } else {
                        $stmt = $conn->query("SELECT * FROM `topic` ORDER BY topic_status DESC , topic_section_id");
                        $stmt->execute();
                        $topics = $stmt->fetchAll();
                        $index = 1;
                        if (!$topics) {
                          echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                        } else {
                          foreach ($topics as $topic) {
                            $topicSection = giveTopicSectionById($conn, $topic['topic_section_id']);
                          ?>
                            <tr>
                              <th scope="row"><?php echo $index++; ?></th>
                              <td><?php echo $topic['topic_name']; ?></td>
                              <td><?php if (empty($topicSection['topic_section'])) {
                                    echo "";
                                  } else {
                                    echo $topicSection['topic_section'];
                                  }; ?></td>
                              <!-- <td><?php if ($topic['topic_level'] == 1) {
                                          echo "ประเมินรายกลุ่มโครงงาน";
                                        } else {
                                          echo "ประเมินรายบุคคล";
                                        };  ?></td>     -->
                              <td><?php if ($topic['topic_status'] == 1) {
                                    echo "เปิดการใช้งาน";
                                  } else {
                                    echo "ปิดการใช้งาน";
                                  };  ?></td>
                              <td>
                                <a href="editTopic.php?id=<?php echo $topic['topic_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                                <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $topic['topic_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                              </td>
                            </tr>
                      <?php }
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
                <form action="./topicmanage.php" method="POST">
                  <div class="d-grid gap-2">
                    <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" class="btn btn-secondary">View All</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
      </main>
    </div>
  </div>
  </div>

  <!-- Link to jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <script src="./search_data/searchTopic.js"></script>

</body>

</html>