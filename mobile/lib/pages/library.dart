import 'package:flutter/material.dart';
import 'package:mobile/api/api_artist.dart';
import 'package:mobile/api/api_playlist.dart';
import 'package:mobile/api/api_sound.dart';
import 'package:mobile/class/artist.dart';
import 'package:mobile/class/playlist.dart';

import 'package:mobile/widgets/music/artist/artist_list.dart';
import 'package:mobile/widgets/music/playlist/playlist_card.dart';
import 'package:mobile/widgets/music/playlist/playlist_list.dart';
import 'package:mobile/widgets/music/sound/sound_card.dart';
import 'package:mobile/widgets/music/sound/sound_list.dart';

import 'package:mobile/class/sound.dart';
import 'package:mobile/class/user.dart';
import 'package:mobile/widgets/profile_button.dart';
import 'package:mobile/widgets/music/artist/artist_card.dart';

/// @file library.dart
///
/// @cond IGNORE_THIS_CLASS
class Library extends StatefulWidget {
  const Library({
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
  State<Library> createState() => _LibraryState();
}

class _LibraryState extends State<Library> {

  late TextEditingController _playlistController;
  late TextEditingController _descriptionController;
  String _privacy = "0";

  List<PlaylistCard> _playlistResults = [];
  List<SoundCard> _likedSoundsResults = [];
  List<PlaylistCard> _likedPlaylistsResults = [];
  List<ArtistCard> _likedArtistsResults = [];
  Playlist? _createdPlaylist;

  List globalDatas = [];

  bool _isLoading = true;

  void updateGlobalDatas() {
    setState(() {
      globalDatas = [
        ..._playlistResults,
        ..._likedSoundsResults,
        ..._likedPlaylistsResults,
        ..._likedArtistsResults,
      ];
    });
  }

  Future<void> fetchUserPlaylists(String token) async {
    final results = await ApiPlaylist.byUser(context, token);

    setState(() {
      _playlistResults.clear();
      _playlistResults = results.map((playlist) {
        return PlaylistCard(playlist: playlist, showPlaylist: widget.showPlaylist, display: playlistCardDisplay.VERTICAL, token: widget.user.token);

      }).toList();
      _isLoading = false;
      updateGlobalDatas();
    });
  }

  Future<void> fetchLikedSounds(String token) async {
    final results = await ApiSound.likedByUser(context, token);

    setState(() {
      _likedSoundsResults.clear();
      _likedSoundsResults = results.map((sound) {
        return SoundCard(sound: sound, display: soundCardDisplay.VERTICAL, showArtist: widget.showArtist, showPlay: widget.showPlay, token: widget.user.token);

      }).toList();
      _isLoading = false;
    });
  }

  Future<void> fetchLikedPlaylists(String token) async {
    final results = await ApiPlaylist.likedByUser(context, token);

    setState(() {
      _likedPlaylistsResults.clear();
      _likedPlaylistsResults = results.map((playlist) {
        return PlaylistCard(playlist: playlist, showPlaylist: widget.showPlaylist, display: playlistCardDisplay.VERTICAL, token: widget.user.token);
      }).toList();
      _isLoading = false;
      updateGlobalDatas();
    });
  }

  Future<void> fetchLikedArtists(String token) async {
    final results = await ApiArtist.likedByUser(context, token);

    setState(() {
      _likedArtistsResults.clear();
      _likedArtistsResults = results.map((artist) {
        return ArtistCard(artist: artist, showArtist: widget.showArtist, token: widget.user.token,);
      }).toList();
      _isLoading = false;
      updateGlobalDatas();
    });
  }

  Future<void> createPlaylistResults(String title, String description, String public, String token) async {
    final results = await ApiPlaylist.create(context, title, description, public, token);

    setState(() {
      _createdPlaylist = results;
    });
  }

  @override
  void initState() {
    super.initState();

    widget.checkSession();
    
    _playlistController = TextEditingController();
    _descriptionController = TextEditingController();

    fetchUserPlaylists(widget.user.token);
    fetchLikedSounds(widget.user.token);
    fetchLikedPlaylists(widget.user.token);
    fetchLikedArtists(widget.user.token);
  }

  @override
  void dispose() {
    _playlistController.dispose();
    _descriptionController.dispose();

    super.dispose();
  }

  Widget _buildLoading() {
    return Row(children: [
      CircularProgressIndicator(),
    ],);
  }

  @override
  Widget build(BuildContext context) {
    if(_isLoading) return _buildLoading();
    return Scaffold(
      appBar: AppBar(
        scrolledUnderElevation: 0.0,
        toolbarHeight: 50,
        leading: Container(
          padding: const EdgeInsets.all(8),
          child: Image.network("http://definity-script.fr/web/images/logos/logoButify.png", fit: BoxFit.cover,),
        ),
        actions: [
          IconButton(onPressed: () => widget.showSearch(false), icon: const Icon(Icons.search)),
          ProfileButton(user: widget.user, showPlay: widget.showPlay)
        ],
      ),
      body: ListView(
        children: [
          if(globalDatas.isEmpty)
            const Center(
              child: Padding(
                padding: EdgeInsets.all(20),
                child: Text(
                  "Vous trouverez vos playlists crées ici ainsi que tous votre contenu aimée",
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
              ),
            )
          else ...[
          if(_playlistResults.isNotEmpty)
          PlaylistList(cards: _playlistResults,
              display: playlistListDirection.HORIZONTAL,
              listTitle: "Vos playlist",
              showPlaylist: widget.showPlaylist,
          ),
          if(_likedSoundsResults.isNotEmpty)
          SoundList(cards: _likedSoundsResults,
              display: soundsListDirection.HORIZONTAL,
              listTitle: "Vos musiques aimées",
              showArtist: widget.showArtist,
              showPlay: widget.showPlay,
          ),
          if(_likedPlaylistsResults.isNotEmpty)
          PlaylistList(cards: _likedPlaylistsResults,
              display: playlistListDirection.HORIZONTAL,
              listTitle: "Vos playlists aimées",
              showPlaylist: widget.showPlaylist,
          ),
          if(_likedArtistsResults.isNotEmpty)
          ArtistList(cards: _likedArtistsResults,
              listTitle: "Vos artistes aimées",
              showArtist: widget.showArtist
          ),
          ]
        ],
      ),
      floatingActionButton: FloatingActionButton.small(
        onPressed: () {
          _createPlaylist();
        },
        child: const Icon(Icons.add),
      ),
    );
  }

  Future<void> _createPlaylist() async {
    final result = await showDialog<String>(
      context: context,
      builder: (context) =>
          AlertDialog(
            title: const Text('Créer une playlist'),
            content: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  TextField(
                    autofocus: true,
                    decoration: const InputDecoration(
                        hintText: 'titre *'),
                    controller: _playlistController,
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    decoration: const InputDecoration(
                        hintText: 'Description'),
                    controller: _descriptionController,
                  ),
                  DropdownButton<String>(
                    items: const [
                      DropdownMenuItem(value: "0", child: Text("Privée"),),
                      DropdownMenuItem(value: "1", child: Text("public"),)
                    ],
                    value: _privacy,
                    onChanged: (value) {
                      setState(() {
                        _privacy = value!;
                      });
                    },
                    iconSize: 30,
                    isExpanded: true,
                  )
                ],
              ),
            ),
            actions: [
              TextButton(
                onPressed: () {
                  createPlaylistResults(
                      _playlistController.text, _descriptionController.text,
                      _privacy, widget.user.token);
                  Navigator.pop(context);
                  widget.showPlaylist(_createdPlaylist);
                },
                child: const Text('Créer la playlist'),
              ),
            ],
          ),
    );
  }
}
/// @cond IGNORE_THIS_CLASS