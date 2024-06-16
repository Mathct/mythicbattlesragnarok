<?php

$this->boards[2] = array (
'name' => 'Vigrid',
'setup' => array (1 => array( 1 => array(12,21), 2 => array(25,34), "runes" => array(9,15,17,11,29,30,37),"side1"=>array(1,8,12,21,26), "side2"=>array(7,20,25,34,39) ),
                  2 => array( 1 => array(1,4,7), 2 => array(26,36,38), "runes" => array(12,13,15,18,19,24,25),"side1"=>array(1,2,3,4,5,6,7), "side2"=>array(35,36,37,38,39)  )),
'zones' => array(
  -3 => array("id"=>-3, "type" => "TABLE", "capacity" => 99,"height"=>0, "visible" => [], "boundaries" => []),
  -2 => array("id"=>-2, "type" => "TABLE", "capacity" => 99,"height"=>0, "visible" => [], "boundaries" => []),
  -1 => array("id"=>-1, "type" => "TABLE", "capacity" => 99,"height"=>0, "visible" => [], "boundaries" => []),
  0 => array("id"=>0, "type" => "CEMETERY", "capacity" => 99,"height"=>0, "visible" => [], "boundaries" => []),
1 => array("id"=> 1, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [2 => array(),3 => array(2,9),4 => array(2,3),5 => array(2,3,4),6 => array(2,3,4,5),7 => array(2,3,4,5,6),8 => array(),9 => array(),10 => array(9,3),11 => array(9,3,4,10),12 => array(8),13 => array(8,14),14 => array(8,9),15 => array(8,9,14),16 => array(9,14,15),17 => array(9,14,16),18 => array(9,14,15,16),19 => array(9,14,16,17),20 => array(9,3,10,11),21 => array(8,12),22 => array(8,12),23 => array(8,9,14,15),24 => array(9,14,15,16,23,18),25 => array(9,14,15,16,18,17,19,24),26 => array(8,12,21),27 => array(8,12,22),28 => array(8,12,13,22,23),29 => array(8,9,14,15,23),30 => array(8,14,13,22,15,23,29),31 => array(8,9,14,15,23,29,30),32 => array(8,9,14,15,23,29,31,24),33 => array(8,9,14,15,16,23,29,24,32),34 => array(8,9,14,15,16,23,24,32),35 => array(8,12,21,27),36 => array(8,12,22,27,28),37 => array(8,14,13,22,15,23,28,30),38 => array(8,9,14,13,22,15,23,29,30,31),39 => array(8,9,14,15,23,29,30,31,32,38)], 'boundaries' => array(2 => NORMAL,8 => NORMAL,9 => NORMAL)),
2 => array("id"=> 2, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [1 => array(),3 => array(),4 => array(3),5 => array(3,4),6 => array(3,4,5),7 => array(3,4,5,6),8 => array(9),9 => array(),10 => array(9,3,4),11 => array(3,4,10),12 => array(9,14,8),13 => array(9,14),14 => array(9),15 => array(9,14),16 => array(9,14,15),17 => array(9,3,16,10),18 => array(9,3,16),19 => array(9,3,16,10,17),20 => array(3,4,10,11),21 => array(9,14,13,12),22 => array(9,14,13),23 => array(9,14,15,16),24 => array(9,14,3,16,18),25 => array(9,3,16,10,17,19,24),26 => array(9,14,13,22,21),27 => array(9,14,13,22),28 => array(9,14,22,15,23),29 => array(9,14,15,23),30 => array(9,14,15,23,29),31 => array(9,14,15,16,23,29,30),32 => array(9,14,15,16,23,24),33 => array(9,14,16,23,24,32),34 => array(9,3,16,18,24,32),35 => array(9,14,13,22,27),36 => array(9,14,22,15,23,28),37 => array(9,14,15,23,29,30),38 => array(9,14,15,23,29,30,31),39 => array(9,14,15,16,23,31,32,33,38)], 'boundaries' => array(1 => NORMAL,3 => NORMAL,9 => NORMAL)),
3 => array("id"=> 3, "type" => BURNING, "capacity" => 3,"height"=>0, "visible" => [1 => array(9,2),2 => array(),4 => array(),5 => array(4),6 => array(4,5),7 => array(4,5,6),8 => array(9),9 => array(),10 => array(4),11 => array(4,10),12 => array(9,14,13),13 => array(9,14),14 => array(9),15 => array(14),16 => array(),17 => array(10),18 => array(16),19 => array(10,17),20 => array(4,10,11),21 => array(9,14,13,22),22 => array(9,14),23 => array(16),24 => array(16,17,18),25 => array(10,17,19,24),26 => array(9,14,22,27,21),27 => array(9,14,22),28 => array(9,14,15,23,29),29 => array(16,23),30 => array(16,15,23,29),31 => array(16,23,29),32 => array(16,18,24),33 => array(16,18,24,32),34 => array(16,10,17,18,24,32),35 => array(9,14,22,27),36 => array(14,15,23,29,28),37 => array(16,15,23,29,30),38 => array(16,23,29,31),39 => array(16,18,23,24,32,33)], 'boundaries' => array(2 => NORMAL,4 => NORMAL,9 => NORMAL,10 => NORMAL,14 => NORMAL,16 => NORMAL)),
4 => array("id"=> 4, "type" => RUINS, "capacity" => 4,"height"=>0, "visible" => [1 => array(3,2),2 => array(3),3 => array(),5 => array(),6 => array(5),7 => array(5,6),8 => array(3,9),9 => array(3),10 => array(),11 => array(5,10),12 => array(3,9,14,13),13 => array(3,9,14),14 => array(3),15 => array(3,16),16 => array(10),17 => array(10),18 => array(10,17),19 => array(10,17),20 => array(5,11),21 => array(3,14,22),22 => array(3,16,14),23 => array(10,16),24 => array(10,17,18),25 => array(10,17,19),26 => array(3,16,15,14,22,27,21),27 => array(3,16,15,23,28,22),28 => array(10,3,16,15,23,29),29 => array(10,16,23),30 => array(10,16,23,29),31 => array(10,17,18,23),32 => array(10,17,18,24),33 => array(10,17,24,32),34 => array(10,17,24,32,25),35 => array(3,16,15,23,28,22,27,36),36 => array(10,16,23,29,30,28),37 => array(10,16,23,29,30),38 => array(10,17,18,23,31),39 => array(10,17,18,24,32,33)], 'boundaries' => array(3 => NORMAL,5 => NORMAL,10 => NORMAL)),
5 => array("id"=> 5, "type" => ROCK, "capacity" => 2,"height"=>0, "visible" => [1 => array(4,3,2),2 => array(4,3),3 => array(4),4 => array(),6 => array(),7 => array(6),8 => array(4,3,9),9 => array(4,3),10 => array(),11 => array(),12 => array(4,3,14,13),13 => array(4,10,3,14),14 => array(4,10,3,16,15),15 => array(10,16),16 => array(10),17 => array(10),18 => array(10,17),19 => array(11),20 => array(11),21 => array(4,10,16,15,14,22),22 => array(10,16,15),23 => array(10,17,18,16),24 => array(10,17),25 => array(11,19),26 => array(10,16,15,23,22,27,35),27 => array(10,17,16,15,23,29,28),28 => array(10,17,16,23,29),29 => array(10,17,18,16,23),30 => array(10,17,18,23,29),31 => array(10,17,18,23),32 => array(10,17,24),33 => array(10,17,24,32),34 => array(11,19,24,25),35 => array(10,17,16,23,29,28,27,36),36 => array(10,17,18,16,23,29,30,28),37 => array(10,17,18,23,29,30),38 => array(10,17,18,24,32),39 => array(10,17,24,32,33)], 'boundaries' => array(4 => NORMAL,6 => NORMAL,10 => NORMAL,11 => NORMAL,19 => NORMAL)),
6 => array("id"=> 6, "type" => OPEN_GROUND, "capacity" => 4,"height"=>0, "visible" => [1 => array(5,4,3,2),2 => array(5,4,3),3 => array(5,4),4 => array(5),5 => array(),7 => array(),8 => array(5,4,3,9),9 => array(5,4,3),10 => array(5),11 => array(),12 => array(5,4,3,14,13),13 => array(5,4,10,3,14),14 => array(5,10,16,15),15 => array(5,10,16),16 => array(5,10),17 => array(11,10),18 => array(11,10,17),19 => array(11),20 => array(11,7),21 => array(5,10,16,15,22),22 => array(5,10,16,15),23 => array(5,10,17,18,16),24 => array(11,17),25 => array(11,19),26 => array(5,10,17,16,15,23,28,22,27,35),27 => array(5,10,17,16,23,29,28),28 => array(5,10,17,18,16,23,29),29 => array(5,11,10,17,18,16,23),30 => array(11,10,17,18,23,29),31 => array(11,10,17,18,23),32 => array(11,17,24),33 => array(11,19,24,32),34 => array(11,19,24,25),35 => array(5,10,17,18,16,23,29,28,27,36),36 => array(5,11,10,17,18,23,29,30,28),37 => array(11,10,17,18,23,30),38 => array(11,17,24,32),39 => array(11,19,24,32,33)], 'boundaries' => array(5 => NORMAL,7 => NORMAL,11 => NORMAL)),
7 => array("id"=> 7, "type" => FOREST, "capacity" => 3,"height"=>0, "visible" => [1 => array(6,5,4,3,2),2 => array(6,5,4,3),3 => array(6,5,4),4 => array(6,5),5 => array(6),6 => array(),8 => array(6,11,5,10,4,3,9),9 => array(6,11,5,10,4,3),10 => array(6,11),11 => array(),12 => array(6,11,10,16,15,14,13),13 => array(6,11,10,16,15,14),14 => array(6,11,10,16,15),15 => array(11,17,18,16),16 => array(11,10,17),17 => array(11),18 => array(11,17),19 => array(20),20 => array(),21 => array(11,17,18,16,15,22),22 => array(11,17,18,16,15),23 => array(11,17,18),24 => array(20,19),25 => array(20),26 => array(11,17,18,23,29,28,27,35),27 => array(11,17,18,23,29,28),28 => array(11,17,18,23,29,30),29 => array(11,17,18,23),30 => array(11,19,17,18,23),31 => array(20,11,19,24),32 => array(20,19,24),33 => array(20,19,24,32),34 => array(20,25),35 => array(11,17,18,23,29,30,28,27,36),36 => array(11,19,17,18,23,30),37 => array(20,11,19,17,18,24,23,31,30),38 => array(20,19,24,32),39 => array(20,25,34,33)], 'boundaries' => array(6 => NORMAL,11 => NORMAL,20 => NORMAL)),
8 => array("id"=> 8, "type" => OPEN_GROUND, "capacity" => 4,"height"=>0, "visible" => [1 => array(),2 => array(9),3 => array(9),4 => array(9,3),5 => array(9,3,4),6 => array(9,3,4,5),7 => array(9,3,4,10,5,11,6),9 => array(),10 => array(9,14,3,16),11 => array(9,14,3,16,10),12 => array(),13 => array(12),14 => array(12,13),15 => array(12,14,13),16 => array(14,15),17 => array(14,15,16,18),18 => array(14,13,15,16),19 => array(14,15,16,18,17),20 => array(9,14,16,10,17,11),21 => array(12),22 => array(12),23 => array(12,13,14,15),24 => array(12,14,13,15,16,23),25 => array(14,13,15,16,18,17,24),26 => array(12,21),27 => array(12,22),28 => array(12,22),29 => array(12,13,14,15,23),30 => array(12,13,22,15,23,29),31 => array(12,13,22,15,23,29,30),32 => array(12,13,14,15,23,29,31,24),33 => array(12,13,14,15,23,29,31,24,32),34 => array(12,13,14,15,23,29,24,32,33),35 => array(12,21),36 => array(12,22,27,28),37 => array(12,22,15,23,28,30),38 => array(12,13,22,15,23,29,30,31,37),39 => array(12,13,22,15,23,29,30,31,32,38)], 'boundaries' => array(1 => NORMAL,9 => NORMAL,12 => NORMAL,14 => NORMAL)),
9 => array("id"=> 9, "type" => POLAR, "capacity" => 3,"height"=>0, "visible" => [1 => array(),2 => array(),3 => array(),4 => array(3),5 => array(3,4),6 => array(3,4,5),7 => array(3,4,10,5,11,6),8 => array(),10 => array(3),11 => array(3,10),12 => array(14),13 => array(14),14 => array(),15 => array(14),16 => array(14,15),17 => array(14,16),18 => array(14,15,16),19 => array(14,15,16,18,17),20 => array(3,16,10,17,11),21 => array(14,13,22,12),22 => array(14,13),23 => array(14,15),24 => array(14,15,16,23,18),25 => array(14,15,16,18,17,19,24),26 => array(14,13,22,21),27 => array(14,13,22),28 => array(14,22,15,23),29 => array(14,15,23),30 => array(14,15,23,29),31 => array(14,15,23,29,30),32 => array(14,15,16,23,29,24),33 => array(14,15,16,23,24,32),34 => array(14,15,16,23,18,24,32),35 => array(14,13,22,27),36 => array(14,22,15,23,28),37 => array(14,15,23,29,30),38 => array(14,15,23,29,30,31),39 => array(14,15,16,23,29,31,32,38)], 'boundaries' => array(1 => NORMAL,2 => NORMAL,3 => NORMAL,8 => NORMAL,14 => NORMAL)),
10 => array("id"=> 10, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [1 => array(3,9),2 => array(4,3,9),3 => array(4),4 => array(),5 => array(),6 => array(5),7 => array(11,6),8 => array(16,3,14,9),9 => array(3),11 => array(),12 => array(16,15,14,13),13 => array(16,15,14),14 => array(16,15),15 => array(17,16),16 => array(17),17 => array(),18 => array(17),19 => array(17),20 => array(11),21 => array(17,16,15,22),22 => array(17,16,15),23 => array(17,18,16),24 => array(17),25 => array(17,19),26 => array(17,18,16,23,29,28,22,27,35),27 => array(17,18,16,23,29,28),28 => array(17,18,16,23,29),29 => array(17,18,16,23),30 => array(17,18,23,29),31 => array(17,18,23),32 => array(17,18,24),33 => array(17,24,32),34 => array(17,24,32,25),35 => array(17,18,16,23,29,28,27,36),36 => array(17,18,16,23,29,30,28),37 => array(17,18,23,29,30),38 => array(17,18,24,23,31,32),39 => array(17,24,32,33)], 'boundaries' => array(3 => NORMAL,4 => NORMAL,5 => NORMAL,11 => NORMAL,16 => NORMAL,17 => NORMAL)),
11 => array("id"=> 11, "type" => POLAR, "capacity" => 2,"height"=>0, "visible" => [1 => array(10,4,3,9),2 => array(10,4,3),3 => array(10,4),4 => array(10,5),5 => array(),6 => array(),7 => array(),8 => array(10,16,3,14,9),9 => array(10,3),10 => array(),12 => array(17,16,15,14,13),13 => array(17,16,15,14),14 => array(17,16,15),15 => array(17,18,16),16 => array(17),17 => array(),18 => array(17),19 => array(),20 => array(),21 => array(17,18,16,23,15,22),22 => array(17,18,16,23,15),23 => array(17,18),24 => array(19),25 => array(19),26 => array(17,18,23,29,28,27,35),27 => array(17,18,23,29,28),28 => array(17,18,23,29,30),29 => array(17,18,23),30 => array(17,18,23),31 => array(19,17,18,24,23),32 => array(19,24),33 => array(19,24,32),34 => array(19,24,25),35 => array(17,18,23,29,30,28,27,36),36 => array(17,18,23,30),37 => array(17,18,23,30),38 => array(19,24,32),39 => array(19,24,32,33)], 'boundaries' => array(5 => NORMAL,6 => NORMAL,7 => NORMAL,10 => NORMAL,17 => NORMAL,19 => NORMAL,20 => NORMAL)),
12 => array("id"=> 12, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [1 => array(8),2 => array(8,14,9),3 => array(13,14,9),4 => array(13,14,9,3),5 => array(13,14,3,4),6 => array(13,14,3,4,5),7 => array(13,14,15,16,10,11,6),8 => array(),9 => array(14),10 => array(13,14,15,16),11 => array(13,14,15,16,17),13 => array(),14 => array(22,13),15 => array(22,13),16 => array(22,13,14,15),17 => array(22,13,14,15,16,18),18 => array(22,13,14,15,16),19 => array(22,13,15,16,23,18,17),20 => array(13,14,15,16,18,17,11),21 => array(),22 => array(),23 => array(22,15),24 => array(22,15,23,29),25 => array(22,15,23,18,24),26 => array(21),27 => array(21,22),28 => array(22),29 => array(22,15,23),30 => array(22,23,28),31 => array(22,15,23,29,30),32 => array(22,15,23,29,30,31),33 => array(22,15,23,29,30,31,32),34 => array(22,15,23,29,30,31,24,32,33),35 => array(21),36 => array(22,27,28),37 => array(22,28,30),38 => array(22,23,28,30,37),39 => array(22,15,23,28,30,31,32,38)], 'boundaries' => array(8 => NORMAL,13 => NORMAL,14 => NORMAL,21 => NORMAL,22 => NORMAL)),
13 => array("id"=> 13, "type" => ROCK, "capacity" => 2,"height"=>0, "visible" => [1 => array(14,8),2 => array(14,9),3 => array(14,9),4 => array(14,9,3),5 => array(14,3,10,4),6 => array(14,3,10,4,5),7 => array(14,15,16,10,11,6),8 => array(12),9 => array(14),10 => array(14,15,16),11 => array(14,15,16,17),12 => array(),14 => array(),15 => array(14),16 => array(14,15),17 => array(14,15,16,18),18 => array(14,15,16),19 => array(14,15,16,18,17),20 => array(14,15,16,17,11),21 => array(22),22 => array(),23 => array(14,15),24 => array(14,15,23),25 => array(14,15,16,23,18,17,24),26 => array(22,21),27 => array(22),28 => array(22,15,23),29 => array(14,22,15,23),30 => array(22,15,23,29),31 => array(14,22,15,23,29,30),32 => array(14,22,15,23,29,30,31,24),33 => array(14,22,15,23,29,30,31,24,32),34 => array(14,15,23,29,24,32,33),35 => array(22,27),36 => array(22,28),37 => array(22,15,23,28,30),38 => array(22,15,23,29,30,31,37),39 => array(14,22,15,23,29,30,31,32,38)], 'boundaries' => array(12 => NORMAL,14 => NORMAL,22 => NORMAL)),
14 => array("id"=> 14, "type" => OPEN_GROUND, "capacity" => 4,"height"=>0, "visible" => [1 => array(9,8),2 => array(9),3 => array(9),4 => array(3),5 => array(15,16,3,10,4),6 => array(15,16,10,5),7 => array(15,16,10,11,6),8 => array(13,12),9 => array(),10 => array(15,16),11 => array(15,16,17),12 => array(13,22),13 => array(),15 => array(),16 => array(15),17 => array(15,16,18),18 => array(15,16),19 => array(15,16,18,17),20 => array(15,16,18,17,11),21 => array(22),22 => array(),23 => array(15),24 => array(15,23),25 => array(15,16,23,18,17,24),26 => array(22,27,21),27 => array(22),28 => array(22,15,23),29 => array(15,23),30 => array(15,23,29),31 => array(15,23,29,30),32 => array(15,23,29,31,24),33 => array(15,23,29,31,24,32),34 => array(15,23,29,24,32,33),35 => array(22,27),36 => array(22,15,23,28),37 => array(15,23,29,30),38 => array(15,23,29,30,31),39 => array(15,23,29,30,31,32,38)], 'boundaries' => array(3 => NORMAL,8 => NORMAL,9 => NORMAL,12 => NORMAL,13 => NORMAL,15 => NORMAL,16 => NORMAL,22 => NORMAL)),
15 => array("id"=> 15, "type" => POLAR, "capacity" => 2,"height"=>0, "visible" => [1 => array(14,9,8),2 => array(14,9),3 => array(14),4 => array(16,3),5 => array(16,10),6 => array(16,10,5),7 => array(16,18,17,11),8 => array(14,13,12),9 => array(14),10 => array(16,17),11 => array(16,18,17),12 => array(22,13),13 => array(14),14 => array(),16 => array(),17 => array(16,18),18 => array(16),19 => array(16,23,18,17),20 => array(16,18,17,11),21 => array(22),22 => array(),23 => array(),24 => array(23),25 => array(16,23,18,24),26 => array(23,22,27,35),27 => array(23,28,22),28 => array(23,29),29 => array(23),30 => array(23,29),31 => array(23,29,30),32 => array(23,29,31,24),33 => array(23,29,31,24,32),34 => array(23,29,24,32,33),35 => array(23,28,22,27,36),36 => array(23,29,28),37 => array(23,29,30),38 => array(23,29,30,31),39 => array(23,29,30,31,32,38)], 'boundaries' => array(14 => NORMAL,16 => NORMAL,22 => NORMAL,23 => NORMAL)),
16 => array("id"=> 16, "type" => BURNING, "capacity" => 3,"height"=>0, "visible" => [1 => array(15,14,9),2 => array(15,14,9),3 => array(),4 => array(10),5 => array(10),6 => array(10,5),7 => array(17,10,11),8 => array(15,14),9 => array(15,14),10 => array(17),11 => array(17),12 => array(15,14,13,22),13 => array(15,14),14 => array(15),15 => array(),17 => array(18),18 => array(),19 => array(18,17),20 => array(18,17,11),21 => array(15,22),22 => array(15),23 => array(),24 => array(18),25 => array(18,17,24),26 => array(15,23,22,27,35),27 => array(15,23,28,22),28 => array(23,29),29 => array(23),30 => array(23,29),31 => array(23,29),32 => array(23,24),33 => array(23,24,32),34 => array(18,24,32),35 => array(15,23,29,28,27,36),36 => array(23,29,30,28),37 => array(23,29,30),38 => array(23,29,31),39 => array(23,31,24,32,33,38)], 'boundaries' => array(3 => NORMAL,10 => NORMAL,14 => NORMAL,15 => NORMAL,17 => NORMAL,18 => NORMAL,23 => NORMAL)),
17 => array("id"=> 17, "type" => OPEN_GROUND, "capacity" => 4,"height"=>0, "visible" => [1 => array(16,14,9),2 => array(10,16,3,9),3 => array(10),4 => array(10),5 => array(10),6 => array(10,11),7 => array(11),8 => array(18,16,15,14),9 => array(16,14),10 => array(),11 => array(),12 => array(18,16,15,14,13,22),13 => array(18,16,15,14),14 => array(18,16,15),15 => array(18,16),16 => array(18),18 => array(),19 => array(),20 => array(11),21 => array(18,16,23,15,22),22 => array(18,16,23,15),23 => array(18),24 => array(18),25 => array(19,24),26 => array(18,23,29,28,27,35),27 => array(18,23,29,28),28 => array(18,23,29,30),29 => array(18,23),30 => array(18,23,29),31 => array(18,23),32 => array(18,24),33 => array(18,24,32),34 => array(24,32),35 => array(18,23,29,30,28,27,36),36 => array(18,23,29,30,28),37 => array(18,23,30),38 => array(18,24,23,31,32),39 => array(18,24,32,33)], 'boundaries' => array(10 => NORMAL,11 => NORMAL,16 => NORMAL,18 => NORMAL,19 => NORMAL,24 => NORMAL)),
18 => array("id"=> 18, "type" => POLAR, "capacity" => 2,"height"=>0, "visible" => [1 => array(16,15,14,9),2 => array(16,3,9),3 => array(16),4 => array(17,10),5 => array(17,10),6 => array(17,10,11),7 => array(17,11),8 => array(16,15,14,13),9 => array(16,15,14),10 => array(17),11 => array(17),12 => array(16,15,14,13,22),13 => array(16,15,14),14 => array(16,15),15 => array(16),16 => array(),17 => array(),19 => array(17),20 => array(17,19),21 => array(23,15,22),22 => array(23,15),23 => array(),24 => array(),25 => array(17,24),26 => array(23,29,28,27,35),27 => array(23,29,28),28 => array(23,29,30),29 => array(23),30 => array(23,29),31 => array(23),32 => array(24),33 => array(24,32),34 => array(24,32),35 => array(23,29,30,28,27,36),36 => array(23,29,30,28),37 => array(23,29,30),38 => array(23,31),39 => array(24,32,33)], 'boundaries' => array(16 => NORMAL,17 => NORMAL,23 => NORMAL,24 => NORMAL)),
19 => array("id"=> 19, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [1 => array(17,16,14,9),2 => array(17,10,16,3,9),3 => array(17,10),4 => array(17,10),5 => array(11),6 => array(11),7 => array(20),8 => array(17,18,16,15,14),9 => array(17,18,16,15,14),10 => array(17),11 => array(),12 => array(17,18,23,16,15,22,13),13 => array(17,18,16,15,14),14 => array(17,18,16,15),15 => array(17,18,23,16),16 => array(17,18),17 => array(),18 => array(17),20 => array(),21 => array(17,18,23,29,22),22 => array(17,18,23,29),23 => array(17,18),24 => array(),25 => array(24),26 => array(24,23,30,28,27,35),27 => array(24,18,23,30,28),28 => array(24,18,23,30),29 => array(17,18,23),30 => array(24,23),31 => array(24),32 => array(24),33 => array(24,32),34 => array(24,25),35 => array(24,23,30,28,36),36 => array(24,23,31,30),37 => array(24,23,31,30),38 => array(24,32),39 => array(24,32,33)], 'boundaries' => array(11 => NORMAL,17 => NORMAL,20 => NORMAL,24 => NORMAL,25 => NORMAL)),
20 => array("id"=> 20, "type" => FOREST, "capacity" => 3,"height"=>0, "visible" => [1 => array(11,10,3,9),2 => array(11,10,4,3),3 => array(11,10,4),4 => array(11,5),5 => array(11),6 => array(7,11),7 => array(),8 => array(11,17,10,16,14,9),9 => array(11,17,10,16,3),10 => array(11),11 => array(),12 => array(11,17,18,16,15,14,13),13 => array(11,17,16,15,14),14 => array(11,17,18,16,15),15 => array(11,17,18,16),16 => array(11,17,18),17 => array(11),18 => array(19,17),19 => array(),21 => array(19,17,18,23,22),22 => array(19,17,18,23,15),23 => array(19,17,18),24 => array(19),25 => array(),26 => array(19,17,18,23,29,30,28,27,35),27 => array(19,17,18,23,29,30,28),28 => array(19,17,18,23,29,30),29 => array(19,17,18,23),30 => array(19,17,18,23),31 => array(19,24),32 => array(19,24),33 => array(19,24,32),34 => array(25),35 => array(19,17,18,23,30,28,36),36 => array(19,17,24,23,30),37 => array(19,24,23,31,30),38 => array(19,24,32),39 => array(25,34,33)], 'boundaries' => array(7 => NORMAL,11 => NORMAL,19 => NORMAL,25 => NORMAL)),
21 => array("id"=> 21, "type" => OPEN_GROUND, "capacity" => 4,"height"=>0, "visible" => [1 => array(12,8),2 => array(12,13,14,9),3 => array(22,13,14,9),4 => array(22,14,3),5 => array(22,14,15,16,10,4),6 => array(22,15,16,10,5),7 => array(22,15,16,18,17,11),8 => array(12),9 => array(22,12,13,14),10 => array(22,15,16,17),11 => array(22,15,23,16,18,17),12 => array(),13 => array(22),14 => array(22),15 => array(22),16 => array(22,15),17 => array(22,15,23,16,18),18 => array(22,15,23),19 => array(22,23,29,18,17),20 => array(22,23,18,17,19),22 => array(),23 => array(22,29),24 => array(22,28,30,23),25 => array(22,28,30,29,23,24),26 => array(),27 => array(),28 => array(22,27),29 => array(22,23),30 => array(22,27,28),31 => array(22,27,28,30),32 => array(22,27,28,30,31),33 => array(22,27,28,30,31,32),34 => array(22,27,28,30,31,32,33),35 => array(26),36 => array(27),37 => array(27,28,30),38 => array(27,28,36,37),39 => array(27,28,30,37,38)], 'boundaries' => array(12 => NORMAL,22 => NORMAL,26 => NORMAL,27 => NORMAL,35 => NORMAL)),
22 => array("id"=> 22, "type" => RUINS, "capacity" => 4,"height"=>0, "visible" => [1 => array(12,8),2 => array(13,14,9),3 => array(14,9),4 => array(14,16,3),5 => array(15,16,10),6 => array(15,16,10,5),7 => array(15,16,18,17,11),8 => array(12),9 => array(13,14),10 => array(15,16,17),11 => array(15,23,16,18,17),12 => array(),13 => array(),14 => array(),15 => array(),16 => array(15),17 => array(15,23,16,18),18 => array(15,23),19 => array(23,29,18,17),20 => array(15,23,18,17,19),21 => array(),23 => array(15,29),24 => array(23,29,30),25 => array(23,29,24),26 => array(27,21),27 => array(),28 => array(),29 => array(23),30 => array(28),31 => array(28,30),32 => array(28,30,31),33 => array(28,30,31,32),34 => array(28,30,31,32,33),35 => array(27),36 => array(27,28),37 => array(28,30),38 => array(28,30,37),39 => array(28,30,31,32,38)], 'boundaries' => array(12 => NORMAL,13 => NORMAL,14 => NORMAL,15 => NORMAL,21 => NORMAL,23 => NORMAL,27 => NORMAL,28 => NORMAL)),
23 => array("id"=> 23, "type" => BURNING, "capacity" => 4,"height"=>0, "visible" => [1 => array(15,14,9,8),2 => array(16,15,14,9),3 => array(16),4 => array(16,10),5 => array(16,18,17,10),6 => array(16,18,17,10,5),7 => array(18,17,11),8 => array(15,14,13,12),9 => array(15,14),10 => array(16,18,17),11 => array(18,17),12 => array(15,22),13 => array(15,14),14 => array(15),15 => array(),16 => array(),17 => array(18),18 => array(),19 => array(18,17),20 => array(18,17,19),21 => array(29,22),22 => array(29,15),24 => array(29),25 => array(29,18,24),26 => array(29,28,27,35),27 => array(29,28),28 => array(29,30),29 => array(),30 => array(29),31 => array(29,30),32 => array(29,31,24),33 => array(29,31,24,32),34 => array(29,24,32,33),35 => array(29,30,28,27,36),36 => array(29,30,28),37 => array(29,30),38 => array(29,30,31),39 => array(29,30,31,32,38)], 'boundaries' => array(15 => NORMAL,16 => NORMAL,18 => NORMAL,22 => NORMAL,24 => NORMAL,28 => NORMAL,29 => NORMAL,30 => NORMAL,31 => NORMAL)),
24 => array("id"=> 24, "type" => RUINS, "capacity" => 3,"height"=>0, "visible" => [1 => array(18,23,16,15,14,9),2 => array(18,16,3,14,9),3 => array(18,17,16),4 => array(17,18,10),5 => array(17,10),6 => array(17,11),7 => array(19,20),8 => array(23,16,15,14,13,12),9 => array(18,23,16,15,14),10 => array(17),11 => array(19),12 => array(23,29,15,22),13 => array(23,15,14),14 => array(23,15),15 => array(23),16 => array(18),17 => array(18),18 => array(),19 => array(),20 => array(19),21 => array(23,30,28,22),22 => array(23,30,29),23 => array(29),25 => array(),26 => array(31,30,28,27,35),27 => array(31,30,28),28 => array(23,30),29 => array(23),30 => array(31),31 => array(32),32 => array(),33 => array(32),34 => array(32),35 => array(31,30,28,36),36 => array(31,30,37),37 => array(31,30),38 => array(32),39 => array(32,33)], 'boundaries' => array(17 => NORMAL,18 => NORMAL,19 => NORMAL,23 => NORMAL,25 => NORMAL,31 => NORMAL,32 => NORMAL)),
25 => array("id"=> 25, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [1 => array(24,19,17,18,16,15,14,9),2 => array(24,19,17,10,16,3,9),3 => array(24,19,17,10),4 => array(19,17,10),5 => array(19,11),6 => array(19,11),7 => array(20),8 => array(24,17,18,16,15,14,13),9 => array(24,19,17,18,16,15,14),10 => array(19,17),11 => array(19),12 => array(24,18,23,15,22),13 => array(24,17,18,23,16,15,14),14 => array(24,17,18,23,16,15),15 => array(24,18,23,16),16 => array(24,17,18),17 => array(24,19),18 => array(24,17),19 => array(24),20 => array(),21 => array(24,23,30,29,28,22),22 => array(24,23,29),23 => array(24,18,29),24 => array(),26 => array(24,31,30,28,27,35),27 => array(24,31,30,28),28 => array(24,23,30),29 => array(24,18,23),30 => array(24,31),31 => array(24,32),32 => array(24),33 => array(32),34 => array(),35 => array(24,31,30,36),36 => array(24,32,31,30,37),37 => array(24,32,31,30),38 => array(24,32,33),39 => array(34,33)], 'boundaries' => array(19 => NORMAL,20 => NORMAL,24 => NORMAL,32 => NORMAL,34 => NORMAL)),
26 => array("id"=> 26, "type" => FOREST, "capacity" => 3,"height"=>0, "visible" => [1 => array(21,12,8),2 => array(21,22,13,14,9),3 => array(21,27,22,14,9),4 => array(21,27,22,15,14,16,3),5 => array(35,27,22,23,15,16,10),6 => array(35,27,22,28,23,15,16,17,10,5),7 => array(35,27,28,29,23,18,17,11),8 => array(21,12),9 => array(21,22,13,14),10 => array(35,27,22,28,23,29,16,18,17),11 => array(35,27,28,29,23,18,17),12 => array(21),13 => array(21,22),14 => array(21,27,22),15 => array(35,27,22,23),16 => array(35,27,22,23,15),17 => array(35,27,28,29,23,18),18 => array(35,27,28,29,23),19 => array(35,27,28,30,23,24),20 => array(35,27,28,30,29,23,18,17,19),21 => array(),22 => array(21,27),23 => array(35,27,28,29),24 => array(35,27,28,30,31),25 => array(35,27,28,30,31,24),27 => array(35),28 => array(35,27),29 => array(35,27,28),30 => array(35,27,28),31 => array(35,36,28,30),32 => array(35,36,30,31),33 => array(35,36,37,30,31,32),34 => array(35,36,37,30,31,32,33),35 => array(),36 => array(35),37 => array(35,36),38 => array(35,36,37),39 => array(35,36,37,38)], 'boundaries' => array(21 => NORMAL,35 => NORMAL)),
27 => array("id"=> 27, "type" => POLAR, "capacity" => 3,"height"=>0, "visible" => [1 => array(22,12,8),2 => array(22,13,14,9),3 => array(22,14,9),4 => array(22,28,23,15,16,3),5 => array(28,23,29,15,16,17,10),6 => array(28,23,29,16,17,10,5),7 => array(28,29,23,18,17,11),8 => array(22,12),9 => array(22,13,14),10 => array(28,23,29,16,18,17),11 => array(28,29,23,18,17),12 => array(22,21),13 => array(22),14 => array(22),15 => array(22,28,23),16 => array(22,28,23,15),17 => array(28,29,23,18),18 => array(28,29,23),19 => array(28,30,23,18,24),20 => array(28,30,29,23,18,17,19),21 => array(),22 => array(),23 => array(28,29),24 => array(28,30,31),25 => array(28,30,31,24),26 => array(35),28 => array(),29 => array(28,30),30 => array(28),31 => array(28,30),32 => array(28,30,31),33 => array(28,30,31,32),34 => array(28,30,31,32,33),35 => array(36),36 => array(),37 => array(28,36),38 => array(28,36,37),39 => array(28,36,37,38)], 'boundaries' => array(21 => NORMAL,22 => NORMAL,28 => NORMAL,35 => NORMAL,36 => NORMAL)),
28 => array("id"=> 28, "type" => BURNING, "capacity" => 3,"height"=>0, "visible" => [1 => array(23,22,13,12,8),2 => array(23,15,22,14,9),3 => array(23,29,15,14,9),4 => array(29,23,15,16,3,10),5 => array(29,23,16,17,10),6 => array(29,23,16,18,17,10,5),7 => array(30,29,23,18,17,11),8 => array(22,12),9 => array(23,15,22,14),10 => array(29,23,16,18,17),11 => array(30,29,23,18,17),12 => array(22),13 => array(23,15,22),14 => array(23,15,22),15 => array(23,29),16 => array(29,23),17 => array(30,29,23,18),18 => array(30,29,23),19 => array(30,23,18,24),20 => array(30,29,23,18,17,19),21 => array(27,22),22 => array(),23 => array(30,29),24 => array(30,23),25 => array(30,23,24),26 => array(27,35),27 => array(),29 => array(30),30 => array(),31 => array(30),32 => array(30,31),33 => array(30,31,32),34 => array(30,31,32,33),35 => array(27,36),36 => array(),37 => array(30),38 => array(30,37),39 => array(30,31,38)], 'boundaries' => array(22 => NORMAL,23 => NORMAL,27 => NORMAL,29 => NORMAL,30 => NORMAL,36 => NORMAL)),
29 => array("id"=> 29, "type" => ROCK, "capacity" => 3,"height"=>0, "visible" => [1 => array(23,15,14,9,8),2 => array(23,15,14,9),3 => array(23,16),4 => array(23,16,10),5 => array(23,16,18,17,10),6 => array(23,16,18,17,10,11,5),7 => array(23,18,17,11),8 => array(23,15,14,13,12),9 => array(23,15,14),10 => array(23,16,18,17),11 => array(23,18,17),12 => array(23,15,22),13 => array(23,15,22,14),14 => array(23,15),15 => array(23),16 => array(23),17 => array(23,18),18 => array(23),19 => array(23,18,17),20 => array(23,18,17,19),21 => array(23,22),22 => array(23),23 => array(),24 => array(23),25 => array(23,18,24),26 => array(28,27,35),27 => array(30,28),28 => array(30),30 => array(),31 => array(30),32 => array(30,23,31,24),33 => array(30,23,31,24,32),34 => array(23,31,24,32,33),35 => array(30,28,27,36),36 => array(30,28),37 => array(30),38 => array(30,31),39 => array(30,23,31,32,38)], 'boundaries' => array(23 => NORMAL,28 => NORMAL,30 => NORMAL)),
30 => array("id"=> 30, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [1 => array(29,23,15,22,14,13,8),2 => array(29,23,15,14,9),3 => array(29,23,16,15),4 => array(29,23,16,10),5 => array(29,23,18,17,10),6 => array(29,23,18,17,10,11),7 => array(23,18,17,19,11),8 => array(29,23,15,22,13,12),9 => array(29,23,15,14),10 => array(29,23,18,17),11 => array(23,18,17),12 => array(28,23,22),13 => array(29,23,15,22),14 => array(29,23,15),15 => array(29,23),16 => array(29,23),17 => array(29,23,18),18 => array(29,23),19 => array(23,24),20 => array(23,18,17,19),21 => array(28,27,22),22 => array(28),23 => array(29),24 => array(31),25 => array(31,24),26 => array(28,27,35),27 => array(28),28 => array(),29 => array(),31 => array(),32 => array(31),33 => array(31,32),34 => array(31,32,33),35 => array(28,36),36 => array(),37 => array(),38 => array(31,37),39 => array(31,32,38)], 'boundaries' => array(23 => NORMAL,28 => NORMAL,29 => NORMAL,31 => NORMAL,36 => NORMAL,37 => NORMAL)),
31 => array("id"=> 31, "type" => BURNING, "capacity" => 3,"height"=>0, "visible" => [1 => array(30,29,23,15,14,9,8),2 => array(30,29,23,16,15,14,9),3 => array(23,29,16),4 => array(23,18,17,10),5 => array(23,18,17,10),6 => array(23,18,17,10,11),7 => array(24,19,11,20),8 => array(30,29,23,15,22,13,12),9 => array(30,29,23,15,14),10 => array(23,18,17),11 => array(23,24,18,17,19),12 => array(30,29,23,15,22),13 => array(30,29,23,15,22,14),14 => array(30,29,23,15),15 => array(30,29,23),16 => array(23,29),17 => array(23,18),18 => array(23),19 => array(24),20 => array(24,19),21 => array(30,28,27,22),22 => array(30,28),23 => array(30,29),24 => array(32),25 => array(32,24),26 => array(30,28,36,35),27 => array(30,28),28 => array(30),29 => array(30),30 => array(),32 => array(),33 => array(32),34 => array(32,33),35 => array(30,36),36 => array(30,37),37 => array(30),38 => array(),39 => array(32,38)], 'boundaries' => array(23 => NORMAL,24 => NORMAL,30 => NORMAL,32 => NORMAL,37 => NORMAL,38 => NORMAL)),
32 => array("id"=> 32, "type" => POLAR, "capacity" => 3,"height"=>0, "visible" => [1 => array(24,31,23,29,15,14,9,8),2 => array(24,23,16,15,14,9),3 => array(24,18,16),4 => array(24,18,17,10),5 => array(24,17,10),6 => array(24,17,11),7 => array(24,19,20),8 => array(24,31,23,29,15,14,13,12),9 => array(24,23,29,16,15,14),10 => array(24,18,17),11 => array(24,19),12 => array(31,23,30,29,15,22),13 => array(24,31,23,30,29,15,22,14),14 => array(24,31,23,29,15),15 => array(24,31,23,29),16 => array(24,23),17 => array(24,18),18 => array(24),19 => array(24),20 => array(24,19),21 => array(31,30,28,27,22),22 => array(31,30,28),23 => array(24,31,29),24 => array(),25 => array(24),26 => array(31,30,36,35),27 => array(31,30,28),28 => array(31,30),29 => array(24,31,23,30),30 => array(31),31 => array(),33 => array(),34 => array(33),35 => array(31,30,37,36),36 => array(31,30,37),37 => array(31,30),38 => array(),39 => array(33)], 'boundaries' => array(24 => NORMAL,25 => NORMAL,31 => NORMAL,33 => NORMAL,34 => NORMAL,38 => NORMAL,39 => NORMAL)),
33 => array("id"=> 33, "type" => ROCK, "capacity" => 2,"height"=>0, "visible" => [1 => array(32,24,23,29,16,15,14,9,8),2 => array(32,24,23,16,14,9),3 => array(32,24,18,16),4 => array(32,24,17,10),5 => array(32,24,17,10),6 => array(32,24,19,11),7 => array(32,24,19,20),8 => array(32,24,31,23,29,15,14,13,12),9 => array(32,24,23,16,15,14),10 => array(32,24,17),11 => array(32,24,19),12 => array(32,31,23,30,29,15,22),13 => array(32,24,31,23,30,29,15,22,14),14 => array(32,24,31,23,29,15),15 => array(32,24,31,23,29),16 => array(32,24,23),17 => array(32,24,18),18 => array(32,24),19 => array(32,24),20 => array(32,24,19),21 => array(32,31,30,28,27,22),22 => array(32,31,30,28),23 => array(32,24,31,29),24 => array(32),25 => array(32),26 => array(32,31,30,37,36,35),27 => array(32,31,30,28),28 => array(32,31,30),29 => array(32,24,31,23,30),30 => array(32,31),31 => array(32),32 => array(),34 => array(),35 => array(32,31,37,36),36 => array(32,31,37),37 => array(32,31,30),38 => array(),39 => array()], 'boundaries' => array(32 => NORMAL,34 => NORMAL,38 => NORMAL,39 => NORMAL)),
34 => array("id"=> 34, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [1 => array(32,24,23,16,15,14,9,8),2 => array(32,24,18,16,3,9),3 => array(32,24,18,17,10,16),4 => array(25,32,24,17,10),5 => array(25,24,19,11),6 => array(25,24,19,11),7 => array(25,20),8 => array(33,32,24,23,29,15,14,13,12),9 => array(32,24,18,23,16,15,14),10 => array(25,32,24,17),11 => array(25,24,19),12 => array(33,32,24,31,23,30,29,15,22),13 => array(33,32,24,23,29,15,14),14 => array(33,32,24,23,29,15),15 => array(33,32,24,23,29),16 => array(32,24,18),17 => array(32,24),18 => array(32,24),19 => array(25,24),20 => array(25),21 => array(33,32,31,30,28,27,22),22 => array(33,32,31,30,28),23 => array(33,32,24,29),24 => array(32),25 => array(),26 => array(33,32,31,30,37,36,35),27 => array(33,32,31,30,28),28 => array(33,32,31,30),29 => array(33,32,24,31,23),30 => array(33,32,31),31 => array(33,32),32 => array(33),33 => array(),35 => array(33,32,31,37,36),36 => array(33,32,31,37),37 => array(33,32,31,30),38 => array(33,39),39 => array()], 'boundaries' => array(25 => NORMAL,32 => NORMAL,33 => NORMAL,39 => NORMAL)),
35 => array("id"=> 35, "type" => FOREST, "capacity" => 3,"height"=>0, "visible" => [1 => array(27,21,12,8),2 => array(27,22,13,14,9),3 => array(27,22,14,9),4 => array(36,27,22,28,23,15,16,3),5 => array(36,27,28,23,29,16,17,10),6 => array(36,27,28,29,23,16,18,17,10,5),7 => array(36,27,28,30,29,23,18,17,11),8 => array(21,12),9 => array(27,22,13,14),10 => array(36,27,28,29,23,16,18,17),11 => array(36,27,28,30,29,23,18,17),12 => array(21),13 => array(27,22),14 => array(27,22),15 => array(36,27,22,28,23),16 => array(36,27,28,23,29,15),17 => array(36,27,28,30,29,23,18),18 => array(36,27,28,30,29,23),19 => array(36,28,30,23,24),20 => array(36,28,30,23,18,17,19),21 => array(26),22 => array(27),23 => array(36,27,28,30,29),24 => array(36,28,30,31),25 => array(36,30,31,24),26 => array(),27 => array(36),28 => array(36,27),29 => array(36,27,28,30),30 => array(36,28),31 => array(36,30),32 => array(36,37,30,31),33 => array(36,37,31,32),34 => array(36,37,31,32,33),36 => array(),37 => array(36),38 => array(36,37),39 => array(36,37,38)], 'boundaries' => array(21 => NORMAL,26 => NORMAL,27 => NORMAL,36 => NORMAL)),
36 => array("id"=> 36, "type" => RUINS, "capacity" => 4,"height"=>0, "visible" => [1 => array(28,27,22,12,8),2 => array(28,23,15,22,14,9),3 => array(28,29,23,15,14),4 => array(28,30,29,23,16,10),5 => array(28,30,29,23,16,18,17,10),6 => array(28,30,29,23,18,17,10,11,5),7 => array(30,23,18,17,19,11),8 => array(28,27,22,12),9 => array(28,23,15,22,14),10 => array(28,30,29,23,16,18,17),11 => array(30,23,18,17),12 => array(28,27,22),13 => array(28,22),14 => array(28,23,15,22),15 => array(28,29,23),16 => array(28,30,29,23),17 => array(28,30,29,23,18),18 => array(28,30,29,23),19 => array(30,31,23,24),20 => array(30,23,24,17,19),21 => array(27),22 => array(28,27),23 => array(28,30,29),24 => array(37,30,31),25 => array(37,30,31,32,24),26 => array(35),27 => array(),28 => array(),29 => array(28,30),30 => array(),31 => array(37,30),32 => array(37,30,31),33 => array(37,31,32),34 => array(37,31,32,33),35 => array(),37 => array(),38 => array(37),39 => array(37,38)], 'boundaries' => array(27 => NORMAL,28 => NORMAL,30 => NORMAL,35 => NORMAL,37 => NORMAL)),
37 => array("id"=> 37, "type" => OPEN_GROUND, "capacity" => 4,"height"=>0, "visible" => [1 => array(30,28,23,15,22,13,14,8),2 => array(30,29,23,15,14,9),3 => array(30,29,23,16,15),4 => array(30,29,23,16,10),5 => array(30,29,23,18,17,10),6 => array(30,23,18,17,10,11),7 => array(30,31,23,24,18,17,19,11,20),8 => array(30,28,23,15,22,12),9 => array(30,29,23,15,14),10 => array(30,29,23,18,17),11 => array(30,23,18,17),12 => array(30,28,22),13 => array(30,28,23,15,22),14 => array(30,29,23,15),15 => array(30,29,23),16 => array(30,29,23),17 => array(30,23,18),18 => array(30,29,23),19 => array(30,31,23,24),20 => array(30,31,23,24,19),21 => array(30,28,27),22 => array(30,28),23 => array(30,29),24 => array(30,31),25 => array(30,31,32,24),26 => array(36,35),27 => array(36,28),28 => array(30),29 => array(30),30 => array(),31 => array(30),32 => array(30,31),33 => array(30,31,32),34 => array(30,31,32,33),35 => array(36),36 => array(),38 => array(),39 => array(38)], 'boundaries' => array(30 => NORMAL,31 => NORMAL,36 => NORMAL,38 => NORMAL)),
38 => array("id"=> 38, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [1 => array(31,30,29,23,15,22,14,13,9,8),2 => array(31,30,29,23,15,14,9),3 => array(31,23,29,16),4 => array(31,23,18,17,10),5 => array(32,24,18,17,10),6 => array(32,24,17,11),7 => array(32,24,19,20),8 => array(37,31,30,29,23,15,22,13,12),9 => array(31,30,29,23,15,14),10 => array(32,31,23,24,18,17),11 => array(32,24,19),12 => array(37,30,28,23,22),13 => array(37,31,30,29,23,15,22),14 => array(31,30,29,23,15),15 => array(31,30,29,23),16 => array(31,23,29),17 => array(32,31,23,24,18),18 => array(31,23),19 => array(32,24),20 => array(32,24,19),21 => array(37,36,28,27),22 => array(37,30,28),23 => array(31,30,29),24 => array(32),25 => array(33,32,24),26 => array(37,36,35),27 => array(37,36,28),28 => array(37,30),29 => array(31,30),30 => array(37,31),31 => array(),32 => array(),33 => array(),34 => array(39,33),35 => array(37,36),36 => array(37),37 => array(),39 => array()], 'boundaries' => array(31 => NORMAL,32 => NORMAL,33 => NORMAL,37 => NORMAL,39 => NORMAL)),
39 => array("id"=> 39, "type" => OPEN_GROUND, "capacity" => 3,"height"=>0, "visible" => [1 => array(38,32,31,23,30,29,15,14,9,8),2 => array(38,33,32,31,23,16,15,14,9),3 => array(33,32,24,23,18,16),4 => array(33,32,24,18,17,10),5 => array(33,32,24,17,10),6 => array(33,32,24,19,11),7 => array(33,34,25,20),8 => array(38,32,31,30,29,23,15,22,13,12),9 => array(38,32,31,23,29,16,15,14),10 => array(33,32,24,17),11 => array(33,32,24,19),12 => array(38,32,31,30,28,23,15,22),13 => array(38,32,31,30,29,23,15,22,14),14 => array(38,32,31,23,30,29,15),15 => array(38,32,31,23,30,29),16 => array(38,33,32,24,31,23),17 => array(33,32,24,18),18 => array(33,32,24),19 => array(33,32,24),20 => array(33,34,25),21 => array(38,37,30,28,27),22 => array(38,32,31,30,28),23 => array(38,32,31,30,29),24 => array(33,32),25 => array(33,34),26 => array(38,37,36,35),27 => array(38,37,36,28),28 => array(38,31,30),29 => array(38,32,31,23,30),30 => array(38,32,31),31 => array(38,32),32 => array(33),33 => array(),34 => array(),35 => array(38,37,36),36 => array(38,37),37 => array(38),38 => array()], 'boundaries' => array(33 => NORMAL,34 => NORMAL,38 => NORMAL))
),
'svg' => '
<svg
   width="2000"
   height="2000"
   viewBox="0 0 2000 2000"
   version="1.1"
   id="svg1"
   inkscape:version="1.3 (0e150ed6c4, 2023-07-21)"
   sodipodi:docname="Vigrid.svg"
   xml:space="preserve"
   xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
   xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
   xmlns:xlink="http://www.w3.org/1999/xlink"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:svg="http://www.w3.org/2000/svg"><path
     style="fill:none"
     d="m -0.27587706,1.72255 c 0,66.979831 0,133.95967 0,200.9395 44.96611806,4.0702 93.48241106,-0.69655 139.81446706,-0.0264 57.60072,-1.96032 116.34508,0.27976 173.31956,-5.17492 C 307.85933,134.47767 291.35325,72.242869 279.88911,9.9799153 275.25797,-11.434011 241.45664,4.7606611 224.48998,-0.27587713 c -74.25581,0 -148.51162,-4e-8 -222.76743,0 z"
     id="zone1"
     inkscape:label="zone1" /><path
     style="fill:none"
     d="M 459.44311,-0.21531875 C 401.8236,1.18954 343.21924,-1.9467074 286.13171,3.4787435 290.6985,38.316792 299.60266,73.252593 307.41462,107.72666 c 67.7674,9.71521 137.66075,12.71131 206.2832,18.98944 81.7095,4.43907 162.99651,16.26267 244.85667,18.25398 -1.96119,-27.3772 -16.38045,-56.814473 -22.39315,-85.279684 -6.52409,-19.550647 -8.37755,-41.540228 -16.07487,-59.96627313 -86.88112,0.0276628 -173.76233,-0.0628084 -260.64336,0.0605584 z"
     id="zone2"
     inkscape:label="zone2" /><path
     style="fill:none"
     d="M 808.8718,-0.24896229 C 783.49359,2.2005611 755.52356,-4.3521413 731.33687,4.3265613 c -2.89301,21.1664237 8.91884,42.9463017 13.1918,63.7517507 34.77117,114.038198 77.17831,225.809268 124.87462,334.984918 7.97776,9.79108 24.88769,-1.98293 35.78263,-0.10521 47.75509,-5.98893 96.22778,-8.62488 143.50388,-18.09594 0.3066,-15.98137 -12.3346,-32.49473 -17.0909,-48.28523 -36.63472,-85.4836 -65.19488,-175.48355 -70.87042,-268.747671 -3.64991,-22.378822 -0.53425,-46.906877 -4.71322,-68.10505613 -49.04782,0.008296 -98.09565,-0.0218967 -147.14346,0.0269148 z"
     id="zone3" /><path
     style="fill:none"
     d="m 964.35213,1.72255 c -0.91059,105.00889 20.21281,210.60527 63.47197,306.3313 10.5032,5.4473 25.246,-6.98306 37.06,-8.13913 69.3982,-22.70774 139.6929,-43.7176 208.7869,-66.67741 11.004,-21.20305 5.9351,-47.77564 8.2266,-71.10215 -2.0766,-59.95825 29.4003,-122.713749 85.8325,-148.136195 33.7019,-13.16261322 7.1627,-17.00032 -13.9134,-14.27484213 -129.1554,0 -258.3108,-4e-8 -387.46614,0 z"
     id="zone4" /><path
     style="fill:none"
     d="m 1427.126,-0.24896229 c -34.4623,1.03482619 -63.9844,23.63467629 -90.6358,43.30598429 -39.5503,34.17737 -49.4994,88.518748 -49.4883,138.272168 1.7298,24.96544 -8.0429,53.96637 5.4489,76.54193 29.9411,15.95716 67.1143,8.94218 99.1745,3.45856 29.5912,-8.48317 63.3589,-21.40649 79.2238,-49.50313 5.4087,-48.61152 8.0225,-98.55433 1.2987,-147.15691 -5.6009,-21.857141 -11.2794,-44.984946 -20.5966,-64.94551713 -8.1417,0.0232298 -16.2838,-0.0439286 -24.4252,0.0269148 z"
     id="zone5" /><path
     style="fill:none"
     d="m 1576.6245,-0.24896229 c -37.5329,2.02433529 -76.5528,-3.53830331 -113.3384,4.07759879 -2.5569,11.4241265 8.6588,23.7685075 9.4763,36.0008405 15.6984,56.216081 7.504,114.849423 7.2109,172.212393 57.263,16.42206 117.9905,25.23428 176.5774,38.48825 40.6989,7.1999 81.803,18.99912 122.1908,24.16951 10.4674,-29.29805 3.7628,-62.51989 6.5883,-93.26474 3.3329,-59.77625 3.5714,-120.267783 13.3758,-179.2615163 -31.5975,-5.7050006 -68.296,-0.8238751 -101.8593,-2.44925083 -40.0739,-0.002935 -80.1479,-0.001762 -120.2218,0.0269148 z"
     id="zone6" /><path
     style="fill:none"
     d="M 1805.1382,1.2784551 C 1789.2018,116.01628 1790.3723,232.51777 1790.3666,348.18327 c 2.8252,14.66521 -4.4545,35.69937 7.5652,46.73151 56.1162,-1.5916 111.5869,-15.91643 167.2871,-23.24778 27.0713,2.35263 37.5257,-11.99105 31.6453,-37.80817 0.5472,-110.70983 1.0894,-221.41969 1.6303,-332.1295514 -46.6486,-4.6752348 -96.8991,-0.6750811 -144.9365,-2.00515573 -15.8431,1.14543529 -33.53,-2.22366267 -48.4198,1.55433223 z"
     id="zone7" /><path
     style="fill:none"
     d="m 286.50178,207.06935 c -94.94522,0.66556 -189.876505,2.5298 -284.8128735,3.90938 -4.5836027,48.4381 -0.655608,100.35957 -1.96478356,150.13773 C 0.21102926,460.85978 -1.3696598,560.7354 1.8100232,660.39608 18.450227,657.82311 33.330054,640.69796 49.228991,632.95906 145.09218,569.1132 242.51273,507.36472 337.15552,441.84081 c 16.31398,-13.49855 1.83015,-37.46352 3.57293,-54.95339 -7.6831,-59.59393 -13.47401,-119.72429 -25.21919,-178.61363 -8.37527,-3.34949 -20.03253,0.0521 -29.00748,-1.20444 z"
     id="zone8" /><path
     style="fill:none"
     d="m 314.96423,117.50348 c -10.17282,3.63105 0.10334,20.90064 -1.54087,29.1084 11.0042,78.05495 26.15126,155.59706 31.80661,234.23989 49.03211,4.61465 101.08287,-2.9509 151.23616,-3.71846 113.14506,-6.10783 226.56104,-10.61283 339.5964,-16.76373 12.25783,-5.55775 -4.4621,-22.11664 -4.67645,-31.22795 -24.54241,-56.9915 -42.75914,-116.75084 -69.39321,-172.73947 -19.44786,-8.5069 -43.92082,-5.24284 -65.07335,-9.00302 C 569.59541,137.24027 442.52297,123.81852 314.96423,117.50348 Z"
     id="zone9" /><path
     style="fill:none"
     d="m 1269.3982,244.84434 c -76.548,17.21901 -150.1678,47.32068 -225.5396,68.09455 -18.7151,4.20171 -2.1029,24.23915 0.055,34.7862 22.5555,51.07088 41.8238,103.87441 66.8019,153.69173 8.7015,9.20344 24.0289,-4.048 35.0385,-1.81069 113.7682,-18.197 228.0962,-33.76644 341.3319,-54.77105 8.1016,-7.03618 -7.4702,-20.98736 -7.7245,-29.90912 -22.1121,-50.10521 -42.2428,-102.56727 -66.944,-150.56836 -42.1223,8.78362 -88.2053,16.87878 -128.5318,-2.60401 -3.5912,-5.90135 -1.0578,-18.91789 -12.5087,-16.99673 z"
     id="zone10" /><path
     style="fill:none"
     d="m 1477.1539,220.98433 c -15.9517,17.73814 -38.5111,28.92128 -57.3017,41.03168 55.0775,122.96268 106.546,247.66547 162.0207,370.39535 6.9651,12.80368 23.7287,-3.06531 34.2236,-2.48033 65.0754,-19.2681 131.8974,-35.612 195.656,-57.68779 -11.7105,-75.20773 -25.7956,-150.51873 -26.5716,-227.01999 -2.1928,-20.52911 2.9147,-43.83394 -3.1356,-62.96728 -101.6846,-19.8099 -203.3029,-42.60588 -304.8914,-61.27164 z"
     id="zone11" /><path
     style="fill:none"
     d="M 345.22996,449.38369 C 241.39488,510.49993 143.0982,580.95702 41.640924,646.02907 29.031618,659.68517 -2.5596712,662.93694 -0.27587706,684.98951 0.75082062,749.29759 -2.3038775,815.00604 1.2313542,878.45341 79.02952,896.4922 157.64537,913.39696 235.626,929.00623 c 49.19301,-44.85721 73.33955,-109.89132 95.11706,-171.18515 11.85874,-44.63338 29.08996,-91.22027 20.13304,-137.66232 -3.80143,-56.47854 1.06502,-113.37737 -3.37182,-169.69847 -0.43915,-0.8837 -1.40299,-1.20982 -2.27432,-1.0766 z"
     id="zone12" /><path
     style="fill:none"
     d="m 435.54946,462.88822 c -32.47908,0.58196 -67.8119,19.11818 -73.22183,53.91716 -11.89665,62.25955 -14.24303,134.93103 27.76266,187.0918 34.17439,31.85944 84.25794,51.46068 130.34186,35.40648 33.32835,-8.669 43.7533,-45.49861 46.42713,-75.67542 11.30917,-53.11499 -27.00678,-98.57202 -53.11547,-140.55171 -19.82772,-26.84199 -38.73794,-64.30381 -78.19435,-60.18831 z"
     id="zone13" /><path
     style="fill:none"
     d="m 825.38406,366.69456 c -158.69397,8.71253 -317.63506,16.57195 -476.49367,23.4361 0.40191,29.99967 4.37634,63.34389 7.17954,94.78735 -0.87503,13.1038 11.01644,5.40985 14.97138,-0.9622 25.68232,-27.53901 73.16027,-36.62717 105.56002,-16.14891 39.33553,39.39703 69.04463,88.12081 92.76201,138.3423 12.96424,34.04978 0.85355,71.76267 -6.18369,105.71478 -2.42684,5.84437 -10.88975,17.3918 1.29864,18.03968 39.77747,10.8418 79.3977,24.97536 120.04021,31.20775 19.73922,-12.03346 26.77645,-37.06207 42.76585,-53.35163 25.46112,-36.42725 57.42438,-67.75231 81.64801,-104.88746 31.29384,-41.17823 50.02027,-91.09726 64.4745,-140.38783 6.78144,-30.05563 -14.84173,-57.78744 -22.51511,-85.65923 -1.59129,-16.94676 -13.63756,-10.2607 -25.50769,-10.1307 z"
     id="zone14" /><path
     style="fill:none"
     d="m 881.13816,461.44154 c -13.40304,49.93182 -32.77357,99.78718 -66.36304,139.68406 -53.79549,78.76003 -120.01133,150.72689 -159.73875,238.69436 -30.45611,65.90183 -49.06399,137.73489 -55.37365,209.80514 9.48369,5.0146 13.67916,-14.7905 19.71247,-19.8089 51.98654,-76.21234 139.63451,-114.94346 216.07502,-161.32801 34.88222,-22.43808 80.48354,-39.66468 93.82515,-83.14669 22.55813,-62.02915 7.11388,-128.94549 -3.37108,-191.47893 0.66951,-10.68248 -9.57985,-20.35837 -10.03251,-31.72587 -10.59294,-33.73138 -20.32532,-68.31758 -32.91013,-101.04505 z"
     id="zone15" /><path
     style="fill:none"
     d="m 1038.6977,393.84491 c -53.73513,7.17764 -108.37028,9.72921 -161.52948,20.45528 -0.83571,19.4058 12.16987,39.38093 16.90925,58.49942 31.00127,90.14788 60.2184,184.69312 49.76325,281.1566 -0.38657,19.45841 -9.52458,37.78224 -17.25012,54.8483 13.79118,3.65973 29.5652,-8.45496 43.71644,-10.79284 56.96986,-16.2259 119.98036,-17.49746 176.34606,2.11281 7.7042,-0.1656 23.7573,16.35234 27.1436,3.72099 -10.1519,-129.55474 -50.1163,-254.43952 -101.5191,-373.1157 -10.1006,-14.63199 -7.7592,-48.59298 -33.5799,-36.88486 z"
     id="zone16" /><path
     style="fill:none"
     d="m 1486.1031,453.36036 c -123.9365,18.07765 -247.4394,39.04494 -371.3575,57.2546 5.4816,36.18642 24.2047,74.74643 55.0898,98.10052 34.4966,28.97069 74.8985,51.8636 113.6595,73.25284 27.3982,15.84032 55.8958,29.85734 81.2223,48.97828 83.1857,44.73108 131.5967,134.06858 150.3799,224.09972 0.8351,8.50957 6.7006,19.01937 15.2607,9.03667 22.2002,-14.21004 45.4341,-28.10554 65.8405,-44.23456 -5.7524,-93.1593 -9.8702,-186.97292 -18.8908,-279.57295 -27.7926,-60.73532 -50.1133,-124.31912 -81.8919,-183.05957 -2.0928,-3.02205 -5.6943,-4.55705 -9.3125,-3.85555 z"
     id="zone17" /><path
     style="fill:none"
     d="m 1145.7111,600.78642 c -3.2336,16.05842 7.9346,34.24326 9.9114,50.96999 13.8125,52.32801 16.8476,106.75972 27.7357,159.55792 12.8338,18.3611 34.2256,29.79066 48.2708,47.6008 58.7538,58.99654 113.8454,122.94833 155.9725,194.82797 10.7859,8.2299 21.1194,-10.8202 30.9923,-13.4477 30.2732,-21.7766 64.202,-40.59655 92.1633,-64.48429 -9.2906,-75.76763 -42.084,-150.7219 -100.3684,-201.72362 -32.4622,-38.07936 -78.8566,-58.01059 -121.0107,-82.77573 -51.5121,-23.25735 -97.311,-56.78355 -142.1799,-90.4917 -0.4788,-0.15435 -0.9982,-0.18117 -1.487,-0.0336 z"
     id="zone18" /><path
     style="fill:none"
     d="m 1810.2924,582.63909 c -75.5418,17.73076 -149.5452,41.62282 -224.2814,62.47608 5.5202,89.11007 10.0483,180.82576 18.033,270.72296 5.6924,12.29332 26.4732,10.95179 37.2503,18.32603 75.9114,28.7219 153.4988,56.55221 228.5001,85.82764 11.7082,-18.5351 7.8988,-44.26334 12.8955,-65.38786 7.2146,-62.03023 15.8123,-124.17513 22.0736,-186.14477 -43.7935,-52.80778 -65.8161,-118.36414 -89.7946,-181.43969 -1.0707,-1.75734 -2.1608,-4.55512 -4.6765,-4.38039 z"
     id="zone19" /><path
     style="fill:none"
     d="m 1901.3857,387.76216 c -34.2192,6.53091 -70.2411,8.93194 -103.6693,16.70066 -8.145,21.05123 3.2044,45.36899 3.8556,67.19291 17.6629,107.33998 42.8711,222.05672 120.2959,303.38412 20.5881,21.52588 45.9823,37.47431 71.829,51.87835 7.4687,-16.38359 1.0925,-40.90847 3.4904,-60.27385 1.3725,-130.5148 0.7984,-261.03941 -0.476,-391.55236 -30.0781,-0.67565 -64.121,9.2978 -95.3256,12.67017 z"
     id="zone20" /><path
     style="fill:none"
     d="M 5.2080225,886.08376 C -2.7090488,903.28608 2.7968824,925.73385 0.44562886,944.63577 -0.82384227,1082.2091 0.01572654,1219.792 -0.19513253,1357.3694 42.317232,1369.81 88.534196,1379.808 125.35589,1407.4983 c 50.76622,33.3539 81.9939,91.1788 89.74754,150.5347 9.25804,5.2056 8.30615,-13.9362 10.80631,-19.1162 13.73867,-61.582 21.5615,-124.2656 20.25329,-187.4407 0.95877,-138.2559 -1.64465,-276.6698 -12.05772,-414.57038 C 159.37329,917.54042 81.044865,903.69904 5.2080225,886.08376 Z"
     id="zone21" /><path
     style="fill:none"
     d="m 359.40737,670.90632 c -19.07305,95.2209 -52.44411,189.73606 -116.21891,264.6968 -3.2065,40.77196 3.16035,84.12958 3.46529,125.89598 3.33378,77.6022 6.36244,155.217 9.45445,232.829 39.46788,-0.027 82.16921,-6.7901 122.95357,-8.5759 70.98777,-5.8738 142.23725,-9.477 212.94367,-18.2515 1.08297,-46.1946 -4.3914,-94.9061 -2.85461,-142.2179 0.11458,-121.9035 29.6302,-244.85935 91.42465,-350.5121 2.18092,-11.18627 -19.84264,-8.06041 -26.50439,-13.14117 -35.13117,-9.64751 -70.30306,-20.05166 -105.62058,-28.43554 -29.89149,19.65313 -67.86044,18.5591 -101.57663,12.48849 -36.78221,-12.90989 -70.84654,-38.39524 -85.71032,-75.38175 z"
     id="zone22" /><path
     style="fill:none"
     d="m 1053.0231,791.79434 c -66.45008,-0.25422 -129.82078,26.10276 -183.07287,64.51968 -90.21369,54.45725 -195.18847,98.78164 -253.92328,191.13098 -12.9735,23.009 -23.51251,51.401 -18.42994,77.5888 22.06058,5.1665 46.55509,-2.7453 69.31856,-2.3047 32.29147,-3.7086 66.41766,-2.975 97.68805,-9.7801 -8.47515,-31.4253 -26.1685,-63.0597 -22.57483,-96.6714 10.27655,-15.8079 34.62878,-10.1417 50.49012,-14.2698 138.69049,-11.90814 277.61449,-21.26197 416.68429,-27.50877 15.3656,24.29465 14.7455,56.44137 7.2334,83.07937 -7.6258,18.3314 -21.5001,33.9063 -34.1886,48.0497 24.2441,48.9343 32.9202,103.7642 43.8375,156.4022 14.169,7.266 31.3659,-5.237 46.3551,-5.3837 52.0817,-12.2249 106.0159,-21.0455 156.778,-36.4016 -11.5948,-54.2071 -27.9899,-108.1191 -47.8609,-160.0129 -39.5585,-78.91542 -102.6039,-142.9116 -163.2591,-205.82711 -41.8711,-45.24055 -104.8943,-63.46877 -165.0755,-62.61065 z"
     id="zone23" /><path
     style="fill:none"
     d="m 1599.6568,926.36183 c -68.944,46.47278 -137.8863,92.94797 -206.8271,139.42557 26.5749,79.5019 48.0949,163.6645 60.3027,247.3879 -4.3781,17.9083 17.6761,13.2363 27.8984,12.1202 118.41,-14.5125 237.5209,-24.2037 355.6785,-40.0041 13.8149,-11.5944 6.1701,-35.0402 11.5956,-50.7165 7.4139,-67.6463 17.3381,-135.7071 23.784,-203.0837 -32.0519,-20.1391 -70.7723,-28.7503 -105.5749,-43.75324 -55.6216,-20.24934 -111.0303,-42.64182 -166.8572,-61.37613 z"
     id="zone24" /><path
     style="fill:none"
     d="m 1911.6268,778.96269 c -14.2274,85.65395 -21.7023,173.42727 -33.2809,259.87891 -13.3235,112.1008 -27.7302,224.0822 -41.0848,336.1705 24.95,7.5451 54.7318,5.3112 81.4527,8.9629 26.3207,0.708 54.6287,6.8961 79.7201,2.6778 -0.01,-183.1645 -0.018,-366.329 -0.027,-549.49349 -30.004,-15.28833 -57.9666,-35.58514 -83.7725,-57.73907 -0.8809,-0.52595 -2.0027,-1.0072 -3.0077,-0.45755 z"
     id="zone25" /><path
     style="fill:none"
     d="m -0.27587706,1366.4801 c 0.57543092,206.211 -1.14857534,412.9886 0.861275,618.8463 C 17.755455,1969.133 31.544201,1945.6725 49.48294,1928.2669 c 4.014716,-12.3475 15.474269,-20.0537 19.701666,-32.392 23.745837,-27.5605 38.119534,-61.9701 57.550664,-92.6476 34.6211,-65.8244 70.76745,-133.6503 82.25151,-207.9526 10.65778,-95.115 -60.25687,-182.4347 -147.035537,-213.1571 -19.890327,-6.2143 -42.711862,-16.1622 -62.22712006,-15.6375 z"
     id="zone26" /><path
     style="fill:none"
     d="m 575.93056,1275.9184 c -105.59991,10.9941 -213.54649,13.0672 -318.17383,27.406 -5.84005,58.9075 -5.89015,118.3131 -14.00245,177.0122 -4.94355,44.2298 16.39792,84.3046 39.03913,120.4567 17.47715,27.6805 32.17748,58.9488 52.11472,84.049 73.09123,-25.7588 149.62788,-40.291 224.57073,-58.5532 13.05332,-22.7756 10.22549,-51.9984 15.71394,-77.3359 10.4967,-90.583 18.46542,-181.7001 17.64029,-272.9675 -3.69907,-4.5006 -11.84432,0.5543 -16.90253,-0.067 z"
     id="zone27" /><path
     style="fill:none"
     d="m 683.68414,1127.5436 c -29.56643,3.8133 -61.64673,2.606 -89.68026,9.4807 8.50001,155.4147 4.74685,311.8181 -20.10252,465.5982 -1.04296,7.7262 -6.96303,24.3006 7.07573,20.9549 81.58653,-12.9964 164.03037,-19.2732 246.32466,-25.3807 8.79004,-26.8768 6.16173,-57.7443 9.40251,-86.102 5.48942,-112.9109 7.016,-227.2223 -11.61626,-339.0314 -5.15125,-19.5828 -28.34934,-25.7164 -39.85201,-41.0891 -15.06043,-22.0159 -43.37177,-5.5458 -65.19487,-7.5022 -12.11899,1.0239 -24.23799,2.0477 -36.35698,3.0716 z"
     id="zone28" /><path
     style="fill:none"
     d="m 1183.9907,981.84005 c -143.8791,7.62882 -287.9819,15.3031 -431.1556,31.67205 -13.0505,3.4735 -1.92846,22.68 -2.23147,31.618 9.33739,61.2509 56.5963,114.3807 117.60868,127.59 61.68979,17.3988 125.9353,2.5881 186.52519,-12.084 62.8014,-17.9545 133.4839,-48.2496 158.2674,-113.9986 5.5678,-19.9514 3.9633,-44.7348 -7.516,-62.17998 -6.2544,-4.03016 -14.4891,-2.34165 -21.4982,-2.61747 z"
     id="zone29" /><path
     style="fill:none"
     d="m 1175.9701,1112.8952 c -42.1285,26.9708 -88.7448,48.0306 -138.1875,58.6945 -64.27207,16.009 -132.99444,20.4412 -196.92923,0.7536 -13.22205,-1.7158 -3.87326,18.5306 -5.2188,25.7582 16.42668,130.8432 13.29005,263.5254 1.39016,394.6113 18.42637,9.2122 42.52727,4.5617 62.92018,7.4352 102.34859,3.2611 204.16039,15.1133 304.42039,35.7362 16.7084,5.4077 12.4608,-15.0663 13.5919,-24.6742 11.5681,-145.8109 17.8248,-295.3673 -17.6023,-438.5235 -7.1541,-19.5657 -10.5632,-42.3378 -21.9154,-59.4145 -0.776,-0.3294 -1.6315,-0.4516 -2.4694,-0.3768 z"
     id="zone30" /><path
     style="fill:none"
     d="m 1425.9081,1229.7729 c -65.2417,13.4799 -130.5614,27.6401 -195.274,43.0772 -9.4667,8.0913 1.7909,23.6511 -1.4534,34.2289 5.1968,102.3089 4.2952,204.8347 -4.414,306.8966 0.6545,10.4371 -6.5481,29.2958 10.7592,28.893 67.5174,17.545 136.7185,34.5073 206.706,33.9733 10.0639,-13.7024 5.0227,-34.2727 7.7132,-50.5148 1.3,-104.6344 5.8372,-209.7488 -3.7298,-314.1006 -5.3098,-26.9855 -3.8673,-57.1705 -16.7276,-81.8346 -1.0017,-0.8064 -2.3649,-0.9011 -3.5796,-0.619 z"
     id="zone31" /><path
     style="fill:none"
     d="m 1832.3222,1293.7091 c -125.3084,12.4283 -250.6085,25.4278 -375.6437,40.601 -2.1371,40.6916 3.955,83.841 2.355,125.5174 -0.1199,70.9098 -0.8702,141.8946 -6.2106,212.634 43.6232,6.4997 90.7923,-5.5168 133.5178,-18.5309 22.0411,-7.9835 46.2988,-16.5822 64.0237,-30.6291 -22.5341,-35.8642 -15.4443,-84.7103 13.2524,-115.4586 33.822,-49.3027 96.2104,-69.7218 153.4447,-71.8014 14.0794,-7.4577 7.1208,-29.2639 11.9268,-42.1219 3.7672,-32.8247 8.659,-65.6428 10.5202,-98.6158 -1.2763,-3.1372 -4.9709,-1.0641 -7.1863,-1.5947 z"
     id="zone32" /><path
     style="fill:none"
     d="m 1803.1667,1443.9477 c -67.3743,1.6606 -134.1149,44.7841 -156.8732,109.1599 -10.1057,35.6032 6.7934,82.9535 44.9478,93.6233 57.5077,21.0098 121.721,26.4567 181.2513,11.9165 35.51,-23.0129 38.2104,-71.5228 40.2915,-109.6039 -27.8539,-33.3245 -48.0443,-75.2599 -84.4789,-100.3386 -7.6678,-4.1361 -16.5491,-5.1834 -25.1385,-4.7572 z"
     id="zone33" /><path
     style="fill:none"
     d="m 1836.9179,1385.4349 c -0.7611,18.5757 -11.6199,39.1603 -4.6697,57.1806 28.4387,19.4 46.1819,51.6507 67.9095,78.0499 23.2032,22.3206 17.152,56.5374 12.115,84.805 -1.0583,13.9962 -12.4053,27.0015 -11.681,40.4194 26.8718,17.7355 60.3428,24.2368 90.5348,35.1643 10.1753,-3.8364 3.6575,-21.8869 6.4192,-30.5215 1.6283,-84.1504 0.5852,-168.3269 0.8882,-252.4881 -51.4471,-8.2966 -106.4945,-9.1872 -159.3964,-14.2582 z"
     id="zone34" /><path
     style="fill:none"
     d="m 235.89515,1530.1022 c -10.93029,45.7976 -23.60916,94.6718 -40.57295,139.9647 -35.53718,91.2628 -80.2667,180.5144 -142.071182,256.8812 -10.626945,15.7776 -20.595383,33.3859 -33.753179,47.9418 -25.6323395,20.9726 -9.336122,28.1655 16.554595,23.544 106.737546,0 213.475086,0 320.212626,0 25.42446,-58.601 28.48798,-127.3485 15.48948,-190.039 -21.11078,-96.0397 -83.28889,-174.68 -125.33569,-261.5652 -3.16666,-5.0158 -3.77684,-15.4995 -10.5237,-16.7275 z"
     id="zone35" /><path
     style="fill:none"
     d="m 851.63276,1605.8002 c -173.89542,2.0743 -347.36233,31.3823 -512.89599,84.203 0.83882,18.9612 14.60946,37.447 19.9753,56.2256 18.00257,43.3039 28.22205,89.2804 24.83118,136.5184 1.07052,37.2697 -2.92721,75.4419 -16.6872,109.8596 20.24114,8.8294 45.4367,2.8737 67.35406,5.2894 180.82253,1.0407 361.65089,0.3197 542.47575,0.5377 4.60586,-47.4985 0.50712,-98.424 0.67103,-147.2693 -1.91981,-79.9528 -2.65458,-160.0092 -6.67305,-239.8671 -32.01339,-6.1803 -67.08561,-3.8781 -100.23067,-5.4809 -6.27332,-0.08 -12.54688,-0.018 -18.82041,-0.016 z"
     id="zone36" /><path
     style="fill:none"
     d="m 977.2107,1614.1034 c 5.38511,127.3867 6.45731,254.9001 8.78769,382.3657 48.82831,4.5837 101.14011,0.6556 151.30851,1.9648 50.2242,-1.083 101.9786,2.1501 151.2614,-1.588 12.6981,-105.8782 18.8659,-212.6443 27.0681,-319.0039 3.8661,-15.6225 -19.847,-10.8047 -28.3683,-15.9305 -101.377,-22.8927 -203.7666,-47.0034 -308.05898,-49.8806 z"
     id="zone37" /><path
     style="fill:none"
     d="m 1652.7531,1633.5292 c -35.5948,14.3858 -70.331,31.5918 -108.487,38.2661 -63.3699,14.1902 -129.9294,11.1721 -194.0407,4.0843 -24.0991,-11.5026 -31.9778,2.6551 -29.4403,25.4835 -8.763,98.2399 -14.7589,196.779 -25.7639,294.7898 35.3274,5.3168 75.1594,0.7645 112.2214,2.281 87.2579,0 174.5159,0 261.7738,0 10.4067,-31.0312 8.2935,-67.2531 13.9125,-100.1347 7.7463,-78.1237 17.1928,-156.5286 24.4075,-234.4303 -7.5632,-11.7309 -26.2296,-10.7468 -36.584,-20.213 -6.5842,-2.3015 -10.4515,-10.9908 -17.9993,-10.1267 z"
     id="zone38" /><path
     style="fill:none"
     d="m 1890.7409,1654.4084 c -30.5906,24.7287 -73.4216,17.8295 -109.9654,16.8467 -20.6486,-0.1842 -42.4886,-7.7046 -62.6664,-4.1093 -11.8797,24.6864 -8.153,54.8495 -13.3003,81.5287 -9.2982,82.453 -16.1039,165.2366 -27.1191,247.4313 34.1012,5.4256 72.8705,0.781 108.7696,2.3281 70.0167,0 140.0335,0 210.0502,0 4.4166,-46.1058 0.1041,-95.7799 1.1623,-143.2585 -1.2188,-54.9429 1.1367,-111.0407 -2.2995,-165.2664 -34.897,-11.5221 -69.6011,-25.4724 -104.6314,-35.5006 z"
     id="zone39" /><rect
     style="fill:none"
     id="rect1"
     width="31.668697"
     height="34.104752"
     x="114.49452"
     y="53.593182" /><rect
     style="fill:none"
     id="rect2"
     width="36.540806"
     height="34.104752"
     x="548.11206"
     y="56.029232" /><rect
     style="fill:none"
     id="rect3"
     width="24.1157"
     height="24.1157"
     x="850.9397"
     y="167.08736" /><rect
     style="fill:none"
     id="rect4"
     width="25.838251"
     height="27.560801"
     x="1121.3801"
     y="58.5667" /><rect
     style="fill:none"
     id="rect5"
     width="27.560801"
     height="24.1157"
     x="1364.2596"
     y="124.02361" /><rect
     style="fill:none"
     id="rect6"
     width="27.560801"
     height="29.28335"
     x="1514.1215"
     y="55.121601" /><rect
     style="fill:none"
     id="rect7"
     width="31.005901"
     height="29.28335"
     x="1918.9208"
     y="203.26091" /><rect
     style="fill:none"
     id="rect8"
     width="29.28335"
     height="31.005901"
     x="117.1334"
     y="332.45215" /><rect
     style="fill:none"
     id="rect9"
     width="31.005901"
     height="32.728451"
     x="518.48755"
     y="287.66586" /><rect
     style="fill:none"
     id="rect10"
     width="31.005901"
     height="25.838251"
     x="1310.8606"
     y="403.07672" /><rect
     style="fill:none"
     id="rect11"
     width="32.728451"
     height="29.28335"
     x="1636.4225"
     y="439.25027" /><rect
     style="fill:none"
     id="rect12"
     width="29.28335"
     height="24.1157"
     x="146.41675"
     y="692.46509" /><rect
     style="fill:none"
     id="rect13"
     width="22.39315"
     height="27.560801"
     x="459.92087"
     y="611.50525" /><rect
     style="fill:none"
     id="rect14"
     width="24.1157"
     height="24.1157"
     x="633.89844"
     y="649.40137" /><rect
     style="fill:none"
     id="rect15"
     width="29.28335"
     height="29.28335"
     x="814.76617"
     y="764.81219" /><rect
     style="fill:none"
     id="rect16"
     width="29.28335"
     height="27.560801"
     x="981.85352"
     y="608.06018" /><rect
     style="fill:none"
     id="rect17"
     width="31.005901"
     height="29.28335"
     x="1340.1439"
     y="639.06604" /><rect
     style="fill:none"
     id="rect18"
     width="25.838251"
     height="24.1157"
     x="1918.9208"
     y="535.71307" /><rect
     style="fill:none"
     id="rect19"
     width="32.728451"
     height="27.560801"
     x="1236.7909"
     y="764.81219" /><rect
     style="fill:none"
     id="rect20"
     width="27.560801"
     height="22.39315"
     x="1701.8794"
     y="868.16522" /><rect
     style="fill:none"
     id="rect21"
     width="27.560801"
     height="25.838251"
     x="86.127502"
     y="1235.0684" /><rect
     style="fill:none"
     id="rect22"
     width="27.560801"
     height="25.838251"
     x="422.02475"
     y="1071.4261" /><rect
     style="fill:none"
     id="rect23"
     width="27.560801"
     height="31.005901"
     x="1005.9692"
     y="943.9574" /><rect
     style="fill:none"
     id="rect24"
     width="24.1157"
     height="22.39315"
     x="1004.2466"
     y="1016.3045" /><rect
     style="fill:none"
     id="rect25"
     width="29.28335"
     height="31.005901"
     x="392.74139"
     y="1526.1793" /><rect
     style="fill:none"
     id="rect26"
     width="31.005901"
     height="31.005901"
     x="694.18768"
     y="1331.5311" /><rect
     style="fill:none"
     id="rect27"
     width="29.28335"
     height="29.28335"
     x="1004.2466"
     y="1371.1498" /><rect
     style="fill:none"
     id="rect28"
     width="29.28335"
     height="27.560801"
     x="1598.5265"
     y="1169.6115" /><rect
     style="fill:none"
     id="rect29"
     width="29.28335"
     height="29.28335"
     x="1917.1982"
     y="1076.5938" /><rect
     style="fill:none"
     id="rect30"
     width="34.451"
     height="34.451"
     x="1314.3057"
     y="1443.4969" /><rect
     style="fill:none"
     id="rect31"
     width="31.005901"
     height="37.896099"
     x="1593.3588"
     y="1398.7106" /><rect
     style="fill:none"
     id="rect32"
     width="25.838251"
     height="24.1157"
     x="1758.7236"
     y="1524.4568" /><rect
     style="fill:none"
     id="rect33"
     width="32.728451"
     height="32.728451"
     x="1922.3658"
     y="1508.9537" /><rect
     style="fill:none"
     id="rect34"
     width="31.005901"
     height="29.28335"
     x="60.289249"
     y="1772.504" /><rect
     style="fill:none"
     id="rect35"
     width="25.838251"
     height="27.560801"
     x="261.82761"
     y="1846.5736" /><rect
     style="fill:none"
     id="rect36"
     width="29.28335"
     height="27.560801"
     x="692.46509"
     y="1789.7295" /><rect
     style="fill:none"
     id="rect37"
     width="27.560801"
     height="29.28335"
     x="1026.6398"
     y="1615.752" /><rect
     style="fill:none"
     id="rect38"
     width="29.28335"
     height="34.451"
     x="1486.5607"
     y="1896.5276" /><rect
     style="fill:none"
     id="rect39"
     width="24.1157"
     height="31.005901"
     x="1832.7932"
     y="1860.354" /></svg>

     ');
