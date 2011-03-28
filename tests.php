<?php
require_once 'chess.php';


$c = new Chess();
$c->set_from_fen('8/8/1b4p1/3q1k1p/1Pp1ppP1/2P4P/4QP2/2B2K2 b KQkq g3 0 45');
assert($c->enpa == $c->get_coord_index('G3'));

assert(in_array($c->get_coord_index('g3'), $c->_check_move($c->get_coord_index('f4'), $c->get_coord_index('g3'))));
assert(in_array($c->get_coord_index('g3'), $c->_get_allowed_P_moves($c->get_coord_index('f4'))));
assert(in_array($c->get_coord_index('g3'), $c->_get_allowed_moves($c->get_coord_index('f4'))));

$c->go_move('f4:g3');






