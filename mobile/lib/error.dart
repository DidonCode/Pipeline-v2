import 'package:flutter/material.dart';

import 'main.dart';

/// @file error.dart
///
/// @cond IGNORE_THIS_CLASS
class ErrorPage extends StatefulWidget {
  const ErrorPage({
    super.key,
    required this.errorCode,
    required this.errorMessage
  });

  final String errorCode;
  final String errorMessage;

  @override
  State<ErrorPage> createState() => _ErrorPageState();
}

class _ErrorPageState extends State<ErrorPage> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(onPressed: () {
          Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => const Main()));
        },
          icon: const Icon(Icons.home),
        ),
      ),
      body: Center(
        child: Text('Erreur : ${widget.errorCode} ${widget.errorMessage}'),
      ),
    );
  }
}
/// @endcond