import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:path_provider/path_provider.dart';

import '../login.dart';

/// @class LocalStorage
///
/// @brief Cette classe regroupe toutes les fonctions permettant de gérer le stockage des informations de l'utilisateur.
/// @file local_storage.dart
class LocalStorage {
  static String fileName = "userData.json";

  /// @brief Permet de récupérer le json ou de le créer s'il n'est pas dans les documents de l'appareil.
  ///
  /// @return Future<File> Renvoie fichier userData.json
  static Future<File> _getDirectory() async {
    final directory = await getApplicationDocumentsDirectory();
    final file = File('${directory.path}/$fileName');

    await Directory(directory.path).create(recursive: true);
    return file;
  }

  /// @brief Permet d'enregistrer les données de l'utilisateur dans le fichier userData.json.
  ///
  /// @return Future<void>
  static Future<void> saveData(Map<String, dynamic> jsonData) async {
    try {
      final file = await _getDirectory();
      final jsonString = jsonEncode(jsonData);

      await file.writeAsString(jsonString);
      debugPrint('Données sauvegardées avec succès.');
    } catch (e) {
      debugPrint('Erreur lors de l\'écriture des données : $e');
    }
  }

  /// @brief Permet de lire les données de l'utilisateur dans le fichier userData.json.
  ///
  /// @return Future<Map<String, dynamic>?> Renvoie le json manipulable de sorte à utiliser les données de l'utilisateur
  static Future<Map<String, dynamic>?> readData(BuildContext context) async {
    try {
      final file = await _getDirectory();
      if (await file.exists()) {
        final jsonString = await file.readAsString();
        if (jsonString.isEmpty) {
          debugPrint('Fichier vide.');
          return null;
        }
        return jsonDecode(jsonString);
      } else {
        debugPrint('Fichier non trouvé.');
        Navigator.push(context, MaterialPageRoute(builder: (context) => const Login()));
        return null;
      }
    } catch (e) {
      debugPrint('Erreur lors de la lecture des données : $e');
      return null;
    }
  }

  /// @brief Permet de supprimer le fichier userData.json par exemple lors de la déconnexion.
  ///
  /// @return Future<void>
  static Future<void> clearData() async {
    try {
      final file = await _getDirectory();
      await file.delete();
      debugPrint('Données supprimées avec succès.');
    } catch (e) {
      debugPrint('Erreur lors de la suppression des données : $e');
    }
  }
}