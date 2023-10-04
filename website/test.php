<div class="accordion" id="ProjectDocuments">
    <div class="accordion-item">
        <?php
        $matchingIndices = findFilesByChapter($files, 1);
        if (!empty($matchingIndices)) {
            $i = 0;
            foreach ($matchingIndices as $fileIndex) {
                $i++;
                $currentFile = $files[$fileIndex];
                $filePath = $currentFile['file_path'];
                $fileId = $currentFile['file_id'];
                $fileStatus = $currentFile['file_status'];

                $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                $stmt->execute();
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
                <h2 class="accordion-header" id="panelsStayOpen-headingCover">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseCover" aria-expanded="false" aria-controls="panelsStayOpen-collapseCover">
                        <div class="row">
                            <div class="ms-3 col-auto me-auto">หน้าปก</div>
                            <div class="ms-3 col-auto me-auto"> </div>
                            <?php if ($fileStatus == 1) : ?>
                                <div class="col-auto">
                                    <div class="text-end">
                                        <i class="bi bi-circle-fill text-success"></i>
                                        <i>เอกสารผ่านการอนุมัติ</i>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="col-auto">
                                    <div class="text-end">
                                        <i class="bi bi-circle-fill text-warning"></i>
                                        <i>รอการอนุมัติเอกสาร</i>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </button>
                </h2>

                <div id="panelsStayOpen-collapseCover" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingCover">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label for="formFileMultiple" class="text-danger">อัปโหลดไฟล์ *เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label>
                            <div class="col-12">
                                <form class="d-flex" method="post" action="Stduploadfile2.php?id=<?php echo $_SESSION['student_id']; ?>" enctype="multipart/form-data">
                                    <input class="form-control" type="file" id="formFile" name="file_path" accept=".pdf">
                                    <input type="hidden" name="file_chapter" value="1">
                                    <button onclick="return confirm('Are you sure you want to Upload File (ไฟล์หน้าปก)?');" type="submit" class="btn btn-primary ms-3 ms-3">Upload</button>
                                </form>

                                <div class="mt-3">
                                    <?php if ($fileStatus == 1) : ?>
                                        <i class="bi bi-check-lg h4 text-danger"></i>
                                    <?php endif; ?>
                                    <a><?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?></a>
                                    <a href="<?php echo './fileUpload/' . $filePath; ?>" target="_blank">
                                        <?php echo $filePath; ?>
                                    </a>

                                    <a onclick="return confirm('Are you sure you want to delete File (ไฟล์หน้าปก)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>

                                    <button id="toggleAccordion" class="btn btn-info text-white">comment</button>

                                    <div id="hiddenContent">
                                        <div class="card shadow-sm mt-3">
                                            <h5 class="card-header">
                                                <div class="row">
                                                    <div class="col-md3">
                                                        <form class="input-group flex-nowrap" method="post" action="commentStd.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                            <span class="input-group-text">Comments</span>
                                                            <input class="form-control" type="text" id="comment" name="comment" placeholder="Type your message here">
                                                            <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">Send</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </h5>
                                            <div class="card-body">
                                                <?php foreach ($comments as $comment) : ?>
                                                    <div class="row">
                                                        <div class="col-1 message-box ms-3">
                                                            <i class="bi bi-person-circle h4"></i>
                                                        </div>
                                                        <div class="col-9">
                                                            <div class="row">

                                                                <?php
                                                                $authorName = "";
                                                                $commentTime = $comment['comment_time'];

                                                                if (!empty($comment['student_id'])) {
                                                                    $Student = getStudentById($conn, $comment['student_id']);
                                                                    $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                } elseif (!empty($comment['teacher_id'])) {
                                                                    $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                    $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                }
                                                                ?>
                                                                <p class="text-muted">
                                                                    <?php echo $authorName; ?>
                                                                    <i class="float-end"><?php echo $commentTime; ?></i>
                                                                </p>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-1 message-box ms-3"></div>
                                                        <div class="col-9">
                                                            <div class="row">
                                                                <div class="mb-3" tabindex="-1">
                                                                    <p><?php echo $comment['comment']; ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php
                        }
                    } else { ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingCover">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseCover" aria-expanded="false" aria-controls="panelsStayOpen-collapseCover">
                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">หน้าปก</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                    </div>
                                </button>
                            </h2>

                            <div id="panelsStayOpen-collapseCover" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingCover">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label for="formFileMultiple" class="text-danger">อัปโหลดไฟล์ *เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label>
                                        <div class="col-12">
                                            <form class="d-flex" method="post" action="Stduploadfile2.php?id=<?php echo $_SESSION['student_id']; ?>" enctype="multipart/form-data">
                                                <input class="form-control" type="file" id="formFile" name="file_path" accept=".pdf">
                                                <input type="hidden" name="file_chapter" value="1">
                                                <button onclick="return confirm('Are you sure you want to Upload File (ไฟล์หน้าปก)?');" type="submit" class="btn btn-primary ms-3 ms-3">Upload</button>
                                            </form>
                                        <?php
                                    }
                                        ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>