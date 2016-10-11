<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Отчет о прибылях и убытках</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.12.4.min.js" ></script>
<script type="text/javascript" src="../js/save_csv.js" ></script>
</head>
<body>
<div class="main">
  <div class="header">
    <div class="header_resize">
      <div class="logo">
        <h1><a href="../index.html">REPORTING <span>SYSTEM</span></a> <small>управленческая и финансовая отчетность</small></h1>
      </div>
      <div class="clr"></div>
      <div class="menu_nav">
        <ul>
          <li><a href="vvod_ps.php">Доходы по вводу и выводу</a></li>
          <li><a href="ds_merch.php">Денежные средства</a></li>
          <li class="active"><a href="pl.php">Финансовая отчетность</a></li>
        </ul>
      </div>
      <div class="clr"></div>
    </div>
  </div> 
 <div class="content">
    <div class="content_resize"> 
      <div class="clr"></div> 
      <div class="sidebar">
        <div class="clr"></div>
        <div class="gadget">
          <h2 class="star"><span>Отчеты</span></h2>
          <div class="clr"></div>
          <ul class="sb_menu">
            <li><a href="pl.php">Отчет о прибылях и убытках</a></li>
            <li><a href="bal.php">Отчет о финансовом состоянии</a></li>
          </ul>
        </div>
      </div>
     
<?php $h2 = '<h2>Отчет о прибылях и убытках</h2>';
		echo $h2;

require_once('../include/dbconnect.php'); //Подключаем файл с параметрами подключения к БД
if (isset($_POST['submit'])) //Проверка, была ли уже отправка данных
{
    $output_form = false; //Если данные формы отправлялись, проверочной переменной присваивается false
	
	$date_begin = $_POST['date_begin'];
	if (empty($date_begin)) {
      echo "<p class='errortext'>Не выбрана дата начала периода.</p><br />";
      $output_form = true;
    }
	$date_end = $_POST['date_end'];
	if (empty($date_end)) {
      echo "<p class='errortext'>Не выбрана дата окончания периода.</p><br />";
      $output_form = true;
    }   
}
else {
    $output_form = true;
}
	
if (!$output_form){
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
    or die('Error connecting to MySQL server.');
	mysqli_query($dbc, 'SET NAMES UTF8');
	
	$date_begin = mysqli_real_escape_string($dbc, $date_begin);
	$date_end = mysqli_real_escape_string($dbc, $date_end);
	
	$h4 = "<h4>Отчет за период с ".$date_begin." по ".$date_end."</h4>";
	echo $h4;
    
	echo '<div id="report">'; //Таблица в блоке div для сохранения в CSV
	$table = '<table><tr><th>Наименование показателя</th><th>UAH, тыс.</th><th>RUB, тыс.</th><th>USD, тыс.</th><th>EUR, тыс.</th></tr>';
	$agr=array();
	$query1 = "SELECT SUM(ps_commis) AS bank, SUM(convert_comm) AS conv, SUM(income) AS inc, SUM(profit) as pf
	FROM invoices where date BETWEEN '$date_begin' AND '$date_end' GROUP BY curr_ps";
	$result1 = mysqli_query($dbc, $query1)
      or die('Error querying database1.');	
	while ($row = mysqli_fetch_array($result1)) {
		array_push($agr,$row);}
	
	$query2 = "SELECT SUM(ps_commis) AS bank, SUM(convert_comm) AS conv, SUM(income) AS inc, SUM(profit) as pf
	FROM withdraws where date BETWEEN '$date_begin' AND '$date_end' GROUP BY curr_ps";
    $result2 = mysqli_query($dbc, $query2)
      or die('Error querying database2.');
	while ($row = mysqli_fetch_array($result2)) {
		array_push($agr,$row);}
		
	$table .= '<tr><td style="text-align:left;">Доходы по вводу</td><td>'.round($agr[2]['inc']/1000).'</td><td>'.round($agr[1]['inc']/1000).'</td><td>'.round($agr[3]['inc']/1000).'</td><td>'.round($agr[0]['inc']/1000).'</td></tr>';
	$table .= '<tr><td style="text-align:left;">Комиссия банка</td><td>('.round($agr[2]['bank']/1000).')</td><td>('.round($agr[1]['bank']/1000).')</td><td>('.round($agr[3]['bank']/1000).')</td><td>('.round($agr[0]['bank']/1000).')</td></tr>';
	$table .= '<tr><td style="text-align:left;">Прибыль от комиссионных операций</td><td>'.round($agr[2]['pf']/1000).'</td><td>'.round($agr[1]['pf']/1000).'</td><td>'.round($agr[3]['pf']/1000).'</td><td>'.round($agr[0]['pf']/1000).'</td></tr>';
	$table .= '<tr><td style="text-align:left;">Конвертационный доход</td><td>'.round($agr[2]['conv']/1000).'</td><td>'.round($agr[1]['conv']/1000).'</td><td>'.round($agr[3]['conv']/1000).'</td><td>'.round($agr[0]['conv']/1000).'</td></tr>';
	$table .= '<tr class="total"><td style="text-align:left;">Всего прибыль по вводу</td><td>'.round(($agr[2]['pf']+$agr[2]['conv'])/1000).'</td><td>'.round(($agr[1]['pf']+$agr[1]['conv'])/1000).'</td><td>'.round(($agr[3]['pf']+$agr[3]['conv'])/1000).'</td><td>'.round(($agr[0]['pf']+$agr[0]['conv'])/1000).'</td></tr>';
	$table .= '<tr><td colspan="5"></td></tr>';	
	$table .= '<tr><td style="text-align:left;">Доходы по выводу</td><td>'.round($agr[6]['inc']/1000).'</td><td>'.round($agr[5]['inc']/1000).'</td><td>'.round($agr[7]['inc']/1000).'</td><td>'.round($agr[4]['inc']/1000).'</td></tr>';
	$table .= '<tr><td style="text-align:left;">Комиссия банка</td><td>('.round($agr[6]['bank']/1000).')</td><td>('.round($agr[5]['bank']/1000).')</td><td>('.round($agr[7]['bank']/1000).')</td><td>('.round($agr[4]['bank']/1000).')</td></tr>';
	$table .= '<tr><td style="text-align:left;">Прибыль от комиссионных операций</td><td>'.round($agr[6]['pf']/1000).'</td><td>'.round($agr[5]['pf']/1000).'</td><td>'.round($agr[7]['pf']/1000).'</td><td>'.round($agr[4]['pf']/1000).'</td></tr>';
	$table .= '<tr><td style="text-align:left;">Конвертационный доход</td><td>'.round($agr[6]['conv']/1000).'</td><td>'.round($agr[5]['conv']/1000).'</td><td>'.round($agr[7]['conv']/1000).'</td><td>'.round($agr[4]['conv']/1000).'</td></tr>';
	$table .= '<tr class="total"><td style="text-align:left;">Всего прибыль по выводу</td><td>'.round(($agr[6]['pf']+$agr[6]['conv'])/1000).'</td><td>'.round(($agr[5]['pf']+$agr[5]['conv'])/1000).'</td><td>'.round(($agr[7]['pf']+$agr[7]['conv'])/1000).'</td><td>'.round(($agr[4]['pf']+$agr[4]['conv'])/1000).'</td></tr>';
	$table .= '<tr><td colspan="5"></td></tr>';
	$table .= '<tr><td class="total">Прибыль всего:</td>
		<td class="total" style="text-align:right;">'.round(($agr[2]['pf']+$agr[2]['conv']+$agr[6]['pf']+$agr[6]['conv'])/1000).'</td>
		<td class="total" style="text-align:right;">'.round(($agr[1]['pf']+$agr[1]['conv']+$agr[5]['pf']+$agr[5]['conv'])/1000).'</td>
		<td class="total" style="text-align:right;">'.round(($agr[3]['pf']+$agr[3]['conv']+$agr[7]['pf']+$agr[7]['conv'])/1000).'</td>
		<td class="total" style="text-align:right;">'.round(($agr[0]['pf']+$agr[0]['conv']+$agr[4]['pf']+$agr[4]['conv'])/1000).'</td></tr>';
	$table .= '</table>'; 

	echo $table; 
	echo '</div>';
	
	mysqli_close($dbc);
	
$pdf = $h2.$h4.htmlspecialchars($table);	
?>

</br></br>
<a href="#" id ="export" role='button'>Зберегти звіт в CSV</a></br></br>
<a href="#" onClick="document.forms['pdf'].submit()">Зберегти звіт в PDF</a>
<form action="../include/pdf.php" name="pdf" method="post" style="display:none">
	<input name="pdf" type="hidden" value="<?php echo $pdf; ?>">
</form>
</br></br>                
<?php 	   
}

if ($output_form) { //Если проверочная переменная = true, выводится форма для заполнения
?>
	<div class="mainbar">
        <div class="article">
		  <h4>Выберите даты начала и окончания периода</h4>
          <div class="clr"></div>
          <form id="Form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div>
		<div  class="wrapper">
			<span>Дата начала периода:</span>
			<div class="bg"><input type="date" name="date_begin" class="input" ></div>
		</div>
		<div  class="wrapper">
			<span>Дата окончания периода:</span>
			<div class="bg"><input type="date" name="date_end" class="input" ></div>
		</div>
		
		<input class="button" type="submit" value="OK" name="submit" /> 
	</div>
</form>
		  
		  
        </div>
      </div>
      <div class="clr"></div>
    </div>
  </div>
</div>




<?php
  }
?>		



</body>
</html>
