<?php

/**
* Monta formulário  para entrada de informações da dispensa
* @author Wanderson Santiago dos Reis
* @version 1
* @since 19-02-2009
**/

$opcao = $_POST['op'];

$opcoes = array(2,3,4);


if( !in_array($opcao,$opcoes) )
	die;

?>
   <div class="panel">
    <strong>Detalhes da dispensa:</strong><br /><br />

<?php
// EDUCACAO FISICA
if ($opcao == 4) 
{
?>


Texto Legal de dispensa de Educa&ccedil;&atilde;o F&iacute;sica:<br />

<textarea name="obs_final" id="obs_final" cols="80" rows="2" readonly="readonly" >Dispensa da Educa&ccedil;&atilde;o F&iacute;sica nos termos do Decreto-Lei N&ordm; 1.044 de 21/10/1969.</textarea> 

<br />

<input type="hidden" name="ref_liberacao_ed_fisica" id="ref_liberacao_ed_fisica" size="2"  value="1" />


<?php
}
// APROVEITAMENTO DE ESTUDOS
if ($opcao == 2)
{
?>

Institui&ccedil;&atilde;o:
  <input type="text" name="ref_instituicao" id="ref_instituicao" size="6" />
  <input type="text" name="instituicao_nome" id="instituicao_nome" size="35" >

    <a href="javascript:abre_consulta_rapida('../consultas_rapidas/instituicao/index.php')">
          <img src="../../public/images/icons/lupa.png" alt="Pesquisar usu&aacute;rio" width="20" height="20" />
   </a>

<br /> <br />
Nome da disciplina na Institui&ccedil;&atilde;o de origem:
<input type="text" name="obs_aproveitamento" id="obs_aproveitamento" size="30"  value="" />
<br /><br />


Nota da disciplina na Institui&ccedil;&atilde;o de origem:
<input type="text" name="nota_final" id="nota_final" size="3"  value="" />

<br />
<?php

}
// CERTIFICACAO DE EXPERIENCIAS
if ($opcao == 3)
{
?>


Nota obtida na avalia&ccedil;&atilde;o de compet&ecirc;ncia / experi&ecirc;ncia:
<input type="text" name="nota_final" id="nota_final" size="3"  value="" />

<br />
<br />

<?php
}


?>

<br />
N&ordm do processo e/ou observa&ccedil;&otilde;es adicionais:<br />

<textarea name="processo" id="processo" cols="80" rows="2"></textarea>

<input type="hidden" name="second" value="1">

</div>



