<?php
require_once("../dompdf/dompdf_config.inc.php");
/* if (isset ($_GET['pdf'])){ */ 
$pdf=$_POST['pdf'];/* }
	else echo 'Error'; */

$html =
'<html><meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../css/pdf.css" />
<body>'.
$pdf.
'</body></html>';



$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream('mypdf.pdf'); // Выводим результат (скачивание)

?>