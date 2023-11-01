import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';

class Project extends StatefulWidget {
  final String teacherId; // เพิ่มพารามิเตอร์ teacherId
  Project({required this.teacherId});
  @override
  State<Project> createState() => _ProjectState();
}

class _ProjectState extends State<Project> {
  List<Map<String, dynamic>> projects = [];

  Future<void> fetchProject() async {
    try {
      final response = await http.post(
        Uri.parse('https://www.cpeproject.shop/flutterIN/project.php'),
        body: {'teacher_id': widget.teacherId},
      );

      if (response.statusCode == 200) {
        final jsonData = json.decode(response.body);

        if (jsonData != null) {
          final List<Map<String, dynamic>> allProjects =
              List<Map<String, dynamic>>.from(jsonData);

          final List<Map<String, dynamic>> filteredProjects = allProjects
              .where((project) =>
                  project['teacher_id1'] == widget.teacherId ||
                  project['teacher_id2'] == widget.teacherId)
              .toList();

          setState(() {
            projects = filteredProjects;
          });
        } else {
          throw Exception('ไม่มีข้อมูลที่ได้จาก API');
        }
      } else {
        throw Exception('เกิดข้อผิดพลาดในการเรียก API');
      }
    } catch (e) {
      print('Error: $e');
    }
  }

  @override
  void initState() {
    super.initState();
    fetchProject();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("โครงงานที่รับเป็นที่ปรึกษา"),
        backgroundColor: Colors.black,
      ),
      body: Center(
        child: projects.isEmpty
            ? Text("ไม่มีโครงงานที่รับเป็นที่ปรึกษา")
            : ListView.builder(
                itemCount: projects.length,
                itemBuilder: (context, index) {
                  return Card(
                    child: ListTile(
                      title: Text(projects[index]['project_nameTH']),
                      subtitle:
                          Text("โครงงานรหัส: ${projects[index]['project_id']}"),
                    ),
                  );
                },
              ),
      ),
    );
  }
}
