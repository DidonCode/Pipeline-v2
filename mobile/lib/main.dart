import 'package:flutter/material.dart';

import 'package:mobile/Utilities/local_storage.dart';

import 'package:mobile/api/api_user.dart';

import 'package:mobile/class/artist.dart';
import 'package:mobile/class/sound.dart';
import 'package:mobile/class/playlist.dart';

import 'package:mobile/components/bottom_nav_bar.dart';

import 'package:mobile/pages/home.dart';
import 'package:mobile/pages/explore.dart';
import 'package:mobile/pages/library.dart';
import 'package:mobile/pages/music/collection.dart';
import 'package:mobile/pages/music/exposure.dart';
import 'package:mobile/pages/music/play.dart';
import 'package:mobile/pages/search.dart';
import 'package:mobile/theme.dart';

import 'package:mobile/class/user.dart';

/// @file main.dart
///
/// @cond IGNORE_THIS_CLASSs
void main() {
  runApp(const Butify());
}

class Butify extends StatelessWidget {
  const Butify({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Butify',
      darkTheme: ThemeClass.darkTheme,
      themeMode: ThemeMode.dark,
      home: const Main(),
    );
  }
}

class Main extends StatefulWidget {
  const Main({
    super.key,
  });

  @override
  State<Main> createState() => _MainState();
}

class _MainState extends State<Main> {
  int _currentIndex = 0;
  Widget? _hoverPage;
  late List<Widget> _pages;

  bool _isTokenLoading = true;

  Play? _play;
  OverlayEntry? _playOverlay;

  late User _user;

  @override
  void initState() {
    super.initState();

    _loadToken();
  }

  Future<void> disconnect(BuildContext context, String token) async {
    await ApiUser.disconnect(context, token);
  }

  Future<void> _loadToken() async {
    final data = await LocalStorage.readData(context);
    setState(() {
      Map<String, dynamic> userJson = data?['user'];

      _user = User(
          userJson['id'].toString(),
          userJson['email'],
          userJson['pseudo'],
          userJson['image'],
          userJson['public'].toString(),
          data?['token']
      );


      //disconnect(context, _user.token);

      _pages = [
        Home(
          showPlaylist: _showPlaylist,
          showArtist: _showArtist,
          showPlay: _showPlay,
          showSearch: _showSearch,
          user: _user,
          checkSession: _checkSession,
        ),
        Explore(
          showPlaylist: _showPlaylist,
          showArtist: _showArtist,
          showPlay: _showPlay,
          showSearch: _showSearch,
          artist: Artist("id", "pseudo", "image", 1),
          checkSession: _checkSession,
          user: _user,
        ),
        Library(
          showPlaylist: _showPlaylist,
          showArtist: _showArtist,
          showPlay: _showPlay,
          showSearch: _showSearch,
          user: _user,
          artist: Artist("id", "pseudo", "image", 1),
          checkSession: _checkSession,
        ),
      ];
      _isTokenLoading = false;
    });
  }

  void _onTabSelected(int index){
    setState(() {
      _currentIndex = index;
      _hoverPage = null;
    });
  }

  void _showArtist(Artist? artist){
    setState(() {
      if (artist == null) {
        _hoverPage = null;
      } else {
        _hoverPage = Exposure(
          artist: artist,
          showArtist: _showArtist,
          showPlaylist: _showPlaylist,
          showPlay: _showPlay,
          showSearch: _showSearch,
          checkSession: _checkSession,
          user: _user,
        );
      }
    });
  }

  void _showPlaylist(Playlist? Playlist){
    setState(() {
      if(Playlist == null) {
        _hoverPage = null;
      } else {
        _hoverPage = Collection(
          playlist: Playlist,
          showArtist: _showArtist,
          showPlay: _showPlay,
          showPlaylist: _showPlaylist,
          showSearch: _showSearch,
          checkSession: _checkSession,
          user: _user,
        );
      }
    });
  }

  void _showPlay(List<Sound> sound) {
    if(sound.isEmpty){
      _playOverlay?.remove();
    } else {
      setState(() {
        _play = Play(
          sounds: sound,
          showArtist: _showArtist,
          showPlay: _showPlay,
          checkSession: _checkSession,
          user: _user,
        );
        if (_playOverlay != null) _playOverlay?.remove();

        _playOverlay = OverlayEntry(
          builder: (context) => _play!,
        );

        Overlay.of(context).insert(_playOverlay!);
      });
    }
  }

  void _showSearch(bool open) {
    setState(() {
      if(open) {
        _hoverPage = null;

        return;
      }
      _hoverPage = Search(
        showArtist: _showArtist,
        showPlay: _showPlay,
        showPlaylist: _showPlaylist,
        showSearch: _showSearch,
        checkSession: _checkSession,
        user: _user,
      );
    });
  }

  Future<void> _checkSession() async {
    final isExpired = await ApiUser.isTokenExpired(context);
    if (isExpired!) {
      debugPrint("Actualisation du Token et des datas");
      await ApiUser.updateToken(context);

      await _loadToken();
    } else if (!isExpired) {
      debugPrint("30 min non atteinds");
    } else {
      debugPrint("Erreur lors de la verification de _checkSession");
    }
  }


  @override
  void dispose() {
    super.dispose();
    _playOverlay?.remove();
  }

  @override
  Widget build(BuildContext context) {
    if(_isTokenLoading) {
      return const Scaffold(
        body: Center(
          child: CircularProgressIndicator(),
        ),
      );
    }
    return Scaffold(
      body: _hoverPage ?? _pages[_currentIndex],
      bottomNavigationBar: BottomNavBar(
        currentIndex: _currentIndex,
        onTabSelected: _onTabSelected,
      ),
    );
  }
}
/// @endcond