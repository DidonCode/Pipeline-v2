import 'package:flutter/cupertino.dart';

import 'package:mobile/Utilities/network.dart';


/// @class ApiLike
///
///  @brief Cette class regroupe les fonction gérant la partie like de l'api.
///
///  @file api_like.dart
class ApiLike {

  /// @brief Récupère l'information du like ou ajoute et supprime le like en fonction de l'action
  ///
  /// @param context Le contexte de l'application, utilisé pour la navigation.
  /// @param type Le type de class demandé (ex : playlist).
  /// @param action Permet le like l'unlike et la récupération d'état du like
  /// @param id L'identifiant du type
  /// @param token Le token de l'utilisateur permettant les requêtes.
  ///
  /// @return boolean Vrai ou faux celon si le la requête a réussis et si la vérification est vrai ou fausse
  static Future<bool> like(BuildContext context, String type, int action, String id, String token) async {
      final Map<String, String> formData = {
        type : id,
        'action': action.toString(),
        'token': token,
      };

      var data = await Network.request(formData, "/api/user/like", context);
      if(data.toString() == "true") return true;

      return false;
  }
}