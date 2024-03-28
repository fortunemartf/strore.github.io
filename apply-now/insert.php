<?php

session_start();
$con = mysqli_connect("localhost", "moneyres", "Amit@2020#", "moneyres_fortu");
if($con->connect_error){
	echo "Database Connection Failed:";
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
date_default_timezone_set("Asia/Kolkata");


if($_SERVER["REQUEST_METHOD"] == "POST"){
	$txtname = $_POST['txtname'];
	$email = $_POST['email'];
	$mobile = $_POST['mobile'];
	//$mobile1 = "91".$mobile;
	$city = $_POST['city'];
	$state = $_POST['state'];
	$business = $_POST['business'];
	$shopava = $_POST['shopava'];
	$property = $_POST['property'];
	$invt = $_POST['invt'];
	$dist = $_POST['dist'];
	$pin = $_POST['pin'];
	$prefix = "FOR20200MART92R-";
	
	$checkexists = $con->query("SELECT * FROM applications WHERE email = '$email ' OR mobile = '$mobile'");
	if($checkexists->num_rows > 0){
		$_SESSION['error'] = "Email Address/mobile Number Already Exists!";
		header("location: index.php");
		exit();
	}else{
		$getappid = $con->query("SELECT * FROM applications ORDER BY id DESC;");
		if($getappid->num_rows == 0){
			$count = "0001";
			$app_id = $prefix.$count;
		}else{
			$lid = $getappid->fetch_assoc();
			$lastid = $lid['app_id'];
			$lastvalue = explode("-",$lastid);
			$nextvalue = sprintf("%'04d", $lastvalue[1]+1);
			$app_id = $prefix.$nextvalue;
		}
		$sql = "INSERT INTO applications (app_id, txtname, email, mobile, city, state,  business, shopava, property, invt, dist, pin, created_on) VALUES ('$app_id', '$txtname', '$email', '$mobile', '$city', '$state',  '$business', '$shopava', '$property', '$invt', '$dist', '$pin', NOW())";
		$mail = new PHPMailer(true);
			
			//Server settings
			$mail->SMTPDebug = 0;                                       // Enable verbose debug output
			$mail->isSMTP();                                            // Set mailer to use SMTP
			$mail->Host       = 'mail.moneyresources.co.in';  // Specify main and backup SMTP servers
			$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		    $mail->Username   = 'info@moneyresources.co.in';                     // SMTP username
			$mail->Password   = 'Amit@2020#';                      // SMTP password
			$mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
			$mail->Port       = 465;                                    // TCP port to connect to

			//Recipients
			$mail->setFrom('info@moneyresources.co.in');					########Put the Email same as Above
			$mail->addAddress('adminpanel@kusumyojanakisan.com');     						// Add a recipient
			

			// Content
			$mail->isHTML(true);                                  		// Set email format to HTML
			$mail->Subject = ' Application Recived';
			$mail->Body    = '<p>Thanks & Regards<p><p>Team Kusum Yojona</p>';
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			if($mail->send()){
				if($con->query($sql) == TRUE){
					
					$_SESSION['success'] = "Application Submitted Successfully! Your Application Id is: $app_id";
					header("location: index.php");
					exit();
				}else{
					$_SESSION['error'] = "Somethign went Wrong! Contact Admin";
					header("location: index.php");
					exit();
				}
			}else{
				$_SESSION['error'] = 'Please Enter correct email Id or contact Admin';
				header("location: index.php");
				exit();
			}
	}
}else{
	$_SESSION['error'] = "Forbidden Access";
	header("location: index.php");
	exit();
}
?>