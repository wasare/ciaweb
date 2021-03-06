<?php

require("../common.php");
require("../lib/GetCidade.php");
require("../lib/GetFiliacao.php");
require("../lib/SQLCombo.php");
require("../lib/GetField.php");
require("../lib/InvData.php");
require("../lib/GetCursoExterno.php");

$conn = new connection_factory($param_conn);

// Verifica as permissoes de acesso do usuario quanto ao arquivo
$ACL_FILE = __FILE__;
require_once($BASE_DIR .'core/login/acesso.php');
// ^ Verifica as permissoes de acesso do usuario quanto ao arquivo ^ //



$id = $_GET['id'];


$op_opcoes = SQLArray("select id || ' - ' || nome, id from pais order by id");

?>
<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
function _init(){
	document.myform.nome.focus();
}
</script>
<?php

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = "select " .
       "    A.id," .
       "    A.nome," .
       "    A.rua," .
       "    A.complemento," .
       "    A.bairro," .
       "    A.cep," .
       "    A.ref_cidade," .
       "    A.fone_particular," .
       "    A.fone_profissional," .
       "    A.fone_celular," .
       "    A.fone_recado," .
       "    A.email," .
       "    A.email_alt," .
       "    A.estado_civil," .
       "    A.dt_cadastro," .
       "    A.dt_nascimento," .
       "    A.sexo," .
       "    A.deficiencia," .
       "    A.deficiencia_desc," .
       "    A.rg_numero," .
       "    A.rg_orgao," .
       "    A.rg_cidade," .
       "    A.rg_data," .
       "    A.ref_filiacao," .
       "    A.ref_naturalidade," .
       "    A.ref_nacionalidade," .
       "    A.cod_cpf_cgc," .
       "    A.titulo_eleitor," .
       "    A.placa_carro," .
       "    A.fl_cartao," .
       "    A.fl_dados_pessoais," .
       "    A.ano_2g," .
       "    A.escola_2g," .
       "    A.cidade_2g," .
       "    A.ref_curso_2g," .
       "    A.cod_passivo,  " .
       "    A.cod_externo,  " .
       "    A.obs," .

       "    B.rg_num," .
       "    B.cpf," .
       "    B.titulo_eleitor," .
       "    B.quitacao_eleitoral," .
       "    B.hist_original," .
       "    B.hist_escolar," .
       "    B.doc_militar," .
       "    B.foto," .
       "    B.atestado_medico, " .
       "    B.diploma_autenticado," .
       "    B.solteiro_emancipado, " .
       "    C.ref_estado AS cidade_uf, " .
       "    D.ref_estado AS naturalidade_uf " .
       " from pessoas A LEFT OUTER JOIN documentos B ON (A.id = B.ref_pessoa) LEFT OUTER JOIN cidade C ON (A.ref_cidade = C.id) LEFT OUTER JOIN cidade D ON (A.ref_naturalidade = D.id)" .
       " where " .
       "       A.id = '$id' ";

//echo $sql; die;

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro n&atilde;o encontrado!");

list ( $id,
$nome,
$rua,
$complemento,
$bairro,
$cep,
$ref_cidade,
$fone_particular,
$fone_profissional,
$fone_celular,
$fone_recado,
$email,
$email_alt,
$estado_civil,
$dt_cadastro,
$dt_nascimento,
$sexo,
$deficiencia,
$deficiencia_desc,
$rg_numero,
$rg_orgao,
$rg_cidade,
$rg_data,
$ref_filiacao,
$ref_naturalidade,
$ref_nacionalidade,
$cod_cpf_cgc,
$titulo_eleitor,
$placa_carro,
$fl_cartao,
$fl_dados_pessoais,
$ano_2g,
$ref_escola_2g,
$cidade_2g,
$ref_curso_2g,
$cod_passivo,
$cod_externo,
$obs,

$rg_num,
$cpf,
$titulo_eleitord,
$quitacao_eleitoral,
$hist_original,
$hist_escolar,
$doc_militar,
$foto,
$atestado_medico,
$diploma_autenticado,
$solteiro_emancipado,
$cidade_uf,
$naturalidade_uf) = $query->GetRowValues();

$query->Close();

$conn->Close();

$dt_cadastro 	= InvData($dt_cadastro);
$rg_data	= InvData($rg_data);
$dt_nascimento 	= InvData($dt_nascimento);

$escola_2g = GetField($ref_escola_2g, "nome", "instituicoes", false);
$cnome = GetCidade($ref_cidade, false);
$naturalidade = GetCidade($ref_naturalidade, false);
$cidade_2g_ = GetCidade($cidade_2g, false);
$curso = GetCursoExterno($ref_curso_2g, false);
$cidade_rg = GetCidade($rg_cidade, false);

list ($nome_pai_, $nome_mae_) = GetFiliacao($ref_filiacao, false);

?>

<script language="JavaScript">
            var tipo_busca;

            function incluiFiliacao()
            {
                tipo_busca = 6;
                var wnd = window.open("../generico/filiacao_inclui.php",'buscaPessoa','toolbar=no,width=550,height=350,scrollbars=yes');
            }

            function buscaCurso()
            {
                tipo_busca = 7;
                var url = "../generico/post/lista_cursos_externos.php" +
                    "?id=" + escape(document.myform.ref_curso_2g.value) +
                    "&curso=" + escape(document.myform.curso.value);

                var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
            }

            function buscaEscola()
            {
                tipo_busca = 8;
                var url = "../generico/post/lista_escolas.php" +
                    "?id=" + escape(document.myform.ref_escola_2g.value) +
                    "&cnome=" + escape(document.myform.escola_2g.value);

                var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
            }

            function buscaCidade(pf_opcao)
            {
                var url;
                tipo_busca=pf_opcao;

                if (tipo_busca == 1)
                    url = '../generico/post/lista_cidades.php' +
                    '?cnome=' + escape(document.myform.cnome.value);

                else if (tipo_busca == 2)
                    url = '../generico/post/lista_cidades.php' +
                    '?cnome=' + escape(document.myform.cnome_rg.value);

                else if (tipo_busca == 3)
                    url = '../generico/post/lista_cidades.php' +
                    '?cnome=' + escape(document.myform.cnome_2g.value);

                else if (tipo_busca == 4)
                    url = '../generico/post/lista_cidades.php' +
                    '?cnome=' + escape(document.myform.naturalidade.value);

                window.open(url,"popWindow","toolbar=no,width=600,height=368,top=5,left=5,directories=no,menubar=no,scrollbars=yes");
            }

            function setResult(id,nome,cep,ref_pais,ref_estado,pais_desc)
            {
                if (tipo_busca == 1)
                {
                    document.myform.ref_cidade.value = id;
                    document.myform.cnome.value = nome;
                    document.myform.cep.value = cep;
                }

                else if (tipo_busca == 2)
                {
                    document.myform.rg_cidade.value = id;
                    document.myform.cnome_rg.value = nome;
                }

                else if (tipo_busca == 3)
                {
                    document.myform.cidade_2g.value = id;
                    document.myform.cnome_2g.value = nome;
                }

                else if (tipo_busca == 4)
                {
                    document.myform.ref_naturalidade.value = id;
                    document.myform.naturalidade.value = nome;
                    document.myform.ref_nacionalidade.value = ref_pais;
                }

                else if (tipo_busca == 6)
                {
                    document.myform.ref_filiacao.value = id;
                    document.myform.nome_filiacao.value = nome;
                }

                else if (tipo_busca == 7)
                {
                    document.myform.ref_curso_2g.value = id;
                    document.myform.curso.value = nome;
                }
                else if (tipo_busca == 8)
                {
                    document.myform.ref_escola_2g.value = id;
                    document.myform.escola_2g.value = nome;
                }
            }
        </script>
<script language="JavaScript">
            function ChangeOption(opt,fld)
            {
                var i = opt.selectedIndex;

                if ( i != -1 )
                    fld.value = opt.options[i].value;
                else
                    fld.value = '';
            }

            function ChangeOp()
            {
                ChangeOption(document.myform.op,document.myform.ref_nacionalidade);
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

                alert(code + ' n&atilde;o &eacute; um c&oacute;digo v&aacute;lido!');

                field.focus();

                return true;
            }
        </script>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="post/pessoaf_edita.php" name="myform">
<table width="90%" align="center">

	<tr bgcolor="#000099">
		<td height="35" colspan="3">
		<div align="center"><font size="3"
			face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Altera&ccedil;&atilde;o
		de Pessoa F&iacute;sica</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td wiidth="10%" bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo&nbsp;</font></td>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" size="2"
			color="#0000FF"> <?
			echo("$id<br>");
			if ($fl_dados_pessoais == 'f')
			{
				echo("<font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"red\"><b>(Divulga&ccedil;&atilde;o de dados pessoais n&atilde;o permitida!!!)</b></font>");
			}
			?> <input type="hidden" name="id" value="<?=$id?>"> </font></td>
		<th rowspan="6"><font size="-1" color="brown">clique na foto para editar</font><br />
		<a href="form_foto.php?id=<?=$id?>&pessoa=<?=$nome?>"><img
			title="<?=$nome?>" src="<?=$BASE_URL?>core/pessoa_foto.php?id=<?=$id?>"
			alt="<?=$nome?>" border="1" width="120" /></a></th>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="nome" type=text value="<?=$nome?>" size="30"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Rua&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="210"><input name="rua" type=text value="<?echo($rua);?>"
					size="30"> <font face="Verdana, Arial, Helvetica, sans-serif"
					size="2">, </font></td>
				<td width="80"><input name="complemento" type=text
					value="<?echo($complemento);?>" size="7"></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF" height="22"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Bairro&nbsp;</font></td>
		<td><input name="bairro" type=text value="<?echo($bairro);?>"
			size="30"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF" height="22"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cidade&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="0">
				<div align="left"><input type="text" name="ref_cidade"
					value="<?echo($ref_cidade);?>" size="6"></div>
				</td>
				<td width="100%">
				<div align="left"><font color="#000000"> <input type="text"
					name="cnome" size="25" value="<?echo($cnome);?>"> 
                   <font face="Verdana, Arial, Helvetica, sans-serif" size="2"> - <strong><?php echo($cidade_uf); ?></strong></font>&nbsp;&nbsp;&nbsp;&nbsp;<input
					type="button" value="..." onClick="buscaCidade(1)" name="button2">
				</font></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cep&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="cep" type=text value="<?echo($cep);?>" size="10"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fone
		Particular&nbsp;</font></td>
		<td><input name="fone_particular" type=text
			value="<?echo($fone_particular);?>" size="15"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fone
		Profissional&nbsp;</font></td>
		<td><input name="fone_profissional" type=text
			value="<?echo($fone_profissional);?>" size="15"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fone
		Celular&nbsp;</font></td>
		<td><input name="fone_celular" type=text
			value="<?echo($fone_celular);?>" size="15"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fone
		Recado&nbsp;</font></td>
		<td><input name="fone_recado" type=text
			value="<?echo($fone_recado);?>" size="15"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF3333">&nbsp;<font
			color="#000099">E-mail Principal </font></font></td>
		<td><input name="email" type=text value="<?echo($email);?>" size="30">
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000">&nbsp;<font
			color="#000099">E-mail Alternativo </font></font></td>
		<td><input name="email_alt" type=text value="<?echo($email_alt);?>"
			size="30"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Estado
		Civil&nbsp;</font></td>
		<td><select name="estado_civil" size="1">
			<option value="<?=$estado_civil?>" selected><?echo($estados_civis[$estado_civil]);?></option>
			<option value="S">Solteiro</option>
			<option value="C">Casado</option>
			<option value="V">Vi&uacute;vo</option>
			<option value="D">Desquitado</option>
			<option value="U">Uni&atilde;o Est&aacute;vel</option>
			<option value="E">Solteiro Emancipado</option>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		de Cadastro<br>
		&nbsp;(dd-mm-aaaa)</font></td>
		<td><input name="dt_cadastro" type=text value="<?echo($dt_cadastro)?>"
			size="15"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		de Nascimento&nbsp;<span class="required">*</span>&nbsp;</font><br>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;(dd-mm-aaaa)</font></td>
		<td><input name="dt_nascimento" type=text
			value="<?echo($dt_nascimento);?>" size="15"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Sexo&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><select name="sexo">
			<option value="" selected="selected"> --- selecione ---</option>
			<option value="M" <?php if($sexo == 'M') echo 'selected="selected"';?>>Masculino</option>
			<option value="F" <?php if($sexo == 'F') echo 'selected="selected"';?>>Feminino</option>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Deficiente&nbsp;</font></td>
		<td><select name="deficiencia">
			<option value="0"
			<? if ( $deficiencia == '0' ) { echo "selected"; } ?>>N&atilde;o</option>
			<option value="1"
			<? if ( $deficiencia == '1' ) { echo "selected"; } ?>>Sim</option>
		</select> <input name="deficiencia_desc" type=text
			value="<?echo($deficiencia_desc);?>" size="40"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Numero&nbsp;RG</font></td>
		<td><input name="rg_numero" type=text value="<?echo($rg_numero);?>"
			size="15"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;&Oacute;rgao&nbsp;Expedidor
		RG </font></td>
		<td><input name="rg_orgao" type=text value="<?echo($rg_orgao);?>"
			size="6"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cidade&nbsp;RG</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="0">
				<div align="left"><input type="text" name="rg_cidade" size="5"
					maxlength="10" value="<?echo($rg_cidade);?>"></div>
				</td>
				<td width="100%">
				<div align="left"><font color="#000000"> <input type="text"
					name="cnome_rg" size="35" value="<?echo($cidade_rg);?>"> </font></div>
				</td>
				<td width="0">
				<div align="right"><font color="#000000"><font color="#000000"> <font
					color="#000000"> <input type="button" value="..."
					onClick="buscaCidade(2)" name="button"> </font></font></font></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data&nbsp;RG<br>
		&nbsp;(dd-mm-aaaa)</font></td>
		<td><input name="rg_data" type=text value="<?echo($rg_data);?>"
			size="15"></td>
	</tr>
	<?php
	if (!$ref_filiacao == '0')
	{
		?>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Filia&ccedil;&atilde;o&nbsp;</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%">
				<h4><font size="2"><font
					face="Verdana, Arial, Helvetica, sans-serif"> </font> <font
					face="Verdana, Arial, Helvetica, sans-serif"><?php echo($nome_pai_); ?>
				- <font color="#000000"> <?php echo($nome_mae_); ?></font></font></font>
				<font color="#000000" size="2"
					face="Verdana, Arial, Helvetica,
                                              sans-serif"> <input
					type="hidden" name="ref_filiacao" value="<?echo($ref_filiacao);?>">
				</font><font color="#000000"><font color="#000000"><font size="2"> </font></font></font>
				</h4>
				</td>
				<td width="0">
				<div align="right"><font color="#000000"><font color="#000000"><font
					color="#000000"> </font><font size="2"> </font><font
					color="#000000"> <input type="button" name="Submit3" value="..."
					onclick="window.open('../generico/filiacao_edita.php?id=<? echo($ref_filiacao) ?>','mywindow','toolbar=no,width=550,height=350,scrollbars=yes')">
				</font></font></font></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<?php
	}
	else
	{
		?>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Filia&ccedil;&atilde;o
		</font></td>
		<td>
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td><input name="ref_filiacao" type=text size="5" maxlength="8"></td>
				<td width="100%"><input type="text" name="nome_filiacao" size="35">
				</td>
				<td><font color="#000000"> <input type="button" value="..."
					onClick="incluiFiliacao()" name="button223"> </font></td>
			</tr>
		</table>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Naturalidade&nbsp;</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="0">
				<div align="left"><input type="text" name="ref_naturalidade"
					size="5" maxlength="10" value="<?echo($ref_naturalidade);?>"></div>
				</td>
				<td width="100%">
				<div align="left"><font color="#000000"> <input type="text"
					name="naturalidade" size="25" value="<?echo($naturalidade);?>"> </font> 
					<font face="Verdana, Arial, Helvetica, sans-serif" size="2"> - <strong><?php echo($naturalidade_uf); ?></strong></font></div>
				</td>
				<td width="0">
				<div align="right"><font color="#000000"><font color="#000000"> <font
					color="#000000"> <input type="button" value="..."
					onClick="buscaCidade(4)" name="button3"> </font></font></font></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nacionalidade&nbsp;</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="0">
				<div align="left"><input type="text" name="ref_nacionalidade"
					size="5" maxlength="10" value="<?echo($ref_nacionalidade);?>"></div>
				</td>
				<td width="100%">
				<div align="left"><font color="#000000"> </font><font
					face="Verdana, Arial, Helvetica, sans-serif" size="2"><font
					color="#000000"> <script language="PHP">
ComboArray("op",$op_opcoes, $ref_nacionalidade ,"ChangeOp()");
                                                </script> </font></font></div>
				</td>
				<td width="0">
				<div align="right"><font color="#000000"><font color="#000000"> <font
					color="#000000"> </font></font></font></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;N&ordm;
		do CPF&nbsp;</font></td>
		<td><input name="cod_cpf_cgc" type=text
			value="<?echo($cod_cpf_cgc);?>" size="15" maxlength="11"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Titulo
		Eleitor&nbsp;</font></td>
		<td><input name="titulo_eleitor" type=text
			value="<?echo($titulo_eleitor);?>" size="15"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Placa
		Carro&nbsp;</font></td>
		<td><input name="placa_carro" type=text
			value="<?echo($placa_carro);?>" size="15"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Carteirinha&nbsp;</font></td>
		<td><select name="fl_cartao">
			<option selected><?echo($opcoes[$fl_cartao]); ?></option>
			<?if ($fl_cartao == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($fl_cartao == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";}
			?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Permite
		Divulga&ccedil;&atilde;o<br>
		&nbsp;dos Dados Pessoais&nbsp;</font></td>
		<td><select name="fl_dados_pessoais">
			<option selected><?echo($opcoes[$fl_dados_pessoais]); ?></option>
			<?if ($fl_dados_pessoais == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($fl_dados_pessoais == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";}
			?>
		</select></td>
	</tr>
	<tr bgcolor="#ffeecc">
		<td colspan="2"><font size="2"
			face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FF3333">Documenta&ccedil;&atilde;o</font></b></font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;pia
		do RG&nbsp;</font></td>
		<td><select name="rg_num">
			<option selected><?echo($opcoes[$rg_num]); ?></option>
			<?if ($rg_num == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($rg_num == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;pia
		do CPF&nbsp;</font></td>
		<td><select name="cpf">
			<option selected><?echo($opcoes[$cpf]); ?></option>
			<?if ($cpf == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($cpf == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;pia
		do T&iacute;tulo de Eleitor&nbsp;</font></td>
		<td><select name="titulo_eleitord">
			<option selected><?echo($opcoes[$titulo_eleitord]); ?></option>
			<?if ($titulo_eleitord == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($titulo_eleitord == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Quita&ccedil;&atilde;o
		Eleitoral&nbsp;</font></td>
		<td><select name="quitacao_eleitoral">
			<option selected><?echo($opcoes[$quitacao_eleitoral]); ?></option>
			<?if ($quitacao_eleitoral == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($quitacao_eleitoral == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Hist&oacute;rico
		Original&nbsp;</font></td>
		<td><select name="hist_original">
			<option selected><?echo($opcoes[$hist_original]); ?></option>
			<?if ($hist_original == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($hist_original == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;pia
		do Hist&oacute;rico&nbsp;</font></td>
		<td><select name="hist_escolar">
			<option selected><?echo($opcoes[$hist_escolar]); ?></option>
			<?if ($hist_escolar == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($hist_escolar == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Documenta&ccedil;&atilde;o
		Militar&nbsp;</font></td>
		<td><select name="doc_militar">
			<option selected><?echo($opcoes[$doc_militar]); ?></option>
			<?if ($doc_militar == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($doc_militar == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Foto&nbsp;</font></td>
		<td><select name="foto">
			<option selected><?echo($opcoes[$foto]); ?></option>
			<?if ($foto == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($foto == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Atestado
		M&eacute;dico&nbsp;</font></td>
		<td><select name="atestado_medico">
			<option selected><?echo($opcoes[$atestado_medico]); ?></option>
			<?if ($atestado_medico == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($atestado_medico == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Diploma
		Autenticado&nbsp;</font></td>
		<td><select name="diploma_autenticado">
			<option selected><?echo($opcoes[$diploma_autenticado]); ?></option>
			<?if ($diploma_autenticado == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($diploma_autenticado == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Solteiro
		Emancipado&nbsp;</font></td>
		<td><select name="solteiro_emancipado">
			<option selected><?echo($opcoes[$solteiro_emancipado]); ?></option>
			<?if ($solteiro_emancipado == 'f')
			{ echo "<option value=\"t\">Sim</option>";}
			if ($solteiro_emancipado == 't')
			{ echo "<option value=\"f\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr bgcolor="#ffeecc">
		<td colspan="2"><font size="2"
			face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FF3333">Informa&ccedil;&atilde;es
		do Ensino M&eacute;dio</font></b></font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Ano
		conclus&atilde;o&nbsp;</font></td>
		<td><input name="ano_2g" type=text value="<?echo($ano_2g);?>" size="4"
			maxlength="4"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Escola
		</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="10%"><input name="ref_escola_2g" type=text
					value="<?echo($ref_escola_2g);?>" size="6" maxlength="10"></td>
				<td width="100%"><input type="text" name="escola_2g" size="35"
					value="<?echo($escola_2g);?>"></td>
				<td width="10%"><input type="button" value="..."
					onClick="buscaEscola()" name="button23"></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cidade
		&nbsp;</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="10%"><input type="text" name="cidade_2g" size="6"
					maxlength="10" value="<?echo($cidade_2g);?>"></td>
				<td width="100%"><input type="text" name="cnome_2g" size="35"
					value="<?echo($cidade_2g_);?>"></td>
				<td width="10%"><input type="button" value="..."
					onClick="buscaCidade(3)" name="button"></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Curso
		</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="10%"><input type="text" name="ref_curso_2g" size="6"
					maxlength="10" value="<?echo($ref_curso_2g);?>"></td>
				<td width="100%"><input type="text" name="curso" size="35"
					value="<?echo($curso);?>"></td>
				<td width="10%"><input type="button" value="..."
					onClick="buscaCurso()" name="button"></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo
		Passivo&nbsp;</font></td>
		<td><input name="cod_passivo" type=text
			value="<?echo($cod_passivo);?>" size="20" maxlength="20"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo
		Externo&nbsp;</font></td>
		<td><input name="cod_externo" type=text
			value="<?echo($cod_externo);?>" size="10" maxlength="10"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Observa&ccedil;&otilde;es&nbsp;</font></td>
		<td><textarea name="obs" cols="40" rows="2"><?echo($obs);?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<div align="center"><input type="submit" name="Submit"
			value=" Salvar "> <input type="button" name="Submit2"
			value=" Voltar " onclick="javascript:history.go(-1)"></div>
		</td>
	</tr>
</table>
</form>
</body>
</html>

