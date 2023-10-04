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

  // เตรียมคำสั่ง SQL ด้วย PDO
  $deletestmt = $conn->prepare("DELETE FROM `topicsection` WHERE topic_section_id = :delete_id");
  $deletestmt->bindParam(':delete_id', $delete_id);

  // รันคำสั่ง SQL
  if ($deletestmt->execute()) {
    echo "<script>alert('Data has been deleted successfully');</script>";
    $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
    header("refresh:1; url=./topicSectionmanage.php");
  }
}

function giveTopic_section_weight($conn)
{
  $topic_section_status = 1;
  $sql = "SELECT * FROM `topicsection` WHERE topic_section_status = :topic_section_status";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':topic_section_status', $topic_section_status);
  $stmt->execute();
  $datas = $stmt->fetchAll();
  $total_topic_section_weight = 0;

  foreach ($datas as $data) {
    $total_topic_section_weight += $data['topic_section_weight'];
  }

  $remaining_weight = 100 - $total_topic_section_weight;
  return $remaining_weight;
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

  <title>topicSectionmanage</title>

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
    <div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มส่วนของการประเมินโครงงานในรายวิชา</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./add_dataTopicSection.php" method="post" enctype="multipart/form-data">
              <div id="topic_section">
                <label class="form-label">ส่วนของการประเมินโครงงาน</label>
                <input type="text" class="form-control" name="topic_section" id="topic_section" value="<?php if (isset($_POST['topic_section'])) {
                                                                                                          echo $_POST['topic_section'];
                                                                                                        } ?>" placeholder="ส่วนของการประเมินโครงงาน">
              </div>


              <div id="topic_section_weight">
                <label class="form-label">น้ำหนักคะแนนส่วนของการประเมินเป็นเปอร์เซ็นต์</label>
                <input type="number" class="form-control" name="topic_section_weight" id="topic_section_weight" placeholder="ใส่แค่เลขไม่ต้องใส่ %" value="<?php echo giveTopic_section_weight($conn); ?>" max="<?php echo giveTopic_section_weight($conn); ?>">
              </div>

              <div id="topic_section_level">
                <label class="form-label">กำหนดสิทธิ์ส่วนของการประเมิน</label>
                <select id="selectbox" name="topic_section_level" class="form-select">
                  <option value="0">กรรมการสอบ</option>
                  <option value="1">อาจารย์ที่ปรึกษา </option>
                  <option value="2">กรรมการสอบและอาจารย์ที่ปรึกษา</option>
                </select>
              </div>


              <div id="topic_section_format">
                <label id="selectbox" class="form-label">รูปแบบการประเมิน</label>
                <select name="topic_section_format" class="form-select">
                  <option value="0">ประเมินรายบุคคล</option>
                  <option value="1">ประเมินรายกลุ่มโครงงาน</option>
                </select>
              </div>



              <div id="topic_section_status">
                <label id="selectbox" class="form-label">สถานะการใช้งาน</label>
                <select name="topic_section_status" class="form-select">
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


        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลส่วนของการประเมินโครงงานในรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-2 ms-3">
            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./topicmanage.php">จัดการข้อมูลหัวข้อการประเมินโครงงาน</a></li>
            <li class="breadcrumb-item active" aria-current="page">ข้อมูลส่วนของการประเมินโครงงานในรายวิชา</li>
          </ol>
        </nav>
        <?php
        $sql = "SELECT SUM(topic_section_weight) AS total_weight
        FROM `topicsection`
        WHERE topic_section_status = 1";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $stmt = $conn->query($sql);

        // ดึงผลลัพธ์เป็นแบบอาเรย์
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalWeight = (int)$result['total_weight'];

        if ($totalWeight !== 100) {
        ?>
          <h5 style="color: red; text-align: center;">***กรุณากรอกข้อมูลน้ำหนักคะแนนส่วนของการประเมินให้ครบ 100 %</h5>
        <?php
        }
        ?>
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
                <form action="./topicSectionmanage.php" method="POST" class="d-flex">
                  <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาส่วนของการประเมินโครงงาน" autocomplete="off" required>
                  <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                </form>
              </div>
              <button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#dataModal" data-bs-whatever="@mdo">เพิ่มส่วนของการประเมิน</button>
              <a type="button" href="./topicmanage.php" class="btn btn-warning text-white ms-3">กลับ</a>
            </div>

            <div class="card-body">
              <div class="col-md-5">
                <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
              </div>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 5em;">ลำดับที่</th>
                      <th scope="col">ส่วนของการประเมินโครงงาน</th>
                      <th scope="col">น้ำหนักคะแนนส่วนของการประเมินเป็นเปอร์เซ็นต์</th>
                      <th scope="col">กำหนดสิทธิ์ส่วนของการประเมิน</th>
                      <th scope="col">รูปแบบการประเมิน</th>
                      <th scope="col">สถานะการใช้งาน</th>
                      <th scope="col">Actions</th>
                    </tr>
                  <tbody>
                    <?php
                    if (isset($_POST['submitsearch'])) {
                      $SearchText = $_POST['search'];
                      $sql = "SELECT * FROM `topicsection`
                        WHERE topic_section LIKE :inputText";
                      $stmt = $conn->prepare($sql);
                      $stmt->bindParam(':inputText', $SearchText);
                      $stmt->execute();
                      $searchData = $stmt->fetchAll();
                      $index = 1;
                      if (!$searchData) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($searchData as $data) { ?>
                        <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $data['topic_section']; ?></td>
                            <td scope="row"><?php echo $data['topic_section_weight'] . '%'; ?></td>
                            <td scope="row"><?php if ($data['topic_section_level'] == 0) {
                                              echo "กรรมการสอบ";
                                            } elseif ($data['topic_section_level'] == 1) {
                                              echo "อาจารย์ที่ปรึกษา";
                                            } else {
                                              echo "กรรมการสอบและอาจารย์ที่ปรึกษา";
                                            } ?></td>
                            <td scope="row"><?php if ($data['topic_section_format'] == 1) {
                                              echo "ประเมินรายกลุ่มโครงงาน";
                                            } else {
                                              echo "ประเมินรายบุคคล";
                                            };  ?></td>
                            <td scope="row"><?php if ($data['topic_section_status'] == 0) {
                                              echo "ปิดการใช้งาน";
                                            } else {
                                              echo "เปิดการใช้งาน";
                                            } ?></td>
                            <td>
                              <a href="editTopicSection.php?id=<?php echo $data['topic_section_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $data['topic_section_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } elseif (isset($_POST['viewAll'])) {

                      $stmt = $conn->query("SELECT * FROM `topicsection` ORDER BY topic_section_id DESC");
                      $stmt->execute();
                      $datas = $stmt->fetchAll();
                      $index = 1;
                      if (!$datas) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($datas as $data) {
                        ?>
                         <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $data['topic_section']; ?></td>
                            <td scope="row"><?php echo $data['topic_section_weight'] . '%'; ?></td>
                            <td scope="row"><?php if ($data['topic_section_level'] == 0) {
                                              echo "กรรมการสอบ";
                                            } elseif ($data['topic_section_level'] == 1) {
                                              echo "อาจารย์ที่ปรึกษา";
                                            } else {
                                              echo "กรรมการสอบและอาจารย์ที่ปรึกษา";
                                            } ?></td>
                            <td scope="row"><?php if ($data['topic_section_format'] == 1) {
                                              echo "ประเมินรายกลุ่มโครงงาน";
                                            } else {
                                              echo "ประเมินรายบุคคล";
                                            };  ?></td>
                            <td scope="row"><?php if ($data['topic_section_status'] == 0) {
                                              echo "ปิดการใช้งาน";
                                            } else {
                                              echo "เปิดการใช้งาน";
                                            } ?></td>
                            <td>
                              <a href="editTopicSection.php?id=<?php echo $data['topic_section_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $data['topic_section_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                            </td>
                          </tr>
                        <?php }
                      }
                    } else {
                      $stmt = $conn->query("SELECT * FROM `topicsection` ORDER BY topic_section_id DESC");
                      $stmt->execute();
                      $datas = $stmt->fetchAll();
                      $index = 1;
                      if (!$datas) {
                        echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                      } else {
                        foreach ($datas as $data) {
                        ?>
                          <tr>
                            <td scope="row"><?php echo $index++; ?></td>
                            <td scope="row"><?php echo $data['topic_section']; ?></td>
                            <td scope="row"><?php echo $data['topic_section_weight'] . '%'; ?></td>
                            <td scope="row"><?php if ($data['topic_section_level'] == 0) {
                                              echo "กรรมการสอบ";
                                            } elseif ($data['topic_section_level'] == 1) {
                                              echo "อาจารย์ที่ปรึกษา";
                                            } else {
                                              echo "กรรมการสอบและอาจารย์ที่ปรึกษา";
                                            } ?></td>
                            <td scope="row"><?php if ($data['topic_section_format'] == 1) {
                                              echo "ประเมินรายกลุ่มโครงงาน";
                                            } else {
                                              echo "ประเมินรายบุคคล";
                                            };  ?></td>
                            <td scope="row"><?php if ($data['topic_section_status'] == 0) {
                                              echo "ปิดการใช้งาน";
                                            } else {
                                              echo "เปิดการใช้งาน";
                                            } ?></td>
                            <td>
                              <a href="editTopicSection.php?id=<?php echo $data['topic_section_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                              <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $data['topic_section_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
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
              <form action="./topicSectionmanage.php" method="POST">
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


<script src="./search_data/searchTopicSection.js"></script>


</body>

</html>