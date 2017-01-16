// OU Audio JS Plugin
// Author: Ben Gurney (ben.gurney@open.ac.uk)

( function( $ ) {
	
	var jQuery = $;
	
	var OUAudio = {
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
		},
		
		trackSelected: function($this) {
			$this.parents('div.ouaudio').find('li a').removeClass('selected');			
			$this.addClass('selected');
			
			var $ouaudio = $this.parents('.ouaudio');
			var $transcriptWrapper = $ouaudio.children('.transcriptWrapper');
			
			if ($transcriptContent = $this.parents('li').next('li.transcript').html()) {				
				$transcriptWrapper.slideDown();
				$transcriptWrapper.children('div.transcript').html($transcriptContent);
			}
			else {
				$transcriptWrapper.slideUp();
			}
		}
	};
	
	(function($) {
			  
	  $('.ouaudio li.transcript').hide();
	  
	  $.fn.ouaudio = function(options) {
		  
		  return this.each(function(){
									
			var $this = $(this);
			var thisWidth = $this.width();
			var thisCount = $this.find('ul li').length;
			var $firstSong = $this.find('li a:first');
					
			var $player = $('<audio class="player" />')
								.attr({
									  'controls': 'controls', 
									  'src': $firstSong.attr('href'), 
									  'type': 'audio/mp3'
									  })
								.prependTo($this);
			
			var $transcriptWrapper = $('<div class="transcriptWrapper" />').appendTo($this);
			var $infoBtm = $('<div class="info-btm" />').appendTo($transcriptWrapper);
			var $transcriptDiv =  $('<div class="transcript" />').appendTo($transcriptWrapper).hide();
			
			var $transcriptBtn = 
						$('<a href="#" class="transcript">Show transcript</a>')
						.data('transcriptDiv', $transcriptDiv )
						.click(OUAudio.transcriptToggle)
						.appendTo($infoBtm);
			
			var playerTarget = $this.find('.player');
			var player = new MediaElementPlayer(playerTarget, { 
				pluginPath: '/ldt_shared/oufabric/plugins/ouaudio/v1/mejs/build/',
				audioWidth: '100%',
				plugins: ['flash'],
				enablePluginDebug: false,
				startVolume: 0.8, 
				enableAutosize: true, 
				enableKeyboard: true 
			});
			
			$this.find('li a').on("click", function() {
				var $this = $(this);
	
				player.pause();
				player.setSrc($this.attr("href"));
				player.play();
				
				OUAudio.trackSelected($this);
			
				return false;
			});
			
			$transcriptWrapper.hide();
			OUAudio.trackSelected($firstSong);
		});
		
	  };
	})(jQuery);

} )( $1_10_2 );