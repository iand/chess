<?php
// Ported from Chess::Rep, see http://search.cpan.org/~mishoo/Chess-Rep-0.8/

define('CASTLE_W_OO', 1);
define('CASTLE_W_OOO', 2);
define('CASTLE_B_OO', 4);
define('CASTLE_B_OOO', 8);
define('FEN_STANDARD', 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');


if (!isset($_SERVER["SERVER_NAME"]) || preg_match('~\.local$~', $_SERVER["SERVER_NAME"])) {
  define('LIB_DIR', '/home/iand/web//lib');
}
else {
  define('LIB_DIR', '/var/www/lib');
}

// Change the path to your moriarty installation
define('MORIARTY_DIR', LIB_DIR . '/moriarty/');

// Change the path to your ARC installation
define('MORIARTY_ARC_DIR', LIB_DIR . '/arc_2008_11_18/');

// Change the path to a writeable directory that can be used for cache files
define('MORIARTY_HTTP_CACHE_DIR', '/tmp');

if (isset($_SERVER["SERVER_NAME"])) {
  define('DATA', 'http://' . $_SERVER["SERVER_NAME"] . '/chess');
}
else {
  define('DATA', 'http://iandavis.com/chess');
}
define('CHESS', 'http://vocab.org/chess/schema/');
define('RDF', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
define('FOAF', 'http://xmlns.com/foaf/0.1/');
define('OWL', 'http://www.w3.org/2002/07/owl#');
define('RDFS', 'http://www.w3.org/2000/01/rdf-schema#');
define('FENRE', '([^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?)/([^/]+?)');


class ChessResource {
  function fen_to_uri($fen) {
    if (preg_match('~^([^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?) ([^/]+?)$~', $fen, $m)) {
      return $m[1].'/'.str_replace(' ',',', $m[2]);
    }
  }
  
  function uri_to_fen($uri) {
    if (preg_match('~/position/'.FENRE.'$~', $uri, $m)) {
      return $m[1] . ' ' . str_replace(',',' ', $m[2]);
    }
    return null;
  }

  function make_position_uri($fen) {
    return DATA.'/position/'.$this->fen_to_uri($fen);
  }

  function make_move_uri($fen, $from, $to) {
    return DATA.'/move/'.$this->fen_to_uri($fen).'/'.$from.'-'.$to;
  }
  
  function make_board_uri($fen) {
    if (preg_match('~^([^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?) ([^/]+?)$~', $fen, $m)) {
      return DATA.'/board/'.$m[1];
    }
    return '';
  }
}


class ChessPosition extends ChessResource {


  function describe($resource_uri, $describe_type='cbd', $output_format = 'json') {

    require_once MORIARTY_DIR . 'simplegraph.class.php';
    require_once MORIARTY_DIR . 'httpresponse.class.php';


    $fen = $this->uri_to_fen($resource_uri);
    if (!$fen) {
      if (preg_match('~/position/start$~', $resource_uri, $m)) {
        $fen = FEN_STANDARD;
      }
      else {
        return new HttpResponse(404);
      }
    }
    
    $g = new SimpleGraph();
    $g->add_resource_triple($resource_uri, RDF.'type', CHESS.'Position');
    $g->add_literal_triple($resource_uri, CHESS.'fen', $fen);
    
    $image = $this->make_board_uri($fen);
    if ($image) {
      $g->add_resource_triple($resource_uri, FOAF.'depiction', $image);
    }


    $c = new Chess($fen);

    $g->add_literal_triple($resource_uri, CHESS.'fullMoves', $c->fullmove, null, 'http://www.w3.org/2001/XMLSchema#nonNegativeInteger');
    $g->add_literal_triple($resource_uri, CHESS.'halfMoves', $c->halfmove, null, 'http://www.w3.org/2001/XMLSchema#nonNegativeInteger');
    $g->add_literal_triple($resource_uri, CHESS.'enPassantPosition', $c->enpa);

    if ($fen == 'rnbqkbnr/pppp2pp/8/4pp2/4P3/5N2/PPPP1PPP/RNBQKB1R w KQkq - 0 3') {
      $g->add_resource_triple($resource_uri, OWL.'sameAs', 'http://dbpedia.org/resource/Latvian_Gambit');
    }
    elseif ($fen == 'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1') {
      $g->add_resource_triple($resource_uri, OWL.'sameAs', 'http://dbpedia.org/resource/King%27s_Pawn_Game');
    }
    elseif ($fen == 'rnbqkbnr/pppp1ppp/8/4p3/4P3/8/PPPP1PPP/RNBQKBNR w KQkq e6 0 2') {
      $g->add_resource_triple($resource_uri, OWL.'sameAs', 'http://dbpedia.org/resource/Open_Game');
    }
    elseif ($fen == 'rnbqkbnr/pp1pppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR w KQkq c6 0 2') {
      $g->add_resource_triple($resource_uri, OWL.'sameAs', 'http://dbpedia.org/resource/Sicilian_Defence');
    }


    if ($c->to_move) {
      $g->add_resource_triple($resource_uri, CHESS.'player', CHESS.'white');
    }
    else {
      $g->add_resource_triple($resource_uri, CHESS.'player', CHESS.'black');
    }

    if ($c->castle & CASTLE_W_OO) {
      $g->add_resource_triple($resource_uri, CHESS.'castling', CHESS.'whitekingside');
    }

    if ($c->castle & CASTLE_W_OOO) {
      $g->add_resource_triple($resource_uri, CHESS.'castling', CHESS.'whitequeenside');
    }

    if ($c->castle & CASTLE_B_OO) {
      $g->add_resource_triple($resource_uri, CHESS.'castling', CHESS.'blackkingside');
    }

    if ($c->castle & CASTLE_B_OOO) {
      $g->add_resource_triple($resource_uri, CHESS.'castling', CHESS.'blackqueenside');
    }

    $move_list = array();
    $element_index = 0;
    foreach ($c->status['moves'] as $move) {
      $move_uri = $this->make_move_uri($fen, strtolower($c->get_field_id($move['from'])), strtolower($c->get_field_id($move['to'])));
      
      $move = new ChessMove();
      $move_response = $move->describe( $move_uri, 'cbd', 'json');
      if ($move_response->is_success()) {
        $move_list[] = $move_uri;
        $g->add_json($move_response->body);
        $g->add_resource_triple($resource_uri, CHESS.'possibleMove', $move_uri);
      }
    }

    $elem_uri = '';
    if (count($move_list) > 0) {
      for ($i = 0; $i < count($move_list); $i++) {
        if ($elem_uri) {
          $new_elem_uri = DATA.'/elem/'.$i;
          $g->add_resource_triple($elem_uri, RDF.'next',$new_elem_uri);
        }
        $elem_uri = DATA.'/elem/'.$i;
        
        if ($i == 0) {
          $g->add_resource_triple($resource_uri, CHESS.'possibleMoveList', $elem_uri);
          $g->add_resource_triple($elem_uri, RDF.'type', RDF.'List');
        }
        $g->add_resource_triple($elem_uri, RDF.'first',$move_list[$i]);
      }
      $g->add_resource_triple($elem_uri, RDF.'next',RDF.'nil');


    } 
    else {
      $g->add_resource_triple($resource_uri, CHESS.'moveList', RDF.'nil');
    }


    $comment = 'Move ' . $c->fullmove . ' of a chess game';

    if (count($move_list) > 0) {
      if ($c->status['check']) {
        if ($c->to_move) {
          $comment .= ', black is in check and';
        }
        else {
          $comment .= ', white is in check and';
        }
      }
      else {
        if ($c->to_move) {
          $comment .= ', white';
        }
        else {
          $comment .= ', black';
        }
      }
      $comment .= ' has ' . count($move_list) . ' possible move';
      if (count($move_list) > 1) $comment .= 's';
    }
    else {
      if ($c->status['mate']) {
        if ($c->to_move) {
          $comment .= ', white has been checkmated.';
        }
        else {
          $comment .= ', black has been checkmated.';
        }
      }
      elseif ($c->status['stalemate']) {
        if ($c->to_move) {
          $comment .= ', white has forced a stalemate.';
        }
        else {
          $comment .= ', black has forced a stalemate.';
        }
      }
    }
    $g->add_literal_triple($resource_uri, RDFS.'comment', $comment);
  
    $response = new HttpResponse(200);
    $response->body = $g->to_json();
    return $response;
  }

}

class ChessMove extends ChessResource {

  function describe($resource_uri, $describe_type='cbd', $output_format = 'json') {

    require_once MORIARTY_DIR . 'simplegraph.class.php';
    require_once MORIARTY_DIR . 'httpresponse.class.php';

    if (preg_match('~/move/' . FENRE .'/([a-h][1-8])-([a-h][1-8])$~', $resource_uri, $m)) {
      $fen = $m[1] . ' ' . str_replace(',',' ', $m[2]);
      $from = $m[3];
      $to = $m[4];

      $cnew = new Chess($fen);
      $to_move = $cnew->to_move;
      $rowcol = $cnew->get_row_col($from);
      $piece = $cnew->get_piece_at($rowcol[0], $rowcol[1]);
      
      $cnew->go_move($from.':'.$to);

      $from_uri = $this->make_position_uri($fen);
      $to_uri = $this->make_position_uri($cnew->get_fen());

      $g = new SimpleGraph();
      $g->add_resource_triple($resource_uri, RDF.'type', CHESS.'Move');
      $g->add_resource_triple($resource_uri, CHESS.'fromPosition', $from_uri);
      $g->add_resource_triple($resource_uri, CHESS.'toPosition', $to_uri);
      $g->add_literal_triple($resource_uri, CHESS.'from', strtolower($cnew->get_field_id($from)));
      $g->add_literal_triple($resource_uri, CHESS.'to', strtolower($cnew->get_field_id($to)));
      if ($to_move) {
        $g->add_resource_triple($resource_uri, CHESS.'player', CHESS.'white');
      }
      else {
        $g->add_resource_triple($resource_uri, CHESS.'player', CHESS.'black');
      }
      switch (strtoupper($piece)) {
        case 'P':
          $g->add_resource_triple($resource_uri, CHESS.'piece', CHESS.'pawn');
          break;
        case 'R':
          $g->add_resource_triple($resource_uri, CHESS.'piece', CHESS.'rook');
          break;
        case 'N':
          $g->add_resource_triple($resource_uri, CHESS.'piece', CHESS.'knight');
          break;
        case 'B':
          $g->add_resource_triple($resource_uri, CHESS.'piece', CHESS.'bishop');
          break;
        case 'Q':
          $g->add_resource_triple($resource_uri, CHESS.'piece', CHESS.'queen');
          break;
        case 'K':
          $g->add_resource_triple($resource_uri, CHESS.'piece', CHESS.'king');
          break;

      }

      $response = new HttpResponse(200);
      $response->body = $g->to_json();
      return $response;

    }
    else {
      return new HttpResponse(404);
    }
  }
}

class ChessBoard extends ChessResource {

  function __construct() {
    $this->settings = array();
    $this->settings['border_width'] = 1;
    $this->settings['border_color'] = '150,150,150';
    $this->settings['ls_color'] = '255,255,240';
    $this->settings['ds_color'] = '41,161,151';
    $this->settings['square_size'] = 35;
    $this->settings['coordinates'] = 'off';
    $this->settings['piece_style'] = 'merida';
    $this->settings['direction'] = 'normal';
  
  }
  
  function render($fen) {
    $direction = $this->getBoardDirection();
    $board = $this->makeBoardImage($direction);

    $pieceArray = $this->parseFenString($fen);

    for ($square = 0; $square < 64; $square++) {
      $this->mergePiece($board, $pieceArray[$square], $square, $direction);
    }

    $this->sendImage($board);
  }
  
  function sendErrorImageAndDie($cerr) {
    $new = imageCreate(600, 30);
    $bgc = imageColorAllocate($new,255,255,255);
    $tc  = imageColorAllocate($new,0,0,0);
    imageFilledRectangle($new,0,0,150,30,$bgc);
    imageString($new,5,5,5,"Error: $cerr", $tc);
    sendImage($new);
    die;
  }

  function sendImage($img) {
    if (! $img) {
      sendErrorImageAndDie("Invalid image object");
    }
    else {
      header("Content-type: image/png");
      imagePNG($img);
      imageDestroy($img);
    }
  }

  function loadPNG($image_name) {
    $im = imageCreateFromPNG($image_name);
    if (! $im) {
      sendErrorImageAndDie("Could not load piece image: $image_name");
    }
    return($im);
  }

  function parseColorString($str, &$red, &$green, &$blue) {
    preg_match("/\(?(\d+),(\d+),(\d+)\)?/", $str, $array);
    if (strlen($array[0]) > 0) {
      $red   = $array[1];
      $green = $array[2];
      $blue  = $array[3];
    }
  }

  function getColorFromUrl($str, $default_red, $default_green, $default_blue,
         &$red, &$green, &$blue) {
    $red = $default_red;
    $green = $default_green;
    $blue = $default_blue;
    $this->parseColorString($this->settings[$str], $red, $green, $blue);
  }


  function getDarkSquareColor($im) {
    $this->getColorFromUrl('ds_color', 41, 161, 151, $red, $green, $blue);
    return imageColorAllocate($im, $red, $green, $blue);
  }

  function getLightSquareColor($im) {
    $this->getColorFromUrl('ls_color', 255, 255, 240, $red, $green, $blue);
    return imageColorAllocate($im, $red, $green, $blue);
  }

  function getOutlineColor($im) {
    $this->getColorFromUrl('border_color', 150, 150, 150, $red, $green, $blue);
    return imageColorAllocate($im, $red, $green, $blue);
  }


  function getBorderWidth() {
    $border_width_string = $this->settings['border_width'];
    if (strlen($border_width_string) > 0) {
      return $border_width_string;
    }
    else {
      return 1;
    }
  }

  function isCoordinatesEnabled() {
    return(strcmp($this->settings['coordinates'], "on") == 0);
  }

  function getCoordinateWidth() {
    if ($this->isCoordinatesEnabled()) {
      $width = max(imageFontWidth($this->getCoordinateFont()), 
                   imageFontHeight($this->getCoordinateFont())) * 1.5;
    }
    else {
      $width = 0;
    }
    return($width);
  }

  function getDecorationWidth() {
    if ($this->isCoordinatesEnabled()) {
      $width = $this->getBorderWidth() + $this->getCoordinateWidth() + 1;
    }
    else {
      $width = $this->getBorderWidth();
    }

    return($width);
  }

  function getCoordinateFont() {
    if (1.5 * max(imageFontWidth(4), imageFontHeight(4)) <= getSquareSize()) {
      return(4);
    }
    else if (1.5 * max(imageFontWidth(2), imageFontHeight(2)) <= getSquareSize()) {
      return(2);
    }
    else {
      return(1);
    }    
  }
   
  function addCoordinates($im, $direction) {
    if (! $this->isCoordinatesEnabled()) {
      return;
    }

    $decorationWidth = $this->getDecorationWidth();
    $squareSize = $this->getSquareSize();
    $font = $this->getCoordinateFont();

    $x_left_numbers = ($decorationWidth - imageFontWidth($font)) / 2;
    $x_right_numbers = $x_left_numbers + 8 * $squareSize + $decorationWidth;
    $y1 = $decorationWidth + ($squareSize - imageFontHeight($font)) / 2;
    if ($direction == 'normal') {
      $deltaY = $squareSize;
    }
    else {
      $y1 = $y1 + (7 * $squareSize);
      $deltaY = -$squareSize;
    }

    $black = imageColorAllocate($im, 0, 0, 0);

    $y = $y1;
    for ($k = 8; $k >= 1; $k--) {
      imageString($im, $font, $x_left_numbers, $y, $k, $black);
      imageString($im, $font, $x_right_numbers, $y, $k, $black);
      $y += $deltaY;
    }

    $file = substr($files, $k - 1, 1);
    $x1 = $decorationWidth + ($squareSize - imageFontWidth($font)) / 2;
    $y_top_letters = ($decorationWidth - imageFontHeight($font)) / 2;
    $y_bottom_letters = $y_top_letters + 8 * $squareSize + $decorationWidth;

    if ($direction == 'normal') {
      $deltaX = $squareSize;
    }
    else {
      $x1 = $x1 + (7 * $squareSize);
      $deltaX = -$squareSize;
    }
    
    $files = 'abcdefgh';
    $x = $x1;
    for ($k = 0; $k < 8; $k++) {
      $file = substr($files, $k, 1);
      imageString($im, $font, $x, $y_top_letters, $file, $black);
      imageString($im, $font, $x, $y_bottom_letters, $file, $black);
      $x += $deltaX;
    }
  }

  function makeBoardImage($direction) {
    $squareSize = $this->getSquareSize();
    $decorationWidth = $this->getDecorationWidth();
    $coordinateWidth = $this->getCoordinateWidth();
    $borderWidth = $this->getBorderWidth();
    $numRows = 8 * $squareSize + 2 * $decorationWidth;
    $numCols = $numRows;

    $im = imageCreateTruecolor($numRows, $numCols);
    imageAlphaBlending($im, 1);

    $dark_square_color = $this->getDarkSquareColor($im);
    $light_square_color = $this->getLightSquareColor($im);
    $outline_color = $this->getOutlineColor($im);
    $white = imageColorAllocate($im, 255, 255, 255);

    imageFilledRectangle($im, 0, 0, $numRows - 1, $numCols - 1, $outline_color);
    if ($this->isCoordinatesEnabled()) {
      imageFilledRectangle($im, $borderWidth, $borderWidth, $numRows - $borderWidth - 1,
        $numCols - $borderWidth - 1, $white);
      imageFilledRectangle($im, $borderWidth + $coordinateWidth, 
        $borderWidth + $coordinateWidth,
        $numRows - $borderWidth - $coordinateWidth - 1, 
        $numCols - $borderWidth - $coordinateWidth - 1, $outline_color);
    }

    for ($rank = 0; $rank < 8; $rank++)
    {
        for ($file = 0; $file < 8; $file++)
        {
            $square_color = ($rank + $file) % 2 ? $dark_square_color : $light_square_color;
            $x1 = $file * $squareSize + $decorationWidth;
            $y1 = $rank * $squareSize + $decorationWidth;
            $x2 = $x1 + $squareSize - 1;
            $y2 = $y1 + $squareSize - 1;
            imageFilledRectangle($im, $x1, $y1, $x2, $y2, $square_color);
        }
    }

    $this->addCoordinates($im, $direction);

    return($im);
  }

  function getSquareSize() {
    $square_size_str = $this->settings['square_size'];
    if (strlen($square_size_str) == 0) {
      return(35);
    }
    else {
      return(min($square_size_str, 150));
    }
  }

  function parseFenString($str)
  {
    $count = 0;
    for ($k = 0; $k < strlen($str); $k++) {
      $char = substr($str, $k, 1);
      if ($char == "/") {
        continue;
      }

      else if (ereg("[prnbqkPRNBQK]", $char)) {
        $out[$count++] = $char;
      }

      else if (ereg("[1-8]", $char)) {
        for ($c = 0; $c < $char; $c++) {
          $out[$count++] = " ";
        }
      }

      else {
        // Invalid FEN character; bail
        break;
      }

      if ($count >= 64) {
        // array is full; bail
        break;
      }
    }

    $out = array_pad($out, 64, " ");

    return $out;
  }

  function getPieceStyle() {
    $piece_style_str = $this->settings['piece_style'];
    if (strlen($piece_style_str) == 0) {
      $piece_style_str = "merida";
    }
   
    return($piece_style_str);
  }

  function pieceFilename($piece)
  {
    static $map = array( "p" => "black_pawn",
                         "r" => "black_rook",
                         "n" => "black_knight",
                         "b" => "black_bishop",
                         "q" => "black_queen",
                         "k" => "black_king",
                         "P" => "white_pawn",
                         "R" => "white_rook",
                         "N" => "white_knight",
                         "B" => "white_bishop",
                         "Q" => "white_queen",
                         "K" => "white_king"   );

    return "./pieces/" . $this->getPieceStyle() . "/" . $map[$piece] . ".png";
  }

  function getBoardDirection() {
    $board_direction_str = $this->settings['direction'];
    if (strlen($board_direction_str) == 0) {
      $board_direction_str = 'normal';
    }

    return($board_direction_str);
  }

  function mergePiece($board, $piece, $square, $direction) {
    if ($piece == " ") {
      return;
    }

    $file = $square % 8;
    $rank = ($square - $file) / 8;
    
    $numCols = imagesx($board);
    $squareSize = $this->getSquareSize();
    $decorationWidth = ($numCols - 8 * $squareSize) / 2;

    if ($direction == 'normal') {
      $x = $decorationWidth + $file * $squareSize;
      $y = $decorationWidth + $rank * $squareSize;
    }
    else {
      $x = $decorationWidth + (7 - $file) * $squareSize;
      $y = $decorationWidth + (7 - $rank) * $squareSize;
    }

    $pieceImage = $this->loadPNG($this->pieceFilename($piece));
    $pieceSize = imageSx($pieceImage);
    if (! imageCopyResampled($board, $pieceImage, $x, $y, 0, 0, $squareSize,
          $squareSize, $pieceSize, $pieceSize)) {
      $this->sendErrorImageAndDie("imageCopy returned false");
    }

    imageDestroy($pieceImage);
  }



}



class Chess {
  var $debug = FALSE;
  function __construct($fen = FEN_STANDARD) {
    $this->set_from_fen($fen);
  }

  function debugon() {
    $this->debug = TRUE;
  }


  function reset() {
    $this->set_from_fen(FEN_STANDARD);
  }

  function set_from_fen($fen) {
    if ($this->debug) print "Setting from $fen\n";
    $this->_reset();
    $data = preg_split("~\s+~", $fen);
    
    $board = $data[0];
    $to_move = $data[1];
    $castle = $data[2];
    $enpa = $data[3];
    $halfmove = $data[4];
    $fullmove = $data[5];
    

    $board = array_reverse(preg_split("/\//", $board));
    for ($row = 0; $row < 8; $row++) {
      $data = $board[$row];
      $col = 0;
      for ($i = 0; $i < strlen($data); $i++) {
        $p = substr($data, $i, 1);
        if (preg_match("/[pnbrqk]/i", $p) ) {
          $this->set_piece_at($this->get_index($row, $col++), $p);
        } 
        elseif (preg_match("/[1-8]/", $p)) {
          $col += $p;
        } 
        else {
          die("Error parsing FEN position: $fen");
        }
      }
    }
    
    $c = 0;


    if (strpos($castle, 'K') !== FALSE) {
      $c |= CASTLE_W_OO;
    }
    if (strpos($castle, 'Q') !== FALSE) {
      $c |= CASTLE_W_OOO ;
    }
    if (strpos($castle, 'k') !== FALSE) {
      $c |= CASTLE_B_OO;
    }
    if (strpos($castle, 'q') !== FALSE) {
      $c |= CASTLE_B_OOO;
    }


    
    $this->castle = $c;
    if (strtolower($to_move) === 'w') {
      $this->to_move = 1;
    } 
    elseif (strtolower($to_move) === 'b') {
      $this->to_move = 0;
    } 
    else {
      $this->to_move = null;
    }
    
    $this->enpa = $enpa != '-' ? $this->get_index($enpa) : 0;
    $this->fullmove = $fullmove;
    $this->halfmove = $halfmove;
    $this->_compute_valid_moves();
  }

  function get_fen($short = FALSE) {
    if ($this->debug) print("\n\nget_fen($short)\n");
    $a = array();
    for ($row = 7; $row >= 0; $row--) {
      $str = '';
      $empty = 0;
      for ($col = 0; $col < 8; $col++) {
        $p = $this->get_piece_at($row, $col);
        if ($p) {
          if ($empty) {
            $str .= $empty;
            $empty = 0;
          } 
          $str .= $p;
        }
        else {
          ++$empty;
        }
      }
      if ($empty) {
        $str .= $empty;
      }
      array_push($a, $str);
    }
    $pos = join('/', $a);
    
    $ret = array($pos);
    $ret[1] = $this->to_move ? 'w' : 'b';
    $castle = $this->castle;
    $c = '';
    if ($castle & CASTLE_W_OO) {
      $c .= 'K';
    }
    if ($castle & CASTLE_W_OOO) {
      $c .= 'Q';
    }
    if ($castle & CASTLE_B_OO) {
      $c .= 'k';
    }
    if ($castle & CASTLE_B_OOO) {
      $c .= 'q'; 
    }
    $ret[2] = $c ? $c :  '-';
    $ret[3] = $this->enpa ? strtolower($this->get_field_id($this->enpa)) : '-';
    
    if (!$short) {
      $ret[4] = $this->halfmove;
      $ret[5] = $this->fullmove;
    }
    return join(' ', $ret);
  }


  function status() {
      return $this->_status;
  }

  function _reset() {
    $a = array();
    for ($i = 0; $i < 120; $i++) {
      $m = $i % 10;
      $a[$i] = $i < 21 || $i > 98 || $m === 0 || $m === 9 ? null : 0;
    }
    $this->pos = $a;
    $this->castle = CASTLE_W_OO | CASTLE_W_OOO | CASTLE_B_OO | CASTLE_B_OOO;
    $this->to_move = 1; # white
    $this->enpa = 0;
    $this->halfmove = 0;
    $this->fullmove = 0;
    $this->status = null;
  }

  function set_piece_at($index, $p) {
    if (preg_match("/^[a-h]/i", $index)) {
      $index = $this->get_index($index);
    }
    $old = $this->get_piece_at($index);
    $this->pos[$index] = $p;
    return $old;
  }


  function get_piece_at($index, $col = -1) {
    if ($this->debug) print("get_piece_at($index, $col)\n");
    if ($col >= 0) {
      $index = $this->get_index($index, $col);
    } 
    elseif (preg_match("/^[a-h]/i", $index)) {
      $index = $this->get_index($index);
    }
    $p = $this->pos[$index];
    if ($this->debug) print("get_piece_at($index, $col) = $p\n");
    return $p;
  }

  function set_to_move($val) {
    $this->to_move = $val;
  }

  function to_move() {
    return $this->to_move;
  }

  function go_move($move) {
    $color = $this->to_move;
    $orig_move = $move;
    $piece = null;
    $from = null;
    $to = null;
    $promote = null;
    $san = null;
    $from_index = null;
    
    
    if (preg_match("/^O-O\+?$/i", $move, $m)) {
      $move = $color ? 'E1-G1' : 'E8-G8';
    } 
    elseif (preg_match("/^O-O-O\+?$/i", $move, $m)) {
      $move = $color ? 'E1-C1' : 'E8-C8';
    }

    if (preg_match("/^([PNBRQK])(.*)$/", $move, $m)) {
      $piece = $m[1];
      $move = $m[2];
    }

    if (preg_match("/^([a-h][1-8])[:x-]?([a-h][1-8])(.*)$/i", $move, $m)) {
      $from = $m[1];
      $to = $m[2];
      $move = $m[3];
    } 
    elseif (preg_match("/^([a-h])[:x-]?([a-h][1-8])(.*)$/i", $move, $m)) {
      $col = ord(strtoupper($m[1])) - 65;
      $to = $m[2];
      $move = $m[3];
    } 
    elseif (preg_match("/^([1-8])[:x-]?([a-h][1-8])(.*)$/i", $move, $m)) {
      $row = ord($m[1]) - 49;
      $to = $m[2];
      $move = $m[3];
    } 
    elseif (preg_match("/^[:x-]?([a-h][1-8])(.*)$/i", $move, $m)) {
      $to = $m[1];
      $move = $m[2];
    } 
    else {
      die("Could not parse move: $orig_move\n");
    }

    if (preg_match("/^=?([RNBQ])(.*)$/i", $move, $m)) {
      $promote = strtoupper($m[1]);
      $move = $m[2];
    }

    if (!$piece) {
      if (!$from) {
        $piece = 'P';
      } 
      else {
        $piece = $this->get_piece_at($from);
        if (!$piece) {
          die("Illegal move: $orig_move (field $from is empty)\n");
        }
      }
    }

    $is_pawn = FALSE;
    if (strtolower($piece) === 'p') {
      $is_pawn = TRUE;
    }

    if (!$color) { # is black, make lowercase
      $piece = strtolower($piece);
    }

    if (!$to) {
      die("Can't parse move: $orig_move (missing target field)\n");
    }

    $to_index = $this->get_index($to);

    # all moves that a piece of type $piece can make to field $to_index
    $tpmove = isset($this->status['type_moves'][$to_index][$piece]) ? $this->status['type_moves'][$to_index][$piece] : array();

    if (!$tpmove) {
    
      printf("%s\n",  $this->dump_pos());
/*
      printf("to_index: %s\n", $to_index);
      printf("enpa: %s\n",  $this->enpa);
      print_r($this->status);
*/
      die("Illegal move: $orig_move (no piece '$piece' can move to $to)\n");
    }

    if (!$from) {
      if (count($tpmove) === 1) {
        # unambiguous
        $from_index = $tpmove[0];
      } 
      else {
        foreach ($tpmove as $origin) {
          $rowcol = $this->get_row_col($origin);
          $t_row = $rowcol[0];
          $t_col = $rowcol[1];
          
          if (isset($row) && intval($row) === intval($t_row)) {
            $from_index = $origin;
            break;
          } 
          elseif (isset($col) && intval($col) === intval($t_col)) {
            $from_index = $origin;
            break;
          }
        }
      }
      if ($from_index) {
        $from = $this->get_field_id($from_index);
      } 
      else {
        //print "To: " . $to_index . "\n";
        var_dump($this->status['type_moves'][$to_index]);
        //var_dump($this->status);
        //var_dump($this->_get_allowed_moves(54));
        die("Ambiguous move: $orig_move");
      }
    }

    if (!$from_index) {
      $from_index = $this->get_index($from);
    }

    $from = strtoupper($from);
    $to = strtoupper($to);

    $rowcol = $this->get_row_col($from_index);
    $from_row = $rowcol[0];
    $from_col = $rowcol[1];

    $rowcol = $this->get_row_col($to_index);
    $to_row = $rowcol[0];
    $to_col = $rowcol[1];


    # execute move

    $this->enpa = 0;

    $is_capture = 0;

    # 1. if it's castling, we have to move the rook
    $done_move = FALSE;
    $move = "$from-$to";
    if (strtolower($piece) === 'k') {
      if ($move === 'E1-G1') {
        $san = 'O-O';
        $this->_move_piece(28, 26); 
        $done_move = TRUE;
      } 
      elseif ($move === 'E8-G8') {
        $san = 'O-O';
        $this->_move_piece(98, 96);
        $done_move = TRUE;
      } 
      elseif ($move === 'E1-C1') {
        $san = 'O-O-O';
        $this->_move_piece(21, 24);
        $done_move = TRUE;
      } 
      elseif ($move === 'E8-C8') {
        $san = 'O-O-O';
        $this->_move_piece(91, 94);
        $done_move = TRUE;
      }
    }

    if (! $done_move) {
      # 2. is it en_passant?
      if ($is_pawn) {

        if ($from_col != $to_col && !$this->get_piece_at($to_index)) {
            $this->set_piece_at($this->get_index($from_row, $to_col), 0);
            $is_capture = 1;
            $done_move = TRUE;
        }

        if (abs($from_row - $to_row) == 2) {
          $this->enpa = $this->get_index(($from_row + $to_row) / 2, $from_col);
        }
      }
    }
    

    if ($this->_move_piece($from_index, $to_index, $promote)) {
      $is_capture = 1;
    }

    $this->to_move = 1 - $this->to_move;

    if ($this->to_move) {
      $this->fullmove++;
    }

    if (!$is_pawn && !$is_capture) {
      $this->halfmove++;
    } 
    else {
      $this->halfmove = 0;
    }

    $status = $this->_compute_valid_moves();

    if (!$san) {
      $san = $is_pawn ? '' : strtoupper($piece);

      $len = ($is_capture && $is_pawn || count($tpmove) > 1) ? 1 : 0;
      foreach ($tpmove as $origin) {
        if ($origin != $from_index && $origin % 10 === $from_index % 10) {
          $len = 2;
          break;
        }
      }

        $san .=  strtolower(substr($from, 0, $len));
        if ($is_capture) {
          $san .= 'x';
        }
        $san .= strtolower($to);
        if ($promote) {
          $san .= "=$promote";
        }

        if ($status['mate']) {
          $san .= '#';
        } 
        elseif ($status['check']) {
          $san .= '+';
        }
    }

    # _debug("$orig_move \t\t\t $san");

    return array(
        'from'       => strtolower($from),
        'from_index' => $from_index,
        'from_row'   => $from_row,
        'from_col'   => $from_col,
        'to'         => strtolower($to),
        'to_index'   => $to_index,
        'to_row'     => $to_row,
        'to_col'     => $to_col,
        'piece'      => $piece,
        'promote'    => $promote,
        'san'        => $san,
    );
  }

  function _move_piece($from, $to, $promote = '') {
    $p = $this->set_piece_at($from, 0);
    $color = $this->piece_color($p);
    $lp = strtolower($p);
    if ($promote) {
      $p = $color ? strtoupper($promote) : strtolower($promote);
    }
    if ($lp === 'k') {
        if ($color) {
            $this->castle = $this->castle | CASTLE_W_OOO ^ CASTLE_W_OOO;
            $this->castle = $this->castle | CASTLE_W_OO ^ CASTLE_W_OO;
        } else {
            $this->castle = $this->castle | CASTLE_B_OOO ^ CASTLE_B_OOO;
            $this->castle = $this->castle | CASTLE_B_OO ^ CASTLE_B_OO;
        }
    }
    if ($from === 21 || $to === 21) {
        $this->castle = $this->castle | CASTLE_W_OOO ^ CASTLE_W_OOO;
    } elseif ($from === 91 || $to === 91) {
        $this->castle = $this->castle | CASTLE_B_OOO ^ CASTLE_B_OOO;
    } elseif ($from === 28 || $to === 28) {
        $this->castle = $this->castle | CASTLE_W_OO ^ CASTLE_W_OO;
    } elseif ($from === 98 || $to === 98) {
        $this->castle = $this->castle | CASTLE_B_OO ^ CASTLE_B_OO;
    }
    $this->set_piece_at($to, $p);
  }

  function _compute_valid_moves() {
    if ($this->debug) print "_compute_valid_moves()\n";
    
    $pieces = array();
    $king = null;
    $op_color = 1 - $this->to_move;

    for ($row = 0; $row < 8; $row++) {
      for ($col = 0; $col < 8; $col++) {
        $p = $this->get_piece_at($row, $col);
        if ($p) {
          $index = $this->get_index($row, $col);
          if ($this->piece_color($p) === $this->to_move) {
            array_push($pieces, array(
                'from' => $index,
                'piece' => $p,
                ) );
            if (strtolower($p) === 'k') {
              # remember king position
              $king = $index;
            }
          }
        }
      }
    }

    $this->in_check = $this->is_attacked($king, $op_color);

    $all_moves = array();
    $hash_moves = array();
    $type_moves = array();
    
    foreach ($pieces as $p) {
      $from = $p['from'];
      $moves = $this->_get_allowed_moves($from);
      $piece = $p['piece'];
      $try_move = array(
          'from'  => $from,
          'piece' => $piece,
          );
      $is_king = intval($from) === intval($king);
      $valid_moves = array();
      foreach ($moves as $move_candidate) {
        $try_move['to'] = $move_candidate;
        if (! $this->is_attacked($is_king ? $move_candidate : $king, $op_color, $try_move) ) {
          array_push($valid_moves, $move_candidate);
        }
      }
      
      
      # _debug("Found moves for $piece");
      $p['to'] = $valid_moves;
      foreach ($valid_moves as $to) {
        $hash_moves["$from-$to"] = 1;
        array_push($all_moves, array('from'=>$from, 'to'=>$to, 'piece'=>$piece));
        if (!isset($type_moves[$to])) {
          $type_moves[$to] = array( $piece => array($from) );
        }
        elseif (!isset($type_moves[$to][$piece])) {
          $type_moves[$to][$piece] = array($from);
        }
        else {
          array_push($type_moves[$to][$piece], $from);
        }
      }
    }

    $this->status = array(
        'moves'      => $all_moves,
        'hash_moves' => $hash_moves,
        'type_moves' => $type_moves,
        'check'      => $this->in_check,
        'mate'       => $this->in_check && count($all_moves) === 0,
        'stalemate'  => !$this->in_check && count($all_moves) === 0,
      );
    return $this->status;
  }



  function _test_attack($type, $i, $opponent_color, $try_move) {
//printf("_test_attack($type, $i, $opponent_color, $try_move)\n");
/*
printf("try_move['from']: %s\n", $try_move['from']);
printf("try_move['to']: %s\n", $try_move['to']);
printf("try_move['piece']: %s\n", $try_move['piece']);
printf("this->enpa: %s\n", $this->enpa);
*/
    $p = null;
    if ($try_move) {

      if ($i === $try_move['from']) {
        $p = 0;
      } 
      elseif ($i === $try_move['to']) {
        // The trial move takes the piece being considered
        $p = $try_move['piece'];
      } 
      elseif (   $try_move['piece'] === 'p' 
              && intval($this->enpa) === intval($try_move['to'])
              && intval($i) === intval($this->enpa) + 10
              ) {
        $p = 0;
      }
      elseif (   $try_move['piece'] === 'P' 
              && intval($this->enpa) === intval($try_move['to'])
              && intval($i) === intval($this->enpa) - 10
              ) {
        $p = 0;
      }
      else {
        $p = $this->pos[$i];
      }
    } 
    else {
      $p = $this->pos[$i];
    }
    
    if ($p === null) {
//print("A\n");
      return 1;
    }

    if ($p && $this->piece_color($p) === $opponent_color && strpos($type, strtolower($p)) !== FALSE) {
//printf("p:%s\n", $p);
//print("B\n");
      return -1;
    }
//print("C\n");
    return $p;
  }


  function is_attacked($i, $opponent_color = null, $try_move = array()) {
//printf("\nChecking if %s is attacked (trying move %s-%s)\n", $this->get_field_id($i), $this->get_field_id($try_move['from']), $this->get_field_id($try_move['to']));

    if ($opponent_color != null) {
      $opponent_color = 1 - $this->to_move;
    }
    
    # check pawns
    # _debug("... checking opponent pawns");
    if ($opponent_color) {
      if ($this->_test_attack('p', $i - 9, $opponent_color, $try_move) === -1) return TRUE;
      if ($this->_test_attack('p', $i - 11, $opponent_color, $try_move) === -1) return TRUE;
    } 
    else {
      if ($this->_test_attack('p', $i + 9, $opponent_color, $try_move) === -1) return TRUE;
      if ($this->_test_attack('p', $i + 11, $opponent_color, $try_move) === -1) return TRUE;
    }

    # check knights
    # _debug("... checking opponent knights");
    foreach (array(19, 21, 8, 12, -19, -21, -8, -12) as $step) {
      if ($this->_test_attack('n', $i + $step, $opponent_color, $try_move) === -1) return TRUE;
    }

    # check bishops or queens
    # _debug("... checking opponent bishops");
    foreach (array(11, 9, -11, -9) as $step) {
      $j = $i;
      $ret = 0;
      while (!$ret) {
        $j += $step;
        $ret = $this->_test_attack('bq', $j, $opponent_color, $try_move);
        if ($ret === -1) return TRUE;
      }
    }

    # check rooks or queens
    # _debug("... checking opponent rooks or queens");
    foreach (array(1, 10, -1, -10) as $step) {
      $j = $i;
      $ret = 0;
      while (!$ret) {
        $j += $step;
        $ret = $this->_test_attack('rq', $j, $opponent_color, $try_move);
        if ($ret === -1) return TRUE;
      }
    }

    foreach (array(9, 10, 11, -1, 1, -9, -10, -11) as $step) {
      if ($this->_test_attack('k', $i + $step, $opponent_color, $try_move) === -1) return TRUE;
    }

    return FALSE;
  }

  function _get_allowed_moves($index) {
    if ($this->debug) print "_get_allowed_moves($index)\n";
    $p = $this->get_piece_at($index);
    $color = $this->piece_color($p);
    $p = strtoupper($p);
    $method = "_get_allowed_${p}_moves";
    return array_unique($this->$method($index));
  }

  function _check_move($from, $to) {
    $what = $this->get_piece_at($to);
    
    if ($what === null) return array();

    $p = $this->get_piece_at($from);
    $color = $this->piece_color($p);
    $p = strtolower($p);

    if ($p === 'k' && $this->is_attacked($to)) {
      return array();
    }

    if (!$what) {
      if ($p === 'p') {
        if (abs($from % 10 - $to % 10) === 1) {
          //print("En passant ($this->enpa | $to)\n");
          if (intval($to) === intval($this->enpa)) { # check en passant
            //print("Adding en-passant: $p " . $this->get_field_id($from) . "-" . $this->get_field_id($to) . "\n");
            return array($to);
          }
          return array(); # must take to move this way
        }
      }
      //print("Adding move $p " . $this->get_field_id($from) . "-" . $this->get_field_id($to));
      return array($to);
    }

    if ($this->piece_color($what) != $color) {
      if ($p === 'p' && $from % 10 === $to % 10) {
          return array();   # pawns can't take this way
      }
      //print("Adding capture: $p " . $this->get_field_id($from) . "-" . $this->$this->get_field_id($to));
      return array($to);
    }

    return array();
  }

  function _get_allowed_P_moves($index, $moves = array()) {
    $color = $this->piece_color_at_index($index);
    $step = $color ? 10 : -10;
    $not_moved = $color
      ? ($index >= 31 && $index <= 38)
        : ($index >= 81 && $index <= 88);
    
    
    $move_one = $this->_check_move($index, $index + $step);
    if (count($move_one)) {
      $moves = array_merge($moves, $move_one);
      if ($not_moved) {
        $moves = array_merge($moves, $this->_check_move($index, $index + 2 * $step));
      }
    }
    $moves = array_merge($moves, $this->_check_move($index, $index + ($color ? 11 : -9)));
    $moves = array_merge($moves, $this->_check_move($index, $index + ($color ? 9 : -11)));
    
    return $moves;
  }

  function _get_allowed_N_moves($index, $moves = array()) {
    foreach (array(19, 21, 8, 12, -19, -21, -8, -12) as  $step) {
      $moves = array_merge($moves, $this->_check_move($index, $index + $step));
    }
    return $moves;
  }

  function _get_allowed_R_moves($index, $moves = array()) {
    foreach (array(1, 10, -1, -10) as  $step) {
      $i = $index;
      $i += $step;
      while ($this->get_piece_at($i) !== null) {
        $moves = array_merge($moves, $this->_check_move($index, $i));
        if ($this->get_piece_at($i)) {
          break;
        }
        $i += $step;  
      }
    }
    return $moves;
  }

  function _get_allowed_B_moves($index, $moves = array()) {
    foreach (array(11, 9, -11, -9) as  $step) {
      $i = $index;
      $i += $step;
      while ($this->get_piece_at($i) !== null) {
        $moves = array_merge($moves, $this->_check_move($index, $i));
        if ($this->get_piece_at($i)) {
          break;
        }
        $i += $step;  
      }
    }
    return $moves;
  }

  function _get_allowed_Q_moves($index, $moves = array()) {
    $moves = array_merge($moves, $this->_get_allowed_R_moves($index, $moves));
    $moves = array_merge($moves, $this->_get_allowed_B_moves($index, $moves));
    return $moves;
  }

  function _get_allowed_K_moves($index, $moves = array()) {
    foreach (array(9, 10, 11, -9, -10, -11) as $step) {
      $moves = array_merge($moves, $this->_check_move($index, $index + $step));
    }

    $color = $this->piece_color_at_index($index);


    $avail = $this->_check_move($index, $index + 1);
    $moves = array_merge($moves, $avail);
    if (count($avail) &&
          !$this->in_check && $this->can_castle($color, 0) &&
            !$this->get_piece_at($index + 1) &&
              !$this->get_piece_at($index + 2)) {
        # kingside castling possible
        $moves = array_merge($moves, $this->_check_move($index, $index + 2));
    }

    $avail = $this->_check_move($index, $index - 1);
    $moves = array_merge($moves, $avail);
    if (count($avail) &&
          !$this->in_check && $this->can_castle($color, 1) &&
            !$this->get_piece_at($index - 1) &&
              !$this->get_piece_at($index - 2) &&
                !$this->get_piece_at($index - 3)) {
        # queenside castling possible
        $moves = array_merge($moves, $this->_check_move($index, $index - 2));
    }

    return $moves;
  }

  function can_castle($color, $ooo) {
    if ($color) {
        return $this->castle & ($ooo ? CASTLE_W_OOO : CASTLE_W_OO);
    } else {
        return $this->castle & ($ooo ? CASTLE_B_OOO : CASTLE_B_OO);
    }
  }


  function piece_color($p) {
    return ord($p) < 97 ? 1 : 0;
  }

  function piece_color_at_index($index) {
    $p = $this->get_piece_at($index);
    return $this->piece_color($p);
  }

  function get_coord_index($coord) {
    $data = unpack('Ccol/Crow', strtoupper($coord));
    return $this->get_index($data['row'] - 49, $data['col'] - 65);
  }


  function get_index($row, $col = -1) {
    if ($col === -1) {
      $rowcol = $this->get_row_col($row);
      $row = $rowcol[0];
      $col = $rowcol[1];
    }
    
    $index = $row * 10 + $col + 21;
    if ($this->debug) print("get_index($row, $col) = $index\n");
    return $index;
  }

  function get_field_id($row, $col = null) {
    if ($col === null) {
      $rowcol = $this->get_row_col($row);
      $row = $rowcol[0];
      $col = $rowcol[1];
    }
    return pack('CC', $col + 65, $row + 49);
  }
  
  function get_row_col($id) {
    if (preg_match("/^[a-h]/i", $id)) {
        $data = unpack('Ccol/Crow', strtoupper($id));
        $rowcol = array(
          $data['row'] - 49,
          $data['col'] - 65,
        );
    } 
    else {
        $idad = $id - 21;
        $rowcol = array(
            floor($idad / 10),
            $idad % 10,
        );
    }
    
    if ($this->debug) print("get_row_col($id) = ($rowcol[0], $rowcol[1])\n");
    return $rowcol;
    
  }

  function dump_pos() {
    $fen = $this->get_fen();
    if ($this->debug) print "$fen\n";
    $a = preg_split("/ /", $fen);
    $fen = $a[0];
    $fen = preg_replace("/(1)/", " ", $fen);
    $fen = preg_replace("/(2)/", "  ", $fen);
    $fen = preg_replace("/(3)/", "   ", $fen);
    $fen = preg_replace("/(4)/", "    ", $fen);
    $fen = preg_replace("/(5)/", "     ", $fen);
    $fen = preg_replace("/(6)/", "      ", $fen);
    $fen = preg_replace("/(7)/", "       ", $fen);
    $fen = preg_replace("/(8)/", "        ", $fen);
    $fen = preg_replace("~([^/])~", "|$1", $fen);
    $fen = preg_replace("~\/~", "|\n|-+-+-+-+-+-+-+-|\n", $fen);
    $fen .= '|';
    return $fen;
  }


}
