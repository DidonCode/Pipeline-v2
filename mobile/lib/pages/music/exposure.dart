import 'package:flutter/material.dart';
import 'package:mobile/api/api_artist.dart';
import 'package:mobile/api/api_playlist.dart';
import 'package:mobile/api/api_sound.dart';
import 'package:mobile/widgets/music/artist/artist_card.dart';
import 'package:mobile/widgets/music/artist/artist_list.dart';
import 'package:mobile/widgets/music/playlist/playlist_card.dart';
import 'package:mobile/widgets/music/playlist/playlist_list.dart';
import 'package:mobile/widgets/music/sound/sound_card.dart';
import 'package:mobile/class/artist.dart';
import 'package:mobile/class/playlist.dart';
import 'package:mobile/class/sound.dart';
import 'package:mobile/class/user.dart';
import 'package:mobile/widgets/profile_button.dart';
import 'package:mobile/widgets/music/sound/sound_list.dart';

/// @file exposure.dart
///
/// @cond IGNORE_THIS_CLASS
class Exposure extends StatefulWidget {
  const Exposure({
    super.key,
    required this.artist,
    required this.showArtist,
    required this.showPlaylist,
    required this.showPlay,
    required this.checkSession,
    required this.showSearch,
    required this.user,
  });

  final Artist artist;
  final Function(Artist?) showArtist;
  final Function(Playlist?) showPlaylist;
  final Function(List<Sound>) showPlay;
  final Function(bool open) showSearch;
  final Function() checkSession;
  final User user;

  @override
  State<Exposure> createState() => _ExposureState();
}

class _ExposureState extends State<Exposure> {
  Artist _artistDatas = Artist("1", "Loading ...", "Loading ...", 1);
  List<SoundCard> _artistSounds = [];
  List<PlaylistCard> _artistPlaylists = [];
  List<SoundCard> _likedSounds = [];
  List<PlaylistCard> _likedPlaylists = [];
  List<ArtistCard> _likedArtists = [];

  List globalDatas = [];

  @override
  void initState() {
    super.initState();

    widget.checkSession();
    _initializeData();
  }

  Future<void> _initializeData() async {
    try {
      fetchArtistData();
      await fetchArtistSounds();
    } catch (error) {
      throw Exception("Erreur lors du chargement des données. : $error");
    }
  }

  void updateGlobalDatas() {
    setState(() {
      globalDatas = [
        ..._artistSounds,
        ..._artistPlaylists,
        ..._likedSounds,
        ..._likedPlaylists,
        ..._likedArtists
      ];
    });
  }

  Future<void> fetchArtistData() async {
    try {
      Artist artistDatas = await ApiArtist.byId(context, widget.artist.id);
      setState(() {
        _artistDatas = artistDatas;
      });
    } catch (error) {
      throw Exception("erreur : $error");
    }
  }

  Future<void> fetchArtistSounds() async {
    try {
      List<Sound> artistSounds =
          await ApiSound.byArtist(context, widget.artist.id);
      List<Playlist> artistPlaylists =
          await ApiPlaylist.byArtist(context, widget.artist.id);

      List<Sound> likedSounds = [];
      List<Playlist> likedPlaylists = [];
      List<Artist> likedArtists = [];

      if (widget.artist.public == 1) {
        debugPrint("test");

        likedSounds = await ApiSound.likedByArtist(context, widget.artist.id);
        likedPlaylists =
            await ApiPlaylist.likedByArtist(context, widget.artist.id);
        likedArtists = await ApiArtist.likedByArtist(context, widget.artist.id);
      }

      setState(() {
        _artistSounds = artistSounds.map((sound) {
          return SoundCard(
            sound: sound,
            display: soundCardDisplay.HORIZONTAL,
            showArtist: widget.showArtist,
            showPlay: widget.showPlay,
            token: widget.user.token,
          );
        }).toList();

        _artistPlaylists = artistPlaylists.map((playlist) {
          return PlaylistCard(
            playlist: playlist,
            showPlaylist: widget.showPlaylist,
            display: playlistCardDisplay.VERTICAL,
            token: widget.user.token,
          );
        }).toList();

        _likedSounds = likedSounds.map((sound) {
          return SoundCard(
            sound: sound,
            display: soundCardDisplay.VERTICAL,
            showArtist: widget.showArtist,
            showPlay: widget.showPlay,
            token: widget.user.token,
          );
        }).toList();

        _likedPlaylists = likedPlaylists.map((playlist) {
          return PlaylistCard(
            playlist: playlist,
            showPlaylist: widget.showPlaylist,
            display: playlistCardDisplay.VERTICAL,
            token: widget.user.token,
          );
        }).toList();

        _likedArtists = likedArtists.map((artist) {
          return ArtistCard(
            artist: artist,
            showArtist: widget.showArtist,
            token: widget.user.token,
          );
        }).toList();

        updateGlobalDatas();
      });
    } catch (error) {
      setState(() {
        //_errorMessage = "Erreur lors du chargement des sons ou playlists.";
      });
    }
  }

  Widget _buildHeader() {
    return Stack(
      children: [
        SizedBox(
          height: 250,
          width: double.infinity,
          child: Image.network(
            _artistDatas.image,
            fit: BoxFit.cover,
            errorBuilder: (context, error, stackTrace) => const Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.error, size: 50, color: Colors.red),
                Text("Image non disponible",
                    style: TextStyle(color: Colors.red)),
              ],
            ),
          ),
        ),
        Positioned(
          left: 10,
          bottom: 10,
          child: Row(
            children: [
              Text(
                _artistDatas.pseudo,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 26,
                  fontWeight: FontWeight.bold,
                  shadows: [
                    Shadow(
                        blurRadius: 10,
                        color: Colors.black,
                        offset: Offset(2, 2)),
                  ],
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    //if (_isLoading) return _buildLoading();
    //if (_errorMessage != null) return _buildError(_errorMessage!);

    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded),
          onPressed: () {
            widget.showArtist(null);
          },
        ),
        actions: [
          IconButton(
            onPressed: () => widget.showSearch(false),
            icon: const Icon(Icons.search),
          ),
          ProfileButton(user: widget.user, showPlay: widget.showPlay,)
        ],
      ),
      body: SingleChildScrollView(
        child: Column(children: [
          _buildHeader(),
          const SizedBox(height: 16),
          if (globalDatas.isEmpty)
              const Center(
                child: Text("L'artist n'a aucun contenu"),
              )
          else ...[
            if (_artistSounds.isNotEmpty)
              SoundList(
                cards: _artistSounds,
                display: soundsListDirection.HORIZONTAL3,
                listTitle: "Titres :",
                showArtist: widget.showArtist,
                showPlay: widget.showPlay,
              ),
            if (_artistPlaylists.isNotEmpty)
              PlaylistList(
                cards: _artistPlaylists,
                display: playlistListDirection.HORIZONTAL,
                listTitle: "Playlists :",
                showPlaylist: widget.showPlaylist,
              ),
            if (_likedSounds.isNotEmpty)
              SoundList(
                cards: _likedSounds,
                display: soundsListDirection.HORIZONTAL,
                listTitle: "Titres aimés :",
                showArtist: widget.showArtist,
                showPlay: widget.showPlay,
              ),
            if (_likedPlaylists.isNotEmpty)
              PlaylistList(
                cards: _likedPlaylists,
                display: playlistListDirection.HORIZONTAL,
                listTitle: "Playlists aimées :",
                showPlaylist: widget.showPlaylist,
              ),
            if (_likedArtists.isNotEmpty)
              ArtistList(
                cards: _likedArtists,
                listTitle: "Artistes aimés par :",
                showArtist: widget.showArtist,
              ),
          ],
        ]),
      ),
    );
  }
}
/// @endcond