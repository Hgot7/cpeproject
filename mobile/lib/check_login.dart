import 'package:flutter/material.dart';
import 'user.dart';

class check_login extends StatefulWidget {
  const check_login({Key? key}) : super(key: key);

  @override
  State<check_login> createState() => _check_loginState();
}

class _check_loginState extends State<check_login> {
  Future checklogin() async {
    bool? signIn =
        await User.getSignIn(); // ส่งคำขอ API เพื่อตรวจสอบการเข้าสู่ระบบ
    if (signIn == false) {
      //หากผู้ใช้ไม่ได้เข้าสู่ระบบ
      Navigator.pushNamed(context, 'login');
    } else {
      Navigator.pushNamed(context, 'studenthome');
    }
  }

  void initState() {
    // เป็นเมธอดที่ถูกเรียกเมื่อ State ถูกสร้างขึ้น ในกรณีนี้ เมื่อ State ถูกสร้าง
    checklogin();
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold();
  }
}
