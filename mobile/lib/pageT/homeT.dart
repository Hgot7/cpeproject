import 'package:flutter/material.dart';
import 'package:flutter_login/pageT/My_button.dart';
import 'package:flutter_login/news.dart';
import 'package:flutter_login/pageT/appoint.dart';
import 'package:flutter_login/api_service.dart';
import 'package:intl/intl.dart';

class homeT extends StatefulWidget {
  final String? teacherId;

  homeT({this.teacherId});
  @override
  State<homeT> createState() => _homeTState();
}

class _homeTState extends State<homeT> {
  List<dynamic> appointT = [];
  int currentIndex = 0;

  @override
  void initState() {
    super.initState();
    fetchappointT();
  }

  Future<void> fetchappointT() async {
    ApiService apiService = ApiService();
    try {
      List<dynamic> data = await apiService.getappointT();
      setState(() {
        appointT = data;
      });
    } catch (e) {
      print(e);
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
      backgroundColor: Colors.white,
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
                  )
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
              // ใช้ Expanded เพื่อให้ ListView สามารถขยายตามข้อมูลได้
              child: buildAppointList(), // แสดงรายการกำหนดการ
            ),
          ],
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        backgroundColor: Colors.black,
        selectedItemColor: Color.fromARGB(255, 74, 147, 231),
        unselectedItemColor: Color.fromARGB(255, 74, 147, 231),
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

  Widget buildAppointList() {
    if (appointT.isEmpty) {
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
      itemCount: appointT.length,
      itemBuilder: (context, index) {
        final dateString = appointT[index]['appoint_date'];
        final appointmentDate = DateTime.parse(dateString);
        final formattedDate =
            DateFormat('dd-MM-yyyy HH:mm').format(appointmentDate);

        return Card(
          elevation: 4,
          margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
          child: ListTile(
            title: Text(
              '${appointT[index]['title']}',
              style: const TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 18,
              ),
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '${appointT[index]['description']}',
                  style: const TextStyle(
                    fontSize: 16,
                    color: Colors.black,
                    fontWeight: FontWeight.w400,
                  ),
                ),
                Text(
                  'กลุ่มเรียน: ${appointT[index]['group_id'] ?? 'ทุกกลุ่มเรียน'}',
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
