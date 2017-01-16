<!DOCTYPE html>
<html lang="en">
	<head>
		<title>OU Carousel</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
		<?php require_once('load-css.php'); ?>
        
        <style>
			#myCarousel {
				width: 100%;
				max-width: 600px;
			}
		</style>
        
		<?php require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/jquery/load-jquery-1.10.2.php' ); ?>
	</head>
	<body>
    	<div id="myCarousel" data-ratio="0.575">
            <ul>
                <li>
                    <a href="http://www.open.ac.uk/"><img src="http://placekitten.com/600/345" alt="" /></a>
                    <p>Slide 1</p>
                </li>
                <li>
                    <a href="http://www.google.com/"><img src="http://placekitten.com/599/345" alt="" /></a>
                    <p>This is a slide with <a href="#">some text</a> that is a bit more lengthy than the rest...</p>
                </li>
                <li>
                    <img src="http://placekitten.com/601/345" alt="" />
                    <p>Slide 3</p>
                </li>
                <li>
                    <img src="http://placekitten.com/602/345" alt="" />
                </li>
            </ul>
        </div>
		
        <script src="/ldt_shared/oufabric/oucommon/v1/gui/OUCommon-dependencies.js"></script>
		<?php require_once('load-js.php'); ?>
        
		<script>
			$1_10_2( '#myCarousel' ).oucarousel();
		</script>
	</body>
</html>