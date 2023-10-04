import 'package:flutter/material.dart';

class MyButton extends StatelessWidget {
  final iconImagePath;
  final String buttonText;
  final String nextPageRoute; // เพิ่มตัวแปรนี้เพื่อระบุหน้าปลายทาง

  const MyButton({
    Key? key,
    required this.iconImagePath,
    required this.buttonText,
    required this.nextPageRoute,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        SizedBox(height: 20),
        Container(
          height: 70,
          padding: EdgeInsets.all(15),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: Colors.grey.shade400,
                blurRadius: 40,
                spreadRadius: 10,
              ),
            ],
          ),
          child: Center(
            child: GestureDetector(
              onTap: () {
                Navigator.pushNamed(context, nextPageRoute);
              },
              child: Image.asset(iconImagePath),
            ),
          ),
        ),
        SizedBox(height: 5),
        Text(
          buttonText,
          style: TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.bold,
            color: Colors.black,
          ),
        ),
      ],
    );
  }
}
