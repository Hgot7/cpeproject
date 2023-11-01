import 'package:flutter/material.dart';
import 'package:flutter_login/download.dart';
import 'package:flutter_login/rules.dart';
import 'package:flutter_login/studenthome.dart';
import 'package:flutter_login/teacherhome.dart';
import 'login.dart';
import 'check_login.dart';
import 'package:provider/provider.dart';

void main() {
  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => StudentIdProvider()),
        // ตัวอย่างเพิ่ม provider อื่น ๆ ตามความจำเป็น
      ],
      child: MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Flutter Demo',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
      home: check_login(),
      routes: {
        'studenthome': (context) => StudentHome(
              studentId: '',
            ),
        'login': (context) => login(),
        'teacherhome': (context) => teacherhome(),
        'download': (context) => Downlond(),
        'rules': (context) => Rules(),
      },
    );
  }
}

class StudentIdProvider extends ChangeNotifier {
  String? studentId;

  void setStudentId(String? id) {
    studentId = id;
    notifyListeners();
  }
}
