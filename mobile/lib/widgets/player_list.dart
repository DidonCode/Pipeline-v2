import 'package:flutter/material.dart';

import 'package:mobile/class/sound.dart';
import 'package:mobile/class/artist.dart';
import 'package:mobile/widgets/music/sound/sound_card.dart';

/// @file player_list.dart
///
/// @cond IGNORE_THIS_CLASS
class PlayerList extends StatefulWidget {
  const PlayerList({super.key,
    required this.sounds,
    required this.overlay,
    required this.showArtist,
    required this.showPlay,
    required this.selectedIndex,
    required this.token
  });

  final List<Sound> sounds;
  final OverlayEntry overlay;
  final void Function(Artist?) showArtist;
  final void Function(List<Sound>) showPlay;
  final void Function(int index) selectedIndex;
  final String token;

  @override
  State<PlayerList> createState() => _PlayerListViewState();
}

class _PlayerListViewState extends State<PlayerList> {

  List<SoundCard> soundCards = [];
  var _currentIndex = 0;

  void changeSound(List<Sound> sounds){
    int index = widget.sounds.indexOf(sounds[0]);
    widget.selectedIndex(index);
  }

  @override
  void initState(){
    super.initState();

    setState(() {
      for(var sound in widget.sounds){
        soundCards.add(
            SoundCard(
              sound: sound,
              display: soundCardDisplay.HORIZONTAL,
              showArtist: widget.showArtist,
              showPlay: changeSound,
              token: widget.token
            )
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Positioned(
      top: 150,
      width: MediaQuery.of(context).size.width,
      height: MediaQuery.of(context).size.height - 150,
      child: GestureDetector(
        onVerticalDragUpdate: (details) {
          if (details.primaryDelta! > 7) {
            setState(() {
              widget.overlay.remove();
            });
          }
        },
        child: Container(
          color: Theme.of(context).primaryColor,
          child: Column(
            children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(15, 5, 15, 5),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      height: 40,
                      decoration: BoxDecoration(
                        border: Border(
                          bottom: BorderSide(
                            color: _currentIndex == 0 ? Theme.of(context).primaryColorDark : Colors.transparent
                          ),
                        ),
                      ),
                      child: IconButton(
                        onPressed: () {
                          setState(() {
                            _currentIndex = 0;
                          });
                        },
                        icon: Text(
                            "à suivre".toUpperCase(),
                            textAlign: TextAlign.center,
                        ),
                      ),
                    ),
                    Container(
                      height: 40,
                      decoration: BoxDecoration(
                        border: Border(
                          bottom: BorderSide(
                              color: _currentIndex == 1 ? Theme.of(context).primaryColorDark : Colors.transparent
                          ),
                        ),
                      ),
                      child:IconButton(
                        onPressed: () {
                          setState(() {
                            _currentIndex = 1;
                          });
                        },
                        icon: Text(
                          "paroles".toUpperCase(),
                          textAlign: TextAlign.center,
                        ),
                      ),
                    ),
                    Container(
                      height: 40,
                      decoration: BoxDecoration(
                        border: Border(
                          bottom: BorderSide(
                              color: _currentIndex == 2 ? Theme.of(context).primaryColorDark : Colors.transparent
                          ),
                        ),
                      ),
                      child:IconButton(
                        onPressed: () {
                          setState(() {
                            _currentIndex = 2;
                          });
                        },
                        icon: Text(
                          "similaires".toUpperCase(),
                          textAlign: TextAlign.center,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              SizedBox(
                height: MediaQuery.of(context).size.height - 250,
                child: IndexedStack(
                  index: _currentIndex,
                  children: [
                    ListView(
                      scrollDirection: Axis.vertical,
                      children: soundCards
                    ),
                    Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            "En développement !".toUpperCase(),
                            style: TextStyle(
                                color: Theme.of(context).primaryColorDark,
                                decoration: TextDecoration.none,
                                fontSize: 20
                            ),
                          ),
                        ],
                      ),
                    ),
                    Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            "En développement !".toUpperCase(),
                            style: TextStyle(
                              color: Theme.of(context).primaryColorDark,
                              decoration: TextDecoration.none,
                              fontSize: 20
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
/// @endcond