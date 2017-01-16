<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oudocs/OUDocs.php' );
$OUDocs = new \OUFabric\OUDocs( 'oucarousel', 'v1' );
$OUDocs->printHead();
?>


<div class="content">
    <h1>OU Carousel</h1>
    
    <p>OU Carousel is a lightweight jQuery plugin for creating sliding content in web pages.</p>
    
    <p><strong>OU Carousel requires that the <a href="/ldt_shared/oufabric/jquery/">OU Fabric jQuery 1.10.2</a> dependency is loaded before use.</strong></p>
    
    <!-- Page nav -->
      <ul class="pagenav">
      	 <li><a href="#key-features">Key features</a></li>
         <li><a href="#installation">Installation</a></li>
         <li><a href="#see-in-action">See it in action</a></li>
      </ul>
    
    
    
    <h2 class="section" id="key-features">Key features</h2>

	<ul>
	    <li>Simple HTML markup</li>
		<li>Responsive - dynamically resizable while maintaining width/height ratio</li>
	    <li>Mobile touch friendly - swipe on mobile devices</li>
	    <li>Supports captions</li>
	    <li>Configurable on a per-instance basis</li>
	    <li>Automatically creates clickable 'bullets' for each slide</li>
	    <li>Pauses on hover</li>
	</ul>
	
	
	
	<h2 class="section" id="installation">Installation</h2>
	
	<h3>Linked CSS:</h3>
	<p>Add the following PHP between the <code>&lt;head&gt;&lt;/head&gt;</code> tags of your page.</p>
	
	<pre>&lt;?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/plugins/oucarousel/v1/load-css.php' );
?&gt;</pre>
	
	<h3>Linked JavaScript:</h3>
	<p>Add the following PHP before the closing <code>&lt;/body&gt;</code> tag of your page (ensure jQuery and OUCommon JS dependencies are loaded before this).</p>
	
	<pre>&lt;?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/plugins/oucarousel/v1/load-js.php' );
?&gt;</pre>
	
	<h3>Build the HTML:</h3>
	<p>OU Carousel will automagically build the carousel component based on semantic HTML markup.</p>
	
	<ul>
		<li>The markup consists of a <code>&lt;div&gt;</code>, which contains an <code>&lt;ul&gt;</code> element in which each <code>&lt;li&gt;</code> element represents a slide in the carousel.
	    <li>The containing <code>&lt;div&gt;</code> must have its width defined, and either a height or aspect ratio also defined (see below).</li>
	    <li>Each <code>&lt;li&gt;</code> should contain at least an <code>&lt;img /&gt;</code> element. Additionally, this can be wrapped in an <code>&lt;a&gt;</code> block if desired, and a <code>&lt;p&gt;</code> element can be used to show a caption.</li>
	</ul>
	
	<p>Example:</p>
	
	<pre>&lt;div id="myCarousel" data-ratio="0.575"&gt;
    &lt;ul&gt;
        &lt;li&gt;
            &lt;a href="http://..."&gt;&lt;img src="http://..." alt="" /&gt;&lt;/a&gt;
            &lt;p&gt;Slide 1 caption here...&lt;/p&gt;
        &lt;/li&gt;
        
        [...]
    &lt;/ul&gt;
&lt;/div&gt;</pre>
	
	<h3>Call the plugin</h3>
	
	<p>Call the plugin to turn your markup into a carousel component:</p>
	
	<pre>&lt;script&gt;
	$1_10_2( '#myCarousel' ).oucarousel();
&lt;/script&gt;
</pre>
	
	<h3>Configuring the plugin</h3>
	
	<p>The plugin can be configured to change how it behaves:</p>
	
	<ul>
		<li><strong>aspectRatio</strong> (default: 0.575)<br />This number represents the proportion of the height of the component, relative to its defined width. For example, 0.5 would force the carousel's height to be fixed at 50% of its width.<br /><em>This can also be defined by setting the data-ratio attribute on the containing <code>&lt;div&gt;</code></em></li>
		<li><strong>timer</strong> (default: 12000)<br />The number of milliseconds representing when the carousel will automatically transition from one slide to the next.</li>
		<li><strong>speed</strong> (default: 300)<br />The duration of the sliding animation, in milliseconds.</li>
        <li><strong>randomise</strong> (default: true)<br />Whether or not to randomise the order of the slides.</li>
	</ul>
	
	<p>Configurable example:</p>
	
	<pre>&lt;script&gt;
	$1_10_2( '#myCarousel' ).oucarousel( {
		aspectRatio:	0.575,
		timer:		12000,
		speed:		300,
		randomise:	true
	} );
&lt;/script&gt;
</pre>
	
	
	<h2 class="section" id="see-in-action">See it in action</h2>
	<p><a href="example.php" target="_blank">Click me!</a></p>
	
</div>



<?php
$OUDocs->printFoot();
?>