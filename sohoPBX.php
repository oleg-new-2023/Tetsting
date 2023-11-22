
<?php 

// TODO: Это нада в базу локальной  //
   $userArray = array(
//          201 => 'Агопян',
//          204 => 'Носачева',
//          203 => 'unknown',
            205 => 'Бадеева',
//          206 => 'Баркар',
//          207 => 'Изюмова',
            238 => 'Мирза',
//          208 => 'Савченко',
//          224 => 'Обнявко',
	    222 => 'Стажер 222',
  	    223 => 'Стажер 223',
//	    225 => 'Mirza',
//          226 => 'Химченко',
//	    230 => 'Мирошниченко',
//	    231 => 'Мартынюк',
//	    232 => 'Буйда',
//          234 => 'Арсенов',
//          235 => 'Михайлова',
//	    233 => 'Соломяный',
//          236 => 'Кара',
//          237 => 'Быстрова',
            239 => 'Овдейчук',
//          240 => 'Мушенко',
//          241 => 'Забигаева',
//          242 => 'Акишин',
            243 => 'Швец',
//          245 => 'Чернявский',
//          246 => 'Носкова',
            247 => 'Каюк',
//          248 => 'Щербинa',
//          249 => 'Ильченко',
            250 => 'Зеркалова',
            251 => 'Небога',
            252 => 'Чернов',
            253 => 'Попова',
            254 => 'Коробка',
//          255 => 'Овчаренко',
//          301 => 'Магар',
//          303 => 'Черный',
            305 => 'Ульянов',
//          306 => 'Шемяков',
//          307 => 'Хандусенко',
//          308 => 'Дубровский',
//          309 => 'Трегубов',
//          310 => 'Баклан',
//          311 => 'Лукьянченко',
//          312 => 'Вольпрес',
//          313 => 'Чайка',
//          314 => 'Осадчий',
//          316 => 'Новиков',
//          315 => 'Мазурин',
//          317 => 'Иванов',
//          318 => 'Матвеев',
//          319 => 'Могила',
//          322 => 'Мирошников',
//          323 => 'Куприенко',
//	    335 => 'Деревянко',
	    338 => 'Верикас',
//	    334 => 'Наумова',
	    333 => 'Стажер 333',
//	    337 => 'Бойко',
            339 => 'Починок',
//          340 => 'Куприйчук',
//          341 => 'Булавко',
//          342 => 'Гарбузов',
//          343 => 'Котолевич',
            344 => 'Белокуров',
//          345 => 'Тонкевич',
            244 => 'Бондаренко',
            346 => 'Мельник',
            347 => 'Погребной',
//          348 => 'Журавель',
            349 => 'Бушанский',
//          350 => 'Cычов',
            351 => 'Адыров',
            352 => 'Коробкин',
            353 => 'Вернидубов',
            354 => 'Левкин',
       );


class  sohoPBX{

	static function formatNumber($num){
		return preg_replace('/^\+?3?8?0?/','0',$num );
	}

	static function formatCallInfo($info){
	    $remove_array = array( "/\<\+?(\d+)\>?/", "/\"/","/\[/","/\]/" );
	    $ret = preg_replace( $remove_array ,"" ,$info );
	    if(preg_match("/id=(\d+)\//", $ret )){
        	    $ret = preg_replace( "/id=(\d+)\//","<a target='about:blank' href='https://manager.sohonet.ua/account/view?id=$1'> id=$1 </a>",$ret );
	            $bColor =  preg_match("/-\d+₴/",$ret) ? 'red':'green';
		    $ret = preg_replace( "/(-?\d+)₴/",", <span style='color:{$bColor};font-weight:bold;'> $1₴ </span>",$ret);
	    }
	    return $ret;

	}

        // Данная функция получает звонок из базы по имени записи для получения clid, где хранится инфо о клиенте 
	  static function getCallInfo($db, $recordingFile){
            $Q = mysqli_query($db,"SELECT * FROM realcdr WHERE recordingfile='{$recordingFile}'");
            if($Q){
		    $CALL = mysqli_fetch_assoc( $Q ) ;
		    return self::formatCallInfo($CALL['clid']);
	    }else{
		return false;
	    }

           }




  // This function returns array:
  //   0:   in percentage - start point and length in 1 - 100 scale on day timeline ( 86400 seconds timeline) 
  //   1-3: the precense % of the  given interval in shifts ( returns  % , number form 1 till 100 per each shift)
    static function getShiftsPresence($since, $till){
        date_default_timezone_set('Europe/Kiev');

        $periodStart =  strtotime(date("Y-m-d " . date('H:i',strtotime($since) ) ));
        $periodEnd =    strtotime(date("Y-m-d " . date('H:i',strtotime($till ) ) ));
        $Presences = array();
        $pLength = $periodEnd - $periodStart;
        $beginAt = $periodStart - strtotime(date("Y-m-d 00:00:00"));
	$Presences[0] = array( 
				'beginsInPercent' => round(($beginAt*100)/86400), 
				'lengthInPercent' => round(($pLength*100)/86400)
			);

        foreach(array(1,2,3) as $shiftID){

             $sh = self::getShift($shiftID);
             $shiftStart = strtotime($sh['from']);
             $shiftEnd  = strtotime($sh['till']);
             $shiftLength = $shiftEnd - $shiftStart;

             $offset1 = ( $periodStart > $shiftStart ) ? $periodStart - $shiftStart : 0;
             $offset2  = ( $periodEnd < $shiftEnd ) ? $shiftEnd - $periodEnd : 0;
             $shDuration = $shiftLength - ($offset1 + $offset2 );
             $Presences[$shiftID] = ( $shDuration > 0 ) ? round( ($shDuration*100)/$shiftLength ) : 0;
	}

        return $Presences;
    }

    //  This function returns points and duration in percentage proection on day timeline ( 86400 seconds = 1 day) for the given SIP name
    static function getPresenceIntervals($db, $SIP_name, $offSet = 0){
	    $result = array();
	    $intervals = mysqli_query($db,"SELECT ts, session_time FROM realstatus
                                           WHERE  datediff(now(),ts ) = {$offSet} AND
						    sip_name = '{$SIP_name}' AND
						    ifnull(session_time,0) > 0" );
	    while( $interval = mysqli_fetch_assoc( $intervals ) )
		    $result[] = array(
			    'final_point' =>  round( ( (strtotime(date("Y-m-d " . date('H:i',strtotime($interval['ts']) ) )) - strtotime(date("Y-m-d 00:00:00")) ) * 100 ) / 86400 ),
			    'session_time' => round( ($interval['session_time'] *100)/86400 )
		    );
	    return $result;
    }


    /*
    This function returns SIP registration status information for each SIPname by intervals, presence in shifts (%) 
    */	     
    static function getOnlineTimers($db, $dayOffset = 0 ){
	    $onlineTimers = array();
	    $SQL = "SELECT  sip_name, 
                            min(ts) as since,
                            max(ts) as till,        
                            TIME_FORMAT( sec_to_time(sum(session_time)),'%H:%i') as online_time 
                       FROM realstatus 
		       WHERE datediff(now(),ts ) = {$dayOffset} 
		       GROUP BY sip_name;";

	    $onlineTime = mysqli_query($db, $SQL );
	    while($online =  mysqli_fetch_array($onlineTime)){

		 $onlineTimers[$online['sip_name']] = $online ;
		 $onlineTimers[$online['sip_name']]['intervals'] = self::getPresenceIntervals($db, $online['sip_name'], $dayOffset);
                 $onlineTimers[$online['sip_name']]['presences'] = self::getShiftsPresence($online['since'], $online['till']);
	    }

	    return $onlineTimers;
    }




	static function getNoAnswerNumbers( $db,$shift = null ){
		$shift = self::getShift($shift);
		$SQL = "SELECT c.src, max(c.calldate) as time, c.data5 , department,billsec,duration
                                        FROM `realcdr` c
                                        WHERE c.calldate BETWEEN '". $shift['from'] ."' AND '" . $shift['till'] ."' AND 
                                             ( c.`disposition` = 'NO ANSWER' OR c.`disposition` = 'BUSY'  )  AND
					     LENGTH(c.src) > 4 AND 
					     c.src != c.dst AND
                                             c.src NOT IN (SELECT c2.src FROM `realcdr` c2 
							   WHERE c2.calldate BETWEEN '". $shift['from'] ."' AND '" . $shift['till'] ."' AND
								 c2.`disposition` like 'ANSWERED'  AND
								 c2.recordingfile != 'none' 
								   )
					 GROUP BY (c.`src`)";
		return (mysqli_query($db, $SQL )) ;
	}


    static function getNoAnswerOutBoundCalls( $db, $shift = null, $number = '' ){
	        // Negative shift - menas day offset in the past from current day 
   	        if($shift < 0 ) {
                   $shift_filter = " datediff(r1.calldate,now()) > {$shift} "; // datediff(date,now())  - returns negative value here!!
		}else{
  	           $shift = self::getShift($shift);
		   $shift_filter = " r1.calldate BETWEEN '". $shift['from'] ."' AND '" . $shift['till'] . "'" ;
		}   
		$num_filter = $number ? ' src = "{$number}" AND ' : '' ;
                $SQL = "SELECT r1.uniqueid, r1.src , r1.dst, max(r1.calldate) as time, r1.data5 ,department,billsec, duration
                                       FROM `realcdr` r1
                                        WHERE {$shift_filter} AND
                                              {$num_filter}         
					      LENGTH(r1.src) < 5 AND
					      ( billsec = 0 OR duration = 0 ) AND 
					      src != '1000' AND 
					      src NOT like 'SOHO-%'
					GROUP BY (r1.uniqueid)";
                return (mysqli_query( $db, $SQL ));
       }

	static function getNoAnswerCalls( $db, $shift = null, $number = '' ){
		$shift = self::getShift($shift);
		$filter = $number ? ' src = "{$number}" AND ' : '' ;
		$SQL = "SELECT r1.uniqueid, r1.src, max(r1.calldate) as time, r1.data5 ,department,billsec,duration
                                       FROM `realcdr` r1
					WHERE r1.calldate BETWEEN '". $shift['from'] ."' AND '" . $shift['till'] . "' AND
					      {$filter}  	
					      ( r1.`disposition` = 'NO ANSWER' OR r1.`disposition` = 'BUSY'  )  AND 
					      LENGTH(r1.src) > 4 AND  src != '1000'  AND src NOT like 'SOHO-%' AND 
                                              r1.uniqueid NOT IN (SELECT r2.uniqueid 
                                                                 FROM `realcdr` r2
                                                                 WHERE r2.uniqueid = r1.uniqueid AND
								       r2.disposition like 'ANSWERED' AND 
								       r2.recordingfile != 'none'		
								 )
                                        GROUP BY (r1.uniqueid)";
		return (mysqli_query( $db, $SQL ));

	}


        static function getNoAnswerCalls2( $db, $shift = null, $number = '', $days = 10 ){
                $shift = self::getShift($shift);
                $filter = $number ? ' src = "{$number}" AND ' : '' ;
                $SQL = "SELECT r1.uniqueid, r1.src, max(r1.calldate) as time, r1.data5 ,department,billsec
                                       FROM `realcdr` r1
                                        WHERE datediff(now(),r1.calldate ) < {$days}  AND
                                              {$filter}         
					      LENGTH(r1.src) > 4 AND  src != '1000'  AND src NOT like 'SOHO-%' AND 
					      dcontext != 'queue-exit' AND
                                              r1.uniqueid NOT IN (SELECT r2.uniqueid 
                                                                 FROM `realcdr` r2
                                                                 WHERE r2.uniqueid = r1.uniqueid AND
								       r2.disposition like 'ANSWERED' AND
								       r2.recordingfile != 'none' )
                                        GROUP BY (r1.uniqueid)";
                return (mysqli_query( $db, $SQL ));

	}


        static function getRequests( $db, $shift = null, $number = '', $days = 10 ){
                $shift = self::getShift($shift);
                $filter = $number ? ' src = "{$number}" AND ' : '' ;
                $SQL = "SELECT r1.uniqueid, r1.src, max(r1.calldate) as time, r1.data5 ,department,billsec, recordingfile
                                       FROM `realcdr` r1
                                        WHERE datediff(now(),r1.calldate ) < {$days}  AND
                                              {$filter}         
					      LENGTH(r1.src) > 4 AND  src != '1000'  AND src NOT like 'SOHO-%' AND 
					      lastapp = 'Record' AND
					      recordingfile != '' AND
					      dcontext = 'queue-exit'
                                        GROUP BY (r1.uniqueid)";
                return (mysqli_query( $db, $SQL ));

        }	


	static function getDelayedService( $db, $shift = null ){
		$shift = self::getShift($shift);
		$SQL = "SELECT service_status,
                                          src,
                                          SUM(CASE WHEN disposition ='ANSWERED' THEN 1 ELSE 0 END) as answer_counter,
                                          src, max(calldate) as mcalldate,
                                          department
                                    FROM realcdr
                                        WHERE calldate BETWEEN '". $shift['from'] ."' AND '" . $shift['till'] . "' AND
                                              service_status IS NOT null
                                       GROUP BY service_status,src
                                       ORDER BY max(calldate)";
		return (mysqli_query($db, $SQL));
	}

	static function getBalance( $db, $itemNumber ){
		$num = preg_replace('/^38/','',$itemNumber);
		$SQL = "SELECT balance FROM sim_cards WHERE number  = '{$num}'";
		$row = mysqli_fetch_assoc( mysqli_query( $db, $SQL ) );
		return round($row['balance']);
	}

   	static function getAnswerCntByNumber( $db, $itemNumber, $shift = null){
		$shift = self::getShift($shift);
		$SQL = "SELECT count(dst) as cnt  FROM realcdr
		       	  	WHERE calldate BETWEEN '". $shift['from'] ."' AND '" . $shift['till'] . "' AND 
				(channel like '%".$itemNumber."%') AND
				disposition like 'ANSWERED' AND
				lastapp LIKE 'Queue' AND
				dstchannel <> ''";
		$row = mysqli_fetch_assoc( mysqli_query($db, $SQL) );	
		return $row['cnt'];
	}

	static function getDialedByNumber( $db, $itemNumber, $shift = null){
		$shift = self::getShift($shift);
		$SQL = "SELECT count(dst) as cnt FROM realcdr 
			WHERE calldate BETWEEN '". $shift['from'] ."' AND '" . $shift['till'] . "' AND
			      dstchannel like '%".$itemNumber."%' AND
			      lastapp like 'Dial' AND
			      disposition like 'ANSWERED'";
		$row = mysqli_fetch_assoc( mysqli_query($db, $SQL) );
                return $row['cnt'];


	}

	static function getConnectedCalls( $type='in', $shift = null ){
		$shift = self::getShift( $shift );
                $counter = 0;
		$inFiles = glob( './monitor/' . $type . '/' . date("Y") . '/' . date("m") . '/' . date("d") . '/*.mp3') ;
		foreach($inFiles as $file)
	         if( filemtime($file) >= strtotime($shift['from']) && filemtime($file) < strtotime($shift['till']) )
		   $counter++;
                return $counter;  
	}

	static function getActiveCalls($type = 'in'){
		return count(glob('./monitor/' . $type . '/' . date("Y") . '/' . date("m") . '/' . date("d") . '/*.wav'));
	}




	// Return defaut shift for all day long
	static function  getShift($shift = null){
		// Shifts declaration - by default, each by 8h length//
		$shifts = array( array( 'from' => date("Y-m-d 00:00:00"),  'till' => date("Y-m-d 23:59:59") ),
				 array( 'from' => date("Y-m-d 00:00:00"),  'till' => date("Y-m-d 07:59:59") ),
				 array( 'from' => date("Y-m-d 08:00:00"),  'till' => date("Y-m-d 15:59:59") ),
				 array( 'from' => date("Y-m-d 16:00:00"),  'till' => date("Y-m-d 23:59:59") )
		 ); 

		// RETURN  ID only
		if( $shift == -1 ){
		 if( isset($_GET['shift'])  )
		    return array( 'active' => $_GET['shift'], 'selected' => $_GET['shift'] );

                 $shift = 0;
 		 for( $i=1; $i <= count($shifts)-1; $i++ )
	 	  if( time() >= strtotime($shifts[$i]['from']) && time() < strtotime( $shifts[$i]['till']) ) 
		    $shift = $i;

		 return array('active' => $shift );
		} 

		$default_shift = isset($_GET['shift']) ? $_GET['shift'] : 0;
		return $shifts[ $shift ? $shift : $default_shift ];
	}

	static function getDailyCalls($db, $SIP_name, $shiftID = 0, $callType = 'IN'){
		$output = array();
		$shift = self::getShift( $shiftID );
		$FILTER = ( $callType == 'IN' ) ? "src like '%{$SIP_name}'" : "dstchannel like 'SIP/{$SIP_name}-%' ";
		$SQL = " SELECT  hour(calldate) as H, count(*) as cnt
			 FROM	realcdr 
			 WHERE  {$FILTER} AND
				calldate BETWEEN '". $shift['from'] ."' AND '" . $shift['till'] . "' AND
				datediff(calldate, now() ) = 0 AND
				recordingfile != 'none' 
			  GROUP BY 1;";
		$res = mysqli_query($db, $SQL);
                while( $row = mysqli_fetch_assoc( $res ) )
		  $output[$row['H']] = $row['cnt']; 

		return $output;
        }

	static function getRecordings($path, $pattern, $shiftID = null){
	    $output = array();
	    $shift = self::getShift( $shiftID ); 
	    $remove_it = array('/-PBX/','/-P/' );

 	    foreach( glob($path) as $file ){
		    //           $file = preg_replace('/-PBX/','-',$file );
              preg_match($pattern, preg_replace( $remove_it,'-',$file), $matches);
	      $outOfShift = ( isset($shiftID) && $shiftID == -1 ); 
	      if( count($matches) > 1 && ( $outOfShift ||  filemtime($file) >= strtotime($shift['from']) && filemtime($file) < strtotime($shift['till']) ) )
	        array_push($output, preg_replace('/[^0-9:-]/','',$matches[1] ) );
	    }  

	    return $output;
	}


}
