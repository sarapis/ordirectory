<?php
namespace App\Custom;
use Fpdf\Fpdf;

class PDF extends Fpdf
{
	public $header='';
	public $logo=null;
	
	function __construct($title='', $headers, $logo=null)		// 'img/we_gov_logo_blue.png'
	{
		$this->title = $title;
		$this->headers = $headers;
		$this->logo = $logo;
		return parent::__construct();
	}
	
	// Page header
	function Header()
	{
		// Logo
		#$this->Image('logo.png',10,-1,70);
		if ($this->logo)
		{
			#$this->Image(public_path($this->logo),10,8,50);
			$this->SetFont('Arial','B',15);
			$this->Cell(120,10,$this->logo,0,0,'L');
		}
		$this->SetFont('Arial','B',10);
		// Move to the right
		#$this->Cell(80);
		// Title
		$this->Cell(80,10,$this->title,0,0,'C');
		// Line break
		$this->Ln(20);
	}

	// Page footer
	function Footer()
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}

	function Row($data)
	{
		//Calculate the height of the row
		$nb=0;
		foreach($data as $k=>$v)
			$nb = max($nb, $this->NbLines($this->headers[$k],$v));
		$h = 5 * $nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		foreach($this->headers as $k=>$w)
		{
			#$w = $this->headers[$k];
			$v = iconv('UTF-8', 'windows-1252', $data[$k] ?? '');
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			//Draw the border
			$this->Rect($x,$y,$w,$h);
			//Print the text
			$this->MultiCell($w, 5, $v, 0, 'L');
			//Put the position to the right of the cell
			$this->SetXY($x + $w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY() + $h > $this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}
}
