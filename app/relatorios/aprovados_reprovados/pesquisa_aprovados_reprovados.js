function ChangeOption(opt,fld){

  var i = opt.selectedIndex;

  if ( i != -1 )
    fld.value = opt.options[i].value;
  else
    fld.value = '';
}

function ChangeOp() {
  ChangeOption(document.form1.periodo,document.form1.periodo1);
}

function ChangeCode(fld_name,op_name){
 
  var field = eval('document.form1.' + fld_name);
  var combo = eval('document.form1.' + op_name);
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

  alert(code + ' não é um código válido!');

  field.focus();

  return true;
}

function setPeriodo() {
	periodo = $F('periodo1');
        var url = 'set_periodo.php';
        var parametros = 'p=' + periodo;
	var myAjax = new Ajax.Request( url, { method: 'post', parameters: parametros, onSuccess: function(transport) { return true; } });
}

function abre_consulta_rapida(url) {
	window.open(url,'consulta_curso','resizable=yes, toolbar=no,width=450,height=450,scrollbars=yes,top=0,left=0');
}
