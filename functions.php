<?
use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Highloadblock as HL;

function checkBrand()
{
    global $USER;

    \Bitrix\Main\Loader::includeModule('iblock');
    \Bitrix\Main\Loader::includeModule('highloadblock');

    $arBrands = array();

    $arSelectBrand = array("IBLOCK_ID", "ID", "NAME");
    $arFilterBrand = array("IBLOCK_ID" => ID_IBLOCK_BRANDS);
    $dbBrand = CIBlockElement::GetList(Array(), $arFilterBrand, false, false, $arSelectBrand);
    while($arBrand = $dbBrand->fetch())
    {
        $arBrands[$arBrand["ID"]] = $arBrand["NAME"];
    }

    $arSelectProd = array("ID", "NAME", "PROPERTY_CML2_MANUFACTURER", "PROPERTY_BRAND", "XML_ID");
    $arFilterProd = array("IBLOCK_ID" => ID_IBLOCK_CATALOG_1C, "!PROPERTY_CML2_MANUFACTURER" => false, /*"PROPERTY_BRAND" => false,*/ "ACTIVE" => "Y");
    $dbProd = CIBlockElement::GetList(Array(), $arFilterProd, false, false, $arSelectProd);
    while($arProd = $dbProd->fetch())
    {
        if($arProd["PROPERTY_CML2_MANUFACTURER_VALUE"] != $arBrands[$arProd["PROPERTY_BRAND_VALUE"]])
        {
            // Выбираем ID свойства из списка CML2_MANUFACTURER (по XML_ID)
            $idEnum = "";
            $property_enums = CIBlockPropertyEnum::GetList(Array("DEF" => "DESC", "SORT" => "ASC"),
                Array("IBLOCK_ID" => ID_IBLOCK_CATALOG_1C, "CODE" => "CML2_MANUFACTURER", "ID" => $arProd["PROPERTY_CML2_MANUFACTURER_ENUM_ID"]));
            if ($enum_fields = $property_enums->GetNext()) {
                $idEnum = $enum_fields["ID"];
                $xmlCode = $enum_fields["XML_ID"];

                if (isset($xmlCode) && strlen($xmlCode) > 0) {
                    // Ищем ID бренда в инфоблоке брендов
                    $arSelectBrand = array("ID", "NAME", "XML_ID");
                    $arFilterBrand = array("IBLOCK_ID" => ID_IBLOCK_BRANDS, "XML_ID" => $xmlCode);
                    $dbBrand = CIBlockElement::GetList(Array(), $arFilterBrand, false, false, $arSelectBrand);

                    // В инфоблоке брендов есть бренд с таким xml_id, обновляем свойство BRAND товара
                    if ($arBrand = $dbBrand->fetch()) {
                        // Обновляем в arFields свойство BRAND (ID свойства: 1159)
                        CIBlockElement::SetPropertyValuesEx($arProd["ID"], ID_IBLOCK_CATALOG_1C, array("BRAND" => $arBrand["ID"]));
                    }
                    // Нет такого бренда в инфоблоке брендов, ищем этот бренд в HL-справочнике.
                    // Если есть (а если его нет, значит не обновлен справочник брендов - отослать сообщение админу?), копируем этого бренда в инфоблок брендов и обновляем свойство товара!
                    else {
                        $hlBlock = HL\HighloadBlockTable::getById(ID_HL_MARKI)->fetch();
                        $entity = HL\HighloadBlockTable::compileEntity($hlBlock);
                        $entity_data_class = $entity->getDataClass();

                        $arFilter = array("UF_XML_ID" => $xmlCode); // ставим наш фильтр
                        $arSelect = array('*'); // выберутся все поля
                        $dbResHL = $entity_data_class::getList(array(
                            "select" => $arSelect,
                            "filter" => $arFilter
                        ));

                        // Нашли бренда в HL-справочнике: копируем бренд в инфоблок и обновляем свойство BRAND товара
                        if ($arResHL = $dbResHL->Fetch()) {
                            $el = new CIBlockElement;
                            $params = Array(
                                "max_len" => "100", // обрезает символьный код до 100 символов
                                "change_case" => "L", // буквы преобразуются к нижнему регистру
                                "replace_space" => "_", // меняем пробелы на нижнее подчеркивание
                                "replace_other" => "_", // меняем левые символы на нижнее подчеркивание
                                "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
                                "use_google" => "false", // отключаем использование google
                            );
                            $symbCode = CUtil::translit($arResHL["UF_NAME"], "ru", $params);

                            // Проверка на символьный код, если есть такой, то добавим еще что-то к символьному коду
                            $arSelectBrandSymb = array("ID", "NAME", "XML_ID");
                            $arFilterBrandSymb = array("IBLOCK_ID" => ID_IBLOCK_BRANDS, "CODE" => $symbCode);
                            $dbBrandSymb = CIBlockElement::GetList(Array(), $arFilterBrandSymb, false, false, $arSelectBrandSymb);
                            if ($arBrandSymb = $dbBrandSymb->GetNext()) {
                                $symbCode = $symbCode . rand(0, 9);
                            }

                            $arProp = array();
                            $arProp[217] = $arResHL["UF_NAME"];
                            $arProp[218][0] = array("VALUE" => array("TEXT" => $arResHL["UF_OPISANIE"], "TYPE" => "HTML"));
                            $arLoadProductArray = Array("MODIFIED_BY" => $USER->GetID(),
                                "IBLOCK_SECTION_ID" => false,
                                "IBLOCK_ID" => ID_IBLOCK_BRANDS,
                                "PROPERTY_VALUES" => $arProp,
                                "NAME" => $arResHL["UF_NAME"],
                                "CODE" => $symbCode,
                                "ACTIVE" => "Y",
                                "PREVIEW_TEXT" => $arResHL["UF_NAME"],
                                "DETAIL_TEXT" => $arResHL["UF_NAME"],
                                "XML_ID" => $xmlCode
                            );

                            if ($idBrand = $el->Add($arLoadProductArray)) {
                                // Обновляем в arFields свойство BRAND (ID свойства: 1159)
                                CIBlockElement::SetPropertyValuesEx($arProd["ID"], ID_IBLOCK_CATALOG_1C, array("BRAND" => $idBrand));
                            } else // Ошибка при добавлении элемента в инфоблок брендов! Отправить письмо или не надо?)
                            {
                                $arEventFields = array(
                                    "MESSAGE" => "При добавлении бренда: " . $arResHL["UF_NAME"] . " (ID бренда в HL-справочнике: " . $arResHL["ID"] . ") в инфоблок брендов, возникла ошибка: " . $el->LAST_ERROR,
                                    "EMAIL" => "Creatingsolo@gmail.com",
                                    "EMAIL_COPY" => "niktimakov77@yandex.ru",
                                );
                                CEvent::Send("ERROR_EXCHANGE_1C", "s1", $arEventFields);
                            }
                        }
                        // Не нашли бренда и в HL-справочнике, а ссылка на несуществующего бренда в товаре есть, значит не обновлен HL-справочник брендов!
                        // Отправляем сообщение админу: необходимо выгрузить справочник брендов на сайт, а потом полная выгрузка товаров!
                        else {
                            $arEventFields = array(
                                "MESSAGE" => "Бренд: " . $enum_fields["VALUE"] . " (xml_id: " . $xmlCode . ") ни где не найден! Необходимо обновить HL-справочник брендов!",
                                "EMAIL" => "Creatingsolo@gmail.com",
                                "EMAIL_COPY" => "niktimakov77@yandex.ru",
                            );
                            CEvent::Send("ERROR_EXCHANGE_1C", "s1", $arEventFields);
                        }
                    }
                }
            }
        }
    }
    return "checkBrand();";
}