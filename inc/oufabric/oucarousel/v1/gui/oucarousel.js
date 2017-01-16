/*
 * OU Carousel
 * Jack Chapple / jc27976 / jchapple@gmail.com
 */

( function( $ ) {
	
	var OUCarousel = {
		setup: function( $container ) {
			var _settings = $container.data( 'settings' ),
				$ul 	  = $container.children( 'ul:first' ),
				$slides   = $ul.children( 'li' );
				
			if ( $slides.size() == 0 ) {
				alert( 'OUCarousel error - no <li> elements found.' );
			}
			
			if ( $.isMobile() ) {
				$container.addClass( 'ou-carousel-mobile' );
	
				$ul
				.on( 'movestart', function( e ) {
					// If the movestart heads off in a upwards or downwards
					// direction, prevent it so that the browser scrolls normally.
					if ((e.distX > e.distY && e.distX < -e.distY) ||
						(e.distX < e.distY && e.distX > -e.distY)) {
						e.preventDefault();
						return;
					}
					
					var posX = parseInt( $(this).css( 'margin-left' ) );
					$(this).data( 'movestart-posX', posX );
				} )
				.on( 'move', function( e ) {
					var $slides = $(this),
						startX	= $slides.data( 'movestart-posX' ),
						posX 	= startX + e.distX;
					
					OUCarousel._moveSlider( $slides, posX );
				} )
				.on( 'moveend', function( e ) {
					var $slides 	= $(this),
						$container	= $slides.parent(),
						curSlide	= $container.data( 'curSlide' ),
						startX		= $slides.data( 'movestart-posX' ),
						slideWidth	= $slides.children( 'li:first' ).width(),
						slideThresh = slideWidth / 3,
						distMeasure = ( e.distX > 0 ) ? e.distX : -e.distX,
						velocity	= ( e.velocityX < 0 ) ? -e.velocityX : e.velocityX;
					
					if ( distMeasure > slideThresh || velocity > 0.35 ) {
						var slideIncr = ( e.distX > 0 ) ? -1 : 1;
						
						OUCarousel.goSlide( {
							currentTarget: $container,
							nextSlide: curSlide + slideIncr,
							velocity: velocity,
							type: 'touch'
						} );
					} else {
						OUCarousel._moveSlider( $slides, startX, 0.12, 'ease-out' );
					}
				} );
			} else {
				var timer = new OUCarousel.Timer( function() {
					OUCarousel.goSlide( {
						currentTarget: $container,
						type: 'timer'
					} );
				}, _settings.timer );
				
				if ( ! $container.isScrolledIntoView() ) {
					timer.pause();
				}
				
				$( '<button />' )
					.addClass( 'btnPrevNext btnPrev' )
					.click( OUCarousel.goSlide )
					.appendTo( $container );
			
				$( '<button />' )
					.addClass( 'btnPrevNext btnNext' )
					.click( OUCarousel.goSlide )
					.appendTo( $container );
			}
			
			if ( ! isNaN ( parseFloat( $container.attr( 'data-ratio' ) ) ) ) {
				_settings.aspectRatio = parseFloat( $container.attr( 'data-ratio' ) );
				$container.data( 'settings', _settings );
			}
			
			if ( !! _settings.randomise ) {
				$slides = $slides.shuffle();
			}
			
			$container
				.addClass( 'ou-carousel-container' )
				.height( OUCarousel._calcContainerHeight( $container ) )
				.data( 'curSlide', 0 )
				.data( '_timer', timer )
				.mouseenter( function( evt ) {
					$( evt.currentTarget ).data( '_timer' ).pause();
				} )
				.mouseleave( function( evt ) {
					$( evt.currentTarget ).data( '_timer' ).resume();
				} );
			
			$ul.addClass( 'slider' );
			
			var $bullets = $( '<ul />' )
				.addClass( 'bullets' )
				.appendTo( $container );
			
			$slides.each( function( i ) {
				$(this).width( $container.width() );
				
				var $bullet = $( '<li />' )
					.click( OUCarousel.goSlide )
					.appendTo( $bullets );
				
				if ( i == 0 ) {
					$bullet.addClass( 'active' );
				}
			} );
			
			$container.addClass( 'loaded' );
			OUCarousel._evtWindowResize();
			
			$( window ).on( 'resize', OUCarousel._evtWindowResize );
		},
		
		goSlide: function( evt ) {
			var $carousel,
				$bullets,
				slideToNum = 0,
				$trigger = $( evt.currentTarget );
			
			if ( evt.type == 'click' ) {
				var $container = $trigger.parents( 'div.ou-carousel-container:first' );
				
				if ( $trigger.is( 'ul.bullets > li' ) ) {
					slideToNum = $trigger.index();
				}
				else if ( $trigger.is( '.btnPrevNext' ) ) {
					slideToNum = $container.data( 'curSlide' ) + ( $trigger.is( '.btnPrev' ) ? -1 : 1 );
				}
			}
			else if ( evt.type == 'timer' ) {
				$container = $trigger;
				slideToNum = $container.data( 'curSlide' ) + 1;
			}
			else if ( evt.type == 'touch' ) {
				$container = $trigger;
				slideToNum = evt.nextSlide;
			}
			
			var _settings 	= $container.data( 'settings' ),
				animSpeed	= _settings.speed,
				$slider		= $container.children( 'ul.slider' ),
				$bullets 	= $container.children( 'ul.bullets' ),
				slideWidth	= $container.width(),
				totalSlides = $slider.children( 'li' ).size(),
				offsetCur 	= $container.data( 'curSlide' ) * slideWidth,
				easing		= 'ease';
			
			if ( evt.type == 'touch' ) {
				animSpeed = parseInt( ( 1 - evt.velocity ) * 330 );
				
				if ( animSpeed < 170 ) {
					animSpeed = 170;
				}
				
				if ( slideToNum < 0 ) {
					slideToNum = 0
				}
				else if ( slideToNum + 1 > totalSlides ) {
					slideToNum = totalSlides - 1;
				}
			} else {
				if ( slideToNum < 0 ) {
					slideToNum = totalSlides - 1;
				}
				else if ( slideToNum + 1 > totalSlides ) {
					slideToNum = 0;
				}
			}
			
			var offsetNew = -( slideToNum * slideWidth );
			
			$bullets.children( 'li' ).removeClass( 'active' );
			
			$bullets.children( 'li:nth-child(' + ( slideToNum + 1 ) + ')' ).addClass( 'active' );
			
			$container.data( 'curSlide', slideToNum );
			
			OUCarousel._moveSlider( $slider, offsetNew, animSpeed / 1000, easing );
		},
		
		_calcContainerHeight: function( $container ) {
			return Math.round( $container.width() * $container.data( 'settings' ).aspectRatio ) + 34;
		},
		
		_evtWindowResize: function() {
			$( '.ou-carousel-container' ).each( function() {
				var $container 	= $(this),
					$slider		= $container.children( 'ul.slider' ),
					curSlide	= $container.data( 'curSlide' ),
					slideWidth 	= $container.width();
	
				$container.height( OUCarousel._calcContainerHeight( $container ) );
				
				$container.find( '> ul.slider > li' ).width( slideWidth );
				
				OUCarousel._moveSlider( $slider, -( slideWidth * curSlide ) );
			} );
		},
		
		_moveSlider: function( $slider, posX, speed, easing ) {
			if ( typeof speed == 'undefined' ) var speed = 0;
			if ( typeof easing == 'undefined' ) var easing = 'ease';
			
			$slider.css( {
				'transition': 'margin-left ' + speed + 's ' + easing,
				'margin-left': posX + 'px'
			} );
		},
		
		Timer: function( callback, delay ) {
			var timerId, start, remaining = delay;
			
			this.pause = function() {
				window.clearTimeout( timerId );
				remaining -= new Date() - start;
			};
			
			var resume = function() {
				start = new Date();
				timerId = window.setTimeout( function() {
					remaining = delay;
					resume();
					callback();
				}, remaining );
			};
			
			this.resume = resume;
			
			this.resume();
		}
	};
	
	
	$.fn.oucarousel = function( options ) {
		var settings = $.extend( {
			aspectRatio: 	0.575,
			timer: 			12000,
			speed: 			280,
			randomise:		true
		}, options );
		
		return this.each( function() {
			var $this = $( this );
			$this.data( 'settings', settings );
			OUCarousel.setup( $this );
		} );
	};
	
	$.fn.shuffle = function() {
		var allElems = this.get(),
		
			getRandom = function( max ) {
				return Math.floor( Math.random() * max );
			},
			
			shuffled = $.map( allElems, function() {
				var random = getRandom( allElems.length ),
					randEl = $( allElems[random] ).clone(true)[0];
					
				allElems.splice( random, 1 );
				
				return randEl;
		   } );
 
		this.each( function( i ) {
			$(this).replaceWith( $( shuffled[i] ) );
		} );
 
		return $( shuffled );
	};
	
	if ( ! $.isMobile() ) {
		setInterval( function() {
			$( '.ou-carousel-container' ).each( function() {
				var $this		= $(this),
					newState 	= $this.isScrolledIntoView(),
					oldState	= ( typeof $this.data( 'scrollViewState' ) == 'undefined' ) ? newState : $this.data( 'scrollViewState' );
				
				if ( oldState != newState ) {
					var timer = $this.data( '_timer' );
					
					if ( newState === true ) {
						OUCarousel._evtWindowResize();

						timer.resume();
					} else if ( newState === false ) {
						timer.pause();
					}
				}
				
				$this.data( 'scrollViewState', newState );
			} );
		}, 300 );
	}
	
} )( $1_10_2 );