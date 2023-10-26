<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Link to custom Bootstrap CSS -->
  <link rel="stylesheet" href="./css/custom.css">
  <script src="./component.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

  <!-- Link to icon -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">

  <title>indexpage</title>

</head>

<body>
  <!-- -------------------------------------------------Header------------------------------------------------- -->
  <div class="HeaderBg">
    <div class="container">
      <navbar_index-component></navbar_index-component>
    </div>
  </div>
  <!-- -------------------------------------------------Materialเนื้อหา----------------------------------------------- -->
  <div class="container-fluid" id="hanging-icons">
    <div class="container px-4">
      <div class="row g-4 py-5 row-cols-2 row-cols-lg-2">
        <div class="col">
          

          <h3 class="fs-1 pb-2 border-bottom">รายละเอียด</h3>
          <div class="card shadow my-3" id="cardrule">
            <div class="card-body">
              <div class="col d-flex align-items-start">
                <div class="icon-square d-inline-flex align-items-start justify-content-center fs-1 flex-shrink-0 me-3">
                  <i class="bi bi-newspaper" width="1em" height="1em"></i>
                </div>
                <div>
                  <h3 class="fs-3 text-body-light">ข้อบังคับรายวิชาโครงงาน</h3>
                  <p>Click here เพื่ออ่านรายละเอียดเพิ่มเติม</p>
                  <a href="#" class="btn btn-primary">
                    Click Here
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-3" id="cardappoint">
            <div class="card-body">
              <div class="col d-flex align-items-start">
                <div class="icon-square d-inline-flex align-items-start justify-content-center fs-1 flex-shrink-0 me-3">
                  <i class="bi bi-flag-fill" width="1em" height="1em"></i>
                </div>
                <div>
              
                  <h3 class="fs-3 text-body-light">กำหนดการในรายวิชา</h3>
                  <p>Click here เพื่ออ่านรายละเอียดเพิ่มเติม</p>
                  <a href="#" class="btn btn-primary">
                    Click Here
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow" id="carddocument">
            <div class="card-body">
              <div class="col d-flex align-items-start">
                <div class="icon-square d-inline-flex align-items-start justify-content-center fs-1 flex-shrink-0 me-3">
                  <i class="bi bi-journal-text" width="1em" height="1em"></i>
                </div>
                <div>
                  <h3 class="fs-3 text-body-light">เอกสารในรายวิชา</h3>
                  <p>Click here เพื่ออ่านรายละเอียดเพิ่มเติม</p>
                  <a href="#" class="btn btn-primary">
                    Click Here
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card shadow h-100" id="cardlogin">
            <div class="card-body d-flex justify-content-center align-items-center">
              <div class="text-white text-center">
                <h3 class="fs-1">Project Management System</h3>
                <p class="fs-2 text-white">Computer Engineering</p>
              </div>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" id="cardlogindetail">
              <div>
                <h3 class="fs-1">Welcome Page</h3>
                <p class="fs-2">Login to continue access</p>
                <a href="loginpage.php">
                  <button type="button" class="btn btn-outline-primary me-2 fs-2">Login</button>
                </a>
              </div>
            </div>
          </div>
        </div>


        <div class="card shadow h-100 w-100" id="cardlogin">
          <div class="card-body align-items-start">
            <div class="text-white">
              <h3 class="fs-1">News ข่าวสาร</h3>
              <p class="fs-2 text-white">Attention, please. Attention please.</p>
              <a href="loginpage.php">
                <button type="button" class="btn btn-outline-primary me-2 fs-4">อ่านเพิ่มเติม</button>
              </a>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>

  <!-- -------------------------------------------------footers------------------------------------------------- -->
  <div class="container">
    <footer class="py-3 my-4">
      <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item"><a href="/index.html" class="nav-link px-2 text-muted">หน้าหลัก</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">โครงงาน</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">เอกสาร</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">ติดต่อ</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">เกี่ยวกับ</a></li>
      </ul>
      <p class="text-center text-muted">© 2023 Computer</p>
    </footer>
  </div>

</body>

</html>