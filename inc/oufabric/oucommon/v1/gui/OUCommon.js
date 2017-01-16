// OUCommon.js


( function( $ ) {

	window.OUCommon = {
		
		Models: {},
		
		_cache: {
			loadedComponents: []
		},
		
		_init: function() {
			OUCommon.Debug.init();
		},
		
		load: function( components ) {
			var funcMap = {
				'toggle':	OUCommon.toggle.init,
				'link':		OUCommon.link.init
			};
			
			if ( typeof components != 'undefined' && $.isArray( components ) ) {
				components = $.map( components, function( item, index ) {
					return item.toLowerCase();
				} );
			}
			else {
				components = $.map( funcMap, function( item, index ) {
					return index;
				} );
			}
			
			$( components ).each( function( i, componentName ) {
				if ( $.inArray( componentName, OUCommon._cache.loadedComponents ) > -1 ) {
					OUCommon.Debug.warn( 'Library already loaded: ' + components[i] );
					return true; // continue;
				}
				
				if ( typeof funcMap[ componentName ] == 'function' ) {
					funcMap[ componentName ]();
					OUCommon._cache.loadedComponents.push( componentName );
					OUCommon.Debug.log( 'Library loaded: ' + componentName );
				}
				else {
					OUCommon.Debug.warn( 'Unknown library: ' + componentName );
				}
			} );
		},
		
		Debug: {
			msgs: [],
			windowLoaded: false,
			
			init: function() {
				OUCommon.Debug.DOMErrors.doCheck();
				$( window ).on( 'load', OUCommon.Debug._evtWindowLoad );
			},
			
			log: function( msg ) {
				if ( typeof console != 'undefined' && OUCommon.Debug.isDebug() ) {
					console.log( msg );
				}
			},
			
			warn: function( msg ) {
				if ( OUCommon.Debug.windowLoaded ) {
					OUCommon.Debug._alert( msg );
				} else {
					if ( OUCommon.Debug.isDebug() ) {
						OUCommon.Debug.msgs.push( msg );
					}
				}
			},
			
			isDebug: function() {
				if ( typeof ouGlobals == 'object' && !! ouGlobals.isDebug ) {
					return true;
				}
				
				return false;
			},
			
			_alert: function( msg ) {
				alert( "OU Common JS Warnings:\n\n" + msg );
			},
			
			_evtWindowLoad: function() {
				if ( OUCommon.Debug.msgs.length > 0 ) {
					var msgString = '';
					
					$( OUCommon.Debug.msgs ).each( function( i, msg ) {
						msgString += "- " + msg + "\n";
					} );
					
					OUCommon.Debug._alert( msgString );
				}
				
				OUCommon.Debug.windowLoaded = true;
			},
			
			DOMErrors: {
				doCheck: function() {
					if ( ! OUCommon.Debug.isDebug() ) {
						return false;
					}
					
					OUCommon.Debug.DOMErrors.checkDuplicateIDs();
				},
				
				checkDuplicateIDs: function() {
					$( '[id]' ).each( function(){
						var ids = $( '[id="' + this.id + '"]' );
						
						if ( ids.length > 1 && ids[0] == this ) {
							OUCommon.Debug.warn( 'Multiple DOM IDs #' + this.id );
						}
					} );
				}
			}
		},
		
		// Function to toggle a content div below a link
		toggle: {
			'classId': 'outoggle-expanded', 
			
			'cookieId': 'OUCommon-outoggle', 
			
			'cookie': {}, 
			
			'init': function() {
				$( window ).bind( 'hashnavigate', OUCommon.toggle._evtHashNavigate );
				$( 'div[data-outoggle]' ).find( 'a:first' ).click( OUCommon.toggle.doToggle );
				
				$.cookie.json = true;
				OUCommon.toggle.cookie = $.cookie(OUCommon.toggle.cookieId) || {};
				
				OUCommon.toggle._checkDuplicateIds();
				
				$.each( $( 'div[data-outoggle]' ), OUCommon.toggle.wrapperSetup );
			}, 
			
			wrapperSetup: function() {
				var $this = $(this);
				var id = $this.attr('data-outoggle-id');
				var $trigger = $this.find('a:first');
				var isSticky = ($this.attr('data-outoggle') == "sticky") ? true : false;
				
				if ( typeof id != 'undefined' && typeof ouGlobals == 'object' && !! ouGlobals.urlHash ) {
					id = ouGlobals.urlHash + '-' + id;
					$this.attr( 'data-outoggle-id', id );
				}
				
				var $content = $this.children('div:first');
				var currentState;
				
				if ( isSticky ) {
					if ( typeof id == 'undefined' ) {
						OUCommon.Debug.warn( 'data-outoggle-id must be set if using sticky mode' );
					}
					
					if (!!OUCommon.toggle.cookie[id] && OUCommon.toggle.cookie[id] == 'v') currentState = "expanded";
					else if (!!OUCommon.toggle.cookie[id] && OUCommon.toggle.cookie[id] == 'h') currentState = "collapsed";
					else if ($this.attr('data-outoggle-show') == "true") currentState = "expanded";
				}
				else {
					if ($this.attr('data-outoggle-show') == "true") currentState = "expanded";	
				}
				
				if (currentState == "expanded") {
					// Expanded
					$trigger.addClass(OUCommon.toggle.classId);
				}
				else {
					// Collapsed
					$content.hide();
				}
			},
			
			doToggle: function() {			
				var $this = $(this);
				var $wrapper = $this.closest('div[data-outoggle]');
				var $id = $wrapper.attr('data-outoggle-id');
				var $mode = $wrapper.attr('data-outoggle');
				
				var $content = $wrapper.children('div:first').slideToggle('fast');
				$this.toggleClass(OUCommon.toggle.classId);
				
				if ($mode == "sticky") {
					var toggleState = ($this.hasClass(OUCommon.toggle.classId)) ? 'v' : 'h';
					OUCommon.toggle.cookie[$id] = toggleState;
					$.cookie(OUCommon.toggle.cookieId, OUCommon.toggle.cookie);
				}
				
				return false;
			},
			
			_evtHashNavigate: function( e, hashTarget ) {
				var $hashTarget = $( hashTarget );
				var $toggleWrapper = $hashTarget.closest( 'div[data-outoggle]' );
				
				if ( $toggleWrapper.size() == 1 ) {
					var $content = $toggleWrapper.children('div:first');
					
					if ( ! $content.is( ':visible' ) ) {
						$content.show();
						$toggleWrapper.find( 'a:first' ).addClass( OUCommon.toggle.classId );
					}
				}
			},
			
			_checkDuplicateIds: function() {
				if ( ! OUCommon.Debug.isDebug() ) {
					return false;
				}
				
				$( 'div[data-outoggle-id]' ).each( function(){
					var $this = $(this);
					var toggleId = $this.attr( 'data-outoggle-id' );
					var ids = $( 'div[data-outoggle-id="' + toggleId + '"]' );
					
					if ( ids.length > 1 && ids[0] == this ) {
						OUCommon.Debug.warn( 'Multiple Toggle IDs: ' + toggleId );
					}
				} );
			}
		}, 
		
		
		// Function to display a yellow notification box at the top of the page
		notify: {
			show: function(msg) {
				var $notification = $('<div />')
					.addClass( 'oucommon-notification-box' )
					.text( msg )
					.appendTo( 'body' );
				
				var topOut = '-' + $notification.outerHeight() + 'px';
				
				$notification
				.css( 'top', topOut )
				.animate( {
					'top': '20px'
				}, 300 )
				.delay( 1700 )
				.animate( {
					'top': topOut
				}, 300, function() {
					$(this).remove();
				} );
			}
		},
		
		
		// Function to enhance links, including handling external links
		link: {
			init: function() {
				$( 'a[data-oulink="external"], *[data-ouinfo]' ).each( function( i ) {
					var $this = $(this);
					
					var ariaId = 'oucommon-linkSpan-' + i;
					
					if ( $this.is( 'a' ) ) {
						$this.data( 'ariaId', ariaId );
						$this.html( '<span id="' + ariaId + '">'  + $this.text() + '</span>' );
					}
				} );
				
				OUCommon.link.moreText();
				OUCommon.link.external();
				OUCommon.link.anchor();
				OUCommon.link.signin();
			},
			
			external: function() {
				var $links = $( 'a[data-oulink="external"]' );
				
				$links.each( function() {
					var $this = $(this);
					
					var $link = $('<a />')
						.attr( {
							'href': $this.attr('href'), 
							'target': '_blank', 
							'title': 'Click here to open this link in a new window or tab',
							'aria-labelledby': $this.data( 'ariaId' )
						} )
						.text( 'Open in new window' )
						.addClass( 'oulink-external' )
						.insertAfter( $this );
				} );
			},
			
			moreText: function() {
				var $links = $( '*[data-ouinfo]' );
				
				$.each( $links, function() {
					var $this = $(this);
					
					var $info = $( '<p />' )
						.text( $this.attr( 'data-ouinfo' ) )
						.addClass( 'oulink-info-paragraph' )
						.attr( 'aria-hidden', 'true' )
						.hide();
					
					var $link = $( '<a />' )
						.data( '$info', $info )
						.attr( {
							'href': '#', 
							'title': 'More info'
						} )
						.text( 'More info' )
						.addClass( 'oulink-info' )
						.click( function() {
							var $info = $(this).data( '$info' );
							
							$info.slideToggle( 150, function() {
								var ariaHidden = ( $(this).css( 'display' ) == 'block' ) ? 'false' : 'true';
								$info.attr( 'aria-hidden', ariaHidden );
							} );
							return false;
						} )
						.insertAfter( $this );
						
					if ( !! $this.data( 'ariaId' ) ) {
						$link.attr( 'aria-labelledby', $this.data( 'ariaId' ) );
					}
						
					$info.insertAfter( $link );
				} );
			},
			
			anchor: function() {
				$( 'a[href^="#"]' ).on( 'click', OUCommon.Events.anchorHandler );
				$( window ).on( 'load', OUCommon.Events.anchorHandler );
			},
			
			signin: function() {
				$( 'a[data-oulink="signin"]' ).attr( 'href', $( '#ou-signin2' ).attr( 'href' ) );
			}
		},
		
		
		// Event handlers
		Events: {
			anchorHandler: function( e ) {
				var hashName = "",
					$currentTarget = $( e.currentTarget ),
					smoothScroll = true;
				
				// If a link was clicked...
				if ( $currentTarget.is( 'a' ) && e.type == "click" ) {			
					hashName = $(this).attr('href').substring(1);
					
					if ( ! $currentTarget.is( 'a[data-oulink="anchor"]' ) ) {
						smoothScroll = false;
					}
				}
				
				// If a hash was passed into the URL...
				else if ( $.isWindow( e.currentTarget ) && e.type == "load" ) {
					if ( window.location.hash.substring( 0, 1 ) == '#' ) {
						hashName = window.location.hash.substring( 1 );
					}
				}
				
				if ( hashName == "" ) {
					return false;
				}
				
				var $hashTarget = $( '#' + hashName + ', [name=' + hashName + ']' );	
				
				if ( $hashTarget.size() == 1 ) {
					// Dispatch our custom event
					$currentTarget.trigger( 'hashnavigate', $hashTarget );
					
					if ( smoothScroll ) {
						e.preventDefault();
						
						// Do the rude!
						$( 'html, body' ).animate( {
							scrollTop: ( $hashTarget.offset().top - 50 ) + 'px'
						}, 230 );
					}
				}
			}
		}
		
	};
	
	// Create shorthand reference for OUDebug
	window.OUDebug = OUCommon.Debug;
	
	
	
	
	OUCommon.Factory = {
		
		USER_TYPE_STAFF: 'staff',
		USER_TYPE_STUDENT: 'student',
		USER_TYPE_TUTOR: 'tutor',
		USER_TYPE_VISITOR: 'visitor',
		USER_TYPE_SELFREG: 'selfreg',
		
		dispenseUser: function( data ) {
			if ( ! !! data ) {
				var data = {};
			}
			
			switch ( data.type ) {
				case OUCommon.Factory.USER_TYPE_STAFF:
					return new OUCommon.Models.Staff( data );
					break;
				case OUCommon.Factory.USER_TYPE_STUDENT:
					return new OUCommon.Models.Student( data );
					break;
				case OUCommon.Factory.USER_TYPE_TUTOR:
					return new OUCommon.Models.Tutor( data );
					break;
				case OUCommon.Factory.USER_TYPE_VISITOR:
					return new OUCommon.Models.Visitor( data );
					break;
				case OUCommon.Factory.USER_TYPE_SELFREG:
					return new OUCommon.Models.SelfReg( data );
					break;
				default:
					return new OUCommon.Models.User( data );
					break;
			}
		}
		
	};
	
	
	/************************************************/
	
	/**
	 * Represents a generic User
	 * @constructor
	 */
	OUCommon.Models.User = function( data ) {
		if ( ! !! data ) {
			return;
		}
		
		this.type = data.type;
		this.id = data.id;
		this.oucu = data.oucu;
		this.displayName = data.displayName;
		this.authIDs = data.authIDs;
		
		if ( 'idAuthentic' in data ) this.idAuthentic = data.idAuthentic;
		if ( 'isAliasing' in data ) this.isAliasing = data.isAliasing;
	};
	
	/**
	 * Gets the user's first name
	 * @returns {String}
	 */
	OUCommon.Models.User.prototype.getFirstName = function() {
		return $.trim( this.displayName.split( ' ' )[0] );
	};
	
	/************************************************/
	
	/**
	 * Represents a Student user
	 * @constructor
	 */
	OUCommon.Models.Student = function( data ) {
		OUCommon.Models.User.apply( this, arguments );
		
		this.framework = data.framework;
		this.pricingCode = data.pricingCode;
		this.level = data.level;
		this.status = data.status;
		
		var modules = [];
		var qualifications = [];
		
		if ( data.modules ) {
			$.each( data.modules, function() {
				modules.push( new OUCommon.Models.Module( this ) );
			} );
		}
		
		if ( data.qualifications ) {
			$.each( data.qualifications, function() {
				qualifications.push( new OUCommon.Models.Qualification( this ) );
			} );
		}
		
		this.modules = modules;
		this.qualifications = qualifications;
	};
	
	OUCommon.Models.Student.prototype = new OUCommon.Models.User();
	
	/************************************************/
	
	/**
	 * Represents a Tutor user
	 * @constructor
	 */
	OUCommon.Models.Tutor = function( data ) {
		OUCommon.Models.User.apply( this, arguments );
		
		this.tutorGroups = data.tutorGroups;
		this.isDeferred = data.isDeferred;
	};
	
	OUCommon.Models.Tutor.prototype = new OUCommon.Models.User();
	
	/************************************************/
	
	/**
	 * Represents a Staff user
	 * @constructor
	 */
	OUCommon.Models.Staff = function( data ) {
		OUCommon.Models.User.apply( this, arguments );
	};
	
	OUCommon.Models.Staff.prototype = new OUCommon.Models.User();
	
	/************************************************/
	
	/**
	 * Represents a Visitor user
	 * @constructor
	 */
	OUCommon.Models.Visitor = function( data ) {
		OUCommon.Models.User.apply( this, arguments );
	};
	
	OUCommon.Models.Visitor.prototype = new OUCommon.Models.User();
	
	/************************************************/
	
	/**
	 * Represents a SelfReg user
	 * @constructor
	 */
	OUCommon.Models.SelfReg = function( data ) {
		OUCommon.Models.User.apply( this, arguments );
	};
	
	OUCommon.Models.SelfReg.prototype = new OUCommon.Models.User();
	
	/************************************************/
	
	/**
	 * Represents a Qualification
	 * @constructor
	 */
	OUCommon.Models.Qualification = function( data ) {
		this.code = data.code;
		this.title = data.title;
		this.description = data.description;
		this.urlInfo = data.urlInfo;
		this.level = data.level;
		this.framework = data.framework;
		this.minAwardPoints = data.minAwardPoints;

		var subjects = [];
		
		$.each( data.subjects, function() {
			subjects.push( new OUCommon.Models.Subject( this ) );
		} );
		
		this.subjects = subjects;
	};
	
	/************************************************/
	
	/**
	 * Represents a Module
	 * @constructor
	 */
	OUCommon.Models.Module = function( data ) {
		this.name = data.name;
		this.code = data.code;
		this.level = data.level;
	};
	
	/************************************************/
	
	/**
	 * Represents a Subject
	 * @constructor
	 */
	OUCommon.Models.Subject = function( data ) {
		this.id = data.id;
		this.title = data.title;
		this.uri = data.uri;
	};
	
	/************************************************/
	
	
	
	$( OUCommon._init );
	
} )( $1_10_2 );