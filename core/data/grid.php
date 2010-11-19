<?php

require_once dirname(__FILE__)."/../../lib/adodb5/adodb-pager.inc.php";
require_once dirname(__FILE__)."/../../config/configuracao.php";


class grid {

    public $atributes =
    '<style>
        table.grid_pagined {
            font-size:12px;
            border-collapse: collapse; /* CSS2 */
            background: #FFFFFF;
        }

        table.grid_pagined td {
            border: 1px solid black;
        }

        table.grid_pagined th {
            border: 1px solid black;
            border-bottom: 2px solid black;
            background: #AAD5FF;
            color: #000000;
        }
        table.grid_pagined tr:hover {
            background: #fff7ab;
            color: #000000;
        }
    </style>';

    public $pager;

    public function  __construct() {

    }

    public function render($conn, $sql, $rows_per_page = 10) {
        $this->pager = new ADODB_Pager($conn, $sql);
        $this->pager->gridAttributes = 'class="grid_pagined"';
        $this->pager->htmlSpecialChars = false;
        $this->pager->page = 'P&aacute;gina';
        echo $this->atributes;
        $this->pager->Render($rows_per_page);
    }

    function options($param, $path_update = null, $path_remove = null)
    {
        if(!empty($path_update) or !($path_update == null) or !($path_update == '')) {
            $update  = ', \'<a href="'.$path_update;
            $update .= '?id=\' || ' . $param . ' || \'">';
            $update .= '<img src="'.$BASE_DIR.'../../public/images/icons/edit.png" alt="Alterar" title="Alterar" />';
            $update .= '</a>  \'';
        }

        if(!empty($path_remove) or !($path_remove == null) or !($path_remove == '')) {
            $remove  = ' || \' <a href="'.$path_remove;
            $remove .= '?id=\' || ' . $param . ' || \'" ';
            $remove .= "onclick=\"return confirm(\'Deseja realmente excluir?\')\" >";
            $remove .= '<img src="'.$BASE_DIR.'../../public/images/icons/delete.png" alt="Excluir" title="Excluir" />';
            $remove .= '</a>  \'';
        }

        $options = $update . $remove . ' as "Opções"';

        return $options;
    }
}
?>
