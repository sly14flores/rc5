<?php

class RC5 {
	
	private $charBin, $binChar, $km, $xor_value;
	
	function __construct($km) {
		
		$this->charBin = array(0=>"0000",1=>"0001",2=>"0010",3=>"0011",4=>"0100",5=>"0101",6=>"0110",7=>"0111",8=>"1000",9=>"1001","A"=>"1010","B"=>"1011","C"=>"1100","D"=>"1101","E"=>"1110","F"=>"1111");	
		$this->binChar = array("0000"=>"0","0001"=>"1","0010"=>"2","0011"=>"3","0100"=>"4","0101"=>"5","0110"=>"6","0111"=>"7","1000"=>"8","1001"=>"9","1010"=>"A","1011"=>"B","1100"=>"C","1101"=>"D","1110"=>"E","1111"=>"F");		
		
		$this->km = strtoupper(base_convert($km,10,16));
		
	}
	
	private function kmXor($str) {

		$charBin = $this->charBin;
		$binChar = $this->binChar;		

		$str_hex = $str;
		$km_hex = $this->km;
		// var_dump($str);
		// var_dump($km_hex);
		
		if (strlen($km_hex) < strlen($str_hex)) {
			$km_hex = str_pad($km_hex,strlen($str_hex),"0",STR_PAD_LEFT);	
		}	
		// var_dump($km_hex); exit();
		$arr_pw_hex = str_split($str_hex);
		$arr_km_hex = str_split($km_hex);

		$arr_xor = [];
		foreach ($arr_pw_hex as $i => $h) {
			
			$pBin = $charBin[$arr_pw_hex[$i]];
			$kBin = $charBin[$arr_km_hex[$i]];
			
			$arr_pBin = str_split($pBin);
			$arr_kBin = str_split($kBin);
			
			$xorString = "";
			foreach ($arr_pBin as $ii => $hh) {
				$xor = intval($arr_pBin[$ii])^intval($arr_kBin[$ii]);
				$xorString .= $xor;
			}

			$arr_xor[] = $binChar[$xorString];
			
		}
		
		return join("",$arr_xor);
		
	}
	
	private function divisions($xor) {
		
		$xor_arr = str_split($xor);
		$divisions = [];
		$division = "";		
		
		$c = 0;
		$d = count($xor_arr)/4;
		foreach ($xor_arr as $i => $value) {
			if ($c == $d) {
				$divisions[] = $division;
				$division = "";
				$c = 0;				
			}
			$division .= $value;
			$c++;
			if (count($xor_arr)==($i+1)) $divisions[] = $division;
		}
		
		return $divisions;
		
	}
	
	public function encrypt($str) {

		$str_hex = strtoupper(bin2hex($str));
	
		$this->xor_value = RC5::kmXor($str_hex);
		$xor = $this->xor_value;
		
		$divisions = RC5::divisions($xor);
		
		// Left Divisions	
		$A = $divisions[0];
		$B = $divisions[1];
		
		// Right Divisions
		$C = $divisions[2];
		$D = $divisions[3];

		$Ar1 = RC5::strXor($A,$B);
		$Ar1 = RC5::leftRotate($Ar1);

		$Br1 = RC5::strXor($Ar1,$B);
		$Br1 = RC5::rightRotate($Br1);
		
		$Dr1_n = RC5::negateStr($D);
		
		$Cr1 = RC5::strXor($C,$Dr1_n);
		$Cr1 = RC5::leftRotate($Cr1);
		
		$Cr1_n = RC5::negateStr($Cr1);
		$Dr1 = RC5::strXor($Cr1_n,$Dr1_n);
		
		$Dr1 = RC5::rightRotate($Dr1);
		
		// echo "Round 1:\n";
		// echo "A. $Ar1\n";
		// echo "B. $Br1\n";
		// echo "C. $Cr1\n";
		// echo "D. $Dr1\n";
		
		$Ar2 = RC5::strXor($Ar1,$Br1);
		$Ar2 = RC5::leftRotate($Ar2);
		
		$Br2 = RC5::strXor($Ar2,$Br1);
		$Br2 = RC5::rightRotate($Br2);
		
		$Dr2_n = RC5::negateStr($Dr1);
		
		$Cr2 = RC5::strXor($Cr1,$Dr2_n);
		$Cr2 = RC5::leftRotate($Cr2);
		
		$Cr2_n = RC5::negateStr($Cr2);
		$Dr2 = RC5::strXor($Cr2_n,$Dr2_n);
		$Dr2 = RC5::rightRotate($Dr2);
		
		// echo "\n";
		// echo "Round 2:\n";
		// echo "A. $Ar2\n";
		// echo "B. $Br2\n";
		// echo "C. $Cr2\n";
		// echo "D. $Dr2\n";
		
		$Ar3 = RC5::strXor($Ar2,$Br2);
		$Ar3 = RC5::leftRotate($Ar3);
		
		$Br3 = RC5::strXor($Ar3,$Br2);
		$Br3 = RC5::rightRotate($Br3);
		
		$Dr3_n = RC5::negateStr($Dr2);
		$Cr3 = RC5::strXor($Cr2,$Dr3_n);
		$Cr3 = RC5::leftRotate($Cr3);
		
		$Cr3_n = RC5::negateStr($Cr3);
		$Dr3 = RC5::strXor($Cr3_n,$Dr3_n);
		$Dr3 = RC5::rightRotate($Dr3);
		
		// echo "\n";
		// echo "Round 3:\n";
		// echo "A. $Ar3\n";
		// echo "B. $Br3\n";
		// echo "C. $Cr3\n";
		// echo "D. $Dr3\n";
		
		$Ar4 = RC5::strXor($Ar3,$Cr3);
		$Br4 = RC5::strXor($Br3,$Cr3);
		$Cr4 = RC5::strXor($Br3,$Dr3);
		$Dr4 = RC5::strXor($Dr3,$Br3);
		
		// echo "\n";
		// echo "Round 4:\n";
		// echo "A. $Ar4\n";
		// echo "B. $Br4\n";
		// echo "C. $Cr4\n";
		// echo "D. $Dr4\n";
		
		$Ar5 = $Cr3;
		$Br5 = $Ar3;
		$Cr5 = $Dr3;
		$Dr5 = $Br3;
		
		// echo "\n";
		// echo "Round 5:\n";
		// echo "A. $Ar5\n";
		// echo "B. $Br5\n";
		// echo "C. $Cr5\n";
		// echo "D. $Dr5\n";

		return $Ar5.$Br5.$Cr5.$Dr5;

	}
	
	public function decrypt($enc) {
		
		// echo "Decryption\n";
		
		$divisions = RC5::divisions($enc);

		$Ar5 = $divisions[0];
		$Br5 = $divisions[1];
		$Cr5 = $divisions[2];
		$Dr5 = $divisions[3];
		
		// echo "\n";
		// echo "Round 5:\n";
		// echo "A. $Ar5\n";
		// echo "B. $Br5\n";
		// echo "C. $Cr5\n";
		// echo "D. $Dr5\n";
		
		$Ar3 = $Br5;
		$Br3 = $Dr5;
		$Cr3 = $Ar5;
		$Dr3 = $Cr5;
		
		// echo "\n";
		// echo "Round 3:\n";
		// echo "A. $Ar3\n";
		// echo "B. $Br3\n";
		// echo "C. $Cr3\n";
		// echo "D. $Dr3\n";

		$Br2_lr = RC5::leftRotate($Br3);
		$Br2 = RC5::strXor($Ar3,$Br2_lr);
		$Ar2_rr = RC5::rightRotate($Ar3);
		$Ar2 = RC5::strXor($Ar2_rr,$Br2);

		$Cr2_n = RC5::negateStr($Cr3);
		$Dr2_lr = RC5::leftRotate($Dr3);
		$Dr2_x = RC5::strXor($Cr2_n,$Dr2_lr);		
		$Dr2 = RC5::negateStr($Dr2_x);
		
		$Cr2_rr = RC5::rightRotate($Cr3);
		$Cr2 = RC5::strXor($Cr2_rr,$Dr2_x);		
		
		// echo "\n";
		// echo "Round 2:\n";
		// echo "A. $Ar2\n";
		// echo "B. $Br2\n";
		// echo "C. $Cr2\n";
		// echo "D. $Dr2\n";

		$Br1_lr = RC5::leftRotate($Br2);
		$Br1 = RC5::strXor($Ar2,$Br1_lr);
		$Ar1_rr = RC5::rightRotate($Ar2);
		$Ar1 = RC5::strXor($Ar1_rr,$Br1);
		
		$Dr1_lr = RC5::leftRotate($Dr2);
		$Cr1_n = RC5::negateStr($Cr2);
		$Dr1_x = RC5::strXor($Cr1_n,$Dr1_lr);
		$Dr1 = RC5::negateStr($Dr1_x);

		$Cr1_rr = RC5::rightRotate($Cr2);
		$Cr1 = RC5::strXor($Cr1_rr,$Dr1_x);		

		// echo "\n";
		// echo "Round 1:\n";
		// echo "A. $Ar1\n";
		// echo "B. $Br1\n";
		// echo "C. $Cr1\n";
		// echo "D. $Dr1\n";

		$B_lr = RC5::leftRotate($Br1);
		$B = RC5::strXor($Ar1,$B_lr);
		$A_rr = RC5::rightRotate($Ar1);
		$A = RC5::strXor($A_rr,$B);
		
		$C_n = RC5::negateStr($Cr1);
		$D_lr = RC5::leftRotate($Dr1);
		$D_x = RC5::strXor($C_n,$D_lr);
		$D = RC5::negateStr($D_x);
		
		$C_rr = RC5::rightRotate($Cr1);
		$C = RC5::strXor($C_rr,$D_x);
		
		// echo "\n";
		// echo "A. $A\n";
		// echo "B. $B\n";
		// echo "C. $C\n";
		// echo "D. $D\n";		

		$pt = $A.$B.$C.$D;	
		// echo "\n";
		// echo $pt;
		
		$dec = RC5::kmXor($pt);	
		
		return hex2bin($dec);
		
	}
	
	private function strXor($str1,$str2) {
	
		$str1_arr = str_split($str1);
		$str2_arr = str_split($str2);

		$str = "";
		foreach ($str1_arr as $k => $f) {
			$charXor = RC5::charXor($str1_arr[$k],$str2_arr[$k]);
			$str .= $charXor;	
		}		
		
		return $str;
		
	}
	
	private function charXor($char1,$char2) {
		
		$charBin = $this->charBin;
		$binChar = $this->binChar;				
		
		$char1Bin = $charBin[$char1];
		$char2Bin = $charBin[$char2];
		
		$arr1 = str_split($char1Bin);
		$arr2 = str_split($char2Bin);
		
		$charBin = "";
		foreach ($arr1 as $k => $value) {
			$xor = intval($arr1[$k])^intval($arr2[$k]);
			$charBin .= $xor;	
		}
		
		return $binChar[$charBin];
		
	}
	
	private function leftRotate($str) {
		
		$charBin = $this->charBin;
		$binChar = $this->binChar;			
		
		$str_bin = "";		
		$str_arr = str_split($str);				
		foreach ($str_arr as $k => $value) {
			
			$str_bin .= $charBin[$value];
			
		}

		$str_bin_arr = str_split($str_bin);
		
		$new_str_bin = "";
		$str_bin_first_char = "";
		foreach ($str_bin_arr as $k => $value) {
			if ($k == 0) {
				$str_bin_first_char = $value;
				continue;
			}			
			$new_str_bin .= $value;
		}
		$new_str_bin .= $str_bin_first_char;

		$new_str_bin_arr = str_split($new_str_bin);
		$divisions = [];
		$division = "";		
		
		$c = 0;
		foreach ($new_str_bin_arr as $i => $value) {
			if ($c == 4) {
				$divisions[] = $division;
				$division = "";
				$c = 0;				
			}
			$division .= $value;
			$c++;
			if (count($new_str_bin_arr)==($i+1)) $divisions[] = $division;
		}
		
		$new_str = "";
		foreach ($divisions as $division) {
			$new_str .= $binChar[$division];
		}
		
		return $new_str;
		
	}
	
	private function rightRotate($str) {
		
		$charBin = $this->charBin;
		$binChar = $this->binChar;			
		
		$str_bin = "";		
		$str_arr = str_split($str);				
		foreach ($str_arr as $k => $value) {
			
			$str_bin .= $charBin[$value];
			
		}

		$str_bin_arr = str_split($str_bin);
		$str_bin_arr_last_index = count($str_bin_arr)-1;

		$new_str_bin = $str_bin_arr[$str_bin_arr_last_index];
		foreach ($str_bin_arr as $k => $value) {
			if ($k == $str_bin_arr_last_index) break;	
			$new_str_bin .= $value;
		}

		$new_str_bin_arr = str_split($new_str_bin);
		$divisions = [];
		$division = "";		
		
		$c = 0;
		foreach ($new_str_bin_arr as $i => $value) {
			if ($c == 4) {
				$divisions[] = $division;
				$division = "";
				$c = 0;				
			}
			$division .= $value;
			$c++;
			if (count($new_str_bin_arr)==($i+1)) $divisions[] = $division;
		}

		$new_str = "";
		foreach ($divisions as $division) {
			$new_str .= $binChar[$division];
		}
		
		return $new_str;
		
	}	

	private function negateStr($str) {

		$charBin = $this->charBin;
		
		$str_arr = str_split($str);

		$str_n = "";
		foreach ($str_arr as $key => $value) {
			$str_n .= RC5::negateChar($charBin[$value]);
		}
		
		return $str_n;

	}
	
	private function negateChar($char) {
		
		$binChar = $this->binChar;
		
		$char_arr = str_split($char);

		$char_n = "";
		foreach ($char_arr as $k => $value) {
			$n = !intval($value);
			$n = ($n)?"1":"0";
			$char_n .= $n;
		}

		return $binChar[$char_n];
		
	}

	public function getXorValue() {
		
		return $this->xor_value;
		
	}
	
}

?>