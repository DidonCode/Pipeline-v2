import 'package:flutter/material.dart';
import 'package:mobile/api/api_artist.dart';
import 'package:mobile/api/api_playlist.dart';
import 'package:mobile/api/api_sound.dart';
import 'package:mobile/widgets/music/artist/artist_list.dart';
import 'package:mobile/widgets/music/playlist/playlist_card.dart';
import 'package:mobile/widgets/music/playlist/playlist_list.dart';

import 'package:mobile/widgets/music/sound/sound_card.dart';
import 'package:mobile/widgets/music/sound/sound_list.dart';

import 'package:mobile/utilities/network.dart';
import 'package:mobile/class/sound.dart';
import 'package:mobile/class/artist.dart';
import 'package:mobile/class/playlist.dart';
import 'package:mobile/class/user.dart';
import 'package:mobile/widgets/profile_button.dart';
import 'package:mobile/widgets/music/artist/artist_card.dart';

/// @file home.dart
///
/// @cond IGNORE_THIS_CLASS
class Home extends StatefulWidget {
  const Home({
    super.key,
    required this.showArtist,
    required this.showPlaylist,
    required this.showPlay,
    required this.showSearch,
    required this.user,
    required this.checkSession,
  });
  final User user;
  final Function(Artist?) showArtist;
  final Function(Playlist?) showPlaylist;
  final Function(List<Sound>) showPlay;
  final Function(bool open) showSearch;
  final Function() checkSession;

  @override
  State<Home> createState() => _HomeState();
}

class _HomeState extends State<Home> {

  List<SoundCard> _recentActivity = [];
  List<SoundCard> _lastActivity = [];
  List<SoundCard> _lastLikedSounds = [];
  List<PlaylistCard> _lastLikedPlaylists = [];
  List<ArtistCard> _lastLikedArtists = [];

  List globalDatas = [];

  void updateGlobalDatas() {
    setState(() {
      globalDatas = [
        ..._recentActivity,
        ..._lastActivity,
        ..._lastLikedSounds,
        ..._lastLikedPlaylists,
        ..._lastLikedArtists
      ];
    });
  }

  Future<void> fetchActivity<T>(String type, Function(List<T>) setter) async {
    try {
      if (T == SoundCard) {
        List<Sound> results = await ApiSound.byHomeActivity(context, widget.user.token, type);
        if (mounted) {
          setState(() {
            setter(results.map((sound) => SoundCard(
                display: soundCardDisplay.VERTICAL,
                sound: sound,
                showArtist: widget.showArtist,
                showPlay: widget.showPlay,
                token: widget.user.token
            ) as T).toList());
            updateGlobalDatas();
          });
        }
      } else if (T == PlaylistCard) {
        List<Playlist> results = await ApiPlaylist.byHomeActivity(context, widget.user.token, type);
        if (mounted) {
          setState(() {
            setter(results.map((playlist) => PlaylistCard(
                playlist: playlist,
                showPlaylist: widget.showPlaylist,
              display: playlistCardDisplay.VERTICAL,
              token: widget.user.token,
            ) as T).toList());
            updateGlobalDatas();
          });
        }
      } else if (T == ArtistCard) {
        List<Artist> results = await ApiArtist.byHomeActivity(context, widget.user.token, type);
        if (mounted) {
          setState(() {
            setter(results.map((artist) => ArtistCard(
                artist: artist,
                showArtist: widget.showArtist,
              token: widget.user.token,
            ) as T).toList());
            updateGlobalDatas();
          });
        }
      }
    } catch (error) {
      debugPrint("Erreur lors de la récupération des données : $error");
    }
  }


  @override
  void initState() {
    super.initState();

    widget.checkSession();

    fetchActivity<SoundCard>("recent", (data) => _recentActivity = data);
    fetchActivity<SoundCard>("last", (data) => _lastActivity = data);
    fetchActivity<SoundCard>("sound", (data) =>  _lastLikedSounds = data);
    fetchActivity<PlaylistCard>("playlist", (data) =>  _lastLikedPlaylists = data);
    fetchActivity<ArtistCard>("artist", (data) =>  _lastLikedArtists = data);
  }

  @override
  void dispose() {
    super.dispose();
  }

/*
  Widget _buildLoading() {
    return Center(
      child: CircularProgressIndicator(),
    );
  }
*/

  @override
  Widget build(BuildContext context) {
    if (globalDatas.isEmpty) {
      return const Center(
        child: Padding(
          padding: EdgeInsets.all(20),
          child: Text(
            "Commencer à explorer pour voir votre activité récente ici",
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
        ),
      );
    }
    return Scaffold(
      appBar: AppBar(
        scrolledUnderElevation: 0.0,
        toolbarHeight: 50,
        leading: Container(
          padding: const EdgeInsets.all(8),
          child: Image.network("http://${Network.hostName}:8080/web/images/logos/logoButify.png", fit: BoxFit.cover,),
        ),
        actions: [
          IconButton(onPressed: () => widget.showSearch(false), icon: const Icon(Icons.search)),
          ProfileButton(user: widget.user, showPlay: widget.showPlay)
        ],
      ),
      body: ListView(
        children: [
          if(_recentActivity.isNotEmpty)
          SoundList(
              cards: _recentActivity,
              display: soundsListDirection.HORIZONTAL,
              listTitle: "Vos écoutes récentes",
              showArtist: widget.showArtist,
              showPlay: widget.showPlay
          ),
          if(_lastActivity.isNotEmpty)
          SoundList(
              cards: _lastActivity,
              display: soundsListDirection.HORIZONTAL,
              listTitle: "Vos anciennes écoutes",
              showArtist: widget.showArtist,
              showPlay: widget.showPlay
          ),
          if(_lastLikedSounds.isNotEmpty)
            SoundList(
                cards: _lastLikedSounds,
                display: soundsListDirection.HORIZONTAL,
                listTitle: "Vos dernières musiques aimées",
                showArtist: widget.showArtist,
                showPlay: widget.showPlay
            ),
          if(_lastLikedPlaylists.isNotEmpty)
            PlaylistList(
                cards: _lastLikedPlaylists,
                display: playlistListDirection.HORIZONTAL,
                listTitle: "Vos dernières playlists aimées",
                showPlaylist: widget.showPlaylist
            ),
          if(_lastLikedArtists.isNotEmpty)
            ArtistList(
                cards: _lastLikedArtists,
                listTitle: "Vos derniers artistes aimées",
                showArtist: widget.showArtist
            ),
        ],
      ),
    );
  }
}
/// @endcond