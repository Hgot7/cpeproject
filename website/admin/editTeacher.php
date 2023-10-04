<?php
session_start();
require_once "../connect.php";

if (isset($_POST['update'])) {
    $teacher_id = $_POST['id'];
    $New_teacher_username = $_POST['input_teacher_username'];
    $New_position = $_POST['input_position'];
    $New_firstname = $_POST['input_firstname'];
    $New_lastname = $_POST['input_lastname'];
    $New_email = $_POST['input_email'];
    $New_phone = $_POST['input_phone'];
    $New_level_id = $_POST['input_level_id'];

    try {

        if (!isset($_SESSION['error'])) {
            $New_teacher_id = $_POST['new_teacher_id'];
            //  Null Coalescing Operator
            $New_teacher_id = empty($New_teacher_id) ? null : $New_teacher_id;
            //teacher name o
            $New_teacher_username = !isset($New_teacher_username) ? null : $New_teacher_username;
            $New_position = empty($New_position) ? null : $New_position;
            $New_firstname = empty($New_firstname) ? null : $New_firstname;
            $New_lastname = empty($New_lastname) ? null : $New_lastname;
            $New_email = empty($New_email) ? null : $New_email;
            $New_phone = empty($New_phone) ? null : $New_phone;
            $New_level_id = !isset($New_level_id) ? null : $New_level_id;



            $sql = $conn->prepare("UPDATE `teacher` SET teacher_id = :new_teacher_id, teacher_username = :input_teacher_username, position = :input_position, firstname = :input_firstname, 
      lastname = :input_lastname, email = :input_email ,phone = :input_phone, level_id = :input_level_id WHERE teacher_id = :id");
            $sql->bindParam(':new_teacher_id', $New_teacher_id);
            $sql->bindParam(':id', $teacher_id);
            $sql->bindParam(':input_teacher_username', $New_teacher_username);
            $sql->bindParam(':input_position', $New_position);
            $sql->bindParam(':input_firstname', $New_firstname);
            $sql->bindParam(':input_lastname', $New_lastname);
            $sql->bindParam(':input_email', $New_email);
            $sql->bindParam(':input_phone', $New_phone);
            $sql->bindParam(':input_level_id', $New_level_id);
            // $sql->bindParam(':inputstudent_id', $Old_student_id);

            $sql->execute();
            if ($sql) {
                $_SESSION['success'] = '<strong>รหัสประจำตัว </strong>' . $teacher_id . ' ได้รับการแก้ไขเรียบร้อยแล้ว';
                header("location: ./teachermanage.php");
            } else {
                $_SESSION['error'] = 'ข้อมูลอาจารย์ยังไม่ได้รับการแก้ไข';
                header("location: teachermanage.php");
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
        header("location: teachermanage.php");
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


    <title>หน้าแก้ไขข้อมูลผู้ดูแลระบบและอาจารย์</title>

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

                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขข้อมูลผู้ดูแลระบบและอาจารย์</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb fs-5 mt-3 ms-3">
                        <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="./teachermanage.php">จัดการข้อมูลผู้ดูแลระบบและอาจารย์</a></li>
                        <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลผู้ดูแลระบบและอาจารย์</li>
                    </ol>
                </nav>


                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
                        <form action="./editTeacher.php" method="post" enctype="multipart/form-data">
                            <?php
                            if (isset($_GET['id'])) {
                                $teacher_id = $_GET['id'];
                                $stmt = $conn->prepare("SELECT * FROM `teacher` WHERE teacher_id = :teacher_id");
                                $stmt->bindParam(':teacher_id', $teacher_id);
                                $stmt->execute();
                                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                            }
                            ?>
                            <input type="hidden" name="id" value="<?php echo $data['teacher_id'] ?? ''; ?>">

                            <div class="pt-3 justify-content-center">
                                <label class="form-label">รหัสประจำตัวอาจารย์</label>
                                <input type="text" class="form-control" name="new_teacher_id" placeholder="รหัสประจำตัวอาจารย์" required value="<?php echo $data['teacher_id'] ?? ''; ?>" readonly>
                            </div>

                            <div id="input_teacher_username">
                                <label class="form-label">ชื่อผู้ใช้งานระบบ</label>
                                <input type="text" class="form-control" name="input_teacher_username" id="input_teacher_username" value="<?php echo $data['teacher_username'] ?? ''; ?>" placeholder="ชื่อผู้ใช้งานระบบ">
                            </div>

                            <div id="input_position">
                                <label class="form-label">ตำแหน่งทางวิชาการ</label>
                                <input type="text" id="input_position" name="input_position" class="form-control" list="position_options" placeholder="ตำแหน่งทางวิชาการ" value="<?php echo $data['position'] ?? ''; ?>">
                                <datalist id="position_options">
                                    <option value="ศาสตราจารย์"></option>
                                    <option value="ศาสตราจารย์ ดร."></option>
                                    <option value="รองศาสตราจารย์"></option>
                                    <option value="รองศาสตราจารย์ ดร."></option>
                                    <option value="ผู้ช่วยศาสตราจารย์"></option>
                                    <option value="ผู้ช่วยศาสตราจารย์ ดร."></option>
                                    <option value="ดร."></option>
                                    <option value="อาจารย์"></option>
                                </datalist>
                            </div>


                            <div id="input_firstname">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" class="form-control" name="input_firstname" id="input_firstname" value="<?php echo $data['firstname'] ? $data['firstname'] : ''; ?>" placeholder="ชื่อ">
                            </div>

                            <div id="input_lastname">
                                <label class="form-label">นามสกุล</label>
                                <input type="text" class="form-control" name="input_lastname" id="input_lastname" value="<?php echo $data['lastname']; ?>" placeholder="นามสกุล">
                            </div>

                            <div id="input_email">
                                <label class="form-label">อีเมล</label>
                                <input type="text" class="form-control" name="input_email" id="input_email" value="<?php echo $data['email']; ?>" placeholder="อีเมล">
                            </div>

                            <div id="input_phone">
                                <label class="form-label">เบอร์โทรศัพท์</label>
                                <input type="text" class="form-control" name="input_phone" id="input_phone" value="<?php echo $data['phone']; ?>" placeholder="092xxxxxxx">
                            </div>

                            <div id="input_level_id" class="col-md-4">
                                <label class="form-label">สิทธิ์ผู้ใช้งาน</label>
                                <select id="selectbox" name="input_level_id" class="form-select">
                                    <option value="0" <?php if ($data['level_id'] == 0) echo 'selected'; ?>>Admin</option>
                                    <option value="1" <?php if ($data['level_id'] == 1) echo 'selected'; ?>>Teacher</option>
                                    <!-- เพิ่มตัวเลือกเพิ่มเติมตามต้องการ -->
                                </select>
                            </div>


                            <div class="pt-3 justify-content-center">
                                <button type="submit" name="update" id="update" class="btn btn-success">อัปเดต</button>
                                <a type="button" href="./teachermanage.php" class="btn btn-secondary ">กลับ</a>

                            </div>
                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>

</body>

</html>