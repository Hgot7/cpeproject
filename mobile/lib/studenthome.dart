import 'package:flutter/material.dart';
import 'package:flutter_login/page/menu.dart';
import 'package:flutter_login/user.dart';
import 'package:flutter_login/page/home.dart';
import 'package:flutter_login/timeTest.dart';

class StudentHome extends StatefulWidget {
  final String studentId;

  StudentHome({required this.studentId});

  @override
  _StudentHomeState createState() => _StudentHomeState();
}

class _StudentHomeState extends State<StudentHome> {
  int _tabBarIndex = 0;

  void _onTabTapped(int index) {
    setState(() {
      _tabBarIndex = index;
    });
  }

  Future logout() async {
    await User.setSignIn(false);
    Navigator.pushNamed(context, 'login');
  }

  @override
  Widget build(BuildContext context) {
    final List<Widget> _tabs = [
      Home(studentId: widget.studentId), // ให้ Home รับ studentId จาก widget.
      TimeTestScreen(),
      Menu(),
    ];
    return DefaultTabController(
      length: _tabs.length,
      child: Scaffold(
        appBar: AppBar(
          backgroundColor: Colors.black,
          title: const Text(
            'Project Manager App',
            style: TextStyle(
              fontSize: 21,
              fontFamily: 'monospace',
              fontWeight: FontWeight.w800,
              color: Color.fromARGB(255, 74, 147, 231),
            ),
          ),
          actions: [
            IconButton(
              onPressed: () {
                // showSearch(context: context, delegate: CustomSearch());
              },
              color: const Color.fromARGB(255, 74, 147, 231),
              iconSize: 30,
              icon: const Icon(Icons.search),
            ),
          ],
          bottom: TabBar(
            onTap: _onTabTapped,
            unselectedLabelColor: const Color.fromARGB(255, 74, 147, 231),
            indicatorSize: TabBarIndicatorSize.tab,
            indicator: BoxDecoration(
              gradient: const LinearGradient(colors: [
                Color.fromARGB(255, 74, 147, 231),
                Color.fromARGB(255, 174, 124, 255),
              ]),
              borderRadius: BorderRadius.circular(40),
              color: const Color.fromARGB(255, 255, 255, 255),
            ),
            tabs: const [
              Tab(text: 'หน้าหลัก'),
              Tab(text: 'เวลาสอบ'),
              Tab(text: 'เมนู'),
            ],
          ),
        ),
        body: IndexedStack(
          index: _tabBarIndex,
          children: _tabs.map((tab) => tab).toList(),
        ),
      ),
    );
  }
}
