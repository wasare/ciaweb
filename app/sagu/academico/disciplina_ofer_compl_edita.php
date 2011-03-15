<?php

require("../common.php");
require("../lib/InvData.php"); 
require("../lib/SQLCombo.php");


$id            = $_GET['id'];
$ref_campus    = $_GET['ref_campus'];
$ref_professor = $_GET['ref_professor'];


$op3_opcoes = SQLArray("select nome, id from dias order by id");

$conn = new Connection;

$conn->Open();

  if ( !$ref_professor )
    $professor = "B.ref_professor is null and ";
  else
    $professor = "B.ref_professor = $ref_professor and ";

  $sql = " select A.id, " .
         "        B.id, " .
       	 "        A.ref_disciplina_ofer," .
         "        A.dia_semana," .
         "        B.ref_professor," .
         "        pessoa_nome(B.ref_professor)," .
         "        A.ref_horario," .
         "        A.ref_regime," .
         "        A.desconto," .
         "        A.num_creditos_desconto, " .
         "        A.num_sala, " .
         "        A.observacao, " .
         "        A.ref_professor_aux, " .
         "        pessoa_nome(A.ref_professor_aux), " .
         "        A.dia_semana_aux, " .
         "        A.ref_horario_aux, " .
         "        A.num_sala_aux, " .
         "        C.num_alunos, " .
         "        A.dt_exame " .
         " from disciplinas_ofer_compl A, disciplinas_ofer_prof B, disciplinas_ofer C " .
    	 " where A.ref_disciplina_ofer = C.id and " .
         "       B.ref_disciplina_ofer = C.id and " .
         "       A.ref_disciplina_ofer = B.ref_disciplina_ofer and " .
    	 "       A.id = B.ref_disciplina_compl and " .
    	 "       $professor" .
    	 "       B.ref_disciplina_compl = '$id' and " .
    	 "       A.id = '$id';";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro não encontrado!");

list ( $id,
       $id_prof,
       $ref_disciplina_ofer,
       $dia_semana,
       $ref_professor,
       $ref_professor_nome,
       $ref_horario,
       $ref_regime,
       $desconto,
       $num_creditos_desconto,
       $num_sala,
       $observacao,
       $ref_professor_aux,
       $ref_professor_aux_nome,
       $dia_semana_aux,
       $ref_horario_aux,
       $num_sala_aux,
       $num_alunos,
       $dt_exame) = $query->GetRowValues();

$dt_exame = InvData($dt_exame);

$query->Close();

$sql = " select ref_periodo " .
       " from disciplinas_ofer " .
       " where id = '$ref_disciplina_ofer'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro não encontrado!");

list ($ref_periodo) = $query->GetRowValues();

$query->Close();

$conn->Close();

?>
<html>
<head>
<script language="Javascript">
function ChangeOption(opt,fld)
{
  var i = opt.selectedIndex;

  if ( i != -1 )
    fld.value = opt.options[i].value;
  else
    fld.value = '';
}

function ChangeOp1()
{
  ChangeOption(document.myform.op1,document.myform.dia_semana);
}

function ChangeOp2()
{
  ChangeOption(document.myform.op2,document.myform.ref_horario);
}

function ChangeOp3()
{
  ChangeOption(document.myform.op3,document.myform.dia_semana_aux);
}

function ChangeOp4()
{
  ChangeOption(document.myform.op4,document.myform.ref_horario_aux);
}

function ChangeOp5()
{
  ChangeOption(document.myform.op5,document.myform.ref_regime);
}

function ChangeCode(fld_name,op_name)
{ 
  var field = eval('document.myform.' + fld_name);
  var combo = eval('document.myform.' + op_name);
  var code  = field.value;
  var n     = combo.options.length;
  for ( var i=0; i<n; i++ )
  {
    if ( combo.options[i].value == code )
    {
      combo.selectedIndex = i;
      return;
    }
  }

  field.focus();

  return true;
}

var cmp;

function buscaPessoa(arg)
{
  cmp = arg;
 
  if (cmp == '1')
  {
  var url = '../generico/post/lista_pessoas.php' +
            '?pnome=' + escape(document.myform.ref_professor_nome.value);
  }
  else
  {
  var url = '../generico/post/lista_pessoas.php' +
            '?pnome=' + escape(document.myform.ref_professor_aux_nome.value);
  }

  var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
}

function buscaSala()
{
  cmp = '3';
  var url = '../generico/post/lista_salas.php' +
            '?ref_campus=' + escape(document.myform.ref_campus.value);

  var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
}

function buscaCapacidade()
{
  cmp = '3';
  var url = '../generico/post/lista_salas.php' +
            '?num_sala=' + escape(document.myform.num_sala.value) + 
            '&ref_campus=' + escape(document.myform.ref_campus.value);
  var wnd = window.open(url,'aguarde','toolbar=no,width=10,height=10,scrollbars=no');
}

function setResult(arg1,arg2,arg3)
{
  if (cmp=='1')
  {
    document.myform.ref_professor.value = arg1;
    document.myform.ref_professor_nome.value = arg2;
  }
  
  if (cmp=='2')
  {
    document.myform.ref_professor_aux.value = arg1;
    document.myform.ref_professor_aux_nome.value = arg2;
  }

  else if (cmp=='3')
  {
    document.myform.num_sala.value = arg1;
    document.myform.num_alunos.value = arg2;
  }
}
</script>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="post/atualiza_disciplina_ofer_compl.php" name="myform">

    <input type="hidden" name="dia_semana" value="-1">
    <input type="hidden" name="ref_horario" value="0">
    <input type="hidden" name="ref_regime" value="1">
    <input type="hidden" name="num_creditos_desconto" value="">
    <input type="hidden" name="desconto" value="">
    
  <table width="90%" align="center">
    <tr> 
      <td colspan="2" height="28" bgcolor="#000099" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;Alteração dos Dados Complementares da Disciplina Oferecida</b></font></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo</font></td>
      <td width="70%"><font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><font color="#3333FF">
      <?php echo($ref_disciplina_ofer);?>
      <input type="hidden" name="id" value="<? echo($id); ?>">
      <input type="hidden" name="id_prof" value="<? echo($id_prof); ?>">
      <input type="hidden" name="ref_disciplina_ofer" value="<? echo($ref_disciplina_ofer); ?>">
      </font></font></font> </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Professor</font></td>
      <td width="70%" valign="middle" align="left"> 
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="90%"> 
              <font color="#000000"> 
                <input name="ref_professor" type=text size="6" value="<?echo($ref_professor);?>">
                <input name="ref_professor_nome" type=text size="40" value="<?echo($ref_professor_nome);?>">
              </font>
            </td>
            <td width="10%"><font color="#000000"> 
              <input type="button" value="..." onClick="buscaPessoa(1)" name="button"></font>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;N&ordm; 
        Sala </font></td>
      <td width="70%"> 
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="90%"> 
              <font color="#000000"> 
	            <input type="hidden" name="ref_campus" value="<?echo($ref_campus)?>">
                <input name="num_sala" type="text" value="<?echo($num_sala)?>" size="6" onChange="buscaCapacidade()">
            </td>
            <td width="10%">
              <font color="#000000">
              <input type="button" value="..." onClick="buscaSala()" name="button2">
              </font>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Número máximo de Alunos</font></td>
      <td width="70%"> 
        <input name="num_alunos" type=text size="20" value="<?echo($num_alunos)?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Observação</font></td>
      <td width="70%"> 
        <input name="observacao" type=text size="20" value="<?echo($observacao)?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data do Exame<br>&nbsp;(Campo Opcional)<br>&nbsp;(dd-mm-aaaa)</font></td>
      <td width="70%"> 
        <input name="dt_exame" type=text size="10" maxlength="10" value="<?echo($dt_exame)?>">
      </td>
    </tr>
    <tr>
      <td colspan="2">
         <input name="ref_professor_aux" type="hidden" value="" />
       <input name="ref_professor_aux_nome" type="hidden" value="" />
       <input name="dia_semana_aux" type="hidden" value="" />
        <input name="num_sala_aux" type="hidden"  value="" />
       <hr />
       </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <div align="center"> 
          <input type="submit" name="Submit"  value=" Salvar ">
          <input type="button" name="Button"  value=" Voltar " onclick="javascript:history.go(-1)">
        </div>
      </td>
    </tr>
  </table>
</form>
</body>
</html>
