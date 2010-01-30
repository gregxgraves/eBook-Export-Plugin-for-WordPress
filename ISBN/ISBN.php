<?php
/*
* @Version 1.0
* @Author John F. Blyberg <blybergj@aadl.org> - http://www.blyberg.net
* @Desc ISBN Class, adapted from ISBN.pm - http://www.manasystems.co.uk/isbnpm.html
*/

class ISBN {


	public function convert($isbn) {
		$isbn2 = substr("978" . trim($isbn), 0, -1);
		$sum13 = self::genchksum13($isbn2);
		$isbn13 = "$isbn2-$sum13";
		return ($isbn13);
	}

	public function gettype($isbn) {
		$isbn = trim($isbn);
		if (preg_match('%[0-9]{12}?[0-9Xx]%s', $isbn)) {
			return 13;
		} else if (preg_match('%[0-9]{9}?[0-9Xx]%s', $isbn)) {
			return 10;
		} else {
			return -1;
		}
	}

	public function validateten($isbn) {
		$isbn = trim($isbn);
		$chksum = substr($isbn, -1, 1);
		$isbn = substr($isbn, 0, -1);
		if (preg_match('/X/i', $chksum)) { $chksum="10"; }
		$sum = &self::genchksum10($isbn);
		if ($chksum == $sum){
			return 1;
		}else{
			return 0;
		}
	}

	public function validatettn($isbn) {
		$isbn = trim($isbn);
		$chksum = substr($isbn, -1, 1);
		$isbn = substr($isbn, 0, -1);
		if (preg_match('/X/i', $chksum)) { $chksum="10"; }
		$sum = self::genchksum13($isbn);
		if ($chksum == $sum){
			return 1;
		}else{
			return 0;
		}
	}

	public function genchksum13($isbn) {
		$isbn = trim($isbn);
		for ($i = 0; $i <= 12; $i++) {
			$tc = substr($isbn, -1, 1);
			$isbn = substr($isbn, 0, -1);
			$ta = ($tc*3);
			$tci = substr($isbn, -1, 1);
			$isbn = substr($isbn, 0, -1);
			$tb = $tb + $ta + $tci;
		}
		$tg = ($tb / 10);
		$tint = intval($tg);
		if ($tint == $tg) { return 0; }
		$ts = substr($tg, -1, 1);
		$tsum = (10 - $ts);
		return $tsum;
	}

	public function genchksum10($isbn) {
		$t = 2;
		$isbn = trim($isbn);
		for($i = 0; $i <= 9; $i++){
			$b = $b + $a;
			$c = substr($isbn, -1, 1);
			$isbn = substr($isbn, 0, -1);
			$a = ($c * $t);
			$t++;
		}
		$s = ($b / 11);
		$s = intval($s);
		$s++;
		$g = ($s * 11);
		$sum = ($g - $b); 
		return $sum;
	}

	public function printinvalid() {
		print "That is an invalid ISBN number\n";
		exit;
	}
	
}



?>
