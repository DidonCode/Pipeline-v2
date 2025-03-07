import 'package:mobile/utilities/network.dart';

/// @class Artist
///
/// @brief Gère les données d'un artist
///
/// @file artist.dart
class Artist {

  late String id;
  late String pseudo;
  late String image;
  late int public;

  Artist(String id, String pseudo, String image, int public){
    this.id = id;
    this.pseudo = pseudo;
    if(image.startsWith("http://localhost")) {
      this.image = image.replaceAll("localhost", Network.hostName);
    } else {
        this.image = image;
      }
    this.public = public;
  }
}