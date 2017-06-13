<?php
include( 'SimPay.php' );

$simPay = new SimPay();

$smsObject = $simPay -> parseSMS( $_POST );

if( $simPay -> isError() ){
	//Wystapił błąd podczas przetwarzania sms'a

	exit( $simPay -> getErrorText() );
}

//Generowanie kodu
$smsCode = $simPay -> generateCode();

//Zapisywanie wygenerowanego kodu w bazie
$stmt = $pdoObject -> prepare('INSERT INTO `generate_codes` ( `code` , `amount` ) VALUES ( :code , :amount )' );

$stmt -> bindValue( ':code' , $smsCode, PDO::PARAM_STR );
$stmt -> bindValue( ':amount' , $smsObject -> getValue(), PDO::PARAM_STR );
	 
$stmt -> execute();

$return = $simPay -> generateXml( 'Twoj kod doladowania to ' . $smsCode );

echo $return;
?>