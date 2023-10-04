// ignore_for_file: prefer_const_constructors
import 'package:flutter/material.dart';
import 'package:flutter_login/page/menu.dart';
import 'package:flutter_login/user.dart';
import 'package:flutter_login/pageT/homeT.dart';
import 'package:flutter_login/timeTest.dart';

class teacherhome extends StatefulWidget {
  const teacherhome({Key? key}) : super(key: key);

  @override
  State<teacherhome> createState() => _teacherhomeState();
}

class _teacherhomeState extends State<teacherhome> {
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

  final List<Widget> _tabs = [homeT(), TimeTestScreen(), Menu()];

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: _tabs.length,
      child: Scaffold(
        appBar: AppBar(
          backgroundColor: Colors.black,
          title: Text(
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
              color: Color.fromARGB(255, 74, 147, 231),
              iconSize: 30,
              icon: Icon(Icons.search),
            ),
          ],
          bottom: TabBar(
            onTap: _onTabTapped,
            unselectedLabelColor: Color.fromARGB(255, 74, 147, 231),
            indicatorSize: TabBarIndicatorSize.tab,
            indicator: BoxDecoration(
              gradient: LinearGradient(colors: [
                Color.fromARGB(255, 74, 147, 231),
                Color.fromARGB(255, 174, 124, 255),
              ]),
              borderRadius: BorderRadius.circular(40),
              color: Color.fromARGB(255, 255, 255, 255),
            ),
            tabs: [
              Tab(text: 'หน้าหลัก'),
              Tab(text: 'เวลาสอบ'),
              Tab(text: 'เมนู'),
            ],
          ),
        ),
        body: _tabs[_tabBarIndex],
      ),
    );
  }
}
