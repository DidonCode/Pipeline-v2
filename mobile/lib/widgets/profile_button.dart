import 'package:flutter/material.dart';

import 'package:mobile/api/api_user.dart';
import 'package:mobile/class/sound.dart';
import 'package:mobile/class/user.dart';

/// @file profile_button.dart
///
/// @cond IGNORE_THIS_CLASS
class ProfileButton extends StatelessWidget {
  final User user;
  final Function(List<Sound>) showPlay;

  const ProfileButton({
    super.key,
    required this.user,
    required this.showPlay,
  });

  Future<void> disconnect(BuildContext context, String token) async {
    await ApiUser.disconnect(context, token);
  }

  @override
  Widget build(BuildContext context) {
    return IconButton(
      onPressed: () {
        showMenu(
          context: context,
          position: const RelativeRect.fromLTRB(1, 80, 0, 0),
          items: [
            PopupMenuItem(
              child: ListTile(
                leading: const Icon(Icons.person),
                title: const Text("Modifier profil"),
                onTap: () {},
              ),
            ),
            PopupMenuItem(
              child: ListTile(
                leading: const Icon(Icons.logout),
                title: const Text("Déconnexion"),
                onTap: () {
                  showPlay([]);
                  disconnect(context, user.token);
                },
              ),
            ),
          ],
        );
      },
      icon: SizedBox(
        width: 30,
        height: 30,
        child: ClipRRect(
          borderRadius: BorderRadius.circular(50),
          child: Image.network(
            user.image,
            fit: BoxFit.cover,
          ),
        ),
      ),
    );
  }
}
/// @endconds