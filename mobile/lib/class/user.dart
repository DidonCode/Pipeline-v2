import 'package:mobile/utilities/network.dart';


/// @class User
///
/// @brief Gère les données d'un utilisateur
///
/// @file user.dart
class User {

  late String id;
  late String email;
  late String pseudo;
  late String image;
  late String public;
  late String token;

  User(String id, String email, String pseudo, String image, String public, String token){
    this.id = id;
    this.email = email;
    this.pseudo = pseudo;

    if(image.startsWith("http://localhost")) {
      this.image = image.replaceAll("localhost", Network.hostName);
    } else {
      this.image = image;
    }
    this.public = public;
    this.token = token;
  }
}