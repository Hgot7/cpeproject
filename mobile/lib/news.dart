import 'package:flutter/material.dart';
import 'package:intl/intl.dart'; // เพิ่มการนำเข้า DateFormat
import 'package:flutter_login/api_service.dart';

class News extends StatefulWidget {
  @override
  State<News> createState() => _NewsState();
}

class _NewsState extends State<News> {
  List<dynamic> newsList = [];

  @override
  void initState() {
    super.initState();
    fetchNewsData();
  }

  Future<void> fetchNewsData() async {
    ApiService apiService = ApiService();
    try {
      List<dynamic> data = await apiService.getNewsData();
      setState(() {
        newsList = data;
      });
    } catch (e) {
      print(e);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('ข่าวสาร'),
        backgroundColor: Colors.black,
      ),
      body: newsList.isEmpty
          ? const Center(
              child: Text(
                'ไม่มีข้อมูล',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.normal,
                  color: Colors.black,
                ),
              ),
            )
          : Padding(
              padding: const EdgeInsets.all(8.0),
              child: ListView.builder(
                itemCount: newsList.length,
                itemBuilder: (context, index) {
                  final newsDate = DateTime.parse(newsList[index]['news_date']);
                  final formattedDate =
                      DateFormat('dd-MM-yyyy HH:mm').format(newsDate);

                  return Card(
                    elevation: 4, // เพิ่มเงาให้กับ Card
                    margin: const EdgeInsets.all(11), // ปรับขอบรอบ Card
                    child: ListTile(
                      title: Text(
                        '${newsList[index]['news_head']}',
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 20,
                          color: Colors.black, // เพิ่มสีให้กับ Text
                        ),
                      ),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            newsList[index]['news_text'],
                            style: const TextStyle(
                              fontSize: 17,
                              color: Colors.black87,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            'วันเวลาที่ลงข่าว: $formattedDate',
                            style: const TextStyle(
                              fontSize: 18,
                              color: Colors.black87,
                              fontWeight: FontWeight.w300,
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
    );
  }
}
