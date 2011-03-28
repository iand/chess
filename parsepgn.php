<?php
require_once('chess.php');

$world_championships = array(

  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1886',
        'pgnfile'=>'WorldChamp1886.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1889',
        'pgnfile'=>'WorldChamp1889.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1891',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1892',
        'pgnfile'=>'WorldChamp1892.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1894',
        'pgnfile'=>'WorldChamp1894.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1897',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1907',
        'pgnfile'=>'WorldChamp1907.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1908',
        'pgnfile'=>'WorldChamp1908.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1910_(Lasker-Schlechter)',
        'pgnfile'=>'WorldChamp1910a.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1910_(Lasker%E2%80%93Janowski)',
        'pgnfile'=>'WorldChamp1910b.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1921',
        'pgnfile'=>'WorldChamp1921.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1927',
        'pgnfile'=>'WorldChamp1927.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1929',
        'pgnfile'=>'WorldChamp1929.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1934',
        'pgnfile'=>'WorldChamp1934.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1935',
        'pgnfile'=>'WorldChamp1935.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1937',
        'pgnfile'=>'WorldChamp1937.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1948',
        'pgnfile'=>'WorldChamp1948.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1951',
        'pgnfile'=>'WorldChamp1951.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1954',
        'pgnfile'=>'WorldChamp1954.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1957',
        'pgnfile'=>'WorldChamp1957.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1958',
        'pgnfile'=>'WorldChamp1958.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1960',
        'pgnfile'=>'WorldChamp1960.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1961',
        'pgnfile'=>'WorldChamp1961.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1963',
        'pgnfile'=>'WorldChamp1963.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1966',
        'pgnfile'=>'WorldChamp1966.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1969',
        'pgnfile'=>'WorldChamp1969.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1972',
        'pgnfile'=>'WorldChamp1972.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1975',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1978',
        'pgnfile'=>'WorldChamp1978.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1981',
        'pgnfile'=>'WorldChamp1981.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1984',
        'pgnfile'=>'WorldChamp1984.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1985',
        'pgnfile'=>'WorldChamp1985.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1986',
        'pgnfile'=>'WorldChamp1986.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1987',
        'pgnfile'=>'WorldChamp1987.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1990',
        'pgnfile'=>'WorldChamp1990.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/Classical_World_Chess_Championship_1993',
        'pgnfile'=>'PCAChamp1993.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/Classical_World_Chess_Championship_1995',
        'pgnfile'=>'PCAChamp1995.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/Classical_World_Chess_Championship_2000',
        'pgnfile'=>'WorldChamp2000.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/Classical_World_Chess_Championship_2004',
        'pgnfile'=>'WorldChamp2004.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_1993',
        'pgnfile'=>'FideChamp1993.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/FIDE_World_Chess_Championship_1996',
        'pgnfile'=>'FideChamp1996.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/FIDE_World_Chess_Championship_1998',
        'pgnfile'=>'FideChamp1998.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/FIDE_World_Chess_Championship_1999',
        'pgnfile'=>'FideChamp1999.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/FIDE_World_Chess_Championship_2000',
        'pgnfile'=>'FideChamp2000.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/FIDE_World_Chess_Championship_2002',
        'pgnfile'=>'FideChamp2002.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/FIDE_World_Chess_Championship_2004',
        'pgnfile'=>'FideChamp2004.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/FIDE_World_Chess_Championship_2005',
        'pgnfile'=>'FideChamp2005.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_2006',
        'pgnfile'=>'WorldChamp2006.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_2007',
        'pgnfile'=>'WorldChamp2007.pgn',
        ),
  array(
        'dbpedia'=>'http://dbpedia.org/resource/World_Chess_Championship_2008',
        'pgnfile'=>'WorldChamp2008.pgn',
        ),
);



function parse($filename) {
  $games = array();
  $game = array();
  $f = fopen($filename, 'r');
  while (!feof ($f)) {
    $line = fgets($f);
    if (preg_match('~^\[Event "([^"]+)"\]~i', $line, $m) ) {
      if (isset($game['name'])) {
        $games[] = $game;
        $game = array();
      }
      $game['name'] = $m[1];
    }
    elseif (preg_match('~^\[([a-z]+) "([^"]+)"\]~i', $line, $m) ) {
      $game[strtolower($m[1])] = $m[2];
    }
    elseif (preg_match('~^(.*[a-h][0-9].*)$~i', $line, $m) ) {
      if (!isset($game['moves'])) {
        $game['moves'] = $m[1];
      }
      else {
        $game['moves'] .= ' ' . $m[1];
      }
    }
  }
  fclose ($f);
  if (isset($game['name'])) {
    $games[] = $game;
  }
  return $games;

}

$res = new ChessResource();


foreach ($world_championships as $info) {
  if (isset($info['pgnfile'])) {
    if (!file_exists('/home/iand/datasets/chess/pgnmentor.com/' . $info['pgnfile'])) {
      print "Cannot find " . $info['pgnfile'] . "\n";
    }
  }
}


exit;

$games = parse('./data/Adams.pgn');

foreach ($games as $game) {
//  if ($game['name'] == 'Oostende op' && $game['round'] == 2) {
    printf("%s (%s) round %s\n", $game['name'], $game['date'], $game['round']);
    $movedata = preg_replace('~[0-9]+?\.~', '', $game['moves']);

    $movedata = preg_replace('~\s~', ' ', $movedata);

    $movedata = trim(preg_replace('~0-1|1-0|1/2-1/2\s*$~', '', $movedata));

    $moves = preg_split('~\s+~', $movedata);
    $board = new Chess();


    foreach ($moves as $move) {
      if (trim($move)) {
        $board->go_move($move);
//        printf("%s : %s\n", $move, $board->get_fen());
    //    print $board->dump_pos();
    //    print "\n";
    //    print $res->make_position_uri($board->get_fen());
    //    print "\n\n";
      }
    }
//  }
}


