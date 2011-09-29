<?php

require("../../common.php");
require_once '../../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

// Verifica as permissoes de acesso do usuario quanto ao arquivo
$ACL_FILE = __FILE__;
require_once($BASE_DIR .'core/login/acesso.php');
// ^ Verifica as permissoes de acesso do usuario quanto ao arquivo ^ //
require("../../lib/InvData.php");


$ref_curso           = $_POST['ref_curso'];
$curso               = $_POST['curso'];
$ref_campus          = $_POST['ref_campus'];
$ref_disciplina      = $_POST['ref_disciplina'];
$ref_disciplina_nome = $_POST['ref_disciplina_nome'];
$semestre_curso      = $_POST['semestre_curso'];
$curriculo_mco       = $_POST['curriculo_mco'];
$dt_inicio_curriculo = $_POST['dt_inicio_curriculo'];
$dt_final_curriculo  = $_POST['dt_final_curriculo'];
$pre_requisito_hora  = $_POST['pre_requisito_hora'];
$ref_area            = $_POST['ref_area'];
$area                = $_POST['area'];
$exibe_historico     = $_POST['exibe_historico'];

CheckFormParameters(array("ref_curso",
                            "ref_campus",
                            "ref_disciplina",
                            "semestre_curso",
                            "curriculo_mco",
                            "exibe_historico"));

$dt_inicio = $dt_inicio_curriculo;
$dt_final = $dt_final_curriculo;

$dt_inicio_curriculo = InvData($dt_inicio_curriculo);
$dt_final_curriculo = InvData($dt_final_curriculo);

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = " insert into cursos_disciplinas (" .
       "        ref_curso," .
       "        ref_campus," .
       "        ref_disciplina," .
       "        semestre_curso," .
       "        curriculo_mco," .
       "        equivalencia_disciplina," .
       "        cursa_outra_disciplina," .
       "        dt_inicio_curriculo," .
       "        dt_final_curriculo," .
       "        curso_substituido," .
       "        disciplina_substituida," .
       "        pre_requisito_hora," .
       "        ref_area," .   
       "        exibe_historico, " .
       "        fl_soma_curriculo)" . 
       " values (" ;
if ($ref_curso == '') { 
	$sql .= "null," ;
} else { 
	$sql .= "'$ref_curso'," ; 
}

if ($ref_campus == '') { 
	$sql .= "null," ;
} else { 
	$sql .= "'$ref_campus'," ; 
}

if ($ref_disciplina == '') { 
	$sql .= "null," ;
} else { 
	$sql .= "'$ref_disciplina'," ; 
}

if ($semestre_curso == '') { 
	$sql .= "null," ;
} else { 
	$sql .= "'$semestre_curso'," ; 
}

if ($curriculo_mco == '') {
	$sql .= "null," ;
} else { 
	$sql .= "'$curriculo_mco'," ; 
}

if ($equivalencia_disciplina == '') { 
	$sql .= "null," ;
} else { 
	$sql .= "'$equivalencia_disciplina'," ; 
}

if ($cursa_outra_disciplina == '') {
	$sql .= "null," ;
} else { 
	$sql .= "'$cursa_outra_disciplina'," ; 
}

if ($dt_inicio_curriculo == '') {
	$sql .= "null," ;
} else { 
	$sql .= "'$dt_inicio_curriculo'," ;
}

if ($dt_final_curriculo == '') { 
	$sql .= "null," ;
} else { 
	$sql .= "'$dt_final_curriculo'," ;
}

if ($curso_substituido == '') {
	$sql .= "null," ;
} else {
	$sql .= "'$curso_substituido'," ;
}

if ($disciplina_substituida == '') {
	$sql .= "null," ;
} else { 
	$sql .= "'$disciplina_substituida',";
}

if ($pre_requisito_hora == '') {
	$sql .= "null," ;
} else { 
	$sql .= "'$pre_requisito_hora'," ; 
}

if ($ref_area == '') { 
	$sql .= "null," ;
} else { 
	$sql .= "'$ref_area'," ;
}

$sql .= "'$exibe_historico', " .
              "'t')" ;  


$ok = $conn->Execute($sql);

SaguAssert($ok,"Nao foi possivel inserir o registro!");

$conn->Finish();
$conn->Close();

SuccessPage("Inclusão de Cursos Disciplinas",
            "location='../cursos_disciplinas.php'",
            "Curso Disciplina incluído com sucesso!!!",
            "location='../consulta_inclui_cursos_disciplinas.php'");

?>
<html>
<head>
</head>
<body>
</body>
</html>
