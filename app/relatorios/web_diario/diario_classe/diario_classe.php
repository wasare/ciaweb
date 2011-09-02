<?php

require_once(dirname(__FILE__) .'/../../../setup.php');
require_once($BASE_DIR .'lib/fpdf17/fpdf.php');
require_once($BASE_DIR .'lib/fpdi/fpdi.php');

class  DiarioClassePDF extends FPDI {

  var $files = array();

	function Header() {
	}

	function Footer() {
	}

	function setFiles($files) {
		$this->files = $files;
	}

	function concat() {
		foreach($this->files as $file) {
			$pagecount = $this->setSourceFile($file);
			for ($i = 1; $i <= $pagecount; $i++) {
				$tplidx = $this->ImportPage($i);
				$s = $this->getTemplatesize($tplidx);
				$this->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
				$this->useTemplate($tplidx);
			}
		}
	}

	function TextWithDirection($x, $y, $txt, $direction='R') {

		if ($direction=='R')
			$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
		elseif ($direction=='L')
			$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
		elseif ($direction=='U')
			$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
		elseif ($direction=='D')
			$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
		else
			$s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
		if ($this->ColorFlag)
			$s='q '.$this->TextColor.' '.$s.' Q';
		$this->_out($s);
	}

	function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0) {
		$font_angle+=90+$txt_angle;
		$txt_angle*=M_PI/180;
		$font_angle*=M_PI/180;

		$txt_dx=cos($txt_angle);
		$txt_dy=sin($txt_angle);
		$font_dx=cos($font_angle);
		$font_dy=sin($font_angle);

		$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',$txt_dx,$txt_dy,$font_dx,$font_dy,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
		if ($this->ColorFlag)
			$s='q '.$this->TextColor.' '.$s.' Q';
		$this->_out($s);
	}

  //Cell with horizontal scaling if text is too wide
	function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true) {
		//Get string width
		$str_width=$this->GetStringWidth($txt);

		//Calculate ratio to fit cell
		if($w==0)
			$w = $this->w-$this->rMargin-$this->x;
		$ratio = ($w-$this->cMargin*2)/$str_width;

		$fit = ($ratio < 1 || ($ratio > 1 && $force));
		if ($fit)
		{
			if ($scale)
			{
				//Calculate horizontal scaling
				$horiz_scale=$ratio*100.0;
				//Set horizontal scaling
				$this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
			}
			else
			{
				//Calculate character spacing in points
				$char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
				//Set character spacing
				$this->_out(sprintf('BT %.2F Tc ET',$char_space));
			}
			//Override user alignment (since text will fill up cell)
			$align='';
		}

		//Pass on to Cell method
		$this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);

		//Reset character spacing/horizontal scaling
		if ($fit)
			$this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
	}

	//Cell with horizontal scaling only if necessary
	function CellFitScale($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
		$this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,false);
	}

	//Cell with horizontal scaling always
	function CellFitScaleForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
		$this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,true);
	}

	//Cell with character spacing only if necessary
	function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
		$this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
	}

	//Cell with character spacing always
	function CellFitSpaceForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
		//Same as calling CellFit directly
		$this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,true);
	}

	//Patch to also work with CJK double-byte text
	function MBGetStringLength($s) {
		if($this->CurrentFont['type']=='Type0')
		{
			$len = 0;
			$nbbytes = strlen($s);
			for ($i = 0; $i < $nbbytes; $i++)
			{
				if (ord($s[$i])<128)
					$len++;
				else
				{
					$len++;
					$i++;
				}
			}
			return $len;
		}
		else
			return strlen($s);
	}
	
	function MultiCellCountLines($w, $h, $txt, $border=0, $align='J', $fill=false) {
	
	  // Output text with automatic or explicit line breaks
	  $cw = &$this->CurrentFont['cw'];
	  if($w==0)
		  $w = $this->w-$this->rMargin-$this->x;
	  $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
	  $s = str_replace("\r",'',$txt);
	  $nb = strlen($s);
	  if($nb>0 && $s[$nb-1]=="\n")
		  $nb--;
	  $b = 0;
	  if($border)
	  { 
		  if($border==1)
		  {
		  	$border = 'LTRB';
		  	$b = 'LRT';
		  	$b2 = 'LR';
		  }
		  else
		  {
		  	$b2 = '';
		  	if(strpos($border,'L')!==false)
		  		$b2 .= 'L';
		  	if(strpos($border,'R')!==false)
		  		$b2 .= 'R';
		  	$b = (strpos($border,'T')!==false) ? $b2.'T' : $b2;
		  }
	  }
	  $sep = -1;
	  $i = 0;
	  $j = 0;
	  $l = 0;
	  $ns = 0;
	  $nl = 1;
	  while($i<$nb)
	  {
		  // Get next character
		  $c = $s[$i];
		  if($c=="\n")
		  {
			  // Explicit line break
			  if($this->ws>0)
			  {
			  	$this->ws = 0;
			  	$this->_out('0 Tw');
			  }
			  //$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			  $i++;
			  $sep = -1;
			  $j = $i;
			  $l = 0;
			  $ns = 0;
			  $nl++;
			  if($border && $nl==2)
			  	$b = $b2;
			  continue;
		  }
		  if($c==' ')
		  {
			  $sep = $i;
			  $ls = $l;
			  $ns++;
		  }
		  $l += $cw[$c];
		  if($l>$wmax)
		  {
			  // Automatic line break
			  if($sep==-1)
			  {
				  if($i==$j)
				  	$i++;
				  if($this->ws>0)
				  {
				  	$this->ws = 0;
				  	$this->_out('0 Tw');
				  }
				  //$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			  }
			  else
			  {
				  if($align=='J')
				  {
				  	$this->ws = ($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
				  	$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
				  }
				  //$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
				  $i = $sep+1;
			  }
			  $sep = -1;
			  $j = $i;
			  $l = 0;
			  $ns = 0;
			  $nl++;
			  if($border && $nl==2)
			  	$b = $b2;
		  }
		  else
		  	$i++;
	  }
	  // Last chunk
	  if($this->ws>0)
	  {
		  $this->ws = 0;
		  $this->_out('0 Tw');
	  }
	  if($border && strpos($border,'B')!==false)
		  $b .= 'B';
	  //$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
	  $this->x = $this->lMargin;
	  
	  return $nl; //THIS RETURNS THE NUMBER OF LINES!
  }
  
  function MultiCellFitLineScale($w, $h, $txt, $border=0, $align='J', $fill=false) {
	
	  // Output text with automatic fitlinescale
	  $cw = &$this->CurrentFont['cw'];
	  if($w==0)
		  $w = $this->w-$this->rMargin-$this->x;
	  $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
	  $s = str_replace("\r",'',$txt);
	  $nb = strlen($s);
	  if($nb>0 && $s[$nb-1]=="\n")
		  $nb--;
	  $b = 0;
	  if($border)
	  { 
		  if($border==1)
		  {
		  	$border = 'LTRB';
		  	$b = 'LRT';
		  	$b2 = 'LR';
		  }
		  else
		  {
		  	$b2 = '';
		  	if(strpos($border,'L')!==false)
		  		$b2 .= 'L';
		  	if(strpos($border,'R')!==false)
		  		$b2 .= 'R';
		  	$b = (strpos($border,'T')!==false) ? $b2.'T' : $b2;
		  }
	  }
	  $sep = -1;
	  $i = 0; // text lenght
	  $j = 0;
	  $l = 0;
	  $ns = 0;
	  $nl = 1; // line count
	  while($i<$nb)
	  {
		  // Get next character		  
		  for ($p = $i; $p < $nb; $p++) {
		      $c = $s[$p];
		      if($c=="\n") {
		        $i = $p;
		        break;
		      }		      
		  }		  

		  if($c=="\n")
		  {

		    $this->CellFitScale($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,0);
		    
			  // Explicit line break
			  if($this->ws>0)
			  {
			  	$this->ws = 0;
			  	$this->_out('0 Tw');
			  }
			  $i++;
			  $sep = -1;
			  $j = $i;
			  $l = 0;
			  $ns = 0;
			  $nl++;
			  if($border && $nl==2)
			  	$b = $b2;
			  continue;
		  }
		  if($c==' ')
		  {
			  $sep = $i;
			  $ls = $l;
			  $ns++;
		  }
		  $l += $cw[$c];
		  if($l>$wmax)
		  {
			  // Automatic line break
			  if($sep==-1)
			  { 
				  if($i==$j)
				  	$i++;
				  if($this->ws>0)
				  {
				  	$this->ws = 0;
				  	$this->_out('0 Tw');
				  }
			  }
			  else
			  {			    
				  if($align=='J')
				  {
				  	$this->ws = ($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
				  	$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
				  }
				  $i = $sep+1;
			  }
			  $sep = -1;
			  $j = $i;
			  $l = 0;
			  $ns = 0;
			  $nl++;
			  if($border && $nl==2)
			  	$b = $b2;
		  }
		  else
		  	$i++;
	  }
	  // Last chunk
	  if($this->ws>0)
	  {
		  $this->ws = 0;
		  $this->_out('0 Tw');
	  }
	  if($border && strpos($border,'B')!==false)
		  $b .= 'B';
	  $this->CellFitScale($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,0);
	  $this->x = $this->lMargin;
  }

}// FIM CLASSE DIARIO PDF


function list_files($dir) {
	if(is_dir($dir)){
		return glob("" . $dir . "*.pdf");
	}
}

function remove_files($dir) {

	if(!is_dir($dir))
      @mkdir("$dir",0770,true);

	if(is_dir($dir))
		$files = glob("" . $dir . "*.pdf");

	foreach($files as $f) {
		echo $image;
		@unlink($f);
	}
}


?>

