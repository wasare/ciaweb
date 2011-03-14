<?
//----------------------------------------------------------------------------
// Retorna o status da disciplina - Aprovado, Reprovado, Matriculado, 
// Desistente, Cancelada ou Dispensado
// Paulo Roberto Mallmann - 03/06/2002
//----------------------------------------------------------------------------
Function StatusDisciplina($dt_cancelamento, $nota_final, $fl_liberado, $conceito, $ref_periodo, $conn)
{
  
    $sql_media = "select media_final from periodos where id = '$ref_periodo'";
    
    $query_media = @$conn->CreateQuery($sql_media);

    if ( @$query_media->MoveNext() )
        $media_final = $query_media->GetValue(1);
    
    if (empty($media_final))
        SaguAssert(0 ," Cadastre a Média Final do Período <b>$ref_periodo</b>");

    $query_media->Close();

    if ($dt_cancelamento == 'f')
    {  
    	$status = 'Cancel';  
    }
    else
    {
        // Matriculado
     	if ((($nota_final == '0') || ($nota_final == '')) && (($fl_liberado == ' ') || (empty($fl_liberado))))
     	{  
     	 	$status = 'Matric';  
     	}
	    // Aprovado
     	if ($nota_final >= $media_final)
     	{  
     		$status = 'Aprova';  
     	}
        // Reprovado
     	if ($fl_liberado == '1')
     	{  
     		$status = 'Reprov';  
     	}
        // Desistente
     	if ($fl_liberado == '2')
     	{  
     		$status = 'Desist';  
     	}
        // Aprovado
     	if (($fl_liberado == '3') && ($nota_final >= $media_final))
     	{  
     		$status = 'Aprova';  
     	}
        // Dispensado
     	if ($fl_liberado == '4')
     	{	  
        	$status = 'Dispen';  
     	}
        // Reprovado
     	if ((($fl_liberado == ' ') || (empty($fl_liberado))) && (($nota_final < $media_final) && ($nota_final != '0') && (!empty($nota_final))))
     	{  
     		$status = 'Reprov';  
     	}
	    // Aproveitamento por conceito
	    if (($conceito == 'Desis') || ($conceito == 'Disp.'))
     	{  
     		$status = "$conceito";  
     	}
	    if (($conceito != 'Desis') && ($conceito != 'Disp.') && ($conceito != ''))
	    {
     		$status = 'Aprova';  
	    }

    }
  
return $status;

}
?>
