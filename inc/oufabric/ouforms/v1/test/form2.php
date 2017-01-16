<?php
require_once('/fs1/WWW-db/NTSS/ben/studentservices/inc/ouforms/OUForms.php');
$OUForms = new OUForms('testform.xml', 'testfrm');
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
  
  <?php $OUForms->autoShow(2); ?>
  
  <?php $OUForms->prevButton('form1.php'); ?>
  
  <input type="hidden" name="nav[next]" value="summary.php" />
  <?php $OUForms->nextButton(); ?>

</form>

<p><a href="form1.php?clear=form">Clear the form</a></p>

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