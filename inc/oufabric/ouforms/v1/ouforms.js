( function( $ ) {
	
	window.OUForms = {
		'conditions': {},
		'currentScrollPos': 0, 
		
		'init': function() {
			$('.ouforms div.conditional-hidden').hide().attr('aria-hidden', 'true');
			OUForms.updateFieldsets();
							
			$('input:radio, select, input:checkbox').on("change", function() {
				OUForms.currentScrollPos = $(window).scrollTop();
				OUForms.updateConditional();
				OUForms.updateFieldsets();
				
				$('html,body').animate({ scrollTop: OUForms.currentScrollPos },0);
			});
			
			$( 'div.ouforms div.input-file > label:nth-child(3) > input' ).click( function( evt ) {
				$(this).parent().next().children( 'input' ).click();
			} );
			
			$( 'div.ouforms div.input-file input[type="file"]' ).click( function() {
				$(this).parent().prev().children( 'input' ).prop( 'checked', true );
			} );
			
			$( 'div.ouforms div.input-textarea textarea[maxlength]' ).each( function() {
				var $this = $(this);
				var length = parseInt( $this.attr('maxlength') );
				
				if ( length > 0 ) {
					$this.data('maxLength', length);
					
					var $p = $('<p />')
						.addClass('maxlengthCounter')
						.text(OUForms._getMaxlengthText($this))
						.insertAfter($this);
					
					$this
						.data('$p', $p)
						.removeAttr('maxlength')
						.keydown(OUForms._evtInputLimitCheck_down)
						.keyup(OUForms._evtInputLimitCheck_up);
				}
			});
			
			$('div.ouforms .title-container a.info, div.ouforms .label-container a.info').on('click', function() {
				$(this).next('div.toggle-info').toggle();
				return false;
			});
	
			$('div.ouforms div.label-container .toggle-info, div.ouforms div.title-container .toggle-info').hide();
	
			$('div.ouforms div.errors a').on('click', function() {
				OUForms.goToByScroll($(this).attr('href'));
				return false;
			});

			$('div.ouforms div.ou-honigfalle').attr('aria-hidden', 'true'); // Hide the honigfalle from screen readers	
		},
		
		'updateConditional': function() {
			$('div.ouforms div.conditional').hide().attr('aria-hidden', 'true');
			$('div.ouforms fieldset').show();
			
			$.each (OUForms.conditions, function(target,conditions) {
				$.each (conditions, function(i,v) {						
					var radioField = $('#fieldContainer_' + v.name + ' input:radio:checked:visible').val();
					var selectField = $('#fieldContainer_' + v.name + ' select:visible').val();
					
					// If condition is for a checkbox
					var checkboxMatch = false;
					if (v.name.indexOf('[') > 0) {
						v.id = v.name;
						v.id = v.id.replace('[', '_');
						v.id = v.id.replace(']', '');
						
						var checkboxField = $('#' + v.id);
						if (checkboxField.is(":checked:visible") && checkboxField.val() == v.value) checkboxMatch = true;
					}
	
					if (v.value == radioField || v.value == selectField || checkboxMatch) {
						$('#fieldContainer_' + target).show().attr('aria-hidden', 'false');
					}
				});
			});			
		},
		
		'updateFieldsets': function() {
			var fieldset = $('div.ouforms fieldset');
			
			$.each(fieldset, function() {
				if ($(this).children('.field-input:visible').size() < 1) $(this).hide();
			});
		}, 
		
		'goToByScroll': function(id) {
			$('html,body').animate({scrollTop: $(id).offset().top}, 'fast');
		},
		
		'_evtInputLimitCheck_up': function(event) {
			var $this = $(this),
				len = $this.val().length,
				limit = parseInt( $this.data('maxLength') ),
				exceeded = len > limit;
			
			$this.data('$p').text(OUForms._getMaxlengthText($this));
				
			if ( exceeded )
				$this.val( $this.val().substr(0,limit) );
		},
		
		'_evtInputLimitCheck_down': function(event) {
			var $this = $(this),
				len = $this.val().length,
				limit = parseInt( $this.data('maxLength') ),
				exceeded = len >= limit,
				code = event.keyCode;
			   
			if ( !exceeded )
				return;
			
			switch (code) {
				case 8:  // allow delete
				case 9:
				case 17:
				case 36: // and cursor keys
				case 35:
				case 37: 
				case 38:
				case 39:
				case 40:
				case 46:
					return;
	
				default:
					return false;
			}
		},
		
		'_getMaxlengthText': function($input) {
			var len = $input.val().length,
				limit = parseInt( $input.data('maxLength') ),
				remaining = limit - len;
				
			return remaining + ' character' + ( ( remaining == 1 ) ? '' : 's' ) + ' left.';
		}
	}
	
} )( $1_10_2 );