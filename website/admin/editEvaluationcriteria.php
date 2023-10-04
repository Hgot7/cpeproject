<?php
session_start();
require_once "../connect.php";

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $evaluationcriteria_start = $_POST['evaluationcriteria_start'];
    $evaluationcriteria_end = $_POST['evaluationcriteria_end'];

    try {
        $sql = $conn->prepare("UPDATE `evaluationcriteria` SET evaluationcriteria_start = :start, evaluationcriteria_end = :end WHERE evaluationcriteria_id = :id");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->bindParam(':start', $evaluationcriteria_start, PDO::PARAM_INT);
        $sql->bindParam(':end', $evaluationcriteria_end, PDO::PARAM_INT);

        if ($sql->execute()) {
            $_SESSION['success'] = 'แก้ไขข้อมูลสำเร็จ';
            header("Location: ./editEvaluationcriteria.php");
            exit();
        } else {
            $_SESSION['error'] = 'ไม่สามารถแก้ไขได้';
            header("Location: editEvaluationcriteria.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        header("Location: editEvaluationcriteria.php");
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

    <title>หน้าจัดการเกณฑ์การประเมิน</title>

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
                <div class="col-md-7 col-lg-8">

                    <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลเกณฑ์การประเมิน</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb fs-5 mt-3 ms-3">
                            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item active" aria-current="page">จัดการเกณฑ์การประเมิน</li>
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
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-lg-0" style="width: 100%;">
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM `evaluationcriteria` ORDER BY evaluationcriteria_end DESC;");
                            $stmt->execute();
                            $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // ตรวจสอบว่ามีข้อมูลหรือไม่
                            if (!empty($datas)) {
                                foreach ($datas as $data) {
                            ?>
                                    <style>
                                        .form-container {
                                            display: flex;
                                            flex-wrap: wrap;
                                            /* ให้รายการ wrap ไปตามกว้างของ container */
                                            justify-content: space-between;
                                            /* ให้ระยะห่างระหว่างองค์ประกอบทางขวา */
                                            align-items: center;
                                            margin-bottom: 20px;
                                        }

                                        .label {
                                            font-weight: bold;
                                            margin-right: 10px;
                                        }

                                        .input-container {
                                            display: flex;
                                            align-items: center;
                                            flex-basis: calc(25% - 20px);
                                            /* ควบคุมความกว้างของแถวในการแสดงผล */
                                        }

                                        .input-container input {
                                            margin-right: 10px;
                                            width: 100%;
                                            /* ให้ input ยืดตามขนาดของ container */
                                        }

                                        .grade {
                                            margin-left: 10px;
                                        }

                                        .btn-container {
                                            margin-top: 10px;
                                        }
                                    </style>

                                    <form action="./editEvaluationcriteria.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $data['evaluationcriteria_id']; ?>">
                                        <div class="form-container">
                                            <div class="label">คะแนน</div>
                                            <div class="input-container">
                                                <input type="number" class="form-control" name="evaluationcriteria_start" placeholder="คะแนนเริ่มต้นของช่วงเกณฑ์การประเมิน" required value="<?php echo $data['evaluationcriteria_start']; ?>">
                                            </div>
                                            <div class="label">ถึง</div>
                                            <div class="input-container">
                                                <input type="number" class="form-control" name="evaluationcriteria_end" placeholder="คะแนนสิ้นสุดของช่วงเกณฑ์การประเมิน" required value="<?php echo $data['evaluationcriteria_end']; ?>">
                                            </div>
                                            <div class="grade"><?php echo "เกรด " . $data['grade']; ?></div>
                                            <div class="btn-container">
                                                <button type="submit" name="update" class="btn btn-success">อัปเดต</button>
                                            </div>
                                        </div>
                                        <hr>
                                    </form>





                            <?php
                                }
                            } else {
                                // ไม่พบข้อมูล
                                echo "ไม่พบข้อมูล";
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>
</body>

</html>