<?php 
	$url=htmlspecialchars($_GET['url']);
	if ($url=='') $url='http://retre.org/rssdd.xml';


	//Заголовки для загрузки торрентов
	$headers_a=array('http'=>array('method'=>"GET",'header'=>"Cookie: uid=0;pass=0;usess=0\r\n"));
	$headers=stream_context_create($headers_a);
	if ($url!='http://www.lostfilm.tv/rssdd.xml') $url=str_replace('!',"&amp;",$url);
	$source=file_get_contents($url, false, $headers);
	//Обновление ленты
	if ($url=='http://retre.org/rssdd.xml') {
		//$source=str_replace('windows-1251','utf-8',$source); 
		//$source=iconv("WINDOWS-1251", "UTF-8", $source);
		$source=str_replace('<link>http://tracktor.in/rssdownloader.php',"<link>http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?url=http://tracktor.in/rssdownloader.php",$source);
		$source=str_replace('&amp;',"!",$source);
	}

	if (strpos(strtolower($url), '/rssdownloader.php?id=')>0) {
		//Получаем имя торрента
		$filename=substr($url,strpos($url,'id=')+3,strlen($url)).'.torrent';
		//Сохраняем торрент
		file_put_contents($filename, $source);
		if (ob_get_level()) ob_end_clean();
		//Заголовки для отдаваемого торрента
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($filename));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename));
		//Отдаем файл
		readfile($filename);
		//Удаляем торрент
		unlink($_SERVER['DOCUMENT_ROOT'].'/'.$filename);
	exit; 
	} else echo $source;
?>
