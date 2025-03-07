import 'package:flutter/material.dart';

import 'package:mobile/api/api_like.dart';
import 'package:mobile/class/artist.dart';

/// @file artist_card.dart
///
/// @cond IGNORE_THIS_CLASSs
class ArtistCard extends StatefulWidget {
  final Artist artist;
  final Function(Artist?) showArtist;
  final String token;

  const ArtistCard({
    super.key,
    required this.artist,
    required this.showArtist,
    required this.token,
  });

  @override
  State<ArtistCard> createState() => _ArtistCardState();
}

class _ArtistCardState extends State<ArtistCard> {
  bool _isLiked = false;

  @override
  void initState() {
    super.initState();

    fetchLike(2, "artist", widget.artist.id, widget.token).then((likeStatus) {
      if(mounted) {
        setState(() {
          _isLiked = likeStatus;
        });
      }
    });
  }

  Future<bool> fetchLike(int action, String type, String soundId, String token) async {
    final results = await ApiLike.like(context, type, action, soundId, token);
    return results;
  }

  @override
  Widget build(BuildContext context) {
    return ElevatedButton(
        onPressed: () {
          widget.showArtist(widget.artist);
        },
        style: ElevatedButton.styleFrom(
          elevation: 0,
          backgroundColor: Colors.transparent,
          shadowColor: Colors.transparent,
          padding: EdgeInsets.zero,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(0),
          ),
        ),
        child: Container(
          margin: const EdgeInsets.only(left: 10),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Row(children: [
                SizedBox(
                  width: 100,
                  child: ClipRRect(
                      borderRadius: const BorderRadius.all(Radius.circular(50)),
                      child: Stack(
                        children: [
                          Image.network(
                            widget.artist.image,
                            fit: BoxFit.cover,
                          ),
                          Positioned(
                              top: 0,
                              right: 0,
                              child: IconButton(
                                color: Colors.white,
                                icon: _isLiked
                                    ? const Icon(Icons.favorite)
                                    : const Icon(Icons.favorite_border),
                                onPressed: () async {
                                  if (_isLiked) {
                                    await fetchLike(1, "artist", widget.artist.id, widget.token);
                                  } else {
                                    await fetchLike(3, "artist", widget.artist.id, widget.token);
                                  }

                                  final updatedLikeStatus = await fetchLike(2, "artist", widget.artist.id, widget.token);

                                  if (mounted) {
                                    setState(() {
                                      _isLiked = updatedLikeStatus;
                                    });
                                  }
                                },
                              )
                          )
                        ],
                      )),
                ),
              ]),
              Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  SizedBox(
                    width: 100,
                    child: Text(
                      widget.artist.pseudo,
                      style: Theme.of(context).textTheme.bodyMedium,
                      overflow: TextOverflow.ellipsis,
                      maxLines: 1,
                    ),
                  ),
                ],
              )
            ],
          ),
        ));
  }
}
/// @endcond