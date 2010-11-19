<script language="PHP">
Function ValidaCpf($cpf)
{
$nulo = "12345678909";
$nulo1 = "11111111111";
$nulo2 = "22222222222";
$nulo3 = "33333333333";
$nulo4 = "44444444444";
$nulo5 = "55555555555";
$nulo6 = "66666666666";
$nulo7 = "77777777777";
$nulo8 = "88888888888";
$nulo9 = "99999999999";
$nulo0 = "00000000000";

if ($cpf == ''){
   $obj = 1;
   return $obj;
}
     
/*Verifica se realmente foram digitados 11 digitos */
if (strlen($cpf) != 11) {
   $obj = 0;
   return $obj;
}

if (($cpf == $nulo) || ($cpf == $nulo1) || ($cpf == $nulo2)
|| ($cpf == $nulo3) || ($cpf == $nulo4)
|| ($cpf == $nulo5) || ($cpf == $nulo6)
|| ($cpf == $nulo7) || ($cpf == $nulo8)
|| ($cpf == $nulo9) || ($cpf == $nulo0)) {

   $obj = 0;
   
}
else
   {
     /* Alocação de cada digito digitado no formulário, em uma celula de
um vetor */
     for ($i=0; $i<11; $i++) {
           $cpf_temp[$i]="$cpf[$i]";
     }
     /*Calcula o penúltimo dígito verificador*/
     $acum=0;
     for ($i=0; $i<9; $i++){
           $acum=$acum+($cpf[$i]*(10-$i));
     }
     $x="$acum";
     $x %= 11;
     if ($x>1)
          $acum = 11 - $x;
     else
         $acum = 0;
     $cpf_temp[9]="$acum";


     /* Calcula o último dígito verificador*/
     $acum=0;
     for ($i=0; $i<10; $i++) {
           $acum=$acum+($cpf_temp[$i]*(11-$i));
     }
     $x="$acum";
     $x%=11;
     if ($x>1)
         $acum=11-$x;
     else
         $acum=0;
     $cpf_temp[10]="$acum";

     /* Este laço verifica se o cpf original é igual ao cpf gerado pelos
dois laços acima*/

     for ($i=0; $i<11; $i++) {
           if ($cpf[$i] != $cpf_temp[$i]) {
                $obj = 0;
                $i=10;
                $z=1;
           }
     }
     if ($z!=1)
           $obj = 1;
   }
  return $obj;
}
</script>
