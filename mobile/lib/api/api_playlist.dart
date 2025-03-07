import 'package:flutter/cupertino.dart';

import 'package:mobile/class/playlist.dart';
import 'package:mobile/utilities/network.dart';


/// @class ApiPlaylist
///
/// @brief Cette classe regroupe toutes les fonctions permettant d'interagir avec l'API pour récupérer des playlists.
/// @file api_playlist.dart
class ApiPlaylist {

  /// @brief Récupère les playlists d'un artiste par son identifiant.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param id L'identifiant de l'artiste .
  ///
  /// @return Future<List<Playlist>> La liste des playlists de l'artiste.
  static Future<List<Playlist>> byArtist (BuildContext context, String id) async {
    var data = await Network.request(null, "/api/playlist?owner=$id", context);

    List<dynamic> playlistsData = data['playlists'];
    List<Playlist> playlists = [];

    for(var playlistDatas in playlistsData) {
      playlists.add(Playlist(
          playlistDatas['id'].toString(),
          playlistDatas['owner'].toString(),
          playlistDatas['title'],
          playlistDatas['description'],
          playlistDatas['image'],
          playlistDatas['public'] is int
              ? playlistDatas['public']
              : int.tryParse(playlistDatas['public'].toString())
      ));
    }

    return playlists;
  }

  /// @brief Récupère les playlists aimées d'un artiste par son identifiant.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param id L'identifiant de l'artiste .
  ///
  /// @return Future<List<Playlist>> la liste des playlists aimées de l'artiste.
  static Future<List<Playlist>> likedByArtist (BuildContext context, String id) async {

      var data = await Network.request(null, "/api/like?artist=$id&type=playlist", context);

      List<Playlist> playlists = [];

      debugPrint(data.toString());

      for (var playlistDatas in data) {
        playlists.add(Playlist(
          playlistDatas['id'].toString(),
          playlistDatas['owner'].toString(),
          playlistDatas['title'],
          playlistDatas['description'],
          playlistDatas['image'],
            playlistDatas['public'] is int
                ? playlistDatas['public']
                : int.tryParse(playlistDatas['public'].toString()),
        ));
      }

      return playlists;
  }

  /// @brief Récupère les playlists de l'utilisateur connectée grâce à son identifiant.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<List<Playlist>> La liste des playlists aimées de l'utilisateur connecté.
  static Future<List<Playlist>> likedByUser(BuildContext context, String token) async {
    final Map<String, String> formData = {
      'type': "playlist",
      'token': token,
    };

    var data = await Network.request(formData, "/api/user/like", context);

    List<Playlist> playlists = [];

    for(var playlistDatas in data){
      playlists.add(
          Playlist(
              playlistDatas['id'].toString(),
              playlistDatas['owner'].toString(),
              playlistDatas['title'],
              playlistDatas['description'],
              playlistDatas['image'],
              playlistDatas['public'] is int
                  ? playlistDatas['public']
                  : int.tryParse(playlistDatas['public'].toString())
          )
      );
    }

    return playlists;
  }

  /// @brief Récupère les playlists de l'utilisateur connecté grâce à son token.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<List<Playlist>> La liste des playlists de l'utilisateur connecté.
  static Future<List<Playlist>> byUser(BuildContext context, String token) async {
      final Map<String, String> formData = {
        'token': token,
      };

      var data = await Network.request(formData, "/api/user/playlist", context);

      List<Playlist> playlists = [];

      for(var playlistDatas in data){
        playlists.add(
            Playlist(
                playlistDatas['id'].toString(),
                playlistDatas['owner'].toString(),
                playlistDatas['title'],
                playlistDatas['description'],
                playlistDatas['image'],
                playlistDatas['public'] is int
                    ? playlistDatas['public']
                    : int.tryParse(playlistDatas['public'].toString())
            )
        );
      }

      return playlists;
  }

  /// @brief Récupère les playlists par un titre.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param title Le titre de la/les playlist/s.
  ///
  /// @return Future<List<Playlist>> La liste des playlists trouvées avec ce titre.
  static Future<List<Playlist>> byTitle (BuildContext context, String title) async {

      var data = await Network.request(null, "/api/playlist?title=$title", context);

      List<dynamic> playlistsDatabase = data['database'];
      List<dynamic> playlistsYoutube = data['youtube'];
      List<Playlist> playlists = [];

      for(var playlistDatas in playlistsDatabase) {
        playlists.add(Playlist(
            playlistDatas['id'].toString(),
            playlistDatas['owner'].toString(),
            playlistDatas['title'],
            playlistDatas['description'],
            playlistDatas['image'],
            playlistDatas['public'] is int
                ? playlistDatas['public']
                : int.tryParse(playlistDatas['public'].toString())
        ));
      }

      for(var playlistDatas in playlistsYoutube) {
        playlists.add(Playlist(
            playlistDatas["id"],
            playlistDatas["owner"],
            playlistDatas["title"],
            playlistDatas["description"],
            playlistDatas["image"],
            playlistDatas['public'] is int
                ? playlistDatas['public']
                : int.tryParse(playlistDatas['public'].toString())
        ));
      }

      return playlists;
  }

  /// @brief Créer une playlist sur le compte de l'utilisateur connecté grâce à son token.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param title Le nom de la playlist.
  /// @param description La description de la playlist.
  /// @param public La visibilité de la playlist (privée, publique)
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<Playlist?> La playlist crée par l'utilisateur.
  static Future<Playlist?> create(BuildContext context, String title, String description, String public, String token) async {
      final Map<String, String> formData = {
        'title': title,
        'description': description,
        'public' : public,
        'token' : token
      };

      var data = await Network.request(formData, "/api/user/playlist", context);

      return Playlist(
          data['id'].toString(),
          data['owner'].toString(),
          data['title'],
          data['description'],
          data['image'],
          data['public'] is int
              ? data['public']
              : int.tryParse(data['public'].toString())
      );
  }

  /// @brief Modifie une playlist de l'utilisateur connecté.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param title Le nom de la playlist.
  /// @param description La description de la playlist.
  /// @param public La visibilité de la playlist (privée, publique).
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<Playlist?> La Playlist modifiée par l'utilisateur.
  static Future<Playlist?> update(BuildContext context, String title, String description, String public, String token) async {
    try {
      final Map<String, String> formData = {
        'title': title,
        'description': description,
        'public' : public,
        'token' : token
      };

      var data = await Network.request(formData, "/api/user/playlist", context);

      return Playlist(
          data['id'].toString(),
          data['owner'].toString(),
          data['title'],
          data['description'],
          data['image'],
          data['public'] is int
              ? data['public']
              : int.tryParse(data['public'].toString())
      );

    } catch (error) {
      debugPrint('Erreur lors de l\'appel API playlist create: $error');
      throw Exception('Impossible de créer la playlist !');
    }
  }

  /// @brief Permet d'ajouter une musique à une playlist.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param playlistId l'id de la playlist cible.
  /// @param sound L'id de la musique a ajouté.
  /// @param action l'action d'ajouter le son
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Boolean Vrai ou faux celon si la musique a été ajoutée ou non.
  static Future<bool> addSound(BuildContext context, String playlistId, String sound, String action, String token) async {
    final Map<String, String> formData = {
      'id': playlistId,
      'sound': sound,
      'action': action,
      'token': token
    };

    try {
      var data = await Network.request(formData, "/api/user/playlist", context);

      return data;
    } catch (error) {
      throw Exception('Erreur in addSound : $error');
    }
  }

  /// @brief Récupere les playlist les plus aimées de Butify.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  ///
  /// @return Future<List<Playlist>> La liste des playlists plus aimées de Butify.
  static Future<List<Playlist>> mostLiked(BuildContext context) async {
    try{
      var data = await Network.request(null, "/api/like?type=mostLikedPlaylist", context);

      List<Playlist> playlists = [];

      for(var playlistDatas in data) {
        playlists.add(Playlist(
            playlistDatas['id'].toString(),
            playlistDatas['owner'].toString(),
            playlistDatas['title'],
            playlistDatas['description'],
            playlistDatas['image'],
            playlistDatas['public'] is int
                ? playlistDatas['public']
                : int.tryParse(playlistDatas['public'].toString())
        ));
      }

      return playlists;
    } catch(error) {
      throw Exception("Erreur dans mostLiked playlist : $error");
    }
  }

  /// @brief Récupere les playlist en fonction de l'activité de l'utilisateur.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param type Le type d'activité a récuperer.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<List<Playlist>> La liste des Playlist celon l'activité de l'utilisateur.
  static byHomeActivity(BuildContext context, String token, String type) async {
    Map<String, String> formData = {
      'type': type,
      'token': token
    };

    var data = await Network.request(formData, "/api/user/activity", context);

    List<Playlist> playlists = [];

    for(var playlistDatas in data) {
      playlists.add(Playlist(
          playlistDatas['id'].toString(),
          playlistDatas['owner'].toString(),
          playlistDatas['title'],
          playlistDatas['description'],
          playlistDatas['image'],
          playlistDatas['public'] is int
              ? playlistDatas['public']
              : int.tryParse(playlistDatas['public'].toString())
      ));
    }
    return playlists;
  }
}