<?php 
	session_start();
	
	define('URL','http://localhost/amazon_ebay');
	
	function pr($data) {
		if(is_array($data) || is_object($data)) {
			print"<pre>";
			print_r($data);
			print"</pre>";
		} else {
			print"<pre>";
			echo($data);
			print"</pre>";
		}
	}
		
	function getAmazonUrl($intPage,$strKey) {
		$aws_access_key_id = "xxxxxxxxxxxxxxxxxxxx";
		// Your AWS Secret Key corresponding to the above ID, as taken from the AWS Your Account page
		$aws_secret_key = "XXXXXXXXXXXXXXXXXXXXXXXXXX";
		// The region you are interested in
		$endpoint = "webservices.amazon.com";
		$uri = "/onca/xml";
		$params = array(
			"Service" => "AWSECommerceService",
			"Operation" => "ItemSearch",
			"AWSAccessKeyId" => "xxxxxxxxxxxxxxxxxxxx",
			"AssociateTag" => "sunil123-20",
			"Version" => "2013-08-01",
			"SearchIndex" => "All",
			"ItemPage"=>$intPage,
			
			/* "Sort" => "-price", */
			"ResponseGroup" => "Images,ItemAttributes,Large,OfferSummary,VariationSummary",
			"Keywords" => $strKey,
		);
		// Set current timestamp if not set
		if (!isset($params["Timestamp"])) {
			$params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
		}
		// Sort the parameters by key
		ksort($params);
		$pairs = array();
		foreach ($params as $key => $value) {
			array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
		}
		// Generate the canonical query
		$canonical_query_string = join("&", $pairs);
		// Generate the string to be signed
		$string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;
		// Generate the signature required by the Product Advertising API
		$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));
		// Generate the signed URL
		$request_url = 'http://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);
		return($request_url);
	}
	
	function compareProduct() {
		$arrAllEbayProduct = $_POST['ebay_product'];
		$arrAllAmazonProduct = $_POST['amazon_product'];
		
		if(!empty($arrAllEbayProduct) && !empty($arrAllAmazonProduct)) {
				$arrSelectedEbayProduct = array();
			foreach($arrAllEbayProduct as $strKey=>$arrOneEbayPdtDetal) {
				$strCheckEbayProduct = !empty($arrOneEbayPdtDetal['checked']) ? $arrOneEbayPdtDetal['checked'] : '';
				if(($strCheckEbayProduct == "on") && !empty($strCheckEbayProduct)) {
					$arrSelectedEbayProduct[] = $arrOneEbayPdtDetal;
				}
			}
				$arrSelectedAmazonProduct = array();
			foreach($arrAllAmazonProduct as $strAmKey=>$arrOneAmznPdtDetal) {
				$strCheckAmzonProduct = !empty($arrOneAmznPdtDetal['checked']) ? $arrOneAmznPdtDetal['checked'] : '';
				if(($strCheckAmzonProduct == 'on') && !empty($strCheckAmzonProduct)) {
					$arrSelectedAmazonProduct[] = $arrOneAmznPdtDetal;
				}
			}
			
			if(!empty($arrSelectedAmazonProduct) && !empty($arrSelectedEbayProduct)) {
				$_SESSION['compare_product'] = array('amazon'=>$arrSelectedAmazonProduct, 'ebay'=>$arrSelectedEbayProduct);
				header('Location: compare_product.php');
			} else {
				$_SESSION['ERR_MSG'] = "Sorry Please Select Product";
				header("Location:index.php");
			}
		} else {
			$_SESSION['ERR_MSG'] = "something went wrong please try agian";
			header("Location:index.php");
		}
		die;
	}
	
	function modifyAmazon($arrAmazoneProduct) {
			$arrAmazonProduct = array();
		foreach($arrAmazoneProduct as $AmazonKey => $AmazonRow) {
			// pr($AmazonRow);echo ("<hr />");
			$arrAmazonProduct['price'][] = !empty($AmazonRow['Offers']['Offer']['OfferListing']['Price']['Amount']) ? $AmazonRow['Offers']['Offer']['OfferListing']['Price']['Amount'] : (!empty($AmazonRow['OfferSummary']['LowestNewPrice']['Amount']) ? $AmazonRow['OfferSummary']['LowestNewPrice']['Amount'] : (!empty($AmazonRow['OfferSummary']['LowestCollectiblePrice']['Amount']) ? $AmazonRow['OfferSummary']['LowestCollectiblePrice']['Amount'] : ''));
			
			 // $arrAmazonProduct['price'][]  = !empty($AmazonRow['Offers']['Offer']['OfferListing']['Price']['Amount']) ? $AmazonRow['Offers']['Offer']['OfferListing']['Price']['Amount'] : '';
			$arrAmazonProduct['DetailPageURL'][]  = $AmazonRow['DetailPageURL'];
			$arrAmazonProduct['productImage'][]  = !empty($AmazonRow['LargeImage']['URL']) ? $AmazonRow['LargeImage']['URL'] : '';
			$arrAmazonProduct['title'][]  = !empty($AmazonRow['ItemAttributes']['Title']) ? $AmazonRow['ItemAttributes']['Title'] : ''; 
			$arrAmazonProduct['SalesRank'][]  = !empty($AmazonRow['SalesRank']) ? $AmazonRow['SalesRank'] : '';
			$arrAmazonProduct['ASIN'][]  = !empty($AmazonRow['ASIN']) ? $AmazonRow['ASIN'] : '';
			$arrAmazonProduct['prime'][] = !empty($AmazonRow['Offers']['Offer']['OfferListing']['IsEligibleForPrime']) ? 'Yes' : 'No';
			
		}
		return($arrAmazonProduct);
	}
	
?>
