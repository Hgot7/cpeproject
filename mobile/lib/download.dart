import 'package:flutter/material.dart';
import 'package:flutter_login/api_service.dart';
import 'package:flutter_pdfview/flutter_pdfview.dart';

class Downlond extends StatefulWidget {
  @override
  State<Downlond> createState() => _DownlondState();
}

class _DownlondState extends State<Downlond> {
  List<dynamic> document = [];
  int documentNumber = 0; // เริ่มต้นที่กฎที่ 1

  Future<void> fetchDocumentData() async {
    ApiService apiService = ApiService();
    try {
      List<dynamic> data = await apiService.getDocumentData();
      setState(() {
        document = data;
      });
    } catch (e) {
      print(e);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('เอกสารในรายวิชาโครงงาน'),
        backgroundColor: Colors.black,
      ),
      body: document.isEmpty
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
                itemCount: document.length,
                itemBuilder: (context, index) {
                  documentNumber++; // เพิ่มค่าตัวแปรนับทุกครั้งที่สร้างรายการใหม่
                  return Card(
                    elevation: 4,
                    margin: const EdgeInsets.all(8),
                    child: ListTile(
                      title: Text(
                        'ลำดับที่ $documentNumber', // ใช้ตัวแปรนับเป็นเลขกฎ
                        style: const TextStyle(
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
                                    filePath: document[index]['document_path'],
                                  ),
                                ),
                              );
                            },
                            child: Text(
                              '${document[index]['document_path'] ?? ''}', // ถ้า 'regulationFile_path' เป็น null ให้แสดง ' '
                              style: const TextStyle(
                                fontSize: 17,
                                color: Colors.blue,
                                fontWeight: FontWeight.w400,
                                decoration: TextDecoration.underline,
                              ),
                            ),
                          ),
                          Text(
                            '${document[index]['document_name']}',
                            style: const TextStyle(
                              fontSize: 17,
                              color: Colors.black,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ],
                      ),
                      trailing: Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text(
                            'ปีการศึกษา: ${document[index]['year']}',
                            style: TextStyle(
                              fontSize: 15,
                              color: Colors.black87,
                            ),
                          ),
                          Text(
                            'ภาคการศึกษา: ${document[index]['term']}',
                            style: TextStyle(
                              fontSize: 15,
                              color: Colors.black87,
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
