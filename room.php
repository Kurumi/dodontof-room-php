<?php

/*
 Original Code by http://www.vampire-blood.net/1170.html
*/
define('OUT_LOGIN_TIME',30);

function ddf_loadRooms($savedata_dir,$sorttype='',$game_none='設定なし') {
	$dirs = getdirlist($savedata_dir);
	date_default_timezone_set('Asia/Tokyo');
	
	$datas = array();
	$gamenames=array();
	$gamenames['diceBot'] = $game_none;
	$gamenames[null] = $game_none;
	$dt = time();
	$dt -= OUT_LOGIN_TIME;
	foreach($dirs as $row => $value) {
		$nowdata = json_decode(file_get_contents($savedata_dir."/".$value.'/playRoomInfo.json'),true);
		$login_num = 0;
		if (file_exists($savedata_dir."/".$value.'/login.json')) {
			$logindata = json_decode(file_get_contents($savedata_dir."/".$value.'/login.json'),true);
			$nullnames= 0;
			$mt = 0;
			foreach($logindata as $logincode => $loginval) {
				if (strpos($logincode,"\t") !== false) {
					
					$login_num++;
					if ($loginval['timeSeconds'] <= $dt) {
						$nullnames++;
					}
				}
				$mt = max($mt,$loginval['timeSeconds']);
			}
			$logindata=null;
			if ($nullnames >= $login_num) {
				$login_num = 0;
			}
			
		}
		$id = substr($value,5);
		if (!isset($gamenames[$nowdata['gameType']])) {
			$gamenames[$nowdata['gameType']] = ddf_loadRuby($nowdata['gameType']);
		}
		$nowgame = $gamenames[$nowdata['gameType']];
		$datas[] = array('id'=>$id-0,'name'=>$nowdata['playRoomName'],'dice'=>$nowgame,
			'visit'=>($nowdata['canVisit']===true),'time'=>$mt,
			'pass'=> ($nowdata['playRoomChangedPassword']!==null),'members'=> $login_num);
	}
	
	if ($sorttype != '') {
		foreach($datas as $key => $row){
			$dt_id[$key] = $row["id"];
			$dt_name[$key] = $row["name"];
			$dt_system[$key] = $row["dice"];
			$dt_visit[$key] = $row["visit"];
			$dt_time[$key] = $row["time"];
			$dt_pass[$key] = $row["pass"];
			$dt_members[$key] = $row["members"];
		}
		switch($sorttype) {
			case 'ID':case 'id':
				array_multisort($dt_id,SORT_ASC,$datas);
				break;
			case '!ID':case '!id':
				array_multisort($dt_id,SORT_DESC,$datas);
				break;
			case 'Name':case 'name':case 'NAME':
				array_multisort($dt_name,SORT_ASC,$dt_id,SORT_ASC,$datas);
				break;
			case '!Name':case '!name':case '!NAME':
				array_multisort($dt_name,SORT_DESC,$dt_id,SORT_DESC,$datas);
				break;
			case 'Time':case 'time':case 'TIME':
				array_multisort($dt_time,SORT_DESC,$dt_id,SORT_ASC,$datas);
				break;
			case '!Time':case '!time':case '!TIME':
				array_multisort($dt_time,SORT_DESC,$dt_id,SORT_DESC,$datas);
				break;
			case 'Game':case 'game':case 'GAME':
				array_multisort($dt_system,SORT_ASC,$dt_time,SORT_ASC,$dt_id,SORT_DESC,$datas);
				break;
			case '!Game':case '!game':case '!GAME':
				array_multisort($dt_system,SORT_DESC,$dt_time,SORT_DESC,$dt_id,SORT_ASC,$datas);
				break;
			case 'Num':case 'num':case 'NUM':
				array_multisort($dt_members,SORT_DESC,$dt_id,SORT_ASC,$datas);
				break;
			case '!Num':case '!num':case '!NUM':
				array_multisort($dt_members,SORT_ASC,$dt_id,SORT_DESC,$datas);
				break;
		}
	}
	return $datas;
}
/* テスト用
print <<<EOF
<table class="tool_data_list">
<tr>

EOF;
	$ifcheck = function($name,$val) {
		if ($_GET['sort']===$val) {
			return '<th><a href="?sort=!'.$val.'">'.$name.'</a></th>';
		} else {
			return '<th><a href="?sort='.$val.'">'.$name.'</a></th>';
		}
	};
	
	print $ifcheck('ルーム名','name');
	print '<th>見学</th>';
	print $ifcheck('パス','pass');
	print $ifcheck('ゲーム','game');
	print $ifcheck('人数','num');
	print $ifcheck('更新','time');
	print '</tr>';

	$data = ddf_loadRooms('../saveData',$_GET['sort']);
	
	foreach($data as $row) {
		print '<tr><td><a href="DodontoF.swf?loginRoom=' . $row["id"] . '">' . $row["name"]. '</a></td><td>' . 
			($row["visit"]?'可':'不可') .
		'</td><td>' . ($row["pass"]?'あり':'なし') . '</td><td>'.
		$row["dice"] . '</td><td>'.$row["members"] . '人'.'</td><td>'.date('Y/m/d h:m:s',$row["time"]).
		"</td></tr>\n";
	}

print "</table>";
*/