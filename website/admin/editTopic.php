<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
  }

if (isset($_POST['update'])) {
    $topic_id = $_POST['id'];
    $topic_name = $_POST['topic_name'];
    $topic_section_id = $_POST['topic_section_id'];
    // $topic_level = $_POST['topic_level'];
    $topic_status = $_POST['topic_status'];
    
    if (empty($topic_name)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อหัวข้อการประเมิน';
        header('location: topicmanage.php');
        exit(); // เพิ่มบรรทัดนี้เพื่อให้โปรแกรมหยุดทำงานทันที
      }
    try {
        if (!isset($_SESSION['error'])) {
            // Set null for empty values
            $topic_name = empty($topic_name) ? null : $topic_name;
            $topic_section_id = empty( $topic_section_id) ? null :  $topic_section_id;
            // $topic_level = empty($topic_level) ? 0 : $topic_level;
            $topic_status = empty($topic_status) ? 0 : $topic_status;

            $sql = $conn->prepare("UPDATE `topic` SET
                topic_name = :topic_name,
                topic_section_id = :topic_section_id,
                topic_status = :topic_status
                WHERE topic_id = :topic_id");
            $sql->bindParam(':topic_name', $topic_name);
            $sql->bindParam(':topic_section_id', $topic_section_id, PDO::PARAM_INT);
            // $sql->bindParam(':topic_level', $topic_level, PDO::PARAM_INT);
            $sql->bindParam(':topic_status', $topic_status, PDO::PARAM_INT);
            $sql->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
            $sql->execute();

            if ($sql) {
                $_SESSION['success'] = '<strong>รหัสหัวข้อการประเมิน </strong> '.$topic_id . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
            } else {
                $_SESSION['error'] = 'ข้อมูลหัวข้อการประเมินยังไม่ได้รับการแก้ไข';
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }
    header("Location: ./topicmanage.php");
    exit();
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

    <title>หน้าแก้ไขหัวข้อการประเมินโครงงาน</title>

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

            <sidebar_admin-component></sidebar_admin-component>

            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">
                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขหัวข้อการประเมินโครงงาน</h1>
                <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./topicmanage.php">จัดการข้อมูลหัวข้อการประเมินโครงงาน</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขหัวข้อการประเมินโครงงาน</li>
          </ol>
        </nav>

                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
                        <form action="./editTopic.php" method="post" enctype="multipart/form-data">
                            <?php
                            if (isset($_GET['id'])) {
                                $topic_id = $_GET['id'];
                                $stmt = $conn->prepare("SELECT * FROM `topic` WHERE topic_id = :topic_id");
                                $stmt->bindParam(':topic_id', $topic_id);
                                if ($stmt->execute()) {
                                    $topics = $stmt->fetch();
                                    if ($topics) {
                                        // ดำเนินการต่อไป
                                    } else {
                                        echo "ไม่พบข้อมูลกลุ่มที่ระบุ";
                                    }
                                } else {
                                    echo "เกิดข้อผิดพลาดในการเรียกข้อมูล";
                                }
                            } else {
                                echo "ไม่พบค่า id";
                            }
                            ?>

                            <input type="hidden" name="id" value="<?php echo $topics['topic_id']; ?>">

                            <!-- <div id="topic_id">
                        <label class="form-label">topic_id</label>
                            <input type="text" class="form-control" name="new_topic_id" placeholder="tp25xx-xxx" required value="<?php echo $topics['topic_id']; ?>">
                        </div> -->

                            <div id="topic_name">
                                <label class="form-label">ชื่อหัวข้อการประเมิน<span style="color: red;"> *</span></label>
                                <input type="text" class="form-control" name="topic_name" id="topic_name" value="<?php echo isset($topics['topic_name']) ? $topics['topic_name'] : ''; ?>" placeholder="ชื่อหัวข้อการประเมิน" required>
                            </div>

                            <div id="topic_section_id">
                                <label class="form-label">ส่วนของการประเมิน<span style="color: red;"> *</span></label>
                                <select name="topic_section_id" class="form-select" required>
                                    <?php
                                    $topicSections = $conn->query("SELECT * FROM `topicsection` ORDER BY topic_section_id DESC");
                                    $topicSections->execute();
                                    while ($topicSection = $topicSections->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($topicSection['topic_section_id'] == $topics['topic_section_id']) ? 'selected' : '';
                                        echo '<option value="' . $topicSection['topic_section_id'] . '" ' . $selected . '>';
                                        echo $topicSection['topic_section'];
                                        echo '</option>';
                                    }
                                    ?>

                                </select>

                            </div>

                            <!-- <div id="topic_level">
                                <label id="selectbox" class="form-label">รูปแบบการประเมิน</label>
                                <select name="topic_level" class="form-select">
                                    <option value="0" <?php if ($topics['topic_level'] == 0) echo 'selected'; ?>>ประเมินรายบุคคล</option>
                                    <option value="1" <?php if ($topics['topic_level'] == 1) echo 'selected'; ?>>ประเมินรายกลุ่มโครงงาน</option>
                                </select>
                            </div> -->

                            <div id="topic_status">
                                <label id="selectbox" class="form-label">สถานะการใช้งาน<span style="color: red;"> *</span></label>
                                <select name="topic_status" class="form-select" required>
                                    <option value="0" <?php if ($topics['topic_status'] == 0) echo 'selected'; ?>>ปิดการใช้งาน</option>
                                    <option value="1" <?php if ($topics['topic_status'] == 1) echo 'selected'; ?>>เปิดการใช้งาน</option>
                                </select>
                            </div>

                            <div class="pt-3 justify-content-center">
                            <button type="submit" name="update" class="btn btn-success">อัปเดต</button>
                                <a type="button" href="./topicmanage.php" class="btn btn-secondary ">กลับ</a>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>

</body>

</html>