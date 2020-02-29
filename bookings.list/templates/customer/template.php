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
?>

<?$flagPastBooking = false;?>
<?$curDate = new DateTime();?>
<div class="col-12 bron_body customer__dashboard">
	<div class="divider"></div>
	<div class="bron__meta">
		<div class="row">
			<div class="col-12">
				<h2 class="bron__number"><?=GetMessage("3NV_LIST_BOOKING")?></h2>
			</div>	
		</div>
	</div>	

<?foreach($arResult["RESERVATIONS"] as $keyRes => $arRes):?>
	<?
	$arDate = split("[.]", $arRes["DATEFROM"]);
	$dateCruise = new DateTime($arDate[2]."-".$arDate[1]."-".$arDate[0]);
	if($dateCruise < $curDate)
		$flagPastBooking = true;
	else
	{?>
		<div class="bron__summary">
			<div class="row">
				<div class="col-md-5">
					<p><?=GetMessage("3NV_LIST_BOOKING_ROUTE")?><b><?=$arRes["ROUTE"]?></b></p>
					<p><?=GetMessage("3NV_LIST_BOOKING_DATE")?><b><?=$arRes["DATEFROM"]?> - <?=$arRes["DATEFROM"]?> (<?=$arRes["DURATION"]?><?=GetMessage("3NV_LIST_BOOKING_NIGHT")?>)</b></p>
				</div>
				<div class="col-md-7">
					<?$guests = GetMessage("3NV_LIST_BOOKING_ADULTS")."<b>(".$arRes["COUNT_ADULTS"].")</b>";?>
					<?if(intval($arRes["COUNT_CHILDREN_6"]) > 0)
						$guests = $guests.GetMessage("3NV_LIST_BOOKING_CHILDREN_6")."<b>(".$arRes["COUNT_CHILDREN_6"].")</b>";
					if(intval($arRes["COUNT_CHILDREN_14"]) > 0)
						$guests = $guests.GetMessage("3NV_LIST_BOOKING_CHILDREN_14")."<b>(".$arRes["COUNT_CHILDREN_14"].")</b>";
					?>
					<p><?=GetMessage("3NV_LIST_BOOKING_GUESTS")?><?=$guests?></p>
					<?$deck = substr($arRes["ROOMTYPEDESCRIPTION"], strpos($arRes["ROOMTYPEDESCRIPTION"], "(")+1, 9);
					$deck = substr($deck, 0, strlen($deck) - 1);?>
					<p><?=GetMessage("3NV_LIST_BOOKING_RESIDENCE")?><b><?=GetMessage("3NV_LIST_BOOKING_CABIN")?><?=$arRes["ROOM"]?>, <?=$arRes["ACCOMMODATIONTYPEDESCRIPTION"]?>, <?=$deck?></b></p>
				</div>
				<br><br>
				<div class="col-md-5">
					<p class="price"><?=GetMessage("3NV_LIST_BOOKING_PRICE")?><b><?=$arRes["TOTALSUM"]?> руб.</b></p>
				</div>
				<div class="col-md-7">
					<p class="status">
						<?if(intval($arRes["BALANCEAMOUNT"]) == 0):?>
							<span class="paid"><?=GetMessage("3NV_LIST_BOOKING_PAYED")?></span> 
						<?else:?>
							<span class="pay"><?=GetMessage("3NV_LIST_BOOKING_PAY")?></span> 
						<?endif;?>
						<span class="cancel"><?=GetMessage("3NV_LIST_BOOKING_CANCEL")?></span> 
						<span class="detail"><?=GetMessage("3NV_LIST_BOOKING_VIEW")?></span>
					</p>
				</div>
			</div>
		</div>
	<?}?>
<?endforeach;?>

<?if($flagPastBooking):?>
	<div class="bron__meta old">
		<div class="row">
			<div class="col-12">
				<h2 class="bron__number"><?=GetMessage("3NV_LIST_BOOKING_PAST")?></h2>
			</div>	
		</div>
	</div>

	<?foreach($arResult["RESERVATIONS"] as $keyRes => $arRes):?>
		<?
		$arDate = split("[.]", $arRes["DATEFROM"]);
		$dateCruise = new DateTime($arDate[2]."-".$arDate[1]."-".$arDate[0]);
		if($dateCruise < $curDate)
		{?>
			<div class="bron__summary old">
				<div class="row">
					<div class="col-md-5">
						<p><?=GetMessage("3NV_LIST_BOOKING_ROUTE")?><b><?=$arRes["ROUTE"]?></b></p>
						<p><?=GetMessage("3NV_LIST_BOOKING_DATE")?><b><?=$arRes["DATEFROM"]?> - <?=$arRes["DATEFROM"]?> (<?=$arRes["DURATION"]?><?=GetMessage("3NV_LIST_BOOKING_NIGHT")?>)</b></p>
					</div>
					<div class="col-md-7">
						<?$guests = GetMessage("3NV_LIST_BOOKING_ADULTS")."<b>(".$arRes["COUNT_ADULTS"].")</b>";?>
						<?if(intval($arRes["COUNT_CHILDREN_6"]) > 0)
							$guests = $guests.GetMessage("3NV_LIST_BOOKING_CHILDREN_6")."<b>(".$arRes["COUNT_CHILDREN_6"].")</b>";
						if(intval($arRes["COUNT_CHILDREN_14"]) > 0)
							$guests = $guests.GetMessage("3NV_LIST_BOOKING_CHILDREN_14")."<b>(".$arRes["COUNT_CHILDREN_14"].")</b>";
						?>
						<p><?=GetMessage("3NV_LIST_BOOKING_GUESTS")?><?=$guests?></p>
						<?$deck = substr($arRes["ROOMTYPEDESCRIPTION"], strpos($arRes["ROOMTYPEDESCRIPTION"], "(")+1, 9);
						$deck = substr($deck, 0, strlen($deck) - 1);?>
						<p><?=GetMessage("3NV_LIST_BOOKING_RESIDENCE")?><b><?=GetMessage("3NV_LIST_BOOKING_CABIN")?><?=$arRes["ROOM"]?>, <?=$arRes["ACCOMMODATIONTYPEDESCRIPTION"]?>, <?=$deck?></b></p>
					</div>
					<br><br>
					<div class="col-md-5">
						<p class="price"><?=GetMessage("3NV_LIST_BOOKING_PRICE")?><b><?=$arRes["TOTALSUM"]?> руб.</b></p>
					</div>
				</div>
			</div>
		<?}?>
	<?endforeach;?>
<?endif;?>
</div>
