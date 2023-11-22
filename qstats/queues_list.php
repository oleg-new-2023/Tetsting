<?php
   include_once('../sohoPBX.php');
   session_start();
   if( isset($_SESSION['QSTATS']['hideloggedoff'] ))
     $offLine =( $_SESSION['QSTATS']['hideloggedoff'] != 1 )?'Unavailable|':'';
   else
     $offLine = 'Unavailable|'; 

   exec('sudo /usr/sbin/asterisk -rx "queue show" 2>/dev/null |egrep -vi "Members|'.$offLine.'ending"|awk -F"W:" \'{print($1)}\'', $QCalls_ );
   $QCalls = is_array($QCalls_) ? bashColorToHtml( preg_replace("/'/","", implode("<br>\n", $QCalls_ ) ) ): 'Нет очередей' ;


   exec('sudo /usr/sbin/asterisk -rx "dialplan show globals " 2>/dev/null |grep "MAXPAUSED"|awk -F"=" \'{print($2)}\'', $_QMAXPAUSED ); 
   exec('sudo /usr/sbin/asterisk -rx "dialplan show globals " 2>/dev/null |grep "QPAUSED"|awk -F"=" \'{print($2)}\'', $_QPAUSED );
   $QMAXPAUSED = "<span>&nbsp;&nbsp;&nbsp;&nbsp; Лимит [ support ] операторов на паузе:<b> ${_QMAXPAUSED[0]}</b> , Сейчас на паузе: <b>${_QPAUSED[0]}</b><br>";

   
    echo $QMAXPAUSED . $QCalls;

   function bashColorToHtml($string) {
    global $userArray;

    $colors = [
        '/[[:^print:]]/s' => '',
	'/\;40m/' => 'm',
	'/has taken no calls yet/' => '',
	'/max unlimited\) in /' => '',
	'/\sSIP\/(\d+)\s/' => '<b style="padding-left:80px;">SIP/$1</b> ',
	'/paused/' => '<span style="color:yellow;opacity:0.7"><b>На паузе</b></span>',
	'/Ringing/' => '<span style="color:linegreen:>Вызов</span>',
        '/Unavailable/' => '<span style="color:red;opacity:0.8"><b>Выключен</b></span>',
	'/No Callers/' => '<span style="color:green;">&nbsp;&nbsp;&nbsp;В очереди нет ожидающих звоноков</span>',
	'/Callers:/' => '<span class="blinked">&nbsp;&nbsp;&nbsp;Ожидающие ответа:</span>',
	'/(\w+?&?\w+) has (\d+)/s' => '<span style="font-size:16px;color:green;"><b>[ $1 ]</b></span> has $2',
	'/has ([1-9]+) calls \(/' => ' <span class="red">[ Абонентов в ожидании: $1 ] </span>',	
	'/has 0 calls \(/' => ' <span style="color:green;">Очередь пуста </span>',
        '/\[0;30m(.*?)\[0m/s' => '<span class="black">$1</span>',
        '/\[0;31m(.*?)\[0m/s' => '<span class="red">$1</span>',
        '/\[0;32m(.*?)\[0m/s' => '<span class="green">$1</span>',
        '/\[0;33m(.*?)\[0m/s' => '<span class="brown">$1</span>',
        '/\[0;34m(.*?)\[0m/s' => '<span class="blue">$1</span>',
        '/\[0;35m(.*?)\[0m/s' => '<span class="purple">$1</span>',
        '/\[0;36m(.*?)\[0m/s' => '<span class="limegreen">$1</span>',
        '/\[0;37m(.*?)\[0m/s' => '<span class="light-gray">$1</span>',
        '/\[1;30m(.*?)\[0m/s' => '<span class="dark-gray">$1</span>',
        '/\[1;31m(.*?)\[0m/s' => '<span class="light-red">$1</span>',
        '/\[1;32m(.*?)\[0m/s' => '<span class="light-green">$1</span>',
        '/\[1;33m(.*?)\[0m/s' => '<span class="yellow">$1</span>',
        '/\[1;34m(.*?)\[0m/s' => '<span class="light-blue">$1</span>',
        '/\[1;35m(.*?)\[0m/s' => '<span class="light-purple">$1</span>',
        '/\[1;36m(.*?)\[0m/s' => '<span class="limegreen">$1</span>',
        '/\[1;37m(.*?)\[0m/s' => '<span class="white">$1</span>',
        '/\[1;30m/s' => '<span style="color:black">',
        '/\[1;31m/s' => '<span style="color:red">',
        '/\[1;32;40m/s' => '<span style="color:green">',
        '/\[1;33m/s' => '<span style="color:yellow">',
        '/\[1;34m/s' => '<span style="color:blue">',
        '/\[1;35m/s' => '<span style="color:purple">',
        '/\[1;36m/s' => '<span style="color:limegreen">',
        '/\[1;37m/s' => '<span style="color:white">',
	'/\[0m/s'   => ' ',
	'/Busy/'   => '<span style="color:silver;background-color:red;">Линия занята</span>',
        '/In use/s' => '<span style="color:orange;font-weight:bold;">Занят</span>',
        '/\(ringinuse disabled\)/' => '',
        '/in call/s' => '<span style="color:limegreen;font-weight:bold;">Разговор</span>',
        '/Not in use/s' => '<span style="color:#00b6ff;font-weight:bold;">Свободен</span>',
        '/ SIP\//s' => '&nbsp;&nbsp;&nbsp; SIP/'

    ];
    $string = preg_replace(array_keys($colors), $colors, $string);
    foreach($userArray as $sip=>$n )
	    $string =  preg_replace("/SIP\/{$sip}/", "<div style='text-align:right;display:inline-flex;min-width:110px;font-size:14px;color:silver !important'>{$n}</div><span style='color:white;'>&#9990;{$sip}</span>", $string);

    $string =  preg_replace("/SIP\/(\d+)/", "<div style='text-align:right;display:inline-flex;min-width:110px;font-size:14px;color:silver !important'>\$1</div><span style='color:white;'>&#9990;\$1</span>", $string);

     return $string;

    }




?>



