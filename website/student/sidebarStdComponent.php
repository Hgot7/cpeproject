<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse shadow-lg ">
  <div class="position-sticky">
    <ul class="nav flex-column">

      <li class="nav-item pb-2">
        <a href="#" class="nav-link text-primary disabled" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-person-workspace h3"></i>
            </div>
            <span class="ps-2 fs-5 fw-bold"><?php echo $_SESSION['student_login']; ?></span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <hr class="sidebar-divider my-0">
      </li>

      <li class="nav-item pb-2">
        <a href="../student/Stdpage.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-house-door h4"></i>
            </div>
            <span class="ps-2">หน้าหลัก</span></span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="../student/profileStd.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-person-lines-fill h4"></i>
            </div>
            <span class="ps-2">โปรไฟล์ของนักศึกษา</span></span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="../student/Stduploadfile.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-file-earmark-arrow-up h4"></i>
            </div>
            <span class="ps-2">อัปโหลดไฟล์</span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="../student/DownloadDocument.php" id="upload" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-file-earmark-arrow-down h4"></i>
            </div>
            <span class="ps-2">ดาวน์โหลดไฟล์เอกสาร</span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="../student/statusproject.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-card-checklist h4"></i>
            </div>
            <span class="ps-2">สถานะโครงงาน</span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="../projectSearchAll.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-search h4"></i>
            </div>
            <span class="ps-2">ค้นหาข้อมูลโครงงาน</span>
          </div>
        </a>
      </li>

      <li class="nav-item pb-2">
        <a href="../logout_Std.php" id="logoutLink" class="nav-link active">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-box-arrow-right h4"></i>
            </div>
            <span class="ps-2">ออกจากระบบ</span>
          </div>
        </a>
      </li>
    </ul>
  </div>

</nav>