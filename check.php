<?php
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />	
	
	<title>
		ویکی پال | ارائه دهنده درگاه پرداخت آنلاین
	</title>
	
	<style type="text/css">
		* {
			text-align:right;
			direction:rtl;
			font-family:tahoma;
			font-weight:normal;
			font-size:12px;
		}

		.panel {
			background-color: #fff;
			border: 1px solid transparent;
			border-radius: 4px;
			box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
			margin-bottom: 20px;
		}
		.panel-primary {
			border-color: #337ab7;
		}
		.panel-primary > .panel-heading {
			background-color: #337ab7;
			border-color: #337ab7;
			color: #fff;
			border-bottom: 1px solid transparent;
			border-top-left-radius: 3px;
			border-top-right-radius: 3px;
			padding: 10px 15px;
		}
		.panel-body {
			padding: 15px;
		}
		table {
			border-collapse: separate;
			border-spacing: 2px;
			box-sizing: border-box;
			display: table;
			text-indent: 0;
			width: 100%;
		}
		tbody {
			display: table-row-group;
			vertical-align: middle;
		}
		tr {
			display: table-row;
			vertical-align: inherit;
		}
		td {
			display: table-cell;
			padding: 1px;
			text-align: inherit;
			vertical-align: inherit;
			padding: 9px;
			
		}
		.table-striped > tbody > tr:nth-of-type(2n+1) {
			background-color: #f9f9f9;
		}
		.btn {
			-moz-user-select: none;
			background-image: none;
			border: 1px solid transparent;
			border-radius: 4px;
			cursor: pointer;
			display: inline-block;
			font-size: 14px;
			font-weight: 400;
			line-height: 1.42857;
			margin-bottom: 0;
			padding: 6px 12px;
			text-align: center;
			vertical-align: middle;
			white-space: nowrap;
		}
		.btn-success {
			background-color: #5cb85c;
			border-color: #4cae4c;
			color: #fff;
		}
		input {
			border: 1px solid #cfcfcf;
			border-radius: 5px;
			padding: 7px;
			width: 70%;
			text-align:left;
			direction:ltr;
		}
	</style>
</head>
<body class="rtl">
	<div id="container">
		<div id="row">
			<div class="col-sm-6" style="margin:0 25% !important">	
				<br>
				<div class="panel panel-primary">
					<div class="panel-heading">نمایش اطلاعات سرور پذیرنده</div>
					<div class="panel-body">
						<table class="table table-striped">
							<tbody>
								<tr>
									<td>IP سرور :</td>
									<td><?php echo get_client_ip(); ?></td>
								</tr>
								<tr>
									<td>نسخه PHP :</td>
									<td><?php echo function_exists('phpversion') ? phpversion() : ''; ?></td>
								</tr>
								<tr>
									<td>بررسی فعال بودن ماژول cURL :</td>
									<td><?php echo extension_loaded('curl') ? '<span style="color:#008000;font-weight:bold;">فعال</span>' : '<span style="color:#FF0000;font-weight:bold;">غیرفعال</span>'; ?></td>
								</tr>
								<tr>
									<td>وضعیت دسترسی به وب سرویس :</td>
									<td><?php $curl = curl_init("http://gatepay.co/webservice/connection.jpg");
									curl_setopt($curl, CURLOPT_NOBODY, true);
									$result = curl_exec($curl);
									if ($result !== false) {
										$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
										if ($statusCode == 404) {
											echo '<span style="color:#FF0000;font-weight:bold;">خطا در دسترسی</span>';
										} else {
											echo '<span style="color:#008000;font-weight:bold;">فعال و در دسترس</span>';
										} 
									} else {
										echo '<span style="color:#FF0000;font-weight:bold;">خطا در دسترسی</span>';
									} ?></td>
								</tr>
							</tbody> 
							<form action="" method="POST">
								<tbody>
									<tr>
										<td style="width:40%">مرچنت کد</td>
										<td><input type="text" class="form-control" name="MerchantCode" value=""/></td>
									</tr>
									<tr>
										<td>بررسی</td>
										<td><button class="btn btn-success" type="submit">بررسی</button></td>
									</tr>
									<?php if ( isset($_POST['MerchantCode']) ) { ?>										
									<tr>
										<td>نتیجه</td>
										<td>
										<?php echo wikipal_Response( $_POST ); ?>
										</td>
									</tr>
									<?php } ?>
								</tbody> 
							</form>
						</table>
					</div>
				</div>
			</div> 
		</div>
	</div>
</body>
</html>
<?php
function get_client_ip() {
	
	$client_ip = '<span style="color:#FF0000;">خطا در دسترسی به IP هاست رخ داده است .</span>';
	
	if ( extension_loaded('curl')) {
		$ch = curl_init('http://gatepay.co/webservice/ip.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$client_ip = curl_exec($ch);
		curl_close($ch);
	}
	else if (function_exists('file_get_contents')) {
		$client_ip = file_get_contents('http://gatepay.co/webservice/ip.php');
	}
	
	return $client_ip;
}

function wikipal_Response( $post ) {
	
	if ( empty($post['MerchantCode']) ) {
		return $Response =	'<span style="color:#FF0000;">مرچنت کد را وارد نمایید .</span>';
	}
	
	if ( $_SERVER['SERVER_PORT'] != '80' ) {
		$ReturnUrl = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
	}
	else {
		$ReturnUrl = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}

	$MerchantID 			= $post['MerchantCode'];
	$Price 					= 500;
	$Description 			= 'ویکی پال';
	$InvoiceNumber 			= time();
	$CallbackURL 			= $ReturnUrl;

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://gatepay.co/webservice/paymentRequest.php');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type' => 'application/json'));
	curl_setopt($curl, CURLOPT_POSTFIELDS, "MerchantID=$MerchantID&Price=$Price&Description=$Description&InvoiceNumber=$InvoiceNumber&CallbackURL=". urlencode($CallbackURL));
	curl_setopt($curl, CURLOPT_TIMEOUT, 400);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec($curl));
	curl_close($curl);
	if ($result->Status == 100){
		$Response = '<span style="color:#0000FF;">اتصال به وب سرویس ویکی پال با موفقیت انجام شد</span>';
	} else {
		$Response = '<span style="color:#FF0000;">خطای : '. $result->Status .' <br><br> '. wikipal_Request_Results($result->Status) .'</span>';
	}
	
	return $Response;
}

function wikipal_Request_Results($Fault) {
	
	switch ($Fault) {

		case '-1' :
			$Response =	'پارامترهای ارسال ناقص میباشد';
			break;
	
		case '-2' :
			$Response =	'مرچنت کد ارسال شده صحیح نمیباشد';
			break;

		case '-3' :
			$Response =	'مرچنت کد ( درگاه مورد نظر ) غیر فعال میباشد';
			break;
	
		case '-4' :
			$Response =	'مقدار پارامتر Price باید یک عدد صحیح برابر یا بزرگتر از 100 باشد ( حداقل مبلغ قابل پرداخت 100 تومان میباشد )';
			break;
	
		case '-5' :
			$Response =	'مقدار InvoiceNumber باید یک عدد صحیح بزرگتر از 0 باشد';
			break;

		case '-6' :
			$Response =	'خطای سیستمی در ایجاد Authority, این موضوع را به بخش پشتیبانی ویکی پال اطلاع دهید';
			break;
	
		case '-7' :
			$Response =	'خطا در دریافت Authority, این موضوع را به پشتیبانی ویکی پال اطلاع دهید';
			break;

		case '-8' :
			$Response =	'خطای سیستمی, این موضوع را به پشتیبانی ویکی پال اطلاع دهید';
			break;

		default :
			$Response =	'خطای نا مشخص';
			break;
	}
	return $Response;
}
?>