import 'package:mobile/utilities/network.dart';

/// @class Sound
///
/// @brief Gère les données d'une musique
///
/// @file sound.dart
class Sound {

  late String id;
  late String title;
  late String artist;
  late int type;
  late String image;
  late String link;

  Sound(String id, String title, String artist, int type, String image, String link){
    this.id = id;
    this.title = title;
    this.artist = artist;
    this.type = type;
    if(image.startsWith("http://localhost")) {
      this.image = image.replaceAll("localhost", Network.hostName);
    } else {
      this.image = image;
    }
    if(link.startsWith("http://localhost")) {
      this.link = link.replaceAll("localhost", Network.hostName);
    } else {
      this.link = link;
    }
  }
}