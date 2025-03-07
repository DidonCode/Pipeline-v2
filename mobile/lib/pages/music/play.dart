import 'dart:math';

import 'package:flutter/material.dart';
import 'package:hexcolor/hexcolor.dart';
import 'package:just_audio/just_audio.dart';
import 'package:mobile/api/api_artist.dart';
import 'package:mobile/api/api_player.dart';
import 'package:mobile/widgets/player_list.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';

import 'package:mobile/class/artist.dart';
import 'package:mobile/class/sound.dart';
import 'package:mobile/class/user.dart';

/// @file play.dart
///
/// @cond IGNORE_THIS_CLASS
class Play extends StatefulWidget {
  Play({
    super.key,
    required this.sounds,
    required this.showArtist,
    required this.showPlay,
    required this.checkSession,
    required this.user,
    this.minimised = false
  });

  final List<Sound> sounds;
  final void Function(Artist?) showArtist;
  final void Function(List<Sound>) showPlay;
  final Function() checkSession;
  final User user;
  bool minimised;

  @override
  State<Play> createState() => _PlayState();
}

class _PlayState extends State<Play> {
  bool isThumbActive = false;

  YoutubePlayerController? youtubeController;
  KeyedSubtree? youtubePlayer;

  AudioPlayer? butifyController;
  Widget? butifyPlayer;

  Artist? _currentArtist;

  int _currentSound = 0;
  double _currentPosition = 0;
  double _videoDuration = 0;

  bool _isPlaying = false;
  bool _isRepeat = false;
  bool _isRandom = false;

  final GlobalKey _playerKey = GlobalKey();

  OverlayEntry? _soundsList;

  void _showSoundsList(BuildContext context) {
    _soundsList = OverlayEntry(
      builder: (context) => PlayerList(
        sounds: widget.sounds,
        overlay: _soundsList!,
        showArtist: widget.showArtist,
        showPlay: widget.showPlay,
        selectedIndex: _selectedIndex,
        token: widget.user.token
      ),
    );

    Overlay.of(context).insert(_soundsList!);
  }

  Future<void> loadRecommendation() async {
    List<Sound> sounds = await ApiPlayer.getRecommendation(context, widget.sounds.elementAt(0), widget.user.token);
    setState(() {
      widget.sounds.addAll(sounds);
    });
  }

  Future<void> loadSound(Sound sound) async {
    _currentArtist = await ApiArtist.byId(context, widget.sounds.elementAt(_currentSound).artist);
    ApiPlayer.addActivity(context, sound, widget.user.token);

    if(double.tryParse(sound.id) != null) {
      setState(() {
        if (butifyController == null) {
          butifyController = AudioPlayer();

          butifyPlayer = Image.network(
            sound.image,
            fit: BoxFit.cover,
          );

          butifyController!.durationStream.listen((duration) {
            setState(() {
              if (duration != null) _videoDuration = duration.inSeconds.toDouble();
            });
          });

          butifyController!.positionStream.listen((position) {
            setState(() {
              _currentPosition = position.inSeconds.toDouble();

              if(_currentPosition > 0 && _videoDuration > 0 && _currentPosition >= _videoDuration - 1){
                _nextSound();
              }
            });
          });
        }else{
          butifyController!.seek(const Duration(seconds: 0));
        }
      });

      await butifyController!.setUrl("${sound.link}");
    }
    else{
      if(youtubeController == null) {
        setState(() {
          youtubeController = YoutubePlayerController(
            initialVideoId: sound.id,
            flags: const YoutubePlayerFlags(
              controlsVisibleAtStart: false,
              autoPlay: true,
              mute: false,
              hideControls: true,
              disableDragSeek: true,
              enableCaption: false,
            ),
          );

          youtubePlayer = KeyedSubtree(
              key: _playerKey,
              child: YoutubePlayer(
                  controller: youtubeController!
              )
          );

          youtubeController!.addListener(() {
            setState(() {
              _currentPosition = youtubeController!.value.position.inSeconds.toDouble();
              _videoDuration = youtubeController!.metadata.duration.inSeconds.toDouble();
              _isPlaying = youtubeController!.value.isPlaying;

              if(_currentPosition > 0 && _videoDuration > 0 && _currentPosition >= _videoDuration - 1){
                _nextSound();
              }
            });
          });
        });
      }
      else{
        youtubeController!.load(sound.id);
        youtubeController!.seekTo(const Duration(seconds: 0));
      }
    }
  }

  Future<void> _play() async {
    if(butifyController != null) {
      await butifyController!.play();
      setState(() { _isPlaying = true; });
    }

    if(youtubeController != null) youtubeController!.play();
  }

  Future<void> _pause() async {
    if(butifyController != null) {
      await butifyController!.pause();
      setState(() { _isPlaying = false; });
    }

    if(youtubeController != null) youtubeController!.pause();
  }

  void _seekTo(Duration position){
    if(butifyController != null) butifyController!.seek(position);
    if(youtubeController != null) youtubeController!.seekTo(position);
  }

  void _nextSound(){
    setState(() {
      if(_isRepeat) {
        loadSound(widget.sounds.elementAt(_currentSound));
        return;
      }

      if(_isRandom) {
        _currentSound = Random().nextInt(widget.sounds.length);
        loadSound(widget.sounds.elementAt(_currentSound));
        return;
      }

      if(widget.sounds.isNotEmpty){
        if (_currentSound >= widget.sounds.length - 1) {
          _currentSound = 0;
        } else {
          _currentSound += 1;
        }

        loadSound(widget.sounds.elementAt(_currentSound));
      }
    });
  }

  void _previousSound(){
    setState(() {
      if(widget.sounds.isNotEmpty){
        if(_currentSound <= 0) {
          _currentSound = widget.sounds.length - 1;
        }else{
          _currentSound -= 1;
        }

        loadSound(widget.sounds.elementAt(_currentSound));
      }
    });
  }

  void _selectedIndex(index){
    loadSound(widget.sounds.elementAt(index));
  }

  String formatDuration(Duration duration) {
    String twoDigits(int n) => n.toString().padLeft(2, '0');
    final hours = twoDigits(duration.inHours);
    final minutes = twoDigits(duration.inMinutes.remainder(60));
    final seconds = twoDigits(duration.inSeconds.remainder(60));
    return duration.inHours > 0 ? "$hours:$minutes:$seconds" : "$minutes:$seconds";
  }

  @override
  void initState() {
    super.initState();

    widget.checkSession();

    if(widget.sounds.length == 1) loadRecommendation();

    setState(() {
      loadSound(widget.sounds.first);
      _play();
    });
  }

  @override
  void dispose() {
    super.dispose();

    if(butifyController != null) butifyController!.dispose();
    if(youtubeController != null) youtubeController!.dispose();
  }

  @override
  Widget build(BuildContext context){
    if(widget.minimised){
      return Positioned(
        bottom: 55,
        width: MediaQuery.of(context).size.width,
        child: ElevatedButton(
          onPressed: () {
            setState(() {
              widget.minimised = false;
            });
          },
          style: ElevatedButton.styleFrom(
            elevation: 0,
            backgroundColor: Colors.transparent,
            padding: EdgeInsets.zero,
            shadowColor: Colors.transparent,
            minimumSize: const Size(0, 0),
            tapTargetSize: MaterialTapTargetSize.shrinkWrap,
          ),
          child: Container(
            color: Theme.of(context).primaryColor,
            height: 60,
            child: Column(
              children: [
                Offstage(
                  child: youtubePlayer,
                ),
                Padding(
                  padding: const EdgeInsets.fromLTRB(15, 5, 0, 5),
                  child: Row(
                    children: [
                      ClipRRect(
                        borderRadius: BorderRadius.circular(10),
                        child: Image.network(
                          widget.sounds.elementAt(_currentSound).image,
                          fit: BoxFit.cover,
                          height: 40,
                          width: 40,
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.fromLTRB(15, 0, 0, 0),
                        child: SizedBox(
                          width: MediaQuery.of(context).size.width - 130,
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                widget.sounds.elementAt(_currentSound).title,
                                overflow: TextOverflow.ellipsis,
                                style: TextStyle(
                                  color: Theme.of(context).primaryColorDark,
                                  fontSize: 14,
                                  decoration: TextDecoration.none,
                                ),
                              ),
                              Text(
                                _currentArtist!.pseudo,
                                style: const TextStyle(
                                  color: Colors.grey,
                                  fontSize: 14,
                                  decoration: TextDecoration.none,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      const Spacer(),
                      IconButton(
                        onPressed: () {
                          setState(() {
                            _isPlaying ? _pause() : _play();
                          });
                        },
                        icon: Icon(
                          _isPlaying ? Icons.pause : Icons.play_arrow,
                          color: Theme.of(context).primaryColorDark,
                          size: 30,
                        ),
                      ),
                    ],
                  ),
                ),
                Stack(
                  children: [
                    Container(
                      height: 2,
                      decoration: BoxDecoration(
                        color: Theme.of(context).primaryColorDark,
                        borderRadius: BorderRadius.circular(0),
                      ),
                    ),
                    FractionallySizedBox(
                      widthFactor: _currentPosition == 0 ? 0 : _currentPosition / _videoDuration,
                      child: Container(
                        height: 2,
                        decoration: BoxDecoration(
                          color: Theme.of(context).highlightColor,
                          borderRadius: BorderRadius.circular(0),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        backgroundColor: Theme.of(context).primaryColor,
        leading: IconButton(
          onPressed: (){
            setState(() {
              widget.minimised = true;
            });
          },
          icon: Icon(
            Icons.keyboard_arrow_down,
            color: Theme.of(context).primaryColorDark,
            size: 25,
          ),
        ),
        actions: [
          IconButton(
            onPressed: (){

            },
            icon: Icon(
              Icons.more_vert,
              color: Theme.of(context).primaryColorDark,
            ),
          ),
        ],
      ),
      backgroundColor: Theme.of(context).primaryColor,
      body: GestureDetector(
        onVerticalDragUpdate: (details) {
          if (details.primaryDelta! > 7) {
            setState(() {
              //widget.overlay?.remove();
              widget.minimised = true;
            });
          }
        },
        child: Stack(
          fit: StackFit.expand,
          children: [
            Align(
              alignment: Alignment.bottomCenter,
              child: SizedBox(
                width: MediaQuery.of(context).size.width - 50.0,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    IconButton(
                      onPressed: () {
                        _showSoundsList(context);
                      },
                      icon: Text("à suivre".toUpperCase()),
                    ),
                    IconButton(
                      onPressed: () {
                        _showSoundsList(context);
                      },
                      icon: Text("paroles".toUpperCase()),
                    ),
                    IconButton(
                      onPressed: () {
                        _showSoundsList(context);
                      },
                      icon: Text("similaires".toUpperCase()),
                    ),
                  ],
                ),
              ),
            ),
            Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(30, 0, 30, 60),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        width: MediaQuery.of(context).size.width,
                        height: MediaQuery.of(context).size.width - 60,
                        decoration: const BoxDecoration(
                          borderRadius: BorderRadius.all(
                            Radius.circular(32.0)
                          ),
                        ),
                        child: youtubePlayer ?? butifyPlayer,
                      ),
                      Padding(
                        padding: const EdgeInsets.fromLTRB(0, 20, 0, 10),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            SizedBox(
                              height: 30,
                              child: Text(
                                widget.sounds.elementAt(_currentSound).title,
                                style: TextStyle(
                                  color: Theme.of(context).primaryColorDark,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 25,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            _currentArtist != null ? TextButton(
                              onPressed: (){
                                setState(() {
                                  widget.showArtist(_currentArtist);
                                  widget.minimised = true;
                                });
                              },
                              style: TextButton.styleFrom(
                                padding: EdgeInsets.zero,
                                minimumSize: const Size(0, 0),
                                tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                                alignment: Alignment.centerLeft,
                                foregroundColor: Colors.black,
                                splashFactory: NoSplash.splashFactory,
                              ),
                              child: Text(
                                _currentArtist!.pseudo,
                                style: const TextStyle(
                                  color: Colors.grey,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ) : const SizedBox(),
                          ],
                        ),
                      ),
                      SizedBox(
                        width: MediaQuery.of(context).size.width,
                        height: 25,
                        child: SliderTheme(
                          data: SliderTheme.of(context).copyWith(
                            trackHeight: 2,
                            overlayShape: const RoundSliderOverlayShape(overlayRadius: 0),
                            thumbShape: isThumbActive
                                ? const RoundSliderThumbShape(enabledThumbRadius: 10)
                                : const RoundSliderThumbShape(enabledThumbRadius: 7),
                          ),
                          child: Slider(
                            min: 0,
                            max: _videoDuration,
                            activeColor: Theme.of(context).highlightColor,
                            thumbColor: Theme.of(context).primaryColorDark,
                            inactiveColor: Theme.of(context).dividerColor,
                            value: _currentPosition,
                            onChanged: (value){
                              setState(() {
                                _seekTo(Duration(seconds: value.round()));
                              });
                            },
                            onChangeStart: (double value) {
                              setState(() {
                                isThumbActive = true;
                              });
                            },
                            onChangeEnd: (double value) {
                              setState(() {
                                isThumbActive = false;
                              });
                            },
                          ),
                        ),
                      ),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            formatDuration(Duration(seconds: _currentPosition.toInt())),
                            textAlign: TextAlign.left,
                            style: TextStyle(
                              color: Theme.of(context).primaryColorDark,
                            ),
                          ),
                          Text(
                            formatDuration(Duration(seconds: _videoDuration.toInt())),
                            textAlign: TextAlign.right,
                            style: TextStyle(
                              color: Theme.of(context).primaryColorDark,
                            ),
                          ),
                        ],
                      ),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          IconButton(
                            onPressed: () {
                              setState(() {
                                _isRepeat = !_isRepeat;
                              });
                            },
                            icon: Icon(
                              Icons.repeat,
                              color: _isRepeat ? Theme.of(context).primaryColorDark : HexColor("#b1b1b1"),
                              size: 30,
                            ),
                          ),
                          IconButton(
                            onPressed: () {
                              _previousSound();
                            },
                            icon: Icon(
                              Icons.skip_previous,
                              color: Theme.of(context).primaryColorDark,
                              size: 30,
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.fromLTRB(25, 0, 25, 0),
                            child: IconButton(
                              onPressed: () {
                                setState(() {
                                  _isPlaying ? _pause() : _play();
                                });
                              },
                              style: IconButton.styleFrom(backgroundColor: Theme.of(context).primaryColorDark),
                              icon: Icon(
                                _isPlaying ? Icons.pause : Icons.play_arrow,
                                color: Theme.of(context).primaryColor,
                                size: 40,
                              ),
                            ),
                          ),
                          IconButton(
                            onPressed: () {
                              _nextSound();
                            },
                            icon: Icon(
                              Icons.skip_next,
                              color: Theme.of(context).primaryColorDark,
                              size: 30,
                            ),
                          ),
                          IconButton(
                            onPressed: () {
                              setState(() {
                                _isRandom = !_isRandom;
                              });
                            },
                            icon: Icon(
                              Icons.shuffle,
                              color: _isRandom ? Theme.of(context).primaryColorDark : HexColor("#b1b1b1"),
                              size: 30,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
/// @endcond