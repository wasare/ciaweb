<?php

$opcoes = array(2,3,4);


if( !in_array($_POST['dispensa_tipo'],$opcoes) )
    die;

$erro_valida = 'Verifique os erros dos campos abaixo:'."\n\n";

$flag_erro = FALSE;

if($_POST['second'] == 1)
{

  // APROVEITAMENTO DE ESTUDOS
  if($_POST['dispensa_tipo'] == 2)
  {

    if (!is_numeric($_POST['ref_instituicao']) )
    {
        $erro_valida .= 'Instituição de origem é inválida'."\n";
        $flag_erro = TRUE;

    }

    if (empty($_POST['obs_aproveitamento']) OR strlen($_POST['obs_aproveitamento']) < 5 )
    {
        $erro_valida .= 'Nome da disciplina na Instituição de origem é inválida'."\n";
        $flag_erro = TRUE;

    }

    if (!is_numeric($_POST['nota_final']) OR  $_POST['nota_final'] < 50 OR $_POST['nota_final'] > 100 )
    {
        $erro_valida .= 'Nota da disciplina é inválida'."\n";
        $flag_erro = TRUE;

    }
  }

  // CERTIFICACAO DE EXPERIENCIA
  if($_POST['dispensa_tipo'] == 3)
  {

    if (!is_numeric($_POST['nota_final']) OR  $_POST['nota_final'] < $MEDIA_FINAL_APROVACAO OR $_POST['nota_final'] > $NOTA_MAXIMA )
    {
        $erro_valida .= 'Nota obtida na disciplina é inválida'."\n";
        $flag_erro = TRUE;

    }

  }

/*
    // EDUCAO FISICA
  if($_POST['dispensa_tipo'] == 4)
  {
  }
*/
	if($flag_processa != 1)
	{
		if( $flag_erro )
			echo iconv("utf-8", "utf-8", $erro_valida);
		else
			echo "0";

		exit();
	}
	else
	{
		if ( $flag_erro )
		{
			exit();
		}
	}
}

?>

