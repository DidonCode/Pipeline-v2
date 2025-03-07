import 'package:flutter/material.dart';
import 'package:hexcolor/hexcolor.dart';

/// @file theme.dart
///
/// @cond IGNORE_THIS_CLASS
class ThemeClass {

  static Color lightPrimaryColor =  HexColor('#FFFFFF');
  static Color darkPrimaryColor = HexColor('#1e1e1e');

  static Color lightBackgroundColor = HexColor('#C0E4FF');
  static Color darkBackgroundColor = HexColor('#282828');

  static Color orange = HexColor('#EE964B');


  static ThemeData lightTheme = ThemeData(
    useMaterial3: true,

    primaryColor: Colors.white,
    primaryColorDark: HexColor("#1e1e1e"),
    highlightColor: orange,
    dividerColor: HexColor('#4f4f4f'),
    scaffoldBackgroundColor: HexColor('#C0E4FF'),

    floatingActionButtonTheme: FloatingActionButtonThemeData(
        backgroundColor: HexColor('#F4D35E')
    ),

    bottomNavigationBarTheme: BottomNavigationBarThemeData(
      backgroundColor: HexColor('#FFFFFF'),
      selectedItemColor: HexColor('#000000'),
      unselectedItemColor: HexColor('#6c757d'),
    ),
    colorScheme: const ColorScheme.light().copyWith(
      primary: lightPrimaryColor,
      surface: lightBackgroundColor,
    ),

    textTheme: const TextTheme(
      displayLarge: TextStyle(color: Colors.black),
      displayMedium: TextStyle(color: Colors.black),
      displaySmall: TextStyle(color: Colors.black),
      headlineLarge: TextStyle(color: Colors.black),
      headlineMedium: TextStyle(color: Colors.black),
      headlineSmall: TextStyle(color: Colors.black),
      titleLarge: TextStyle(
          color: Colors.black,
          fontSize: 20,
          fontWeight: FontWeight.bold,
      ),
      titleMedium: TextStyle(color: Colors.black),
      titleSmall: TextStyle(color: Colors.black),
      bodyLarge: TextStyle(color: Colors.black),
      bodyMedium: TextStyle(color: Colors.black),
      bodySmall: TextStyle(color: Colors.black),
      labelLarge: TextStyle(color: Colors.black),
      labelMedium: TextStyle(color: Colors.black),
      labelSmall: TextStyle(color: Colors.black),
    ),

  );


  static ThemeData darkTheme = ThemeData(
    useMaterial3: true,
    primaryColor: HexColor('#1e1e1e'),
    primaryColorDark: Colors.white,
    highlightColor: orange,
    floatingActionButtonTheme:
      FloatingActionButtonThemeData(backgroundColor: HexColor('#F4D35E')),
    scaffoldBackgroundColor: HexColor('#282828'),
    colorScheme: const ColorScheme.dark().copyWith(
      primary: HexColor('#1e1e1e'),
    ),
    bottomNavigationBarTheme: BottomNavigationBarThemeData(
      backgroundColor: HexColor('#1e1e1e'),
      selectedItemColor: HexColor('#F4D35E'),
      unselectedItemColor: HexColor('#b1b1b1'),
    ),

    textTheme: const TextTheme(
      displayLarge: TextStyle(color: Colors.white),
      displayMedium: TextStyle(color: Colors.white),
      displaySmall: TextStyle(color: Colors.white),
      headlineLarge: TextStyle(color: Colors.white),
      headlineMedium: TextStyle(color: Colors.white),
      headlineSmall: TextStyle(color: Colors.white),
      titleLarge: TextStyle(
        color: Colors.white,
        fontSize: 20,
        fontWeight: FontWeight.bold,
      ),
      titleMedium: TextStyle(color: Colors.white),
      titleSmall: TextStyle(color: Colors.white),
      bodyLarge: TextStyle(color: Colors.white),
      bodyMedium: TextStyle(color: Colors.white),
      bodySmall: TextStyle(color: Colors.white),
      labelLarge: TextStyle(color: Colors.white),
      labelMedium: TextStyle(color: Colors.white),
      labelSmall: TextStyle(color: Colors.white),
    ),
  );
}
/// @endconds