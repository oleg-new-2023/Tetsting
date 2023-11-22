<?php
/*
   Sohonet.ua

   This file is part of Asternic Call Center Stats.

    Asternic Call Center Stats is free software: you can redistribute it 
    and/or modify it under the terms of the GNU General Public License as 
    published by the Free Software Foundation, either version 3 of the 
    License, or (at your option) any later version.

    Asternic Call Center Stats is distributed in the hope that it will be 
    useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Asternic Call Center Stats.  If not, see 
    <http://www.gnu.org/licenses/>.
*/

require_once("config.php");
include("sesvars.php");
?>
<!-- http://www.house.com.ar/quirksmode -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Asternic Call Center Stats</title>
	<style type="text/css" media="screen">@import "css/basic.css";</style>
	<style type="text/css" media="screen">@import "css/tab.css";</style>
	<style type="text/css" media="screen">@import "css/table.css";</style>
	<style type="text/css" media="screen">@import "css/fixed-all.css";</style>
	<script type="text/javascript" src="js/sorttable.js"></script>
	<script type="text/javascript" src="js/prototype-1.4.0.js"></script>
        <script language='javascript'>
		Event.observe(window, 'load', init, false);
		function init(){
			getdata();
		}
		function getdata(){
 		    var url = 'queues_list.php';
		    var target = 'content_refresh';
		    var myAjax = new Ajax.PeriodicalUpdater(target, url, {asynchronous:true, frequency:5});
		}
		function sethide(elemento) {
		   var url  = 'set_sesvar.php';
                   if(elemento.checked==true) {
		   	var pars = 'sesvar=hideloggedoff&value=1';
		    } else {
		   	var pars = 'sesvar=hideloggedoff&value=0';
                    }
			var myAjax = new Ajax.Request(
				url, 
				{
				method: 'get', 
				parameters: pars
				});
		}

  function toggle(obj){
     var x = document.getElementById(obj);
     if (x.style.display === "none") {
      x.style.display = "block";
     } else {
      x.style.display = "none";
     }
  }	  
 
	</script>
</head>
<body style='background-color:black;'>
<?php include("menu.php"); ?>
<div id="main">
        <div id="contents" style='min-width: 900px;background-color:#282828;'>

<div style='min-width: 870px'>
<h1 style='color:silver;'>
<?php
echo $lang[$language]['current_agent_status'];
?>
</h1>
<Br>
<a href='#' ><span onclick='toggle("dtmf");'>Показать Фунциональные Команды DTMF(во время разговора) и номера набора</span></a>
<span style='color:gray'>

<table id=dtmf style='display:none'>
<tr> <td>#1[номер оператора]  </td><td> Перевод звонка на определенного оператора ( абонотдела либо тех суппорта) </td></tr>
<tr> <td>#1200  </td><td> Перевод звонка в абонотдел <small> Звонок поступает прямо в очередь Abon. Перед отправкой убедится что там есть операторы на месте</small> </td></tr>
<tr> <td>#1300  </td><td> Перевод звонка в тех.поддержку <small> Звонок поступает прямо в очередь Support. Перед отправкой убедится что там есть операторы на месте</small> </td></tr>
<tr> <td>*8  </td><td>  Перехвата звонка оператором из соседней очереди.<br><small> Пример: вы в очереди <b>Abon</b>, но видите звонок в ожидании в Очереди <b>Support</b>,  чтобы перехватить его себе - наберите *8 и кнопку Звонить (зеленую).</small> </td></tr>

<tr> <td>*80[номер оператора]  </td><td> Прослушивание разговора <small> Режим прослушивание - вас никто не слышит при этом</small> </td></tr>
<tr> <td>*81[номер опертора]  </td><td> Курирование разговора<small> В этом режиме вас слышит только  оператор.</small> </td></tr>
<tr> <td>*82[номер опертора]  </td><td> Вмешивание в  разговор<small> В этом режиме вы становитесь третим участником разговора.</small> </td></tr>
<tr> <td>**1  </td><td> Стать на паузу во всех очередях </td></tr>
<tr> <td>**0  </td><td> Снятся с паузы во всех очередях</tr>
</table>
 

</span>
<hr>

<input type=checkbox  id=showOffline <?php echo ($_SESSION['QSTATS']['hideloggedoff']==1)?'checked':''; ?> onchange='sethide(this);'>
 <span style='color:silver!important;'> Показать Выключенных Операторов</span> <br>
<div id='content_refresh' style='color:silver;'>
</div>



</div>
</div>
</div>
<script type="text/javascript" src="js/wz_tooltip.js"></script>
</body>
</html>
