<?php
include( 'SimPay.php' );

$simPay = new SimPay();

//Ustawianie klucza api dostepnego w panelu usługi
$simPay -> setApiKey( '<klucz api>' );

//Parsowanie danych otrzymanych POST do danych zawartych w klasie i możliwych do pobrania poprzez metody
$smsObject = $simPay -> parseSMS( $_POST );

//Sprawdzenie czy przy parsowaniu wystąpił błąd
if( $simPay -> isError() ){
	//Wystapił błąd podczas przetwarzania sms'a , wyświetlenie typu błędu
	exit( $simPay -> getErrorText() );
}

//Pobranie różnych części SMS'a np. SIM.TEST zwróci nam tablicę [ 'SIM' , 'TEST' ] lub SIM.TEST.TRESC zwróci nam [ 'SIM' , 'TEST' , 'TRESC' ] itp. itd.
$arrayPieces = $simPay -> getPieces();

//Generowanie kodu do zwrotu
$smsCode = $simPay -> generateCode();

//Zapisywanie wygenerowanego kodu w bazie
$stmt = $pdoObject -> prepare('INSERT INTO `generate_codes` ( `code` , `amount` ) VALUES ( :code , :amount )' );

$stmt -> bindValue( ':code' , $smsCode, PDO::PARAM_STR );
$stmt -> bindValue( ':amount' , $smsObject -> getValue(), PDO::PARAM_STR );
	 
$stmt -> execute();

//Generowanie XML'a do wysyłki ( nie można używać polskich znaków treści sms'a )
$return = $simPay -> generateXml( 'Twoj kod doladowania to ' . $smsCode );

//Odpowiedź do serwerów simpay z wygenerowanym XML'em
echo $return;
?>
