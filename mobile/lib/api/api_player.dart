import 'package:flutter/material.dart';

import 'package:mobile/Utilities/network.dart';
import 'package:mobile/class/sound.dart';

/// @class ApiPlayer
///
/// @brief permet de gérer les interactions avec l'API liées au lecteur audio.
///
/// @file api_player.dart
class ApiPlayer {

  /// @brief Récupère une liste de recommandations basées sur un son donné.
  ///
  /// @param context Le contexte de l'application utilisé pour la navigation.
  /// @param sound La musique pour laquelle on veut des recommandations.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<List<Sound>> La Liste de musique recommandées.
  static Future<List<Sound>> getRecommendation(BuildContext context, Sound sound, String token) async {
    final Map<String, String> formData = {
      'sound': sound.id,
      'token': token,
    };

    var data = await Network.request(formData, "/api/user/play", context);

    List<Sound> sounds = [];

    for (var soundDatas in data) {
      sounds.add(
          Sound(
            soundDatas['id'].toString(),
            soundDatas['title'],
            soundDatas['artist'].toString(),
            soundDatas['type'] is int
                ? soundDatas['type']
                : int.tryParse(soundDatas['type'].toString()),
            soundDatas['image'],
            soundDatas['link'],
          )
      );
    }
    return sounds;
  }

  /// @brief Envoie de la musique lancée pour permettre l'ajout à l'activité de l'utilisateur.
  ///
  /// @param context Le contexte de l'application utilisé pour la navigation.
  /// @param sound La musique lancée par l'utilisateur.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return void
  static void addActivity(BuildContext context, Sound sound, String token) async {
    final Map<String, String> formData = {
      'sound': sound.id,
      'token': token,
    };
    Network.request(formData, "/api/user/activity", context);
  }
}