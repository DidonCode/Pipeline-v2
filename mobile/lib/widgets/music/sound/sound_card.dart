import 'package:flutter/material.dart';
import 'package:mobile/api/api_like.dart';

import 'package:mobile/api/api_artist.dart';
import 'package:mobile/api/api_playlist.dart';
import 'package:mobile/class/artist.dart';
import 'package:mobile/class/sound.dart';

enum soundCardDisplay {
  HORIZONTAL,
  VERTICAL,
}

/// @file sound_card.dart
///
/// @cond IGNORE_THIS_CLASS
class SoundCard extends StatefulWidget {
  const SoundCard({
    super.key,
    required this.display,
    required this.sound,
    required this.showArtist,
    required this.showPlay,
    required this.token,
  });

  final Sound sound;
  final soundCardDisplay? display;
  final Function(Artist?) showArtist;
  final Function(List<Sound>) showPlay;
  final String token;

  @override
  State<SoundCard> createState() => _SoundCardState();
}

class _SoundCardState extends State<SoundCard> {
  Artist? _artist;
  bool _isLoadingArtist = true;
  bool _isLiked = false;

  Offset? _position;

  @override
  void initState() {
    super.initState();

    fetchArtistData(widget.sound.artist);
    fetchLike(2, "sound", widget.sound.id, widget.token).then((likeStatus) {
      if(mounted) {
        setState(() {
          _isLiked = likeStatus;
        });
      }
    });
  }

  Future<void> fetchArtistData(String id) async {
    final results = await ApiArtist.byId(context, id);

    if (mounted) {
      setState(() {
        _artist = results;
        _isLoadingArtist = false;
      });
    }
  }

  Future<bool> fetchLike(int action, String type, String soundId, String token) async {
    final results = await ApiLike.like(context, type, action, soundId, token);
    return results;
  }

  Future<void> addSound(BuildContext context, String playlistId, String sound,
      String action, String token) async {
    final results =
        await ApiPlaylist.addSound(context, playlistId, sound, action, token);

    debugPrint(results.toString());
  }

  @override
  Widget build(BuildContext context) {
    double c_width = MediaQuery.of(context).size.width * 0.90;

    if (widget.display == soundCardDisplay.HORIZONTAL) {
      return ElevatedButton(
          onPressed: () {
            widget.showPlay([widget.sound]);
          },
          style: ElevatedButton.styleFrom(
            elevation: 0,
            backgroundColor: Colors.transparent,
            shadowColor: Colors.transparent,
            padding: EdgeInsets.zero,
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(0)),
          ),
          child: Container(
              decoration: BoxDecoration(
                  border: Border.all(color: Colors.black),
                  borderRadius: BorderRadius.circular(10)),
              margin: const EdgeInsets.all(8),
              height: 65,
              width: c_width,
              child: Row(
                children: [
                  SizedBox(
                      width: 65,
                      height: 65,
                      child: ClipRRect(
                        borderRadius: const BorderRadius.only(
                            topLeft: Radius.circular(10),
                            bottomLeft: Radius.circular(10)),
                        child: Image.network(
                          widget.sound.image,
                          fit: BoxFit.cover,
                        ),
                      )),
                  const SizedBox(width: 8),
                  Expanded(
                      child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                        Align(
                            alignment: Alignment.centerLeft,
                            child: Text(
                              widget.sound.title,
                              style: Theme.of(context).textTheme.bodyMedium,
                              overflow: TextOverflow.ellipsis,
                              maxLines: 1,
                            )),
                        const SizedBox(height: 4),
                        Container(
                          alignment: Alignment.topLeft,
                          child: TextButton(
                            style: TextButton.styleFrom(
                              padding: EdgeInsets.zero,
                              minimumSize: const Size(0, 0),
                              tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                            ),
                            onPressed: () => widget.showArtist(_artist),
                            child: Text(
                              _isLoadingArtist
                                  ? "Loading..."
                                  : (_artist?.pseudo ?? "Unknown Artist"),
                              style: Theme.of(context).textTheme.bodySmall,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ),
                      ])),
                  Container(
                    alignment: Alignment.centerRight,
                    child: GestureDetector(
                      onTapDown: (details) {
                        setState(() {
                          _position = details.globalPosition;
                        });

                        showMenu(
                          context: context,
                          position: RelativeRect.fromLTRB(
                            _position!.dx,
                            _position!.dy,
                            _position!.dx + 1,
                            _position!.dy + 1,
                          ),
                          items: [
                            PopupMenuItem(
                              child: ListTile(
                                leading: const Icon(Icons.playlist_add),
                                title: const Text('Ajouter à une playlist'),
                                onTap: () {
                                  Navigator.pop(context);
                                },
                              ),
                            ),
                            PopupMenuItem(
                              child: ListTile(
                                leading: _isLiked
                                    ? const Icon(Icons.favorite)
                                    : const Icon(Icons.favorite_border),
                                title: const Text('Ajouter aux likes'),
                                  onTap: () async {
                                    if (_isLiked) {
                                      await fetchLike(1, "sound", widget.sound.id, widget.token);
                                    } else {
                                      await fetchLike(3, "sound", widget.sound.id, widget.token);
                                    }

                                    final updatedLikeStatus = await fetchLike(2, "sound", widget.sound.id, widget.token);

                                    if (mounted) {
                                      setState(() {
                                        _isLiked = updatedLikeStatus;
                                      });
                                    }Navigator.pop(context);
                                  }
                              ),
                            )
                          ],
                        );
                      },
                      child: IconButton(
                        color: Colors.white,
                        onPressed: null,
                        icon: const Icon(Icons.more_vert),
                        style: ButtonStyle(
                          foregroundColor: MaterialStateProperty.all(
                              Colors.white), // Forcer la couleur blanche
                        ),
                      ),
                    ),
                  )
                ],
              )));
    } else if (widget.display == soundCardDisplay.VERTICAL) {
      return ElevatedButton(
          onPressed: () {
            widget.showPlay([widget.sound]);
          },
          style: ElevatedButton.styleFrom(
            elevation: 0,
            backgroundColor: Colors.transparent,
            shadowColor: Colors.transparent,
            padding: EdgeInsets.zero,
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(0)),
          ),
          child: Container(
              margin: const EdgeInsets.only(left: 10),
              child: Column(children: [
                Row(children: [
                  SizedBox(
                    height: 100,
                    child: ClipRRect(
                        borderRadius:
                            const BorderRadius.all(Radius.circular(10)),
                        child: Stack(children: [
                          Image.network(
                            widget.sound.image,
                            fit: BoxFit.cover,
                          ),
                          Positioned(
                            top: 0,
                            right: 0,
                            child: GestureDetector(
                              onTapDown: (details) {
                                setState(() {
                                  _position = details.globalPosition;
                                });

                                showMenu(
                                  context: context,
                                  position: RelativeRect.fromLTRB(
                                    _position!.dx,
                                    _position!.dy,
                                    _position!.dx + 1,
                                    _position!.dy + 1,
                                  ),
                                  items: [
                                    PopupMenuItem(
                                      child: ListTile(
                                        leading: const Icon(Icons.playlist_add),
                                        title: const Text(
                                            'Ajouter à une playlist'),
                                        onTap: () {
                                          Navigator.pop(context);
                                        },
                                      ),
                                    ),
                                    PopupMenuItem(
                                      child: ListTile(
                                          leading: _isLiked
                                              ? const Icon(Icons.favorite)
                                              : const Icon(
                                                  Icons.favorite_border),
                                          title: _isLiked
                                              ? const Text(
                                                  'Enlever des titres aimés')
                                              : const Text(
                                                  'Ajouter aux titres likés'),
                                          onTap: () async {
                                            if (_isLiked) {
                                              await fetchLike(1, "sound", widget.sound.id, widget.token);
                                            } else {
                                              await fetchLike(3, "sound", widget.sound.id, widget.token);
                                            }

                                            final updatedLikeStatus = await fetchLike(2, "sound", widget.sound.id, widget.token);

                                            if (mounted) {
                                              setState(() {
                                                _isLiked = updatedLikeStatus;
                                              });
                                            }

                                            Navigator.pop(context);
                                          }),
                                    )
                                  ],
                                );
                              },
                              child: IconButton(
                                color: Colors.white,
                                onPressed: null,
                                style: ButtonStyle(
                                  foregroundColor:
                                      MaterialStateProperty.all(Colors.white),
                                ),
                                icon: const Icon(Icons.more_vert),
                              ),
                            ),
                          )
                        ])),
                  )
                ]),
                const SizedBox(height: 8),
                Container(
                  constraints:
                      const BoxConstraints(minWidth: 80, maxWidth: 180),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          widget.sound.title,
                          style: Theme.of(context).textTheme.bodyMedium,
                          overflow: TextOverflow.ellipsis,
                          maxLines: 1,
                        ),
                        const SizedBox(height: 4),
                        TextButton(
                          style: TextButton.styleFrom(
                            padding: EdgeInsets.zero,
                            minimumSize: const Size(0, 0),
                            tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                          ),
                          onPressed: () => widget.showArtist(_artist),
                          child: Text(
                            _isLoadingArtist
                                ? "Loading..."
                                : (_artist?.pseudo ?? "Unknown Artist"),
                            style: Theme.of(context).textTheme.bodySmall,
                            overflow: TextOverflow.ellipsis,
                            maxLines: 1,
                          ),
                        ),
                      ]),
                )
              ])));
    } else {
      return const Center(
        child: Text("Erreur soundCardDisplay not defined"),
      );
    }
  }
}
/// @endcond