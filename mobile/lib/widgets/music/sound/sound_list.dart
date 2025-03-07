import 'package:flutter/material.dart';
import 'package:mobile/widgets/music/sound/sound_card.dart';

import 'package:mobile/class/artist.dart';
import 'package:mobile/class/sound.dart';

enum soundsListDirection { HORIZONTAL, HORIZONTAL3, VERICAL}

/// @file sound_list.dart
///
/// @cond IGNORE_THIS_CLASS
class SoundList extends StatefulWidget {
  const SoundList(
      {super.key,
      required this.cards,
      required this.display,
      required this.listTitle,
      required this.showArtist,
      required this.showPlay,
      });

  final List<SoundCard> cards;
  final soundsListDirection display;
  final String listTitle;
  final Function(Artist?) showArtist;
  final Function(List<Sound>) showPlay;

  @override
  State<SoundList> createState() => _SoundListState();
}

class _SoundListState extends State<SoundList> {
  @override
  Widget build(BuildContext context) {

    double verticalCardWidth = MediaQuery.of(context).size.width * 0.90;
    double horizontalCardWidth = MediaQuery.of(context).size.width * 1;

    if (widget.display == soundsListDirection.HORIZONTAL) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            margin: EdgeInsets.only(left: 10),
            child: Text(
              widget.listTitle,
              style: Theme.of(context).textTheme.titleLarge
            ),
          ),
          const SizedBox(height: 8),
          Container(
              height: 200,
              width: horizontalCardWidth,
              child: ListView(
                scrollDirection: Axis.horizontal,
                children: widget.cards,
              ))
        ],
      );
    } else if (widget.display == soundsListDirection.HORIZONTAL3) {
      List<Widget> columns = [];
      const int inColumn = 3;
      double containerHeight;
      var column = Column(children: []);

      if(widget.cards.length == 1) {
        containerHeight = 85;
      }
      else if(widget.cards.length == 2) {
        containerHeight = 170;
      } else if(widget.cards.length == 3) {
        containerHeight = 255;
      } else {
        containerHeight = 325;
      }

      while(widget.cards.length > columns.length * inColumn + column.children.length){
        if(column.children.isNotEmpty && column.children.length % inColumn == 0) {
          columns.add(column);
          column = Column(children: []);
        }
        else{
          column.children.add(widget.cards[column.children.length + (columns.length * 3)]);
        }
      }
      columns.add(column);

      return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            Container(
              margin: EdgeInsets.only(left: 10),
              child: Text(
                widget.listTitle,
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 20,
                ),
              ),
            ),
          ],
        ),
        Container(
          width: horizontalCardWidth,
          height: containerHeight,
          child: ListView(
              scrollDirection: Axis.horizontal,
              children: columns
          ),
        ),
      ]);
    } else if (widget.display == soundsListDirection.VERICAL) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            margin: EdgeInsets.only(left: 10),
            child: Text(
                widget.listTitle,
                style: Theme.of(context).textTheme.titleLarge
            ),
          ),
          const SizedBox(height: 8),
          Container(
              height: 200,
              width: horizontalCardWidth,
              child: ListView(
                scrollDirection: Axis.vertical,
                children: widget.cards,
              ))
        ],
      );
    } else {
      return Column(
        children: [
          Center(
            child: Text("Error !"),
          )
        ],
      );
    }
  }
}
/// @endcond