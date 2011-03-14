<?
// ----------------------------------------------------------
// Verifica o motivo de desativação do contrato
// ----------------------------------------------------------
function VerificaContrato($ref_motivo_desativacao)
{
?>
  <script language="JavaScript">

  var ref_motivo_desativacao;
  
  <? echo "ref_motivo_desativacao=$ref_motivo_desativacao;\n";  ?>
  
  
  if (ref_motivo_desativacao == '105' ||
      ref_motivo_desativacao == '152' ||
      ref_motivo_desativacao == '550' ||
      ref_motivo_desativacao == '6' ||
      ref_motivo_desativacao == '10' ||
      ref_motivo_desativacao == '16' ||
      ref_motivo_desativacao == '14'
     )
  {
    url = "javascript:history.go(-1)";
    
    if (confirm("O contrato deste aluno foi desativado por um dos seguintes motivos:\n" +
                "105 - Transferência para outra Instituição\n" +
                "152 - Guia de Tranferência Expedida\n" +
                "550 - Óbito\n" +
                "6 - Tranferência interna para outro Curso\n" +
                "10 - Reingresso com transferência para outro curso\n" +
                "16 - Vestibulando desistente de vaga\n" +
                "14 - Conclusão de todas as disciplinas do curso\n" +
                "Deseja alterar o contrato mesmo assim?"))
    {
         alert("Não esqueça de mudar o Motivo de Ativação e o \nStatus do Livro Matrícula do Contrato do Aluno.");
    }
    else
    {
        location=(url);
    }
  }
</script>
<?
}
?>
