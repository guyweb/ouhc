/* 
 * OU Video
 * Author: Jack Chapple
 */

( function( $ ) {

	var jQuery = $;

	var OUVideo = {
		flowplayerSwfUri: '/ldt_shared/oufabric/plugins/ouvideo/v1/flowplayer-5/flowplayer.swf',
		
		isIphone: ( navigator.userAgent.toLowerCase().indexOf( 'iphone' ) > -1 ),
		isIpod: ( navigator.userAgent.toLowerCase().indexOf( 'ipod' ) > -1 ),
		
		tabToggle: function() {
			var $this = $(this);
			var $tabDiv = $this.data( 'tabDiv' );
			
			if ( $tabDiv.css( 'display' ) == 'none' ) {
				$tabDiv.parent().children( 'div' ).hide();
				$tabDiv.show();
	
				$this.parent().parent().children( 'li' ).removeClass( 'active' );
				$this.parent().addClass( 'active' );
				
				if ( ! OUVideo.isIphone && ! OUVideo.isIpod ) {
					$this.parents( 'div.ouvideo' ).find( '.player' ).each( function() {
						var flowplayer = $(this).data( 'flowplayer' );
						
						if ( typeof flowplayer == 'object' && !! flowplayer && !! flowplayer['pause'] ) {
							flowplayer.pause();
						}
					} );
				}
			}
			
			return false;
		},
		
		transcriptToggle: function() {
			var $this = $(this);
			var $transcriptDiv = $this.data( 'transcriptDiv' );
			
			if ( $transcriptDiv.css( 'display' ) == 'none' ) {
				$this.text( 'Hide transcript' );
				$transcriptDiv.slideDown( 300 );
			} else {
				$this.text( 'Show transcript' );
				$transcriptDiv.slideUp( 300 );
			}
			
			return false;
		}
	};
	
	
	// A little hack to stop YouTube videos appearing above inline fullscreen videos
	$('.ouvideo iframe').each(function() {
	  var fixed_src = $(this).attr('src') + '&amp;wmode=opaque';
	  $(this).attr('src', fixed_src);
	});
	
	// OU Video jQuery function
	$.fn.ouvideo = function( conf ) {
		return this.each( function() {
			var $container = $(this);
			var $children = $container.children();
			var $tabsUl = $( '<ul class="tabs" />' );
			
			$children.each( function( i ) {
				var $this = $(this);
				var isActiveDiv = false;
				
				// Determine if this is the 'active' default video
				if ( window.location.hash && $this.attr( 'id' ) && $container.find( 'div' + window.location.hash ).size() > 0 ) {
					isActiveDiv = ( $this.attr( 'id' ) == window.location.hash.substring( 1 ) );
				} else {
					isActiveDiv = ( i == 0 );
				}
				
				if ( ! isActiveDiv ) {
					$this.hide();
				}
				
				// Create navigation tab
				
				var $tabLi = $( '<li />' );
				
				$( '<a href="#' + $this.attr( 'id' ) + '"></a>' )
					.html( $this.children( '.tab-title' ).html() )
					.data( 'tabDiv', $this )
					.click( OUVideo.tabToggle )
					.appendTo( $tabLi );
				
				if ( isActiveDiv ) {
					$tabLi.addClass( 'active' );
				}
				
				$tabLi.appendTo( $tabsUl );
				
				// Create bottom duration text & transcript link
				
				var duration = $this.children( '.duration' ).hide().text();
				var $transcriptDiv = $this.children( '.transcript' ).hide();
				var $infoBtm = $( '<div class="info-btm" />' );
				
				if ( duration ) {
					$( '<span class="time" />' ).text( duration ).appendTo( $infoBtm );
				}
				
				if ( $transcriptDiv.size() > 0 ) {
					$( '<a href="#" class="transcript">Show transcript</a>' )
						.data( 'transcriptDiv', $transcriptDiv )
						.click( OUVideo.transcriptToggle )
						.appendTo( $infoBtm );
				}
				
				$infoBtm.appendTo( $this );
								
				// Parse video element
				
				var $vidFirstElem = $this.find( '> .video > :first' );
				
				if ( $vidFirstElem.is( 'a' ) ) {
					$vidFirstElem.hide();
					var $vidImg = $vidFirstElem.children( 'img' );
					var vidRatio = ( $vidFirstElem.attr( 'data-ratio' ) ) ? $vidFirstElem.attr( 'data-ratio' ) : 9/16;
					
					var $player = $( '<div class="player play-button" />' )
						.attr( 'title', $this.children( '.tab-title' ).html() );
					
					var $video =
						$( '<video />' )
						.attr( 'poster', $vidImg.attr( 'src' ) )
						.appendTo( $player );
						
					$( '<source />' )
						.attr( 'src', $vidFirstElem.attr( 'href' ) )
						.attr( 'type', 'video/mp4' )
						.appendTo( $video );
					
					$player.insertAfter( $vidFirstElem );
					
					// Flowplayer Javascript API unavailable to iPod / iPhone
					if ( ! OUVideo.isIphone && ! OUVideo.isIpod ) {

						var flowplayerConf = {
							'swf': OUVideo.flowplayerSwfUri,
							'flashfit': true,
							'native_fullscreen': true,
							'ratio': vidRatio,
							'splash': true,
							'tooltip': false,
							'engine': 'html5'
						};

						if ( !! conf && !! conf.googleAnalytics ) flowplayerConf.analytics = conf.googleAnalytics;

						$player.flowplayer( flowplayerConf );
						
						// Append clickable anchor to play video (for accessibility)
						$( '<a />' )
							.attr( 'title', 'Play video' )
							.attr( 'href', '#' )
							.addClass( 'play-accessible' )
							.click( function( evt ) {
								evt.preventDefault();
								
								var $fpUi = $( this ).siblings( 'div.fp-ui' ); 
								
								$fpUi.click();
								
								setTimeout( function() {
									$fpUi.focus();
								}, 500 );
							} )
							.appendTo( $player );
					}
					
					// Help instructions
					//$player.after('<div class="help">(Click on the video to Play/Pause)</div>');
					
					// Disable fullscreen mode in IE7 because of bugs
					if ($.browser.msie && parseInt($.browser.version, 10) === 7) $('.ouvideo .fp-fullscreen').hide();
				}
			} );
			
			$tabsUl.prependTo( $(this) );
		} );
	};

} )( $1_10_2 );