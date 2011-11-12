<?php

require("../common.php"); 

require_once(dirname(__FILE__).'/bitmap.inc.php');

ini_set("memory_limit","25M");

if(!isset($_FILES['imgfile'])) {

  echo '<h3>Por favor, selecione a foto e complete o formul&aacute;rio</h3></p>';
  echo '<h4> '.$_GET['id'].' - '.$_GET['pessoa'].' </h4>';
  $id_foto = $_GET['id'];
}
else {

  // CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃO PERSISTENTE)
  $conn = new connection_factory($param_conn);

	$msg_error = '';
	// VALIDA CAMPOS

	if(empty($_POST['id_foto']) || !is_numeric($_POST['id_foto'])) {

        $msg_error .= 'Registro da pessoa inv&aacute;lido!<br />';
    }
					
	$err_num = strlen($msg_error);

  $pessoa = $_POST['id_foto'];
	

	// VALIDA COMPOS
 
    if(is_uploaded_file($_FILES['imgfile']['tmp_name']) && $err_num == 0) {

		  $maxsize = $_POST['MAX_FILE_SIZE'];

		  $tmpfilesize = $_FILES['imgfile']['size'];
      $tmpfilename = $_FILES['imgfile']['tmp_name'];
      $tmpfiletype = $_FILES['imgfile']['type'];
 
      // check the file is less than the maximum file size
      if($tmpfilesize < $maxsize) {
      
      // prepare the image for insertion
			//echo    $tmpfiletype;

        $file_type = exif_imagetype($tmpfilename);
        /*
                1   IMAGETYPE_GIF
                2   IMAGETYPE_JPEG
                3   IMAGETYPE_PNG
                6   IMAGETYPE_BMP
        */

        // create image from uploaded image
        switch ($file_type) {
                    case IMAGETYPE_JPEG:
                        $img = imagecreatefromjpeg($tmpfilename);
                        break;
                    case IMAGETYPE_GIF:
                        $img = imagecreatefromgif($tmpfilename);
                        break;
                    case IMAGETYPE_PNG:
                        $img = imagecreatefrompng($tmpfilename);
                        break;
                    case IMAGETYPE_BMP:
                        $img = imagecreatefrombmp($tmpfilename);
                        break;
        }

                //resize image
                $imginfo = getimagesize($tmpfilename);
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
                imagejpeg($img, NULL, 80);
                $imgdata = bin2hex(ob_get_contents());
                ob_end_clean();				

				
				$select = "SELECT ref_pessoa FROM pessoas_fotos WHERE ref_pessoa = $pessoa;";

				$ref_pessoa =  (int) $conn->get_one($select);

				if($ref_pessoa != 0 && $_POST['troca'] == 1) {
					$sql = "UPDATE pessoas_fotos SET foto = decode('{$imgdata}' , 'hex') WHERE ref_pessoa = $pessoa ;";
				}
				else {
					$sql = "INSERT INTO pessoas_fotos (ref_pessoa, foto) VALUES ($pessoa, decode('{$imgdata}' , 'hex'));";
				}

        $conn->Execute($sql);

        } 
		else {
			$msg_error .= '<h4><font color="red">Falha ao carregar o arquivo!</font></h4><br />'; 
			$msg_error .= '<div>O arquivo excedeu o limite m&aacute;ximo de tamanh d '.$maxsize.'!</div>';
			$msg_error .= '<div>O arquivo '.$_FILES['imgfile']['name'].' possui '.$_FILES['imgfile']['size'].' bytes</div> <hr />';
		}
		
	}
  else {
      // if the file is not less than the maximum allowed, print an error
		 	$msg_error .= '<h4><font color="red">Falha ao carregar o arquivo!</font></h4>';
      echo '<h5><font color="red">'.$msg_error.'</font></h5>';
			echo '<br /><a href="javascript:history.go(-1)">VOLTAR</a>';
			exit;
  }
}

$err_num = strlen($msg_error);

if(@isset($_FILES['imgfile']) && @$err_num == 0) {

	  echo '<h4>Foto Registrada</h4>';

	  echo '<img title="Foto Pessoa" src="'. $BASE_URL. 'core/pessoa_foto.php?id='. $_POST['id_foto'] .'" alt="Foto Pessoa" border="1" width="120" />';
	  echo '<br /><br /><font size="2" color="red"> Somente &eacute; exibida uma foto diferente quando nova ou substitu&iacute;da!</font> <br /> <br />';
    echo '<a href="pessoaf_edita.php?id='. $_POST['id_foto'] .'">Voltar</a>';
	  exit;
}

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
