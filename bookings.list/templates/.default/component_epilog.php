<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/*
if(array_key_exists("GROUP_CODE", $_REQUEST) && intval($_REQUEST["GROUP_CODE"]) > 0)
{
	$APPLICATION->RestartBuffer();

	$testWebService = "http://85.172.6.53:8888/apitest/ws/1CHotelReservationInterfaces.1cws?wsdl";
	$externalSystemCode = "1CBITRIX";
	$languageCode = strtoupper(LANGUAGE_ID);
	$hotel = "1594";

	$login = "info@mosturflot.ru"; //"ateslenko@inbox.ru"; //"planeta-tours@mail.ru"; //$arUser["LOGIN"];
	$email = "info@mosturflot.ru"; //"ateslenko@inbox.ru"; //"planeta-tours@mail.ru"; //$arUser["EMAIL"];
	$phone = ""; //$arUser["PERSONAL_PHONE"];

	$arGroupReservationDetailsParams = array(
												"EMail" => $email, 
												"Phone" => $phone,
												"Login" => $login,
												"Hotel" => $hotel,
												"ExternalSystemCode" => $externalSystemCode, 
												"LanguageCode" => $languageCode,
												"GuestGroupCode" => $_REQUEST["GROUP_CODE"]
											);

	try
	{
		$client = new SoapClient($testWebService);

		$arGroupReservationDetailsRes = $client->GetGroupReservationDetails($arGroupReservationDetailsParams);
		$arGroupReservationDetails = (array) $arGroupReservationDetailsRes->return;

		if( $arGroupReservationDetails["ErrorDescription"] != "" && 
			strpos($arGroupReservationDetails["ErrorDescription"], GetMessage("3NV_RESERVATION_CANCELED")) === false)
				throw new Exception($arGroupReservationDetails["ErrorDescription"]);
	}
	catch (Exception $e)
	{
		printExceptionWebService("GetGroupReservationDetails", $arGroupReservationDetailsParams, $e, $curPage, "Log");
	}

	echo $_REQUEST["GROUP_CODE"];
	print_r($arGroupReservationDetails);

	die();
}
*/