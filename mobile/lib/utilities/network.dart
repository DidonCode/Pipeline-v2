import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

import '../error.dart';
import '../login.dart';


/// @class Network
///
/// @brief Cette classe regroupe les fonctions permettant d'interagir avec l'API pour récupérer des musiques.
/// @file network.dart
class Network {
  //static const String baseUrl = 'http://definity-script.fr';
  static const String hostName = '10.0.2.2';
  static const String hostPort = '8082';

  /// @brief Gère les erreurs et redirige sur la page d'erreur ou de connexion en fonction du code d'erreur obtenu
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param errorCode le code d'erreur renvoyé par l'api.
  /// @param errorMessage Le message d'erreur renvoyé par l'api
  ///
  /// @return void
  static void checkCode(BuildContext context, int errorCode, String errorMessage) {

    debugPrint('Erreur : $errorCode $errorMessage');

    if(errorCode == 401 || errorCode == 403) {
      Navigator.push(
        context,
        MaterialPageRoute(builder: (context) => const Login()),
      );
    }

    if(errorCode == 400 || errorCode == 404 || errorCode == 500) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => ErrorPage(errorCode: errorCode.toString(), errorMessage: errorMessage)),
      );
    }
  }

  /// @brief Permet l'optimisation de centaines de ligne de code. Elle permet d'éffectuer les appelle api en fonction de si ma requête est un post ou un get tout en gerant les erreurs.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param formData le contenu de la requête post
  /// @param url L'endpoint de la requête (ex: /api/user/like)
  ///
  /// @return Future<void>
  static Future<dynamic> request(Map<String, String>? formData, String url, BuildContext context) async {
    try {
      var urlParsed = Uri.parse('http://$hostName:$hostPort$url');

      debugPrint(urlParsed.toString());

      var response;

      if(formData == null) {
        response = await http.get(urlParsed);
      } else {
        response = await http.post(
          urlParsed,
          body: formData,
        );
      }

      var data = json.decode(response.body);

      if(data is Map && data.containsKey('error') && data['error'] != null) {
        checkCode(context, data['error']['error_code'] ?? 'Unknown error code', data['error']['error_message'] ?? 'Unknown error message');
      } else {
        return data;
      }

    } catch(error) {
      debugPrint("Erreur : $error");
      throw Exception('Erreur sur la requête contenant l\'url : $url , erreur : $error');
    }
  }
}