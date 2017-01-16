<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oudocs/OUDocs.php' );
$OUDocs = new \OUFabric\OUDocs( 'oucommon/javascript', 'v1' );
$OUDocs->printHead();
?>
   
   <div class="content">
      <h1>GUI Components</h1>
      
      <p>OU Common provides a collection of nifty GUI components for use across OU websites.</p>
      
      <p>OU Common GUI Components requires that the <a href="/ldt_shared/oufabric/jquery/">OU Fabric jQuery 1.10.2</a> dependency is loaded before use.</p>
      
      <!-- Page nav -->
      <ul class="pagenav">
      	 <li><a href="#installation">Installation</a></li>
         <li><a href="#component-toggle">Component: Toggle</a></li>
         <li><a href="#component-notify">Component: Notify</a></li>
         <li><a href="#component-link">Component: Link</a></li>
         <li><a href="#glyph-icons">Glyph Icons</a></li>
         <li><a href="#debugging">Debugging</a></li>
         <li><a href="#dependencies">Dependencies</a></li>
      </ul>
      
      <h2 class="section" id="installation">Installation</h2>
      <h3>Load the CSS</h3>
      <p>Add the following code between the <code>&lt;head>&lt;/head></code> tags of your page.</p>
      <pre>&lt;?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oucommon/v1/load-css.php' );
?></pre>  
	  <h3>Load the JS</h3>
      <p>Add the following code before the closing <code>&lt;/body></code> tag of your page (ensure jQuery is loaded before this).</p>    
      <pre>&lt;?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oucommon/v1/load-js.php' );
?></pre> 
	  <h3>Load the library</h3>
      <p>Call <code>OUCommon.load()</code> in your Javascript source to load the library. If the function is called without any parameters, all components will be loaded. Alternatively, it is possible to pass an array of components to be loaded into the function.</p>
      <pre>&lt;script&gt;
	OUCommon.load();    
	/*
	... or optionally only load selected components:    
	OUCommon.load( [ 'toggle', 'link', ... ] );
	*/
&lt;/script&gt;</pre>


		<h2 class="section" id="component-toggle">Component: Toggle</h2>
      	<p>An HTML component for showing and hiding inline content when a link is clicked. It can be accessed by simply adding the <code>data-outoggle</code> attribute to the container <code>&lt;div></code>.</p>
        <p>Note: If you anchor link to the Toggle link or an element that sits inside the content area, the Toggle will automatically be expanded to allow the page to scroll to the correct anchor position. This is true for both external links to the page and anchor links within the same page.</p>
        <h3>Build the HTML</h3>
        <pre>&lt;div data-outoggle=&quot;on&quot;&gt;
	&lt;a href=&quot;#&quot;&gt;Click to Toggle&lt;/a&gt;
	&lt;div&gt;
		Content to be toggled goes here
	&lt;/div&gt;
&lt;/div&gt;</pre>
		<p>When the content toggle is expanded, a class of <code>outoggle-expanded</code> is added to the <code>&lt;a></code> tag. This class is removed when the toggle is collapsed.</a>
        <h3>Additional options</h3>
        <p>Toggle has a number of options that can be used to configure its behaviour. These options should all be set as <code>data-</code> attributes on the container <code>&lt;div&gt;</code></p>
        <ul>
          <li><code>data-outoggle:</code>
            <ul>
              <li><span style="font-weight: bold">on</span>: This will turn the basic Toggle functionality on.</li>
              <li><span style="font-weight: bold">sticky</span>: This will turn the Toggle functionality on but will use Cookies to remember the expand/collapse state of each component. (This option requires the <code>data-outoggle-id</code> attribute to be set).</li>
            </ul>
          </li>
          <li><code>data-outoggle-show:</code>
            <ul>
              <li><span style="font-weight: bold">true</span>: The contents of the toggle will be visible by default when the page first loads. (Overridden by any 'sticky' states).</li>
              <li><span style="font-weight: bold">false</span>: The contents of the toggle will be hidden by default when the page first loads. (Overriden by any 'sticky' states).<br /><em>Note: rather than setting this option to false you may omit the option completely.</em></li>
            </ul>
          </li>
          <li><code>data-outoggle-id:</code>
            <ul>
              <li>This is only required when using the 'sticky' option. The ID must be unique within the scope of the current page (e.g. "support_returning_students")</li>
            </ul>
          </li>
        </ul>
        <h3>Toggle in action</h3>
        <div class="message-box info" data-outoggle="on">
            <a href="#">Click to Toggle</a>
            <div>
                <p>Oh, hi!</p>
            </div>
        </div>
        
        <h2 class="section" id="component-notify">Component: Notify</h2>
        <p>Easily display a quick notification message to the user. A common example of this would be letting the user know that changes made to a form have been saved. Notify requires no markup and is simply called by your Javascript.</p>
        <h3>Code</h3>
        <pre>&lt;script&gt;
	OUCommon.notify.show( 'Changes Saved!' );
&lt;/script&gt;</pre>
        <h3>Notify in action</h3>
        <div class="message-box info">
        	<a href="#" onclick="OUCommon.notify.show( 'This is the notify component in action!' );return false;">Click to notify</a>
        </div>
        
        
        <h2 class="section" id="component-link">Component: Link</h2>
        <p>Automatically mark-up certain types of links.</p>

        <h3>External</h3>
        <p>External links will have an additional link appended next to them giving the user the option to open the link in a new window or tab.</p>
        <p>To mark a link as external simply add the <code>data-oulink="external"</code> attribute to the <code>&lt;a&gt;</code> tag.</p>
        <p>The link that is appended to the end will have a class of <code>oulink-external</code> for additional styling.</p>
        
        <pre>&lt;a href=&quot;http://external-url.com&quot; data-oulink=&quot;external&quot;&gt;External URL&lt;/a&gt;</pre>
        
        <div class="message-box info">
        	<a href="http://www.lolcats.com/" data-oulink="external">External URL</a>
        </div>
        
        <h3>Anchor</h3>
        <p>Anchor links (href=&quot;#anchor&quot;) will use a smooth scroll animation to scroll users to an anchor point on the current page, rather than a more confusing 'jump' that occurs by default.</p>
        <p>To mark a link as an anchor link simply add the <code>data-oulink="anchor"</code> attribute to the <code>&lt;a&gt;</code> tag.</p>
        
        <pre>&lt;a href=&quot;#anchor&quot; data-oulink=&quot;anchor&quot;&gt;Anchor Link&lt;/a&gt;</pre>
        
        <div class="message-box info">
        	<a href="#component-link" data-oulink="anchor">Anchor Link</a>
        </div>
        
        <p>If a hash is passed to the page URL when it loads the smooth scroll animation will run automatically.</p>
        
        <h3>Helpful info links</h3>
        <p>Links may have additional, helpful text (hints, etc.) assigned to them by using the <code>data-ouinfo</code> attribute.</p>
        <p>This text will be hidden by default, but can be made visible by the user if they click on the help button that will automatically be placed to the right of the link.</p>
        <p>For additional styling - the help button will have the class name <code>oulink-info</code>, and the paragraph will have the class name <code>oulink-info-paragraph</code>.</p>
        
        <pre>&lt;a href=&quot;#&quot; data-ouinfo=&quot;Helpful text goes here.&quot;&gt;Click here&lt;/a&gt;</pre>
        
        <p><em><strong>Note:</strong> The <code>data-ouinfo</code> attribute can be applied to any DOM element - not just anchor elements.</em></p>
        
        <div class="message-box info">
        	<a href="#" data-ouinfo="Helpful text goes here.">Click here</a>
        </div>
        
        
        <h2 class="section" id="glyph-icons">Glyph Icons</h2>
        <p>A full set of high-quality glyph icons for use in websites and applications. See <a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">Font Awesome</a> (3.2.1) for a complete list of icons and use examples.</p>
        
        
        <h2 class="section" id="debugging">Debugging</h2>
        <p>Developers can benefit from using debugging functions that will automatically 'switch off' in the live environment. This allows developers to add useful debug information into their code that doesn't need to be removed before sites go live</p>

        <p>The OUCommon Debug object can be accessed either by referencing <code>OUCommon.Debug</code>, or the shorthand <code>OUDebug</code>.</p>
        
        <h3>Examples</h3>
        
        <ul>
            <li><code>OUDebug.log( 'Hello world' );</code><br />This will send a message to the browser's debug console if debugging is on.</li>
            <li><code>OUDebug.warn( 'Oops!' );</code><br />This will send a warning message (in the form of an alert popup) to the browser. If multiple calls are made to this method before the page has finished loading, the messages will be grouped together and shown as one single warning.</li>
        </ul>
        
        <h3>Error checking</h3>
        
        <p>OUCommon Debug will automatically check for errors in the page markup. It will warn in the following cases:</p>
        
        <ul>
            <li>Multiple DOM elements with the same id attribute.</li>
            <li>Multiple Toggle elements with the same outoggle-id.</li>
        </ul>
        
        
        
        <h2 class="section" id="dependencies">Dependencies</h2>
        <p>OU Common has a set of dependencies that must be loaded before using OU Common.<br /><strong>If OU Common is loaded using the <em>OUCommon-js.php</em> script, this file is loaded automatically.</strong></p>
        <p>If needed, these dependencies can be loaded manually by calling the isolated dependencies script:</p>
        
        <pre>&lt;script src="/ldt_shared/oufabric/oucommon/v1/gui/OUCommon-dependencies.js"&gt;&lt;/script&gt;</pre>
        
        <h3>List of dependencies:</h3>
        
        <ul>
          <li>In-house jQuery plugins
            <ul>
                <li><code>$.isMobile()</code><br />Returns a boolean based on whether or not the user-agent string matches one of: Android, webOS, iPhone, iPad, iPod, BlackBerry</li>
                <li><code>$.isTouchDevice()</code><br />Returns a boolean based on whether or not the browser supports touch events.</li>
                <li><code>$( '#myElement' ).isScrolledIntoView()</code><br />Returns a boolean based on whether or not the given element is visible on the page.</li>
            </ul>
          </li>
          <li><a href="https://github.com/carhartl/jquery-cookie" target="_blank">jQuery Cookie</a></li>
          <li><a href="https://github.com/douglascrockford/JSON-js" target="_blank">JSON2.js</a></li>
          <li><a href="http://gsgd.co.uk/sandbox/jquery/easing/" target="_blank">jQuery Easing</a></li>
          <li><a href="https://github.com/stephband/jquery.event.move" target="_blank">jQuery event.move</a></li>
          <li><a href="https://github.com/stephband/jquery.event.swipe" target="_blank">jQuery event.swipe</a></li>
        </ul>
   </div>


<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oucommon/v1/load-css.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oucommon/v1/load-js.php' );
?>

<script>
$( function() {
	OUCommon.load();
} );
</script>

<?php
$OUDocs->printFoot();
?>