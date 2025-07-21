<?php
// This example header.inc.php is intended to be modfied for your application.
use QCubed as Q;
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="<?php echo(QCUBED_ENCODING); ?>"/>
	<meta content="text/html"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="-1">
	<?php if (isset($strPageTitle)){ ?><title><?php _p($strPageTitle); ?></title><?php } ?>

	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700&subset=all" rel="stylesheet" type="text/css"/>
	<link href="../../../../project/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../assets/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="../assets/css/awesome-bootstrap-checkbox.css" rel="stylesheet"/>
	<link href="../assets/css/style.css" rel="stylesheet"/>
    <link href="../assets/css/vauu-table.css" rel="stylesheet" />
	<link href="../assets/css/toastr.css" rel="stylesheet"/>
	<link href="../assets/css/toastr.fontawesome.css" rel="stylesheet"/>
    <link href="../assets/css/select2.css" rel="stylesheet" />
    <link href="../assets/css/select2-bootstrap.css" rel="stylesheet" />
    <link href="../assets/css/select2-web-vauu.css" rel="stylesheet"/>
	<link href="../assets/css/bootstrap-datetimepicker.css" rel="stylesheet"/>
	<link href="../assets/css/bootstrap-clockpicker.css" rel="stylesheet"/>
    <link href="../assets/css/qcubed.mediafinder.css" rel="stylesheet" />
    <link href="../assets/css/jquery.fileupload.css" rel="stylesheet" />
    <link href="../assets/css/jquery.fileupload-ui.css" rel="stylesheet" />
    <link href="../assets/css/vauu-table.css" rel="stylesheet" />
    <link href="../assets/css/qcubed.mediafinder.css" rel="stylesheet" />
    <link href="../assets/css/qcubed.filemanager.css" rel="stylesheet"/>
    <link href="../assets/css/qcubed.uploadhandler.css" rel="stylesheet"/>
    <link href="../assets/css/qcubed.fileinfo.css" rel="stylesheet"/>
    <link href="../assets/css/custom-svg-icons.css" rel="stylesheet" />
    <link href="../assets/css/croppie.css" rel="stylesheet" />
    <link href="../assets/css/custom-buttons-inputs.css" rel="stylesheet"/>
    <link href="../assets/css/custom-switch.css" rel="stylesheet" />
    <link href="../assets/css/jquery.bxslider.css" rel="stylesheet" />
    <link href="../assets/css/infobox.css" rel="stylesheet" />


	<style>
        .select2-container--web-vauu .select2-results > .select2-results__options {
			height: auto;
			max-height: none;
			overflow-y: auto;
		}
		[type="search"]::-webkit-search-cancel-button,
		[type="search"]::-webkit-search-decoration {
			-webkit-appearance: none;
		}
        .news-preview img {
            height: 50px;
            width: 50px;
            object-fit: cover;
            object-position: 100% 0;
        }
        .preview img {
            height: 90px;
            width: 90px;
            object-fit: cover;
            object-position: 100% 0;
        }
        .hidden {display: none;}
	</style>
</head>
	<body>