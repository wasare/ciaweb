<?php

require("../../config/configuracao.php");
require_once($BASE_DIR .'core/data/connection_factory.php');
require_once($BASE_DIR.'app/sagu/academico/bitmap.inc.php');


// CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃO PERSISTENTE)
$conn = new connection_factory($param_conn);

$dir = dirname(__FILE__).'/fotos';

if(is_dir($dir)){
  $fotos = array_merge((array) glob("" . $dir . "/*.jpg"), (array) glob("" . $dir . "/*.JPG"));
}
else
  echo '<h3>Nenhuma foto dispon&iacute;vel para importa&ccedil;&atilde;o!</h3>';


if (count($fotos) == 0) echo '<h3>Nenhuma foto dispon&iacute;vel para importa&ccedil;&atilde;o!</h3>';

//print_r($fotos);

foreach($fotos as $foto_aluno) {

//$foto_aluno = $dir .'/102933.JPG';

$info = pathinfo($foto_aluno);
$prontuario = mb_strtoupper(basename($foto_aluno,'.'.$info['extension']), 'UTF-8');

// recupera ref_pessoa
$sql_verifica = "SELECT ref_pessoa FROM pessoa_prontuario_campus ";
$sql_verifica .= " WHERE prontuario = '". $prontuario  ."';";

$ref_pessoa = (int) $conn->adodb->GetOne($sql_verifica);

if ($ref_pessoa != 0) {

  // prepare the image for insertion
  //echo    $tmpfiletype;

  $file_type = exif_imagetype($foto_aluno);
  /*
      1   IMAGETYPE_GIF
      2   IMAGETYPE_JPEG
      3   IMAGETYPE_PNG
      6   IMAGETYPE_BMP
  */

   // create image from uploaded image
   switch ($file_type) {
    case IMAGETYPE_JPEG:
        $img = imagecreatefromjpeg($foto_aluno);
        break;
    case IMAGETYPE_GIF:
        $img = imagecreatefromgif($foto_aluno);
        break;
    case IMAGETYPE_PNG:
        $img = imagecreatefrompng($foto_aluno);
        break;
    case IMAGETYPE_BMP:
        $img = imagecreatefrombmp($foto_aluno);
        break;
   }

   //resize image
   $imginfo = getimagesize($foto_aluno);
   $width = $imginfo[0];
   $height = $imginfo[1];
   $maxsize = 600;

   if (($width > $maxsize) || ($height > $maxsize)) {
      $ratio = max($width, $height) / $maxsize;
      $newwidth = floor($width / $ratio);
      $newheight = floor($height / $ratio);
      $newimg = imagecreatetruecolor($newwidth, $newheight);
      imagecopyresampled($newimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
      $img = $newimg;
   }

   //prepare image for database
   ob_start();
   imagejpeg($img, NULL, 90);
   $imgdata = bin2hex(ob_get_contents());
   ob_end_clean();				

	  $select = "SELECT ref_pessoa FROM pessoas_fotos WHERE ref_pessoa = $ref_pessoa;";

		$pessoa = (int) $conn->get_one($select);
		  
		if ($pessoa != 0) 
		  $sql = "UPDATE pessoas_fotos SET foto = decode('{$imgdata}' , 'hex') WHERE ref_pessoa = $ref_pessoa ;";
		else
		  $sql = "INSERT INTO pessoas_fotos (ref_pessoa, foto) VALUES ($ref_pessoa, decode('{$imgdata}' , 'hex'));";
			
    $ret = $conn->adodb->Execute($sql);
    
    if ($ret === FALSE)
        echo $prontuario . ' | '. $ref_pessoa  .'|<font color="red">ERRO</font> '. $conn->adodb->ErrorMsg() .'<br />';
    else 
        echo $prontuario . ' | '. $ref_pessoa  .'|<font color="green">OK</font> '. $conn->adodb->ErrorMsg() .'<br />';
      
}
else 
     echo $prontuario . ' | <font color="orange">N&Atilde;O ENCONTRADO</font> '. $conn->adodb->ErrorMsg() .'<br />';


}


die();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
    	<title>Cadastro de Fotos</title>
    </head>
    <body>
        <form enctype="multipart/form-data" action="" method="post">
            <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
            
		    Arquivo:
			<input name="imgfile" type="file" /> (formatos: jpeg, png, gif ou bmp)

			<br /><br />
		   <input name="id_foto" value="<?=$id_foto?>"  type="hidden" />
			<input name="troca" value="1"  type="checkbox" /> Substituir a foto atual (caso exista)?
			<!-- checked="checked"-->

			<br /><br />	
						
            <input type="submit" value="Enviar" />
            &nbsp;&nbsp;<a href="pessoaf_edita.php?id=<?=$id_foto?>">Cancelar</a>
        </form>
    </body>
</html>
