/********************************************************************
 * openWYSIWYG settings file Copyright (c) 2006 openWebWare.com
 * Contact us at devs@openwebware.com
 * This copyright notice MUST stay intact for use.
 *
 * $Id: wysiwyg-settings.js,v 1.4 2007/01/22 23:05:57 xhaggi Exp $
 ********************************************************************/

/*
 * Full featured setup used the openImageLibrary addon
 */
var full = new WYSIWYG.Settings();

//full.ImagesDir = "openwysiwyg_v1.4.7/images/";
//full.PopupsDir = "openwysiwyg_v1.4.7/popups/";
//full.CSSFile = "openwysiwyg_v1.4.7/styles/wysiwyg.css";

full.Width = "85%";
full.Height = "250px";
// customize toolbar buttons
full.addToolbarElement("font", 3, 1);
full.addToolbarElement("fontsize", 3, 2);
full.addToolbarElement("headings", 3, 3);
// openImageLibrary addon implementation
full.ImagePopupFile = "addons/imagelibrary/insert_image.php";
full.ImagePopupWidth = 600;
full.ImagePopupHeight = 245;



/*
 * Small Setup Example
 */
var small = new WYSIWYG.Settings();
small.Width = "350px";
small.Height = "100px";
small.DefaultStyle = "font-family: Arial; font-size: 12px; background-color: #AA99AA";
small.Toolbar[0] = new Array("font", "fontsize", "bold", "italic", "underline"); // small setup for toolbar 1
small.Toolbar[1] = ""; // disable toolbar 2
small.StatusBarEnabled = false;


/*
* AVISO  - EDITOR PEQUENO COM POUCAS OPCOES
* Santiago Silva Pereira em 15/07/2008
* Alterado por Wanderson S Reis em 15/03/2011
*/

var AVISO = new WYSIWYG.Settings();

AVISO.ImagesDir = "../../lib/openwysiwyg/images/";
AVISO.PopupsDir = "../../lib/openwysiwyg/popups/";
AVISO.CSSFile = "../../lib/openwysiwyg/styles/wysiwyg.css";

AVISO.Width = "500";
AVISO.Height = "250px";
/*
font, fontsize,	bold, italic, underline, forecolor, backcolor, justifyleft, justifycenter, justifyright, unorderedlist, orderedlist, outdent, indent, subscript, superscript, cut, copy, paste, removeformat, undo, redo, inserttable, insertimage, createlink, seperator, undo, redo, seperator, preview, print, viewSource, help
*/
AVISO.Toolbar[0] = new Array("font","fontsize", "bold","italic","underline","forecolor")
AVISO.Toolbar[1] = "";
AVISO.StatusBarEnabled = false;

