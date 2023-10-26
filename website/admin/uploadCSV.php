<?php

session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
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

    <title>หน้าอัปโหลดไฟล์ CSV</title>

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

                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">อัปโหลดไฟล์ CSV และบันทึกลงในฐานข้อมูล</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb fs-5 mt-3 ms-3">
                        <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active" aria-current="page">อัปโหลดไฟล์ CSV</li>
                    </ol>
                </nav>
                <?php
                $defaultSystemId = 1;
                $stmt = $conn->prepare("SELECT * FROM `defaultsystem` WHERE default_system_id = :id");
                $stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>

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
                <div class="card shadow-sm mb-3" id="card_CSV">
                    <div class="card-header border-success d-flex justify-content-between align-items-center">
                        <div class="col align-self-start">
                            อัปโหลดไฟล์ CSV. ในส่วนของรายชื่อนักศึกษา
                            <a href="<?php echo './CSV_import/templateCSV/เทมเพลตรายชื่อนักศึกษาที่ลงทะเบียนรายวิชาโปรเจค.xlsx'; ?>" target="_blank" rel="noopener noreferrer">เทมเพลตรายชื่อนักศึกษาที่ลงทะเบียนรายวิชาโครงงาน.xlsx</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .CSV เท่านั้น</label>
                            <form action="./CSV_import/uploadStudent.php" method="post" enctype="multipart/form-data" onsubmit="showLoading('form1')">
                                <div class="col-md3">
                                    <div class="input-group flex-nowrap">
                                        <input class="form-control" type="file" name="file" accept=".csv" id="formFileMultiple" multiple></input>
                                        <span class="input-group-text">ปีการศึกษา</span>
                                        <input type="number" class="form-control" name="numberYear" value="<?php echo isset($data['year']) ?  $data['year'] : '' ?>" placeholder="ปีการศึกษาที่ลงทะเบียน" style="width: 5em;">
                                        <span class="input-group-text">ภาคการศึกษา</span>
                                        <!-- <input type="text" class="form-control" name="numberTerm" placeholder="1 or 2 or 3"> -->

                                        <select id="selectbox" name="numberTerm" class="form-select">
                                            <option value="" <?php if ($data['term'] == "") echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                                            <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                                            <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                                            <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
                                        </select>

                                        <span class="input-group-text">กลุ่มเรียน</span>
                                        <select id="selectbox" name="inputgroup" class="form-select">
                                            <?php
                                            $groups = $conn->query("SELECT * FROM `groups` ORDER BY group_name DESC");
                                            $groups->execute();
                                            ?>
                                            <option value="<?php echo null ?>">เลือกกลุ่มเรียน</option>
                                            <?php

                                            while ($group = $groups->fetch(PDO::FETCH_ASSOC)) { ?>
                                                <option value="<?php echo $group['group_id']; ?>">
                                                    <?php echo $group['group_name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary">อัปโหลด</button>
                                    </div>
                                    <div class="loading-overlay mt-2" id="form1-loadingOverlay" style="display: none;">
                                        <div class="d-flex align-items-center text-center">
                                            <strong class="text-primary" role="status">กำลังอัปโหลดไฟล์...</strong>
                                            <div class="spinner-border text-primary ms-3" role="status"></div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="card shadow-sm mb-3" id="card_CSV">
                    <div class="card-header border-success d-flex justify-content-between align-items-center">
                        <div class="col align-self-start">
                            อัปโหลดไฟล์ CSV. ในส่วนของกำหนดการในรายวิชา
                            <a href="<?php echo './CSV_import/templateCSV/เทมเพลตกำหนดการในรายวิชา.xlsx'; ?>" target="_blank" rel="noopener noreferrer">เทมเพลตกำหนดการในรายวิชาโครงงาน.xlsx</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .CSV เท่านั้น</label>
                            <form action="./CSV_import/uploadappoint.php" method="post" enctype="multipart/form-data" onsubmit="showLoading('form2')">
                                <div class="input-group flex-nowrap">
                                    <input class="form-control" type="file" name="file" accept=".csv" id="formFileMultiple" multiple></input>
                                    <span class="input-group-text">กลุ่มเรียน</span>
                                    <select id="selectbox" name="inputgroup" class="form-select">
                                        <?php
                                        $groups = $conn->query("SELECT * FROM `groups` ORDER BY group_name DESC");
                                        $groups->execute();
                                        ?>
                                        <option value="<?php echo null ?>">ทุกกลุ่มเรียน</option>
                                        <?php

                                        while ($group = $groups->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value="<?php echo $group['group_id']; ?>">
                                                <?php echo $group['group_name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary">อัปโหลด</button>
                                </div>
                                <div class="loading-overlay mt-2" id="form2-loadingOverlay" style="display: none;">
                                    <div class="d-flex align-items-center text-center">
                                        <strong class="text-primary" role="status">กำลังอัปโหลดไฟล์...</strong>
                                        <div class="spinner-border text-primary ms-3" role="status"></div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="card shadow-sm mb-3" id="card_CSV">
                    <div class="card-header border-success d-flex justify-content-between align-items-center">
                        <div class="col align-self-start">
                            อัปโหลดไฟล์ CSV. ในส่วนของข้อมูลโครงงาน
                            <a href="<?php echo './CSV_import/templateCSV/เทมเพลตข้อมูลกลุ่มโปรเจค.xlsx'; ?>" target="_blank" rel="noopener noreferrer">เทมเพลตข้อมูลกลุ่มโครงงาน.xlsx</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .CSV เท่านั้น</label>
                            <form action="./CSV_import/uploadProject.php" method="post" enctype="multipart/form-data" onsubmit="showLoading('form3')">
                                <div class="input-group flex-nowrap">
                                    <input class="form-control" type="file" name="file" accept=".csv" id="formFileMultiple" multiple></input>
                                    <span class="input-group-text">กลุ่มเรียน</span>
                                    <select id="selectbox" name="inputgroup" class="form-select">
                                        <?php
                                        $groups = $conn->query("SELECT * FROM `groups` ORDER BY group_name DESC");
                                        $groups->execute();
                                        ?>
                                        <option value="<?php echo null ?>">เลือกกลุ่มเรียน</option>
                                        <?php

                                        while ($group = $groups->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value="<?php echo $group['group_id']; ?>">
                                                <?php echo $group['group_name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <!-- <input type="text" class="form-control" name="numberTerm" placeholder="1 or 2 or 3"> -->
                                    <button type="submit" class="btn btn-primary">อัปโหลด</button>
                                </div>
                                <div class="loading-overlay mt-2" id="form3-loadingOverlay" style="display: none;">
                                    <div class="d-flex align-items-center text-center">
                                        <strong class="text-primary" role="status">กำลังอัปโหลดไฟล์...</strong>
                                        <div class="spinner-border text-primary ms-3" role="status"></div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="card shadow-sm mb-3" id="card_CSV">
                    <div class="card-header border-success d-flex justify-content-between align-items-center">
                        <div class="col align-self-start">
                            อัปโหลดไฟล์ CSV. ในส่วนของเวลาสอบโครงงาน
                            <a href="<?php echo './CSV_import/templateCSV/เทมเพลตข้อมูลเวลาสอบโครงงาน.xlsx'; ?>" target="_blank" rel="noopener noreferrer">เทมเพลตข้อมูลเวลาสอบโครงงาน.xlsx</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .CSV เท่านั้น</label>
                            <form action="./CSV_import/uploadTimeTest.php" method="post" enctype="multipart/form-data" onsubmit="showLoading('form4')">
                                <div class="col-md3">
                                    <input class="form-control" type="file" name="file" accept=".csv" id="formFileMultiple" multiple></input>
                                    <button type="submit" class="btn btn-primary">อัปโหลด</button>
                                </div>
                                <div class="loading-overlay mt-2" id="form4-loadingOverlay" style="display: none;">
                                    <div class="d-flex align-items-center text-center">
                                        <strong class="text-primary" role="status">กำลังอัปโหลดไฟล์...</strong>
                                        <div class="spinner-border text-primary ms-3" role="status"></div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="card shadow-sm mb-3" id="card_CSV">
                    <div class="card-header border-success d-flex justify-content-between align-items-center">
                        <div class="col align-self-start">
                            อัปโหลดไฟล์ CSV. ในส่วนของข่าวสาร
                            <a href="<?php echo './CSV_import/templateCSV/เทมเพลตข่าวสารในรายวิชา.xlsx'; ?>" target="_blank" rel="noopener noreferrer">เทมเพลตข่าวสารในรายวิชา.xlsx</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .CSV เท่านั้น</label>
                            <form action="./CSV_import/uploadNews.php" method="post" enctype="multipart/form-data" onsubmit="showLoading('form5')">
                                <div class="input-group flex-nowrap">
                                    <input class="form-control" type="file" name="file" accept=".csv" id="formFileMultiple" multiple></input>
                                    <span class="input-group-text">ปีการศึกษา</span>
                                    <!-- <input type="text" class="form-control" name="numberYear" placeholder="25xx"> -->
                                    <input type="number" class="form-control" name="numberYear" value="<?php echo isset($data['year']) ?  $data['year'] : '' ?>" placeholder="ปีการศึกษา">
                                    <span class="input-group-text">ภาคการศึกษา</span>
                                    <!-- <input type="text" class="form-control" name="numberTerm" placeholder="1 or 2 or 3"> -->
                                    <select id="selectbox" name="numberTerm" class="form-select">
                                        <option value="" <?php if ($data['term'] == "") echo 'selected'; ?>>เลือกภาคการศึกษา</option>
                                        <option value="1" <?php if ($data['term'] == "1") echo 'selected'; ?>>1</option>
                                        <option value="2" <?php if ($data['term'] == "2") echo 'selected'; ?>>2</option>
                                        <option value="3" <?php if ($data['term'] == "3") echo 'selected'; ?>>3</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">อัปโหลด</button>
                                </div>
                                <div class="loading-overlay mt-2" id="form5-loadingOverlay" style="display: none;">
                                    <div class="d-flex align-items-center text-center">
                                        <strong class="text-primary" role="status">กำลังอัปโหลดไฟล์...</strong>
                                        <div class="spinner-border text-primary ms-3" role="status"></div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>





        </div>

        </main>

    </div>
    </div>

    <script>
              // animetion uploading    
      function showLoading(formId) {
        // แสดง Popup Loading เฉพาะฟอร์มที่ถูกส่ง
        document.getElementById(formId + "-loadingOverlay").style.display = "block";
        return true; // ต้อง return true เพื่อให้ฟอร์มส่งข้อมูลไปยัง action
      }

      function hideLoading(formId) {
        // ซ่อน Popup Loading เมื่ออัปโหลดสำเร็จ
        document.getElementById(formId + "-loadingOverlay").style.display = "none";
      }
      
    </script>

</body>

</html>