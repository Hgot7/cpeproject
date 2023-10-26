<?php
session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
}
if (isset($_SESSION['selectedGroup'])) {
    unset($_SESSION['selectedGroup']);
}
//                                   delete data
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $deletestmt = $conn->prepare("DELETE FROM `appoint` WHERE appoint_id = :delete_id");
    $deletestmt->bindParam(':delete_id', $delete_id);
    $deletestmt->execute();
    if ($deletestmt) {
        echo "<script>alert('Data has been deleted successfully');</script>";
        $_SESSION['success'] = "ลบข้อมูลเสร็จสิ้น";
        header("refresh:1; url=./Appointmanage.php");
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

    <title>หน้าจัดการกำหนดการในรายวิชา</title>

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
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">เพิ่มกำหนดการในรายวิชา</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="./add_Appoint.php" method="post" enctype="multipart/form-data">


                            <div id="title">
                                <label class="form-label">หัวข้อกำหนดการ<span style="color: red;"> *</span></label>
                                <input type="text" class="form-control" name="title" id="title" value="<?php echo isset($_POST['title']) ?  $_POST['title'] : '' ?>" placeholder="หัวข้อกำหนดการ" required>
                            </div>

                            <label class="form-label">เนื้อหากำหนดการ</label>
                            <div class="form-floating" id="description">

                                <textarea type="text" class="form-control" name="description" id="description" value="<?php echo isset($_POST['description']) ?  $_POST['description'] : '' ?>" placeholder="เนื้อหากำหนดการ"></textarea>
                                <label for="floatingTextarea2">รายละเอียด</label>
                            </div>

                            <div id="title">
                                <label class="form-label">วันเวลาที่สิ้นสุดกำหนดการ<span style="color: red;"> *</span></label>
                                <input type="datetime-local" class="form-control" name="appoint_date" id="appoint_date" value="<?php echo isset($_POST['appoint_date']) ?  $_POST['appoint_date'] : '' ?>" placeholder="หัวข้อกำหนดการ" required>
                            </div>

                            <div id="group_id">
                                <label class="form-label">กลุ่มเรียน<span style="color: red;"> *</span></label>
                                <!-- <input type="text" class="form-control" name="group_id" id="group_id" placeholder="กลุ่มเรียนนักศึกษา"> -->
                                <select id="selectbox" name="group_id" class="form-select" >
                                    <option value="<?php echo null ?>">ทุกกลุ่มเรียน</option>
                                    <?php
                                    $groups = $conn->query("SELECT * FROM `groups` ORDER BY group_id DESC");
                                    $groups->execute();
                                    while ($group = $groups->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($group['group_id'] == $data['group_id']) ? 'selected' : '';
                                        echo '<option value="' . $group['group_id'] . '" ' . $selected . '>';
                                        echo $group['group_name'];
                                        echo '</option>';
                                    }
                                    ?>
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
                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลกำหนดการในรายวิชา</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb fs-5 mt-2 ms-3">
                        <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active" aria-current="page">จัดการข้อมูลกำหนดการในรายวิชา</li>
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
                            <form action="./Appointmanage.php" method="POST">
                                <div class="row g-3 mb-2">

                                    <div class="col-md-6">
                                        <label for="filtergroup" class="form-label">ฟิลเตอร์กลุ่มเรียน</label>
                                        <select class="form-select" name="filtergroup">
                                            <?php
                                            if (isset($_POST['resetfilter'])) {
                                                unset($_SESSION['selectedGroup']);
                                            }
                                            if (isset($_POST['submitfilter'])) {
                                                $_SESSION['selectedGroup'] = isset($_POST['filtergroup']) ? $_POST['filtergroup'] : null;
                                                $selectedGroup = $_SESSION['selectedGroup'];
                                            }
                                            $groups = $conn->prepare("SELECT groups.group_name 
                                            FROM `groups`
                                            LEFT JOIN `appoint` ON groups.group_id = appoint.group_id
                                            GROUP BY groups.group_id, groups.group_name
                                            HAVING COUNT(appoint.group_id) >= 1
                                            ORDER BY groups.group_name DESC");
                                            $groups->execute();
                                            $selectedGroup = isset($_SESSION['selectedGroup']) ? $_SESSION['selectedGroup'] : null; // ดึงค่าที่ถูกเลือกจาก Session Variables     
                                            ?>
                                            <option value="">ทุกกลุ่มเรียน</option>
                                            <?php
                                              while ($datagroup = $groups->fetch(PDO::FETCH_ASSOC)) {
                                                $groupValue = $datagroup['group_name'];
                                                $isGroupSelected = ($selectedGroup == $groupValue) ? 'selected' : ''; // เพิ่มเงื่อนไขเช็คค่า selected
                                              ?>
                                                <option value="<?php echo $groupValue; ?>" <?php echo $isGroupSelected; ?>>
                                                  <?php echo $groupValue; ?>
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
                                    <form action="./Appointmanage.php" method="POST" class="d-flex">
                                        <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาหัวข้อกำหนดการ" autocomplete="off" required>
                                        <button type="submit" id="submitSearch" name="submitsearch" class="btn btn-success ms-3">ค้นหา</button>
                                    </form>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" data-bs-whatever="@mdo">เพิ่มกำหนดการ</button>
                                </div>
                            </div>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="col-md-5">
                                <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" scope="col" style="width : 6em;">ลำดับที่</th>
                                            <!-- <th scope="col">appoint_id</th> -->
                                            <th scope="col">หัวข้อกำหนดการ</th>
                                            <th scope="col">เนื้อหากำหนดการ</th>
                                            <th scope="col">วันเวลาที่สิ้นสุดกำหนดการ</th>
                                            <th scope="col">กลุ่มเรียน</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    <tbody>
                                        <?php
                                        function giveGroupById($conn, $group_id)
                                        {
                                            $sql = "SELECT * FROM `groups` WHERE group_id = :group_id";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':group_id', $group_id);
                                            $stmt->execute();
                                            return $stmt->fetch();
                                        }
                                        if (isset($_POST['submitfilter'])) {
                                            $selectedGroup = isset($_POST['filtergroup']) ? $_POST['filtergroup'] : null;

                                            if (empty($selectedGroup)) {
                                                $sql = "SELECT appoint.*
                                            FROM `appoint`
                                            LEFT JOIN `groups` ON appoint.group_id = groups.group_id
                                            WHERE appoint.group_id IS NULL";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->execute();

                                                $filteredData = $stmt->fetchAll();
                                            } elseif (!empty($selectedGroup)) {   //ใส่ selectedTerm และ selectedGroup

                                                $sql = "SELECT appoint.*
                                            FROM `appoint`
                                            LEFT JOIN `groups` ON appoint.group_id = groups.group_id
                                            WHERE (groups.group_name LIKE :group_name AND :group_name <> '')";

                                                if (empty($sql)) {
                                                    $sql = "SELECT appoint.*
                                            FROM `appoint`
                                            LEFT JOIN `groups` ON appoint.group_id = groups.group_id
                                            WHERE appoint.group_id IS NULL)";
                                                }

                                                $stmt = $conn->prepare($sql);
                                                $stmt->bindParam(':group_name', $selectedGroup);
                                                $stmt->execute();
                                                $filteredData = $stmt->fetchAll();
                                            }
                                            $index = 1;
                                            if (!$filteredData) {
                                                echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                                            } else {
                                                foreach ($filteredData as $appoint) {
                                                    $group_id = ($appoint['group_id']) ? giveGroupById($conn, $appoint['group_id']) : null;
                                        ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $index++; ?></th>
                                                        <!-- <th scope="row"><?php echo $appoint['appoint_id']; ?></th> -->
                                                        <td><?php echo $appoint['title']; ?></td>
                                                        <!-- <td><?php echo ($appoint['description']); ?></td> -->
                                                        <td><?php echo ($appoint['description']); ?></td>
                                                        <td><?php echo ($appoint['appoint_date']); ?></td>
                                                        <td><?php echo $group_id ? $group_id['group_name'] : 'ทุกกลุ่มเรียน'; ?></td>
                                                        <td>
                                                            <a href="editAppoint.php?id=<?php echo $appoint['appoint_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                                                            <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $appoint['appoint_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            }
                                        } elseif (isset($_POST['submitsearch'])) {
                                            $SearchText = $_POST['search'];
                                            $sql = "SELECT * FROM `appoint`
                                            WHERE title LIKE :inputText";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':inputText', $SearchText);
                                            $stmt->execute();
                                            $searchData = $stmt->fetchAll();
                                            $index = 1;
                                            if (!$searchData) {
                                                echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                                            } else {
                                                foreach ($searchData as $appoint) {
                                                    $group_id = ($appoint['group_id']) ? giveGroupById($conn, $appoint['group_id']) : null;
                                                ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $index++; ?></th>
                                                        <!-- <th scope="row"><?php echo $appoint['appoint_id']; ?></th> -->
                                                        <td><?php echo $appoint['title']; ?></td>
                                                        <!-- <td><?php echo ($appoint['description']); ?></td> -->
                                                        <td><?php echo ($appoint['description']); ?></td>
                                                        <td><?php echo ($appoint['appoint_date']); ?></td>
                                                        <td><?php echo $group_id ? $group_id['group_name'] : 'ทุกกลุ่มเรียน'; ?></td>
                                                        <td>
                                                            <a href="editAppoint.php?id=<?php echo $appoint['appoint_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                                                            <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $appoint['appoint_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            }
                                        } elseif (isset($_POST['viewAll'])) {

                                            $stmt = $conn->query("SELECT * FROM `appoint` ORDER BY appoint_date DESC");
                                            $stmt->execute();
                                            $appoints = $stmt->fetchAll();
                                            $index = 1;
                                            if (!$appoints) {
                                                echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                                            } else {
                                                foreach ($appoints as $appoint) {
                                                    $group_id = ($appoint['group_id']) ? giveGroupById($conn, $appoint['group_id']) : null;
                                                ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $index++; ?></th>
                                                        <!-- <th scope="row"><?php echo $appoint['appoint_id']; ?></th> -->
                                                        <td><?php echo $appoint['title']; ?></td>
                                                        <!-- <td><?php echo ($appoint['description']); ?></td> -->
                                                        <td><?php echo ($appoint['description']); ?></td>
                                                        <td><?php echo ($appoint['appoint_date']); ?></td>
                                                        <td><?php echo $group_id ? $group_id['group_name'] : 'ทุกกลุ่มเรียน'; ?></td>
                                                        <td>
                                                            <a href="editAppoint.php?id=<?php echo $appoint['appoint_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                                                            <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $appoint['appoint_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            }
                                        } else {
                                            $stmt = $conn->query("SELECT * FROM `appoint` ORDER BY appoint_date DESC");
                                            $stmt->execute();
                                            $appoints = $stmt->fetchAll();
                                            $index = 1;
                                            if (!$appoints) {
                                                echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                                            } else {
                                                foreach ($appoints as $appoint) {
                                                    $group_id = ($appoint['group_id']) ? giveGroupById($conn, $appoint['group_id']) : null;
                                                ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $index++; ?></th>
                                                        <!-- <th scope="row"><?php echo $appoint['appoint_id']; ?></th> -->
                                                        <td><?php echo $appoint['title']; ?></td>
                                                        <!-- <td><?php echo ($appoint['description']); ?></td> -->
                                                        <td><?php echo ($appoint['description']); ?></td>
                                                        <td><?php echo ($appoint['appoint_date']); ?></td>
                                                        <td><?php echo $group_id ? $group_id['group_name'] : 'ทุกกลุ่มเรียน'; ?></td>
                                                        <td>
                                                            <a href="editAppoint.php?id=<?php echo $appoint['appoint_id']; ?>" class="btn btn-warning mb-1">แก้ไขข้อมูล</a>
                                                            <a onclick="return confirm('Are you sure you want to delete?');" href="?delete=<?php echo $appoint['appoint_id']; ?>" class="btn btn-danger mb-1">ลบข้อมูล</a>
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
                            <form action="./Appointmanage.php" method="POST">
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

<script src="./search_data/searchAppoint.js"></script>




</body>

</html>