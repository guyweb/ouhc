<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oudocs/OUDocs.php' );
$OUDocs = new \OUFabric\OUDocs( 'oucommon/docs', 'v1' );
$OUDocs->printHead();
?>


<div class="content">
    <h1>Docs</h1>
    
    <p>This page outlines how to install and use OU Common within your website or application.</p>
    
    <!-- Page nav -->
    <ul class="pagenav">
    	<li><a href="#usage">Usage</a></li>
        <li><a href="#environment">Environment handler</a></li>
    	<li><a href="#database">Database</a></li>
        <li><a href="#data-open">Data.open.ac.uk</a></li>
        <li><a href="#uri-cache">URI Cache</a></li>
        <li><a href="#sanitisation">Sanitisation</a></li>
    </ul>
    
    
    <h2 class="section" id="usage">Usage</h2>
    <p>To include OU Common in a website, simply use the following code:</p>
    <pre>&lt;?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oucommon/v1/OUCommon.php' );
?></pre>
    
    <h2 class="section" id="environment">Environment handler</h2>
    <p>OU Common will automatically detect the current environment (<code>dev</code>, <code>test</code> or <code>live</code>) and store this as a constant called <code>ENVIRONMENT</code>. If this constant has already been defined before OU Common is included then that value will be used instead of automatic detection.</p>
	<p>Automatic detection is done by looking for the <code>getenv('OUENV')</code> variable.</p>
    
    <h2 class="section" id="database">Database</h2>
    <p>OU Common provides a Database class for opening database connections. By default it uses the <code>PDO</code> class but also has support for legacy <code>mysql_connect</code>.</p>
    <p>OU Database should be used for all database connections as it has built in error handling that meets the usability standards of the OU.</p>
    
    <h3>Usage</h3>
    <p>OU Database should be instantiated and configured before any page output occurs, i.e. within the page or site Controller. A single reference for each database should be created at this point and used by any subsequent functions throughout the site. You should not instantiate OU Database inside any other methods.</p>

    <p><u>PHP Example: Instantiating OU Database</u></p>
    
    <pre>$db = new \OUFabric\OUCommon\Database( array(
  "host" => "aacsmysqlcluster.open.ac.uk", 
  "name" => "mydatabase", 
  "user" => "myuser", 
  "pass" => "mypass")
) );

// The PDO method
self::$db = $db->connect();

// ...or the mysql_connect method
// self::$db = $db->connectLegacy();
</pre>
    
    <h3>Defaults</h3>
    
    <p>OU Database is configured with default connection details for each development environment. The environment is automatically detected from the environment handler in OU Common. Any connection details that are passed into OU Database when it is instantiated will override any defaults that exist. As a minimum, the database name should be provided.</p>
    <p>The defaults are as follows. The corresponding username and passwords for each server are also stored as defaults:</p>
    <ul>
    <li><strong>Dev:</strong> crake.open.ac.uk</li>
    <li><strong>Test:</strong> piculet.open.ac.uk</li>
    <li><strong>Live:</strong> kagu.open.ac.uk</li>
    </ul>
    
    <h3>Accessing the database object</h3>
    
    <p><u>The PDO method</u></p>
    <pre>$stmt = MyClass::$db->prepare('SELECT ...');</pre>

    
    <p><u>The mysql_connect method</u></p>
    <pre>$link = MyClass::$db;<br />
$query = mysql_query('SELECT ...', $link);</pre>
    
    <h3>Error Handling</h3>
    <p>OU Database automatically handles database connection issues and by default will display the page located in <code>ldt_shared/oufabric/oucommon/v1/inc/dbErrorPage.php</code></p>
    <p>A custom error page can be defined for the site by setting the <code>$errorPage</code> static property inside the OU Database object. This must be done before instantiating the class, but is not required.</p>
    <pre>OUDatabase::$errorPage = "/fs1/WWW-db/learning/mysite/dbError.php";<br />
$db = new OUDatabase(...</pre>
    
    <h3>Error Logging</h3>
	<p>OU Database keeps a log of all connection errors in <code>ldt_shared/_data/logs/OUDatabase.log</code></p>
    
    
    <h2 class="section" id="data-open">Data.open.ac.uk</h2>
    <p>OU Common provides a <code>DataOpen</code> class to allow easy integration with the <a href="http://data.open.ac.uk/">data.open.ac.uk</a> service.  The OU Common <a href="#uri-cache">URI Cache</a> is made use of to improve reliability and performance.</p>
    <h3>getURI()</h3>
    <p>This method will request a data.open endpoint (URI) and return the raw XML response.</p>
    <pre>$path = 'course/m366';
$response = \OUFabric\OUCommon\DataOpen::getURI( $path );</pre>
    <h3>query()</h3>
    <p>Used to perform SPARQL queries and return the raw XML response.</p>
    <pre>// Query to find courses in Nigeria
$query = &lt;&lt;&lt;EOF
select distinct ?course where {
?course &lt;http://data.open.ac.uk/saou/ontology#isAvailableIn> &lt;http://sws.geonames.org/2328926/>.
?course a &lt;http://purl.org/vocab/aiiso/schema#Module>}
EOF;

$response = \OUFabric\OUCommon\DataOpen::query( $query );</pre>


    
    <h2 class="section" id="uri-cache">URI Cache</h2>
    <p>The <code>URICache</code> class is a simple class for retrieving the raw response from a given URI. It will automatically manage a locally cached copy of the response to improve performance and reliability.</p>
   	<h3>Usage</h3>
    <pre>
$uri = 'http://www.mysite.com/url/to/retrieve';

// Instantiate URICache
$cache = new \OUFabric\OUCommon\URICache( $uri );

if ( ! $data = $cache->get() ) {
	// No cached copy, retrieve live data
	$data = file_get_contents( $uri );
    
    // Update the cache
    $cache->set( $data );
}

// Do something with the data
echo $data;
</pre>
    <p>By default, cached data will expire 24 hours after being set. This expiration period can be adjusted by altering the <code>cacheExp</code> property.</p>
    <pre>$cache = new \OUFabric\OUCommon\URICache( $uri );
$cache->cacheExp = 1800; // in seconds</pre>
    
    
    <h2 class="section" id="sanitisation">Sanitisation</h2>
    <p>The sanitisation class provides methods for sanitising data.</p>
    <h3>inputStringSafe( $str, $nl2br = false )</h3>
    <p>Takes a raw string and automatically converts special characters to their HTML entities, regardless of the character set used. Optionally converts newline characters (carriage returns) to <code>&lt;br /></code> tags.</p>
    <pre>echo \OUFabric\OUCommon\Sanitisation::inputStringSafe( "Désinfectez-moi, s'il vous plaît!" );</pre>
	<p>Outputs:</p>
    <pre>D&amp;eacute;sinfectez-moi, s&amp;#039;il vous pla&amp;icirc;t!  </pre>
          
</div>



<?php
$OUDocs->printFoot();
?>