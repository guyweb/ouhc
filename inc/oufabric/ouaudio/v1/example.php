<!DOCTYPE html>
<html lang="en">
<head>
<title>OU Audio Example</title>

<link rel="stylesheet" href="http://www.open.ac.uk/includes/ouice/3/screen.css" media="screen, projection" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/plugins/ouaudio/v1/load-css.php'); ?>

</head>

<body>

<div class="ouaudio"> 
  <ul>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T154439_Track01-intro-music.mp3">Intro music</a></li>
    <li class="transcript">
      <blockquote>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut malesuada fringilla tellus, eget iaculis diam mollis sed. Nulla facilisi. Cras fringilla sem vel justo tincidunt imperdiet nec ac ligula. Ut cursus nulla ac lorem ullamcorper sollicitudin. Etiam eget nisi felis. In in erat libero, id sollicitudin felis. Suspendisse ac enim nec ipsum tincidunt egestas et sed quam. Quisque iaculis lectus ut erat porta sed consequat magna sollicitudin. Nunc eget risus odio. Sed sed velit quam, quis congue arcu. Nullam a ligula velit. Mauris sed nibh et purus egestas bibendum nec eget nulla. Nullam molestie magna et augue elementum rutrum.</p>
        <p>Mauris ultricies gravida mauris dictum porta. Suspendisse tempus volutpat tellus. Ut vitae dictum enim. Maecenas ac lorem elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Duis vitae dolor non erat pretium accumsan. Mauris tincidunt, risus non feugiat placerat, dui orci mollis ligula, vel aliquet odio justo in augue.</p>
      </blockquote>
    </li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T154605_Track02-Prologue.mp3">Prologue</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T154709_Track03-Act1-Scene-1.mp3">Act 1 Scene 1</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T154912_Track04-Act1-Scene-2.mp3">Act 1 Scene 2</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T155111_Track05-Act1-Scene-3.mp3">Act 1 Scene 3</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T155356_Track06-Act1-Scene-4.mp3">Act 1 Scene 4</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T155436_Track07-Act2-Scene-1.mp3">Act 2 Scene 1</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T155527_Track08-Act2-Scene-2.mp3">Act 2 Scene 2</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T155604_Track09-Act2-Scene-3.mp3">Act 2 Scene 3</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T155648_Track10-Act3-Chorus.mp3">Act 3 Chorus</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T155748_Track11-Act3-Scene-1.mp3">Act 3 Scene 1</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T155842_Track12-Act3-Scene2.mp3">Act 3 Scene 2</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T160643_Track01-Act4-Chorus.mp3">Act 4 Chorus</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T161332_Track02-Act4-Scence-1.mp3">Act 4 Scene 1</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T161431_Track03-Act4-Scene-2.mp3">Act 4 Scene 2</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T161640_Track04-Act5-Scene-1.mp3">Act 5 Scene 1</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T161748_Track05-Act-Scene-2.mp3">Act 5 Scene 2</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T162004_Track06-Epiloque.mp3">Epilogue</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T162100_Track07-Outro-music.mp3">Outro music</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T162202_Track08-Credits.mp3">Credits</a></li>   
  </ul>  
</div>

<div class="ouaudio"> 
  <ul>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T154439_Track01-intro-music.mp3">Intro music</a></li>
    <li><a href="http://podcast.open.ac.uk/feeds/2047/20120424T154605_Track02-Prologue.mp3">Prologue</a></li>  
  </ul>
</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/plugins/ouaudio/v1/load-js.php'); ?>
<script>
$(function() {
	$('.ouaudio').ouaudio();
});
</script>
</body>
</html>
