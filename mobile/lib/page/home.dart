import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';
import 'package:flutter_login/pageT/My_button.dart';
import 'package:flutter_login/news.dart';
import 'package:flutter_login/pageT/appoint.dart';
import 'package:intl/intl.dart';

class Home extends StatefulWidget {
  final String? studentId;

  Home({this.studentId});

  @override
  State<Home> createState() => _HomeState();
}

class _HomeState extends State<Home> {
  List<dynamic> appointments = [];
  List<dynamic> student = [];
  List<dynamic> matchingAppointments = [];

  int currentIndex = 0;

  @override
  void initState() {
    super.initState();
    if (widget.studentId != null) {
      fetchAppointments();
      fetchStudent();
    }
  }

  Future<void> fetchAppointments() async {
    try {
      final response = await http.post(
        Uri.parse('https://www.cpeproject.shop/flutterIN/appointS.php'),
        body: {'student_id': widget.studentId!},
      );

      if (response.statusCode == 200) {
        final jsonData = json.decode(response.body);

        if (jsonData != null) {
          setState(() {
            appointments = jsonData;
            checkMatchingAppointments();
          });
        } else {
          throw Exception('ไม่มีข้อมูลที่ได้จาก API');
        }
      } else {
        throw Exception('เกิดข้อผิดพลาดในการเรียก API');
      }
    } catch (e) {
      print('Error: $e');
      // จัดการกรณีที่เกิดข้อผิดพลาดในการเรียก API ที่นี่
    }
  }

  Future<void> fetchStudent() async {
    try {
      final response2 = await http.post(
        Uri.parse('https://www.cpeproject.shop/flutterIN/student.php'),
        body: {'student_id': widget.studentId!},
      );

      if (response2.statusCode == 200) {
        final jsonData2 = json.decode(response2.body);
        if (jsonData2 != null) {
          setState(() {
            student = jsonData2;
            checkMatchingAppointments();
          });
        } else {
          throw Exception('เกิดข้อผิดพลาดในการเรียก API สำหรับนักเรียน');
        }
      }
    } catch (e) {
      print('Error: $e');
      // จัดการกรณีที่เกิดข้อผิดพลาดในการเรียก API ที่นี่
    }
  }

  void checkMatchingAppointments() {
    // ตรวจสอบว่า 'group_id' ใน appointments และ student มีค่าเหมือนกัน
    matchingAppointments.clear(); // เคลียร์รายการที่ตรงกันก่อนหน้านี้
    for (var appointment in appointments) {
      if (appointment['group_id'] == null) {
        matchingAppointments.add(appointment);
        continue; // ข้ามการตรวจสอบกับนักเรียนในกลุ่มเดียวกัน
      }
      for (var studentGroup in student) {
        if (appointment['group_id'] == studentGroup['group_id']) {
          matchingAppointments.add(appointment);
          break; // ไม่ต้องตรวจสอบกับนักเรียนอื่นในกลุ่มเดียวกัน
        }
      }
    }
  }

  void goToPage(int index) {
    setState(() {
      currentIndex = index;
    });

    if (index == 0) {
      Navigator.push(
        context,
        MaterialPageRoute(builder: (context) => News()),
      );
    } else if (index == 1) {
      Navigator.push(
        context,
        MaterialPageRoute(builder: (context) => Appoint()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Column(
          children: [
            const Padding(
              padding: EdgeInsets.all(5.0),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  MyButton(
                    iconImagePath: 'assets/img/filedownlond.png',
                    buttonText: 'ดาวน์โหลด',
                    nextPageRoute: 'download',
                  ),
                  MyButton(
                    iconImagePath: 'assets/img/rules.png',
                    buttonText: 'กฎข้อบังคับ',
                    nextPageRoute: 'rules',
                  ),
                ],
              ),
            ),
            const Padding(
              padding: EdgeInsets.symmetric(vertical: 8.0),
              child: Text(
                'กำหนดการในรายวิชาโครงงาน',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Colors.black,
                ),
              ),
            ),
            Expanded(
              child: buildAppointmentsList(),
            ),
          ],
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        backgroundColor: Colors.black,
        selectedItemColor: Colors.white,
        unselectedItemColor: Colors.white,
        currentIndex: currentIndex,
        onTap: goToPage,
        items: const [
          BottomNavigationBarItem(
            icon: Icon(
              Icons.newspaper,
            ),
            label: 'ข่าวสาร',
          ),
          BottomNavigationBarItem(
            icon: Icon(
              Icons.calendar_month,
            ),
            label: 'กำหนดการ',
          ),
        ],
      ),
    );
  }

  Widget buildAppointmentsList() {
    if (matchingAppointments.isEmpty) {
      return const Center(
        child: Text(
          'ไม่มีข้อมูล',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.normal,
            color: Colors.black,
          ),
        ),
      );
    }

    return ListView.builder(
      itemCount: matchingAppointments.length,
      itemBuilder: (context, index) {
        final dateString = matchingAppointments[index]['appoint_date'];
        final appointmentDate = DateTime.parse(dateString);
        final formattedDate =
            DateFormat('dd-MM-yyyy HH:mm').format(appointmentDate);

        return Card(
          elevation: 4,
          margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
          child: ListTile(
            title: Text(
              '${matchingAppointments[index]['title']}',
              style: const TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 17.5,
              ),
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '${matchingAppointments[index]['description']}',
                  style: const TextStyle(
                    fontSize: 16,
                    color: Colors.black,
                    fontWeight: FontWeight.w400,
                  ),
                ),
                Text(
                  'กลุ่มเรียน: ${matchingAppointments[index]['group_id'] ?? 'ทุกกลุ่มเรียน'}',
                  style: const TextStyle(
                    fontSize: 16,
                    color: Colors.black87,
                    fontWeight: FontWeight.w400,
                  ),
                ),
              ],
            ),
            trailing: Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                const Text(
                  'วันเวลาที่สิ้นสุด',
                  style: TextStyle(
                    fontSize: 16,
                    color: Colors.black87,
                    fontWeight: FontWeight.w400,
                  ),
                ),
                Text(
                  formattedDate,
                  style: const TextStyle(
                    fontSize: 15,
                    color: Colors.black87,
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
