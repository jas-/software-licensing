<?PHP
class logging
{
 function process($argv)
 {
  global $handles, $defined;
  $this->dbconn = $handles['db']->dbConnect($defined['dbhost'],$defined['username'],$defined['password'],$defined['dbname']);
  $data = $this->geolocation($argv);
  if(count($data)>0){
   $argv = $this->parsegeo($data,$argv);
  }
  $handles['db']->dbQuery($handles['val']->ValidateSQL($this->add($argv),$this->dbconn),$this->dbconn);
  $handles['db']->dbFixTable("logs",$this->dbconn);
  $handles['db']->dbFreeData($this->dbconn);
  $handles['db']->dbCloseConn($this->dbconn);
  return;
 }

 function content($argv,$template)
 {
  global $handles;
  $data = $this->queryall();
  $ret['content'] = $this->createjson($data);
  return $ret;
 }

 function contentlatest($time)
 {
  return $this->createjsonAJAX($this->querynew($time));
 }

 function createjson($data)
 {
  global $handles;
  if (function_exists("json_encode")) {
   $obj = json_encode($data);
  } else {
   $obj = $handles['misc']->arr2json($data);
  }
  return 'var addresses = ' . $obj . ';';
 }

 function createjsonAJAX($data)
 {
  global $handles;
  if (function_exists("json_encode")) {
   $obj = json_encode($data);
  } else {
   $obj = $handles['misc']->arr2json($data);
  }
  return $obj;
 }

 private function isproxy($argv)
 {
  if ($argv['HTTP_X_FORWARDED_FOR'] || $argv['HTTP_X_FORWARDED'] || $argv['HTTP_FORWARDED_FOR'] || $argv['HTTP_CLIENT_IP'] || $argv['HTTP_VIA'] || in_array($argv['REMOTE_PORT'], array(8080,80,6588,8000,3128,553,554)) || @fsockopen($argv['REMOTE_ADDR'], 80, $errno, $errstr, 30)) {
   return 1;
  }
  return 0;
 }

 private function add($argv)
 {
  return "INSERT INTO `logs` (`resource`,`total`,`remote-ip`,`remote-port`,`remote-host`,`client-ip`,`http-via`,`x-forwarded-for`,`x-forwarded`,`referer`,`agent`,`time`,`method`,`query-string`,`request-uri`,`language`,`city`,`state`,`country`,`latitude`,`longitude`) VALUES (\"" . md5($argv['REMOTE_ADDR']) . "\", \"1\", \"" . $argv['REMOTE_ADDR'] . "\",\"" . $argv['REMOTE_PORT'] . "\",\"" . $argv['REMOTE_HOST'] . "\",\"" . $argv['HTTP_CLIENT_IP'] . "\",\"" . $argv['HTTP_VIA'] . "\",\"" . $argv['HTTP_X_FORWARDED_FOR'] . "\",\"" . $argv['HTTP_X_FORWARDED'] . "\",\"" . $argv['HTTP_REFERER'] . "\",\"" . $argv['HTTP_USER_AGENT'] . "\",\"" . $argv['REQUEST_TIME'] . "\",\"" . $argv['REQUEST_METHOD'] . "\",\"" . $argv['QUERY_STRING'] . "\",\"" . $argv['REQUEST_URI'] . "\",\"" . $argv['HTTP_ACCEPT_LANGUAGE'] . "\",\"" . $argv['city'] . "\",\"" . $argv['state'] . "\",\"" . $argv['country'] . "\",\"" . $argv['latitude'] . "\",\"" . $argv['longitude'] . "\") ON DUPLICATE KEY UPDATE `resource` = \"" . md5($argv['REMOTE_ADDR']) . "\",`total` = `total` + 1, `remote-ip` = \"" . $argv['REMOTE_ADDR'] . "\",`remote-port` = \"" . $argv['REMOTE_PORT'] . "\",`remote-host` = \"" . $argv['REMOTE_HOST'] . "\",`client-ip` = \"" . $argv['HTTP_CLIENT_IP'] . "\",`http-via` = \"" . $argv['HTTP_VIA'] . "\",`x-forwarded-for` = \"" . $argv['HTTP_X_FORWARDED_FOR'] . "\",`x-forwarded` = \"" . $argv['HTTP_X_FORWARDED'] . "\",`referer` = \"" . $argv['HTTP_REFERER'] . "\",`agent` = \"" . $argv['HTTP_USER_AGENT'] . "\",`time` = \"" . $argv['REQUEST_TIME'] . "\",`method` = \"" . $argv['REQUEST_METHOD'] . "\",`query-string` = \"" . $argv['QUERY_STRING'] . "\",`request-uri` = \"" . $argv['REQUEST_URI'] . "\",`language` = \"" . $argv['HTTP_ACCEPT_LANGUAGE'] . "\",`city` = \"" . $argv['city'] . "\",`state` = \"" . $argv['state'] . "\",`country` = \"" . $argv['country'] . "\",`latitude` = \"" . $argv['latitude'] . "\",`longitude` = \"" . $argv['longitude'] . "\"";
 }
 
 function createrowall($argv,$template)
 {
  if(count($argv)>0) {
   foreach($argv as $key => $value) {
    $agent = $this->parseagent($value['agent']);
    $agent['agent'] = $this->assignbrowserimage(strtolower($agent['agent']),$template);
    $agent['os'] = $this->assignosimage(strtolower($agent['os']),$template);
    $row .= "<div class=\"log-content\">\r\n";
    $row .= "\t<ol>\r\n";
    $row .= "\t\t<li>" . $value['remote-ip'] . "</li>\r\n";
    $row .= "\t\t<li>" . $value['request-uri'] . "</li>\r\n";
    $row .= "\t\t<li><a href=\"#\" class=\"show\" rel=\"" . $value['resource'] . "\"><img src=\"templates/development/images/icons/icon-info.png\" title=\"Show visitor details?\" alt=\"Show visitor details?\"></a></li>\r\n";
    $row .= "\t</ol>\r\n";
    $row .= "</div>";
    $row .= "<div class=\"log-content\" id=\"" . $value['resource'] . "\" style=\"display: none\">";
    $row .= "\t<ol>\r\n";
    $row .= "\t\t<li><div class=\"flag-48 " . strtolower($agent['country']) . "\"></div></li>\r\n";
    $row .= "\t\t<li><img src=\"" . $agent['agent'] . "\"></li>\r\n";
    $row .= "\t\t<li><img src=\"" . $agent['os'] . "\"></li>\r\n";
    $row .= "\t\t<li>" . $value['time'] . "</li>\r\n";
    $row .= "\t\t<li>" . $value['query-string'] . "</li>\r\n";
    $row .= "\t\t<li>" . $value['city'] . "</li>\r\n";
    $row .= "\t\t<li>" . $value['state'] . "</li>\r\n";
    $row .= "\t\t<li>" . $value['latitude'] . "</li>\r\n";
    $row .= "\t\t<li>" . $value['longitude'] . "</li>\r\n";
    $row .= "\t</ol>\r\n";
    $row .= "</div>";
   }
   return $row;
  }
  return "Logging data is unavailable";
 }
 
 function createrowsingle($argv)
 {
  $row .= "<tr>";
  $row .= "<td>" . $argv['remote-ip'] . "</td>";
  $row .= "<td>" . $argv['referer'] . "</td>";
  $row .= "<td>" . $argv['agent'] . "</td>";
  $row .= "<td>" . $argv['time'] . "</td>";
  $row .= "<td>" . $argv['method'] . "</td>";
  $row .= "<td>" . $argv['query-string'] . "</td>";
  $row .= "<td>" . $argv['request-uri'] . "</td>";
  $row .= "<td>" . $argv['language'] . "</td>";
  $row .= "<td>" . $argv['city'] . "</td>";
  $row .= "<td>" . $argv['state'] . "</td>";
  $row .= "<td>" . $argv['country'] . "</td>";
  $row .= "<td>" . $argv['latitude'] . "</td>";
  $row .= "<td>" . $argv['longitude'] . "</td>";
  $row .= "</tr>";
  return $row;
 }
 
 private function queryall()
 {
  global $handles, $defined;
  $this->dbconn = $handles['db']->dbConnect($defined['dbhost'],$defined['username'],$defined['password'],$defined['dbname']);
  $sql = "SELECT * FROM `logs` LIMIT 15";
  if(($value = $handles['db']->dbQuery($handles['val']->ValidateSQL($sql,$this->dbconn),$this->dbconn))!==-1) {
   if($handles['db']->dbNumRows($value)>0) {
    $data = $handles['db']->dbArrayResultsAssoc($value);
   }
  }
  $handles['db']->dbFixTable("logs",$this->dbconn);
  $handles['db']->dbFreeData($this->dbconn);
  $handles['db']->dbCloseConn($this->dbconn);
  return $data;
 }
 
 private function querynew($time)
 {
  global $handles, $defined;
  $this->dbconn = $handles['db']->dbConnect($defined['dbhost'],$defined['username'],$defined['password'],$defined['dbname']);
  $time = $time - 5000;
  //$sql = "SELECT * FROM `logs` WHERE `time` <> \"" . $time . "\" LIMIT 10";
  $sql = "SELECT * FROM `logs` ORDER BY `id` DESC LIMIT 2";
  return $handles['db']->dbArrayResults($handles['db']->dbQuery($handles['val']->ValidateSQL($sql,$this->dbconn),$this->dbconn));
  $handles['db']->dbFixTable("logs",$this->dbconn);
  $handles['db']->dbFreeData($this->dbconn);
  $handles['db']->dbCloseConn($this->dbconn);
  return;
 }
 
 private function geolocation($argv)
 {
  return unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$argv['REMOTE_ADDR']));
 }

 private function parsegeo($data,$argv)
 {
  if(count($data)>0) {
   $argv['city'] = (!empty($data['geoplugin_city'])) ? $data['geoplugin_city'] : "Undefined";
   $argv['state'] = (!empty($data['geoplugin_region'])) ? $data['geoplugin_region'] : "Undefined";
   $argv['country'] = (!empty($data['geoplugin_countryName'])) ? $data['geoplugin_countryName'] : "Undefined";
   $argv['latitude'] = (!empty($data['geoplugin_latitude'])) ? $data['geoplugin_latitude'] : "Undefined";
   $argv['longitude'] = (!empty($data['geoplugin_longitude'])) ? $data['geoplugin_longitude'] : "Undefined";
  }
  return $argv;
 }

 private function parseagent($agent)
 {
  if(!empty($agent)) {
   $y = preg_split('/ /', $agent, -1, PREG_SPLIT_OFFSET_CAPTURE);
   $x['agent'] = preg_split('/\//',$y[0][0]);
   $x['country'] = preg_split('/-/',$y[5][0]);
   $x['os'] = preg_split('/\//',$y[8][0]);
   $z['agent'] = $x['agent'][0];
   $z['country'] = substr($x['country'][1],0,-1);
   $z['os'] = $x['os'][0];
  } else {
   $z = "Agent string empty";
  }
  return $z;
 }
 
 private function assignbrowserimage($argv,$template)
 {
  if(!empty($argv)){
   switch($argv){
    case 'firefox':
     return $template . "/images/icons/icon-browser-firefox.png";
    case 'camino':
     return $template . "/images/icons/icon-browser-camino.png";
    case 'internet explorer':
     return $template . "/images/icons/icon-browser-ie.png";
    case 'chrome':
     return $template . "/images/icons/icon-browser-chrome-png";
    case 'safari':
     return $template . "/images/icons/icon-browser-safari.png";
    default:
     return $template . "/images/icons/icon-browser-firefox.png";
   }
  } else {
   return $template . "/images/icons/icon-browser-firefox.png";
  }
 }
 
 private function assignosimage($argv,$template)
 {
  if(!empty($argv)){
   switch($argv){
    case 'apple':
     return $template . "/images/icons/icon-os-apple.png";
    case 'windows':
     return $template . "/images/icons/icon-os-windows.png";
    case 'vista':
     return $template . "/images/icons/icon-os-windows-vista.png";
    case 'windows 7':
     return $template . "/images/icons/icon-os-windows-7-png";
    case 'linux':
     return $template . "/images/icons/icon-os-linux.png";
    case 'ubuntu':
     return $template . "/images/icons/icon-os-ubuntu.png";
    default:
     return $template . "/images/icons/icon-os-windows.png";
   }
  } else {
   return $template . "/images/icons/icon-os-windows.png";
  }
 }
}
?>