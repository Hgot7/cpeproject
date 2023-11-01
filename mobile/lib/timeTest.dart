import 'package:flutter/material.dart';
import 'package:flutter_login/moreTimeTest.dart';
import 'package:flutter_login/api_service.dart';

class TimeTestScreen extends StatefulWidget {
  @override
  State<TimeTestScreen> createState() => _TimeTestScreenState();
}

class _TimeTestScreenState extends State<TimeTestScreen> {
  List<dynamic> timeTest = [];
  int currentIndex = 0;

  @override
  void initState() {
    super.initState();
    fetchData();
  }

  Future<void> fetchData() async {
    ApiService apiService = ApiService();
    try {
      List<dynamic> data = await apiService.getDataTest();
      setState(() {
        timeTest = data;
      });
    } catch (e) {
      print(e);
    }
  }

  void goToPage(int index) {
    setState(() {
      currentIndex = index;
    });
    if (index >= 0 && index < timeTest.length) {
      String projectID =
          timeTest[index]['project_id']; // ดึง project_id จากข้อมูลที่เลือก
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => MoreTestScreen(
            projectID: projectID, // ส่ง project_id ไปยังหน้า MoreTestScreen
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(body: buildTimeTest());
  }

  Widget buildTimeTest() {
    if (timeTest.isEmpty) {
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
      itemCount: timeTest.length,
      itemBuilder: (context, index) {
        return Padding(
          padding: const EdgeInsets.all(8.0),
          child: Card(
            elevation: 8,
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(32)),
            child: Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  ListTile(
                    title: Text(
                      '${'รหัสโปรเจค'} ${timeTest[index]['project_id']}',
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 18,
                      ),
                    ),
                    subtitle: Text(
                      '${'ห้องสอบ'} ${timeTest[index]['room_number']}',
                      style: const TextStyle(
                        fontSize: 16,
                        color: Colors.black,
                        fontWeight: FontWeight.w400,
                      ),
                    ),
                    trailing: Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        const Text(
                          'วันที่สอบ',
                          style: TextStyle(
                            fontSize: 15,
                            color: Colors.black87,
                          ),
                        ),
                        Text(
                          timeTest[index]['timeTest_date'].toString(),
                          style: const TextStyle(
                            fontSize: 15,
                            color: Colors.black87,
                          ),
                        ),
                      ],
                    ),
                  ),
                  TextButton(
                      onPressed: () {
                        goToPage(index);
                      },
                      child: const Text('Learn More'))
                ],
              ),
            ),
          ),
        );
      },
    );
  }
}
