import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  final String baseUrl =
      "https://www.cpeproject.shop/flutterIN/"; // เพิ่ม URL หลักของ API

  Future<List<dynamic>> fetchData(String endpoint) async {
    final url =
        Uri.parse("$baseUrl/$endpoint"); //endpoint ซึ่งเป็นส่วนท้ายของ URL
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        List<dynamic> data = json.decode(utf8.decode(response.bodyBytes));
        return data; //API สำเร็จ โค้ดจะแปลงข้อมูล JSON จาก response body และส่งข้อมูลกลับเป็น List<dynamic>.
      } else {
        throw Exception("Failed to load data");
      }
    } catch (e) {
      //Exception
      throw Exception("Error: $e");
    }
  }

//เรียกใช้งาน fetchData() โดยระบุ endpoint ที่เฉพาะเจาะจงสำหรับแต่ละ API ที่ต้องการเรียก
  Future<List<dynamic>> getNewsData() async {
    return fetchData("news.php");
  }

  Future<List<dynamic>> getRulesData() async {
    return fetchData("regulation.php");
  }

  Future<List<dynamic>> getappointT() async {
    return fetchData("appointT.php");
  }

  Future<List<dynamic>> getDataTest() async {
    return fetchData("timeTest.php");
  }

  Future<List<dynamic>> getDocumentData() async {
    return fetchData("regulation.php");
  }
}
