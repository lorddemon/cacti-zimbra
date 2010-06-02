<?php
require('zimbracactistats-soap-class.php');


/*
# 
# Soap Service Configuration used for -c handler on command line
# 
*/

$Cfg['zwm.maronda.com'] = array('hostname' => 'zwm.maronda.com', 
																'username' => 'bleto@maronda.com', 
																'pwd' => 'otel2285');



/*
############################################################################
#  Do not edit anything below this line
############################################################################
*/
$Args = getopt("c:f:");

if($Args['c'] == '' || $Args['f'] == ''){
	printusage();
	exit;
}
if(!is_array($Cfg[$Args['c']])){
	printusage();
	print "Error:\n";
	print "Invalid Configuration string: ".$Args['c']."\n";
	print "Make sure you have an entry in this script for \$Cfg['<name>'] \n\n";
	exit;
}else{
	$SetCfgArr = $Cfg[$Args['c']];
}

switch($Args['f']){
	case 'user':
		$SetFunction = 'users';
		break;
  case 'mtastats':
    $SetFunction = 'mta';
    break;
	default:
		printusage();
		print "Error:\n";
		print "Invalid lookup function string: ".$Args['f']."\n";
		exit;
}

$ZStats = new ZimbraSoapStats();
$ZStats->Auth($SetCfgArr['hostname'], $SetCfgArr['username'], $SetCfgArr['pwd']); 

if($SetFunction == 'users'){
	$total_users = $ZStats->GetUserTotal();
	$active_users = $ZStats->GetUserTotalActive();	
	print "totalusers:".$total_users." activeusers:".$active_users."\n";
}
elseif($SetFunction == 'mta'){
	print "not avalible yet\n";
}


function printusage(){
	
	print "\n\n";
	print "Zimbra Cacti Stats Soap Interface Script\n";
	print "Version .01 Beta\n";	
	print "By: Ben Leto <ben@otelconsulting.com>\n";
	print "\n\n";	
	print "Usage: php zimbra-stats-soap.php -c <config name> -f <lookup function> \n\n";	
	print "-c : server configuration : this is set in the top of zimbra-stats-soap.php file\n";
	print "-f : lookup function : user one of the following\n";
	print "   user : this will retrive total users and total active users\n";
	print "   mtastats : this will retrive mta stats (total messages / virus / spam) counts\n";
	print "\n\n";	
}

?>