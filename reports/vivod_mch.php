<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Доходы по выводу по мерчантам</title>
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
          <li class="active"><a href="vvod_ps.php">Доходы по вводу и выводу</a></li>
          <li><a href="ds_merch.php">Денежные средства</a></li>
          <li><a href="pl.php">Финансовая отчетность</a></li>
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
            <li><a href="vvod_ps.php">Доходы по вводу в разрезе платежных систем</a></li>
            <li><a href="vivod_ps.php">Доходы по выводу в разрезе платежных систем</a></li>
            <li><a href="vvod_mch.php">Доходы по вводу в разрезе мерчантов</a></li>
            <li><a href="vivod_mch.php">Доходы по выводу в разрезе мерчантов</a></li>
          </ul>
        </div>
      </div>
     
<?php $h2 = '<h2>Доходы по выводу в разрезе мерчантов</h2>';
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
  
    $query1 = "SELECT curr_ps, merch_id, merch_name, SUM(withdr_ps_curr), SUM(ps_withdr), SUM(ps_commis), SUM(convert_comm), SUM(income), SUM(profit)
	FROM withdraws WHERE date BETWEEN '$date_begin' AND '$date_end' GROUP BY curr_ps, merch_id";
	$result1 = mysqli_query($dbc, $query1)
      or die('Error querying database1.');
	$h4 = "<h4>Отчет за период с ".$date_begin." по ".$date_end."</h4>";
	echo $h4;
	//Формируем вывод результатов в таблице
	echo '<div id="report">'; //Таблица в блоке div для сохранения в CSV	
	  
	$table = '<table><tr><th>Валюта</th><th>ID мерч.</th><th>Мерчант</th><th>Списано с кассы</th><th>Списано в ПС</th>
				<th>Комиссия ПС</th><th>Конв. комиссия</th><th>Доход</th><th>Прибыль</th></tr>';	
	while ($row = mysqli_fetch_array($result1)) {
		$table .= '<tr><td style="text-align:left;">'.$row['curr_ps'].'</td>';
		$table .= '<td style="text-align:center;">'.$row['merch_id'].'</td>';
		$table .= '<td style="text-align:left;">'.$row['merch_name'].'</td>';
		$table .= '<td>'.number_format($row['SUM(withdr_ps_curr)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(ps_withdr)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(ps_commis)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(convert_comm)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(income)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(profit)'],2,',','').'</td></tr>';
    }
	$query2 = "SELECT curr_ps, SUM(withdr_ps_curr), SUM(ps_withdr), SUM(merch_get), SUM(ps_commis), SUM(convert_comm), SUM(income), SUM(profit)
	FROM withdraws where date BETWEEN '$date_begin' AND '$date_end' GROUP BY curr_ps";
    $result2 = mysqli_query($dbc, $query2)
      or die('Error querying database2.');
	$table .= '<tr><td colspan="9" class="total">Всего по валютам:</td></tr>';
	while ($row = mysqli_fetch_array($result2)) {
		$table .= '<tr><td style="text-align:left;">'.$row['curr_ps'].'</td>';
		$table .= '<td></td><td></td>';
		$table .= '<td>'.number_format($row['SUM(withdr_ps_curr)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(ps_withdr)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(ps_commis)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(convert_comm)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(income)'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['SUM(profit)'],2,',','').'</td></tr>';
    }
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
