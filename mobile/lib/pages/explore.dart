import 'package:flutter/material.dart';
import 'package:mobile/api/api_playlist.dart';
import 'package:mobile/api/api_sound.dart';
import 'package:mobile/widgets/music/playlist/playlist_card.dart';
import 'package:mobile/widgets/music/playlist/playlist_list.dart';
import 'package:mobile/widgets/music/sound/sound_card.dart';
import 'package:mobile/widgets/music/sound/sound_list.dart';

import 'package:mobile/class/sound.dart';
import 'package:mobile/class/playlist.dart';
import 'package:mobile/class/artist.dart';
import 'package:mobile/class/user.dart';
import 'package:mobile/widgets/profile_button.dart';

/// @file explore.dart
///
/// @cond IGNORE_THIS_CLASS
class Explore extends StatefulWidget {
  const Explore({
    super.key,
    required this.artist,
    required this.showPlaylist,
    required this.showArtist,
    required this.showPlay,
    required this.showSearch,
    required this.checkSession,
    required this.user
  });
  final Artist artist;
  final Function(Playlist?) showPlaylist;
  final Function(Artist?) showArtist;
  final Function(List<Sound>) showPlay;
  final Function(bool open) showSearch;
  final Function() checkSession;
  final User user;

  @override
  State<Explore> createState() => _ExploreState();
}

class _ExploreState extends State<Explore> {

  List<SoundCard> _mostListened = [];
  List<SoundCard> _mostLikedSound = [];
  List<SoundCard> _leastListened = [];
  List<PlaylistCard> _mostLikedPlaylist = [];

  Future<void> fetchMostListened(BuildContext context) async {
    final results = await ApiSound.byExplore(context, "mostListened");

    setState(() {
      _mostListened = results.map((sound) {
        return SoundCard(display: soundCardDisplay.VERTICAL, sound: sound, showArtist: widget.showArtist, showPlay: widget.showPlay, token: widget.user.token);
      }).toList();
    });
  }

  Future<void> fetchLeastListened(BuildContext context) async {
    final results = await ApiSound.byExplore(context, "leastListened");

    setState(() {
      _leastListened = results.map((sound) {
        return SoundCard(display: soundCardDisplay.VERTICAL, sound: sound, showArtist: widget.showArtist, showPlay: widget.showPlay, token: widget.user.token);
      }).toList();
    });
  }

  Future<void> fetchMostLikedSound(BuildContext context) async {
    final results = await ApiSound.byExplore(context, "mostLikedSound");

    setState(() {
      _mostLikedSound = results.map((sound) {
        return SoundCard(display: soundCardDisplay.VERTICAL, sound: sound, showArtist: widget.showArtist, showPlay: widget.showPlay, token: widget.user.token);
      }).toList();
    });
  }

  Future<void> fetchMostLikedPlaylist(BuildContext context) async {
    final results = await ApiPlaylist.mostLiked(context);

    setState(() {
      _mostLikedPlaylist = results.map((playlist) {
        return PlaylistCard(display: playlistCardDisplay.VERTICAL, playlist: playlist, token: widget.user.token, showPlaylist: widget.showPlaylist);
      }).toList();
    });
  }

  @override
  void initState() {
    super.initState();

    widget.checkSession();

    fetchMostListened(context);
    fetchLeastListened(context);
    fetchMostLikedSound(context);
    fetchMostLikedPlaylist(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        scrolledUnderElevation: 0.0,
        toolbarHeight: 50,
        leading: Container(
          padding: const EdgeInsets.all(8),
          child: Image.network("http://definity-script.fr/web/images/logos/logoButify.png", fit: BoxFit.cover,),
        ),
        actions: [
          IconButton(
            onPressed: () => widget.showSearch(false),
            icon: const Icon(Icons.search),
          ),
          ProfileButton(user: widget.user, showPlay: widget.showPlay,)
        ],
      ),
      body: ListView(
        children: [
          SoundList(cards: _mostListened,
              display: soundsListDirection.HORIZONTAL,
              listTitle: "Musiques les plus écoutées",
              showArtist: widget.showArtist,
              showPlay: widget.showPlay
          ),
          SoundList(cards: _leastListened,
              display: soundsListDirection.HORIZONTAL,
              listTitle: "Musiques émergentes",
              showArtist: widget.showArtist,
              showPlay: widget.showPlay
          ),
          SoundList(cards: _mostLikedSound,
              display: soundsListDirection.HORIZONTAL,
              listTitle: "Musiques les plus aimées",
              showArtist: widget.showArtist,
              showPlay: widget.showPlay
          ),
          PlaylistList(cards: _mostLikedPlaylist,
              display: playlistListDirection.HORIZONTAL,
              listTitle: "Playlists les plus aimées",
              showPlaylist: widget.showPlaylist
          ),
        ],
      ),
    );
  }
}
/// @endcond