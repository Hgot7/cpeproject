// ========================= components admin =========================
class NavBarIndex extends HTMLElement {

  constructor() {
    super();
  }
  connectedCallback() {
    this.innerHTML = `
    <header class="d-flex flex-wrap align-items-center justify-content-start justify-content-md-between py-3 border-bottom container-fluid">
    <a href="index.php" class="d-flex align-items-center text-dark text-decoration-none">
      <svg class="bi me-2" width="10" height="32" role="img" aria-label="Bootstrap">
      </svg>
      <i class="bi bi-book-half pe-2 h4"></i>
      <h2 class="fs-4">Project Management System</h2>
    </a>

    <ul class="nav col col-md-auto mb-2 justify-content-center mb-md-0">
      <li><a href="#" class="nav-link px-2 link-secondary"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
    </ul>

    <div class="col-md-3 pe-5 text-end justify-content-center align-items-center">
    <i class="bi bi-folder-fill h4"></i>
    </div>
  </header>`
  }
}
customElements.define('navbar_index-component', NavBarIndex);
class NavBarAdmin extends HTMLElement {

  constructor() {
    super();
  }
  connectedCallback() {
    this.innerHTML = `
    <header
    class="d-flex flex-wrap align-items-center justify-content-md-between py-3 border-bottom container-fluid">
    <a href="../admin/adminpage.php" class="d-flex align-items-center text-dark text-decoration-none">
      <svg class="bi me-2" width="10" height="32" role="img" aria-label="Bootstrap">
      </svg>
      <i class="bi bi-book-half pe-2 h4"></i>
      <h2 class="fs-4">Project Management System</h2>
    </a>

    <ul class="nav col col-md-auto mb-2 justify-content-center mb-md-0">
      <li><a href="#" class="nav-link px-2 link-secondary"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
    </ul>


    <!-- -----------------------------------------------Profile--------------------------------------- -->
   
      <div class="col-1 text-end">
       <a href="#" class="nav-link disabled" aria-current="page">
                <i class="bi bi-person-workspace h3"></i>
                <span class="px-1 fs-5 fw-bold">Admin</span>
    

  </div>

  
     </header>`
  }
}
customElements.define('navbar_admin-component', NavBarAdmin);
class SideBarAdmin extends HTMLElement {
  constructor() {
    super();
  }
  connectedCallback() {
    this.innerHTML = ` 
    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse shadow-lg ">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item pb-2">
                <a href="#" class="nav-link text-primary disabled" aria-current="page">
                    <i class="bi bi-person-workspace h3"></i>
                    <span class="ps-2 fs-5 fw-bold">Admin</span>
                </a>
            </li>

            <li class="nav-item pb-2">
            <hr class="sidebar-divider my-0">
        </li>

        <li class="nav-item pb-2">
        <a href="../admin/adminpage.php" class="nav-link active" aria-current="page" onClick="toggleSidebar()">
            <div class="d-flex align-items-center">
                <div class="flex-grow">
                    <i class="bi bi-house-door h4"></i>
                </div>
                <span class="ps-2">หน้าหลัก</span>
            </div>
        </a>
    </li>

            <li class="nav-item pb-2">
                <a href="../admin/Reportpage.php" class="nav-link active" aria-current="page" onClick="toggleSidebar()">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow">
                            <i class="bi bi-file-earmark-pdf-fill h4"></i>
                        </div>
                        <span class="ps-2">รายงานสรุป</span>
                    </div>
                </a>
            </li>


            <li class="nav-item pb-2">
            <a href="../admin/editDefaultSystem.php" class="nav-link active" aria-current="page">
                <div class="d-flex align-items-center">
                    <div class="flex-grow">
                        <i class="bi bi-house-gear h4"></i>
                    </div>
                    <span class="ps-2">ค่าพื้นฐานของระบบ</span>
                </div>
            </a>
        </li>

     
        <li class="nav-item pb-2">
        <a href="../admin/groupmanage.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
                <div class="flex-grow">
                    <i class="bi bi-people-fill h4"></i>
                </div>
                <span class="ps-2">จัดการข้อมูลกลุ่มเรียน</span>
            </div>
        </a>
    </li>

   

        <li class="nav-item pb-2">
                <a href="../admin/studentmanage.php" class="nav-link active" aria-current="page">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow">
                            <i class="bi bi-person-fill-gear h4"></i>
                        </div>
                        <span class="ps-2">จัดการข้อมูลนักศึกษา</span>
                    </div>
                </a>
            </li>

            <li class="nav-item pb-2">
            <a href="../admin/teachermanage.php" class="nav-link active" aria-current="page">
                <div class="d-flex align-items-center">
                    <div class="flex-grow">
                        <i class="bi bi-person-lines-fill h4"></i>
                    </div>
                    <span class="ps-2">จัดการข้อมูลผู้ดูแลระบบและอาจารย์</span>
                </div>
            </a>
        </li>

        <li class="nav-item pb-2">
        <a href="../admin/projectmanage.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
                <div class="flex-grow">
                    <i class="bi bi-clipboard-check h4"></i>
                </div>
                <span class="ps-2">จัดการข้อมูลโครงงาน</span>
            </div>
        </a>
    </li>

    <li class="nav-item pb-2">
    <a href="../admin/uploadCSV.php" class="nav-link active" aria-current="page">
        <div class="d-flex align-items-center">
            <div class="flex-grow">
                <i class="bi bi-filetype-csv h4"></i>
            </div>
            <span class="ps-2">อัปโหลดไฟล์ CSV.</span>
        </div>
    </a>
</li>

            
            <li class="nav-item pb-2">
                <a href="../admin/regulationmanage.php" class="nav-link active" aria-current="page">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow">
                        <i class="bi bi-info-circle h4"></i>
                        </div>
                        <span class="ps-2">จัดการข้อมูลกฎข้อบังคับ</span>
                    </div>
                </a>
            </li>

            <li class="nav-item pb-2">
                <a href="../admin/Appointmanage.php" class="nav-link active" aria-current="page">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow">
                            <i class="bi bi-calendar2-event h4"></i>
                        </div>
                        <span class="ps-2">จัดการข้อมูลกำหนดการในรายวิชา</span>
                    </div>
                </a>
            </li>

            

            

            <li class="nav-item pb-2">
                <a href="../admin/documentmanage.php" class="nav-link active" aria-current="page">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow">
                        <i class="bi bi-file-earmark-text h4"></i>
                        </div>
                        <span class="ps-2">จัดการข้อมูลเอกสารในรายวิชา</span>
                    </div>
                </a>
            </li>


            <li class="nav-item pb-2">
                <a href="../admin/Newsmanage.php" class="nav-link active" aria-current="page">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow">
                        <i class="bi bi-newspaper h4"></i>
                        </div>
                        <span class="ps-2">จัดการข้อมูลข่าวสาร</span>
                    </div>
                </a>
            </li>

            

            
            
            <li class="nav-item pb-2">
                <a href="../admin/topicmanage.php" class="nav-link active" aria-current="page">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow">
                            <i class="bi bi-question-circle-fill h4"></i>
                        </div>
                        <span class="ps-2">จัดการข้อมูลหัวข้อประเมินโครงงาน</span>
                    </div>
                </a>
            </li>

            
        <li class="nav-item pb-2">
        <a href="../admin/editEvaluationcriteria.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
                <div class="flex-grow">
                    <i class="bi bi-clipboard h4"></i>
                </div>
                <span class="ps-2">จัดการเกณฑ์การประเมิน</span>
            </div>
        </a>
    </li>

            <li class="nav-item pb-2">
                <a href="../admin/TimeTestmanage.php" class="nav-link active" aria-current="page">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow">
                            <i class="bi bi-calendar2-day h4"></i>
                        </div>
                        <span class="ps-2">จัดการข้อมูลเวลาสอบโครงงาน</span>
                    </div>
                </a>
            </li>

            

            

            <li class="nav-item pb-2">
                <a href="../logout_Admin.php" class="nav-link active" aria-current="page" id="logoutLink">
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
</nav>`;

    // เมื่อมีการ scroll ใน sidebar
    document.querySelector('#sidebar').addEventListener('scroll', function() {
      // บันทึกค่าตำแหน่ง scroll ล่าสุดไว้ใน Local Storage
      localStorage.setItem('sidebarScrollPosition', this.scrollTop);
    });

    // เมื่อหน้าเว็บถูกโหลดใหม่ (event 'load')
    window.addEventListener('load', function() {
      // อ่านค่าตำแหน่ง scroll จาก Local Storage
      const savedScrollPosition = localStorage.getItem('sidebarScrollPosition');

      // ตรวจสอบว่ามีค่า scroll ที่บันทึกไว้หรือไม่
      if (savedScrollPosition !== null) {
        // กำหนดค่าตำแหน่ง scroll ให้กับ sidebar
        document.querySelector('#sidebar').scrollTop = parseInt(savedScrollPosition);
      }
    });
    
    document.querySelector('#logoutLink').addEventListener('click', function() {
      // ลบค่า scroll ใน Local Storage
      localStorage.removeItem('sidebarScrollPosition');
      // สามารถเรียกใช้งานหน้า logout ของคุณได้ที่นี่
    });
  }
}
customElements.define('sidebar_admin-component', SideBarAdmin);

// ========================= API Std =========================// ========================= API Std =========================

// const url_Params = new URLSearchParams(window.location.search);
// const id = url_Params.get('id');
// // รับค่า parameter จาก URL
// var requestOptions = {
//   method: 'GET',
//   redirect: 'follow'
// };
// console.log(id);
// fetch("http://localhost/login/restAPI.php?id=" + id, requestOptions)
//   .then(response => response.text())
//   .then(result => {
//     var jsonObj = JSON.parse(result);
//     for (let data of jsonObj) {
//       if (id == data.student_id) {
//         console.log(data.student_id);
//         sessionStorage.setItem('Name', data.firstname);    //สามารถทำให้ข้อมูลไม่หายไปเมื่อรีเฟรชหน้าเว็บคือใช้ sessionStorage 
//         //เพื่อเก็บข้อมูลในหน่วยความจำเซสชัน (session storage) ซึ่งจะยังคงอยู่หลังจากที่รีเฟรชหน้าเว็บ
//         let Name = sessionStorage.getItem('Name');
//         console.log(Name);
//         document.getElementById('name').innerHTML = Name;
//         break;
//       } else {
//         continue;
//       }
//     }
//   }).catch(error => console.log('error', error));

// window.addEventListener('DOMContentLoaded', function() {
//   // ตรวจสอบว่าหน้าเว็บได้รีเฟรชหรือไม่
//   if (!sessionStorage.getItem('refreshed')) {
//     // ตั้งค่าแบบฟลากให้แสดงว่าหน้าเว็บได้รีเฟรชแล้ว
//     sessionStorage.setItem('refreshed', 'true');

//     // รีเฟรชหน้าเว็บ
//     window.location.reload();
//   } else {
//     // ลบแบบฟลากเพื่อให้รีเฟรชได้ครั้งถัดไป
//     sessionStorage.removeItem('refreshed');

//     // โค้ดที่จะทำงานหลังจากหน้าเว็บโหลดเสร็จ
//     let Name = sessionStorage.getItem('Name');
//     console.log(Name);
//     if (Name) {
//       // แสดงค่าชื่อในหน้าเว็บ
//       document.getElementById('name').innerHTML = Name;
//     } else {
//       console.error("ไม่พบชื่อใน sessionStorage");
//     }
//   }
// });

// ========================= components students =========================
class SideBarStd extends HTMLElement {

  constructor() {
    super();
  }
  connectedCallback() {
    this.innerHTML = `
    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse shadow-lg ">
    <div class="position-sticky">
      <ul class="nav flex-column">

        <li class="nav-item pb-2">
          <a href="#" class="nav-link text-primary disabled" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-person-workspace h3"></i>
              </div>
              <span id="name" class="ps-2 fs-5 fw-bold"></span>
            </div>
          </a>
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
              <span class="ps-2">สถานะโครงงานของฉัน</span>
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
          <a href="../logout_Std.php" id="logout" class="nav-link active">
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
  `
    let Name = sessionStorage.getItem('Name');
    console.log(Name);
    document.getElementById('name').innerHTML = Name;

  }
}
customElements.define('sidebar_student-component', SideBarStd);

class NavBarStd extends HTMLElement {
  constructor() {
    super();
  }
  connectedCallback() {
    this.innerHTML = ` 
      <header
      class="d-flex flex-wrap align-items-center justify-content-start justify-content-md-between py-3 border-bottom container-fluid">
     <a href="../student/Stdpage.php" class="d-flex align-items-center text-dark text-decoration-none">
    <svg class="bi me-2" width="10" height="32" role="img" aria-label="Bootstrap">
    </svg>
    <i class="bi bi-book-half pe-2 h4"></i>
    <h2 class="fs-4">Project Management System</h2>
  </a>
  <ul class="nav col col-md-auto mb-2 justify-content-center mb-md-0">
  <li><a href="#" class="nav-link px-2 link-secondary"> </a></li>
  <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
  <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
  <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
  <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
  <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
  <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
  <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
</ul>

  <!-- -----------------------------------------------Profile--------------------------------------- -->
 
    <div class="col-1 text-end">
     <a href="#" class="nav-link disabled" aria-current="page">
              <i class="bi bi-person-workspace h3"></i>
              <span class="px-1 fs-5 fw-bold">Student</span>
  

</div>


   </header>`
  }
}
customElements.define('navbar_std-component', NavBarStd);


// ========================= components techer =========================
class NavBarTeacher extends HTMLElement {
  constructor() {
    super();
  }
  connectedCallback() {
    this.innerHTML = ` 
    <header
    class="d-flex flex-wrap align-items-center justify-content-md-between py-3 border-bottom container-fluid">
    <a href="./Teacherpage.php" class="d-flex align-items-center text-dark text-decoration-none">
      <svg class="bi me-2" width="10" height="32" role="img" aria-label="Bootstrap">
      </svg>
      <i class="bi bi-book-half pe-2 h4"></i>
      <h2 class="fs-4">Project Management System</h2>
    </a>
  
    <ul class="nav col col-md-auto mb-2 justify-content-center mb-md-0">
      <li><a href="#" class="nav-link px-2 link-secondary"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
      <li><a href="#" class="nav-link px-2 link-dark"> </a></li>
    </ul>
  
  
    <!-- -----------------------------------------------Profile--------------------------------------- -->
   
      <div class="col-1 text-end">
       <a href="#" class="nav-link disabled" aria-current="page">
                <i class="bi bi-person-workspace h3"></i>
                <span class="px-1 fs-5 fw-bold">Teacher</span>
    
  
  </div>
  
  
     </header>`
  }
}
customElements.define('navbar_teacher-component', NavBarTeacher);

class SideBarTeacher extends HTMLElement {
  constructor() {
    super();
  }
  connectedCallback() {
    this.innerHTML = ` 
    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse shadow-lg ">
    <div class="position-sticky">
      <ul class="nav flex-column">
        <a href="#" class="nav-link text-primary disabled" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-person-workspace h3"></i>
              </div>
              <span class="ps-2">อาจารย์</span>
            </div>
          </a>
         
        </li>
       
        <li class="nav-item pb-2">
        <hr class="sidebar-divider my-0">
    </li>

        <li class="nav-item pb-2">
          <a href="../Teacher/Teacherpage.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-house-door h4"></i>
              </div>
              <span class="ps-2">หน้าหลัก</span>
            </div>
          </a>
        </li>
        
        <li class="nav-item pb-2">
        <a href="../teacher/DownloadDocument.php" id="upload" class="nav-link active" aria-current="page">
          <div class="d-flex align-items-center">
            <div class="flex-grow">
              <i class="bi bi-file-earmark-arrow-down h4"></i>
            </div>
            <span class="ps-2">เอกสารในรายวิชา</span>
          </div>
        </a>
      </li>

        <li class="nav-item pb-2">
          <a href="../Teacher/Teacheryourproject.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-clipboard h4"></i>
              </div>
              <span class="ps-2">โครงงานที่รับเป็นที่ปรึกษา</span>
            </div>
          </a>
        </li>

        <li class="nav-item pb-2">
          <a href="../Teacher/viewpointTest.php" class="nav-link active" aria-current="page">
            <div class="d-flex align-items-center">
              <div class="flex-grow">
                <i class="bi bi-person-fill-gear h4"></i>
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
          <a href="../logout_Teacher.php" class="nav-link active" aria-current="page">
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

  </nav>`
  }
}
customElements.define('sidebar_teacher-component', SideBarTeacher);


//---------------------------------------------------------------------------------------------- popup navigator bar
// var slide = document.getElementById("mySidenav");
// function openNav() {

//   slide.style.width = "300px";
// }
// function closeNav() {
//   slide.style.width = "0";


// }
// document.getElementById("mySidenav").addEventListener("click", e => e.stopPropagation(), false)
// document.getElementById("opennav").addEventListener("click", e => e.stopPropagation(), false)
// document.documentElement.addEventListener("click", function (e) {
//   closeNav()
//   // e.preventDefault()
// }, false)
//---------------------------------------------------------------------------------------------- popup navigator bar



