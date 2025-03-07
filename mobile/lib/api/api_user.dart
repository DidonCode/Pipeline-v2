import 'package:flutter/material.dart';

import 'package:mobile/Utilities/local_storage.dart';
import 'package:mobile/login.dart';

import 'package:mobile/Utilities/network.dart';

/// @class ApiUser
///
/// @brief Cette classe regroupe toutes les fonctions permettant d'interagir avec l'API pour gérer la partie utilisateur.
/// @file api_user.dart
class ApiUser {

  /// @brief Permet de gérer l'inscritpion du compte.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param pseudo Le pseudo de l'utilisateur.
  /// @param email L'email de l'utilisateur.
  /// @param password Le mot de passe de l'utilisateur.
  /// @param isArtist S'il crée un compte artiste ou non.
  ///
  /// @return Future<bool?> Renvoie si l'inscritption a marché ou non.
  static Future<bool?> register(BuildContext context, String pseudo, String email, String password, bool isArtist) async {

    final Map<String, String> formData = {
        'pseudo' : pseudo,
        'email': email,
        'password': password,
        if(isArtist) 'artist' : 'true'
      };

    var data = await Network.request(formData, "/api/user/account", context);

    data['lastLogin'] = DateTime.now().toIso8601String();

    await LocalStorage.saveData(data);
    return true;
  }

  /// @brief Permet de gérer la connection du compte.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param email L'email de l'utilisateur.
  /// @param password Le mot de passe de l'utilisateur.
  ///
  /// @return Future<bool?> Renvoie si la connection a fonctionné ou non.
  static Future<bool?> connect(BuildContext context, String email, String password) async {

      final Map<String, String> formData = {
        'email': email,
        'password': password,
      };

      var data = await Network.request(formData, "/api/user/account", context);

      data['lastLogin'] = DateTime.now().toIso8601String();

      await LocalStorage.saveData(data);
      return true;
  }

  /// @brief Vérifie la validité du token.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  ///
  /// @return Future<bool?> Renvoie si le token est expiré(true) ou non(false)
  static Future<bool?> isTokenExpired(BuildContext context) async {
    final data = await LocalStorage.readData(context);

    if (data == null) {
      debugPrint('Aucune donnée utilisateur trouvée.');
      return null;
    }

    if (data['lastLogin'] != null) {
      try {
        final lastLogin = DateTime.parse(data['lastLogin']);
        final now = DateTime.now();

        if (now.difference(lastLogin).inDays > 30) {
          return true;
        }
        return false;
      } catch (e) {
        debugPrint('Erreur de parsing de la date : $e');
        return null;
      }
    }
    debugPrint('Clé "lastLogin" introuvable dans les données utilisateur.');
    return null;
  }

  /// @brief Demande un nouveau token à l'api en échange de l'ancien.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<void>
  static Future<void> newToken(BuildContext context, String token) async {
    try {
      final Map<String, String> formData = {
        'token': token,
        'type': "new",
      };

      var data = await Network.request(formData, "/api/user/account", context);

      await LocalStorage.saveData(data);

    } catch (error) {
      debugPrint('Erreur lors de l\'appel API newToken: $error');
      throw Exception('Impossible de se créer un nouveau token !');
    }
  }

  /// @brief Gère la déconnexion de l'utilisateur.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<void>
  static Future<void> disconnect(BuildContext context, String token) async {
      final Map<String, String> formData = {
        'token': token,
        'type': "disconnect",
      };
      
      Network.request(formData, '/api/user/account', context);

      await LocalStorage.clearData();

      Navigator.push(context, MaterialPageRoute(builder: (context) => const Login()));
  }

  /// @brief Permet de mettre à jour le token celon le token stockée en json dans l'appareil de l'utilisateur.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  ///
  /// @return Future<void>
  static Future<void> updateToken(BuildContext context) async {
    final data = await LocalStorage.readData(context);

    if (data == null || data.isEmpty) {
      debugPrint('Aucune donnée utilisateur à mettre à jour.');
      return;
    }
    await newToken(context, data['token']);

    data['lastLogin'] = DateTime.now().toIso8601String();

    await LocalStorage.saveData(data);
  }
}