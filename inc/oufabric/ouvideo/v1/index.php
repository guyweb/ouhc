<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oudocs/OUDocs.php' );
$OUDocs = new \OUFabric\OUDocs( 'ouvideo', 'v1' );
$OUDocs->printHead();
?>


<div class="content">
    <h1>OU Video</h1>
    
    <p>OU Video is a video player component for use within OU web pages. It is based around the popular Flowplayer, with added functionality including multiple tabbed videos and embedded transcripts.</p> 
    
    <p><strong>OU Video requires that the <a href="/ldt_shared/oufabric/jquery/">OU Fabric jQuery 1.10.2</a> dependency is loaded before use.</strong></p>
    
    <!-- Page nav -->
      <ul class="pagenav">
      	 <li><a href="#key-features">Key features</a></li>
         <li><a href="#installation">Installation</a></li>
         <li><a href="#see-in-action">See it in action</a></li>
         <li><a href="#future-ideas">Future ideas</a></li>
      </ul>
      
    
    <h2 class="section" id="key-features">Key features</h2>

    <ul>
      <li>HTML5 video player in pure HTML and CSS</li>
      <li>Flash video player fallback for older browsers</li>
      <li>Multiple (tabbed) videos within one container</li>
      <li>Embedded transcripts</li>
      <li>Responsive design</li>
      <li>Automatic mark-up from semantic HTML</li>
      <li>Javascript degradable</li>
    </ul>


    
    <h2 class="section" id="installation">Installation</h2>
    
    <h3>Linked CSS:</h3>
    <p>Add the following PHP between the <code>&lt;head&gt;&lt;/head&gt;</code> tags of your page.</p>
    
    <pre>&lt;?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/plugins/ouvideo/v1/load-css.php' );
?&gt;</pre>
    
    <h3>Linked JavaScript:</h3>
    <p>Add the following PHP before the closing <code>&lt;/body&gt;</code> tag of your page (ensure jQuery is loaded before this).</p>
    
    <pre>&lt;?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/plugins/ouvideo/v1/load-js.php' );
?&gt;</pre>
    
    <h3>Build the HTML:</h3>
    <p>OU Video will automagically build the player component based on semantic HTML markup.</p>
    
    <ul>
      <li>The player is contained within a <code>&lt;div&gt;</code> with the class name <code>ouvideo</code> applied to it. Using this class name ensures that the player will be styled correctly.</li>
      <li>Each <code>ouvideo</code> container can hold multiple videos. Each child <code>&lt;div&gt;</code> within the container is regarded as a separate video.</li>
      <li>Each video <code>&lt;div&gt;</code> expects a set of properties - these are defined using <code>&lt;span&gt;</code> child blocks.</li>
      <li>An optional class of <code>ouvideo-alttabs</code> can be added to the parent <code>&lt;div&gt;</code> to force the tabs to become pills.</li>
    </ul>
    
    <p>Example:</p>
    
    <pre>&lt;div class="ouvideo">
  &lt;div>
    &lt;span class="tab-title">Video Tab Title&lt;/span>
    &lt;span class="description">Video description goes here.&lt;/span>
    &lt;div class="video">
      &lt;a href="http://podcast.open.ac.uk/feeds/new-to-ou-study/20100810T132854_advice-for-students.m4v">
        &lt;img src="http://placehold.it/560x315/" alt="" />
      &lt;/a>
    &lt;/div>
    &lt;span class="duration">1 min 22 sec&lt;/span>
    &lt;div class="transcript">
      &lt;blockquote>
        &lt;p>Lorem ipsum dolor sit amet, consectetur adipiscing elit...&lt;/p>
        &lt;p>Mauris ultricies gravida mauris dictum porta...&lt;/p>
      &lt;/blockquote>
    &lt;/div>
  &lt;/div>
  
  [ ... ]
&lt;/div></pre>
    
    <p>The order of the elements in the example above should be maintained for layout reasons.</p>
    
    <h3>Breakdown of video properties:</h3>
    <ul>
      <li><strong>tab-title</strong>: The title to be used for the video's tab.</li>
      <li><strong>description</strong>: A short description that will be shown above the video.</li>
      <li><strong>video</strong>: This can contain one of the following:
        <ul>
            <li>An <code>&lt;a&gt;</code> link with a <code>href</code> property that points to the URL of the video file. An optional <code>data-ratio=&quot;0.75&quot;</code> property should be added if the video is in a 4:3 ratio. Within this block, a placeholder <code>&lt;img&gt;</code> should be used for accessibility (see above example).</li>
            <li>Copy &amp; pasted third-party embed code, such as that provided by YouTube.</li>
        </ul>
      </li>
      <li><strong>duration</strong>: The duration of the video (e.g. <code>1 min 22 sec</code>)</li>
      <li><strong>transcript</strong>: The transcript for the video. This must be contained within the <code>&lt;blockquote&gt;&lt;/blockquote&gt;</code> tags</li>
    </ul>
    
    <h3>Call the plugin:</h3>
    <p>Call the plugin to turn your markup into a video player component.</p>
    
    <pre>&lt;script&gt;
	$1_10_2('.ouvideo').ouvideo();
&lt;/script&gt;</pre>
    
    <p>You can create multiple player components per page if you wish.</p>
    
    <h2 class="section" id="see-in-action">See it in action:</h2>
    <p>You can see an example of OU Video <a href="/ldt_shared/oufabric/plugins/ouvideo/v1/example.php" target="_blank">here</a>.</p>
    <p>OU Video is completely HTML/CSS driven so you can skin the player using your own CSS.</p>
    
    
    <h2 class="section" id="future-ideas">Future ideas</h2>
    
    <ul>
      <li>Timed captions overlaid on top of video, pulled from the transcript markup</li>
      <li>Interactive transcripts - clickable parts of the transcript allowing users to quickly seek to the corresponding part of the video</li>
      <li>A simple method to embed YouTube videos that will play within Flowplayer</li>
    </ul>
</div>



<?php
$OUDocs->printFoot();
?>