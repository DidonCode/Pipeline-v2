import 'package:flutter/material.dart';
import 'package:mobile/widgets/music/artist/artist_card.dart';

import 'package:mobile/class/artist.dart';

/// @file artist_list.dart
/// @cond IGNORE_THIS_CLASS
class ArtistList extends StatefulWidget {
  final List<ArtistCard> cards;
  final String listTitle;
  final Function(Artist?) showArtist;

  const ArtistList({
    super.key,
    required this.cards,
    required this.listTitle,
    required this.showArtist,
  });

  @override
  State<ArtistList> createState() => _ArtistListState();
}

class _ArtistListState extends State<ArtistList> {
  @override
  Widget build(BuildContext context) {
    return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            margin: const EdgeInsets.only(left:  10),
            child: Text(
              widget.listTitle,
              style: Theme.of(context).textTheme.titleLarge
            ),
          ),
          Container(
              height: 125,
              child:  ListView(
                scrollDirection: Axis.horizontal,
                children: widget.cards,
              )
          )
        ],
      );
    }
  }
/// @cond IGNORE_THIS_CLASS