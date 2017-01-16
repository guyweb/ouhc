<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oudocs/OUDocs.php' );
$OUDocs = new \OUFabric\OUDocs( 'oucommon', 'v1' );
$OUDocs->printHead();
?>


<div class="content">
    <h1>OU Common</h1>
    
    <p>OU Common is a collection of common classes for use across OU websites.</p>
</div>



<?php
$OUDocs->printFoot();
?>