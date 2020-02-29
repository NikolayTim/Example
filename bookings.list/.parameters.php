<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
if($arCurrentValues["IBLOCK_TYPE"] == "")
	$arCurrentValues["IBLOCK_TYPE"] = "1chotels";

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("3NV_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlockType,
			"DEFAULT" => "1chotels",
			"REFRESH" => "Y",
		),

		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("3NV_IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
//			"DEFAULT" => "49",
			"REFRESH" => "Y",
		),

		"EDIT_URL" => array(
			"PARENT" => "PARAMS",
			"TYPE" => "TEXT",
			"NAME" => GetMessage("3NV_IBLOCK_ADD_EDIT_URL"),
		),
	),
);
?>