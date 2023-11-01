import 'package:flutter/material.dart';
import 'package:flutter_login/api_service.dart';
import 'package:flutter_pdfview/flutter_pdfview.dart';

class Rules extends StatefulWidget {
  @override
  State<Rules> createState() => _RulesState();
}

class _RulesState extends State<Rules> {
  List<dynamic> rulesList = [];
  int ruleNumber = 0; // เริ่มต้นที่กฎที่ 1

  @override
  void initState() {
    super.initState();
    fetchRulesData();
  }

  Future<void> fetchRulesData() async {
    ApiService apiService = ApiService();
    try {
      List<dynamic> data = await apiService.getDocumentData();
      setState(() {
        rulesList = data;
      });
    } catch (e) {
      print(e);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('กฎข้อบังคับ'),
        backgroundColor: Colors.black,
      ),
      body: rulesList.isEmpty
          ? Center(
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
                itemCount: rulesList.length,
                itemBuilder: (context, index) {
                  ruleNumber++; // เพิ่มค่าตัวแปรนับทุกครั้งที่สร้างรายการใหม่
                  return Card(
                    elevation: 4,
                    margin: EdgeInsets.all(8),
                    child: ListTile(
                      title: Text(
                        'กฎที่ $ruleNumber', // ใช้ตัวแปรนับเป็นเลขกฎ
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 18,
                        ),
                      ),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          InkWell(
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => PDFView(
                                    filePath: rulesList[index]
                                        ['regulationFile_path'],
                                  ),
                                ),
                              );
                            },
                            child: Text(
                              '${rulesList[index]['regulationFile_path'] ?? ''}', // ถ้า 'regulationFile_path' เป็น null ให้แสดง ' '
                              style: TextStyle(
                                fontSize: 17,
                                color: Colors.blue,
                                fontWeight: FontWeight.w400,
                                decoration: TextDecoration.underline,
                              ),
                            ),
                          ),
                          Text(
                            '${rulesList[index]['regulation_text']}',
                            style: TextStyle(
                              fontSize: 17,
                              color: Colors.black,
                              fontWeight: FontWeight.w400,
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
