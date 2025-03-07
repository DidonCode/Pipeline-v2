import 'package:flutter/cupertino.dart';
import 'package:mobile/Utilities/network.dart';
import 'package:mobile/class/artist.dart';

/// @class ApiArtist
///
/// @brief Cette class regroupe toutes les fonctions permettant d'interagir avec l'API pour récuperer un ou plusieurs artistes.
/// @file api_artist.dart
class ApiArtist {

  /// @brief Récupère un artiste par son identifiant.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param id L'identifiant de l'artiste à récupérer.
  ///
  /// @return Future<Artist> Les informations de l'artiste.
  static Future<Artist> byId(BuildContext context, String id) async {
    try {
      var data = await Network.request(null, "/api/artist?id=$id", context);

      return Artist(
          data['id'].toString(),
          data['pseudo'],
          data['image'],
          data['public'] is int
              ? data['public']
              : int.tryParse(data['public'].toString())
      );

    } catch (error) {
      throw Exception("Erreur dans la requête artist by id : $error");
    }
  }

  /// @brief Récupère la liste des artistes aimés par un artiste.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param id L'identifiant de l'artiste.
  /// @param public Indicateur de visibilité publique.
  ///
  /// @return Future<List<Artist>> la liste des artistes aimés par un artiste.
  static Future<List<Artist>> likedByArtist(BuildContext context, String id) async {
    var data = await Network.request(null, "/api/like?artist=$id&type=artist", context);

    List<Artist> artists = [];

    debugPrint(data.toString());


    for (var artistDatas in data) {
      artists.add(Artist(
          artistDatas['id'].toString(),
          artistDatas['pseudo'],
          artistDatas['image'],
          artistDatas['public'] is int
              ? artistDatas['public']
              : int.tryParse(artistDatas['public'].toString())
      ));
    }

    return artists;
  }

  /// @brief Récupère la liste des artistes aimés par l'utilisateur connecté.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return Future<List<Artist>> la liste des artistes aimés par l'utilisateur connecté.
  static Future<List<Artist>> likedByUser(BuildContext context, String token) async {
    final Map<String, String> formData = {
      'type': "artist",
      'token': token,
    };

    var data = await Network.request(formData, "/api/user/like", context);
    List<Artist> artists = [];

    for (var artistDatas in data) {
      artists.add(Artist(
          artistDatas['id'].toString(),
          artistDatas['pseudo'],
          artistDatas['image'],
          artistDatas['public'] is int
              ? artistDatas['public']
              : int.tryParse(artistDatas['public'].toString())
      ));
    }

    return artists;
  }

  /// @brief Récupère la liste des artistes par pseudo.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param title Le pseudo de l'artiste recherché.
  ///
  /// @return Future<List<Artist>> la liste des artistes par pseudo.
  static Future<List<Artist>> byTitle(BuildContext context, String title) async {
    var data = await Network.request(null, "/api/artist?pseudo=$title", context);

    List<dynamic> artistsDatabase = data['database'];
    List<dynamic> artistsYoutube = data['youtube'];

    List<Artist> artists = [];

    for (var artistDatas in artistsDatabase) {
      artists.add(Artist(
          artistDatas['id'].toString(),
          artistDatas['pseudo'],
          artistDatas['image'],
          artistDatas['public'] is int
              ? artistDatas['public']
              : int.tryParse(artistDatas['public'].toString())
      ));
    }

    for (var artistDatas in artistsYoutube) {
      artists.add(Artist(
          artistDatas['id'],
          artistDatas['pseudo'],
          artistDatas['image'],
          artistDatas['public'] is int
              ? artistDatas['public']
              : int.tryParse(artistDatas['public'].toString())
      ));
    }

    return artists;
  }

  /// @brief Recherche des artistes par rapport à l'activité de l'utilisateur.
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param token Le token de l'utilisateur permettant les requêtes.
  /// @param type Le type de requête demandé (ex mostLiked).
  ///
  /// @return Future<List<Artist>> la liste d'artiste celon l'activité de l'utilisateur
  static Future<List<Artist>> byHomeActivity(BuildContext context, String token, String type) async {
    Map<String, String> formData = {
      'type': type,
      'token': token
    };

    var data = await Network.request(formData, "/api/user/activity", context);

    List<Artist> artists = [];

    for (var artistDatas in data) {
      artists.add(Artist(
          artistDatas['id'],
          artistDatas['pseudo'],
          artistDatas['image'],
          artistDatas['public'] is int
              ? artistDatas['public']
              : int.tryParse(artistDatas['public'].toString())
      ));
    }
    return artists;
  }
}
