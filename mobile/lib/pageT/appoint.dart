import 'package:flutter/material.dart';

class Appoint extends StatefulWidget {
  @override
  State<Appoint> createState() => _AppointState();
}

class _AppointState extends State<Appoint> {
  List<dynamic> appoint = [];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Appoint'),
        backgroundColor: Colors.black,
      ),
      body: ListView.builder(
        itemCount: appoint.length,
        itemBuilder: (context, index) {
          final appointment = appoint[index];
          return ListTile(
            title: Text(appointment['title'] ?? ''),
            subtitle: Text(appointment['description'] ?? ''),
            trailing: Text(appointment['appoint_date'] ?? ''),
          );
        },
      ),
    );
  }
}
