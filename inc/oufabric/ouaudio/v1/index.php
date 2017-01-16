<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oudocs/OUDocs.php' );
$OUDocs = new \OUFabric\OUDocs( 'ouaudio', 'v1' );
$OUDocs->printHead();
?>


<div class="content">
    <h1>OU Audio</h1>
    
    <p>This is where the OU Audio docs should go...</p>
</div>



<?php
$OUDocs->printFoot();
?>