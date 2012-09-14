<?
$AppID          = "338115866266320";
$AppSecret      = "3aeab59509c17ef4dfa32bf87a585211";
 
require "src/facebook.php";
$facebook 		= new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );
$UserLogado 	= $facebook->getUser();
 
switch($_REQUEST["Acao"])
{
 
    case "AddAviso":
		{
        $Log        = $facebook->api( $UserLogado . '/apprequests', 'POST', array('message' => 'Aviso - Atenção houve alterações em suas notas/faltas, por favor consulte o boletim!') );
 
        echo "<script>alert('Criado aviso sob ID: " . $Log["request"] . "');</script>";
 
		break;
		}
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left">
        <input type="button" value="Adicionar Aviso" onclick="window.location = '?Acao=AddAviso'" />
        <input type="button" value="Deletar Todos os Avisos" onclick="window.location = '?Acao=DelTodos';" />
    </td>
  </tr>
  <tr>
    <td> </td>
  </tr>
  <tr>
    <td align="left">
        Lista de avisos cadastrados:
    </td>
  </tr>
  <?
  $Lista = $facebook->api($CodigoCliente . '/apprequests', 'GET');
 
  if( count($Lista["data"]) > 0 )
  {
  ?>
  <tr>
    <td>
 
        <? for( $i=0; $i<count($Lista["data"]); $i++ ){ ?>
 
            <a href="?Acao=DelAviso&Codigo=<?=$Lista["data"][$i]["id"]?>">
                <?=$Lista["data"][$i]["message"]?>
            </a>
            <Br />
 
        <? } ?>
 
    </td>
  </tr>
  <?
  }else{
  ?>
  <tr>
    <td align="center">
        Nenhum aviso encontrado!
    </td>
  </tr>
  <?
  }
  ?>
</table>