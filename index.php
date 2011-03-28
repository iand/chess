<?php
require_once('chess.php');

if (preg_match('~/board/([^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?/[^/]+?)$~', $_SERVER["REQUEST_URI"], $m) ) {
  $fen = $m[1];
  $board = new ChessBoard();
  $board->render($fen);
  exit;
}


// Change the following to suit your website
// regex is a regex that matches the URIs that you are using on your website
// store is the URI of the Talis Platform store your data is held in
// template is the filename of a template file to use
$uri_map[] = array( 'regex' => '^' .DATA.'/position/',
                    'store' => 'http://api.talis.com/stores/iand',
                    'describer' => 'ChessPosition',
                    'template' => 'plain.tmpl.html'
                    );

$uri_map[] = array( 'regex' => '^' .DATA.'/move/',
                    'store' => 'http://api.talis.com/stores/iand',
                    'describer' => 'ChessMove',
                    'template' => 'plain.tmpl.html'
                    );

// this loads and runs the dataspace script
require_once(MORIARTY_DIR . 'examples/dataspace/dataspace.php');
