<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
  $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
  header('Location: ../index.php');
  exit();
}

if (isset($_POST['update'])) {
  $news_id = $_POST['id'];
  $new_news_head = $_POST['input_news_head'];
  $new_news_text = $_POST['input_news_text'];
  $new_news_year = $_POST['input_news_year'];
  $new_news_term = $_POST['input_news_term'];
  try {
      // ตรวจสอบค่า $news_id ว่ามีค่าหรือไม่
      if (empty($news_id)) {
        $_SESSION['error'] = 'Invalid news ID';
        header("location: Newsmanage.php");
        exit();
      }
  
      $new_news_head = empty($new_news_head) ? null : $new_news_head;
      $new_news_text = empty($new_news_text) ? null : $new_news_text;
      $new_news_year = empty($new_news_year) ? null : $new_news_year;
      $new_news_term = empty($new_news_term) ? null : $new_news_term;
      $sql = $conn->prepare("UPDATE `news` SET news_head = :input_news_head, news_text = :input_news_text, news_date = CONCAT(YEAR(NOW()) + 543, DATE_FORMAT(NOW(), '-%m-%d %H:%i:%s')), year = :input_news_year, term = :input_news_term WHERE news_id = :id");
      
      $sql->bindParam(':id', $news_id);
      $sql->bindParam(':input_news_head', $new_news_head);
      $sql->bindParam(':input_news_text', $new_news_text);
      $sql->bindParam(':input_news_year', $new_news_year);
      $sql->bindParam(':input_news_term', $new_news_term);
      $result = $sql->execute(); // ตรวจสอบผลลัพธ์จากการ execute()
  
      if ($result) {
        $_SESSION['success'] = '<strong>ข้อมูลหัวข้อข่าว	</strong>'.$new_news_head . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
        header("location: ./Newsmanage.php");
      } else {
        $_SESSION['error'] = 'ข้อมูลข่าวสารยังไม่ได้รับการแก้ไข';
        header("location: Newsmanage.php");
      }
    
  } catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header("location: Newsmanage.php");
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


  <title>หน้าแก้ไขข้อมูลข่าวสารในรายวิชา</title>

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

        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขข้อมูลข่าวสารในรายวิชา</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./Newsmanage.php">จัดการข้อมูลข่าวสาร</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลข่าวสารในรายวิชา</li>
          </ol>
        </nav>

        <div class="row">
          <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
            <form action="./editnews.php" method="post" enctype="multipart/form-data">
              <?php
              if (isset($_GET['id'])) {
                $news_id = $_GET['id'];
                $stmt = $conn->prepare("SELECT * FROM `news` WHERE news_id = :news_id");
                $stmt->bindParam(':news_id', $news_id);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
              }
              ?>

              <div id="input_news_head">
                <label class="form-label">หัวข่าว<span style="color: red;"> *</span></label>
                <input type="text" class="form-control" name="input_news_head" id="input_news_head" value="<?php echo $data['news_head'] ?? ''; ?>" placeholder="หัวข้อข่าวสาร" required>
                <input type="hidden" name="id" value="<?php echo $data['news_id'] ?? ''; ?>">
              </div>


              <label class="form-label">เนื้อหาข่าว<span style="color: red;"> *</span></label>
              <div class="form-floating" id="input_news_text">
                <textarea class="form-control" name="input_news_text" id="input_news_text" required placeholder="เนื้อหากำหนดการ"><?php echo isset($data['news_text']) ? $data['news_text'] : ''; ?></textarea>
                <label for="description">รายละเอียด</label>
              </div>

              <div id="input_year">
                <label class="form-label">ปีการศึกษา<span style="color: red;"> *</span></label>
                <input type="number" class="form-control" name="input_news_year" id="input_news_year" value="<?php echo $data['year']; ?>" placeholder="ปีการศึกษา" required>
              </div>


              <div id="input_term">
                <label class="form-label">ภาคการศึกษา<span style="color: red;"> *</span></label>
                <select id="selectbox" name="input_news_term" class="form-select" required>
                <option value="" <?php if ($data['term'] == "") echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                  <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                  <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                  <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
                </select>
              </div>
              <div class="pt-3 justify-content-center">
              <button type="submit" name="update" id="update" class="btn btn-success">อัปเดต</button>
                <a type="button" href="./Newsmanage.php" class="btn btn-secondary ">กลับ</a>
                
              </div>
            </form>
          </div>
        </div>
      </main>

    </div>
  </div>

</body>

</html>