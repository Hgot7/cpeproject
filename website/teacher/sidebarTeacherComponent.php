<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse shadow-lg ">
    <div class="position-sticky">
      <ul class="nav flex-column">
        <a href="#" class="nav-link text-primary disabled" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-person-workspace h3"></i>
              </div>
              <span class="ps-2 fs-5 fw-bold"><?php echo $_SESSION['teacher_login'];?></span>
            </div>
          </a>
        </li>
       
        <li class="nav-item pb-2">
        <hr class="sidebar-divider my-0">
    </li>

        <li class="nav-item pb-2">
          <a href="../teacher/Teacherpage.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-house-door h4"></i>
              </div>
              <span class="ps-2">หน้าหลัก</span>
            </div>
          </a>
        </li>

        <li class="nav-item pb-2">
        <a href="../teacher/profileTeacher.php" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-person-lines-fill h4"></i>
            </div>
            <span class="ps-2">โปรไฟล์ของอาจารย์</span></span>
          </div>
        </a>
      </li>

        
        <li class="nav-item pb-2">
        <a href="../teacher/DownloadDocument.php" id="upload" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-file-earmark-text h4"></i>
            </div>
            <span class="ps-2">เอกสารในรายวิชา</span>
          </div>
        </a>
      </li>

        <li class="nav-item pb-2">
          <a href="../teacher/Teacheryourproject.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-clipboard h4"></i>
              </div>
              <span class="ps-2">โครงงานที่รับเป็นที่ปรึกษา</span>
            </div>
          </a>
        </li>

        <li class="nav-item pb-2">
          <a href="../teacher/viewpointTest.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-clipboard2-check h4"></i>
              </div>
              <span class="ps-2">ประเมินโครงงาน</span>
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
          <a href="../logout_Teacher.php" class="nav-link active" aria-current="page" id="logoutLink">
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