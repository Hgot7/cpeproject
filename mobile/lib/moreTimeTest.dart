import 'dart:convert';
import 'package:flutter_login/api_service.dart';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';

class MoreTestScreen extends StatefulWidget {
  final String projectID;

  MoreTestScreen({required this.projectID});

  @override
  State<MoreTestScreen> createState() => _MoreTestScreenState();
}

class _MoreTestScreenState extends State<MoreTestScreen> {
  List<Map<String, dynamic>> project = [];
  Map<String, List<dynamic>> studentLists =
      {}; // เก็บรายการของนักเรียนแยกตามโปรเจค
  Map<String, List<dynamic>> teacherLists =
      {}; // เก็บรายการของอาจารย์แยกตามโปรเจค
  List<dynamic> timeTestList = [];
  String role = '';
  TextStyle bigText = const TextStyle(
    fontSize: 20.5,
    color: Colors.black,
    fontWeight: FontWeight.bold, // เพิ่มตัวหนาที่นี่
  );

  @override
  void initState() {
    super.initState();
    fetchProject();
  }

  Future<void> fetchProject() async {
    try {
      final response = await http.post(
        Uri.parse('https://www.cpeproject.shop/flutterIN/project.php'),
        body: {'project_id': widget.projectID},
      );

      if (response.statusCode == 200) {
        final jsonData = json.decode(response.body);

        if (jsonData != null) {
          setState(() {
            project = List<Map<String, dynamic>>.from(jsonData);
          });

          for (var currentProject in project) {
            await fetchStudent(currentProject['student_id1'].toString(),
                currentProject['project_id']);
            await fetchStudent(currentProject['student_id2'].toString(),
                currentProject['project_id']);
            await fetchStudent(currentProject['student_id3'].toString(),
                currentProject['project_id']);
            await fetchTeacher(currentProject['teacher_id1'].toString(),
                currentProject['project_id']);
            await fetchTeacher(currentProject['teacher_id2'].toString(),
                currentProject['project_id']);
            await fetchTeacher(currentProject['referee_id'].toString(),
                currentProject['project_id']);
            await fetchTeacher(currentProject['referee_id1'].toString(),
                currentProject['project_id']);
            await fetchTeacher(currentProject['referee_id2'].toString(),
                currentProject['project_id']);
          }

          fetchData();
        } else {
          throw Exception('ไม่มีข้อมูลที่ได้จาก API');
        }
      } else {
        throw Exception('เกิดข้อผิดพลาดในการเรียก API');
      }
    } catch (e) {
      print('Error: $e');
    }
  }

  Future<void> fetchStudent(var studentID, var projectID) async {
    try {
      final response2 = await http.post(
        Uri.parse('https://www.cpeproject.shop/flutterIN/student.php'),
        body: {'student_id': studentID},
      );

      if (response2.statusCode == 200) {
        final jsonData2 = json.decode(response2.body);
        if (jsonData2 != null) {
          // สร้างรายการของนักเรียนตัวเอง
          List<dynamic> studentDataList = jsonData2;

          // เก็บรายการของนักเรียนในแต่ละโปรเจค
          setState(() {
            if (studentLists.containsKey(projectID)) {
              studentLists[projectID]!.addAll(studentDataList);
            } else {
              studentLists[projectID] = List<dynamic>.from(studentDataList);
            }
          });
        } else {
          throw Exception('เกิดข้อผิดพลาดในการเรียก API สำหรับนักเรียน');
        }
      } else {
        throw Exception('เกิดข้อผิดพลาดในการเรียก API สำหรับนักเรียน');
      }
    } catch (e) {
      print('Error: $e');
    }
  }

  Future<void> fetchTeacher(var teacherID, var projectID) async {
    try {
      final response3 = await http.post(
        Uri.parse('https://www.cpeproject.shop/flutterIN/teacher.php'),
        body: {'teacher_id': teacherID},
      );

      if (response3.statusCode == 200) {
        final jsonData3 = json.decode(response3.body);

        // สร้างรายการของอาจารย์ตัวเอง
        List<dynamic> teacherDataList = jsonData3;

        // เก็บรายการของอาจารย์ในแต่ละโปรเจค
        setState(() {
          if (teacherLists.containsKey(projectID)) {
            teacherLists[projectID]!.addAll(teacherDataList);
          } else {
            teacherLists[projectID] = List<dynamic>.from(teacherDataList);
          }

          // เพิ่มค่า null ถ้าไม่มีข้อมูลอาจารย์
          if (teacherDataList.isEmpty) {
            teacherLists[projectID]?.add(null);
          }
        });
      } else {
        throw Exception('เกิดข้อผิดพลาดในการเรียก API สำหรับอาจารย์');
      }
    } catch (e) {
      print('Error: $e');
    }
  }

// ตัวอย่างการแสดงข้อมูลนักเรียนในแต่ละโปรเจค
  Widget buildStudentList(String projectID) {
    List<dynamic> students = studentLists[projectID] ?? [];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        for (var i = 0; i < students.length; i++)
          if (students[i] != null)
            Text(
              'นักศึกษา ${i + 1}: ${students[i]['firstname']}',
              style: bigText,
            ),
      ],
    );
  }

  Widget buildTeacherList(String projectID) {
    List<Widget> teacherWidgets = [];
    List<dynamic> teachers = teacherLists[projectID] ?? [];

    int refereeCount = 0;

    for (var i = 0; i < teachers.length; i++) {
      String role;

      if (teachers[i] != null) {
        if (i == 0) {
          role = 'อาจารย์ที่ปรึกษาหลัก';
        } else if (i == 1) {
          role = 'อาจารย์ที่ปรึกษารอง';
        } else if (i == 2) {
          role = 'ประธานกรรมการ';
        } else {
          refereeCount++;
          role = 'กรรมการ ${refereeCount}';
        }

        teacherWidgets.add(
          Text(
            '$role: ${giveTeacherPosition(teachers[i]['position'])} ${teachers[i]['firstname']} ',
            style: bigText,
          ),
        );
      } else {
        // แสดง role เมื่อข้อมูลอาจารย์เป็น null
        if (i == 0) {
          role = 'อาจารย์ที่ปรึกษาหลัก';
        } else if (i == 1) {
          role = 'อาจารย์ที่ปรึกษารอง';
        } else if (i == 2) {
          role = 'ประธานกรรมการ ${refereeCount}';
        } else {
          refereeCount++;
          role = 'กรรมการ ${refereeCount}';
        }

        teacherWidgets.add(
          Text(
            '$role: ',
            style: bigText,
          ),
        );
      }
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: teacherWidgets,
    );
  }

  Future<void> fetchData() async {
    ApiService apiService = ApiService();
    try {
      List<dynamic> data = await apiService.getDataTest();
      setState(() {
        timeTestList = data;
      });
    } catch (e) {
      print(e);
    }
  }

  String giveTeacherPosition(String position) {
    switch (position) {
      case "ศาสตราจารย์":
        return "ศ.";
      case "ศาสตราจารย์ ดร.":
        return "ศ.ดร.";
      case "รองศาสตราจารย์":
        return "รศ.";
      case "รองศาสตราจารย์ ดร.":
        return "รศ.ดร.";
      case "ผู้ช่วยศาสตราจารย์":
        return "ผศ.";
      case "ผู้ช่วยศาสตราจารย์ ดร.":
        return "ผศ.ดร.";
      case "อาจารย์":
        return "อ.";
      case "อาจารย์ ดร.":
        return "อ.ดร.";
      case "ดร.":
        return "ดร.";
      default:
        return position;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('ข้อมูลโปรเจค'),
        backgroundColor: Colors.black,
      ),
      body: ListView(
        children: [
          for (var currentProject in project)
            Container(
              margin: const EdgeInsets.all(16.0),
              padding: const EdgeInsets.all(16.0),
              decoration: BoxDecoration(
                border: Border.all(color: Colors.black),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'รหัสโปรเจคที่สอบ: ${currentProject['project_id']}',
                    style: bigText,
                  ),
                  Text(
                    'ชื่อโปรเจคที่สอบ: ${currentProject['project_nameTH']}',
                    style: bigText,
                  ),
                  buildStudentList(currentProject['project_id']),
                  buildTeacherList(currentProject['project_id']),
                  for (var timeTest in timeTestList)
                    if (timeTest['project_id'] == currentProject['project_id'])
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'วันที่สอบ: ${timeTest['timeTest_date']}',
                            style: bigText,
                          ),
                          Text(
                            'เริ่มสอบ: ${timeTest['start_time']}',
                            style: bigText,
                          ),
                          Text(
                            'หมดเวลาสอบ: ${timeTest['stop_time']}',
                            style: bigText,
                          ),
                          Text(
                            'ห้องสอบ: ${timeTest['room_number']}',
                            style: bigText,
                          ),
                        ],
                      ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
