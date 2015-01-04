<?php 
$url=htmlspecialchars($_GET['url']);

if ($url=='') $url='https://www.lostfilm.tv/rssdd.xml';

//Заголовки для загрузки торрентов
$headers_a=array('http'=>array('method'=>"GET",'header'=>"Cookie: uid=0;pass=0;usess=0\r\n"));
$headers=stream_context_create($headers_a);

if ($url!='https://www.lostfilm.tv/rssdd.xml') $url=str_replace('!',"&amp;",$url);
$source=file_get_contents($url, false, $headers);

//Обновление ленты
if ($url=='https://www.lostfilm.tv/rssdd.xml') {
$source=str_replace('windows-1251','utf-8',$source); 
$source=iconv("WINDOWS-1251", "UTF-8", $source);
$source=str_replace('<link>http://lostfilm.tv/download.php',"<link>http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?url=http://lostfilm.tv/download.php",$source);
$source=str_replace('&amp;',"!",$source);}

if (strpos(strtolower($url), '.torrent')>0) {

//Получаем имя торрента
$filename=substr($url,strpos($url,'&amp;')+5,strlen($url));

//Сохраняем торрент
file_put_contents($filename, $source);

//Заголовки для отдаваемого торрента
header('Content-Description: File Transfer');
header('Content-type: application/x-bittorrent');
header('Content-Disposition: attachment; filename='.basename($filename));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: '.filesize($filename));
ob_clean();
flush();
readfile($filename);
//Удаляем торрент
unlink($_SERVER['DOCUMENT_ROOT'].'/'.$filename);
exit;} 
else echo $source;
?>