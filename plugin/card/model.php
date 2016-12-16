<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('CardModel')) {
	class CardModel extends PluginModel
	{
		public function getCdkey($len=16){
            $chars = array(
                "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
                "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
                "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
                "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
                "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
                "3", "4", "5", "6", "7", "8", "9"
            );
            $charsLen = count($chars) - 1;
            shuffle($chars);
            $output = "";
            for ($i=0; $i<$len; $i++)
            {
                $output .= $chars[mt_rand(0, $charsLen)];
            }
            return $output;
        }
	}
}
