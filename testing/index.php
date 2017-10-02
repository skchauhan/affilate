<?php include('function.php'); ?>
<style>
body { font-family:arial; } 
.content { margin: 0 auto; width: 950px; }
.compare-price { float: left; width: 100%; }
.compare_form { float: left; width: 100%; text-align:center; } 
.Ebay { margin-right: 30px; }
.Amazon, .Ebay { float: left; width: 459px; border-collapse:collapse; font-size:13px;  }
.Amazon tr, .Ebay tr { height:170px; }
.sort_product { float:left; width:100%; text-align:right; }
.compare_form .key { height: 35px; width: 250px; }
.submit { width: 119px; height: 35px; cursor: pointer; }  
select.sort_price { height: 30px; margin-bottom: 10px; width: 158px; }
.compare_submit {   cursor: pointer; float: right; height: 35px; margin-right: 142px; position: relative; top: -50px; width: 123px; }

.Amazon th, .Ebay th { background: rgb(51, 153, 255) none repeat scroll 0 0; color: #fff; height: 35px; }
.ebay_product, .amazon_product { float: right; margin-top: -30px !important; width: 77px; text-align:center; }
</style>
<?php include('site_message.php'); ?>
<div class="content">
<form class="compare_form" method="get">
	<input type="text" class="key" name="key" value="<?php echo !empty($_GET['key']) ? $_GET['key'] : ''; ?>">
	<input type="submit" value="Search" name="search" class="submit">
</form>
<?php 
	if(isset($_POST['campare'])) {
		compareProduct();
	}

	$strKey = !empty($_GET['key']) ? $_GET['key'] : '';
	if(!empty($strKey)) {
?>	

	<form method="post">
	 <input type="submit" value="Complete" name="campare" class="compare_submit" />
<div class="compare-price">
<?php
		$sortBy = '';
		$strSortKey = !empty($_GET['sort']) ? $_GET['sort'] : '';
	if(!empty($strSortKey)) {
		if($strSortKey == 'high-to-low') {
			$sortBy = SORT_DESC;
		} else {
			$sortBy = SORT_ASC;
		}
	}
	// echo($sortBy);
	?>
	<div class="sort_product">
		<select class="sort_price">
			<option value="">Sort</option>
			<option value="low-to-high" <?php echo(($strSortKey == 'low-to-high') ? 'selected' : ''); ?>>Low To High  </option>
			<option value="high-to-low" <?php echo(($strSortKey == 'high-to-low') ? 'selected' : ''); ?>>High To Low  </option>
		</select>
	</div>	
<?php /* ebay Product code start  */
	$strEbayUrl = "http://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsByKeywords&SERVICE-VERSION=1.0.0&SECURITY-APPNAME=XXXXXXXXXXXXXXXXXXXX&RESPONSE-DATA-FORMAT=XML&REST-PAYLOAD&keywords=".urlencode($strKey);
	
// echo($strEbayUrl);
	$getEbayData =  @file_get_contents($strEbayUrl);
	$loadEbayData   = simplexml_load_string($getEbayData);
	$arrEbayAllData = json_decode(json_encode((array) $loadEbayData), 1);
	// pr($arrEbayAllData);
	$arrEbayItem = array();
	 if(!empty($arrEbayAllData['searchResult'])) {
		$arrEbayItem = $arrEbayAllData['searchResult'];
	 } else {
		 $arrEbayItem = $arrEbayAllData;
	 }
	
	// pr($arrEbayAllData);
	echo('<table border="2" class="Ebay">');
	echo('<tr style="height:auto;"><th colspan="2" style="text-align:center;">  Ebay Product List </th></tr>');
	
	if(!empty($arrEbayItem['item'][0])) {
		if(!empty($strSortKey)) {
			foreach ($arrEbayItem['item'] as $key => $row) {
				$ebaySort[$key]  = $row['sellingStatus']['currentPrice'];
			}
			array_multisort($ebaySort, $sortBy, $arrEbayItem['item']);
		}
		$inj = 0;
		foreach($arrEbayItem['item'] as $arrSingleProduct) {
			if($inj < 10) {
?>
		<tr>
			<td style='text-align:center;'>
			<?php if(!empty($arrSingleProduct['galleryURL'])) { ?>
				<a href="<?php echo($arrSingleProduct['viewItemURL']); ?>"><img  src="<?php echo($arrSingleProduct['galleryURL']); ?>"> </a>
			<?php } else { ?>
				NO Image
			<?php } ?> 
			</td>
			<td>
			
				<div class="ebay_product">Select <br /><input type="checkbox" name="ebay_product[<?php echo($arrSingleProduct['viewItemURL']); ?>][checked]">
					<input type="hidden" name="ebay_product[<?php echo($arrSingleProduct['viewItemURL']); ?>][product_url]" value="<?php echo($arrSingleProduct['viewItemURL']); ?>">
					<input type="hidden" name="ebay_product[<?php echo($arrSingleProduct['viewItemURL']); ?>][image_url]" value="<?php echo($arrSingleProduct['galleryURL']); ?>">
					<input type="hidden" name="ebay_product[<?php echo($arrSingleProduct['viewItemURL']); ?>][price]" value="<?php echo($arrSingleProduct['sellingStatus']['currentPrice']); ?>">
					<input type="hidden" name="ebay_product[<?php echo($arrSingleProduct['viewItemURL']); ?>][title]" value="<?php echo($arrSingleProduct['title']); ?>">
					<input type="hidden" name="ebay_product[<?php echo($arrSingleProduct['viewItemURL']); ?>][shippingServiceCost]" value="<?php echo(!empty($arrSingleProduct['shippingInfo']['shippingServiceCost']) ? $arrSingleProduct['shippingInfo']['shippingServiceCost'] : '0.0'); ?>">
				</div>
					
					
				<?php if(!empty($arrSingleProduct['sellingStatus']['currentPrice'])) { ?>
					<?php echo('$'.$arrSingleProduct['sellingStatus']['currentPrice']);?>
					<?php echo('<br />'.$arrSingleProduct['title']);?>
				<?php } ?>
			</td>
		</tr>
	<?php 		
			}
			$inj++;
		  
		}		
	} else { 
	?>
		<tr>
			<td colspan="2">
				<strong style='color:red'>We did not find any matches for your request.</strong>
			</td>
		</tr>
<?php }
	echo('</table>');
	/* amazon Product code start  */
	
	
	$strAmazonUrl = getAmazonUrl(1,$strKey);
	$getAmazonData =  @file_get_contents($strAmazonUrl);
	$loadAmazonXml = simplexml_load_string($getAmazonData);
	$arrayALLAmzonproduct = json_decode(json_encode((array) $loadAmazonXml), 1);
	$arrAmazoneProduct = !empty($arrayALLAmzonproduct['Items']) ? $arrayALLAmzonproduct['Items'] : '';
	/* $intAmazonPage = $arrAmazoneProduct['TotalPages'];
		$intTotPage = '';
	if($intAmazonPage>3) {
		$intTotPage = 3;
	} else {
		$intTotPage = $intAmazonPage;
	}
	for($inj=2; $inj<$intTotPage; $inj++) {
		echo($inj).'<hr />';
	} */
	// pr($arrAmazoneProduct['Item']);
	echo('<table border="2" class="Amazon">');
	echo('<tr style="height:auto;"><th colspan="2" style="text-align:center;">  Amazon Product List </th> <th colspan="2" style="text-align: center; width: 105px;">  Sales Rank </th> </tr>');
	if(!empty($arrAmazoneProduct['Item'][0])) {
		$arrAmazonProduct = modifyAmazon($arrAmazoneProduct['Item']);
			$arrProductDetail = array();
		foreach($arrAmazonProduct['price'] as $strKeyQ=>$strDate) {
			foreach($arrAmazonProduct as $dataq) {
				$arrProductDetail[$strKeyQ][] = $dataq[$strKeyQ];
			}
		}
		if(!empty($strSortKey)) {
			foreach ($arrProductDetail as $key => $row) {
				$amzonSort[$key]  = $row['0'];
			}
			array_multisort($amzonSort, $sortBy, $arrProductDetail);
		}
		foreach($arrProductDetail as $data) {
	?>
		<tr>
		<td>
		<?php if(!empty($data[2])) { ?>
			<a href="<?php echo($data['1']); ?>"><img style='width:120px' src="<?php echo($data['2']); ?>"> </a>
		<?php } else { ?>
			NO Image
		<?php } ?> 
		</td>
		<td>
			<div class="amazon_product">Select One <br /><input class='amzon_check' type="checkbox" name="amazon_product[<?php echo($data[1]); ?>][checked]">
			<input type="hidden" name="amazon_product[<?php echo($data[1]); ?>][product_url]" value="<?php echo($data[1]); ?>">
			<input type="hidden" name="amazon_product[<?php echo($data[1]); ?>][image_url]" value="<?php echo($data[2]); ?>">
			<input type="hidden" name="amazon_product[<?php echo($data[1]); ?>][price]" value="<?php echo($data[0]); ?>">
			<input type="hidden" name="amazon_product[<?php echo($data[1]); ?>][salesRank]" value="<?php echo($data[4]); ?>">
			<input type="hidden" name="amazon_product[<?php echo($data[1]); ?>][Asin]" value="<?php echo($data[5]); ?>">
			<input type="hidden" name="amazon_product[<?php echo($data[1]); ?>][title]" value="<?php echo($data[3]); ?>">
			<input type="hidden" name="amazon_product[<?php echo($data[1]); ?>][prime]" value="<?php echo($data[6]); ?>">
			</div>
			
				<?php 
				echo '$'.number_format($data[0]/100,2);
				// echo $data[0];
				?>
				<?php echo('<br />'.$data[3]);?>
		</td>
		<td>
			<?php echo(!empty($data[4]) ? $data[4] : ''); ?>
		</td>
		</tr>
	<?php } ?>
	
	<?php } else { ?>
		<tr>
			<td colspan="2">
				<strong style='color:red'>We did not find any matches for your request.</strong>
			</td>
		</tr>
<?php } ?>
</table>
</div>
</form>
<?php } ?>
</div>

<script src="lib.js"></script>
<script>
$(document).ready(function(){
	$(document).on('change','.sort_price',function(){
		var strSortCondti = $(this).val();
		if(strSortCondti != '') {
			window.location.href="<?php echo(URL); ?>/index.php?key=<?php echo(urlencode($strKey)); ?>&sort="+strSortCondti;
		} else {
			window.location.href="<?php echo(URL); ?>/index.php?key=<?php echo(urlencode($strKey)); ?>";
		}
	});
	
	$(document).on('change','.amzon_check',function(){
		$('.amzon_check').not(this).prop('checked',false);
	});
	
})
	
</script>