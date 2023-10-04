import 'package:shared_preferences/shared_preferences.dart';

class User {
  static Future<bool?> getSignIn() async {
    //อ่านข้อมูลการเข้าสู่ระบบของผู้ใช้
    SharedPreferences pref = await SharedPreferences.getInstance();
    return pref.getBool("Sign-in");
  }

  static Future setSignIn(bool signIn) async {
    //บันทึกข้อมูลการเข้าสู่ระบบของผู้ใช้
    SharedPreferences pref = await SharedPreferences.getInstance();
    await pref.setBool("Sign-in", signIn);
  }
}
