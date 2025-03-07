import 'package:mobile/utilities/network.dart';

/// @class Playlist
///
/// @brief Gère les données d'une playlist
///
/// @file playlist.dart
class Playlist {

  late String id;
  late String owner;
  late String title;
  late String description;
  late String image;
  late int public;

  Playlist(String id, String owner, String title, String description, String image, int public){
    this.id = id;
    this.owner = owner;
    this.title = title;
    this.description = description;
    if(image.startsWith("http://localhost")) {
      this.image = image.replaceAll("localhost", Network.hostName);
    } else {
      this.image = image;
    }
    this.public = public;
  }
}