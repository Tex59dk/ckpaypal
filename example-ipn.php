 <?php 
/**
 * Plugin CKPaypal
 * Copyright (c) <2014> <Jacques Malgrange contacter@boiteasite.fr>
 * License MIT
 */
//
// EXAMPLE IPN FILE - THIS FILE MUST BE TAILORED TO YOUR NEEDS
//
	$hostPaypal = 'www.paypal.com'; // ADD sandbox if you need
	$urlPaypal = 'https://www.paypal.com'; // ADD sandbox if you need
	$req = 'cmd=_notify-validate';
	foreach($_POST as $k=>$v){$req .= "&$k=$v";}
	$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n";
	$header .= "Host: ".$hostPaypal."\r\n";
	$header .= "Connection: close\r\n\r\n";
	if(function_exists('openssl_open')) {$fp=fsockopen('ssl://'.$hostPaypal,443,$errno,$errstr,30); $type="_SSL";}
	else {$fp=fsockopen($hostPaypal,80,$errno,$errstr,30); $type="_HTTP";}
	if(!$fp)
		{ //error connecting to paypal
		}
	else
		{ //successful connection
		$written=fwrite($fp,$header.$req);
		if ($written)
			{
			$res=stream_get_contents($fp);
			fclose($fp);
			if(strpos($res, "VERIFIED")!==false)
				{ //insert order into database
				if($_POST['payment_status']=="Completed")
					{
					// check txn_id in my database : new payment ?
					// see function VerifIXNID() in the bottom of this script
					if(VerifIXNID($_POST['txn_id'])==0)
						{ // check receiver_email : is it me ?
						if($myemail==$_POST['receiver_email'])
							{ // OK
							}
						else
							{ // bad email
							}
						}
					else
						{ // already payed
						}
					}
				else
					{ // Payment statut not good
					}
				}
			else if(strpos($res, "INVALID")!==false)
				{ //insert into DB in a table for bad payments for you to process later
				}
			}
		}
//
function VerifIXNID($txn_id)
	{ // check if txn_id already in database
	// write a test in your database (MySQL, XML, JSON, TXT ...)
	return 0; // 0:OK - 1:already in base
	}
?>
