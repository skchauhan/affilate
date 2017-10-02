<?php 
	include('function.php'); 
	
	$arrCompareProduct = !empty($_SESSION['compare_product']) ? $_SESSION['compare_product'] : '';
	if(!empty($arrCompareProduct)) {
		$arrAmazoneProduct = $arrCompareProduct['amazon'];
		$arrEbayoneProduct = $arrCompareProduct['ebay'];
		//pr($arrEbayoneProduct);
		$intAmazonProduct = count($arrAmazoneProduct);
		$intEbayProduct = count($arrEbayoneProduct);
			$intMaxProduct = '';  $type = '';
		if($intEbayProduct >= $intAmazonProduct) {
			$intMaxProduct = $intEbayProduct;
			$type = 'ebay';
		} else {
			$intMaxProduct = $intAmazonProduct;
			$type = 'amazon';
		}
	?>
	<style>
		body { font-family:arial; } 
		.c_table img { width:90px; }
		table { float: left; width: 100%; border-collapse:collapse; font-size:13px; }
		.content { width:900px; margin:0 auto;  }
		
		table th {
			background: rgb(51, 153, 255) none repeat scroll 0 0;
			color: #fff;
			height: 35px;
		}
	</style>  
	<div class="content">
		<div class=""> <a href="index.php">Back</a> To Search Page </div>
		<br />
		<table border='2' class="c_table">
				<tr>
					<th>Product</th>
					<th>Am Pic</th>
					<th>Eb Pic</th>
					<th>Profit(%)</th>
					<th>Profit Margin</th>
					<th>AM Price</th>
					<th>EB Price</th>
					<th>AM Prime</th>
					<th>AM Rev</th>
				</tr>
				
				<?php 
					if($type == 'ebay') {
						foreach($arrEbayoneProduct as $ebayKey=>$ebayDetail) {
							$intAmazonPrice = number_format($arrAmazoneProduct[0]['price']/100,2);
							$intProfit = ($intAmazonPrice-$ebayDetail['price'])/$intAmazonPrice;
							$intProfitMargin = $intAmazonPrice-$ebayDetail['price'];
				?>			
							<tr>
								<td style="width: 293px;"> (AM)<a target="_blank" href="<?php echo($arrAmazoneProduct[0]['product_url']);?>"><?php echo($arrAmazoneProduct[0]['title']); ?></a> ( FREE Shipping on orders over $35 ) <br /> - <br />(EB)<a target="_blank" href="<?php echo($ebayDetail['product_url']);?>"><?php echo(!empty($ebayDetail['title']) ? $ebayDetail['title'] : '');?></a> (Shipping : <?php echo(($ebayDetail['shippingServiceCost'] == '0.0') ?  'FREE' : $ebayDetail['shippingServiceCost']); ?>) </td>
								<td> <a target="_blank" href="<?php echo($arrAmazoneProduct[0]['product_url']);?>">  <img src="<?php echo($arrAmazoneProduct[0]['image_url']);?>"> </a> </td>
								<td> <a target="_blank" href="<?php echo($ebayDetail['product_url']);?>"><img src="<?php echo($ebayDetail['image_url']);?>"></a> </td>
								<td> <?php echo(round($intProfit,2)*100); ?> </td>
								<td> <?php echo(round($intProfitMargin,2)); ?> </td>
								<td> <?php echo($intAmazonPrice); ?> </td>
								<td> <?php echo($ebayDetail['price']); ?> </td>
								<td> <?php echo($arrAmazoneProduct[0]['prime']); ?> </td>
								<td> <?php echo(!empty($arrAmazoneProduct[0]['salesRank']) ? $arrAmazoneProduct[0]['salesRank'] : ''); ?> </td>
								
								
							</tr>
				<?php 	
						}
					}
				?>
		</table>
	</div>	
<?php } else { 
	header('location:index.php');
} ?>
	
	