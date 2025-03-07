import 'package:flutter/material.dart';
import 'package:mobile/api/api_sound.dart';
import 'package:mobile/class/artist.dart';

import 'package:mobile/api/api_artist.dart';
import 'package:mobile/api/api_like.dart';
import 'package:mobile/class/sound.dart';
import 'package:mobile/class/playlist.dart';
import 'package:mobile/class/user.dart';
import 'package:mobile/widgets/profile_button.dart';
import 'package:mobile/widgets/music/sound/sound_card.dart';

/// @file collection.dart
///
/// @cond IGNORE_THIS_CLASS
class Collection extends StatefulWidget {
  const Collection({
    super.key,
    required this.playlist,
    required this.showPlaylist,
    required this.showPlay,
    required this.showArtist,
    required this.checkSession,
    required this.showSearch,
    required this.user
  });

  final Playlist playlist;
  final Function(Playlist?) showPlaylist;
  final Function(List<Sound>) showPlay;
  final Function(Artist?) showArtist;
  final Function() checkSession;
  final Function(bool open) showSearch;
  final User user;

  @override
  State<Collection> createState() => _CollectionState();
}

class _CollectionState extends State<Collection> {

  late Playlist _currentPlaylist;
  Artist? _artist;
  User? _user;

  bool _isLiked = false;

  List<SoundCard> _playlistSoundsCards = [];
  List<Sound> _playlistSounds = [];
  bool _isLoading = true;

  Future<void> fetchUserDatas(String id) async {
    try {
      final results = await ApiArtist.byId(context, id);
        setState(() {
          _artist = results;
        });
    } catch (e) {
      debugPrint("Error fetching user data: $e");
    }
  }

  Future<void> fetchPlaylistSounds (String id) async {
    final results = await ApiSound.byPlaylist(context, id);

    setState(() {
      _playlistSoundsCards.clear();
      _playlistSounds.clear();

      _playlistSounds = results.toList();

      _playlistSoundsCards = results.map((sound) {
        return SoundCard(sound: sound, display: soundCardDisplay.HORIZONTAL, showArtist: widget.showArtist, showPlay: widget.showPlay, token: widget.user.token,);
      }).toList();

      _isLoading = false;
    });
  }

  Future<void> fetchLike(int action, String type, String soundId, String token) async {
    final results = await ApiLike.like(context, type, action, soundId, token);

    if(mounted) {
      setState(() {
        _isLiked = results;
      });
    }
  }

  @override
  void initState() {
    super.initState();

    widget.checkSession();

    _currentPlaylist = widget.playlist;

    fetchLike(2, "playlist", widget.playlist.id, widget.user.token);

    fetchUserDatas(widget.playlist.owner);
    fetchPlaylistSounds(widget.playlist.id);
  }

  Widget _buildLoading() {
    return const Center(
      child: CircularProgressIndicator(),
    );
  }

  @override
  Widget build(BuildContext context) {
    if(_isLoading) return _buildLoading();
        return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded),
          onPressed: () {
            widget.showPlaylist(null);
          },
        ),
        actions: [
          IconButton(onPressed: () => widget.showSearch(false), icon: const Icon(Icons.search)),
          ProfileButton(user: widget.user, showPlay: widget.showPlay)
        ],
      ),
        body: ListView(children: [
      Column(children: [
        Container(
          margin: const EdgeInsets.only(top: 30),
          child: Column(
            children: [
              SizedBox(
                width: 200,
                height: 200,
                child: Image.network(
                  _currentPlaylist.image,
                  fit: BoxFit.cover,
                ),
              ),
              const SizedBox(height: 16),
              Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    _currentPlaylist.title,
                    style: const TextStyle(
                      fontSize: 25,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  TextButton(
                    style: TextButton.styleFrom(
                      padding: EdgeInsets.zero,
                      minimumSize: const Size(0, 0),
                      tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                    ),
                    onPressed: () {
                      if (_artist != null) widget.showArtist(_artist);
                    },
                    child: Text(
                      _user?.pseudo ?? _artist?.pseudo ?? 'Unknown Owner',
                      //widget.artist.pseudo,
                      style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w400,
                          color: Colors.black
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(height: 8),
                    SizedBox(
                      width: MediaQuery.of(context).size.width -50,
                      child: Text(
                        _currentPlaylist.description,
                        textAlign: TextAlign.center,
                        overflow: TextOverflow.ellipsis,
                        maxLines: 6,
                      ),
                    )
                ],
              ),
            ],
          ),
        ),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            IconButton(onPressed: () {
              _isLiked? fetchLike(1, "playlist", widget.playlist.id, widget.user.token) : fetchLike(3, "playlist", widget.playlist.id, widget.user.token);

              fetchLike(2, "playlist", widget.playlist.id, widget.user.token);
            },
                icon: _isLiked? const Icon(Icons.favorite) : const Icon(Icons.favorite_border)
            ),
            IconButton(onPressed: () {}, icon: const Icon(Icons.shuffle)),
            IconButton(
              onPressed: () => widget.showPlay(_playlistSounds),
              icon: const Icon(Icons.play_arrow),
            ),
            IconButton(
              onPressed: () { },
              icon: const Icon(Icons.more_vert),
            ),

            // Les icones suivantes seront à utiliser dans la pop up du more_vert
              //IconButton(onPressed: () {}, icon: Icon(Icons.create_rounded)),
              //IconButton(onPressed: () {}, icon: Icon(Icons.share)),
              //IconButton(onPressed: () {}, icon: Icon(Icons.delete)),
          ],
        ),
        ..._playlistSoundsCards
      ])
    ]));
  }
}
/// @endcond