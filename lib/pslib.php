<?php
function SA_PS_begin_page($file, $page)
{
  if ($file=='help') {
    $e_msg .= ("<br><b>PSLib HELP:</b> Function SA_PS_begin_page(param1, param2) <br>");
    $e_msg .= ("param1 = ps file name <br>");
    $e_msg .= ("param2 = page number <br>");
    return("");
  }

  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_begin_page - Missing parameter: 1 (file name) <br>");
 
  if (empty($page))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_begin_page - Missing parameter: 2 (page number) <br>");

  fwrite($file, '%%Page: ' . $page . ' ' . $page . "\n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_close($file)
{
  if ($file=='help') {
    $e_msg .= ("<br><b>PSLib HELP:</b> Function SA_PS_close(param1) <br>");
    $e_msg .= ("param1 = ps file name <br>");
    return("");
  }

  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_close - Missing parameter: 1 (file name) <br>");

  fclose($file);

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_end_page($file)
{

  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_end_page: Missing parameter: 1 (file name) <br>");

  fwrite($file, "showpage \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_line($file, $xcoord_from, $ycoord_from, $xcoord_to, $ycoord_to, $linewidth)
{
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_line - Missing parameter: 1 (file name) <br>");

  if (empty($xcoord_from))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_line - Missing parameter: 2 (xcoord_from) <br>");

  if (empty($ycoord_from))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_line - Missing parameter: 3 (ycoord_from) <br>");

  if (empty($xcoord_to))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_line - Missing parameter: 4 (xcoord_to) <br>");

  if (empty($ycoord_to))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_line - Missing parameter: 5 (ycoord_to) <br>");

  if (empty($linewidth))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_line - Missing parameter: 6 (linewidth, must be >= 1) <br>");

  fwrite($file, $linewidth . " setlinewidth  \n");
  fwrite($file, $xcoord_from . ' ' . $ycoord_from  . " moveto \n");
  fwrite($file, $xcoord_to . ' ' . $ycoord_to  . " lineto \n");
  fwrite($file, "stroke \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_moveto($file, $xcoord, $ycoord)
{
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_moveto - Missing parameter: 1 (file name) <br>");
  
  if (empty($xcoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_moveto - Missing parameter: 2 (xcoord) <br>");
  
  if (empty($ycoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_moveto - Missing parameter: 3 (ycoord) <br>");

  fwrite($file, $xcoord . ' ' . $ycoord . " moveto \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_moveto_font($file, $xcoord, $ycoord, $font_name, $font_size)
{

  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_moveto_font - Missing parameter: 1 (file name) <br>");
  
  if (empty($xcoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_moveto_font - Missing parameter: 2 (xcoord) <br>");
  
  if (empty($ycoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_moveto_font - Missing parameter: 3 (ycoord) <br>");
  
  if (empty($font_name))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_moveto_font - Missing parameter: 4 (font_name) <br>");
  
  if (empty($font_size))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_moveto_font - Missing parameter: 5 (font_size) <br>");
  
  if (intval($font_size) == 0)
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_moveto_font: Incorrect value: parameter 5 (font_size) <br>");

  fwrite($file, $xcoord . ' ' . $ycoord . " moveto \n");
  fwrite($file, '/' . $font_name . ' findfont ' . $font_size . " scalefont setfont \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_open($file, $author, $title, $orientation)
{
  if ($file=='help')
  {
    $e_msg .= ("<br><b>PSLib HELP:</b> Function SA_PS_open(param1, param2, param3, param4) <br>");
    $e_msg .= ("param1 = ps file name to create <br>");
    $e_msg .= ("param2 = creator/author name <br>");
    $e_msg .= ("param3 = file title <br>");
    $e_msg .= ("param4 = orientation: Portrait, Landscape <br><br>");
    return("");
  }
  
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_open - Missing parameter: 1 (file name) <br>");
  
  if (empty($author))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_open - Missing parameter: 2 (author) <br>");
  
  if (empty($title))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_open - Missing parameter: 3 (title) <br>");
  
  if (empty($orientation)) {
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_open - Missing parameter: 4 (orientation. Assuming Portrait) <br>");
    $orientation = 'Portrait';
  }

  fwrite($file, "%!PS-Adobe-3.0 \n");
  fwrite($file, '%%Creator: ' . $author . "\n");
  fwrite($file, '%%CreationDate: ' . date("d/m/Y, H:i") . "\n");
  fwrite($file, '%%Title: ' . $title . "\n");
  fwrite($file, "%%PageOrder: Ascend \n");
  fwrite($file, '%%Orientation: ' . $orientation . "\n");
  fwrite($file, "%%EndComments \n");
  fwrite($file, "%%BeginProlog \n");
  fwrite($file, "%%BeginResource: definicoes \n");
  SA_PS_set_acent($file);
  fwrite($file, "%%EndResource \n");
  fwrite($file, "%%EndProlog \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_open_ps($file, $ps_file)
{
  if ($file=='help')
  {
    $e_msg .= ("<br><b>PSLib HELP:</b> Function SA_PS_open_ps(param1, param2) <br>");
    $e_msg .= ("param1 = ps file name to write to <br>");
    $e_msg .= ("param2 = source ps file (remember to exclude any file information like title, author,... in the top of the file)<br><br>");
    return("");
  }
  
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_open_ps - Missing parameter: 1 (file name) <br>");
  
  if (empty($ps_file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_open_ps - Missing parameter: 2 (source ps file name) <br>");

  $temp_ = fopen($ps_file,'r');
  while(!feof($temp_))
  {
    $line_ = fgets($temp_, 500);
    $cont_ = $cont_ . $line_;
  }
  fclose($temp_);
  fwrite($file, $cont_);

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_rect($file, $xcoord_from, $ycoord_from, $xcoord_to, $ycoord_to, $linewidth)
{
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect - Missing parameter: 1 (file name) <br>");
  
  if (empty($xcoord_from))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect - Missing parameter: 2 (xcoord_from) <br>");
  
  if (empty($ycoord_from))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect - Missing parameter: 3 (ycoord_from) <br>");
  
  if (empty($xcoord_to))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect - Missing parameter: 4 (xcoord_to) <br>");
  
  if (empty($ycoord_to))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect - Missing parameter: 5 (ycoord_to) <br>");
  
  if (empty($linewidth))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect - Missing parameter: 6 (linewidth, must be >= 1) <br>");
  
  fwrite($file, $linewidth . " setlinewidth  \n");
  fwrite($file, "newpath \n");
  fwrite($file, $xcoord_from . ' ' . $ycoord_from  . " moveto \n");
  fwrite($file, $xcoord_to . ' ' . $ycoord_from  . " lineto \n");
  fwrite($file, $xcoord_to . ' ' . $ycoord_to  . " lineto \n");
  fwrite($file, $xcoord_from . " " . $ycoord_to  . " lineto \n");
  fwrite($file, "closepath \n");
  fwrite($file, "stroke \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_rect_fill($file, $xcoord_from, $ycoord_from, $xcoord_to, $ycoord_to, $linewidth, $darkness)
{
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect_fill - Missing parameter: 1 (file name) <br>");
  
  if (empty($xcoord_from))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect_fill - Missing parameter: 2 (xcoord_from) <br>");

  if (empty($ycoord_from))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect_fill - Missing parameter: 3 (ycoord_from) <br>");
  
  if (empty($xcoord_to))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect_fill - Missing parameter: 4 (xcoord_to) <br>");
  
  if (empty($ycoord_to))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect_fill - Missing parameter: 5 (ycoord_to) <br>");
  
  if (empty($linewidth))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect_fill - Missing parameter: 6 (linewidth, must be >= 1) <br>");
  
  if (empty($darkness))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rect_fill - Missing parameter:  (darkness) <br>");
  
  fwrite($file, "newpath \n");
  fwrite($file, $linewidth . " setlinewidth  \n");
  fwrite($file, $xcoord_from . ' ' . $ycoord_from  . " moveto \n");
  fwrite($file, $xcoord_to . ' ' . $ycoord_from  . " lineto \n");
  fwrite($file, $xcoord_to . ' ' . $ycoord_to  . " lineto \n");
  fwrite($file, $xcoord_from . ' ' . $ycoord_to  . " lineto \n");
  fwrite($file, "closepath \n");
  fwrite($file, "gsave \n");
  fwrite($file, $darkness . " setgray  \n");
  fwrite($file, "fill \n");
  fwrite($file, "grestore \n");
  fwrite($file, "stroke \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_rotate($file, $degrees)
{
  if ($file=='help')
  {
    $e_msg .= ("<br><b>PSLib HELP:</b> Function SA_PS_rotate(param1, param2) <br>");
    $e_msg .= ("param1 = ps file name to write to <br>");
    $e_msg .= ("param2 = degrees to rotate <br>");
    $e_msg .= ("=> if param2 = 0  or  param2 = 360 -> back to normal <br><br>");
    return("");
  }

 if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rotate - Missing parameter: 1 (file name) <br>");
  
  if (empty($degrees))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_rotate - Missing parameter: 2 (degrees) <br>");
  
  if (($degrees == '0') or ($degrees == '360'))
  {
    fwrite($file, "grestore \n");
  }
  else
  {
    fwrite($file, "gsave \n");
    fwrite($file, $degrees . " rotate \n");
  }

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_set_font($file, $font_name, $font_size)
{
  if ($file=='help')
  {
    $e_msg .= ("<br><b>PSLib HELP:</b> Function SA_PS_set_font(param1, param2, param3) <br>");
    $e_msg .= ("param1 = ps file name to write to <br>");
    $e_msg .= ("param2 = font name <br>");
    $e_msg .= ("param3 = font size <br><br>");
    return("");
  }
  
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_set_font - Missing parameter: 1 (file name) <br>");
  
  if (empty($font_name))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_set_font - Missing parameter: 2 (font name) <br>");
  
  if (empty($font_size))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_set_font - Missing parameter: 3 (font size) <br>");
  
  if (intval($font_size) == 0)
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_set_font - Incorrect value: parameter 3 (font_size) <br>");

  fwrite($file, '/' . $font_name . ' findfont ' . $font_size . " scalefont setfont \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_show($file, $text)
{
  if ($file=='help')
  {
    $e_msg .= ("<br><b>PSLib HELP:</b> Function SA_PS_show(param1, param2) <br>");
    $e_msg .= ("param1 = ps file name to write to <br>");
    $e_msg .= ("param2 = text to show <br><br>");
    return("");
  }
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show - Missing parameter: 1 (file name) <br>");
  
  if (empty($text))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show - Missing parameter: 2 (text) <br>");
 
  fwrite($file, '(' . $text  . ") show \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_show_eval($file, $text)
{
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_eval - Missing parameter: 1 (file name) <br>");
  
  if (empty($text))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_eval - Missing parameter: 2 (text) <br>");

  eval ("\$text = \"$text\";");
  fwrite($file, "(" . $text  . ") show \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_show_xy($file, $text, $xcoord, $ycoord)
{
  if ($file=='help')
  {
    $e_msg .= ("<br><b>PSLib HELP:</b> Function SA_PS_show_xy(param1, param2, param3, param4) <br>");
    $e_msg .= ("param1 = ps file name to write to <br>");
    $e_msg .= ("param2 = text to show <br>");
    $e_msg .= ("param3 = X coordenate <br>");
    $e_msg .= ("param4 = Y coordenate <br><br>");
    return("");
  }

  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy - Missing parameter: 1 (file name) <br>");

  if (!isset($text))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy - Missing parameter: 2 (text) <br>");

  if (empty($xcoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy - Missing parameter: 3 (xcoord) <br>");

  if (empty($ycoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy - Missing parameter: 4 (ycoord) <br>");

  fwrite($file, $xcoord . ' ' . $ycoord . " moveto \n");
  fwrite($file, '(' . $text  . ") show \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}


function SA_PS_show_xy_font($file, $text, $xcoord, $ycoord, $font_name, $font_size)
{
  if (empty($file))
    $e_msg = ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font - Missing parameter: 1 (file name) <br>");

  if (empty($text))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font - Missing parameter: 2 (text) <br>");

  if (empty($xcoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font - Missing parameter: 3 (xcoord) <br>");

  if (empty($ycoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font - Missing parameter: 4 (ycoord) <br>");

  if (empty($font_name))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font - Missing parameter: 5 (font_name) <br>");

  if (empty($font_size))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font - Missing parameter: 6 (font_size) <br>");

  if (intval($font_size) == 0)
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font - Incorrect value: parameter 6 (font_size) <br>");

  fwrite($file, $xcoord . ' ' . $ycoord . " moveto \n");
  fwrite($file, '/' . $font_name . ' findfont ' . $font_size . " scalefont setfont \n");
  fwrite($file, '(' . $text  . ") show \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}

function SA_PS_set_acent($file)
{

  $acentos_ps = dirname(__FILE__).'/acentos.ps';
  if (file_exists($acentos_ps))
  {
    $file_acentos = fopen($acentos_ps,'r');
    while(!feof($file_acentos))
    {
      $line = fgets($file_acentos, 500);
      $acentos = $acentos . $line;
    }
    fclose($file_acentos);
    fwrite($file, $acentos . "\n");
  }
  //else // do not insert the lines (no warning message)
}

function SA_PS_circ($file, $xcoord, $ycoord, $raio, $angulo_ini, $angulo_fim, $linewidth)
{
  if (empty($file))
    $e_msg = ("<br><b>PSLib Warning:</b> Function SA_PS_circ - Missing parameter: 1 (file name) <br>");

  if (empty($xcoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_circ - Missing parameter: 2 (xcoord) <br>");

  if (empty($ycoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_circ - Missing parameter: 3 (ycoord) <br>");

  if (empty($raio))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_circ - Missing parameter: 4 (raio) <br>");

  if (empty($angulo_ini))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_circ - Missing parameter: 5 (angulo_ini) <br>");

  if (empty($angulo_fim))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_circ - Missing parameter: 6 (angulo_fim) <br>");

  if (empty($linewidth))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_circ - Missing parameter: 7 (linewidth) <br>");

  fwrite($file, "newpath  \n");
  fwrite($file, $linewidth . " setlinewidth  \n");
  fwrite($file, $xcoord . ' ' . $ycoord . ' ' . $raio . ' ' . $angulo_ini . ' ' . $angulo_fim . " arc \n");
  fwrite($file, " closepath \n");
  fwrite($file, " stroke \n");

	if(ini_get('display_errors') == 1)
        echo $e_msg;
}

//quebra nas linhas novas ou na especificaçao de final de linha
function SA_PS_show_xy_font_quebrado($file, $text, $xcoord, $ycoord, $font_name, $font_size, $n_char, $increment)
{
  if (empty($file))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font_quebrado - Missing parameter: 1 (file name) <br>");

  if (empty($text))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font_quebrado - Missing parameter: 2 (text) <br>");

  if (empty($xcoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font_quebrado - Missing parameter: 3 (xcoord) <br>");

  if (empty($ycoord))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font_quebrado - Missing parameter: 4 (ycoord) <br>");

  if (empty($font_name))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font_quebrado - Missing parameter: 5 (font_name) <br>");

  if (empty($font_size))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font_quebrado - Missing parameter: 6 (font_size) <br>");

  if (intval($font_size) == 0)
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font_quebrado - Incorrect value: parameter 6 (font_size) <br>");

  if (empty($n_char))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font_quebrado - Missing parameter: 7 (n_char) <br>");

  if (empty($increment))
    $e_msg .= ("<br><b>PSLib Warning:</b> Function SA_PS_show_xy_font_quebrado - Missing parameter: 8 (increment) <br>");

  $count = strlen($text);
  $n = explode ("\n", $text);
  $n = count($n);
  $count += $n;

  $n = ($count / $n_char);
  $y=0;
  $text .= ' ';
  for($x=0;$x<=(int)$n;$x++)
  {
    if ( strrpos(substr($text,$y,$n_char),' ') >= strrpos(substr($text,$y,$n_char),"\n") )
      $pos += strrpos(substr($text,$y,$n_char),' ');
    else
      $pos += strrpos(substr($text,$y,$n_char),"\n");

    if ( $pos < $n_char )
    {
      $string = substr($text,$y,$pos-$y +1);
      $y = $pos+1;
    }
    else
    {
      $string = substr($text,$y,$pos-$y +2);
      $pos+=1;
      $y = $pos+1;
    }

    fwrite($file, $xcoord . ' ' . $ycoord . " moveto \n");
    fwrite($file, '/' . $font_name . ' findfont ' . $font_size . " scalefont setfont \n");
    fwrite($file, '(' . $string  . ") show \n");
    $ycoord -= $increment;
  }
  $ycoord += $increment;

	if(ini_get('display_errors') == 1)
        echo $e_msg;

  return $ycoord;
}

?>
