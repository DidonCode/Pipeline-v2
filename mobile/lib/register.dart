import 'package:flutter/material.dart';
import 'package:hexcolor/hexcolor.dart';
import 'package:mobile/api/api_user.dart';

import 'package:mobile/main.dart';

/// @file register.dart
///
/// @cond IGNORE_THIS_CLASS
class Register extends StatefulWidget {
  const Register({
    super.key,
  });

  @override
  State<Register> createState() => _RegisterState();
}

class _RegisterState extends State<Register> {
  late TextEditingController _emailController;
  late TextEditingController _usernameController;
  late TextEditingController _passwordController;
  bool isArtist = false;

  @override
  void initState() {
    super.initState();

    _emailController = TextEditingController();
    _usernameController = TextEditingController();
    _passwordController = TextEditingController();
  }

  @override
  void dispose() {
    _emailController.dispose();
    _usernameController.dispose();
    _passwordController.dispose();

    super.dispose();
  }

  void register() async {
    final success = await ApiUser.register(context, _usernameController.text, _emailController.text, _passwordController.text, isArtist);

    if (success == true) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const Main()),
      );
    } else {
      showDialog(
        context: context,
        builder: (context) => const AlertDialog(
          title: Text("Erreur"),
          content: Text("L'inscription a échoué. Veuillez réessayer."),
        ),
      );
    }
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
                      TextButton(
                          onPressed: () {
                            Navigator.pop(context);
                          },
                          child: const Text("Connexion"),
                      ),
                      const Text("Inscription")
                    ],
                  ),
                  Column(
                      children: [
                        TextField(
                          autofocus: false,
                          decoration: const InputDecoration(hintText: 'Pseudo'),
                          controller: _usernameController,
                        ),
                        const SizedBox(height: 35),
                        TextField(
                          autofocus: false,
                          decoration: const InputDecoration(hintText: 'E-mail'),
                          controller: _emailController,
                        ),
                        const SizedBox(height: 35),
                        TextField(
                          autofocus: false,
                          decoration: const InputDecoration(hintText: 'Mot de passe'),
                          controller: _passwordController,
                        ),
                        Row(
                          children: [
                            const Text("Créer un compte artiste"),
                            Checkbox(value: isArtist, onChanged: (bool? value) {
                              setState(() {
                                isArtist = value ?? false;
                              });
                            }),
                          ],
                        ),
                        const SizedBox(height: 35),
                        ElevatedButton(
                          onPressed: register,
                          style: ElevatedButton.styleFrom(
                              backgroundColor: HexColor("#F4D35E")),
                          child: const Text("Inscription"),
                        )
                      ],
                    ),
                ],
              ),
            ))
    );
  }
}
/// @endcond