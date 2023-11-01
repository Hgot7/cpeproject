// ignore_for_file: prefer_const_constructors, non_constant_identifier_names

import 'package:flutter/material.dart';
import 'package:flutter_login/studenthome.dart';
import 'dart:async';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'user.dart';

class login extends StatefulWidget {
  const login({Key? key}) : super(key: key);

  @override
  State<login> createState() => _loginState();
}

class _loginState extends State<login> {
  final fromkey = GlobalKey<FormState>();
  TextEditingController email = TextEditingController();
  TextEditingController pass = TextEditingController();

  Future<void> sign_in() async {
    String url =
        "https://www.cpeproject.shop/flutterIN/login.php"; // กำหนด URL ของเซิร์ฟเวอร์ที่รอรับ HTTP POST request เพื่อตรวจสอบการเข้าสู่ระบบ

    final response = await http.post(
      Uri.parse(url),
      headers: {
        "Content-Type": "application/json"
      }, // เพิ่ม header เพื่อระบุว่าคุณกำลังส่ง JSON
      body: jsonEncode({
        'username': email.text,
        'password': pass.text,
        'is_signed_in': (await User.getSignIn()) ?? false,
      }), // โค้ดนี้สร้าง request body โดยแปลงข้อมูลบัญชีผู้ใช้ที่รับมา (email และ password) เป็น JSON และส่งไปใน request body เพื่อให้เซิร์ฟเวอร์ตรวจสอบ
    );

    if (response.statusCode == 200) {
      var data = json.decode(response.body);
      print(data);

      if (data.containsKey("message")) {
        if (data["message"] == "Student Success" &&
            data.containsKey("student_id")) {
          // สำเร็จ: เข้าสู่ระบบนักเรียน
          await User.setSignIn(true);
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(
              builder: (context) => StudentHome(studentId: data['student_id']),
            ),
          ); // นำผู้ใช้ไปยังหน้านักเรียน
        } else if (data["message"] == "Teacher Success" &&
            data.containsKey("teacher_id")) {
          // สำเร็จ: เข้าสู่ระบบครู
          await User.setSignIn(true);
          Navigator.pushNamed(context, 'teacherhome'); // นำผู้ใช้ไปยังหน้าครู
        } else {
          // ไม่สำเร็จหรือข้อความผิดพลาดอื่นๆ
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text("เข้าสู่ระบบไม่สำเร็จ"),
              backgroundColor: Colors.red,
            ),
          );
        }
      } else {
        // ไม่พบคีย์ "message" ใน JSON response
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("การตอบกลับจากเซิร์ฟเวอร์ไม่ถูกต้อง"),
            backgroundColor: Colors.red,
          ),
        );
      }
    } else {
      // ไม่สามารถติดต่อเซิร์ฟเวอร์ได้
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text("เกิดข้อผิดพลาดระหว่างเข้าสู่ระบบ"),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF5F5F5),
      body: Center(
        child: Form(
          key: fromkey,
          child: ListView(
            shrinkWrap: true,
            children: [
              Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: <Widget>[
                  Text(
                    'Project management system',
                    style: TextStyle(fontSize: 30, fontWeight: FontWeight.bold),
                  ),
                  SizedBox(
                    height: 30,
                  ),
                  Image.asset('assets/img/Picture.png'),
                  SizedBox(
                    height: 30,
                  ),
                  SizedBox(
                    width: 350,
                    child: TextFormField(
                      obscureText: false,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(),
                        labelText: 'ID Teacher or Student',
                      ),
                      validator: (val) {
                        if (val!.isEmpty) {
                          return 'Empty';
                        }
                        return null;
                      },
                      controller: email,
                    ),
                  ),
                  SizedBox(
                    height: 20,
                  ),
                  SizedBox(
                    width: 350,
                    child: TextFormField(
                      obscureText: true,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(),
                        labelText: 'Password',
                      ),
                      validator: (val) {
                        if (val!.isEmpty) {
                          return 'Empty';
                        }
                        return null;
                      },
                      controller: pass,
                    ),
                  ),
                  SizedBox(
                    height: 20,
                  ),
                  SizedBox(
                    width: 350,
                    height: 60,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF3F60A0),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(15))),
                      onPressed: () {
                        bool pass = fromkey.currentState!.validate();

                        if (pass) {
                          sign_in();
                        }
                      },
                      child: const Text(
                        'Sign in',
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ), // This trailing comma makes auto-formatting nicer for build methods.
    );
  }
}
