import 'package:flutter/material.dart';
/// @file bottom_nav_bar.dart
///
/// @cond IGNORE_THIS_CLASS
class BottomNavBar extends StatelessWidget {

  const BottomNavBar({
    super.key,
    required this.currentIndex,
    required this.onTabSelected,
  });

  final int currentIndex;
  final void Function(int) onTabSelected;

  @override
  Widget build(BuildContext context) {
    return Container(
        height: 55,
        child: BottomNavigationBar(
          currentIndex: currentIndex,
          onTap: onTabSelected,
          selectedFontSize: 11,
          unselectedFontSize: 11,
          items: const [
            BottomNavigationBarItem(
                icon: Icon(Icons.home),
                label: 'Acceuil',
            ),

            BottomNavigationBarItem(
                icon: Icon(Icons.search),
                label: 'Explorer',
            ),

            BottomNavigationBarItem(
                icon: Icon(Icons.library_music),
                label: 'Bibliothèque',
            )
          ],
        ),
      );
  }
}
/// @endcond