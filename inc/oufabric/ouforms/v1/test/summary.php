<?php
require_once('/fs1/WWW-db/NTSS/ben/studentservices/inc/ouforms/OUForms.php');
$OUForms = new OUForms('testform.xml', 'testfrm');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>OU Forms</title>
<?php require_once('/fs1/WWW-db/NTSS/ben/studentservices/inc/ouforms/load-css.php'); ?>

</head>

<body>

<div class="ouforms">

<h1>Summary page</h1>

<h2>Please review your information</h2>

<p><a href="form1.php">Edit all</a></p>

<?php $OUForms->showSummary(); ?>

<div class="email_example">
<h2>Email output example</h2>
<pre>
<?php echo $OUForms->outputForEmail(); ?>
</pre>
</div>

<div class="email_example">
<h2>VOICE output example</h2>
<pre>
<?php
echo $OUForms->outputForVoice(
							array(
								"Owner" => "Q-DSS-RESOURCES", 
								"Type" => "Disability", 
								"Area" => "Residential Schools", 
								"Description" => "FRF2 form despatched"
							)
);
?>
</pre>
</div>

</div>

</body>
</html>