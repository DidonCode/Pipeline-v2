import 'package:flutter/cupertino.dart';

import 'package:mobile/class/sound.dart';
import 'package:mobile/utilities/network.dart';

/// @class ApiSound
///
/// @brief Cette classe regroupe toutes les fonctions permettant d'interagir avec l'API pour récupérer des musiques.
/// @file api_sound.dart
class ApiSound {

  /// @brief Récupère les musiques d'une playlist.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param id L'identifiant de la playlist .
  ///
  /// @return Future<List<Sound>> La liste des musiques de la playlists.
  static Future<List<Sound>> byPlaylist(BuildContext context, String id) async {

      var data = await Network.request(null, "/api/sound?playlist=$id", context);

      List<Sound> sounds = [];

        for (var soundDatas in data) {
          sounds.add(Sound(
            soundDatas['id'].toString(),
            soundDatas['title'],
            soundDatas['artist'].toString(),
            soundDatas['type'] is int
                ? soundDatas['type']
                : int.tryParse(soundDatas['type'].toString()),
            soundDatas['image'],
            soundDatas['link'],
          ));
      }
      return sounds;
  }

  /// @brief Récupère les musiques d'un artiste.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param id L'identifiant de l'artiste.
  ///
  /// @return Future<List<Sound>> La liste des musiques de l'artiste.
  static Future<List<Sound>> byArtist (BuildContext context, String id) async {

      var data = await Network.request(null, "/api/sound?artist=$id", context);

      List<dynamic> soundsData = data['sounds'];
      List<Sound> sounds = [];

      debugPrint(data.toString());

      for(var soundDatas in soundsData) {
        sounds.add(Sound(
            soundDatas['id'].toString(),
            soundDatas['title'],
            soundDatas['artist'].toString(),
            soundDatas['type'] is int
                ? soundDatas['type']
                : int.tryParse(soundDatas['type'].toString()),
            soundDatas['image'],
            soundDatas['link']
        ));
      }

      return sounds;
  }

  /// @brief Récupère les musiques aimées d'un artiste.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param id L'identifiant de l'artiste .
  ///
  /// @return Future<List<Sound>> La liste des musiques aimées de l'artiste.
  static Future<List<Sound>> likedByArtist (BuildContext context, String id) async {

      var data = await Network.request(null, "/api/like?artist=$id&type=sound", context);

      List<Sound> sounds = [];

      if(data is List) {
        for (var soundDatas in data) {
          sounds.add(Sound(
              soundDatas['id'].toString(),
              soundDatas['title'],
              soundDatas['artist'].toString(),
              soundDatas['type'] is int
                  ? soundDatas['type']
                  : int.tryParse(soundDatas['type'].toString()),
              soundDatas['image'],
              soundDatas['link']
          ));
        }
      }

      return sounds;
  }

  /// @brief Récupère les musiques aimées de l'utilisateur connecté.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<List<Sound>> La liste de musiques aimées par l'utilisateur.
  static Future<List<Sound>> likedByUser(BuildContext context, String token) async {
      final Map<String, String> formData = {
        'type': "sound",
        'token': token,
      };

      var data = await Network.request(formData, "/api/user/like", context);

      List<Sound> sounds = [];

        for (var soundDatas in data) {
          sounds.add(Sound(
              soundDatas['id'].toString(),
              soundDatas['title'],
              soundDatas['artist'].toString(),
              soundDatas['type'] is int
                  ? soundDatas['type']
                  : int.tryParse(soundDatas['type'].toString()),
              soundDatas['image'],
              soundDatas['link']
          ));
      }
      return sounds;
  }

  /// @brief Récupère les musiques par titres.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param title Le titre de la/les musique/s.
  ///
  /// @return Future<List<Sound>> La liste des musique par titre.
  static Future<List<Sound>> byTitle (BuildContext context, String title) async {

      var data = await Network.request(null, "/api/sound?title=$title", context);

      List<dynamic> soundsDatabase = data['database'];
      List<dynamic> soundsYoutube = data['youtube'];
      List<Sound> sounds = [];

      for(var soundDatas in soundsDatabase) {
        sounds.add(Sound(
            soundDatas['id'].toString(),
            soundDatas['title'],
            soundDatas['artist'].toString(),
            soundDatas['type'] is int
                ? soundDatas['type']
                : int.tryParse(soundDatas['type'].toString()),
            soundDatas['image'],
            soundDatas['link']
        ));
      }

      for(var soundDatas in soundsYoutube) {
        sounds.add(Sound(
            soundDatas['id'],
            soundDatas['title'],
            soundDatas['artist'],
            soundDatas['type'] is int
                ? soundDatas['type']
                : int.tryParse(soundDatas['type'].toString()),
            soundDatas['image'],
            soundDatas['link']
        ));
      }

      return sounds;
  }

  /// @brief Récupere les musiques en fonction de l'activité de l'utilisateur.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param type Le type d'activité a récuperer.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<List<Sound>> La liste de musiques celon l'activité de l'utilisateur.
  static Future<List<Sound>> byHomeActivity(BuildContext context, String token, String type) async {

      Map<String, String> formData = {
        'type': type,
        'token': token
      };

      var data = await Network.request(formData, "/api/user/activity", context);

      List<Sound> sounds = [];

      for(var soundDatas in data){
        sounds.add(Sound(
            soundDatas['id'].toString(),
            soundDatas['title'],
            soundDatas['artist'].toString(),
            soundDatas['type'] is int
                ? soundDatas['type']
                : int.tryParse(soundDatas['type'].toString()),
            soundDatas['image'],
            soundDatas['link']
        ));
      }
      return sounds;
  }

  /// @brief Récupere les musiques de l'exploration de Butify (Plus aimées, plus écoutées etc ...).
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param type Le type d'explorations à récuperer.
  ///
  /// @return Future<List<Sound>> La liste des musiques celon l'exploration.
  static Future<List<Sound>> byExplore(BuildContext context, String type) async {

      var data = await Network.request(null, "/api/like?type=$type", context);

      List<Sound> sounds = [];

      for(var soundDatas in data) {
        sounds.add(Sound(
            soundDatas['id'].toString(),
            soundDatas['title'],
            soundDatas['artist'].toString(),
            soundDatas['type'] is int
                ? soundDatas['type']
                : int.tryParse(soundDatas['type'].toString()),
            soundDatas['image'],
            soundDatas['link']
            )
        );
      }
      return sounds;
  }
}