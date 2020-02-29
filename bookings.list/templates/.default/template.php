<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

CJSCore::Init("jquery");
?>

<div class="col-12 bron_body">
	<div class="divider"></div>
	<div class="bron__meta">
		<div class="row">
			<div class="col-12">
				<h2 class="bron__number"><?=GetMessage("3NV_LIST_BOOKING")?></h2>
			</div>	
		</div>
	</div>	
	<div class="bron__filter">
		<form name="bronFilter" action="" metod="get" id="bronFilter">
			<div class="row">
				<div class="field-item">
					<label><?=GetMessage("3NV_LIST_BOOKING_NUMBER")?></label><br>
					<input type="text" name="orderNumber" />
				</div>	
				
				<div class="field-item">
					<label><?=GetMessage("3NV_LIST_BOOKING_DATE")?></label><br>
					<input type="date" name="orderDate" />
				</div>
					
				<div class="field-item">
					<label><?=GetMessage("3NV_LIST_BOOKING_FIO")?></label><br>
					<input type="text" name="orderName" />
				</div>	
				
				<div class="field-item">
					<label><?=GetMessage("3NV_LIST_BOOKING_FLIGHT")?></label><br>
					<select name="orderRoute" id="orderRoute">
						<option value=""> </option>
						<?foreach($arResult["FLIGHTS"] as $flight):?>
							<option value="<?=$flight?>"><?=$flight?></option>
						<?endforeach;?>
					</select>
				</div>	
				
				<div class="field-item">
					<label><?=GetMessage("3NV_LIST_BOOKING_STATUS")?></label><br>
					<select name="orderPaid" id="orderPaid">
						<option value=""></option>
						<option value="<?=GetMessage("3NV_RESERVATION_PAYED")?>"><?=GetMessage("3NV_RESERVATION_PAYED")?></option>
						<option value="<?=GetMessage("3NV_RESERVATION_PART_PAYED")?>"><?=GetMessage("3NV_RESERVATION_PART_PAYED")?></option>
						<option value="<?=GetMessage("3NV_RESERVATION_NOT_PAYED")?>"><?=GetMessage("3NV_RESERVATION_NOT_PAYED")?></option>
					</select>
				</div>

				<div class="field-item">
					<a class="filterSubmit" href="javascript:"></a>
					<a class="filterReset" href="javascript:"></a>
				</div>			
			</div>
		</form>
	</div>
	<div class="filter__results">
		<div class="row">
			<div class="col-12">
				<p>
					<b><?=GetMessage("3NV_LIST_BOOKING_RESULT_FILTER")?></b>
				</p><br>
			</div>
			<div class="col-12 col-md-6">
				<p>
					<?=GetMessage("3NV_LIST_BOOKING_SUM_ALL")?><span id="priceAll"><b>4 456 178<?=GetMessage("3NV_LIST_BOOKING_RUB")?></b></span>
				</p>
				<p>
					<?=GetMessage("3NV_LIST_BOOKING_COMISSION_FEE")?><span id="comissionFee"><b>90 167<?=GetMessage("3NV_LIST_BOOKING_RUB")?></b></span>
				</p>
				<p>
					<?=GetMessage("3NV_LIST_BOOKING_SUM_SELECTED")?><span id="priceSelected"><b>3 456 178<?=GetMessage("3NV_LIST_BOOKING_RUB")?></b></span>
				</p>
			</div>
			<div class="col-12 col-md-6">
				<p>
					<?=GetMessage("3NV_LIST_BOOKING_PAID_ORDERS")?><span id="paidOrders"><b>14</b><?=GetMessage("3NV_LIST_BOOKING_SUMS")?><b>5 360 110<?=GetMessage("3NV_LIST_BOOKING_RUB")?></b></span>
				</p>
				<p>
					<?=GetMessage("3NV_LIST_BOOKING_CANCEL_ORDERS")?><span id="cancelOrders"><b>5</b><?=GetMessage("3NV_LIST_BOOKING_SUMS")?><b>398 902<?=GetMessage("3NV_LIST_BOOKING_RUB")?></b></span>
				</p>
				<p>
					<?=GetMessage("3NV_LIST_BOOKING_PART_ORDERS")?><span id="partOrders"><b>2</b><?=GetMessage("3NV_LIST_BOOKING_SUMS")?><b>145 612<?=GetMessage("3NV_LIST_BOOKING_RUB")?></b></span>
				</p>
			</div>
		</div>	
	</div>
	<div class="attention">
		<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
			"AREA_FILE_RECURSIVE" => "Y",
			"AREA_FILE_SHOW" => "file",
			"AREA_FILE_SUFFIX" => "inc",
			"EDIT_TEMPLATE" => "standard.php",
			"PATH" => SITE_DIR."inc/for_agents.php"
			)	
		);?>
	</div>

<?foreach($arResult["RESERVATIONS"] as $keyRes => $arRes):?>
	<div class="result__card <?=$arRes["CLASS"]?>" data-num="<?=$arRes["GROUPCODE"]?>" data-date="<?=$arRes["CHECKDATE"]?>"
		data-fio="<?=$arRes["GUESTFULLNAME"]?>" data-status="<?=$arRes["STATUS"]?>" data-flight="<?=$arRes["ROUTE"]?>">
		<div class="items">
			<div class="item">
				<p><b>№</b></p>
				<p><?=$arRes["GROUPCODE"]?></p>
				<p><a href="#"><span class="detail"><?=GetMessage("3NV_LIST_BOOKING_VIEW")?></span></a></p>
			</div>
			<div class="item">
				<p><b><?=GetMessage("3NV_LIST_BOOKING_CREATED")?></b></p>
				<p><?=$arRes["CHECKDATE"]?></p>
			</div>
			<div class="item">
				<p><b><?=GetMessage("3NV_LIST_BOOKING_DATES")?></b></p>
				<p><?=$arRes["DATEFROM"]?></p>
				<p><?=$arRes["DATETO"]?></p>
				<p><?=$arRes["DURATION"]?><?=GetMessage("3NV_LIST_BOOKING_NIGHT")?></p>
			</div>
			<div class="item">
				<p><b><?=GetMessage("3NV_LIST_BOOKING_GUESTS")?></b></p>
				<p><?=GetMessage("3NV_LIST_BOOKING_GUESTS2")?><?=$arRes["COUNT_GUESTS"]?></p>
				<p><b><?=$arRes["GUESTFULLNAME"]?></b></p>
				<p><b><?=GetMessage("3NV_LIST_BOOKING_CABIN")?><?=$arRes["ROOM"]?></b></p>
			</div>
			<div class="item">
				<p><b><?=GetMessage("3NV_LIST_BOOKING_PRICE")?></b></p>
				<p><?=$arRes["TOTALSUM"]?><?=GetMessage("3NV_LIST_BOOKING_RUB")?></p>
				<div class="d-none d-sm-block"><a class="submit" href="#"><?=GetMessage("3NV_LIST_BOOKING_VOUCHER")?></a></div>
			</div>
			<div class="item">
				<p><b><?=GetMessage("3NV_LIST_BOOKING_REMAINS_PAY")?></b></p>
				<p><?=$arRes["BALANCEAMOUNT"]?><?=GetMessage("3NV_LIST_BOOKING_RUB")?></p>
				<div class="d-none d-sm-block"><a class="submit" href="#"><?=GetMessage("3NV_LIST_BOOKING_REPORT")?></a></div>
			</div>
			<div class="item">
				<p><b><?=GetMessage("3NV_LIST_BOOKING_STATUS2")?></b></p>
				<p class="<?($arRes["CLASS"] != "" ? $arRes["CLASS"] : 'paid')?>"><?=$arRes["STATUS"]?></p>
			</div>
			
			<div class="item d-block d-sm-none">
				<a class="submit" href="#"><?=GetMessage("3NV_LIST_BOOKING_VOUCHER")?></a>
				<a class="submit" href="#"><?=GetMessage("3NV_LIST_BOOKING_REPORT")?></a>
			</div>
		</div>
	</div>
<?endforeach;?>
</div>

<script>
let arResult = <?=CUtil::PhpToJSObject($arResult)?>;
let priceAll      = 0;
let comissionFee  = 0;	// Комиссия???
let priceSelected = 0; //$('#priceSelected').html();
let paidOrders    = 0;
let cancelOrders  = 0;	// Отмененные заказы???
let partOrders    = 0;
let cntPaid = cntPart = cntCancel = 0;

function filter(elem, num, date, fio, route, status)
{
	let resFilter = false;

	if(num.length > 0 && num != elem.data('num'))
		resFilter = true;

	if(date.length > 0 && date != elem.data('date'))
		resFilter = true;

	if(fio.length > 0 && fio != elem.data('fio'))
		resFilter = true;

	if(route.length > 0 && route != elem.data('flight'))
		resFilter = true;

	if(status.length > 0 && status != elem.data('status'))
		resFilter = true;

	return resFilter;
}

$(document).ready(function() {
	let arKeys = Object.keys(arResult.RESERVATIONS);
	for (let i = 0; i < arKeys.length; i++) 
	{
		priceAll = priceAll + parseFloat(arResult.RESERVATIONS[arKeys[i]].TOTALSUM);
	}
	$('#priceAll').html('<b>' + priceAll.toFixed(2) + ' ' + "<?=GetMessage('3NV_LIST_BOOKING_RUB')?>" + '</b>');
	$('#priceSelected').html('<b>' + priceAll.toFixed(2) + ' ' + "<?=GetMessage('3NV_LIST_BOOKING_RUB')?>" + '</b>');

	$('.filterSubmit').click(function() {
		let num   = $('input[name=orderNumber]').val();
		let date  = $('input[name=orderDate]').val();
		let fio   = $('input[name=orderName]').val();
		let route = $('#orderRoute').next('div.select__styled').text().trim();
		let status = $('#orderPaid').next('div.select__styled').text().trim();

		$('div.result__card').each(function() 
		   {
				if(filter($(this), num, date, fio, route, status))
					$(this).hide();
				else
				{
					$(this).show();
					priceSelected = priceSelected + parseFloat(arResult.RESERVATIONS[$(this).data("num")].TOTALSUM);
				}
		});
		$('#priceSelected').html('<b>' + priceSelected.toFixed(2) + ' ' + "<?=GetMessage('3NV_LIST_BOOKING_RUB')?>" + '</b>');
	});

	$('.filterReset').click(function() {
		$('div.result__card').each(function() 
		{
				$(this).show();
		});
	});
});
</script>