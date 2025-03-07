import 'package:flutter/material.dart';
import 'package:mobile/api/api_artist.dart';
import 'package:mobile/api/api_playlist.dart';
import 'package:mobile/api/api_sound.dart';
import 'package:mobile/widgets/music/artist/artist_card.dart';
import 'package:mobile/widgets/music/artist/artist_list.dart';
import 'package:mobile/widgets/music/playlist/playlist_card.dart';
import 'package:mobile/widgets/music/playlist/playlist_list.dart';
import 'package:mobile/widgets/music/sound/sound_card.dart';
import 'package:mobile/class/sound.dart';
import 'package:mobile/class/playlist.dart';
import 'package:mobile/class/artist.dart';
import 'package:mobile/class/user.dart';
import 'package:mobile/widgets/profile_button.dart';
import 'package:mobile/widgets/music/sound/sound_list.dart';

/// @file search.dart
///
/// @cond IGNORE_THIS_CLASS
class Search extends StatefulWidget {
  const Search({
    Key? key,
    required this.showPlaylist,
    required this.showArtist,
    required this.showPlay,
    required this.checkSession,
    required this.showSearch,
    required this.user,
  }) : super(key: key);

  final Function(Playlist?) showPlaylist;
  final Function(Artist?) showArtist;
  final Function(List<Sound>) showPlay;
  final Function(bool open) showSearch;
  final Function() checkSession;
  final User user;

  @override
  State<Search> createState() => _SearchState();
}

class _SearchState extends State<Search> {
  final TextEditingController _searchController = TextEditingController();
  List<SoundCard> _soundResults = [];
  List<PlaylistCard> _playlistResults = [];
  List<ArtistCard> _artistResults = [];
  bool _isSoundLoading = false;
  bool _isPlaylistLoading = false;
  bool _isArtistLoading = false;

  Future<void> fetchSounds(String title) async {
    setState(() {
      _isSoundLoading = true;
    });

    try {
      final List<dynamic> results = await ApiSound.byTitle(context, title);

      setState(() {
        _soundResults = results.map((sound) {
          return SoundCard(
            display: soundCardDisplay.HORIZONTAL,
            sound: sound,
            showArtist: widget.showArtist,
            showPlay: widget.showPlay,
            token: widget.user.token,
          );
        }).toList();
        _isSoundLoading = false;
      });
    } catch (e) {
      debugPrint('Error fetching sounds: $e');
      setState(() {
        _isSoundLoading = false;
      });
    }
  }

  Future<void> fetchPlaylists(String title) async {
    setState(() {
      _isPlaylistLoading = true;
    });

    try {
      final results = await ApiPlaylist.byTitle(context, title);

      setState(() {
        _playlistResults = results.map((playlist) {
          return PlaylistCard(
            playlist: playlist,
            showPlaylist: widget.showPlaylist,
            display: playlistCardDisplay.HORIZONTAL,
            token: widget.user.token,
          );
        }).toList();
        _isPlaylistLoading = false;
      });
    } catch (e) {
      debugPrint('Error fetching playlists: $e');
      setState(() {
        _isPlaylistLoading = false;
      });
    }
  }

  Future<void> fetchArtists(String title) async {
    setState(() {
      _isArtistLoading = true;
    });

    try {
      final results = await ApiArtist.byTitle(context, title);

      setState(() {
        _artistResults = results.map((artist) {
          return ArtistCard(
            artist: artist,
            showArtist: widget.showArtist,
            token: widget.user.token,
          );
        }).toList();
        _isArtistLoading = false;
      });
    } catch (e) {
      debugPrint('Error fetching artists: $e');
      setState(() {
        _isArtistLoading = false;
      });
    }
  }

  @override
  void initState() {
    super.initState();
    widget.checkSession();
  }

  Widget _buildLoading() {
    return const Center(
      child: CircularProgressIndicator(),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          onPressed: () => widget.showSearch(true),
          icon: const Icon(Icons.arrow_back),
        ),
        actions: [
          ProfileButton(user: widget.user, showPlay: widget.showPlay)
        ],
        title: TextField(
          controller: _searchController,
          decoration: const InputDecoration(
            hintText: "Rechercher...",
            prefixIcon: Icon(Icons.search),
            border: InputBorder.none,
          ),
          onSubmitted: (value) {
            setState(() {
              _soundResults.clear();
              _playlistResults.clear();
              _artistResults.clear();
            });

            fetchSounds(value);
            fetchPlaylists(value);
            fetchArtists(value);
          },
        ),
      ),
      body: Stack(
        children: [
          if (_isSoundLoading || _isPlaylistLoading || _isArtistLoading)
            _buildLoading()
          else
            ListView(
              padding: const EdgeInsets.all(16.0),
              children: [
                if (_soundResults.isNotEmpty)
                  SoundList(
                    cards: _soundResults,
                    display: soundsListDirection.VERICAL,
                    listTitle: "Titres :",
                    showArtist: widget.showArtist,
                    showPlay: widget.showPlay,
                  )
                else
                  const Text("Aucun titre trouvé"),
                const SizedBox(height: 16),
                if (_playlistResults.isNotEmpty)
                  PlaylistList(
                    cards: _playlistResults,
                    display: playlistListDirection.VERTICAL,
                    listTitle: "Playlists :",
                    showPlaylist: widget.showPlaylist,
                  )
                else
                  const Text("Aucune playlist trouvée"),
                const SizedBox(height: 16),
                if (_artistResults.isNotEmpty)
                  ArtistList(
                    cards: _artistResults,
                    listTitle: "Artistes :",
                      showArtist: widget.showArtist
                    )
                else const Text("Aucun artiste trouvé"),
              ],
            ),
        ],
      ),
    );
  }
}
/// @endcond