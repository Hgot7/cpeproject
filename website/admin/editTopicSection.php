<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
  }

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $topic_section = $_POST['topic_section'];
    $topic_section_weight = $_POST['topic_section_weight'];
    $topic_section_level = $_POST['topic_section_level'];
    $topic_section_format = $_POST['topic_section_format'];
    $topic_section_status = $_POST['topic_section_status'];

    if (empty($topic_section)) {
        $_SESSION['error'] = 'กรุณากรอกส่วนของการประเมินโครงงาน';
        header('Location: topicSectionmanage.php');
        exit();
    }

    try {
        $topic_section_weight = empty($topic_section_weight) ? 0 : $topic_section_weight;
        $topic_section_level = empty($topic_section_level) ? 0 : $topic_section_level;
        $topic_section_format = empty($topic_section_format) ? 0 : $topic_section_format;
        $topic_section_status = empty($topic_section_status) ? 0 : $topic_section_status;
        if($topic_section_weight == 0){
            $topic_section_status = 0;
          }
        if ($topic_section_status == 0) {
            $topic_section_weight = 0;

            $sql = $conn->prepare("UPDATE `topic` SET topic_status = :topic_status WHERE topic_section_id = :id");
            $sql->bindParam(':id', $id, PDO::PARAM_INT);
            $sql->bindParam(':topic_status', $topic_section_status, PDO::PARAM_INT);
            $sql->execute();
        }
        $sql = $conn->prepare("UPDATE `topicsection` SET topic_section = :topic_section, topic_section_weight = :topic_section_weight, topic_section_level = :topic_section_level, topic_section_format = :topic_section_format, topic_section_status = :topic_section_status WHERE topic_section_id = :id");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->bindParam(':topic_section', $topic_section);
        $sql->bindParam(':topic_section_weight', $topic_section_weight, PDO::PARAM_INT);
        $sql->bindParam(':topic_section_level', $topic_section_level, PDO::PARAM_INT);
        $sql->bindParam(':topic_section_format', $topic_section_format, PDO::PARAM_INT);
        $sql->bindParam(':topic_section_status', $topic_section_status, PDO::PARAM_INT);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $_SESSION['success'] = 'ข้อมูล ' . $topic_section . ' ถูกแก้ไขเรียบร้อยแล้ว';
        } else {
            $_SESSION['error'] = 'ไม่สามารถแก้ไขข้อมูลได้';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: topicSectionmanage.php");
    exit();
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

    <title>หน้าแก้ไขข้อมูลส่วนของการประเมินโครงงานในรายวิชา</title>

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

                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขข้อมูลส่วนของการประเมินโครงงานในรายวิชา</h1>
                <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
          <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./topicmanage.php">จัดการข้อมูลหัวข้อการประเมินโครงงาน</a></li>
            <li class="breadcrumb-item"><a href="./topicSectionmanage.php">ข้อมูลส่วนของการประเมินโครงงานในรายวิชา</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลส่วนของการประเมินโครงงานในรายวิชา</li>
          </ol>
        </nav>

                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
                        <form action="./editTopicSection.php" method="post" enctype="multipart/form-data">
                            <?php
                            if (isset($_GET['id'])) {
                                $id = $_GET['id'];
                                $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE topic_section_id = :id");
                                $stmt->bindParam(':id', $id);
                                if ($stmt->execute()) {
                                    $data = $stmt->fetch();
                                    if ($data) {
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

                            <input type="hidden" name="id" value="<?php echo $data['topic_section_id']; ?>">

                            <div class="pt-3 justify-content-center">
                                <label class="form-label">ส่วนของการประเมินโครงงาน<span style="color: red;"> *</span></label>
                                <input type="text" class="form-control" name="topic_section" placeholder="กรอกส่วนของการประเมินโครงงาน" required value="<?php echo $data['topic_section']; ?>">
                            </div>

                            <div class="pt-3 justify-content-center">
                                <label class="form-label">น้ำหนักคะแนนส่วนของการประเมินเป็นเปอร์เซ็นต์<span style="color: red;"> *</span></label>
                                <?php
                                $maxTopicSectionWeight = giveTopic_section_weight($conn) + $data['topic_section_weight'];
                                ?>
                                <input type="number" class="form-control" name="topic_section_weight" placeholder="กรอกน้ำหนักคะแนนส่วนของการประเมินเป็นเปอร์เซ็นต์" required value="<?php if($data['topic_section_weight'] == 0){echo $maxTopicSectionWeight;}else{echo $data['topic_section_weight'];} ?>" max="<?php echo $maxTopicSectionWeight; ?>">
                            </div>


                            <div id="topic_section_level">
                                <label class="form-label">กำหนดสิทธิ์ส่วนของการประเมิน<span style="color: red;"> *</span></label>
                                <select id="selectbox" name="topic_section_level" class="form-select" required>
                                    <option value="0" <?php if ($data['topic_section_level'] == 0) echo 'selected'; ?>>กรรมการสอบ </option>
                                    <option value="1" <?php if ($data['topic_section_level'] == 1) echo 'selected'; ?>>อาจารย์ที่ปรึกษา </option>
                                    <option value="2" <?php if ($data['topic_section_level'] == 2) echo 'selected'; ?>>กรรมการสอบและอาจารย์ที่ปรึกษา</option>
                                </select>
                            </div>


                            <div id="topic_section_format">
                                <label id="selectbox" class="form-label">รูปแบบการประเมิน<span style="color: red;"> *</span></label>
                                <select name="topic_section_format" class="form-select" required>
                                    <option value="0" <?php if ($data['topic_section_format'] == 0) echo 'selected'; ?>>ประเมินรายบุคคล</option>
                                    <option value="1" <?php if ($data['topic_section_format'] == 1) echo 'selected'; ?>>ประเมินรายกลุ่มโครงงาน</option>
                                </select>
                            </div>


                            <div id="topic_section_status">
                                <label id="selectbox" class="form-label">สถานะการใช้งาน<span style="color: red;"> *</span></label>
                                <select name="topic_section_status" class="form-select" required>
                                    <option value="0" <?php if ($data['topic_section_status'] == 0) echo 'selected'; ?>>ปิดการใช้งาน </option>
                                    <option value="1" <?php if ($data['topic_section_status'] == 1) echo 'selected'; ?>>เปิดการใช้งาน </option>
                                </select>
                            </div>

                            <div class="pt-3 justify-content-center">
                                <button type="submit" name="update" class="btn btn-success">อัปเดต</button>
                                <a type="button" href="./topicSectionmanage.php" class="btn btn-secondary">กลับ</a>
                            </div>




                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>
</body>

</html>