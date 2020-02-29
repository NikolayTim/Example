<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

ini_set("soap.wsdl_cache_enabled", "0");
ini_set('default_socket_timeout', 10000);

$curDate = new DateTime();
$curPage = $APPLICATION->GetCurPage(false);
$citiesIBLOCK_ID = 48;

/* Вытащить из опций модуля бронирования */
$testWebService = "http://85.172.6.53:8888/apitest/ws/1CHotelReservationInterfaces.1cws?wsdl";
$externalSystemCode = "1CBITRIX";
$languageCode = strtoupper(LANGUAGE_ID);
$hotel = "1594";
/*****************************************/

if (CModule::IncludeModule("iblock"))
{
	if(intval($arParams["IBLOCK_ID"]) <= 0)
		$arParams["IBLOCK_ID"] = 49;

	if ($USER->IsAuthorized())
	{

		$client = new SoapClient($testWebService);

		$dbUser = CUser::GetByID($USER->GetID());
		if($arUser = $dbUser->GetNext())
		{
			$login = "info@mosturflot.ru"; //"ateslenko@inbox.ru"; //"planeta-tours@mail.ru"; //$arUser["LOGIN"];
			$email = "info@mosturflot.ru"; //"ateslenko@inbox.ru"; //"planeta-tours@mail.ru"; //$arUser["EMAIL"];
			$phone = ""; //$arUser["PERSONAL_PHONE"];
		}

		$arGroupListParams = Array("EMail" => $email, "Phone" => $phone);

		// Выбираем коды броней (если были) по текущему пользователю
		try
		{
			$arGroupListRes = $client->GetGroupList($arGroupListParams);
			$arGroupList = $arGroupListRes->return->GuestGroupCode;
			if(is_array($arGroupList))
				$arResult["GROUP_LIST"] = $arGroupList;
			else
				$arResult["GROUP_LIST"][] = $arGroupList;

		}
		catch (Exception $e)
		{
			printExceptionWebService("GetGroupList", $arGroupListParams, $e, $curPage, "Log");
		}

		if(count($arGroupList) > 0)
		{
			// Есть брони, вытаскиваем все наименования города (прибытия и отправления)
			$arResult["CITIES"] = array();
			$arSelectCities = array("ID", "NAME");
			$arFilterCities = array("IBLOCK_ID" => $citiesIBLOCK_ID, "ACTIVE" => "Y");
			$dbResCities = CIBlockElement::GetList(Array(), $arFilterCities, false, false, $arSelectCities);
			while($arResCities = $dbResCities->GetNext())
				$arResult["CITIES"][$arResCities["ID"]] = $arResCities["NAME"];

			// Есть брони, вытаскиваем все города и даты прибытия/отправления
			$arResult["ROUTES"] = array();
			$arSelectCruises = array("ID", "NAME", "PROPERTY_CRUISE_DEPART_CITY", "PROPERTY_CRUISE_ARRIVE_CITY",
									"PROPERTY_CRUISE_FROM", "PROPERTY_CRUISE_TO");
			$arFilterCruises = array("IBLOCK_ID" => $arParams["IBLOCK_ID"]);
			$dbResCruises = CIBlockElement::GetList(Array(), $arFilterCruises, false, false, $arSelectCruises);
			while($arResCruises = $dbResCruises->GetNext())
			{
				$tmpDate = substr($arResCruises["PROPERTY_CRUISE_FROM_VALUE"], 0, 10); // анализируем сначала отправление
				if(!array_key_exists($tmpDate, $arResult["ROUTES"]))
				{
					$arResult["ROUTES"][$tmpDate] = $arResult["CITIES"][$arResCruises["PROPERTY_CRUISE_DEPART_CITY_VALUE"]];
				}
				$tmpDate = substr($arResCruises["PROPERTY_CRUISE_TO_VALUE"], 0, 10); // анализируем теперь прибытие
				if(!array_key_exists($tmpDate, $arResult["ROUTES"]))
				{
					$arResult["ROUTES"][$tmpDate] = $arResult["CITIES"][$arResCruises["PROPERTY_CRUISE_ARRIVE_CITY_VALUE"]];
				}
			}
		}

		// Выбираем брони по полученным ранее кодам броней
		$arResult["RESERVATIONS"] = Array();
		$arGroupReservationDetailsParams = array(
													"EMail" => $email, 
													"Phone" => $phone,
													"Login" => $login,
													"Hotel" => $hotel,
													"ExternalSystemCode" => $externalSystemCode, 
													"LanguageCode" => $languageCode
												);

		foreach($arResult["GROUP_LIST"] as $curGroup)
		{
			$arGroupReservationDetailsParams["GuestGroupCode"] = $curGroup;

			try
			{
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

			if ($arGroupReservationDetails["ErrorDescription"] == "") // не аннулированная и не ошибочная бронь
			{
				$arExternalReservationStatusRow = array();
				if(is_object($arGroupReservationDetails["ExternalReservationStatusRow"]))
					$arExternalReservationStatusRow[] = (array) $arGroupReservationDetails["ExternalReservationStatusRow"];
				else
				{
					foreach($arGroupReservationDetails["ExternalReservationStatusRow"] as $obj)
						$arExternalReservationStatusRow[] = (array) $obj;
				}
		
				$arBirthDates = array();
				foreach($arExternalReservationStatusRow as $arReserv)
				{
					$curBirthDate   = new DateTime(substr($arReserv["GuestBirthDate"], 0, 10));
					$arBirthDates[] = array("BIRTH_DATE" => substr($arReserv["GuestBirthDate"], 0, 10), 
											"AGE" => $curBirthDate->diff($curDate)->y);
					$room           = $arReserv["Room"];		// Комната
					$roomType 		= $arReserv["RoomTypeDescription"];	// Тип комнаты (и палуба)
					$accommodation  = $arReserv["AccommodationTypeDescription"]; // Вид размещения
					$roomRated      = $arReserv["RoomRateDescription"]; // Наименование тарифа
					$duration       = $arReserv["Duration"]; // Длительность
				}

				// Считаем взрослых и детей
				$adults  = $child6 = $child14 = 0;
				foreach($arBirthDates as $human)
				{
					if(intval($human["AGE"]) > 14)
						$adults++;
					elseif(intval($human["AGE"]) > 6)
						$child14++;
					else
						$child6++;
				}

				$checkDate = new DateTime(substr($arGroupReservationDetails["CheckDate"], 0, 10));
				$dateFrom  = new DateTime(substr($arGroupReservationDetails["DateFrom"], 0, 10));
				$dateTo    = new DateTime(substr($arGroupReservationDetails["DateTo"], 0, 10));

				if(array_key_exists($dateFrom->format('d.m.Y'), $arResult["ROUTES"]))
				{
					if(array_key_exists($dateTo->format('d.m.Y'), $arResult["ROUTES"]))
						$route = $arResult["ROUTES"][$dateFrom->format('d.m.Y')]." - ".$arResult["ROUTES"][$dateTo->format('d.m.Y')];
					else
						$route = "Город прибытия не определен по дате прибытия!!!";
				}
				else
					$route = "Город отправления не определен по дате отправления!!!";

				$class = "";
				if(intval($arGroupReservationDetails["BalanceAmount"]) == 0)
					$status = GetMessage("3NV_RESERVATION_PAYED");
				elseif(intval($arGroupReservationDetails["BalanceAmount"]) == intval($arGroupReservationDetails["TotalSum"]))
				{
					$status = GetMessage("3NV_RESERVATION_NOT_PAYED");
					$class = "none-paid";
				}
				else
				{
					$status = GetMessage("3NV_RESERVATION_PART_PAYED");
					$class = "part-paid";
				}

				$arResult["RESERVATIONS"][$curGroup] = array(
					"GROUPCODE"           => $curGroup, 								// номер заказа
					"CHECKDATE"           => $checkDate->format('d.m.Y'), 	// дата создания заказа
					"DATEFROM"            => $dateFrom->format('d.m.Y'),  	// начало круиза
					"DATETO"              => $dateTo->format('d.m.Y'),    	// окончание круиза
					"ROUTE"				  => $route, // Маршрут
					"DURATION"            => $duration,	// длительность
					"COUNT_GUESTS"        => count($arExternalReservationStatusRow),	// количество пассажиров
					"GUESTFULLNAME"       => $arGroupReservationDetails["GuestFullName"], 	// Плательщик
					"ROOM"                => $room,		// Комната
					"ROOMTYPEDESCRIPTION" => $roomType,	// Тип комнаты (и палуба)
					"ACCOMMODATIONTYPEDESCRIPTION" => $accommodation, // Вид размещения
					"ROOMRATEDESCRIPTION" => $roomRated, // Наименование тарифа
					"TOTALSUM"			  => $arGroupReservationDetails["TotalSum"],		// Сумма всего (за каюту)
					"BALANCEAMOUNT"       => $arGroupReservationDetails["BalanceAmount"], // Сумма задолженности по оплате (если = 0, то оплачено полностью)
					"COUNT_ADULTS"		  => $adults, // количество взрослых
					"COUNT_CHILDREN_6"    => $child6, // количество детей до 6 лет
					"COUNT_CHILDREN_14"   => $child14, // количество детей до 14 лет
					"AGES"                => $arBirthDates,
					"STATUS"			  => $status,
					"CLASS"				  => $class
															);
			}
			elseif (strpos($arGroupReservationDetails["ErrorDescription"], "Бронь аннулирована!") !== false) // аннулированные брони - нужны или нет????
			{
				$arResult["RESERVATION_CANCELED"][$curGroup] = array("Бронь № ".$curGroup." ".$arGroupReservationDetails["ErrorDescription"]);
			}
		}

		$arResult["FLIGHTS"] = array();
		foreach($arResult["RESERVATIONS"] as $arReserv)
		{
			if(!array_key_exists($arReserv["ROUTE"], $arResult["FLIGHTS"]))
				$arResult["FLIGHTS"][$arReserv["ROUTE"]] = $arReserv["ROUTE"];
		}
		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}
?>