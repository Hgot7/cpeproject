<?php 
require_once('connect.php');

header("Access-Control-Allow-Origin: *");
header('Content-Type: text/html; charset=utf-8');
header('content-type: application/json; charset=utf-8');

if($_SERVER['REQUEST_METHOD'] == "GET"){
    // อ่านที่ล่ะตัว
    $data = array();
    $sql = $conn->prepare("SELECT * FROM student");
    $sql->execute(); 
    if(isset($sql)){
    foreach($sql as $row){
        $datas = array(
            'student_id' => $row['student_id'],
            'student_password' => $row['student_password'],
            'firstname' => $row['firstname'],
            'lastname' => $row['lastname'],
            'year' => $row['year'],
            'term' => $row['term'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'group_id' => $row['group_id'],
        );
        array_push($data,$datas);
    }
    http_response_code(200);
    echo json_encode($data);
}else{
    $response = [
        'status' => false,
        'msssage' => 'error',
    ];
    http_response_code(404);
    echo json_encode($response);
   }
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    echo 'This is POST';
}else{
    http_response_code(405);
}
?>