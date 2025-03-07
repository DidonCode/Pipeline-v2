import 'package:flutter/material.dart';
import 'package:mobile/widgets/music/playlist/playlist_card.dart';

import 'package:mobile/class/playlist.dart';

enum playlistListDirection {
  HORIZONTAL,
  VERTICAL
}
/// @file playlist_list.dart
///
/// @cond IGNORE_THIS_CLASS
class PlaylistList extends StatefulWidget {
  final List<PlaylistCard> cards;
  final String listTitle;
  final playlistListDirection display;
  final Function(Playlist?) showPlaylist;

  const PlaylistList({
    super.key,
    required this.cards,
    required this.display,
    required this.listTitle,
    required this.showPlaylist,
  });

  @override
  State<PlaylistList> createState() => _PlaylistListState();
}

class _PlaylistListState extends State<PlaylistList> {
  @override
  Widget build(BuildContext context) {

    double horizontalCardWidth = MediaQuery.of(context).size.width * 1;

    if(widget.display == playlistListDirection.HORIZONTAL) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            margin: EdgeInsets.only(left:  10),
            child: Text(
              widget.listTitle,
              style: Theme.of(context).textTheme.titleLarge
            ),
          ),
          const SizedBox(height: 8),
          Container(
              height: 175,
              width: horizontalCardWidth,
              child: ListView(
                scrollDirection: Axis.horizontal,
                children: widget.cards,
              )
          )
        ],
      );
    }
    else if (widget.display == playlistListDirection.VERTICAL) {
      return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              margin: EdgeInsets.only(left:  10),
              child: Text(
                widget.listTitle,
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 20,
                ),
              ),
            ),
            const SizedBox(height: 8),
            ConstrainedBox(
              constraints: BoxConstraints(
                minHeight: 100,
                maxHeight: 325,
              ),
              child: ListView.builder(
                padding: const EdgeInsets.all(8),
                shrinkWrap: true,
                itemCount: widget.cards.length,
                itemBuilder: (context, index) {
                  return widget.cards[index];
                }
              ),
            )
          ]
      );
    }
    else {
      return Column(
        children: [
          Text("Erreur")
        ],
      );
    }
  }
}
/// @endcond