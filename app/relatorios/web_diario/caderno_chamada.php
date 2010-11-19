<?php 

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/date.php');
require_once($BASE_DIR .'lib/pslib.php');
  
$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['diario_id'];

if(!is_numeric($diario_id))
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!");window.close();</script>');

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
  // ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //
}


ini_set('display_errors', 0);

$data_em = date("d/m/Y");
$dia = -1;
$turno = 0;
$curso_id = 1;
$campus_id = 'undefined';

?>
<html>
<head>
    <title><?=$IEnome?> - Caderno de Chamada</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
</head>
<body  bgcolor="#FFFFFF">
<div align="left" class="titulo1">
  Caderno de Chamada
</div>

  <br /><br />


<?=papeleta_header($diario_id)?>

<br />

<?php

   $nr_pag = 1;
   $data = $data_em ;         //Data sugerida pelo usu?rio
   
   //======================== DECLARA NOME DO ARQUIVO PS DESTINO
    $nome_arq_ps = 'caderno_chamada_' . $diario_id . '.ps';
    $caminho_arquivo = $BASE_DIR .'public/relat/ps/';
    $url_arquivo = $BASE_URL .'public/relat/ps/'. $nome_arq_ps;

   
    $myfile_ps = fopen($caminho_arquivo . $nome_arq_ps,"w");

   //========================= ABRE ARQUIVO PS DESTINO
   SA_PS_open($myfile_ps, "SA", $caminho_arquivo . $nome_arq_ps, 'Landscape');

   //========================= AJUSTA O USO DE ACENTOS
   SA_PS_set_acent($myfile_ps);

   //========================= INICIA A PRIMEIRA PAGINA
   SA_PS_begin_page($myfile_ps, $nr_pag);
   
    $sql =  " SELECT distinct " .
    	    "	   A.ref_disciplina, " .
            "      C.descricao_extenso, " .
            "      A.ref_curso, " .
            "      curso_desc(A.ref_curso), " .
            "      get_departamento(A.ref_disciplina), " .
            "      B.ref_professor_aux, " .
            "      pessoa_nome(B.ref_professor_aux), " .
            "      B.dia_semana, " .
            "      get_dia_semana(B.dia_semana), " .
            "      C.num_creditos, " .
            "      C.carga_horaria, " .
            "      A.creditos_aprov, " .
            "      A.carga_horaria_aprov, " .
            "      B.num_creditos_desconto, " .
            "      A.ref_periodo, " .
            "      B.num_sala, " .
            "      A.ref_disciplina_ofer, " .
            "      A.ref_pessoa, " .
            "      pessoa_nome(A.ref_pessoa) AS aluno_nome, " .
            "      A.ref_disciplina_subst, " .
            "      descricao_disciplina(A.ref_disciplina_subst), " .
            "      get_creditos(A.ref_disciplina_subst), " .
            "      get_carga_horaria(A.ref_disciplina_subst), " .
            "      get_campus(A.ref_campus), " .
            "      A.ref_campus, " .
            "      is_ouvinte(A.ref_pessoa, A.ref_curso), " .
            "      B.turno, " .
            "      get_turno(B.turno), " .
            "      A.turma, " .
            "      B.dia_semana_aux, " .
            "      get_dia_semana(B.dia_semana_aux), " .
            "      B.turno_aux, " .
            "      get_turno(B.turno_aux), " .
            "      B.num_sala_aux, " .
            "      get_complemento_ofer(A.ref_disciplina_ofer), " .
            "      A.dt_cancelamento, " .
            "      get_tipo_curso(A.ref_curso) " .
            " FROM matricula A, disciplinas_ofer_compl B, disciplinas C " .
            " WHERE A.ref_disciplina_ofer = $diario_id and " .
            "       A.obs_aproveitamento = '' and " .  // Aproveitamentos n?o entram no caderno
            "       A.ref_disciplina_ofer = B.ref_disciplina_ofer and " .
            "       A.ref_disciplina = C.id and " .
	    "       ( (A.dt_cancelamento is null) or (A.dt_cancelamento >= get_dt_inicio_aula(A.ref_periodo)) ) " .
            " ORDER BY A.creditos_aprov, " .
    	    "          A.carga_horaria_aprov, " .
    	    "          A.turma, " .
            "          A.ref_disciplina, " .
            "          descricao_disciplina(A.ref_disciplina_subst), " .
    	    "          is_ouvinte(A.ref_pessoa, A.ref_curso), " .
    	    "          pessoa_nome(A.ref_pessoa)";

    $sql = 'SELECT * FROM ('. $sql .') AS T1 ORDER BY lower(to_ascii(T1.aluno_nome));';

function cabecalho($myfile_ps, $data, $ref_disciplina, $disciplina, $ref_curso, $curso, $campus, $texto, $dia_semana, $dia_semana_desc, $departamento, $creditos, $hora_aula, $creditos_desconto, $hora_aula_desconto, $ref_professor, $nome_professor, $periodo, $sala, $fl_ouvinte, $turno, $turno_desc, $ref_disciplina_ofer, $descricao_disciplina_subst, &$quebra_pagina, $complemento_disc)
{
	global $conn;

 SA_PS_line($myfile_ps, 45, -15, 814, -15, 2);
 SA_PS_show_xy_font($myfile_ps, 'Lista de Chamada', 45, -30, 'Arial-Bold', 12);
 
 if($fl_ouvinte)
 {
    SA_PS_show_xy_font($myfile_ps, "ALUNO OUVINTE", 370, -30, 'Arial-Bold', 10);
 }

 SA_PS_show_xy_font($myfile_ps, "Emissão: $data", 580, -30, 'Arial', 8);
 SA_PS_show_xy_font($myfile_ps, "Disciplina:", 45, -42, 'Arial-Bold', 10);

 $nome_disciplina = $ref_disciplina . ' - ' . $disciplina;

 if($descricao_disciplina_subst != '')
 {
    $nome_disciplina = $nome_disciplina  . ' (' . $descricao_disciplina_subst . ')';
 }

 if($complemento_disc != '')
 {
    $nome_disciplina = $nome_disciplina . ' (' . $complemento_disc . ')';
 }

 SA_PS_show_xy_font($myfile_ps, "$nome_disciplina", 98, -42, 'Arial', 10);

// echo '$nome_disciplina:'.$nome_disciplina.'</br>';

 $lin = -54;

 SA_PS_show_xy_font($myfile_ps, "Centro:", 45, "$lin", 'Arial-Bold', 10);
 SA_PS_show_xy_font($myfile_ps, "$departamento", 85, "$lin", 'Arial', 10);

 // echo '$departamento:'.$departamento.'</br>';

 
 SA_PS_show_xy_font($myfile_ps, "Unidade:", 330, "$lin", 'Arial-Bold', 10);
 SA_PS_show_xy_font($myfile_ps, "$campus", 375, "$lin", 'Arial', 10);

 // echo '$campus:'.$campus.'</br>';
 
 SA_PS_show_xy_font($myfile_ps, "Período:", 480, "$lin", 'Arial-Bold', 10);
 SA_PS_show_xy_font($myfile_ps, "$periodo", 522, "$lin", 'Arial', 10);

 // echo '$periodo:'.$periodo.'</br>'; 
 
 SA_PS_show_xy_font($myfile_ps, "Sala:", 580, "$lin", 'Arial-Bold', 10);
 SA_PS_show_xy_font($myfile_ps, "____", 606, "$lin", 'Arial', 10);

 // OK  echo '$sala:'.$sala.'</br>';

 $lin = $lin - 12;
 
 SA_PS_show_xy_font($myfile_ps, "Dia:", 45, "$lin", 'Arial-Bold', 10);
 SA_PS_show_xy_font($myfile_ps, "$dia_semana_desc", 70, "$lin", 'Arial', 10);

 // echo '$dia_semana_desc:'.$dia_semana_desc.'</br>';
 
 SA_PS_show_xy_font($myfile_ps, "Turno:", 330, "$lin", 'Arial-Bold', 10);
 SA_PS_show_xy_font($myfile_ps, "$turno_desc", 365, "$lin", 'Arial', 10);

// echo '$turno_desc:'.$turno_desc.'</br>';
 
 SA_PS_show_xy_font($myfile_ps, "H/A Total:", 480, "$lin", 'Arial-Bold', 10);
 $hora_aula = substr($hora_aula, 0, strpos($hora_aula, ".")+3);
 
 SA_PS_show_xy_font($myfile_ps, "$hora_aula", 530, "$lin", 'Arial', 10);

 // echo '$hora_aula:'.$hora_aula.'</br>';

 SA_PS_show_xy_font($myfile_ps, "H/A Previstas: ", 580, "$lin", 'Arial-Bold', 10);
 $hora_aula_desconto = substr($hora_aula_desconto, 0, strpos($hora_aula_desconto, ".")+3);
 SA_PS_show_xy_font($myfile_ps, "$hora_aula_desconto", 650, "$lin", 'Arial', 10);

 $lin = $lin - 12;

 $frequencia_minima = (($hora_aula * 75) / 100);
 SA_PS_show_xy_font($myfile_ps, "* Frequência Mínima para aprovação: $frequencia_minima H/A", 370, "$lin", 'Arial', 8);

 $lin_aux = $lin - 12;
 
 SA_PS_show_xy_font($myfile_ps, "$ref_disciplina_ofer", 780, "$lin_aux", 'Arial', 10);
 
 SA_PS_show_xy_font($myfile_ps, "Professor:", 45, "$lin", 'Arial-Bold', 10);


 if ($ref_professor == '')
 {
    $sql = " select B.ref_professor, " .
           "        pessoa_nome(B.ref_professor) " .
           " from disciplinas_ofer_compl A, disciplinas_ofer_prof B " .
           " where A.ref_disciplina_ofer = B.ref_disciplina_ofer and " .
    	   "       A.id = B.ref_disciplina_compl and " .
    	   "       A.ref_disciplina_ofer = '$ref_disciplina_ofer' and " .
      	   "       A.dia_semana = '$dia_semana' and " .
      	   "       A.turno = '$turno'";

    $profs = $conn->get_all($sql);
    
    $totalLinhas = count($profs);
    
   /* while( $query2->MoveNext() )     
    {*/
   foreach($profs as $row)
   {
		list($ref_professor,$nome_professor) = $row;

		SA_PS_show_xy_font($myfile_ps, "$ref_professor - $nome_professor", 99, "$lin", 'Arial', 10);
		$lin = $lin - 12;
		$quebra_pagina = $quebra_pagina - 1;
	}
 
    $lin = $lin - 5;
    
 }
 else
 {
    SA_PS_show_xy_font($myfile_ps, "$ref_professor - $nome_professor", 99, "$lin", 'Arial', 10);
    $lin = $lin - 17;
    $quebra_pagina = $quebra_pagina - 1;
 }
 
SA_PS_line($myfile_ps, 45, "$lin", 814, "$lin", 2);

return $lin;

}


function titulo_tab ($myfile_ps, $lin_ini, $lin_fin, $col_ini, $col_fin, $col_ini, $col_txt, $lin_txt, $k)
{

$lin_fin = $lin_ini - 38;
$col_ini = 45;
$col_fin = $col_ini + 15;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);

$col_ini = $col_fin;
$col_fin = $col_ini + 35;
$col_txt = $col_ini + 3;
$lin_txt = $lin_fin + 10;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'Cód', $col_txt, $lin_txt, 'Arial-Bold', 10);

$col_ini = $col_fin;
$col_fin = $col_ini + 225;
$col_txt = $col_ini + 5;
$lin_txt = $lin_fin + 10;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'Nome', $col_txt, $lin_txt, 'Arial-Bold', 10);

$col_ini = $col_fin;
$col_fin = $col_ini + 20;
$col_txt = $col_ini + 2;
$lin_txt = $lin_fin + 10;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'Cur', $col_txt, $lin_txt, 'Arial-Bold', 10);

$col_ini = $col_fin;
$col_fin = $col_ini + 15;
$col_txt = $col_ini + 1;
$lin_txt = $lin_fin + 30;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'Aula', $col_txt, $lin_txt, 'Arial', 6);

$col_txt = $col_ini + 1;
$lin_txt = $lin_fin + 15;

SA_PS_show_xy($myfile_ps, 'Mês', $col_txt, $lin_txt);

$col_txt = $col_ini + 1;
$lin_txt = $lin_fin + 4;

SA_PS_show_xy($myfile_ps, 'Dia', $col_txt, $lin_txt);

$lin_fin = $lin_ini - 10;
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
$col_txt = $col_ini + 4;
$lin_txt = $lin_fin + 2;

$ind = 1;

    for ($k=1;$k < 21;$k++)
    {
    	SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
    	SA_PS_show_xy_font($myfile_ps, $ind, $col_txt, $lin_txt, 'Arial', 7);

    	$col_ini = $col_fin;
       	$col_fin = $col_ini + 15;
    	$col_txt = $col_ini + 4;

    	++ $ind;
    }

$lin_fin = $lin_ini - 38;
$lin_txt = $lin_fin + 10;
$col_ini = $col_fin - 15;
$col_fin = $col_ini + 26;
$col_txt = $col_ini + 7;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'Fr', $col_txt, $lin_txt, 'Arial-Bold', 10);

$col_ini = $col_fin;
$col_fin = $col_ini + 26;
$col_txt = $col_ini + 7;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'N1', $col_txt, $lin_txt, 'Arial-Bold', 10);

$col_ini = $col_fin;
$col_fin = $col_ini + 26;
$col_txt = $col_ini + 7;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'N2', $col_txt, $lin_txt, 'Arial-Bold', 10);

$col_ini = $col_fin;
$col_fin = $col_ini + 26;
$col_txt = $col_ini + 7;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'Md', $col_txt, $lin_txt, 'Arial-Bold', 10);

$col_ini = $col_fin;
$col_fin = $col_ini + 26;
$col_txt = $col_ini + 4;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'Ex', $col_txt, $lin_txt, 'Arial-Bold', 10);

$col_ini = $col_fin;
$col_fin = $col_ini + 26;
$col_txt = $col_ini + 7;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, 'Nf', $col_txt, $lin_txt, 'Arial-Bold', 10);

$col_ini = $col_fin;
$col_fin = $col_ini + 163;
$col_txt = 705;

$lin_ini = $lin_ini - 10;
$lin_fin = $lin_ini - 14;
$col_ini = 355;
$col_fin = $col_ini + 15;

    for ($k=1;$k < 21;$k++)
    {
    SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
    $col_ini = $col_fin;
    $col_fin = $col_ini + 15;
    }

$lin_ini = $lin_ini - 14;
$lin_fin = $lin_ini - 14;
$col_ini = 355;
$col_fin = $col_ini + 15;

    for ($k=1;$k < 21;$k++)
    {
    	SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
    	$col_ini = $col_fin;
    	$col_fin = $col_ini + 15;
    }
    
return $lin_ini;

}

function rodape ($myfile_ps, $lin_fin, $lin, $campus)
{

$lin = $lin_fin - 7;

SA_PS_line($myfile_ps, 45, $lin, 814, $lin, 2);

$lin = $lin - 15;

SA_PS_show_xy_font($myfile_ps, 'Registro de Presença:', 45, $lin, 'Arial-Bold', 8);
SA_PS_show_xy_font($myfile_ps, '-    Ex.: 2 H/A |-/|    4 H/A |=|', 132, $lin, 'Arial', 8);
SA_PS_show_xy_font($myfile_ps, 'Códigos:', 350, $lin, 'Arial', 8);
SA_PS_show_xy_font($myfile_ps, 'Fr', 390, $lin, 'Arial-Bold', 8);
SA_PS_show_xy_font($myfile_ps, '- Frequência', 400, $lin, 'Arial', 8);
SA_PS_show_xy($myfile_ps, 'OBS.:', 512, $lin);

$lin = $lin - 14;

SA_PS_show_xy_font($myfile_ps, 'Registro de Ausência:', 45, $lin, 'Arial-Bold', 8);
SA_PS_show_xy_font($myfile_ps, '/    Ex.: 4 H/A |X|', 132, $lin, 'Arial', 8);
SA_PS_show_xy_font($myfile_ps, 'N', 390, $lin, 'Arial-Bold', 8);
SA_PS_show_xy_font($myfile_ps, ' - Notas das avaliações (1 e 2)', 400, $lin, 'Arial', 8);

$lin = $lin - 14;

SA_PS_show_xy_font($myfile_ps, '* :', 45, $lin, 'Arial-Bold', 8);
SA_PS_show_xy_font($myfile_ps, 'Trancamento conforme processo encaminhado via Protocolo.', 55, $lin, 'Arial', 8);
SA_PS_show_xy_font($myfile_ps, 'Md', 390, $lin, 'Arial-Bold', 8);
SA_PS_show_xy_font($myfile_ps, ' - Média => (N1+N2)/2', 400, $lin, 'Arial', 8);
$lin = $lin - 14;

SA_PS_show_xy_font($myfile_ps, 'Somente a Secretaria Geral está autorizada a incluir alunos na folha de chamada.', 45, $lin, 'Arial', 8);
SA_PS_show_xy_font($myfile_ps, 'Ex', 390, $lin, 'Arial-Bold', 8);
SA_PS_show_xy_font($myfile_ps, ' - Nota do Exame', 400, $lin, 'Arial', 8);

$lin = $lin - 14;

SA_PS_show_xy($myfile_ps, 'Horas/Aula Dadas: ___________', 45, $lin);
SA_PS_show_xy($myfile_ps, 'Professor(a): _____________________________', 175, $lin);
SA_PS_show_xy_font($myfile_ps, 'Nf ', 390, $lin, 'Arial-Bold', 8);
SA_PS_show_xy_font($myfile_ps, ' - Nota Final => (Md+Ex)/2', 400, $lin, 'Arial', 8);

$lin = $lin - 7;

SA_PS_line($myfile_ps, 45, $lin, 814, $lin, 2);
}

// END Functions

// Begin Program Principal

$quebra_pagina = 25;

$alunos_diario = $conn->get_all($sql);


$num = 1 ;
$count = 1;
$totalLinhas = count($alunos_diario);

if($totalLinhas < 1)
{
      echo '<script language=javascript>window.alert("ERRO! Não existem alunos matriculados nesta disciplina!"); javascript:window.history.back(1);</script>';
      exit;
}

$row = -1;
foreach($alunos_diario as $linha)
{
		$row++;

        list($ref_disciplina,
        $disciplina,
        $ref_curso,
        $curso,
        $departamento,
        $ref_professor_aux,
        $nome_professor_aux,
        $dia_semana,
        $dia_semana_desc,
        $creditos,
        $hora_aula,
        $creditos_aprov,
        $hora_aula_aprov,
        $creditos_desconto,
        $periodo,
        $sala,
        $ref_disciplina_ofer,
        $ref_pessoa,
        $nome,
        $ref_disciplina_subst,
        $descricao_disciplina_subst,
        $creditos_subst,
        $hora_aula_subst,
        $campus,
        $ref_campus,
        $fl_ouvinte,
        $turno,
        $turno_desc,
    	$turma,
    	$dia_semana_aux,
    	$dia_semana_aux_desc,
    	$turno_aux,
    	$turno_desc_aux,
    	$num_sala_aux,
        $complemento_disc,
        $dt_cancelamento,
        $ref_tipo_curso) = $linha; 

$aux_ref_professor_aux = $ref_professor_aux;
$hora_aula_desconto = 0;

// O n?mero de cr?ditos e a carga hor?ria utilizados 
// ser?o o que o aluno realmente estar? em sala de aula
if (($ref_disciplina_subst != 0) && ($ref_disciplina_subst != '') && ($creditos != $creditos_subst))
{
   $creditos  = $creditos_subst;
   $hora_aula = $hora_aula_subst;
}

if (($creditos_desconto != 0) && ($creditos_desconto != ''))
{
   $creditos_desconto  = sprintf("%.2f", $creditos_desconto);
   
   // Os cursos t?cnicos quebram a regra pois tem Carga Hor?ria igual a N?mero de Cr?ditos
   if ($ref_tipo_curso != '7')
   {
       $hora_aula_desconto = ($creditos_desconto * 15);
   }
   $hora_aula_desconto = ($hora_aula_desconto + ($hora_aula_desconto / 15));
}

if (($creditos_aprov != 0) && ($creditos_aprov != ''))
{
   $creditos          = $creditos_aprov;
   $creditos          = sprintf("%.2f", $creditos);
   $creditos_desconto = $creditos_aprov;
   $creditos_desconto = sprintf("%.2f", $creditos_desconto);
}

if (($hora_aula_aprov != 0) && ($hora_aula_aprov != ''))
{
   $hora_aula          = $hora_aula_aprov;
   $hora_aula_desconto = ($hora_aula_aprov + ($hora_aula_aprov / 15));
}

$hora_aula = ($hora_aula + ($hora_aula / 15));

if (($hora_aula_desconto == 0) || ($hora_aula_desconto == ''))
{
    $hora_aula_desconto = $hora_aula;
}

if ($fl_ouvinte == '')
{
   $fl_ouvinte = '0';
}
if ($row==0)
{
   $aux_ouvinte = $fl_ouvinte;
   $aux_ofer = $ref_disciplina_ofer;
   $aux_creditos = $creditos;
   $aux_hora_aula = $hora_aula;
   $aux_turma = $turma;
   $aux_ref_professor_aux = $ref_professor_aux;
   $aux_descricao_disciplina_subst = $descricao_disciplina_subst;
   $aux_ref_disciplina = $ref_disciplina;
   
   //===== Rotate (Para usar a p?gina em LANDSCAPE)
   SA_PS_rotate($myfile_ps, 90);

   //===== Inserir Cabe?alho
   $quebra_pagina = 25;
   
   $lin_ini = cabecalho($myfile_ps, $data, $ref_disciplina, $disciplina, $ref_curso, $curso, $campus, $texto, $dia_semana, $dia_semana_desc, $departamento, $creditos, $hora_aula, $creditos_desconto, $hora_aula_desconto, $ref_professor, $nome_professor, $periodo, $sala, $fl_ouvinte, $turno, $turno_desc, $ref_disciplina_ofer, $descricao_disciplina_subst, $quebra_pagina, $complemento_disc);

   $lin_ini = $lin_ini - 14;

   //===== Inserir Titulo da Tabela
   $lin_ini = titulo_tab ($myfile_ps, $lin_ini, $lin_fin, $col_ini, $col_fin, $col_ini, $col_txt, $lin_txt, $k);
   
   $lin_ini = $lin_ini - 14;
}

else

if( ($fl_ouvinte != $aux_ouvinte) || ($ref_disciplina_ofer != $aux_ofer) || ($aux_creditos != $creditos) || ($aux_hora_aula != $hora_aula) || ($descricao_disciplina_subst != $aux_descricao_disciplina_subst) || ($aux_ref_disciplina != $ref_disciplina) || (($aux_turma != $turma) && ($aux_ref_professor_aux != '') && ($aux_ref_professor_aux != '0'))   )
{
    
   rodape ($myfile_ps, $lin_fin, $lin, $campus);
   SA_PS_rotate($myfile_ps, 360);
   SA_PS_end_page($myfile_ps);
   $nr_pag ++;
   SA_PS_begin_page($myfile_ps, $nr_pag);
   SA_PS_rotate($myfile_ps, 90);

   if (($aux_turma != $turma)&&($aux_ref_professor_aux != '')&&($aux_ref_professor_aux != '0'))
   {
   	$ref_professor   = $ref_professor_aux;
   	$nome_professor  = $nome_professor_aux;
   	$dia_semana      = $dia_semana_aux;
   	$dia_semana_desc = $dia_semana_aux_desc;
   	$turno_desc      = $turno_desc_aux;
   	$sala            = $num_sala_aux;
   }
   else
   {
   	$ref_professor  = '';
   	$nome_professor = '';
   }

   //===== Inserir Cabe?alho
   $quebra_pagina = 25;

   $lin_ini = cabecalho($myfile_ps, $data, $ref_disciplina, $disciplina, $ref_curso, $curso, $campus, $texto, $dia_semana, $dia_semana_desc, $departamento, $creditos, $hora_aula, $creditos_desconto, $hora_aula_desconto, $ref_professor, $nome_professor, $periodo, $sala, $fl_ouvinte, $turno, $turno_desc, $ref_disciplina_ofer, $descricao_disciplina_subst, $quebra_pagina, $complemento_disc);
   
   $lin_ini = $lin_ini - 14;

   //===== Inserir T?tulo da Tabela
   $lin_ini = titulo_tab ($myfile_ps, $lin_ini, $lin_fin, $col_ini, $col_fin, $col_ini, $col_txt, $lin_txt, $k);

   $lin_ini = $lin_ini - 14;
   $count = 1 ;
   $num = 1;
   $aux_ouvinte = $fl_ouvinte;
   $aux_ofer = $ref_disciplina_ofer;
   $aux_creditos = $creditos;
   $aux_hora_aula = $hora_aula;
   $aux_turma = $turma;
   $aux_descricao_disciplina_subst = $descricao_disciplina_subst;
   $aux_ref_disciplina = $ref_disciplina;
}

$lin_fin = $lin_ini - 14;
$col_ini = 45;
$col_fin = $col_ini + 15;
$lin_txt = $lin_fin + 3;


SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);

if ($dt_cancelamento)
{
    SA_PS_show_xy_font($myfile_ps, "*", 50, $lin_txt, 'Arial', 8);
    $num = $num - 1;
}
else
{
    SA_PS_show_xy_font($myfile_ps, "$num", 48, $lin_txt, 'Arial', 8);
}
$col_ini = $col_fin;
$col_fin = $col_ini + 35;
$col_txt = $col_ini + 3;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy($myfile_ps, $ref_pessoa, $col_txt, $lin_txt);

$col_ini = $col_fin;
$col_fin = $col_ini + 225;
$col_txt = $col_ini + 1;
$lin_txt = $lin_fin + 3;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, $nome, $col_txt, $lin_txt, 'Arial', 8);

$col_ini = $col_fin;
$col_fin = $col_ini + 35;
$col_txt = $col_ini + 7;
$lin_txt = $lin_fin + 3;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
SA_PS_show_xy_font($myfile_ps, $ref_curso, $col_txt, $lin_txt, 'Arial', 8);

$col_ini = $col_fin;
$col_fin = $col_ini + 15;

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;

if ($dt_cancelamento)
{
    $dt_cancelamento = date::convert_date($dt_cancelamento);
    SA_PS_show_xy_font($myfile_ps, "Trancou em $dt_cancelamento", $col_ini+2, $lin_txt+2, 'Arial', 5);
}

SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 15;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 26;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 26;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 26;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 26;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 26;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$col_fin = $col_ini + 26;
SA_PS_rect($myfile_ps, $col_ini, $lin_ini, $col_fin, $lin_fin, 0.3);
$col_ini = $col_fin;
$lin_ini = $lin_fin ;
$num ++;

   if ($count==$quebra_pagina)
   {
     if ($totalLinhas > $row+1)
     {
        rodape ($myfile_ps, $lin_fin, $lin, $campus);
        SA_PS_rotate($myfile_ps, 360);
        SA_PS_end_page($myfile_ps);
        $nr_pag ++;
        SA_PS_begin_page($myfile_ps, $nr_pag);
        SA_PS_rotate($myfile_ps, 90);
        $aux_turma = 'A';

  	if (($aux_turma != $turma) && ($aux_ref_professor_aux != '') && ($aux_ref_professor_aux != '0'))
  	{
     		$ref_professor   = $ref_professor_aux;
         	$nome_professor  = $nome_professor_aux;
     		$dia_semana      = $dia_semana_aux;
     		$dia_semana_desc = $dia_semana_aux_desc;
     		$turno_desc      = $turno_desc_aux;
     		$sala            = $num_sala_aux;
  	}
  	else
  	{	
     		$ref_professor  = '';
     		$nome_professor = '';
  	}

        $aux_turma = $turma;
	
        $quebra_pagina = 25;
        
    	$lin_ini = cabecalho($myfile_ps, $data, $ref_disciplina, $disciplina, $ref_curso, $curso, $campus, $texto, $dia_semana, $dia_semana_desc, $departamento, $creditos, $hora_aula, $creditos_desconto, $hora_aula_desconto, $ref_professor, $nome_professor, $periodo, $sala, $fl_ouvinte, $turno, $turno_desc, $ref_disciplina_ofer, $descricao_disciplina_subst, $quebra_pagina, $complemento_disc);

        $lin_ini = $lin_ini - 14;

        $lin_ini = titulo_tab ($myfile_ps, $lin_ini, $lin_fin, $col_ini, $col_fin, $col_ini, $col_txt, $lin_txt, $k);

        $lin_ini = $lin_ini - 14;
	
        $count = 1 ;
     }
   }

$count ++ ;

}

rodape ($myfile_ps, $lin_fin, $lin, $campus);

//========================= LOGOTIPO
SA_PS_rotate($myfile_ps, 360);

//========================= FECHA A P?GINA
SA_PS_end_page($myfile_ps);

//========================= FECHA O ARQUIVO PS DESTINO
SA_PS_close($myfile_ps);


// EFETUA O DOWNLOAD
// REDIRECIONA
echo '<meta http-equiv="refresh" content="0;url='. $url_arquivo .'">';
?>
      
 <form name="myform" action="" >
   <p align="left">
     <input type="button" name="botao2" value="Imprimir novamente" onClick="location='<?=$url_arquivo?>'">
	 &nbsp;&nbsp;
	<a href="#" onclick="javascript:window.close();">Fechar</a>
   </p>
  </form>
</body>
</html>
