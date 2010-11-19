<?php

//require_once("../core/login/check_login.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SA</title>
<link href="styles/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div align="center" />
<h2> Documentos </h2>
<?php

$dir = "docs/usuario/";

// Abre um diretorio conhecido, e faz a leitura de seu conteudo
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if (is_file($dir . $file))
               echo '<a href="'. $dir . $file .'" target="_blank">'. $file .'</a><br /><br />';
        }
        closedir($dh);
    }
}

?>
<br />

<div class="pesquisa" align="center">
<h2>Suporte e desenvolvimento</h2>
<p>
<a href="<?=$IEurl?>" target="_blank"> Instituto Federal Minas Gerais - Campus Bambu&iacute;</a><br />
GTI - Ger&ecirc;ncia de Tecnologia da Informa&ccedil;&atilde;ao<br />
Ramal de Contato: (37) 3431-4965 / 4930</p>
<div align="center">&copy;2009 <?=$IEnome?></div>
</div>

</body>
</html>
