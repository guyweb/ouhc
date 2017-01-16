<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oudocs/OUDocs.php' );
$OUDocs = new \OUFabric\OUDocs( 'jquery' );
$OUDocs->printHead();
?>


<div class="content">
    <h1>OU Fabric jQuery</h1>
    
    <p>OU Fabric provides a sandboxed instance of jQuery, which is internally referenced by OU Fabric plugins. By isolating this jQuery instance, OU Fabric libraries and plugins benefit from a namespaced environment to protect important properties and methods from being manipulated.</p>
    <p>This separation enables developers to build sites using their own version of jQuery, with the confidence that OU Fabric libraries will behave as expected.</p>
    
    <ul class="pagenav">
	  <li><a href="#loading">How to load</a></li>
	</ul>
    
    <h2 class="section" id="loading">How to load</h2>
    <p>To load the OU Fabric jQuery library, simply use the following PHP code within the <code>&lt;head>&lt;/head></code> of the document, and <strong>before</strong> any other versions of jQuery are loaded.</p>
    <pre>&lt;?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/jquery/load-jquery-1.10.2.php' );
?&gt;
</pre>
    <p>This must be loaded before making use of any OU Fabric plugins. The instance can be referenced as <strong><code>$1_10_2</code></strong> within the Javascript environment.</p>
    
    
</div>



<?php
$OUDocs->printFoot();
?>