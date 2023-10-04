import 'package:flutter/material.dart';
import 'package:flutter_login/pageT/My_button.dart';

class Project extends StatefulWidget {
  @override
  State<Project> createState() => _ProjectState();
}

class _ProjectState extends State<Project> {
  int currentIndex = 0;

  void goToPade(int index) {
    setState(() {
      currentIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
        body: SafeArea(
            child: Column(children: [
      Padding(
        padding: EdgeInsets.all(5.0),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
          children: [
            MyButton(
              iconImagePath: 'assets/img/score.png',
              buttonText: 'คะแนน',
              nextPageRoute: 'score',
            ),
            MyButton(
              iconImagePath: 'assets/img/grade.png',
              buttonText: 'เกรด',
              nextPageRoute: 'grade',
            )
          ],
        ),
      ),
      Row(
        children: [
          Text(
            'โครงงานที่รับเป็นที่ปรึกษา',
            style: TextStyle(
                fontSize: 21, fontWeight: FontWeight.bold, color: Colors.black),
          ),
        ],
      ),
    ])));
  }
}
