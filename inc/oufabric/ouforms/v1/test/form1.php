<?php
require_once('/fs1/WWW-db/NTSS/ben/studentservices/inc/ouforms/OUForms.php');
$OUForms = new OUForms('testform.xml', 'testfrm');

// We can programatically set default values for inputs. Default values in the XML file will take priority over programatically set default values.
//$OUForms->setDefault('name', 'jess');
$OUForms->setDefault('pi', '123456789');
//$OUForms->setDefault('hobby', array('music' => '1', 'gym' => '2'));
//$OUForms->setDefault('gender', 'male');
//$OUForms->setDefault('skirtsize', 'large');
//$OUForms->setDefault('bio', 'boo');

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>OU Forms</title>
<link href="styles.css" rel="stylesheet" type="text/css" />
<?php require_once('/fs1/WWW-db/NTSS/ben/studentservices/inc/ouforms/load-css.php'); ?>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
</head>

<body>

<div class="ouforms">
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

  <?php $OUForms->errorHandler(); ?>
  
  <?php $OUForms->autoShow(1); ?>
  
  <input type="hidden" name="nav[next]" value="form2.php" />
  <?php $OUForms->nextButton(); ?>

</form>

<p><a href="?clear=form">Clear the form</a></p>

</div>

<?php require_once('/fs1/WWW-db/NTSS/ben/studentservices/inc/ouforms/load-js.php'); ?>

<!-- OU Forms Javascript : Start -->
<script>
$(function() {
	OUForms['conditions'] = <?php $OUForms->showConditionJSON(); ?>;
	OUForms.init();
});
</script>
<!-- OU Forms Javascript : End -->
</body>
</html>