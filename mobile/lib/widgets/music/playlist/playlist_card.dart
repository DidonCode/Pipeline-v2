import 'package:flutter/material.dart';

import 'package:mobile/api/api_like.dart';
import 'package:mobile/class/playlist.dart';

enum playlistCardDisplay { HORIZONTAL, VERTICAL }

/// @file playlist_card.dart
///
/// @cond IGNORE_THIS_CLASS
class PlaylistCard extends StatefulWidget {
  final Playlist playlist;
  final Function(Playlist?) showPlaylist;
  final playlistCardDisplay display;
  final String token;

  const PlaylistCard(
      {super.key,
      required this.playlist,
      required this.showPlaylist,
      required this.display,
      required this.token});

  @override
  State<PlaylistCard> createState() => _PlaylistCardState();
}

class _PlaylistCardState extends State<PlaylistCard> {
  bool _isLiked = false;

  Future<bool> fetchLike(int action, String type, String soundId, String token) async {
    final results = await ApiLike.like(context, type, action, soundId, token);
    return results;
  }

  @override
  void initState() {
    super.initState();

    fetchLike(2, "playlist", widget.playlist.id, widget.token).then((likeStatus) {
      if(mounted) {
        setState(() {
          _isLiked = likeStatus;
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    if (widget.display == playlistCardDisplay.HORIZONTAL) {
      double c_width = MediaQuery.of(context).size.width * 0.95;
      return ElevatedButton(
          onPressed: () {
            widget.showPlaylist(widget.playlist);
          },
          style: ElevatedButton.styleFrom(
            elevation: 0,
            backgroundColor: Colors.transparent,
            shadowColor: Colors.transparent,
            padding: EdgeInsets.zero,
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(0)),
          ),
          child:
              Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            SizedBox(
              height: 75,
              width: c_width,
              child: Column(children: [
                Row(
                  children: [
                    SizedBox(
                      width: 75,
                      height: 75,
                      child: Image.network(
                        widget.playlist.image,
                        fit: BoxFit.cover,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                        child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          widget.playlist.title,
                          style: Theme.of(context).textTheme.bodyMedium,
                          maxLines: 1,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          widget.playlist.description,
                          style: Theme.of(context).textTheme.bodyMedium,
                          overflow: TextOverflow.ellipsis,
                          maxLines: 2,
                        ),
                      ],
                    )),
                    Container(
                        alignment: Alignment.centerRight,
                        child: IconButton(
                          color: Colors.white,
                          icon: _isLiked
                              ? const Icon(Icons.favorite)
                              : const Icon(Icons.favorite_border),
                          onPressed: () async {
                            if (_isLiked) {
                              await fetchLike(1, "playlist", widget.playlist.id, widget.token);
                            } else {
                              await fetchLike(3, "playlist", widget.playlist.id, widget.token);
                            }

                            final updatedLikeStatus = await fetchLike(2, "playlist", widget.playlist.id, widget.token);

                            if (mounted) {
                              setState(() {
                                _isLiked = updatedLikeStatus;
                              });
                            }
                          },
                        ))
                  ],
                ),
              ]),
            ),
            const SizedBox(height: 8)
          ]));
    } else if (widget.display == playlistCardDisplay.VERTICAL) {
      return ElevatedButton(
          onPressed: () {
            widget.showPlaylist(widget.playlist);
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
                  Container(
                    constraints: const BoxConstraints(minHeight: 100),
                    child: ClipRRect(
                        borderRadius:
                            const BorderRadius.all(Radius.circular(10)),
                        child: Stack(
                          children: [
                            Image.network(
                              width: 100,
                              height: 100,
                              widget.playlist.image,
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
                                      await fetchLike(1, "playlist", widget.playlist.id, widget.token);
                                    } else {
                                      await fetchLike(3, "playlist", widget.playlist.id, widget.token);
                                    }

                                    final updatedLikeStatus = await fetchLike(2, "playlist", widget.playlist.id, widget.token);

                                    if (mounted) {
                                      setState(() {
                                        _isLiked = updatedLikeStatus;
                                      });
                                    }
                                  },
                                ))
                          ],
                        )),
                  ),
                ]),
                const SizedBox(height: 8),
                SizedBox(
                  width: 100,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        widget.playlist.title,
                        style: Theme.of(context).textTheme.bodyMedium,
                        overflow: TextOverflow.ellipsis,
                        maxLines: 1,
                      ),
                      const SizedBox(height: 4),
                      Text(
                        widget.playlist.description,
                        style: Theme.of(context).textTheme.bodySmall,
                        overflow: TextOverflow.ellipsis,
                        maxLines: 2,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ));
    } else {
      return const Center(
          child: Text("You need to define a playlistCard display"));
    }
  }
}
/// @endcond
