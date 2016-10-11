<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Денежные средства на счетах мерчантов</title>
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
          <li class="active"><a href="ds_merch.php">Денежные средства</a></li>
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
            <li><a href="ds_merch.php">Денежные средства на счетах мерчантов</a></li>
            <li><a href="ds_ps.php">Денежные средства в платежных системах</a></li>
            <li><a href="ds_compare.php">Сравнение остатков ДС по мерчантам и ПС</a></li>
          </ul>
        </div>
      </div>
     
<?php $h2 = '<h2>Денежные средства на счетах мерчантов</h2>';
		echo $h2;

require_once('../include/dbconnect.php'); //Подключаем файл с параметрами подключения к БД
if (isset($_POST['submit'])) //Проверка, была ли уже отправка данных
{
    $output_form = false; //Если данные формы отправлялись, проверочной переменной присваивается false
	
	$date_balance = $_POST['date_balance'];
	if (empty($date_balance)) {
      echo "<p class='errortext'>Не выбрана дата формирования балансов.</p><br />";
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
	
	$date_balance = mysqli_real_escape_string($dbc, $date_balance);
  
    $query1 = "SELECT p.merch_name, p.curr_merch, i.inSum, o.outSum, IFNULL(i.inSum,0)-IFNULL(o.outSum,0) AS balance
	FROM (SELECT merch_name, curr_merch from merchants) AS p
	LEFT JOIN
	(SELECT merch_name, curr_merch, SUM(input_co_curr) AS inSum FROM invoices
	WHERE date <= '$date_balance' GROUP BY merch_name, curr_merch)
	AS i ON p.merch_name=i.merch_name AND p.curr_merch=i.curr_merch
	LEFT JOIN
	(SELECT merch_name, curr_merch, SUM(withdr_co_curr) AS outSum FROM withdraws
	WHERE date <= '$date_balance' GROUP BY merch_name, curr_merch)
    AS o ON p.merch_name=o.merch_name AND p.curr_merch=o.curr_merch";
	$result1 = mysqli_query($dbc, $query1)
      or die('Error querying database1.');
	$h4 = "<h4>Отчет на дату ".$date_balance."</h4>";
	echo $h4;
	//Формируем вывод результатов в таблице
	echo '<div id="report">'; //Таблица в блоке div для сохранения в CSV	
	  
	$table = '<table><tr><th>Мерчант</th><th>Валюта</th><th>Ввод всего</th><th>Вывод всего</th><th>Баланс</th></tr>';	
	while ($row = mysqli_fetch_array($result1)) {
		$table .= '<tr><td style="text-align:left;">'.$row['merch_name'].'</td>';
		$table .= '<td style="text-align:left;">'.$row['curr_merch'].'</td>';
		$table .= '<td>'.number_format($row['inSum'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['outSum'],2,',','').'</td>';
		$table .= '<td>'.number_format($row['balance'],2,',','').'</td></tr>';
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
		  <h4>Выберите дату получения балансов</h4>
          <div class="clr"></div>
          <form id="Form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div>
		<div  class="wrapper">
			<span>Дата балансов:</span>
			<div class="bg"><input type="date" name="date_balance" class="input" ></div>
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
