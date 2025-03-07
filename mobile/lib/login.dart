import 'package:flutter/material.dart';
import 'package:hexcolor/hexcolor.dart';

import 'package:mobile/api/api_user.dart';
import 'package:mobile/main.dart';
import 'package:mobile/register.dart';

/// @file login.dart
///
/// @cond IGNORE_THIS_CLASS
class Login extends StatefulWidget {
  const Login({
    super.key,
  });

  @override
  State<Login> createState() => _LoginState();
}

class _LoginState extends State<Login> {
  late TextEditingController _emailController;
  late TextEditingController _passwordController;

  bool _isEmailInvalid = false;
  bool _isPasswordInvalid = false;
  @override
  void initState() {
    super.initState();
    _emailController = TextEditingController();
    _passwordController = TextEditingController();

    _emailController.text = "TheoLeBg@outlook.com";
    _passwordController.text = "JeSuisTropBg";
  }

  @override
  void dispose() {
    super.dispose();
    _emailController.dispose();
    _passwordController.dispose();
  }

  void connect() async {
    final user = await ApiUser.connect(context, _emailController.text, _passwordController.text);

    setState(() {
      if (user == false || user == null) {
        _isEmailInvalid = true;
        _isPasswordInvalid = true;
      } else {
        Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => const Main()));
      }
    });
  }

  OutlineInputBorder _inputBorder(bool isInvalid) {
    return OutlineInputBorder(
      borderRadius: BorderRadius.circular(5.0),
      borderSide: BorderSide(
        color: isInvalid ? Colors.red : Colors.grey,
        width: 1.5,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: Center(
            child: Container(
              margin: const EdgeInsets.only(top: 50),
              height: MediaQuery.of(context).size.height * 0.80,
              width: MediaQuery.of(context).size.width * 0.85,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Row(
                    children: [
                      const Text("Connexion"),
                      TextButton(
                          onPressed: () {
                            Navigator.push(context, MaterialPageRoute(builder: (context) => const Register()),);
                          },
                          child: const Text("Inscription")
                      )
                    ],
                  ),
                  Column(
                    children: [
                      TextField(
                        autofocus: false,
                        decoration: InputDecoration(
                          hintText: 'E-mail',
                          border: _inputBorder(_isEmailInvalid),
                          focusedBorder: _inputBorder(_isEmailInvalid),
                          enabledBorder: _inputBorder(_isEmailInvalid),
                        ),
                        controller: _emailController,
                      ),
                      const SizedBox(height: 35),
                      TextField(
                        autofocus: false,
                        decoration: InputDecoration(
                          hintText: 'Mot de passe',
                          border: _inputBorder(_isPasswordInvalid),
                          focusedBorder: _inputBorder(_isPasswordInvalid),
                          enabledBorder: _inputBorder(_isPasswordInvalid),
                        ),
                        controller: _passwordController,
                        obscureText: true,
                      ),
                      const SizedBox(height: 35),
                      ElevatedButton(
                        onPressed: connect,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: HexColor("#F4D35E"),
                        ),
                        child: const Text("Connexion"),
                      )
                    ],
                  ),
                ],
              ),
            )
        )
    );
  }
}
/// @endconds