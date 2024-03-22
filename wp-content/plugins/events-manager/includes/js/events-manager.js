jQuery(document).ready( function($){

	// backcompat changes 6.x to 5.x
	if( $('#recurrence-frequency').length > 0  ){
		$('#recurrence-frequency').addClass('em-recurrence-frequency');
		$('.event-form-when .interval-desc').each( function(){
			this.classList.add(this.id);
		});
		$('.event-form-when .alternate-selector').each( function(){
			this.classList.add('em-' + this.id);
		});
		$('#recurrence-interval').addClass('em-recurrence-interval');
	}
	$('#em-wrapper').addClass('em');


	var load_ui_css = false; //load jquery ui css?
	/* Time Entry */
	$('#start-time').each(function(i, el){
		$(el).addClass('em-time-input em-time-start').next('#end-time').addClass('em-time-input em-time-end').parent().addClass('em-time-range');
	});
	if( $(".em-time-input").length > 0 ){
		em_setup_timepicker('body');
	}

	/*
	 * ADMIN AREA AND PUBLIC FORMS (Still polishing this section up, note that form ids and classes may change accordingly)
	 */
	//Events List
	//Approve/Reject Links
	$('.events-table').on('click', '.em-event-delete', function(){
		if( !confirm("Are you sure you want to delete?") ){ return false; }
		window.location.href = this.href;
	});
	//Forms
	$('#event-form #event-image-delete, #location-form #location-image-delete').on('click', function(){
		var el = $(this);
		if( el.is(':checked') ){
			el.closest('.event-form-image, .location-form-image').find('#event-image-img, #location-image-img').hide();
		}else{
			el.closest('.event-form-image, .location-form-image').find('#event-image-img, #location-image-img').show();
		}
	});
	//Event Editor
	//Recurrence Date Patterns
	$('.event-form-with-recurrence').each( function(){
		let recurring_form = $(this);
		recurring_form.on('change', '.em-recurrence-checkbox', function(){
			if( this.checked ){
				recurring_form.find('.em-recurring-text').each( function(){
					this.style.removeProperty('display');
				});
				recurring_form.find('.em-event-text').each( function(){
					this.style.setProperty('display', 'none', 'important');
				});
			}else{
				recurring_form.find('.em-recurring-text').each( function(){
					this.style.setProperty('display', 'none', 'important');
				});
				recurring_form.find('.em-event-text').each( function(){
					this.style.removeProperty('display');
				});
			}
		});
	});
	$('.event-form-with-recurrence .em-recurrence-checkbox').trigger('change');
	//Recurrence Warnings
	$('#event-form.em-event-admin-recurring').on('submit', function(event){
		var form = $(this);
		if( form.find('input[name="event_reschedule"]').first().val() == 1 ){
			var warning_text = EM.event_reschedule_warning;
		}else if( form.find('input[name="event_recreate_tickets"]').first().val() == 1 ){
			var warning_text = EM.event_recurrence_bookings;
		}else{
			var warning_text = EM.event_recurrence_overwrite;
		}
		confirmation = confirm(warning_text);
		if( confirmation == false ){
			event.preventDefault();
		}
	});
	//Buttons for recurrence warnings within event editor forms
	$('.em-reschedule-trigger').on('click', function(e){
		e.preventDefault();
		var trigger = $(this);
		trigger.closest('.em-recurrence-reschedule').find(trigger.data('target')).removeClass('reschedule-hidden');
		trigger.siblings('.em-reschedule-value').val(1);
		trigger.addClass('reschedule-hidden').siblings('a').removeClass('reschedule-hidden');
	});
	$('.em-reschedule-cancel').on('click', function(e){
		e.preventDefault();
		var trigger = $(this);
		trigger.closest('.em-recurrence-reschedule').find(trigger.data('target')).addClass('reschedule-hidden');
		trigger.siblings('.em-reschedule-value').val(0);
		trigger.addClass('reschedule-hidden').siblings('a').removeClass('reschedule-hidden');
	});
	// Event Status
	$('select[name="event_active_status"]').on('change', function(event){
		var selected = $(this);
		if( selected.val() == '0' ){
			var warning_text = EM.event_cancellations.warning.replace(/\\n/g, '\n');
			confirmation = confirm(warning_text);
			if( confirmation == false ){
				event.preventDefault();
			}
		}
	});
	//Tickets & Bookings
	if( $("#em-tickets-form").length > 0 ){
		//Enable/Disable Bookings
		$('#event-rsvp').on('click', function(event){
			if( !this.checked ){
				confirmation = confirm(EM.disable_bookings_warning);
				if( confirmation == false ){
					event.preventDefault();
				}else{
					$('#event-rsvp-options').hide();
				}
			}else{
				$('#event-rsvp-options').fadeIn();
			}
		});
		if($('input#event-rsvp').is(":checked")) {
			$("div#rsvp-data").fadeIn();
		} else {
			$("div#rsvp-data").hide();
		}
		//Ticket(s) UI
		var reset_ticket_forms = function(){
			$('#em-tickets-form table tbody tr.em-tickets-row').show();
			$('#em-tickets-form table tbody tr.em-tickets-row-form').hide();
		};
		//recurrences and cut-off logic for ticket availability
		if( $('#em-recurrence-checkbox').length > 0 ){
			$('#em-recurrence-checkbox').on('change', function(){
				if( $('#em-recurrence-checkbox').is(':checked') ){
					$('#em-tickets-form .ticket-dates-from-recurring, #em-tickets-form .ticket-dates-to-recurring, #event-rsvp-options .em-booking-date-recurring').show();
					$('#em-tickets-form .ticket-dates-from-normal, #em-tickets-form .ticket-dates-to-normal, #event-rsvp-options .em-booking-date-normal, #em-tickets-form .hidden').hide();
				}else{
					$('#em-tickets-form .ticket-dates-from-normal, #em-tickets-form .ticket-dates-to-normal, #event-rsvp-options .em-booking-date-normal').show();
					$('#em-tickets-form .ticket-dates-from-recurring, #em-tickets-form .ticket-dates-to-recurring, #event-rsvp-options .em-booking-date-recurring, #em-tickets-form .hidden').hide();
				}
			}).trigger('change');
		}else if( $('#em-form-recurrence').length > 0 ){
			$('#em-tickets-form .ticket-dates-from-recurring, #em-tickets-form .ticket-dates-to-recurring, #event-rsvp-options .em-booking-date-recurring').show();
			$('#em-tickets-form .ticket-dates-from-normal, #em-tickets-form .ticket-dates-to-normal, #event-rsvp-options .em-booking-date-normal, #em-tickets-form .hidden').hide();
		}else{
			$('#em-tickets-form .ticket-dates-from-recurring, #em-tickets-form .ticket-dates-to-recurring, #event-rsvp-options .em-booking-date-recurring, #em-tickets-form .hidden').hide();
		}
		//Add a new ticket
		$("#em-tickets-add").on('click', function(e){
			e.preventDefault();
			reset_ticket_forms();
			//create copy of template slot, insert so ready for population
			var tickets = $('#em-tickets-form table tbody');
			tickets.first('.em-ticket-template').find('input.em-date-input.flatpickr-input').each(function(){
				if( '_flatpickr' in this ){
					this._flatpickr.destroy();
				}
			}); //clear all datepickers, should be done first time only, next times it'd be ignored
			var rowNo = tickets.length+1;
			var slot = tickets.first('.em-ticket-template').clone(true).attr('id','em-ticket-'+ rowNo).removeClass('em-ticket-template').addClass('em-ticket').appendTo($('#em-tickets-form table'));
			//change the index of the form element names
			slot.find('*[name]').each( function(index,el){
				el = $(el);
				el.attr('name', el.attr('name').replace('em_tickets[0]','em_tickets['+rowNo+']'));
			});
			// sort out until datepicker ids
			let start_datepicker = slot.find('.ticket-dates-from-normal').first();
			if( start_datepicker.attr('data-until-id') ){
				let until_id = start_datepicker.attr('data-until-id').replace('-0', '-'+ rowNo);
				start_datepicker.attr('data-until-id', until_id);
				slot.find('.ticket-dates-to-normal').attr('id', start_datepicker.attr('data-until-id'));

			}
			//show ticket and switch to editor
			slot.show().find('.ticket-actions-edit').trigger('click');
			//refresh datepicker and values
			slot.find('.em-time-input').off().each(function(index, el){
				if( typeof this.em_timepickerObj == 'object' ){
					this.em_timepicker('remove');
				}
			}); //clear all em_timepickers - consequently, also other click/blur/change events, recreate the further down
			em_setup_datepicker(slot);
			em_setup_timepicker(slot);
			$('html, body').animate({ scrollTop: slot.offset().top - 30 }); //sends user to form
			check_ticket_sortability();
		});
		//Edit a Ticket
		$(document).on('click', '.ticket-actions-edit', function(e){
			e.preventDefault();
			reset_ticket_forms();
			var tbody = $(this).closest('tbody');
			tbody.find('tr.em-tickets-row').hide();
			tbody.find('tr.em-tickets-row-form').fadeIn();
			return false;
		});
		$(document).on('click', '.ticket-actions-edited', function(e){
			e.preventDefault();
			var tbody = $(this).closest('tbody');
			var rowNo = tbody.attr('id').replace('em-ticket-','');
			tbody.find('.em-tickets-row').fadeIn();
			tbody.find('.em-tickets-row-form').hide();
			tbody.find('*[name]').each(function(index,el){
				el = $(el);
				if( el.attr('name') == 'ticket_start_pub'){
					tbody.find('span.ticket_start').text(el.val());
				}else if( el.attr('name') == 'ticket_end_pub' ){
					tbody.find('span.ticket_end').text(el.val());
				}else if( el.attr('name') == 'em_tickets['+rowNo+'][ticket_type]' ){
					if( el.find(':selected').val() == 'members' ){
						tbody.find('span.ticket_name').prepend('* ');
					}
				}else if( el.attr('name') == 'em_tickets['+rowNo+'][ticket_start_recurring_days]' ){
					var text = tbody.find('select.ticket-dates-from-recurring-when').val() == 'before' ? '-'+el.val():el.val();
					if( el.val() != '' ){
						tbody.find('span.ticket_start_recurring_days').text(text);
						tbody.find('span.ticket_start_recurring_days_text, span.ticket_start_time').removeClass('hidden').show();
					}else{
						tbody.find('span.ticket_start_recurring_days').text(' - ');
						tbody.find('span.ticket_start_recurring_days_text, span.ticket_start_time').removeClass('hidden').hide();
					}
				}else if( el.attr('name') == 'em_tickets['+rowNo+'][ticket_end_recurring_days]' ){
					var text = tbody.find('select.ticket-dates-to-recurring-when').val() == 'before' ? '-'+el.val():el.val();
					if( el.val() != '' ){
						tbody.find('span.ticket_end_recurring_days').text(text);
						tbody.find('span.ticket_end_recurring_days_text, span.ticket_end_time').removeClass('hidden').show();
					}else{
						tbody.find('span.ticket_end_recurring_days').text(' - ');
						tbody.find('span.ticket_end_recurring_days_text, span.ticket_end_time').removeClass('hidden').hide();
					}
				}else{
					var classname = el.attr('name').replace('em_tickets['+rowNo+'][','').replace(']','').replace('[]','');
					tbody.find('.em-tickets-row .'+classname).text(el.val());
				}
			});
			//allow for others to hook into this
			$(document).triggerHandler('em_maps_tickets_edit', [tbody, rowNo, true]);
			$('html, body').animate({ scrollTop: tbody.parent().offset().top - 30 }); //sends user back to top of form
			return false;
		});
		$(document).on('change', '.em-ticket-form select.ticket_type', function(e){
			//check if ticket is for all users or members, if members, show roles to limit the ticket to
			var el = $(this);
			if( el.find('option:selected').val() == 'members' ){
				el.closest('.em-ticket-form').find('.ticket-roles').fadeIn();
			}else{
				el.closest('.em-ticket-form').find('.ticket-roles').hide();
			}
		});
		$(document).on('click', '.em-ticket-form .ticket-options-advanced', function(e){
			//show or hide advanced tickets, hidden by default
			e.preventDefault();
			var el = $(this);
			if( el.hasClass('show') ){
				el.closest('.em-ticket-form').find('.em-ticket-form-advanced').fadeIn();
				el.find('.show,.show-advanced').hide();
				el.find('.hide,.hide-advanced').show();
			}else{
				el.closest('.em-ticket-form').find('.em-ticket-form-advanced').hide();
				el.find('.show,.show-advanced').show();
				el.find('.hide,.hide-advanced').hide();
			}
			el.toggleClass('show');
		});
		$('.em-ticket-form').each( function(){
			//check whether to show advanced options or not by default for each ticket
			var show_advanced = false;
			var el = $(this);
			el.find('.em-ticket-form-advanced input[type="text"]').each(function(){ if(this.value != '') show_advanced = true; });
			if( el.find('.em-ticket-form-advanced input[type="checkbox"]:checked').length > 0 ){ show_advanced = true; }
			el.find('.em-ticket-form-advanced option:selected').each(function(){ if(this.value != '') show_advanced = true; });
			if( show_advanced ) el.find('.ticket-options-advanced').trigger('click');
		});
		//Delete a ticket
		$(document).on('click', '.ticket-actions-delete', function(e){
			e.preventDefault();
			var el = $(this);
			var tbody = el.closest('tbody');
			if( tbody.find('input.ticket_id').val() > 0 ){
				//only will happen if no bookings made
				el.text('Deleting...');
				$.getJSON( $(this).attr('href'), {'em_ajax_action':'delete_ticket', 'id':tbody.find('input.ticket_id').val()}, function(data){
					if(data.result){
						tbody.remove();
					}else{
						el.text('Delete');
						alert(data.error);
					}
				});
			}else{
				//not saved to db yet, so just remove
				tbody.remove();
			}
			check_ticket_sortability();
			return false;
		});
		//Sort Tickets
		$('#em-tickets-form.em-tickets-sortable table').sortable({
			items: '> tbody',
			placeholder: "em-ticket-sortable-placeholder",
			handle:'.ticket-status',
			helper: function( event, el ){
				var helper = $(el).clone().addClass('em-ticket-sortable-helper');
				var tds = helper.find('.em-tickets-row td').length;
				helper.children().remove();
				helper.append('<tr class="em-tickets-row"><td colspan="'+tds+'" style="text-align:left; padding-left:15px;"><span class="dashicons dashicons-tickets-alt"></span></td></tr>');
				return helper;
			},
		});
		var check_ticket_sortability = function(){
			var em_tickets = $('#em-tickets-form table tbody.em-ticket');
			if( em_tickets.length == 1 ){
				em_tickets.find('.ticket-status').addClass('single');
				$('#em-tickets-form.em-tickets-sortable table').sortable( "option", "disabled", true );
			}else{
				em_tickets.find('.ticket-status').removeClass('single');
				$('#em-tickets-form.em-tickets-sortable table').sortable( "option", "disabled", false );
			}
		};
		check_ticket_sortability();
	}
	//Manageing Bookings
	let bookings_tables = $('.em-bookings-table');
	if( bookings_tables.length > 0 ){
		load_ui_css = true;
		//Pagination link clicks
		$(document).on('click', '.em-bookings-table .tablenav-pages a', function(){
			var el = $(this);
			var form = el.closest('.em-bookings-table form.bookings-filter');
			//get page no from url, change page, submit form
			var match = el.attr('href').match(/#[0-9]+/);
			if( match != null && match.length > 0){
				var pno = match[0].replace('#','');
				form.find('input[name=pno]').val(pno);
			}else{
				// new way
				let url = new URL(el.attr('href'));
				if( url.searchParams.has('paged') ){
					form.find('input[name=pno]').val( url.searchParams.get('paged'));
					form.find('input[name=paged]').val( url.searchParams.get('paged') );
				}else{
					form.find('input[name=pno]').val(1);
					form.find('input[name=paged]').val(1);
				}
			}
			form.trigger('submit');
			return false;
		});
		$(document).on('change', '.em-bookings-table .tablenav-pages input[name=paged]', function(e){
			var el = $(this);
			var form = el.closest('.em-bookings-table form.bookings-filter');
			var last = form.find('.tablenav-pages a.last-page');
			if( last.length > 0 ){
				// check val isn't more than last page
				let url = new URL(last.attr('href'));
				if( url.searchParams.has('paged') ){
					let lastPage = parseInt(url.searchParams.get('paged'));
					if( parseInt(this.value) > lastPage ){
						this.value = lastPage;
					}
				}
			}else{
				// make sure it's less than current page, we're on last page already
				let lastPage = form.find('input[name=pno]').val();
				if( lastPage && parseInt(this.value) > parseInt(lastPage) ){
					this.value = lastPage;
					e.preventDefault();
					return false;
				}
			}
			form.find('input[name=pno]').val(this.value);
			form.trigger('submit');
		});

		//Settings & Export Modal
		$(document).on('click', '.em-bookings-table-trigger', function(e){
			e.preventDefault();
			let modal = $(this.getAttribute('rel'));
			modal.find('input[name=show_tickets]').each(check_tickets_columns_export);
			openModal( modal );
		});
		$(document).on('submit', '.em-bookings-table-settings form', function(e){
			e.preventDefault();
			//we know we'll deal with cols, so wipe hidden value from main
			let $form = $(this);
			let modal = $form.closest('.em-modal');
			let form = $($form.attr('rel'));
			let match = form.find("[name=cols]").val('');
			let booking_form_cols = $form.find('.em-bookings-cols-selected .item');
			$.each( booking_form_cols, function(i,item_match){
				//item_match = $(item_match);
				if( !item_match.classList.contains('hidden') ){
					if( match.val() !== ''){
						match.val(match.val()+','+item_match.getAttribute('data-value'));
					}else{
						match.val(item_match.getAttribute('data-value'));
					}
				}
			});
			//sync row count
			let limit = $form.find('select[name="limit"]').val();
			form.find('[name="limit"]').val(limit);
			//submit main form
			modal.trigger('submitted'); //hook into this with bind()
			form.trigger('submit');
			closeModal(modal);
		});
		$(document).on('submit', '.em-bookings-table-export form', function(e){
			let $table_form = $(this.getAttribute('rel'));
			var $form_filters = $(this).find('.em-bookings-table-filters').empty();
			let filters = $table_form.find('.em-bookings-table-filter').clone();
			filters.appendTo($form_filters);
		});
		let check_tickets_columns_export = function(){
			let $this = $(this);
			let form = $this.closest('form');
			let ticket_data = form.find('[data-type="ticket"]');
			if( $this.is(':checked') ){
				ticket_data.show().find('input').val(1);
			}else{
				ticket_data.hide().find('input').val(0);
			}
		}
		$(document).on('click', '.em-bookings-table-export input[name=show_tickets]', check_tickets_columns_export);

		$(document).on('keypress', '.em-bookings-table .tablenav .actions input[type="text"]', function(e){
			let keycode = (e.keyCode ? e.keyCode : e.which);
			if( keycode === 13 ){
				$(this).closest('form').submit();
			}
		});

		$(document).on('click', '.em-bookings-table button.em-bookings-table-bulk-action', function( e ){
			e.preventDefault();
			let $form = $(this).closest('form');
			let action = $form.find('select.bulk-action-selector').val();
			EM.bulk_action = true;
			if( action === 'delete' ){
				if( !confirm(EM.booking_delete) ){ return false; }
			}
			let rows = $form.find('tbody .check-column input:checked');
			// find all checked items and perform action on them. future interations can check if action is available to row.
			rows.each( function(){
				// check if sibling has the relevant action
				let actions = $(this.parentElement).find('a.em-bookings-'+ action);
				actions.trigger('click');
			});
			EM.bulk_action = false;
		})

		// Sorting
		$(document).on('click', '.em-bookings-table th[scope="col"].sortable a, .em-bookings-table th[scope="col"].sorted a', function(e){
			e.preventDefault();
			// add args to form and submit it
			let params = (new URL(this.href)).searchParams;
			let $form = $(this).closest('form');
			if( params.get('orderby') ){
				$form.find('input[name="orderby"]').val( params.get('orderby') );
				let order = params.get('order') ? params.get('order') : 'asc';
				$form.find('input[name="order"]').val( order );
				$form.submit();
			}
		});

		// anything requiring extra setup
		let resetup_bookings_table = function( table ){
			em_setup_tippy(table);
			em_setup_selectize(table);
		}
		let setup_bookings_table = function( table ) {
			table.find(".em-bookings-cols-sortable").sortable().disableSelection();
			let breakpoints = {
				'small' : 600,
				'large' : false,
			}
			const bookings_table_ro = EM_ResizeObserver( breakpoints, table.toArray() );
		}

		bookings_tables.each( function(){
			setup_bookings_table( $(this) );
		});

		//Widgets and filter submissions
		$(document).on('submit', '.em-bookings-table form.bookings-filter', function(e){
			var el = $(this);
			//append loading spinner
			let root = el.parents('.em-bookings-table').first();
			root.find('.table-wrap').first().append('<div id="em-loading" />');
			//ajax call
			$.post( EM.ajaxurl, el.serializeArray(), function(data){
				let $data = $(data);
				if( !root.hasClass('frontend') ) {
					// remove modals as they are supplied again on the backend
					root.find('.em-bookings-table-trigger').each( function(){
						let modal = $(this.getAttribute('rel'));
						modal.remove();
					});
				}
				root.replaceWith($data);
				// re-setup bookings table
				setup_bookings_table($data);
				resetup_bookings_table($data);
				// fire hook
				jQuery(document).triggerHandler('em_bookings_filtered', [$data, root, el]);
			});
			return false;
		});
		// Action links (approve/reject etc.)
		$(document).on('click', '.em-bookings-approve,.em-bookings-reject,.em-bookings-unapprove,.em-bookings-delete,.em-bookings-ajax-action', function(){
			let el = $(this);
			if( el.hasClass('em-bookings-delete') && (!('bulk_action' in EM) || !EM.bulk_action) ){
				if( !confirm(EM.booking_delete) ){ return false; }
			}
			let url = em_ajaxify( el.attr('href') );
			let td = el.parents('td').first();
			if( td.length > 0 && (td.hasClass('column-actions') || td.hasClass('em-bt-col-actions')) ){
				td.html(EM.txt_loading);
				td.load( url );
			}else{
				// set up a custom url action to retrieve a row afterwards
				let dropdown = el.closest('[data-tippy-root], .em-tooltip-ddm-content');
				if( dropdown.length > 0 ) {
					if( '_tippy' in dropdown[0] ) {
						dropdown[0]._tippy.hide();
					}
					let tr = el.closest('tr');
					if( url.match(/^\//) ){
						url = window.location.origin + url;
					}
					let params = (new URL(url)).searchParams;
					let formData = new FormData(tr.closest('form')[0]);
					formData.set('action', 'em_bookings_table_row');
					formData.set('row_action', params.get('action'));
					formData.set('booking_id', params.get('booking_id'));
					let cols = el.closest('form').find('[name="cols"]').val();
					tr.addClass('loading');
					$.ajax({
						url: EM.ajaxurl,
						data: formData,
						processData: false,
						contentType: false,
						type: 'POST',
						success: function (data) {
							let $data = $(data);
							$data.addClass('faded-out');
							tr.replaceWith( $data ).delay(200);
							resetup_bookings_table( $data );
							$data.fadeIn();
							$data.removeClass('faded-out');
						}
					});
				}
			}
			return false;
		});
	}
	//Old Bookings Table - depreciating soon
	if( $('.em_bookings_events_table').length > 0 ){
		//Widgets and filter submissions
		$(document).on('submit', '.em_bookings_events_table form', function(e){
			var el = $(this);
			var url = em_ajaxify( el.attr('action') );
			el.parents('.em_bookings_events_table').find('.table-wrap').first().append('<div id="em-loading" />');
			$.get( url, el.serializeArray(), function(data){
				el.parents('.em_bookings_events_table').first().replaceWith(data);
			});
			return false;
		});
		//Pagination link clicks
		$(document).on('click', '.em_bookings_events_table .tablenav-pages a', function(){
			var el = $(this);
			var url = em_ajaxify( el.attr('href') );
			el.parents('.em_bookings_events_table').find('.table-wrap').first().append('<div id="em-loading" />');
			$.get( url, function(data){
				el.parents('.em_bookings_events_table').first().replaceWith(data);
			});

			return false;
		});
	}

	//Manual Booking
	$(document).on('click', 'a.em-booking-button', function(e){
		e.preventDefault();
		var button = $(this);
		if( button.text() != EM.bb_booked && $(this).text() != EM.bb_booking){
			button.text(EM.bb_booking);
			var button_data = button.attr('id').split('_');
			$.ajax({
				url: EM.ajaxurl,
				dataType: 'jsonp',
				data: {
					event_id : button_data[1],
					_wpnonce : button_data[2],
					action : 'booking_add_one'
				},
				success : function(response, statusText, xhr, $form) {
					if(response.result){
						button.text(EM.bb_booked);
						button.addClass('disabled');
					}else{
						button.text(EM.bb_error);
					}
					if(response.message != '') alert(response.message);
					$(document).triggerHandler('em_booking_button_response', [response, button]);
				},
				error : function(){ button.text(EM.bb_error); }
			});
		}
		return false;
	});
	$(document).on('click', 'a.em-cancel-button', function(e){
		e.preventDefault();
		var button = $(this);
		if( button.text() != EM.bb_cancelled && button.text() != EM.bb_canceling){
			button.text(EM.bb_canceling);
			// old method is splitting id with _ and second/third items are id and nonce, otherwise supply it all via data attributes
			var button_data = button.attr('id').split('_');
			let button_ajax = {};
			if( button_data.length < 3 ){
				// legacy support
				button_ajax = {
					booking_id : button_data[1],
					_wpnonce : button_data[2],
					action : 'booking_cancel',
				};
			}
			let ajax_data = Object.assign( button_ajax, button[0].dataset);
			$.ajax({
				url: EM.ajaxurl,
				dataType: 'jsonp',
				data: ajax_data,
				success : function(response, statusText, xhr, $form) {
					if(response.result){
						button.text(EM.bb_cancelled);
						button.addClass('disabled');
					}else{
						button.text(EM.bb_cancel_error);
					}
				},
				error : function(){ button.text(EM.bb_cancel_error); }
			});
		}
		return false;
	});
	$(document).on('click', 'a.em-booking-button-action', function(e){
		e.preventDefault();
		var button = $(this);
		var button_data = {
			_wpnonce : button.attr('data-nonce'),
			action : button.attr('data-action'),
		}
		if( button.attr('data-event-id') ) button_data.event_id =  button.attr('data-event-id');
		if( button.attr('data-booking-id') ) button_data.booking_id =  button.attr('data-booking-id');
		if( button.text() != EM.bb_booked && $(this).text() != EM.bb_booking){
			if( button.attr('data-loading') ){
				button.text(button.attr('data-loading'));
			}else{
				button.text(EM.bb_booking);
			}
			$.ajax({
				url: EM.ajaxurl,
				dataType: 'jsonp',
				data: button_data,
				success : function(response, statusText, xhr, $form) {
					if(response.result){
						if( button.attr('data-success') ){
							button.text(button.attr('data-success'));
						}else{
							button.text(EM.bb_booked);
						}
						button.addClass('disabled');
					}else{
						if( button.attr('data-error') ){
							button.text(button.attr('data-error'));
						}else{
							button.text(EM.bb_error);
						}
					}
					if(response.message != '') alert(response.message);
					$(document).triggerHandler('em_booking_button_action_response', [response, button]);
				},
				error : function(){
					if( button.attr('data-error') ){
						button.text(button.attr('data-error'));
					}else{
						button.text(EM.bb_error);
					}
				}
			});
		}
		return false;
	});

	//Datepicker - legacy
	if( $('.em-date-single, .em-date-range, #em-date-start').length > 0 ){
		load_ui_css = true;
		em_setup_datepicker('body');
	}
	if( load_ui_css ) em_load_jquery_css();
	// Datepicker - new
	if( $('.em-datepicker').length > 0 ){
		em_setup_datepicker('body');
	}

	//previously in em-admin.php
	$('#em-wrapper input.select-all').on('change', function(){
		if($(this).is(':checked')){
			$('input.row-selector').prop('checked', true);
			$('input.select-all').prop('checked', true);
		}else{
			$('input.row-selector').prop('checked', false);
			$('input.select-all').prop('checked', false);
		}
	});


	// recurrence stuff
	// recurrency descriptor
	function updateIntervalDescriptor () {
		$(".interval-desc").hide();
		var number = "-plural";
		if ($('input.em-recurrence-interval').val() == 1 || $('input.em-recurrence-interval').val() == "") number = "-singular";
		var descriptor = "span.interval-desc.interval-"+$("select.em-recurrence-frequency").val()+number;
		$(descriptor).show();
	}
	function updateIntervalSelectors () {
		$('.alternate-selector').hide();
		$('.em-'+ $('select.em-recurrence-frequency').val() + "-selector").show();
	}
	// recurrency elements
	updateIntervalDescriptor();
	updateIntervalSelectors();
	$('input.em-recurrence-interval').on('keyup', updateIntervalDescriptor);
	$('select.em-recurrence-frequency').on('change', updateIntervalDescriptor);
	$('select.em-recurrence-frequency').on('change', updateIntervalSelectors);

	/* Load any maps */
	if( $('.em-location-map').length > 0 || $('.em-locations-map').length > 0 || $('#em-map').length > 0 || $('.em-search-geo').length > 0 ){
		em_maps_load();
	}

	/* Location Type Selection */
	$('.em-location-types .em-location-types-select').on('change', function(){
		let el = $(this);
		if( el.val() == 0 ){
			$('.em-location-type').hide();
		}else{
			let location_type = el.find('option:selected').data('display-class');
			$('.em-location-type').hide();
			$('.em-location-type.'+location_type).show();
			if( location_type != 'em-location-type-place' ){
				jQuery('#em-location-reset a').trigger('click');
			}
		}
		if( el.data('active') !== '' && el.val() !== el.data('active') ){
			$('.em-location-type-delete-active-alert').hide();
			$('.em-location-type-delete-active-alert').show();
		}else{
			$('.em-location-type-delete-active-alert').hide();
		}
	}).trigger('change');

	//Finally, add autocomplete here
	if( jQuery( 'div.em-location-data [name="location_name"]' ).length > 0 ){
		$('div.em-location-data [name="location_name"]').selectize({
			plugins: ["restore_on_backspace"],
			valueField: "id",
			labelField: "label",
			searchField: "label",
			create:true,
			createOnBlur: true,
			maxItems:1,
			persist: false,
			addPrecedence : true,
			selectOnTab : true,
			diacritics : true,
			render: {
				item: function (item, escape) {
					return "<div>" + escape(item.label) + "</div>";
				},
				option: function (item, escape) {
					let meta = '';
					if( typeof(item.address) !== 'undefined' ) {
						if (item.address !== '' && item.town !== '') {
							meta = escape(item.address) + ', ' + escape(item.town);
						} else if (item.address !== '') {
							meta = escape(item.address);
						} else if (item.town !== '') {
							meta = escape(item.town);
						}
					}
					return  '<div class="em-locations-autocomplete-item">' +
						'<div class="em-locations-autocomplete-label">' + escape(item.label) + '</div>' +
						'<div style="font-size:11px; text-decoration:italic;">' + meta + '</div>' +
						'</div>';

				},
			},
			load: function (query, callback) {
				if (!query.length) return callback();
				$.ajax({
					url: EM.locationajaxurl,
					data: {
						q : query,
						method : 'selectize'
					},
					dataType : 'json',
					type: "POST",
					error: function () {
						callback();
					},
					success: function ( data ) {
						callback( data );
					},
				});
			},
			onItemAdd : function (value, data) {
				this.clearCache();
				var option = this.options[value];
				if( value === option.label ){
					jQuery('input#location-address').focus();
					return;
				}
				jQuery("input#location-name" ).val(option.value);
				jQuery('input#location-address').val(option.address);
				jQuery('input#location-town').val(option.town);
				jQuery('input#location-state').val(option.state);
				jQuery('input#location-region').val(option.region);
				jQuery('input#location-postcode').val(option.postcode);
				jQuery('input#location-latitude').val(option.latitude);
				jQuery('input#location-longitude').val(option.longitude);
				if( typeof(option.country) === 'undefined' || option.country === '' ){
					jQuery('select#location-country option:selected').removeAttr('selected');
				}else{
					jQuery('select#location-country option[value="'+option.country+'"]').attr('selected', 'selected');
				}
				jQuery("input#location-id" ).val(option.id).trigger('change');
				jQuery('div.em-location-data input, div.em-location-data select').prop('readonly', true).css('opacity', '0.5');
				jQuery('#em-location-reset').show();
				jQuery('#em-location-search-tip').hide();
				// selectize stuff
				this.disable();
				this.$control.blur();
				jQuery('div.em-location-data [class^="em-selectize"]').each( function(){
					if( 'selectize' in this ) {
						this.selectize.disable();
					}
				})
				// trigger hook
				jQuery(document).triggerHandler('em_locations_autocomplete_selected', [event, option]);
			}
		});
		jQuery('#em-location-reset a').on('click', function(){
			jQuery('div.em-location-data input, div.em-location-data select').each( function(){
				this.style.removeProperty('opacity')
				this.readOnly = false;
				if( this.type == 'text' ) this.value = '';
			});
			jQuery('div.em-location-data option:selected').removeAttr('selected');
			jQuery('input#location-id').val('');
			jQuery('#em-location-reset').hide();
			jQuery('#em-location-search-tip').show();
			jQuery('#em-map').hide();
			jQuery('#em-map-404').show();
			if(typeof(marker) !== 'undefined'){
				marker.setPosition(new google.maps.LatLng(0, 0));
				infoWindow.close();
				marker.setDraggable(true);
			}
			// clear selectize autocompleter values, re-enable any selectize ddms
			let $selectize = $("div.em-location-data input#location-name")[0].selectize;
			$selectize.enable();
			$selectize.clear(true);
			$selectize.clearOptions();
			jQuery('div.em-location-data select.em-selectize').each( function(){
				if( 'selectize' in this ){
					this.selectize.enable();
					this.selectize.clear(true);
				}
			});
			// return true
			return false;
		});
		if( jQuery('input#location-id').val() != '0' && jQuery('input#location-id').val() != '' ){
			jQuery('div.em-location-data input, div.em-location-data select').each( function(){
				this.style.setProperty('opacity','0.5', 'important')
				this.readOnly = true;
			});
			jQuery('#em-location-reset').show();
			jQuery('#em-location-search-tip').hide();
			jQuery('div.em-location-data select.em-selectize, div.em-location-data input.em-selectize-autocomplete').each( function(){
				if( 'selectize' in this ) this.selectize.disable();
			});
		}
	}

	// trigger selectize loader
	em_setup_ui_elements(document);

	/* Done! */
	$(document).triggerHandler('em_javascript_loaded');
});

/**
 * Sets up external UI libraries and adds them to elements within the supplied container. This can be a jQuery or DOM element, subfunctions will either handle accordingly or this function will ensure it's the right one to pass on..
 * @param container
 */
function em_setup_ui_elements ( container ) {
	// Selectize
	em_setup_selectize( container );
	// Tippy
	em_setup_tippy( container );
	// Moment JS
	em_setup_moment_times( container );
}

/* Local JS Timezone related placeholders */
/* Moment JS Timzeone PH */
function em_setup_moment_times( container_element ) {
	container = jQuery(container_element);
	if( window.moment ){
		var replace_specials = function( day, string ){
			// replace things not supported by moment
			string = string.replace(/##T/g, Intl.DateTimeFormat().resolvedOptions().timeZone);
			string = string.replace(/#T/g, "GMT"+day.format('Z'));
			string = string.replace(/###t/g, day.utcOffset()*-60);
			string = string.replace(/##t/g, day.isDST());
			string = string.replace(/#t/g, day.daysInMonth());
			return string;
		};
		container.find('.em-date-momentjs').each( function(){
			// Start Date
			var el = jQuery(this);
			var day_start = moment.unix(el.data('date-start'));
			var date_start_string = replace_specials(day_start, day_start.format(el.data('date-format')));
			if( el.data('date-start') !== el.data('date-end') ){
				// End Date
				var day_end = moment.unix(el.data('date-end'));
				var day_end_string = replace_specials(day_start, day_end.format(el.data('date-format')));
				// Output
				var date_string = date_start_string + el.data('date-separator') + day_end_string;
			}else{
				var date_string = date_start_string;
			}
			el.text(date_string);
		});
		var get_date_string = function(ts, format){
			let date = new Date(ts * 1000);
			let minutes = date.getMinutes();
			if( format == 24 ){
				let hours = date.getHours();
				hours = hours < 10 ? '0' + hours : hours;
				minutes = minutes < 10 ? '0' + minutes : minutes;
				return hours + ':' + minutes;
			}else{
				let hours = date.getHours() % 12;
				let ampm = hours >= 12 ? 'PM' : 'AM';
				if( hours === 0 ) hours = 12; // the hour '0' should be '12'
				minutes = minutes < 10 ? '0'+minutes : minutes;
				return hours + ':' + minutes + ' ' + ampm;
			}
		}
		container.find('.em-time-localjs').each( function(){
			var el = jQuery(this);
			var strTime = get_date_string( el.data('time'), el.data('time-format') );
			if( el.data('time-end') ){
				var separator = el.data('time-separator') ? el.data('time-separator') : ' - ';
				strTime = strTime + separator + get_date_string( el.data('time-end'), el.data('time-format') );
			}
			el.text(strTime);
		});
	}
};

function em_load_jquery_css( wrapper = false ){
	if( EM.ui_css && jQuery('link#jquery-ui-em-css').length == 0 ){
		var script = document.createElement("link");
		script.id = 'jquery-ui-em-css';
		script.rel = "stylesheet";
		script.href = EM.ui_css;
		document.body.appendChild(script);
		if( wrapper ){
			em_setup_jquery_ui_wrapper();
		}
	}
}

function em_setup_jquery_ui_wrapper(){
	if( jQuery('#em-jquery-ui').length === 0 ){
		jQuery('body').append('<div id="em-jquery-ui" class="em">');
	}
}

/* Useful function for adding the em_ajax flag to a url, regardless of querystring format */
var em_ajaxify = function(url){
	if ( url.search('em_ajax=0') != -1){
		url = url.replace('em_ajax=0','em_ajax=1');
	}else if( url.search(/\?/) != -1 ){
		url = url + "&em_ajax=1";
	}else{
		url = url + "?em_ajax=1";
	}
	return url;
};

function em_setup_datepicker(wrap){
	wrap = jQuery(wrap);

	//apply datepickers - jQuery UI (backcompat)
	let dateDivs = wrap.find('.em-date-single, .em-date-range');
	if( dateDivs.length > 0 ){
		//default picker vals
		var datepicker_vals = {
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			firstDay : EM.firstDay,
			yearRange:'c-100:c+15',
			beforeShow : function( el, inst ){
				em_setup_jquery_ui_wrapper();
				inst.dpDiv.appendTo('#em-jquery-ui');
			}
		};
		if( EM.dateFormat ) datepicker_vals.dateFormat = EM.dateFormat;
		if( EM.yearRange ) datepicker_vals.yearRange = EM.yearRange;
		jQuery(document).triggerHandler('em_datepicker', datepicker_vals);
		//apply datepickers to elements
		dateDivs.find('input.em-date-input-loc').each(function(i,dateInput){
			//init the datepicker
			var dateInput = jQuery(dateInput);
			var dateValue = dateInput.nextAll('input.em-date-input').first();
			var dateValue_value = dateValue.val();
			dateInput.datepicker(datepicker_vals);
			dateInput.datepicker('option', 'altField', dateValue);
			//now set the value
			if( dateValue_value ){
				var this_date_formatted = jQuery.datepicker.formatDate( EM.dateFormat, jQuery.datepicker.parseDate('yy-mm-dd', dateValue_value) );
				dateInput.val(this_date_formatted);
				dateValue.val(dateValue_value);
			}
			//add logic for texts
			dateInput.on('change', function(){
				if( jQuery(this).val() == '' ){
					jQuery(this).nextAll('.em-date-input').first().val('');
				}
			});
		});
		//deal with date ranges
		dateDivs.filter('.em-date-range').find('input.em-date-input-loc[type="text"]').each(function(i,dateInput){
			//finally, apply start/end logic to this field
			dateInput = jQuery(dateInput);
			if( dateInput.hasClass('em-date-start') ){
				dateInput.datepicker('option','onSelect', function( selectedDate ) {
					//get corresponding end date input, we expect ranges to be contained in .em-date-range with a start/end input element
					var startDate = jQuery(this);
					var endDate = startDate.parents('.em-date-range').find('.em-date-end').first();
					var startValue = startDate.nextAll('input.em-date-input').first().val();
					var endValue = endDate.nextAll('input.em-date-input').first().val();
					startDate.trigger('em_datepicker_change');
					if( startValue > endValue && endValue != '' ){
						endDate.datepicker( "setDate" , selectedDate );
						endDate.trigger('change').trigger('em_datepicker_change');
					}
					endDate.datepicker( "option", 'minDate', selectedDate );
				});
			}else if( dateInput.hasClass('em-date-end') ){
				var startInput = dateInput.parents('.em-date-range').find('.em-date-start').first();
				if( startInput.val() != '' ){
					dateInput.datepicker('option', 'minDate', startInput.val());
				}
			}
		});
	}

	// datpicker - new format
	let datePickerDivs = wrap.find('.em-datepicker, .em-datepicker-range');
	if( datePickerDivs.length > 0 ){
		// wrappers and locale
		let datepicker_wrapper = jQuery('#em-flatpickr');
		if( datepicker_wrapper.length === 0 ){
			datepicker_wrapper = jQuery('<div class="em pixelbones em-flatpickr" id="em-flatpickr"></div>').appendTo('body');
		}
		// locale
		if( 'locale' in EM.datepicker ){
			flatpickr.localize(flatpickr.l10ns[EM.datepicker.locale]);
			flatpickr.l10ns.default.firstDayOfWeek = EM.firstDay;
		}
		//default picker vals
		let datepicker_options = {
			appendTo : datepicker_wrapper[0],
			dateFormat: "Y-m-d",
			disableMoble : "true",
			allowInput : true,
			onChange : [function( selectedDates, dateStr, instance ){
				let wrapper = jQuery(instance.input).closest('.em-datepicker');
				let data_wrapper = wrapper.find('.em-datepicker-data');
				let inputs = data_wrapper.find('input');
				let dateFormat = function(d) {
					let month = '' + (d.getMonth() + 1),
						day = '' + d.getDate(),
						year = d.getFullYear();
					if (month.length < 2) month = '0' + month;
					if (day.length < 2) day = '0' + day;
					return [year, month, day].join('-');
				}
				if( selectedDates.length === 0 ){
					inputs.attr('value', '');
				}else{
					if( instance.config.mode === 'range' && selectedDates[1] !== undefined ) {
						// deal with end date
						inputs[0].setAttribute('value', dateFormat(selectedDates[0]));
						inputs[1].setAttribute('value', dateFormat(selectedDates[1]));
					}else if( instance.config.mode === 'single' && wrapper.hasClass('em-datepicker-until') ){
						if( instance.input.classList.contains('em-date-input-start') ){
							inputs[0].setAttribute('value', dateFormat(selectedDates[0]));
							// set min-date of other datepicker
							let fp;
							if( wrapper.attr('data-until-id') ){
								let fp_input = jQuery('#' + wrapper.attr('data-until-id') + ' .em-date-input-end');
								fp = fp_input[0]._flatpickr;
							}else {
								fp = wrapper.find('.em-date-input-end')[0]._flatpickr;
							}
							if( fp.selectedDates[0] !== undefined && fp.selectedDates[0] < selectedDates[0] ){
								fp.setDate(selectedDates[0], false);
								inputs[1].setAttribute('value', dateFormat(fp.selectedDates[0]));
							}
							fp.set('minDate', selectedDates[0]);
						}else{
							inputs[1].setAttribute('value', dateFormat(selectedDates[0]));
						}
					}else{
						inputs[0].setAttribute('value', dateFormat(selectedDates[0]));
					}
				}
				inputs.trigger('change');
				let current_date = data_wrapper.attr('date-value');
				data_wrapper.attr('data-value', dateStr);
				if( current_date === dateStr ) data_wrapper.trigger('change');
			}],
			onClose : function( selectedDates, dateStr, instance ){
				// deal with single date choice and clicking out
				if( instance.config.mode === 'range' && selectedDates[1] !== undefined ){
					if(selectedDates.length === 1){
						instance.setDate([selectedDates[0],selectedDates[0]], true); // wouldn't have been triggered with a single date selection
					}
				}
			},
			locale : {},
		};
		if( EM.datepicker.format !== datepicker_options.dateFormat ){
			datepicker_options.altFormat = EM.datepicker.format;
			datepicker_options.altInput = true;
		}
		jQuery(document).triggerHandler('em_datepicker_options', datepicker_options);
		//apply datepickers to elements
		datePickerDivs.each( function(i,datePickerDiv) {
			// hide fallback fields, show range or single
			datePickerDiv = jQuery(datePickerDiv);
			datePickerDiv.find('.em-datepicker-data').addClass('hidden');
			let isRange = datePickerDiv.hasClass('em-datepicker-range');
			let altOptions = {};
			if( datePickerDiv.attr('data-datepicker') ){
				altOptions = JSON.parse(datePickerDiv.attr('data-datepicker'));
				if( typeof altOptions !== 'object' ){
					altOptions = {};
				}
			}
			let options = Object.assign({}, datepicker_options, altOptions); // clone, mainly shallow concern for 'mode'
			options.mode = isRange ? 'range' : 'single';
			if( isRange && 'onClose' in options ){
				options.onClose = [function( selectedDates, dateStr, instance ){
					if(selectedDates.length === 1){ // deal with single date choice and clicking out
						instance.setDate([selectedDates[0],selectedDates[0]], true);
					}
				}];
			}
			if( datePickerDiv.attr('data-separator') ) options.locale.rangeSeparator = datePickerDiv.attr('data-separator');
			if( datePickerDiv.attr('data-format') ) options.altFormat = datePickerDiv.attr('data-format');
			let FPs = datePickerDiv.find('.em-date-input');
			FPs.attr('type', 'text').flatpickr(options);
		});
		// add values to elements, done once all datepickers instantiated so we don't get errors with date range els in separate divs
		datePickerDivs.each( function(i,datePickerDiv) {
			datePickerDiv = jQuery(datePickerDiv);
			let FPs = datePickerDiv.find('.em-date-input');
			let inputs = datePickerDiv.find('.em-datepicker-data input');
			inputs.attr('type', 'hidden'); // hide so not tabbable
			if( datePickerDiv.hasClass('em-datepicker-until') ){
				let start_fp, end_fp;
				if( datePickerDiv.attr('data-until-id') ){
					end_fp = jQuery('#' + datePickerDiv.attr('data-until-id') + ' .em-date-input-end')[0]._flatpickr;
				}else{
					end_fp = FPs.filter('.em-date-input-end')[0]._flatpickr;
					if( inputs[1] && inputs[1].value ) {
						end_fp.setDate(inputs[1].value, false, 'Y-m-d');
					}
				}
				if( inputs[0] && inputs[0].value ){
					start_fp = FPs.filter('.em-date-input-start')[0]._flatpickr;
					start_fp.setDate(inputs[0].value, false, 'Y-m-d');
					end_fp.set('minDate', inputs[0].value);
				}
			}else{
				let dates = [];
				inputs.each( function( i, input ){
					if( input.value ){
						dates.push(input.value);
					}
				});
				FPs[0]._flatpickr.setDate(dates, false, 'Y-m-d');
			}
		});
		// fire trigger
		jQuery(document).triggerHandler('em_flatpickr_loaded', [wrap]);
	}
}

function em_setup_timepicker(wrap){
	wrap = jQuery(wrap);
	var timepicker_options = {
		step:15
	}
	timepicker_options.timeFormat = EM.show24hours == 1 ? 'G:i':'g:i A';
	jQuery(document).triggerHandler('em_timepicker_options', timepicker_options);
	wrap.find(".em-time-input").em_timepicker(timepicker_options);

	// Keep the duration between the two inputs.
	wrap.find(".em-time-range input.em-time-start").each( function(i, el){
		var time = jQuery(el);
		time.data('oldTime', time.em_timepicker('getSecondsFromMidnight'));
	}).on('change', function() {
		var start = jQuery(this);
		var end = start.nextAll('.em-time-end');
		if (end.val()) { // Only update when second input has a value.
			// Calculate duration.
			var oldTime = start.data('oldTime');
			var duration = (end.em_timepicker('getSecondsFromMidnight') - oldTime) * 1000;
			var time = start.em_timepicker('getSecondsFromMidnight');
			if( end.em_timepicker('getSecondsFromMidnight') >= oldTime ){
				// Calculate and update the time in the second input.
				end.em_timepicker('setTime', new Date(start.em_timepicker('getTime').getTime() + duration));
			}
			start.data('oldTime', time);
		}
	});
	// Validate.
	wrap.find(".event-form-when .em-time-range input.em-time-end").on('change', function() {
		var end = jQuery(this);
		var start = end.prevAll('.em-time-start');
		var wrapper = end.closest('.event-form-when');
		var start_date = wrapper.find('.em-date-end').val();
		var end_date = wrapper.find('.em-date-start').val();
		if( start.val() ){
			if( start.em_timepicker('getTime') > end.em_timepicker('getTime') && ( end_date.length == 0 || start_date == end_date ) ) { end.addClass("error"); }
			else { end.removeClass("error"); }
		}
	});
	wrap.find(".event-form-when .em-date-end").on('change', function(){
		jQuery(this).closest('.event-form-when').find('.em-time-end').trigger('change');
	});
	//Sort out all day checkbox
	wrap.find('.em-time-range input.em-time-all-day').on('change', function(){
		var allday = jQuery(this);
		if( allday.is(':checked') ){
			allday.closest('.em-time-range').find('.em-time-input').each( function(){
				this.style.setProperty('background-color','#ccc', 'important');
				this.readOnly = true;
			});
		}else{
			allday.closest('.em-time-range').find('.em-time-input').each( function(){
				this.style.removeProperty('background-color');
				this.readOnly = false;
			});
		}
	}).trigger('change');
}


let em_close_other_selectized = function(){
	// find all other selectized items and close them
	let control = this.classList.contains('selectize-control') ? this.closest('.em-selectize.selectize-control') : this;
	document.querySelectorAll('.em-selectize.dropdown-active').forEach( function( el ){
		if( el !== control && 'selectize' in el.previousElementSibling) {
			el.previousElementSibling.selectize.close();
		}
	});
}

document.addEventListener('DOMContentLoaded', function(){
	Selectize.define('multidropdown', function( options ) {
		if( !this.$input.hasClass('multidropdown') ) return;
		let s = this;
		let s_setup = s.setup;
		let s_refreshOptions = s.refreshOptions;
		let s_open = s.open;
		let s_close = s.close;
		let placeholder;
		let placeholder_text
		let placeholder_default;
		let placeholder_label;
		let counter;
		let isClosing = false;
		this.changeFunction = function() {
			let items = s.getValue();
			let selected_text = this.$input.attr('data-selected-text') ? this.$input.attr('data-selected-text') : '%d Selected';
			counter.children('span.selected-text').text(selected_text.replace('%d', items.length));
			if( items.length > 0 ) {
				counter.removeClass('hidden');
				placeholder_text.text( placeholder_label );
				s.$control_input.attr('placeholder', s.$input.attr('placeholder'));
			} else {
				counter.addClass('hidden');
				placeholder_text.text( placeholder_default );
			}
		}
		this.setup = function() {
			s_setup.apply(s);
			s.isDropdownClosingPlaceholder = false;
			// add section to top of selection to show the dropdown text
			placeholder = jQuery('<div class="em-selectize-placeholder"></div>').prependTo(s.$wrapper);
			let clear_text = this.$input.attr('data-clear-text') ? this.$input.attr('data-clear-text') : 'Clear Selection';
			counter = jQuery('<span class="placeholder-count hidden"><a href="#" class="remove" tabindex="-1">X</a><span class="selected-text"></span><span class="clear-selection">' + clear_text + '</span></div>').prependTo(placeholder);
			placeholder_text = jQuery('<span class="placeholder-text"></span>').appendTo(placeholder);
			placeholder_default = s.$input.attr('data-default') ? s.$input.attr('data-default') : s.$input.attr('placeholder');
			placeholder_label = s.$input.attr('data-label') ? s.$input.attr('data-label') : s.$input.attr('placeholder');
			placeholder_text.text( placeholder_default );
			s.$dropdown.prepend(s.$control_input.parent());
			s.on('dropdown_close', function() {
				s.$wrapper.removeClass('dropdown-active');
			});
			s.on('dropdown_open', function() {
				s.$wrapper.addClass('dropdown-active');
				s.$control_input.val('');
			});
			s.on('change', this.changeFunction);
			placeholder.on('focus blur click', function (e) {
				// only if we're clicking on the placeholder
				if( this.matches('.em-selectize-placeholder') ) {
					if ( !s.isOpen && e.type !== 'blur' ){
						s.open();
					} else if ( s.isOpen && e.type !== 'focus' ) {
						s.close();
					}
				}
			}).on('focus blur click mousedown mouseup', function( e ){
				if( this.matches('.em-selectize-placeholder') ) {
					// stope selectize doing anything to our own open/close actions
					e.stopPropagation();
					e.preventDefault();
					if( e.type === 'click' ) {
						em_close_other_selectized.call( this.closest('.selectize-control') );
						if ( s.isOpen && s.$control_input.val() && !this.matches('.placeholder-count') && !this.closest('.placeholder-count') ) {
							isClosing = true;
							s.close();
						}
					} else {
						isClosing = false;
					}
					return false;
				}
			});
			counter.on( 'click' , function( e ){
				e.preventDefault();
				e.stopPropagation();
				s.clear();
				if( s.isOpen ) s.refreshOptions();
			});
			this.changeFunction();
		}

		// prevent dropdown from closing when no options are found, because the search input shows within the dropdown in multidropdown
		this.refreshOptions = function ( ...args ) {
			s_refreshOptions.apply(s, args);
			if ( !this.hasOptions && this.lastQuery ) {
				// intervene on closing only if not in a closing process caused by our own listeners
				if( isClosing === false ) {
					this.$wrapper.addClass("dropdown-active");
					s.isOpen = true;
				}
				this.$wrapper.addClass("no-options");
				isClosing = false;
			} else {
				this.$wrapper.removeClass("no-options");
			}
		};
	});
});

function em_setup_selectize( container_element ){
	container = jQuery(container_element); // in case we were given a dom object

	container.find('.em-selectize.selectize-control').on( 'click', em_close_other_selectized );

	// Selectize General
	container.find('select:not([multiple]).em-selectize, .em-selectize select:not([multiple])').selectize({
		selectOnTab : false,
	});
	container.find('select[multiple].em-selectize, .em-selectize select[multiple]').selectize({
		selectOnTab : false,
		hideSelected : false,
		plugins: ["remove_button", 'click2deselect','multidropdown'],
		diacritics : true,
		render: {
			item: function (item, escape) {
				return '<div class="item"><span>' + item.text.replace(/^\s+/i, '') + '</span></div>';
			},
			option : function (item, escape) {
				let html = '<div class="option"';
				if( 'data' in item ){
					// any key/value object pairs wrapped in a 'data' key within JSON object in the data-data attribute is added automatically as a data-key="value" attribute
					Object.entries(item.data).forEach( function( item_data ){
						html += ' data-'+ escape(item_data[0]) + '="'+ escape(item_data[1]) +'"';
					});
				}
				html +=	'>';
				if( this.$input.hasClass('checkboxes') ){
					html += item.text.replace(/^(\s+)?/i, '$1<span></span> ');
				}else{
					html += item.text;
				}
				html += '</div>';
				return html;
			},
			optgroup : function (item, escape) {
				let html = '<div class="optgroup" data-group="' + escape(item.label) + '"';
				if( 'data' in item ){
					// any key/value object pairs wrapped in a 'data' key within JSON object in the data-data attribute is added automatically as a data-key="value" attribute
					Object.entries(item.data).forEach( function( item_data ){
						html += ' data-'+ escape(item_data[0]) + '="'+ escape(item_data[1]) +'"';
					});
				}
				html +=	'>';
				return html + item.html + '</div>';
			}

		},
	});
	container.find('.em-selectize:not(.always-open)').each( function(){
		if( 'selectize' in this ){
			let s = this.selectize;
			this.selectize.$wrapper.on('keydown', function(e) {
				if( e.keyCode === 9 ) {
					s.blur();
				}
			});
		}
	});
	container.find('.em-selectize.always-open').each( function(){
		//extra behaviour for selectize "always open mode"
		if( 'selectize' in this ){
			let s = this.selectize;
			s.open();
			s.advanceSelection = function(){}; // remove odd item shuffling
			s.setActiveItem = function(){}; // remove odd item shuffling
			// add event listener to fix remove button issues due to above hacks
			this.selectize.$control.on('click', '.remove', function(e) {
				if ( s.isLocked  ) return;
				var $item = jQuery(e.currentTarget).parent();
				s.removeItem($item.attr('data-value'));
				s.refreshOptions();
				return false;
			});
		}
	});

	// Sortables - selectize and sorting
	container.find('.em-bookings-table-modal .em-bookings-table-cols').each( function(){
		let parent = jQuery(this);
		let sortables = jQuery(this).find('.em-bookings-cols-sortable');
		container.find('.em-selectize.always-open').each( function() {
			//extra behaviour for selectize column picker
			if ('selectize' in this) {
				let selectize = this.selectize;
				// add event listener to fix remove button issues due to above hacks
				selectize.on('item_add', function (value, item) {
					let col = item.clone();
					let option  = selectize.getOption(value);
					let type = option.attr('data-type');
					col.appendTo(sortables);
					col.attr('data-type', type);
					jQuery('<input type="hidden" name="cols[' + value + ']" value="1">').appendTo(col);
				});
				selectize.on('item_remove', function (value) {
					parent.find('.item[data-value="'+ value +'"]').remove();
				});
				parent.on('click', '.em-bookings-cols-selected .item .remove', function(){
					let value = this.parentElement.getAttribute('data-value');
					selectize.removeItem(value, true);
				});
			}
		});
	});
}

function em_setup_tippy( container_element ){
	let container = jQuery(container_element);
	var tooltip_vars = {
		theme : 'light-border',
		appendTo : 'parent',
		content(reference) {
			if( 's' in reference.dataset && reference.dataset.content.match(/^[.#][a-zA-Z0-9]+/) ){
				try {
					let content = container[0].querySelector(reference.dataset.content);
					if (content) {
						content.classList.remove('hidden');
						return content;
					}
				} catch ( error ) {
					console.log('Invlid tooltip selector in %o : %o', reference, error);
				}
			}
			return reference.getAttribute('aria-label');
		},
		'touch' : ['hold', 300],
		allowHTML : true,
	};
	jQuery(document).trigger('em-tippy-vars',[tooltip_vars, container]);
	tippy('.em-tooltip', tooltip_vars);
	// Set up Tippy DDMs
	let tippy_ddm_options = {
		theme : 'light-border',
		arrow : false,
		allowHTML : true,
		interactive : true,
		trigger : 'manual',
		placement : 'bottom',
		zIndex : 1000000,
		touch : true,
	};
	jQuery(document).trigger('em-tippy-ddm-vars',[tippy_ddm_options, container]);
	container.find('.em-tooltip-ddm').each( function(){
		let ddm_content, ddm_content_sibling;
		if( this.getAttribute('data-content') ){
			ddm_content = document.getElementById(this.getAttribute('data-content'))
			ddm_content_sibling = ddm_content.previousElementSibling;
		}else{
			ddm_content = this.nextElementSibling;
			ddm_content_sibling = ddm_content.previousElementSibling;
		}
		let tippy_content = document.createElement('div');
		// allow for custom width
		let button_width = this.getAttribute('data-button-width');
		if( button_width ){
			if( button_width == 'match' ){
				tippy_ddm_options.maxWidth = this.clientWidth;
				ddm_content.style.width = this.clientWidth + 'px';
			}else{
				tippy_ddm_options.maxWidth = this.getAttribute('data-button-width');
			}
		}
		tippy_ddm_options.content = tippy_content;
		let tippy_ddm = tippy(this, tippy_ddm_options);
		tippy_ddm.props.distance = 50;
		tippy_ddm.setProps({
			onShow(instance){
				if( instance.reference.getAttribute('data-tooltip-class') ) {
					instance.popper.classList.add( instance.reference.getAttribute('data-tooltip-class') );
				}
				instance.popper.classList.add( 'em-tooltip-ddm-display' );
				tippy_content.append(ddm_content);
				ddm_content.classList.remove('em-tooltip-ddm-content');
			},
			onShown(instance){ // keyboard support
				ddm_content.firstElementChild.focus();
			},
			onHidden(instance){
				if( ddm_content.previousElementSibling !== ddm_content_sibling ) {
					ddm_content_sibling.after(ddm_content);
					ddm_content.classList.add('em-tooltip-ddm-content');
				}
			},
		});
		let tippy_listener = function(e){
			if( e.type === 'keydown' && !(e.which === 13 || e.which === 40) ) return false;
			e.preventDefault();
			e.stopPropagation();
			this._tippy.show();
		}
		this.addEventListener('click', tippy_listener);
		this.addEventListener('keydown', tippy_listener);
		tippy_content.addEventListener('blur', function(){
			tippy_content.hide();
		});
		tippy_content.addEventListener('mouseover', function(){
			ddm_content.firstElementChild.blur();
		});
	});
}


/*
 * MAP FUNCTIONS
 */
var em_maps_loaded = false;
var maps = {};
var maps_markers = {};
var infoWindow;
//loads maps script if not already loaded and executes EM maps script
function em_maps_load(){
	if( !em_maps_loaded ){
		if ( jQuery('script#google-maps').length == 0 && ( typeof google !== 'object' || typeof google.maps !== 'object' ) ){
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.id = "google-maps";
			var proto = (EM.is_ssl) ? 'https:' : 'http:';
			if( typeof EM.google_maps_api !== 'undefined' ){
				script.src = proto + '//maps.google.com/maps/api/js?v=quarterly&libraries=places&callback=em_maps&key='+EM.google_maps_api;
			}else{
				script.src = proto + '//maps.google.com/maps/api/js?v=quarterly&libraries=places&callback=em_maps';
			}
			document.body.appendChild(script);
		}else if( typeof google === 'object' && typeof google.maps === 'object' && !em_maps_loaded ){
			em_maps();
		}else if( jQuery('script#google-maps').length > 0 ){
			jQuery(window).load(function(){ if( !em_maps_loaded ) em_maps(); }); //google isn't loaded so wait for page to load resources
		}
	}
}
jQuery(document).on('em_view_loaded_map', function( e, view, form ){
	if( !em_maps_loaded ){
		em_maps_load();
	}else{
		let map = view.find('div.em-locations-map');
		em_maps_load_locations( map );
	}
});
//re-usable function to load global location maps
function em_maps_load_locations(el){
	var el = jQuery(el);
	var map_id = el.attr('id').replace('em-locations-map-','');
	var em_data = jQuery.parseJSON( el.nextAll('.em-locations-map-coords').first().text() );
	if( em_data == null ){
		var em_data = jQuery.parseJSON( jQuery('#em-locations-map-coords-'+map_id).text() );
	}
	jQuery.getJSON(document.URL, em_data , function(data){
		if(data.length > 0){
			//define default options and allow option for extension via event triggers
			var map_options = { mapTypeId: google.maps.MapTypeId.ROADMAP };
			if( typeof EM.google_map_id_styles == 'object' && typeof EM.google_map_id_styles[map_id] !== 'undefined' ){ console.log(EM.google_map_id_styles[map_id]); map_options.styles = EM.google_map_id_styles[map_id]; }
			else if( typeof EM.google_maps_styles !== 'undefined' ){ map_options.styles = EM.google_maps_styles; }
			jQuery(document).triggerHandler('em_maps_locations_map_options', map_options);
			var marker_options = {};
			jQuery(document).triggerHandler('em_maps_location_marker_options', marker_options);

			maps[map_id] = new google.maps.Map(el[0], map_options);
			maps_markers[map_id] = [];

			var bounds = new google.maps.LatLngBounds();

			jQuery.map( data, function( location, i ){
				if( !(location.location_latitude == 0 && location.location_longitude == 0) ){
					var latitude = parseFloat( location.location_latitude );
					var longitude = parseFloat( location.location_longitude );
					var location_position = new google.maps.LatLng( latitude, longitude );
					//extend the default marker options
					jQuery.extend(marker_options, {
						position: location_position,
						map: maps[map_id]
					})
					var marker = new google.maps.Marker(marker_options);
					maps_markers[map_id].push(marker);
					marker.setTitle(location.location_name);
					var myContent = '<div class="em-map-balloon"><div id="em-map-balloon-'+map_id+'" class="em-map-balloon-content">'+ location.location_balloon +'</div></div>';
					em_map_infobox(marker, myContent, maps[map_id]);
					//extend bounds
					bounds.extend(new google.maps.LatLng(latitude,longitude))
				}
			});
			// Zoom in to the bounds
			maps[map_id].fitBounds(bounds);

			//Call a hook if exists
			jQuery(document).triggerHandler('em_maps_locations_hook', [maps[map_id], data, map_id, maps_markers[map_id]]);
		}else{
			el.children().first().html('No locations found');
			jQuery(document).triggerHandler('em_maps_locations_hook_not_found', [el]);
		}
	});
}
function em_maps_load_location(el){
	el = jQuery(el);
	var map_id = el.attr('id').replace('em-location-map-','');
	em_LatLng = new google.maps.LatLng( jQuery('#em-location-map-coords-'+map_id+' .lat').text(), jQuery('#em-location-map-coords-'+map_id+' .lng').text());
	//extend map and markers via event triggers
	var map_options = {
		zoom: 14,
		center: em_LatLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		mapTypeControl: false,
		gestureHandling: 'cooperative'
	};
	if( typeof EM.google_map_id_styles == 'object' && typeof EM.google_map_id_styles[map_id] !== 'undefined' ){ console.log(EM.google_map_id_styles[map_id]); map_options.styles = EM.google_map_id_styles[map_id]; }
	else if( typeof EM.google_maps_styles !== 'undefined' ){ map_options.styles = EM.google_maps_styles; }
	jQuery(document).triggerHandler('em_maps_location_map_options', map_options);
	maps[map_id] = new google.maps.Map( document.getElementById('em-location-map-'+map_id), map_options);
	var marker_options = {
		position: em_LatLng,
		map: maps[map_id]
	};
	jQuery(document).triggerHandler('em_maps_location_marker_options', marker_options);
	maps_markers[map_id] = new google.maps.Marker(marker_options);
	infoWindow = new google.maps.InfoWindow({ content: jQuery('#em-location-map-info-'+map_id+' .em-map-balloon').get(0) });
	infoWindow.open(maps[map_id],maps_markers[map_id]);
	maps[map_id].panBy(40,-70);

	//JS Hook for handling map after instantiation
	//Example hook, which you can add elsewhere in your theme's JS - jQuery(document).on('em_maps_location_hook', function(){ alert('hi');} );
	jQuery(document).triggerHandler('em_maps_location_hook', [maps[map_id], infoWindow, maps_markers[map_id], map_id]);
	//map resize listener
	jQuery(window).on('resize', function(e) {
		google.maps.event.trigger(maps[map_id], "resize");
		maps[map_id].setCenter(maps_markers[map_id].getPosition());
		maps[map_id].panBy(40,-70);
	});
}
jQuery(document).on('em_search_ajax', function(e, vars, wrapper){
	if( em_maps_loaded ){
		wrapper.find('div.em-location-map').each( function(index, el){ em_maps_load_location(el); } );
		wrapper.find('div.em-locations-map').each( function(index, el){ em_maps_load_locations(el); });
	}
});
//Load single maps (each map is treated as a seperate map).
function em_maps() {
	//Find all the maps on this page and load them
	jQuery('div.em-location-map').each( function(index, el){ em_maps_load_location(el); } );
	jQuery('div.em-locations-map').each( function(index, el){ em_maps_load_locations(el); } );

	//Location stuff - only needed if inputs for location exist
	if( jQuery('select#location-select-id, input#location-address').length > 0 ){
		var map, marker;
		//load map info
		var refresh_map_location = function(){
			var location_latitude = jQuery('#location-latitude').val();
			var location_longitude = jQuery('#location-longitude').val();
			if( !(location_latitude == 0 && location_longitude == 0) ){
				var position = new google.maps.LatLng(location_latitude, location_longitude); //the location coords
				marker.setPosition(position);
				var mapTitle = (jQuery('input#location-name').length > 0) ? jQuery('input#location-name').val():jQuery('input#title').val();
				mapTitle = em_esc_attr(mapTitle);
				marker.setTitle( mapTitle );
				jQuery('#em-map').show();
				jQuery('#em-map-404').hide();
				google.maps.event.trigger(map, 'resize');
				map.setCenter(position);
				map.panBy(40,-55);
				infoWindow.setContent(
					'<div id="location-balloon-content"><strong>' + mapTitle + '</strong><br>' +
					em_esc_attr(jQuery('#location-address').val()) +
					'<br>' + em_esc_attr(jQuery('#location-town').val()) +
					'</div>'
				);
				infoWindow.open(map, marker);
				jQuery(document).triggerHandler('em_maps_location_hook', [map, infoWindow, marker, 0]);
			} else {
				jQuery('#em-map').hide();
				jQuery('#em-map-404').show();
			}
		};

		//Add listeners for changes to address
		var get_map_by_id = function(id){
			if(jQuery('#em-map').length > 0){
				jQuery('#em-map-404 .em-loading-maps').show();
				jQuery.getJSON(document.URL,{ em_ajax_action:'get_location', id:id }, function(data){
					if( data.location_latitude!=0 && data.location_longitude!=0 ){
						loc_latlng = new google.maps.LatLng(data.location_latitude, data.location_longitude);
						marker.setPosition(loc_latlng);
						marker.setTitle( data.location_name );
						marker.setDraggable(false);
						jQuery('#em-map').show();
						jQuery('#em-map-404').hide();
						jQuery('#em-map-404 .em-loading-maps').hide();
						map.setCenter(loc_latlng);
						map.panBy(40,-55);
						infoWindow.setContent( '<div id="location-balloon-content">'+ data.location_balloon +'</div>');
						infoWindow.open(map, marker);
						google.maps.event.trigger(map, 'resize');
						jQuery(document).triggerHandler('em_maps_location_hook', [map, infoWindow, marker, 0]);
					}else{
						jQuery('#em-map').hide();
						jQuery('#em-map-404').show();
						jQuery('#em-map-404 .em-loading-maps').hide();
					}
				});
			}
		};
		jQuery('#location-select-id, input#location-id').on('change', function(){get_map_by_id(jQuery(this).val());} );
		jQuery('#location-name, #location-town, #location-address, #location-state, #location-postcode, #location-country').on('change', function(){
			//build address
			if( jQuery(this).prop('readonly') === true ) return;
			var addresses = [ jQuery('#location-address').val(), jQuery('#location-town').val(), jQuery('#location-state').val(), jQuery('#location-postcode').val() ];
			var address = '';
			jQuery.each( addresses, function(i, val){
				if( val != '' ){
					address = ( address == '' ) ? address+val:address+', '+val;
				}
			});
			if( address == '' ){ //in case only name is entered, no address
				jQuery('#em-map').hide();
				jQuery('#em-map-404').show();
				return false;
			}
			//do country last, as it's using the text version
			if( jQuery('#location-country option:selected').val() != 0 ){
				address = ( address == '' ) ? address+jQuery('#location-country option:selected').text():address+', '+jQuery('#location-country option:selected').text();
			}
			//add working indcator whilst we search
			jQuery('#em-map-404 .em-loading-maps').show();
			//search!
			if( address != '' && jQuery('#em-map').length > 0 ){
				geocoder.geocode( { 'address': address }, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						jQuery('#location-latitude').val(results[0].geometry.location.lat());
						jQuery('#location-longitude').val(results[0].geometry.location.lng());
					}
					refresh_map_location();
				});
			}
		});

		//Load map
		if(jQuery('#em-map').length > 0){
			var em_LatLng = new google.maps.LatLng(0, 0);
			var map_options = {
				zoom: 14,
				center: em_LatLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControl: false,
				gestureHandling: 'cooperative'
			};
			if( typeof EM.google_maps_styles !== 'undefined' ){ map_options.styles = EM.google_maps_styles; }
			map = new google.maps.Map( document.getElementById('em-map'), map_options);
			var marker = new google.maps.Marker({
				position: em_LatLng,
				map: map,
				draggable: true
			});
			infoWindow = new google.maps.InfoWindow({
				content: ''
			});
			var geocoder = new google.maps.Geocoder();
			google.maps.event.addListener(infoWindow, 'domready', function() {
				document.getElementById('location-balloon-content').parentNode.style.overflow='';
				document.getElementById('location-balloon-content').parentNode.parentNode.style.overflow='';
			});
			google.maps.event.addListener(marker, 'dragend', function() {
				var position = marker.getPosition();
				jQuery('#location-latitude').val(position.lat());
				jQuery('#location-longitude').val(position.lng());
				map.setCenter(position);
				map.panBy(40,-55);
			});
			if( jQuery('#location-select-id').length > 0 ){
				jQuery('#location-select-id').trigger('change');
			}else{
				refresh_map_location();
			}
			jQuery(document).triggerHandler('em_map_loaded', [map, infoWindow, marker]);
		}
		//map resize listener
		jQuery(window).on('resize', function(e) {
			google.maps.event.trigger(map, "resize");
			map.setCenter(marker.getPosition());
			map.panBy(40,-55);
		});
	}
	em_maps_loaded = true; //maps have been loaded
	jQuery(document).triggerHandler('em_maps_loaded');
}

function em_map_infobox(marker, message, map) {
	var iw = new google.maps.InfoWindow({ content: message });
	google.maps.event.addListener(marker, 'click', function() {
		if( infoWindow ) infoWindow.close();
		infoWindow = iw;
		iw.open(map,marker);
	});
}

function em_esc_attr( str ){
	if( typeof str !== 'string' ) return '';
	return str.replace(/</gi,'&lt;').replace(/>/gi,'&gt;');
}

// Modal Open/Close
let openModal = function( modal, onOpen = null ){
	modal = jQuery(modal);
	modal.appendTo(document.body);
	setTimeout( function(){
		modal.addClass('active').find('.em-modal-popup').addClass('active');
		jQuery(document).triggerHandler('em_modal_open', [modal]);
		if( typeof onOpen === 'function' ){
			setTimeout( onOpen, 200); // timeout allows css transition
		}
	}, 100); // timeout allows css transition
};
let closeModal = function( modal, onClose = null ){
	modal = jQuery(modal);
	modal.removeClass('active').find('.em-modal-popup').removeClass('active');
	setTimeout( function(){
		if( modal.attr('data-parent') ){
			let wrapper = jQuery('#' + modal.attr('data-parent') );
			if( wrapper.length ) {
				modal.appendTo(wrapper);
			}
		}
		modal.triggerHandler('em_modal_close');
		if( typeof onClose === 'function' ){
			onClose();
		}
	}, 500); // timeout allows css transition
}
jQuery(document).on('click', '.em-modal .em-close-modal', function(e){
	let modal = jQuery(this).closest('.em-modal');
	if( !modal.attr('data-prevent-close') ) {
		closeModal(modal);
	}
});
jQuery(document).on('click', '.em-modal', function(e){
	var target = jQuery(e.target);
	if( target.hasClass('em-modal') ) {
		let modal = jQuery(this);
		if( !modal.attr('data-prevent-close') ){
			closeModal(modal);
		}
	}
});

function EM_Alert( content ){
	// find the alert modal, create if not
	let modal = document.getElementById('em-alert-modal');
	if( modal === null ){
		modal = document.createElement('div');
		modal.setAttribute('class', "em pixelbones em-modal");
		modal.id = 'em-alert-modal';
		modal.innerHTML = '<div class="em-modal-popup"><header><a class="em-close-modal"></a><div class="em-modal-title">&nbsp;</div></header><div class="em-modal-content" id="em-alert-modal-content"></div></div>';
		document.body.append(modal);
	}
	document.getElementById('em-alert-modal-content').innerHTML = content;
	openModal(modal);
};

//Events Search
jQuery(document).ready( function($){
	// handle the views tip/ddm
	let views_ddm_options = {
		theme : 'light-border',
		allowHTML : true,
		interactive : true,
		trigger : 'manual',
		placement : 'bottom',
		zIndex : 1000000,
		touch: true,
	};
	$(document).trigger('em-search-views-trigger-vars',[views_ddm_options]);
	let tooltip_vars = { theme : 'light-border', appendTo : 'parent', touch : false, };
	$(document).trigger('em-tippy-vars',[tooltip_vars]);

	// sync main search texts to advanced search
	let search_forms = $('.em-search:not(.em-search-advanced)');
	search_forms.each( function(){
		/*
		 * Important references we'll reuse in scope
		 */
		let search = $(this);
		let search_id = search.attr('id').replace('em-search-', '');
		let search_form = search.find('.em-search-form').first();
		let search_advanced = search.find('.em-search-advanced'); // there should only be one anyway

		/*
		 * Counter functions
		 */
		const update_input_count = function( input, qty = 1 ){
			let el = jQuery(input);
			let total = qty > 0 ? qty : null;
			el.attr('data-advanced-total-input', total);
			update_search_totals();
		};

		const update_search_totals = function( applied = false ){
			// set everything to 0, recount
			search.find('span.total-count').remove();
			// find all fields with total attributes and sum them up
			let total = 0;
			search_advanced.find('[data-advanced-total-input]').each( function(){
				let total_input = this.getAttribute('data-advanced-total-input');
				total += Math.abs( total_input );
			});
			search.attr('data-advanced-total', total);
			update_trigger_count( applied );
			// find all sections with totals and display them (added above)
			search_advanced.find('.em-search-advanced-section').each( function(){
				let section = $(this);
				let section_total = 0;
				section.attr('data-advanced-total', 0);
				// go through all set qtys and calculate
				section.find('[data-advanced-total-input]').each( function(){
					let total_input = this.getAttribute('data-advanced-total-input');
					section_total += Math.abs( total_input );
				});
				section.attr('data-advanced-total', section_total);
				update_section_count(section);
			});
			// update triggers, search, clear button etc.
			if( total > 0 || !search.attr('data-advanced-previous-total') || total != search.attr('data-advanced-previous-total') ){
				update_submit_buttons( true );
			}
			update_clear_button_count();
		}

		const update_trigger_count = function( applied = false ){
			let triggers = jQuery('.em-search-advanced-trigger[data-search-advanced-id="em-search-advanced-'+ search_id +'"]'); // search like this ato apply to external triggers
			triggers.find('span.total-count').remove();
			let total = search.attr('data-advanced-total');
			if( total > 0 ){
				let trigger_count = jQuery('<span class="total-count">'+ total + '</span>').appendTo(triggers);
				if( !applied ){
					trigger_count.addClass('tentative');
				}
			}
		};

		const update_submit_buttons = function( enabled = false ){
			// update the clear link
			let submit_button = search_advanced.find('button[type="submit"]');
			let main_submit_button = search.find('.em-search-main-bar button[type="submit"]');
			let submit_buttons = submit_button.add( main_submit_button ); // merge together to apply chanegs
			if( enabled ){
				submit_buttons.removeClass('disabled').attr('aria-disabled', 'false');
			}else{
				submit_buttons.addClass('disabled').attr('aria-disabled', 'true');
			}
		};

		const update_section_count = function( section ){
			let section_total = section.attr('data-advanced-total');
			section.find('header span.total-count').remove();
			if( section_total > 0 ){
				$('<span class="total-count">'+ section_total +'</span>').appendTo( section.find('header') );
			}
		};

		const update_clear_button_count = function(){
			// update the clear link
			let clear_link = search_advanced.find('button[type="reset"]');
			if( !clear_link.attr('data-placeholder') ){
				clear_link.attr('data-placeholder', clear_link.text());
			}
			let total = search.attr('data-advanced-total');
			if( total > 0 ){
				clear_link.text( clear_link.attr('data-placeholder') + ' (' + total + ')' ).prop('disabled', false);
				clear_link.removeClass('disabled').attr('aria-disabled', 'false');
			}else{
				clear_link.text( clear_link.attr('data-placeholder') );
				clear_link.addClass('disabled').attr('aria-disabled', 'true');
			}
		};

		/*
		 * Triggers
		 */
		search.find('.em-search-views-trigger').each( function(){
			tooltip_vars.content = this.parentElement.getAttribute('aria-label');
			let views_tooltip = tippy(this.parentElement, tooltip_vars);
			let views_content = this.parentElement.querySelector('.em-search-views-options');
			let views_content_parent = views_content.parentElement;
			let tippy_content = document.createElement('div');
			views_ddm_options.content = tippy_content;
			let views_ddm = tippy(this, views_ddm_options);
			views_ddm.setProps({
				onShow(instance){
					views_tooltip.disable();
					tippy_content.append(views_content);
				},
				onShown(instance){ // keyboard support
					views_content.querySelector('input:checked').focus();
				},
				onHidden(instance){
					views_tooltip.enable();
					if( views_content.parentElement !== views_content_parent ) {
						views_content_parent.append(views_content);
					}
				}
			});
			let tippy_listener = function(e){
				if( e.type === 'keydown' && !(e.which === 13 || e.which === 40) ) return false;
				e.preventDefault();
				e.stopPropagation();
				this._tippy.show();
				views_tooltip.hide();
			}
			this.addEventListener('click', tippy_listener);
			this.addEventListener('keydown', tippy_listener);
			this.firstElementChild.addEventListener('focus', function(e){
				views_ddm.hide();
				views_tooltip.enable();
				views_tooltip.show();
			});
			this.firstElementChild.addEventListener('blur', function(){
				views_tooltip.hide();
			});

			search.on('focus blur', '.em-search-views-options input', function(){
				if( document.activeElement === this ){
					this.parentElement.classList.add('focused');
				}else{
					this.parentElement.classList.remove('focused');
				}
			});

			search[0].addEventListener('change', function(){
				update_submit_buttons(true);
			});

			search.on('keydown click', '.em-search-views-options input', function( e ){
				// get relevant vars
				if( e.type === 'keydown' && e.which !== 13 ){
					if ( [37, 38, 39, 40].indexOf(e.which) !== -1 ) {
						if (e.which === 38) {
							if (this.parentElement.previousElementSibling) {
								this.parentElement.previousElementSibling.focus();
							}
						} else if (e.which === 40) {
							if (this.parentElement.nextElementSibling) {
								this.parentElement.nextElementSibling.focus();
							}
						}
						return false;
					} else if ( e.which === 9 ) {
						// focus out
						views_ddm.hide();
					}
					return true;
				}
				this.checked = true;
				let input = $(this);
				// mark label selected
				input.closest('fieldset').find('label').removeClass('checked');
				input.parent().addClass('checked');
				// get other reference elements we need
				let views_wrapper = $(this).closest('.em-search-views');
				let view_type = this.value;
				let trigger = views_wrapper.children('.em-search-views-trigger');
				let trigger_option = trigger.children('.em-search-view-option');
				// change view, if different
				if( view_type !== trigger_option.attr('data-view') ){
					trigger_option.attr('data-view', this.value).text(this.parentElement.innerText);
					// remove custom search vals from current view so it's not used into another view
					$('#em-view-'+search_id).find('#em-view-custom-data-search-'+search_id).remove();
					// trigger custom event in case form disabled due to no search vals
					search_form.find('button[type="submit"]').focus();
					search_form.trigger('forcesubmit');
				}
				views_ddm.hide();
			});
		});

		search.find('.em-search-sort-trigger').each( function(){
			tooltip_vars.content = this.parentElement.getAttribute('aria-label');
			let views_tooltip = tippy(this.parentElement, tooltip_vars);
			search.on('keydown click', '.em-search-sort-option', function( e ){
				// get other reference elements we need
				let order = this.dataset.sort === 'ASC' ? 'DESC' : 'ASC';
				this.setAttribute('data-sort', order);
				this.parentElement.querySelector('input[name="order"]').value = order;
				// trigger custom event in case form disabled due to no search vals
				search_form.find('button[type="submit"]').focus();
				search_form.trigger('forcesubmit');
			});
		});

		// add trigger logic for advanced popup modal
		let search_advanced_trigger_click = function( e ){
			if( search.hasClass('advanced-mode-inline') ){
				// inline
				if( !search_advanced.hasClass('visible') ){
					search_advanced.slideDown().addClass('visible');
					if( '_tippy' in this ){
						this._tippy.setContent(this.getAttribute('data-label-hide'));
					}
				}else{
					search_advanced.slideUp().removeClass('visible');
					if( '_tippy' in this ){
						this._tippy.setContent(this.getAttribute('data-label-show'));
					}
				}
			}else{
				// wrap modal popup element in a form, so taht it's 'accessible' with keyboard
				if( !search_advanced.hasClass('active') ) {
					let form_wrapper = $('<form action="" method="post" class="em-search-advanced-form" id="em-search-form-advanced-' + search_id + '"></form>');
					form_wrapper.appendTo(search_advanced);
					search_advanced.find('.em-modal-popup').appendTo(form_wrapper);
					// open modal
					let button = this;
					openModal(search_advanced, function () {
						// Do this instead
						button.blur();
						search_advanced.find('input.em-search-text').focus();
					});
				}
			}
		};
		search.on('click', 'button.em-search-advanced-trigger:not([data-search-advanced-id],[data-parent-trigger])', search_advanced_trigger_click);
		search_form.on('search_advanced_trigger', search_advanced_trigger_click);

		search_advanced.on('em_modal_close', function(){
			search_advanced.find('.em-modal-popup').appendTo(search_advanced);
			search_advanced.children('form').remove();
			let trigger = search.find('button.em-search-advanced-trigger').focus();
			if( trigger.length > 0 && '_tippy' in trigger[0] ){
				trigger[0]._tippy.hide();
			}
		});

		// add header toggle logic to expand/collapse sections - add directly to elements since they move around the DOM due to modal
		search_advanced.find('.em-search-advanced-section > header').on('click', function(){
			let header = $(this);
			let section = header.closest('section');
			let content = header.siblings('.em-search-section-content');
			if( section.hasClass('active') ){
				content.slideUp();
				section.removeClass('active');
			}else{
				content.slideDown();
				section.addClass('active');
			}
		});

		/*
		 *  Advanced Search Field Listeners - Main Search Form
		 */

		let search_form_advanced_calculate_totals_inputs = function( input ){
			// for textboxes we only need to add or remove 1 advanced
			let el = $(input);
			let qty = el.val() !== '' ? 1:0;
			update_input_count( el, qty );
		};

		// These are for the main search bar, syncing information back into the advanced form
		search.on('change input', '.em-search-main-bar input.em-search-text', function( e ){
			// sync advanced input field with same text
			let advanced_search_input = search_advanced.find('input.em-search-text');
			if ( advanced_search_input.length === 0 ) {
				search_form_advanced_calculate_totals_inputs(this);
			} else {
				advanced_search_input.val( this.value );
				// recalculate totals from here
				search_form_advanced_calculate_totals_inputs(advanced_search_input[0]);
			}
			// any change without advanced form should show the search form still
			update_submit_buttons( true);
		});
		search.on('change', '.em-search-main-bar input.em-search-geo-coords', function(){
			let el = $(this);
			let advanced_geo = search_advanced.find('div.em-search-geo');
			// copy over value and class names
			let advanced_geo_coords = advanced_geo.find('input.em-search-geo-coords');
			if( advanced_geo_coords.length > 0 ) {
				advanced_geo_coords.val(el.val()).attr('class', el.attr('class'));
				let geo_text = el.siblings('input.em-search-geo').first();
				advanced_geo.find('input.em-search-geo').val(geo_text.val()).attr('class', geo_text.attr('class'));
				// calculate totals from here
				search_form_advanced_calculate_totals_inputs(advanced_geo_coords);
			} else {
				// calculate totals from here
				search_form_advanced_calculate_totals_inputs(this);
			}
		});
		search.find('.em-search-main-bar .em-datepicker input.em-search-scope.flatpickr-input').each( function(){
			if( !('_flatpickr' in this) ) return;
			this._flatpickr.config.onClose.push( function( selectedDates, dateStr, instance ) {
				// any change without advanced form should show the search form
				let advanced_datepicker = search_advanced.find('.em-datepicker input.em-search-scope.flatpickr-input');
				if( advanced_datepicker.length === 0 ) {
					// update counter
					let qty = dateStr ? 1:0;
					update_input_count(instance.input, qty);
				} else {
					// update advanced search form datepicker values, trigger a close for it to handle the rest
					advanced_datepicker[0]._flatpickr.setDate( selectedDates, true );
					advanced_datepicker[0]._flatpickr.close();
				}
			});
		});

		search.find('select.em-selectize').each(function () {
			if( 'selectize' in this ) {
				this.selectize.on('change', function () {
					search_advanced_selectize_change(this);
				});
			}
		});

		/*
		 *  Advanced Search Field Listeners - Advanced Search Form
		 */

		// regular text advanced or hidden inputs that represent another ui
		search_advanced.on('change input', 'input.em-search-text', function( e ){
			if( e.type === 'change' ){
				// copy over place info on change only, not on each keystroke
				search.find('.em-search-main input.em-search-text').val( this.value );
			}
			search_form_advanced_calculate_totals_inputs(this);
		});
		search_advanced.on('change', 'input.em-search-geo-coords', function( e ){
			search_form_advanced_calculate_totals_inputs(this);
			//update values in main search
			let el = $(this);
			let main = search.find('.em-search-main div.em-search-geo');
			if( main.length > 0 ){
				// copy over value and class names
				main.find('input.em-search-geo-coords').val( el.val() ).attr('class', el.attr('class'));
				let geo_text = el.siblings('input.em-search-geo');
				main.find('input.em-search-geo').val(geo_text.val()).attr('class', geo_text.attr('class'));
			}
		});
		search_advanced.on('clear_search', function(){
			let text = $(this).find('input.em-search-text');
			if( text.length === 0 ) {
				// select geo from main if it exists, so we keep counts synced
				text = search.find('input.em-search-text');
			}
			text.val('').trigger('change');
		});
		/* Not sure we should be calculating this... since it's always set to something.
		search_advanced.on('change', 'select.em-search-geo-unit, select.em-search-geo-distance', function( e ){
			// combine both values into parent, if value set then it's a toggle
			let el = jQuery(this);
			let qty = el.val() ? 1 : null;
			el.closest('.em-search-geo-units').attr('data-advanced-total-input', qty);
			update_search_totals();
		});
		 */
		search_advanced.on('change', 'input[type="checkbox"]', function( e ){
			let el = $(this);
			let qty = el.prop('checked') ? 1:0;
			update_input_count( el, qty );
		});
		search_advanced.on('calculate_totals', function(){
			search_advanced.find('input.em-search-text, input.em-search-geo-coords').each( function(){
				search_form_advanced_calculate_totals_inputs(this);
			});
			search_advanced.find('input[type="checkbox"]').trigger('change');
		});
		search_advanced.on('clear_search', function(){
			let geo = $(this).find('input.em-search-geo');
			if( geo.length === 0 ) {
				// select geo from main if it exists, so we keep counts synced
				geo = search.find('input.em-search-geo');
			}
			geo.removeClass('off').removeClass('on').val('');
			geo.siblings('input.em-search-geo-coords').val('').trigger('change');
			search_advanced.find('input[type="checkbox"]').prop("checked", false).trigger('change').prop("checked", false); // set checked after trigger because something seems to be checking during event
		});

		// datepicker advanced logic
		search_advanced.find('.em-datepicker input.em-search-scope.flatpickr-input').each( function(){
			if( !('_flatpickr' in this) ) return;
			this._flatpickr.config.onClose.push( function( selectedDates, dateStr, instance ) {
				// check previous value against current value, no change, no go
				let previous_value = instance.input.getAttribute('data-previous-value');
				if( previous_value !== dateStr ){
					// update counter
					let qty = dateStr ? 1:0;
					update_input_count(instance.input, qty);
					// update main search form datepicker values
					let main_datepicker = search.find('.em-search-main-bar .em-datepicker input.em-search-scope.flatpickr-input');
					if( main_datepicker.length > 0 ) {
						main_datepicker[0]._flatpickr.setDate(selectedDates, true);
					}
					// set for next time
					instance.input.setAttribute('data-previous-value', dateStr);
				}
			});
		});
		search_advanced.on('calculate_totals', function(){
			search_advanced.find('.em-datepicker input.em-search-scope.flatpickr-input').first().each( function(){
				let qty = this._flatpickr.selectedDates.length > 0 ? 1 : 0;
				update_input_count(this, qty);
			});
		});
		search_advanced.on('clear_search', function(){
			let datepickers = search_advanced.find('.em-datepicker input.em-search-scope.flatpickr-input');
			if( datepickers.length === 0 ) {
				// find datepickers on main form so syncing is sent up
				datepickers = search.find('.em-datepicker input.em-search-scope.flatpickr-input');
			}
			datepickers.each(function () {
				this._flatpickr.clear();
				update_input_count(this, 0);
			});
		});
		// clear the date total for calendars, before anything is done
		let scope_calendar_check = function(){
			search.find('.em-datepicker input.em-search-scope.flatpickr-input').each( function(){
				if( search.attr('data-view') == 'calendar' ){
					this.setAttribute('data-advanced-total-input', 0);
					this._flatpickr.input.disabled = true;
				}else{
					this._flatpickr.input.disabled = false;
					let qty = this._flatpickr.selectedDates.length > 0 ? 1 : 0;
					this.setAttribute('data-advanced-total-input', qty);
				}
			});
		};
		$(document).on('em_search_loaded', scope_calendar_check);
		scope_calendar_check();

		// selectize advanced
		let search_advanced_selectize_change = function( selectize ){
			let qty = selectize.items.length;
			// handle 'all' default values
			if( qty == 1 && !selectize.items[0] ){
				qty = 0;
			}
			if ( selectize.$input.closest('.em-search-advanced').length === 0 ) {
				// sync advanced input field with same text
				let classSearch = '.' + selectize.$input.attr('class').replaceAll(' ', '.').trim();
				let advanced_search_input = search_advanced.find( classSearch );
				if ( advanced_search_input.length > 0 ) {
					// copy over values
					advanced_search_input[0].selectize.setValue( selectize.items );
					// recalculate totals from here
					search_advanced_selectize_change(advanced_search_input[0].selectize);
				}
			}
			update_input_count( selectize.$input, qty );
		};

		search_advanced.find('select.em-selectize').each(function () {
			if( 'selectize' in this ) {
				this.selectize.on('change', function () {
					search_advanced_selectize_change(this);
				});
			}
		});
		search_advanced.on('calculate_totals', function(){
			$(this).find('select.em-selectize').each( function(){
				search_advanced_selectize_change(this.selectize);
			});
		});
		search_advanced.on('clear_search', function(){
			let clearSearch = function(){
				this.selectize.clear();
				this.selectize.refreshItems();
				this.selectize.refreshOptions(false);
				this.selectize.blur();
			};
			search_advanced.find('select.em-selectize').each( clearSearch );
			search.find('.em-search-main-bar select.em-selectize').each( clearSearch );
		});

		// location-specific stuff for dropdowns (powered by selectize)
		let locations_selectize_load_complete = function(){
			if( 'selectize' in this ) {
				this.selectize.settings.placeholder = this.selectize.settings.original_placeholder;
				this.selectize.updatePlaceholder();
				// get options from select again
				let options = [];
				this.selectize.$input.find('option').each( function(){
					let value = this.value !== null ? this.value : this.innerHTML;
					options.push({ value : value, text: this.innerHTML});
				});
				this.selectize.addOption(options);
				this.selectize.refreshOptions(false);
			}
		};
		let locations_selectize_load_start = function(){
			if( 'selectize' in this ){
				this.selectize.clearOptions();
				if( !('original_placeholder' in this.selectize.settings) ) this.selectize.settings.original_placeholder = this.selectize.settings.placeholder;
				this.selectize.settings.placeholder = EM.txt_loading;
				this.selectize.updatePlaceholder();
			}
		};
		$('.em-search-advanced select[name=country], .em-search select[name=country]').on('change', function(){
			var el = $(this);
			let wrapper = el.closest('.em-search-location');
			wrapper.find('select[name=state]').html('<option value="">'+EM.txt_loading+'</option>');
			wrapper.find('select[name=region]').html('<option value="">'+EM.txt_loading+'</option>');
			wrapper.find('select[name=town]').html('<option value="">'+EM.txt_loading+'</option>');
			wrapper.find('select[name=state], select[name=region], select[name=town]').each( locations_selectize_load_start );
			if( el.val() != '' ){
				wrapper.find('.em-search-location-meta').slideDown();
				var data = {
					action : 'search_states',
					country : el.val(),
					return_html : true,
				};
				wrapper.find('select[name=state]').load( EM.ajaxurl, data, locations_selectize_load_complete );
				data.action = 'search_regions';
				wrapper.find('select[name=region]').load( EM.ajaxurl, data, locations_selectize_load_complete );
				data.action = 'search_towns';
				wrapper.find('select[name=town]').load( EM.ajaxurl, data, locations_selectize_load_complete );
			}else{
				wrapper.find('.em-search-location-meta').slideUp();
			}
		});
		$('.em-search-advanced select[name=region], .em-search select[name=region]').on('change', function(){
			var el = $(this);
			let wrapper = el.closest('.em-search-location');
			wrapper.find('select[name=state]').html('<option value="">'+EM.txt_loading+'</option>');
			wrapper.find('select[name=town]').html('<option value="">'+EM.txt_loading+'</option>');
			wrapper.find('select[name=state], select[name=town]').each( locations_selectize_load_start );
			var data = {
				action : 'search_states',
				region : el.val(),
				country : wrapper.find('select[name=country]').val(),
				return_html : true
			};
			wrapper.find('select[name=state]').load( EM.ajaxurl, data, locations_selectize_load_complete );
			data.action = 'search_towns';
			wrapper.find('select[name=town]').load( EM.ajaxurl, data, locations_selectize_load_complete );
		});
		$('.em-search-advanced select[name=state], .em-search select[name=state]').on('change', function(){
			var el = $(this);
			let wrapper = el.closest('.em-search-location');
			wrapper.find('select[name=town]').html('<option value="">'+EM.txt_loading+'</option>').each( locations_selectize_load_start );
			var data = {
				action : 'search_towns',
				state : el.val(),
				region : wrapper.find('select[name=region]').val(),
				country : wrapper.find('select[name=country]').val(),
				return_html : true
			};
			wrapper.find('select[name=town]').load( EM.ajaxurl, data, locations_selectize_load_complete );
		});

		/*
		 *  Clear & Search Actions
		 */
		// handle clear link for advanced
		search_advanced.on( 'click', 'button[type="reset"]', function(){
			// clear text search advanced, run clear hook for other parts to hook into
			if( search.attr('data-advanced-total') == 0 ) return;
			// search text and geo search
			search_advanced.find('input.em-search-text, input.em-search-geo').val('').attr('data-advanced-total-input', null).trigger('change');
			// other implementations hook here and do what you need
			search.trigger('clear_search');
			search_advanced.trigger('clear_search');
			// remove counters, set data counters to 0, hide section and submit form without search settings
			update_search_totals(true); // in theory, this is 0 and removes everything
			if( search_advanced.hasClass('em-modal') ) {
				search_advanced_trigger_click();
			}
			search_advanced.append('<input name="clear_search" type="hidden" value="1">');
			search_advanced.find('button[type="submit"]').trigger('forceclick');
			update_clear_button_count();
		}).each( function(){
			search_advanced.trigger('calculate_totals');
			update_search_totals(true);
		});
		const on_update_trigger_count = function(e, applied = true){
			update_trigger_count( applied );
		};
		search.on('update_trigger_count', on_update_trigger_count);
		search_advanced.on('update_trigger_count', on_update_trigger_count);

		// handle submission for advanced
		search_advanced.on( 'click forceclick', 'button[type="submit"]', function(e){
			e.preventDefault();
			if( this.classList.contains('disabled') && e.type !== 'forceclick' ) return false;
			// close attach back to search form
			if( search_advanced.hasClass('em-modal') ) {
				closeModal(search_advanced, function () {
					// submit for search
					search_form.submit();
				});
			}else{
				search_form.submit();
			}
			return false; // we handled it
		});

		search.on('submit forcesubmit', '.em-search-form', function(e){
			e.preventDefault();
			let form = $(this);
			let submit_buttons = form.find('button[type="submit"]');
			if( e.type !== 'forcesubmit' && submit_buttons.hasClass('disabled') ) return false;
			let wrapper = form.closest('.em-search');
			if( wrapper.hasClass('em-search-legacy') ){
				em_submit_legacy_search_form(form);
			}else{
				let view = $('#em-view-'+search_id);
				let view_type = form.find('[name="view"]:checked, [name="view"][type="hidden"], .em-search-view-option-hidden').val();
				if( Array.isArray(view_type) ) view_type = view_type.shift();
				// copy over custom view information, remove it further down
				let custom_view_data = view.find('#em-view-custom-data-search-'+search_id).clone();
				let custom_view_data_container = $('<div class="em-view-custom-data"></div>');
				custom_view_data.children().appendTo(custom_view_data_container);
				custom_view_data.remove();
				custom_view_data_container.appendTo(form);
				// add loading stuff
				view.append('<div class="em-loading"></div>');
				submit_buttons.each( function(){
					if( EM.txt_searching !== this.innerHTML ) {
						this.setAttribute('data-button-text', this.innerHTML);
						this.innerHTML = EM.txt_searching;
					}
				});
				var vars = form.serialize();
				$.ajax( EM.ajaxurl, {
					type : 'POST',
					dataType : 'html',
					data : vars,
					success : function(responseText){
						submit_buttons.each( function(){
							this.innerHTML = this.getAttribute('data-button-text');
						});
						view = EM_View_Updater( view, responseText );
						// update view definitions
						view.attr('data-view', view_type);
						search.attr('data-view', view_type);
						search_advanced.attr('data-view', view_type);
						jQuery(document).triggerHandler('em_view_loaded_'+view_type, [view, form, e]);
						jQuery(document).triggerHandler('em_search_loaded', [view, form, e]); // ajax has loaded new results
						jQuery(document).triggerHandler('em_search_result', [vars, view, e]); // legacy for backcompat, use the above
						wrapper.find('.count.tentative').removeClass('tentative');
						// deactivate submit button until changes are made again
						submit_buttons.addClass('disabled').attr('aria-disabled', 'true');
						// update search totals
						update_search_totals(true);
						search.attr('data-advanced-previous-total', search.attr('data-advanced-total')); // so we know if filters were used in previous search
						update_submit_buttons(false);
						custom_view_data_container.remove(); // remove data so it's reloaded again later
						search.find('input[name="clear_search"]').remove();
					}
				});
			}
			return false;
		});

		// observe resizing
		EM_ResizeObserver( EM.search.breakpoints, [search[0]]);
	});

	// handle external triggers, e.g. a calendar shortcut for a hidden search form
	$(document).on('click', '.em-search-advanced-trigger[data-search-advanced-id], .em-search-advanced-trigger[data-parent-trigger]', function(){
		if( this.getAttribute('data-search-advanced-id') ){
			// trigger the search form by parent
			let search_advanced_form = document.getElementById( this.getAttribute('data-search-advanced-id') );
			if( search_advanced_form ){
				let search_form = search_advanced_form.closest('form.em-search-form');
				if( search_form ){
					search_form.dispatchEvent( new CustomEvent('search_advanced_trigger') );
					return;
				}
			}
		} else if( this.getAttribute('data-parent-trigger') ) {
			let trigger = document.getElementById(this.getAttribute('data-parent-trigger'));
			if ( trigger ) {
				trigger.click();
				return;
			}
		}
		console.log('Cannot locate a valid advanced search form trigger for %o', this);
	});

	$(document).on('click', '.em-view-container .em-ajax.em-pagination a.page-numbers', function(e){
		let a = $(this);
		let view = a.closest('.em-view-container');
		let href = a.attr('href');
		//add data-em-ajax att if it exists
		let data = a.closest('.em-pagination').attr('data-em-ajax');
		if( data ){
			href += href.includes('?') ? '&' : '?';
			href += data;
		}
		// build querystring from url
		let url_params = new URL(href, window.location.origin).searchParams;
		if( view.attr('data-view') ) {
			url_params.set('view', view.attr('data-view'));
		}
		// start ajax
		view.append('<div class="loading" id="em-loading"></div>');
		$.ajax( EM.ajaxurl, {
			type : 'POST',
			dataType : 'html',
			data : url_params.toString(),
			success : function(responseText) {
				view = EM_View_Updater( view, responseText );
				view.find('.em-pagination').each( function(){
					paginationObserver.observe(this);
				});
				jQuery(document).triggerHandler('em_page_loaded', [view]);
				view[0].scrollIntoView({ behavior: "smooth" });
			}
		});
		e.preventDefault();
		return false;
	});

	const paginationObserver = new ResizeObserver( function( entries ){
		for (let entry of entries) {
			let el = entry.target;
			if( !el.classList.contains('observing') ) {
				el.classList.add('observing'); // prevent endless loop on resizing within this check
				// check if any pagination parts are overflowing
				let overflowing = false;
				el.classList.remove('overflowing');
				for ( const item of el.querySelectorAll('.not-current')) {
					if( item.scrollHeight > item.clientHeight || item.scrollWidth > item.clientWidth ){
						overflowing = true;
						break; // break if one has overflown
					}
				};
				// add or remove overflow classes
				if( overflowing ){
					el.classList.add('overflowing')
				}
				el.classList.remove('observing');
			}
		}
	});
	$('.em-pagination').each( function(){
		paginationObserver.observe(this);
	});

	/* START Legacy */
	// deprecated - hide/show the advanced search advanced link - relevant for old template overrides
	$(document).on('click change', '.em-search-legacy .em-toggle', function(e){
		e.preventDefault();
		//show or hide advanced search, hidden by default
		var el = $(this);
		var rel = el.attr('rel').split(':');
		if( el.hasClass('show-search') ){
			if( rel.length > 1 ){ el.closest(rel[1]).find(rel[0]).slideUp(); }
			else{ $(rel[0]).slideUp(); }
			el.find('.show, .show-advanced').show();
			el.find('.hide, .hide-advanced').hide();
			el.removeClass('show-search');
		}else{
			if( rel.length > 1 ){ el.closest(rel[1]).find(rel[0]).slideDown(); }
			else{ $(rel[0]).slideDown(); }
			el.find('.show, .show-advanced').hide();
			el.find('.hide, .hide-advanced').show();
			el.addClass('show-search');
		}
	});
	// handle search form submission
	let em_submit_legacy_search_form = function( form ){
		if( this.em_search && this.em_search.value == EM.txt_search){ this.em_search.value = ''; }
		var results_wrapper = form.closest('.em-search-wrapper').find('.em-search-ajax');
		if( results_wrapper.length == 0 ) results_wrapper = $('.em-search-ajax');
		if( results_wrapper.length > 0 ){
			results_wrapper.append('<div class="loading" id="em-loading"></div>');
			var submitButton = form.find('.em-search-submit button');
			submitButton.attr('data-button-text', submitButton.val()).val(EM.txt_searching);
			var img = submitButton.children('img');
			if( img.length > 0 ) img.attr('src', img.attr('src').replace('search-mag.png', 'search-loading.gif'));
			var vars = form.serialize();
			$.ajax( EM.ajaxurl, {
				type : 'POST',
				dataType : 'html',
				data : vars,
				success : function(responseText){
					submitButton.val(submitButton.attr('data-button-text'));
					if( img.length > 0 ) img.attr('src', img.attr('src').replace('search-loading.gif', 'search-mag.png'));
					results_wrapper.replaceWith(responseText);
					if( form.find('input[name=em_search]').val() == '' ){ form.find('input[name=em_search]').val(EM.txt_search); }
					//reload results_wrapper
					results_wrapper = form.closest('.em-search-wrapper').find('.em-search-ajax');
					if( results_wrapper.length == 0 ) results_wrapper = $('.em-search-ajax');
					jQuery(document).triggerHandler('em_search_ajax', [vars, results_wrapper, e]); //ajax has loaded new results
				}
			});
			e.preventDefault();
			return false;
		}
	};
	if( $('.em-search-ajax').length > 0 ){
		$(document).on('click', '.em-search-ajax a.page-numbers', function(e){
			var a = $(this);
			var data = a.closest('.em-pagination').attr('data-em-ajax');
			var wrapper = a.closest('.em-search-ajax');
			var wrapper_parent = wrapper.parent();
			var qvars = a.attr('href').split('?');
			var vars = qvars[1];
			//add data-em-ajax att if it exists
			if( data != '' ){
				vars = vars != '' ? vars+'&'+data : data;
			}
			vars += '&legacy=1';
			wrapper.append('<div class="loading" id="em-loading"></div>');
			$.ajax( EM.ajaxurl, {
				type : 'POST',
				dataType : 'html',
				data : vars,
				success : function(responseText) {
					wrapper.replaceWith(responseText);
					wrapper = wrapper_parent.find('.em-search-ajax');
					jQuery(document).triggerHandler('em_search_ajax', [vars, wrapper, e]); //ajax has loaded new results
				}
			});
			e.preventDefault();
			return false;
		});
	}
	/* END Legacy */
});

/*
* CALENDAR
*/
jQuery(document).ready( function($){

	const em_calendar_init = function( calendar ){
		calendar = $(calendar);
		if( !calendar.attr('id') || !calendar.attr('id').match(/^em-calendar-[0-9+]$/) ){
			calendar.attr('id', 'em-calendar-' + Math.floor(Math.random() * 10000)); // retroactively add id to old templates
		}
		calendar.find('a').off("click");
		calendar.on('click', 'a.em-calnav, a.em-calnav-today', function(e){
			e.preventDefault();
			const el = $(this);
			if( el.data('disabled') == 1 || el.attr('href') === '') return; // do nothing if disabled or no link provided
			el.closest('.em-calendar').prepend('<div class="loading" id="em-loading"></div>');
			let url = em_ajaxify(el.attr('href'));
			const calendar_id = calendar.attr('id').replace('em-calendar-', '');
			const custom_data = $('form#em-view-custom-data-calendar-'+ calendar_id);
			let form_data = new FormData();
			if( custom_data.length > 0 ){
				form_data = new FormData(custom_data[0]);
				let url_params = new URL(url, window.location.origin).searchParams;
				for (const [key, value] of url_params.entries()) {
					form_data.set(key, value);
				}
			}
			// check advanced trigger
			if( calendar.hasClass('with-advanced') ){
				form_data.set('has_advanced_trigger', 1);
			}
			$.ajax({
				url: url,
				data: form_data,
				processData: false,
				contentType: false,
				method: 'POST',
				success: function( data ){
					let view = EM_View_Updater( calendar, data );
					if( view.hasClass('em-view-container') ){
						calendar = view.find('.em-calendar');
					}else{
						calendar = view;
					}
					calendar.trigger('em_calendar_load');
				},
				dataType: 'html'
			});
		} );
		let calendar_trigger_ajax = function( calendar, year, month ){
			let link = calendar.find('.em-calnav-next');
			let url = new URL(link.attr('href'), window.location.origin);
			url.searchParams.set('mo', month);
			url.searchParams.set('yr', year);
			link.attr('href', url.toString()).trigger('click');
		};
		let calendar_resize_monthpicker = function( instance, text ){
			let span = $('<span class="marker">'+ text +'</span>');
			span.insertAfter(instance);
			let width = span.width() + 40;
			span.remove();
			instance.style.setProperty('width', width+'px', 'important');
		}
		let calendar_month_init = function(){
			let month_form = calendar.find('.month form');
			calendar.find('.event-style-pill .em-cal-event').on('click', function( e ){
				e.preventDefault();
				if( !(calendar.hasClass('preview-tooltips') && calendar.data('preview-tooltips-trigger')) && !(calendar.hasClass('preview-modal')) ) {
					let link = this.getAttribute('data-event-url');
					if (link !== null) {
						window.location.href = link;
					}
				}
			});
			if( month_form.length > 0 ){
				month_form.find('input[type="submit"]').hide();
				let select = $('<select style="display:none;visibility:hidden;"></select>').appendTo(month_form);
				let option = $('<option></option>').appendTo(select);
				let current_datetime = calendar.find('select[name="month"]').val() + calendar.find('select[name="year"]').val();
				let month = calendar.find('select[name="month"]');
				let year = calendar.find('select[name="year"]');
				let monthpicker = calendar.find('.em-month-picker');
				let month_value = monthpicker.data('month-value');
				monthpicker.prop('type', 'text').prop('value', month_value);
				calendar_resize_monthpicker( monthpicker[0], month_value );
				let monthpicker_wrapper = $('#em-flatpickr');
				if( monthpicker_wrapper.length === 0 ) {
					monthpicker_wrapper = $('<div class="em pixelbones" id="em-flatpickr"></div>').appendTo('body');
				}
				let minDate = null;
				if( calendar.data('scope') === 'future' ){
					minDate = new Date();
					minDate.setMonth(minDate.getMonth()-1);
				}
				// locale
				if( 'locale' in EM.datepicker ){
					flatpickr.localize(flatpickr.l10ns[EM.datepicker.locale]);
					flatpickr.l10ns.default.firstDayOfWeek = EM.firstDay;
				}
				monthpicker.flatpickr({
					appendTo : monthpicker_wrapper[0],
					dateFormat : 'F Y',
					minDate : minDate,
					disableMobile: "true",
					plugins: [
						new monthSelectPlugin({
							shorthand: true, //defaults to false
							dateFormat: "F Y", //defaults to "F Y"
							altFormat: "F Y", //defaults to "F Y"
						})
					],
					onChange: function(selectedDates, dateStr, instance) {
						calendar_resize_monthpicker( instance.input, dateStr );
						calendar_trigger_ajax( calendar, selectedDates[0].getFullYear(), selectedDates[0].getMonth()+1);
					},
				});
				monthpicker.addClass('select-toggle')
				/* Disabling native picker at the moment, too quriky cross-browser
			}
			*/
			}
			if( calendar.hasClass('preview-tooltips') ){
				var tooltip_vars = {
					theme : 'light-border',
					allowHTML : true,
					interactive : true,
					trigger : 'mouseenter focus click',
					content(reference) {
						return document.createElement('div');
					},
					onShow( instance ){
						const id = instance.reference.getAttribute('data-event-id');
						const template = calendar.find('section.em-cal-events-content .em-cal-event-content[data-event-id="'+id+'"]');
						instance.props.content.append(template.first().clone()[0]);
					},
					onHide( instance ){
						instance.props.content.innerHTML = '';
					}
				};
				if( calendar.data('preview-tooltips-trigger') ) {
					tooltip_vars.trigger = calendar.data('preview-tooltips-trigger');
				}
				$(document).trigger('em-tippy-cal-event-vars',[tooltip_vars]);
				tippy(calendar.find('.em-cal-event').toArray(), tooltip_vars);
			}else if( calendar.hasClass('preview-modal') ){
				// Modal
				calendar.find('.em-cal-event').on('click', function(){
					const id = this.getAttribute('data-event-id');
					const modal = calendar.find('section.em-cal-events-content .em-cal-event-content[data-event-id="'+id+'"]');
					modal.attr('data-calendar-id', calendar.attr('id'));
					openModal(modal);
				});
			}
			// responsive mobile view for date clicks
			if( calendar.hasClass('responsive-dateclick-modal') ){
				calendar.find('.eventful .em-cal-day-date, .eventful-post .em-cal-day-date, .eventful-pre .em-cal-day-date').on('click', function( e ){
					//if( calendar.hasClass('size-small') || calendar.hasClass('size-medium') ){
					e.preventDefault();
					const id = this.getAttribute('data-calendar-date');
					const modal = calendar.find('.em-cal-date-content[data-calendar-date="'+id+'"]');
					modal.attr('data-calendar-id', calendar.attr('id'));
					openModal(modal);
					//}
				});
			}
			// observe resizing if not fixed
			if( !calendar.hasClass('size-fixed') ){
				EM_ResizeObserver( EM.calendar.breakpoints, [calendar[0], calendar[0]]);
			}
			// even aspect, because aspect ratio can screw up widths vs row template heights
			let calendar_body = calendar.find('.em-cal-body');
			if( calendar_body.hasClass('even-aspect') ) {
				let ro_function = function (el) {
					let width = el.firstElementChild.getBoundingClientRect().width;
					if (width > 0) {
						el.style.setProperty('--grid-auto-rows', 'minmax(' + width + 'px, auto)');
					}
				}
				let ro = new ResizeObserver(function (entries) {
					for (let entry of entries) {
						ro_function(entry.target);
					}
				});
				ro.observe(calendar_body[0]);
				ro_function(calendar_body[0]);
			}

			// figure out colors
			calendar.find('.date-day-colors').each( function(){
				let colors = JSON.parse(this.getAttribute('data-colors'));
				let day = $(this).siblings('.em-cal-day-date.colored');
				let sides = {
					1 : { 1 : '--date-border-color', 'class' : 'one' },
					2 : { 1 : '--date-border-color-top', 2 : '--date-border-color-bottom', 'class' : 'two' },
					3 : { 1 : '--date-border-color-top', 2 : '--date-border-color-right', 3 : '--date-border-color-bottom', 'class' : 'three' },
					4 : { 1 : '--date-border-color-top', 2 : '--date-border-color-right', 3 : '--date-border-color-bottom', 4 : '--date-border-color-left', 'class' : 'four' },
				};
				for (let i = 0; i < colors.length; i += 4) {
					const ring_colors = colors.slice(i, i + 4);
					// add a ring
					let outer_ring = day.children().first();
					let new_ring = $('<div class="ring"></div>').prependTo(day);
					outer_ring.appendTo(new_ring);
					new_ring.addClass( sides[ring_colors.length].class );
					for ( let it = 0; it < ring_colors.length; it++ ){
						new_ring.css(sides[ring_colors.length][it+1], ring_colors[it]);
					}
				}
			});

			if( calendar.hasClass('with-advanced') ){
				const trigger = calendar.find('.em-search-advanced-trigger');
				const search_advanced = $('#'+trigger.attr('data-search-advanced-id'));
				search_advanced.triggerHandler('update_trigger_count');
			}
		}
		calendar_month_init();
		$(document).triggerHandler('em_calendar_loaded', [calendar]);
	};
	$('.em-calendar').each( function(){
		let calendar = $(this);
		em_calendar_init( calendar );
	});
	$(document).on('em_calendar_load', '.em-calendar', function(){
		em_calendar_init( this );
	});
	$(document).on('em_view_loaded_calendar', function( e, view, form ){
		let calendar;
		if( view.hasClass('em-calendar') ){
			calendar = view;
		}else {
			calendar = view.find('.em-calendar').first();
		}
		em_calendar_init( calendar );
	});
});

let EM_View_Updater = function( element, html ){
	let content = jQuery(html);
	let view = element.hasClass('em-view-container') ? element : element.parent('.em-view-container');
	if( view.length > 0 ){
		if( content.hasClass('em-view-container') ){
			view.replaceWith(content);
			view = content;
		}else{
			view.empty().append(content);
		}
	}else{
		// create a view if possible
		if( content.hasClass('em-view-container') ){
			element.replaceWith(content);
			view = content;
		}else if( content.attr('data-view-id') ){
			let view = jQuery('<div class="em em-view-container"></div>');
			let view_id = content.attr('data-view-id');
			view.attr('data-view-id', view_id);
			view.attr('id', 'em-view-'+ view_id);
			view.attr('data-view-type', content.attr('data-view-type'));
			view.append(content);
			element.replaceWith(view);
		}
	}
	em_setup_ui_elements( view );
	return view;
}

/**
 * Resize watcher for EM elements. Supply an object (localized array) of name > breakpoint, in order of least to largest and it'll add size-{name} class name according to the breakpoint.
 * An object item with value false will represent any screensize, i.e. it should be the last value when all breakpoints aren't met.
 * @param breakpoints
 * @param elements
 * @constructor
 */
let EM_ResizeObserver = function( breakpoints, elements ){
	const ro = new ResizeObserver( function( entries ){
		for (let entry of entries) {
			let el = entry.target;
			if( !el.classList.contains('size-fixed') ) {
				for (const [name, breakpoint] of Object.entries(breakpoints)) {
					if (el.offsetWidth <= breakpoint || breakpoint === false) {
						for (let breakpoint_name of Object.keys(breakpoints)) {
							if (breakpoint_name !== name) el.classList.remove('size-' + breakpoint_name);
						}
						el.classList.add('size-' + name);
						break;
					}
				}
			}
		}
	});
	elements.forEach( function( el ){
		if( typeof el !== 'undefined' ){
			ro.observe(el);
		}
	});
	return ro;
};

// event list and event page (inc. some extra booking form logic) - todo/cleanup
jQuery(document).ready( function($){
	// Events List
	let breakpoints = {
		'small' : 600,
		'large' : false,
	}
	const events_ro = EM_ResizeObserver( breakpoints, $('.em-list').toArray() );
	$(document).on('em_page_loaded em_view_loaded_list em_view_loaded_list-grouped em_view_loaded_grid', function( e, view ){
		let new_elements = view.find('.em-list').each( function(){
			if( !this.classList.contains('size-fixed') ){
				events_ro.observe( this );
			}
		});
	});

	$(document).on('click', '.em-grid .em-item[data-href]', function(e){
		if( e.target.type !== 'a' ){
			window.location.href = this.getAttribute('data-href');
		}
	});

	// Single event area
	breakpoints = {
		'small' : 600,
		'medium' : 900,
		'large' : false,
	}
	const event_ro = EM_ResizeObserver( breakpoints, $('.em-item-single').toArray() );
	$(document).on('em_view_loaded', function( e, view ){
		let new_elements = view.find('.em-event-single').each( function(){
			if( !this.classList.contains('size-fixed') ){
				event_ro.observe( this );
			}
		});
	});
	// booking form area (WIP)
	$(document).on("click", ".em-event-booking-form .em-login-trigger a", function( e ){
		e.preventDefault();
		var parent = $(this).closest('.em-event-booking-form');
		parent.find('.em-login-trigger').hide();
		parent.find('.em-login-content').fadeIn();
		let login_form = parent.find('.em-login');
		login_form[0].scrollIntoView({
			behavior: 'smooth'
		});
		login_form.first().find('input[name="log"]').focus();

	});
	$(document).on("click", ".em-event-booking-form .em-login-cancel", function( e ){
		e.preventDefault();
		let parent = $(this).closest('.em-event-booking-form');
		parent.find('.em-login-content').hide();
		parent.find('.em-login-trigger').show();
	});
	EM_ResizeObserver( {'small': 500, 'large' : false}, $('.em-login').toArray());

});

// handle generic ajax submission forms vanilla style
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('form.em-ajax-form').forEach( function(el){
		el.addEventListener('submit', function(e){
			e.preventDefault();
			let form = e.currentTarget;
			let formData =  new FormData(form);
			let button = form.querySelector('button[type="submit"]');
			let loader;

			if( form.classList.contains('no-overlay-spinner') ){
				form.classList.add('loading');
			}else{
				let loader = document.createElement('div');
				loader.id = 'em-loading';
				form.append(loader);
			}

			var request = new XMLHttpRequest();
			if( form.getAttribute('data-api-url') ){
				request.open('POST', form.getAttribute('data-api-url'), true);
				request.setRequestHeader('X-WP-Nonce', EM.api_nonce);
			}else{
				request.open('POST', EM.ajaxurl, true);
			}

			request.onload = function() {
				if( loader ) loader.remove();
				if (this.status >= 200 && this.status < 400) {
					// Success!
					try {
						let data = JSON.parse(this.response);
						let notice;
						if( !form.classList.contains('no-inline-notice') ){
							notice = form.querySelector('.em-notice');
							if( !notice ){
								notice = document.createElement('div');
								form.prepend(notice);
								if( formData.get('action') ){
									form.dispatchEvent( new CustomEvent( 'em_ajax_form_success_' + formData.get('action'), {
										detail : {
											form : form,
											notice : notice,
											response : data,
										}
									}) );
								}
							}
							notice.innerHTML = '';
							notice.setAttribute('class', 'em-notice');
						}
						if( data.result ){
							if( !form.classList.contains('no-inline-notice') ){
								notice.classList.add('em-notice-success');
								notice.innerHTML = data.message;
								form.replaceWith(notice);
							}else{
								form.classList.add('load-successful');
								form.classList.remove('loading');
								if( data.message ){
									EM_Alert(data.message);
								}
							}
						}else{
							if( !form.classList.contains('no-inline-notice') ){
								notice.classList.add('em-notice-error');
								notice.innerHTML = data.errors;
							}else{
								EM_Alert(data.errors);
							}
						}
					} catch(e) {
						alert( 'Error Encountered : ' + e);
					}
				} else {
					alert('Error encountered... please see debug logs or contact support.');
				}
				form.classList.remove('loading');
			};

			request.onerror = function() {
				alert('Connection error encountered... please see debug logs or contact support.');
			};

			request.send( formData );
			return false;
		});
	});
});

// phone numbers
window.addEventListener('load', function(){
	// enable for now only in beta if EM_PHONE_INTL_ENABLED PHP Constant is true
	if ( !EM.phone ) return false;

	/*!
	 * International Telephone Input v18.2.1
	 * https://github.com/jackocnr/intl-tel-input.git
	 */
	(function(factory){if(typeof module==="object"&&module.exports)module.exports=factory();else window.intlTelInput=factory()})(function(undefined){"use strict";return function(){var allCountries=[["Afghanistan (‫افغانستان‬‎)","af","93"],["Albania (Shqipëri)","al","355"],["Algeria (‫الجزائر‬‎)","dz","213"],["American Samoa","as","1",5,["684"]],["Andorra","ad","376"],["Angola","ao","244"],["Anguilla","ai","1",6,["264"]],["Antigua and Barbuda","ag","1",7,["268"]],["Argentina","ar","54"],["Armenia (Հայաստան)","am","374"],["Aruba","aw","297"],["Ascension Island","ac","247"],["Australia","au","61",0],["Austria (Österreich)","at","43"],["Azerbaijan (Azərbaycan)","az","994"],["Bahamas","bs","1",8,["242"]],["Bahrain (‫البحرين‬‎)","bh","973"],["Bangladesh (বাংলাদেশ)","bd","880"],["Barbados","bb","1",9,["246"]],["Belarus (Беларусь)","by","375"],["Belgium (België)","be","32"],["Belize","bz","501"],["Benin (Bénin)","bj","229"],["Bermuda","bm","1",10,["441"]],["Bhutan (འབྲུག)","bt","975"],["Bolivia","bo","591"],["Bosnia and Herzegovina (Босна и Херцеговина)","ba","387"],["Botswana","bw","267"],["Brazil (Brasil)","br","55"],["British Indian Ocean Territory","io","246"],["British Virgin Islands","vg","1",11,["284"]],["Brunei","bn","673"],["Bulgaria (България)","bg","359"],["Burkina Faso","bf","226"],["Burundi (Uburundi)","bi","257"],["Cambodia (កម្ពុជា)","kh","855"],["Cameroon (Cameroun)","cm","237"],["Canada","ca","1",1,["204","226","236","249","250","263","289","306","343","354","365","367","368","382","387","403","416","418","428","431","437","438","450","584","468","474","506","514","519","548","579","581","584","587","604","613","639","647","672","683","705","709","742","753","778","780","782","807","819","825","867","873","902","905"]],["Cape Verde (Kabu Verdi)","cv","238"],["Caribbean Netherlands","bq","599",1,["3","4","7"]],["Cayman Islands","ky","1",12,["345"]],["Central African Republic (République centrafricaine)","cf","236"],["Chad (Tchad)","td","235"],["Chile","cl","56"],["China (中国)","cn","86"],["Christmas Island","cx","61",2,["89164"]],["Cocos (Keeling) Islands","cc","61",1,["89162"]],["Colombia","co","57"],["Comoros (‫جزر القمر‬‎)","km","269"],["Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)","cd","243"],["Congo (Republic) (Congo-Brazzaville)","cg","242"],["Cook Islands","ck","682"],["Costa Rica","cr","506"],["Côte d’Ivoire","ci","225"],["Croatia (Hrvatska)","hr","385"],["Cuba","cu","53"],["Curaçao","cw","599",0],["Cyprus (Κύπρος)","cy","357"],["Czech Republic (Česká republika)","cz","420"],["Denmark (Danmark)","dk","45"],["Djibouti","dj","253"],["Dominica","dm","1",13,["767"]],["Dominican Republic (República Dominicana)","do","1",2,["809","829","849"]],["Ecuador","ec","593"],["Egypt (‫مصر‬‎)","eg","20"],["El Salvador","sv","503"],["Equatorial Guinea (Guinea Ecuatorial)","gq","240"],["Eritrea","er","291"],["Estonia (Eesti)","ee","372"],["Eswatini","sz","268"],["Ethiopia","et","251"],["Falkland Islands (Islas Malvinas)","fk","500"],["Faroe Islands (Føroyar)","fo","298"],["Fiji","fj","679"],["Finland (Suomi)","fi","358",0],["France","fr","33"],["French Guiana (Guyane française)","gf","594"],["French Polynesia (Polynésie française)","pf","689"],["Gabon","ga","241"],["Gambia","gm","220"],["Georgia (საქართველო)","ge","995"],["Germany (Deutschland)","de","49"],["Ghana (Gaana)","gh","233"],["Gibraltar","gi","350"],["Greece (Ελλάδα)","gr","30"],["Greenland (Kalaallit Nunaat)","gl","299"],["Grenada","gd","1",14,["473"]],["Guadeloupe","gp","590",0],["Guam","gu","1",15,["671"]],["Guatemala","gt","502"],["Guernsey","gg","44",1,["1481","7781","7839","7911"]],["Guinea (Guinée)","gn","224"],["Guinea-Bissau (Guiné Bissau)","gw","245"],["Guyana","gy","592"],["Haiti","ht","509"],["Honduras","hn","504"],["Hong Kong (香港)","hk","852"],["Hungary (Magyarország)","hu","36"],["Iceland (Ísland)","is","354"],["India (भारत)","in","91"],["Indonesia","id","62"],["Iran (‫ایران‬‎)","ir","98"],["Iraq (‫العراق‬‎)","iq","964"],["Ireland","ie","353"],["Isle of Man","im","44",2,["1624","74576","7524","7924","7624"]],["Israel (‫ישראל‬‎)","il","972"],["Italy (Italia)","it","39",0],["Jamaica","jm","1",4,["876","658"]],["Japan (日本)","jp","81"],["Jersey","je","44",3,["1534","7509","7700","7797","7829","7937"]],["Jordan (‫الأردن‬‎)","jo","962"],["Kazakhstan (Казахстан)","kz","7",1,["33","7"]],["Kenya","ke","254"],["Kiribati","ki","686"],["Kosovo","xk","383"],["Kuwait (‫الكويت‬‎)","kw","965"],["Kyrgyzstan (Кыргызстан)","kg","996"],["Laos (ລາວ)","la","856"],["Latvia (Latvija)","lv","371"],["Lebanon (‫لبنان‬‎)","lb","961"],["Lesotho","ls","266"],["Liberia","lr","231"],["Libya (‫ليبيا‬‎)","ly","218"],["Liechtenstein","li","423"],["Lithuania (Lietuva)","lt","370"],["Luxembourg","lu","352"],["Macau (澳門)","mo","853"],["Madagascar (Madagasikara)","mg","261"],["Malawi","mw","265"],["Malaysia","my","60"],["Maldives","mv","960"],["Mali","ml","223"],["Malta","mt","356"],["Marshall Islands","mh","692"],["Martinique","mq","596"],["Mauritania (‫موريتانيا‬‎)","mr","222"],["Mauritius (Moris)","mu","230"],["Mayotte","yt","262",1,["269","639"]],["Mexico (México)","mx","52"],["Micronesia","fm","691"],["Moldova (Republica Moldova)","md","373"],["Monaco","mc","377"],["Mongolia (Монгол)","mn","976"],["Montenegro (Crna Gora)","me","382"],["Montserrat","ms","1",16,["664"]],["Morocco (‫المغرب‬‎)","ma","212",0],["Mozambique (Moçambique)","mz","258"],["Myanmar (Burma) (မြန်မာ)","mm","95"],["Namibia (Namibië)","na","264"],["Nauru","nr","674"],["Nepal (नेपाल)","np","977"],["Netherlands (Nederland)","nl","31"],["New Caledonia (Nouvelle-Calédonie)","nc","687"],["New Zealand","nz","64"],["Nicaragua","ni","505"],["Niger (Nijar)","ne","227"],["Nigeria","ng","234"],["Niue","nu","683"],["Norfolk Island","nf","672"],["North Korea (조선 민주주의 인민 공화국)","kp","850"],["North Macedonia (Северна Македонија)","mk","389"],["Northern Mariana Islands","mp","1",17,["670"]],["Norway (Norge)","no","47",0],["Oman (‫عُمان‬‎)","om","968"],["Pakistan (‫پاکستان‬‎)","pk","92"],["Palau","pw","680"],["Palestine (‫فلسطين‬‎)","ps","970"],["Panama (Panamá)","pa","507"],["Papua New Guinea","pg","675"],["Paraguay","py","595"],["Peru (Perú)","pe","51"],["Philippines","ph","63"],["Poland (Polska)","pl","48"],["Portugal","pt","351"],["Puerto Rico","pr","1",3,["787","939"]],["Qatar (‫قطر‬‎)","qa","974"],["Réunion (La Réunion)","re","262",0],["Romania (România)","ro","40"],["Russia (Россия)","ru","7",0],["Rwanda","rw","250"],["Saint Barthélemy","bl","590",1],["Saint Helena","sh","290"],["Saint Kitts and Nevis","kn","1",18,["869"]],["Saint Lucia","lc","1",19,["758"]],["Saint Martin (Saint-Martin (partie française))","mf","590",2],["Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)","pm","508"],["Saint Vincent and the Grenadines","vc","1",20,["784"]],["Samoa","ws","685"],["San Marino","sm","378"],["São Tomé and Príncipe (São Tomé e Príncipe)","st","239"],["Saudi Arabia (‫المملكة العربية السعودية‬‎)","sa","966"],["Senegal (Sénégal)","sn","221"],["Serbia (Србија)","rs","381"],["Seychelles","sc","248"],["Sierra Leone","sl","232"],["Singapore","sg","65"],["Sint Maarten","sx","1",21,["721"]],["Slovakia (Slovensko)","sk","421"],["Slovenia (Slovenija)","si","386"],["Solomon Islands","sb","677"],["Somalia (Soomaaliya)","so","252"],["South Africa","za","27"],["South Korea (대한민국)","kr","82"],["South Sudan (‫جنوب السودان‬‎)","ss","211"],["Spain (España)","es","34"],["Sri Lanka (ශ්‍රී ලංකාව)","lk","94"],["Sudan (‫السودان‬‎)","sd","249"],["Suriname","sr","597"],["Svalbard and Jan Mayen","sj","47",1,["79"]],["Sweden (Sverige)","se","46"],["Switzerland (Schweiz)","ch","41"],["Syria (‫سوريا‬‎)","sy","963"],["Taiwan (台灣)","tw","886"],["Tajikistan","tj","992"],["Tanzania","tz","255"],["Thailand (ไทย)","th","66"],["Timor-Leste","tl","670"],["Togo","tg","228"],["Tokelau","tk","690"],["Tonga","to","676"],["Trinidad and Tobago","tt","1",22,["868"]],["Tunisia (‫تونس‬‎)","tn","216"],["Turkey (Türkiye)","tr","90"],["Turkmenistan","tm","993"],["Turks and Caicos Islands","tc","1",23,["649"]],["Tuvalu","tv","688"],["U.S. Virgin Islands","vi","1",24,["340"]],["Uganda","ug","256"],["Ukraine (Україна)","ua","380"],["United Arab Emirates (‫الإمارات العربية المتحدة‬‎)","ae","971"],["United Kingdom","gb","44",0],["United States","us","1",0],["Uruguay","uy","598"],["Uzbekistan (Oʻzbekiston)","uz","998"],["Vanuatu","vu","678"],["Vatican City (Città del Vaticano)","va","39",1,["06698"]],["Venezuela","ve","58"],["Vietnam (Việt Nam)","vn","84"],["Wallis and Futuna (Wallis-et-Futuna)","wf","681"],["Western Sahara (‫الصحراء الغربية‬‎)","eh","212",1,["5288","5289"]],["Yemen (‫اليمن‬‎)","ye","967"],["Zambia","zm","260"],["Zimbabwe","zw","263"],["Åland Islands","ax","358",1,["18"]]];for(var i=0;i<allCountries.length;i++){var c=allCountries[i];allCountries[i]={name:c[0],iso2:c[1],dialCode:c[2],priority:c[3]||0,areaCodes:c[4]||null}}"use strict";function _objectSpread(target){for(var i=1;i<arguments.length;i++){var source=arguments[i]!=null?Object(arguments[i]):{};var ownKeys=Object.keys(source);if(typeof Object.getOwnPropertySymbols==="function"){ownKeys.push.apply(ownKeys,Object.getOwnPropertySymbols(source).filter(function(sym){return Object.getOwnPropertyDescriptor(source,sym).enumerable}))}ownKeys.forEach(function(key){_defineProperty(target,key,source[key])})}return target}function _defineProperty(obj,key,value){key=_toPropertyKey(key);if(key in obj){Object.defineProperty(obj,key,{value:value,enumerable:true,configurable:true,writable:true})}else{obj[key]=value}return obj}function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor)){throw new TypeError("Cannot call a class as a function")}}function _defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||false;descriptor.configurable=true;if("value"in descriptor)descriptor.writable=true;Object.defineProperty(target,_toPropertyKey(descriptor.key),descriptor)}}function _createClass(Constructor,protoProps,staticProps){if(protoProps)_defineProperties(Constructor.prototype,protoProps);if(staticProps)_defineProperties(Constructor,staticProps);Object.defineProperty(Constructor,"prototype",{writable:false});return Constructor}function _toPropertyKey(arg){var key=_toPrimitive(arg,"string");return typeof key==="symbol"?key:String(key)}function _toPrimitive(input,hint){if(typeof input!=="object"||input===null)return input;var prim=input[Symbol.toPrimitive];if(prim!==undefined){var res=prim.call(input,hint||"default");if(typeof res!=="object")return res;throw new TypeError("@@toPrimitive must return a primitive value.")}return(hint==="string"?String:Number)(input)}var intlTelInputGlobals={getInstance:function getInstance(input){var id=input.getAttribute("data-intl-tel-input-id");return window.intlTelInputGlobals.instances[id]},instances:{},documentReady:function documentReady(){return document.readyState==="complete"}};if(typeof window==="object"){window.intlTelInputGlobals=intlTelInputGlobals}var id=0;var defaults={allowDropdown:true,autoInsertDialCode:false,autoPlaceholder:"polite",customContainer:"",customPlaceholder:null,dropdownContainer:null,excludeCountries:[],formatOnDisplay:true,geoIpLookup:null,hiddenInput:"",initialCountry:"",localizedCountries:null,nationalMode:true,onlyCountries:[],placeholderNumberType:"MOBILE",preferredCountries:["us","gb"],separateDialCode:false,showFlags:true,utilsScript:""};var regionlessNanpNumbers=["800","822","833","844","855","866","877","880","881","882","883","884","885","886","887","888","889"];var forEachProp=function forEachProp(obj,callback){var keys=Object.keys(obj);for(var i=0;i<keys.length;i++){callback(keys[i],obj[keys[i]])}};var forEachInstance=function forEachInstance(method){forEachProp(window.intlTelInputGlobals.instances,function(key){window.intlTelInputGlobals.instances[key][method]()})};var Iti=function(){function Iti(input,options){var _this=this;_classCallCheck(this,Iti);this.id=id++;this.telInput=input;this.activeItem=null;this.highlightedItem=null;var customOptions=options||{};this.options={};forEachProp(defaults,function(key,value){_this.options[key]=customOptions.hasOwnProperty(key)?customOptions[key]:value});this.hadInitialPlaceholder=Boolean(input.getAttribute("placeholder"))}_createClass(Iti,[{key:"_init",value:function _init(){var _this2=this;if(this.options.nationalMode){this.options.autoInsertDialCode=false}if(this.options.separateDialCode){this.options.autoInsertDialCode=false}var forceShowFlags=this.options.allowDropdown&&!this.options.separateDialCode;if(!this.options.showFlags&&forceShowFlags){this.options.showFlags=true}this.isMobile=/Android.+Mobile|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);if(this.isMobile){document.body.classList.add("iti-mobile");if(!this.options.dropdownContainer){this.options.dropdownContainer=document.body}}this.isRTL=!!this.telInput.closest("[dir=rtl]");if(typeof Promise!=="undefined"){var autoCountryPromise=new Promise(function(resolve,reject){_this2.resolveAutoCountryPromise=resolve;_this2.rejectAutoCountryPromise=reject});var utilsScriptPromise=new Promise(function(resolve,reject){_this2.resolveUtilsScriptPromise=resolve;_this2.rejectUtilsScriptPromise=reject});this.promise=Promise.all([autoCountryPromise,utilsScriptPromise])}else{this.resolveAutoCountryPromise=this.rejectAutoCountryPromise=function(){};this.resolveUtilsScriptPromise=this.rejectUtilsScriptPromise=function(){}}this.selectedCountryData={};this._processCountryData();this._generateMarkup();this._setInitialState();this._initListeners();this._initRequests()}},{key:"_processCountryData",value:function _processCountryData(){this._processAllCountries();this._processCountryCodes();this._processPreferredCountries();if(this.options.localizedCountries){this._translateCountriesByLocale()}if(this.options.onlyCountries.length||this.options.localizedCountries){this.countries.sort(this._countryNameSort)}}},{key:"_addCountryCode",value:function _addCountryCode(iso2,countryCode,priority){if(countryCode.length>this.countryCodeMaxLen){this.countryCodeMaxLen=countryCode.length}if(!this.countryCodes.hasOwnProperty(countryCode)){this.countryCodes[countryCode]=[]}for(var i=0;i<this.countryCodes[countryCode].length;i++){if(this.countryCodes[countryCode][i]===iso2){return}}var index=priority!==undefined?priority:this.countryCodes[countryCode].length;this.countryCodes[countryCode][index]=iso2}},{key:"_processAllCountries",value:function _processAllCountries(){if(this.options.onlyCountries.length){var lowerCaseOnlyCountries=this.options.onlyCountries.map(function(country){return country.toLowerCase()});this.countries=allCountries.filter(function(country){return lowerCaseOnlyCountries.indexOf(country.iso2)>-1})}else if(this.options.excludeCountries.length){var lowerCaseExcludeCountries=this.options.excludeCountries.map(function(country){return country.toLowerCase()});this.countries=allCountries.filter(function(country){return lowerCaseExcludeCountries.indexOf(country.iso2)===-1})}else{this.countries=allCountries}}},{key:"_translateCountriesByLocale",value:function _translateCountriesByLocale(){for(var i=0;i<this.countries.length;i++){var iso=this.countries[i].iso2.toLowerCase();if(this.options.localizedCountries.hasOwnProperty(iso)){this.countries[i].name=this.options.localizedCountries[iso]}}}},{key:"_countryNameSort",value:function _countryNameSort(a,b){if(a.name<b.name){return-1}if(a.name>b.name){return 1}return 0}},{key:"_processCountryCodes",value:function _processCountryCodes(){this.countryCodeMaxLen=0;this.dialCodes={};this.countryCodes={};for(var i=0;i<this.countries.length;i++){var c=this.countries[i];if(!this.dialCodes[c.dialCode]){this.dialCodes[c.dialCode]=true}this._addCountryCode(c.iso2,c.dialCode,c.priority)}for(var _i=0;_i<this.countries.length;_i++){var _c=this.countries[_i];if(_c.areaCodes){var rootCountryCode=this.countryCodes[_c.dialCode][0];for(var j=0;j<_c.areaCodes.length;j++){var areaCode=_c.areaCodes[j];for(var k=1;k<areaCode.length;k++){var partialDialCode=_c.dialCode+areaCode.substr(0,k);this._addCountryCode(rootCountryCode,partialDialCode);this._addCountryCode(_c.iso2,partialDialCode)}this._addCountryCode(_c.iso2,_c.dialCode+areaCode)}}}}},{key:"_processPreferredCountries",value:function _processPreferredCountries(){this.preferredCountries=[];for(var i=0;i<this.options.preferredCountries.length;i++){var countryCode=this.options.preferredCountries[i].toLowerCase();var countryData=this._getCountryData(countryCode,false,true);if(countryData){this.preferredCountries.push(countryData)}}}},{key:"_createEl",value:function _createEl(name,attrs,container){var el=document.createElement(name);if(attrs){forEachProp(attrs,function(key,value){return el.setAttribute(key,value)})}if(container){container.appendChild(el)}return el}},{key:"_generateMarkup",value:function _generateMarkup(){if(!this.telInput.hasAttribute("autocomplete")&&!(this.telInput.form&&this.telInput.form.hasAttribute("autocomplete"))){this.telInput.setAttribute("autocomplete","off")}var _this$options=this.options,allowDropdown=_this$options.allowDropdown,separateDialCode=_this$options.separateDialCode,showFlags=_this$options.showFlags,customContainer=_this$options.customContainer,hiddenInput=_this$options.hiddenInput,dropdownContainer=_this$options.dropdownContainer;var parentClass="iti";if(allowDropdown){parentClass+=" iti--allow-dropdown"}if(separateDialCode){parentClass+=" iti--separate-dial-code"}if(showFlags){parentClass+=" iti--show-flags"}if(customContainer){parentClass+=" ".concat(customContainer)}var wrapper=this._createEl("div",{class:parentClass});this.telInput.parentNode.insertBefore(wrapper,this.telInput);var showFlagsContainer=allowDropdown||showFlags||separateDialCode;if(showFlagsContainer){this.flagsContainer=this._createEl("div",{class:"iti__flag-container"},wrapper)}wrapper.appendChild(this.telInput);if(showFlagsContainer){this.selectedFlag=this._createEl("div",_objectSpread({class:"iti__selected-flag"},allowDropdown&&{role:"combobox","aria-haspopup":"listbox","aria-controls":"iti-".concat(this.id,"__country-listbox"),"aria-expanded":"false","aria-label":"Telephone country code"}),this.flagsContainer)}if(showFlags){this.selectedFlagInner=this._createEl("div",{class:"iti__flag"},this.selectedFlag)}if(this.selectedFlag&&this.telInput.disabled){this.selectedFlag.setAttribute("aria-disabled","true")}if(separateDialCode){this.selectedDialCode=this._createEl("div",{class:"iti__selected-dial-code"},this.selectedFlag)}if(allowDropdown){if(!this.telInput.disabled){this.selectedFlag.setAttribute("tabindex","0")}this.dropdownArrow=this._createEl("div",{class:"iti__arrow"},this.selectedFlag);this.countryList=this._createEl("ul",{class:"iti__country-list iti__hide",id:"iti-".concat(this.id,"__country-listbox"),role:"listbox","aria-label":"List of countries"});if(this.preferredCountries.length){this._appendListItems(this.preferredCountries,"iti__preferred",true);this._createEl("li",{class:"iti__divider",role:"separator","aria-disabled":"true"},this.countryList)}this._appendListItems(this.countries,"iti__standard");if(dropdownContainer){this.dropdown=this._createEl("div",{class:"iti iti--container"});this.dropdown.appendChild(this.countryList)}else{this.flagsContainer.appendChild(this.countryList)}}if(hiddenInput){var hiddenInputName=hiddenInput;var name=this.telInput.getAttribute("name");if(name){var i=name.lastIndexOf("[");if(i!==-1){hiddenInputName="".concat(name.substr(0,i),"[").concat(hiddenInputName,"]")}}this.hiddenInput=this._createEl("input",{type:"hidden",name:hiddenInputName});wrapper.appendChild(this.hiddenInput)}}},{key:"_appendListItems",value:function _appendListItems(countries,className,preferred){var tmp="";for(var i=0;i<countries.length;i++){var c=countries[i];var idSuffix=preferred?"-preferred":"";tmp+="<li class='iti__country ".concat(className,"' tabIndex='-1' id='iti-").concat(this.id,"__item-").concat(c.iso2).concat(idSuffix,"' role='option' data-dial-code='").concat(c.dialCode,"' data-country-code='").concat(c.iso2,"' aria-selected='false'>");if(this.options.showFlags){tmp+="<div class='iti__flag-box'><div class='iti__flag iti__".concat(c.iso2,"'></div></div>")}tmp+="<span class='iti__country-name'>".concat(c.name,"</span>");tmp+="<span class='iti__dial-code'>+".concat(c.dialCode,"</span>");tmp+="</li>"}this.countryList.insertAdjacentHTML("beforeend",tmp)}},{key:"_setInitialState",value:function _setInitialState(){var attributeValue=this.telInput.getAttribute("value");var inputValue=this.telInput.value;var useAttribute=attributeValue&&attributeValue.charAt(0)==="+"&&(!inputValue||inputValue.charAt(0)!=="+");var val=useAttribute?attributeValue:inputValue;var dialCode=this._getDialCode(val);var isRegionlessNanp=this._isRegionlessNanp(val);var _this$options2=this.options,initialCountry=_this$options2.initialCountry,autoInsertDialCode=_this$options2.autoInsertDialCode;if(dialCode&&!isRegionlessNanp){this._updateFlagFromNumber(val)}else if(initialCountry!=="auto"){if(initialCountry){this._setFlag(initialCountry.toLowerCase())}else{if(dialCode&&isRegionlessNanp){this._setFlag("us")}else{this.defaultCountry=this.preferredCountries.length?this.preferredCountries[0].iso2:this.countries[0].iso2;if(!val){this._setFlag(this.defaultCountry)}}}if(!val&&autoInsertDialCode){this.telInput.value="+".concat(this.selectedCountryData.dialCode)}}if(val){this._updateValFromNumber(val)}}},{key:"_initListeners",value:function _initListeners(){this._initKeyListeners();if(this.options.autoInsertDialCode){this._initBlurListeners()}if(this.options.allowDropdown){this._initDropdownListeners()}if(this.hiddenInput){this._initHiddenInputListener()}}},{key:"_initHiddenInputListener",value:function _initHiddenInputListener(){var _this3=this;this._handleHiddenInputSubmit=function(){_this3.hiddenInput.value=_this3.getNumber()};if(this.telInput.form){this.telInput.form.addEventListener("submit",this._handleHiddenInputSubmit)}}},{key:"_getClosestLabel",value:function _getClosestLabel(){var el=this.telInput;while(el&&el.tagName!=="LABEL"){el=el.parentNode}return el}},{key:"_initDropdownListeners",value:function _initDropdownListeners(){var _this4=this;this._handleLabelClick=function(e){if(_this4.countryList.classList.contains("iti__hide")){_this4.telInput.focus()}else{e.preventDefault()}};var label=this._getClosestLabel();if(label){label.addEventListener("click",this._handleLabelClick)}this._handleClickSelectedFlag=function(){if(_this4.countryList.classList.contains("iti__hide")&&!_this4.telInput.disabled&&!_this4.telInput.readOnly){_this4._showDropdown()}};this.selectedFlag.addEventListener("click",this._handleClickSelectedFlag);this._handleFlagsContainerKeydown=function(e){var isDropdownHidden=_this4.countryList.classList.contains("iti__hide");if(isDropdownHidden&&["ArrowUp","Up","ArrowDown","Down"," ","Enter"].indexOf(e.key)!==-1){e.preventDefault();e.stopPropagation();_this4._showDropdown()}if(e.key==="Tab"){_this4._closeDropdown()}};this.flagsContainer.addEventListener("keydown",this._handleFlagsContainerKeydown)}},{key:"_initRequests",value:function _initRequests(){var _this5=this;if(this.options.utilsScript&&!window.intlTelInputUtils){if(window.intlTelInputGlobals.documentReady()){window.intlTelInputGlobals.loadUtils(this.options.utilsScript)}else{window.addEventListener("load",function(){window.intlTelInputGlobals.loadUtils(_this5.options.utilsScript)})}}else{this.resolveUtilsScriptPromise()}if(this.options.initialCountry==="auto"){this._loadAutoCountry()}else{this.resolveAutoCountryPromise()}}},{key:"_loadAutoCountry",value:function _loadAutoCountry(){if(window.intlTelInputGlobals.autoCountry){this.handleAutoCountry()}else if(!window.intlTelInputGlobals.startedLoadingAutoCountry){window.intlTelInputGlobals.startedLoadingAutoCountry=true;if(typeof this.options.geoIpLookup==="function"){this.options.geoIpLookup(function(countryCode){window.intlTelInputGlobals.autoCountry=countryCode.toLowerCase();setTimeout(function(){return forEachInstance("handleAutoCountry")})},function(){return forEachInstance("rejectAutoCountryPromise")})}}}},{key:"_initKeyListeners",value:function _initKeyListeners(){var _this6=this;this._handleKeyupEvent=function(){if(_this6._updateFlagFromNumber(_this6.telInput.value)){_this6._triggerCountryChange()}};this.telInput.addEventListener("keyup",this._handleKeyupEvent);this._handleClipboardEvent=function(){setTimeout(_this6._handleKeyupEvent)};this.telInput.addEventListener("cut",this._handleClipboardEvent);this.telInput.addEventListener("paste",this._handleClipboardEvent)}},{key:"_cap",value:function _cap(number){var max=this.telInput.getAttribute("maxlength");return max&&number.length>max?number.substr(0,max):number}},{key:"_initBlurListeners",value:function _initBlurListeners(){var _this7=this;this._handleSubmitOrBlurEvent=function(){_this7._removeEmptyDialCode()};if(this.telInput.form){this.telInput.form.addEventListener("submit",this._handleSubmitOrBlurEvent)}this.telInput.addEventListener("blur",this._handleSubmitOrBlurEvent)}},{key:"_removeEmptyDialCode",value:function _removeEmptyDialCode(){if(this.telInput.value.charAt(0)==="+"){var numeric=this._getNumeric(this.telInput.value);if(!numeric||this.selectedCountryData.dialCode===numeric){this.telInput.value=""}}}},{key:"_getNumeric",value:function _getNumeric(s){return s.replace(/\D/g,"")}},{key:"_trigger",value:function _trigger(name){var e=document.createEvent("Event");e.initEvent(name,true,true);this.telInput.dispatchEvent(e)}},{key:"_showDropdown",value:function _showDropdown(){this.countryList.classList.remove("iti__hide");this.selectedFlag.setAttribute("aria-expanded","true");this._setDropdownPosition();if(this.activeItem){this._highlightListItem(this.activeItem,false);this._scrollTo(this.activeItem,true)}this._bindDropdownListeners();this.dropdownArrow.classList.add("iti__arrow--up");this._trigger("open:countrydropdown")}},{key:"_toggleClass",value:function _toggleClass(el,className,shouldHaveClass){if(shouldHaveClass&&!el.classList.contains(className)){el.classList.add(className)}else if(!shouldHaveClass&&el.classList.contains(className)){el.classList.remove(className)}}},{key:"_setDropdownPosition",value:function _setDropdownPosition(){var _this8=this;if(this.options.dropdownContainer){this.options.dropdownContainer.appendChild(this.dropdown)}if(!this.isMobile){var pos=this.telInput.getBoundingClientRect();var windowTop=window.pageYOffset||document.documentElement.scrollTop;var inputTop=pos.top+windowTop;var dropdownHeight=this.countryList.offsetHeight;var dropdownFitsBelow=inputTop+this.telInput.offsetHeight+dropdownHeight<windowTop+window.innerHeight;var dropdownFitsAbove=inputTop-dropdownHeight>windowTop;this._toggleClass(this.countryList,"iti__country-list--dropup",!dropdownFitsBelow&&dropdownFitsAbove);if(this.options.dropdownContainer){var extraTop=!dropdownFitsBelow&&dropdownFitsAbove?0:this.telInput.offsetHeight;this.dropdown.style.top="".concat(inputTop+extraTop,"px");this.dropdown.style.left="".concat(pos.left+document.body.scrollLeft,"px");this._handleWindowScroll=function(){return _this8._closeDropdown()};window.addEventListener("scroll",this._handleWindowScroll)}}}},{key:"_getClosestListItem",value:function _getClosestListItem(target){var el=target;while(el&&el!==this.countryList&&!el.classList.contains("iti__country")){el=el.parentNode}return el===this.countryList?null:el}},{key:"_bindDropdownListeners",value:function _bindDropdownListeners(){var _this9=this;this._handleMouseoverCountryList=function(e){var listItem=_this9._getClosestListItem(e.target);if(listItem){_this9._highlightListItem(listItem,false)}};this.countryList.addEventListener("mouseover",this._handleMouseoverCountryList);this._handleClickCountryList=function(e){var listItem=_this9._getClosestListItem(e.target);if(listItem){_this9._selectListItem(listItem)}};this.countryList.addEventListener("click",this._handleClickCountryList);var isOpening=true;this._handleClickOffToClose=function(){if(!isOpening){_this9._closeDropdown()}isOpening=false};document.documentElement.addEventListener("click",this._handleClickOffToClose);var query="";var queryTimer=null;this._handleKeydownOnDropdown=function(e){e.preventDefault();if(e.key==="ArrowUp"||e.key==="Up"||e.key==="ArrowDown"||e.key==="Down"){_this9._handleUpDownKey(e.key)}else if(e.key==="Enter"){_this9._handleEnterKey()}else if(e.key==="Escape"){_this9._closeDropdown()}else if(/^[a-zA-ZÀ-ÿа-яА-Я ]$/.test(e.key)){if(queryTimer){clearTimeout(queryTimer)}query+=e.key.toLowerCase();_this9._searchForCountry(query);queryTimer=setTimeout(function(){query=""},1e3)}};document.addEventListener("keydown",this._handleKeydownOnDropdown)}},{key:"_handleUpDownKey",value:function _handleUpDownKey(key){var next=key==="ArrowUp"||key==="Up"?this.highlightedItem.previousElementSibling:this.highlightedItem.nextElementSibling;if(next){if(next.classList.contains("iti__divider")){next=key==="ArrowUp"||key==="Up"?next.previousElementSibling:next.nextElementSibling}this._highlightListItem(next,true)}}},{key:"_handleEnterKey",value:function _handleEnterKey(){if(this.highlightedItem){this._selectListItem(this.highlightedItem)}}},{key:"_searchForCountry",value:function _searchForCountry(query){for(var i=0;i<this.countries.length;i++){if(this._startsWith(this.countries[i].name,query)){var listItem=this.countryList.querySelector("#iti-".concat(this.id,"__item-").concat(this.countries[i].iso2));this._highlightListItem(listItem,false);this._scrollTo(listItem,true);break}}}},{key:"_startsWith",value:function _startsWith(a,b){return a.substr(0,b.length).toLowerCase()===b}},{key:"_updateValFromNumber",value:function _updateValFromNumber(originalNumber){var number=originalNumber;if(this.options.formatOnDisplay&&window.intlTelInputUtils&&this.selectedCountryData){var useNational=this.options.nationalMode||number.charAt(0)!=="+"&&!this.options.separateDialCode;var _intlTelInputUtils$nu=intlTelInputUtils.numberFormat,NATIONAL=_intlTelInputUtils$nu.NATIONAL,INTERNATIONAL=_intlTelInputUtils$nu.INTERNATIONAL;var format=useNational?NATIONAL:INTERNATIONAL;number=intlTelInputUtils.formatNumber(number,this.selectedCountryData.iso2,format)}number=this._beforeSetNumber(number);this.telInput.value=number}},{key:"_updateFlagFromNumber",value:function _updateFlagFromNumber(originalNumber){var number=originalNumber;var selectedDialCode=this.selectedCountryData.dialCode;var isNanp=selectedDialCode==="1";if(number&&isNanp&&number.charAt(0)!=="+"){if(number.charAt(0)!=="1"){number="1".concat(number)}number="+".concat(number)}if(this.options.separateDialCode&&selectedDialCode&&number.charAt(0)!=="+"){number="+".concat(selectedDialCode).concat(number)}var dialCode=this._getDialCode(number,true);var numeric=this._getNumeric(number);var countryCode=null;if(dialCode){var countryCodes=this.countryCodes[this._getNumeric(dialCode)];var alreadySelected=countryCodes.indexOf(this.selectedCountryData.iso2)!==-1&&numeric.length<=dialCode.length-1;var isRegionlessNanpNumber=selectedDialCode==="1"&&this._isRegionlessNanp(numeric);if(!isRegionlessNanpNumber&&!alreadySelected){for(var j=0;j<countryCodes.length;j++){if(countryCodes[j]){countryCode=countryCodes[j];break}}}}else if(number.charAt(0)==="+"&&numeric.length){countryCode=""}else if(!number||number==="+"){countryCode=this.defaultCountry}if(countryCode!==null){return this._setFlag(countryCode)}return false}},{key:"_isRegionlessNanp",value:function _isRegionlessNanp(number){var numeric=this._getNumeric(number);if(numeric.charAt(0)==="1"){var areaCode=numeric.substr(1,3);return regionlessNanpNumbers.indexOf(areaCode)!==-1}return false}},{key:"_highlightListItem",value:function _highlightListItem(listItem,shouldFocus){var prevItem=this.highlightedItem;if(prevItem){prevItem.classList.remove("iti__highlight")}this.highlightedItem=listItem;this.highlightedItem.classList.add("iti__highlight");this.selectedFlag.setAttribute("aria-activedescendant",listItem.getAttribute("id"));if(shouldFocus){this.highlightedItem.focus()}}},{key:"_getCountryData",value:function _getCountryData(countryCode,ignoreOnlyCountriesOption,allowFail){var countryList=ignoreOnlyCountriesOption?allCountries:this.countries;for(var i=0;i<countryList.length;i++){if(countryList[i].iso2===countryCode){return countryList[i]}}if(allowFail){return null}throw new Error("No country data for '".concat(countryCode,"'"))}},{key:"_setFlag",value:function _setFlag(countryCode){var _this$options3=this.options,allowDropdown=_this$options3.allowDropdown,separateDialCode=_this$options3.separateDialCode,showFlags=_this$options3.showFlags;var prevCountry=this.selectedCountryData.iso2?this.selectedCountryData:{};this.selectedCountryData=countryCode?this._getCountryData(countryCode,false,false):{};if(this.selectedCountryData.iso2){this.defaultCountry=this.selectedCountryData.iso2}if(showFlags){this.selectedFlagInner.setAttribute("class","iti__flag iti__".concat(countryCode))}this._setSelectedCountryFlagTitleAttribute(countryCode,separateDialCode);if(separateDialCode){var dialCode=this.selectedCountryData.dialCode?"+".concat(this.selectedCountryData.dialCode):"";this.selectedDialCode.innerHTML=dialCode;var selectedFlagWidth=this.selectedFlag.offsetWidth||this._getHiddenSelectedFlagWidth();if(this.isRTL){this.telInput.style.paddingRight="".concat(selectedFlagWidth+6,"px")}else{this.telInput.style.paddingLeft="".concat(selectedFlagWidth+6,"px")}}this._updatePlaceholder();if(allowDropdown){var prevItem=this.activeItem;if(prevItem){prevItem.classList.remove("iti__active");prevItem.setAttribute("aria-selected","false")}if(countryCode){var nextItem=this.countryList.querySelector("#iti-".concat(this.id,"__item-").concat(countryCode,"-preferred"))||this.countryList.querySelector("#iti-".concat(this.id,"__item-").concat(countryCode));nextItem.setAttribute("aria-selected","true");nextItem.classList.add("iti__active");this.activeItem=nextItem}}return prevCountry.iso2!==countryCode}},{key:"_setSelectedCountryFlagTitleAttribute",value:function _setSelectedCountryFlagTitleAttribute(countryCode,separateDialCode){if(!this.selectedFlag){return}var title;if(countryCode&&!separateDialCode){title="".concat(this.selectedCountryData.name,": +").concat(this.selectedCountryData.dialCode)}else if(countryCode){title=this.selectedCountryData.name}else{title="Unknown"}this.selectedFlag.setAttribute("title",title)}},{key:"_getHiddenSelectedFlagWidth",value:function _getHiddenSelectedFlagWidth(){var containerClone=this.telInput.parentNode.cloneNode();containerClone.style.visibility="hidden";document.body.appendChild(containerClone);var flagsContainerClone=this.flagsContainer.cloneNode();containerClone.appendChild(flagsContainerClone);var selectedFlagClone=this.selectedFlag.cloneNode(true);flagsContainerClone.appendChild(selectedFlagClone);var width=selectedFlagClone.offsetWidth;containerClone.parentNode.removeChild(containerClone);return width}},{key:"_updatePlaceholder",value:function _updatePlaceholder(){var shouldSetPlaceholder=this.options.autoPlaceholder==="aggressive"||!this.hadInitialPlaceholder&&this.options.autoPlaceholder==="polite";if(window.intlTelInputUtils&&shouldSetPlaceholder){var numberType=intlTelInputUtils.numberType[this.options.placeholderNumberType];var placeholder=this.selectedCountryData.iso2?intlTelInputUtils.getExampleNumber(this.selectedCountryData.iso2,this.options.nationalMode,numberType):"";placeholder=this._beforeSetNumber(placeholder);if(typeof this.options.customPlaceholder==="function"){placeholder=this.options.customPlaceholder(placeholder,this.selectedCountryData)}this.telInput.setAttribute("placeholder",placeholder)}}},{key:"_selectListItem",value:function _selectListItem(listItem){var flagChanged=this._setFlag(listItem.getAttribute("data-country-code"));this._closeDropdown();this._updateDialCode(listItem.getAttribute("data-dial-code"));this.telInput.focus();var len=this.telInput.value.length;this.telInput.setSelectionRange(len,len);if(flagChanged){this._triggerCountryChange()}}},{key:"_closeDropdown",value:function _closeDropdown(){this.countryList.classList.add("iti__hide");this.selectedFlag.setAttribute("aria-expanded","false");this.selectedFlag.removeAttribute("aria-activedescendant");this.dropdownArrow.classList.remove("iti__arrow--up");document.removeEventListener("keydown",this._handleKeydownOnDropdown);document.documentElement.removeEventListener("click",this._handleClickOffToClose);this.countryList.removeEventListener("mouseover",this._handleMouseoverCountryList);this.countryList.removeEventListener("click",this._handleClickCountryList);if(this.options.dropdownContainer){if(!this.isMobile){window.removeEventListener("scroll",this._handleWindowScroll)}if(this.dropdown.parentNode){this.dropdown.parentNode.removeChild(this.dropdown)}}this._trigger("close:countrydropdown")}},{key:"_scrollTo",value:function _scrollTo(element,middle){var container=this.countryList;var windowTop=window.pageYOffset||document.documentElement.scrollTop;var containerHeight=container.offsetHeight;var containerTop=container.getBoundingClientRect().top+windowTop;var containerBottom=containerTop+containerHeight;var elementHeight=element.offsetHeight;var elementTop=element.getBoundingClientRect().top+windowTop;var elementBottom=elementTop+elementHeight;var newScrollTop=elementTop-containerTop+container.scrollTop;var middleOffset=containerHeight/2-elementHeight/2;if(elementTop<containerTop){if(middle){newScrollTop-=middleOffset}container.scrollTop=newScrollTop}else if(elementBottom>containerBottom){if(middle){newScrollTop+=middleOffset}var heightDifference=containerHeight-elementHeight;container.scrollTop=newScrollTop-heightDifference}}},{key:"_updateDialCode",value:function _updateDialCode(newDialCodeBare){var inputVal=this.telInput.value;var newDialCode="+".concat(newDialCodeBare);var newNumber;if(inputVal.charAt(0)==="+"){var prevDialCode=this._getDialCode(inputVal);if(prevDialCode){newNumber=inputVal.replace(prevDialCode,newDialCode)}else{newNumber=newDialCode}this.telInput.value=newNumber}else if(this.options.autoInsertDialCode){if(inputVal){newNumber=newDialCode+inputVal}else{newNumber=newDialCode}this.telInput.value=newNumber}}},{key:"_getDialCode",value:function _getDialCode(number,includeAreaCode){var dialCode="";if(number.charAt(0)==="+"){var numericChars="";for(var i=0;i<number.length;i++){var c=number.charAt(i);if(!isNaN(parseInt(c,10))){numericChars+=c;if(includeAreaCode){if(this.countryCodes[numericChars]){dialCode=number.substr(0,i+1)}}else{if(this.dialCodes[numericChars]){dialCode=number.substr(0,i+1);break}}if(numericChars.length===this.countryCodeMaxLen){break}}}}return dialCode}},{key:"_getFullNumber",value:function _getFullNumber(){var val=this.telInput.value.trim();var dialCode=this.selectedCountryData.dialCode;var prefix;var numericVal=this._getNumeric(val);if(this.options.separateDialCode&&val.charAt(0)!=="+"&&dialCode&&numericVal){prefix="+".concat(dialCode)}else{prefix=""}return prefix+val}},{key:"_beforeSetNumber",value:function _beforeSetNumber(originalNumber){var number=originalNumber;if(this.options.separateDialCode){var dialCode=this._getDialCode(number);if(dialCode){dialCode="+".concat(this.selectedCountryData.dialCode);var start=number[dialCode.length]===" "||number[dialCode.length]==="-"?dialCode.length+1:dialCode.length;number=number.substr(start)}}return this._cap(number)}},{key:"_triggerCountryChange",value:function _triggerCountryChange(){this._trigger("countrychange")}},{key:"handleAutoCountry",value:function handleAutoCountry(){if(this.options.initialCountry==="auto"){this.defaultCountry=window.intlTelInputGlobals.autoCountry;if(!this.telInput.value){this.setCountry(this.defaultCountry)}this.resolveAutoCountryPromise()}}},{key:"handleUtils",value:function handleUtils(){if(window.intlTelInputUtils){if(this.telInput.value){this._updateValFromNumber(this.telInput.value)}this._updatePlaceholder()}this.resolveUtilsScriptPromise()}},{key:"destroy",value:function destroy(){var form=this.telInput.form;if(this.options.allowDropdown){this._closeDropdown();this.selectedFlag.removeEventListener("click",this._handleClickSelectedFlag);this.flagsContainer.removeEventListener("keydown",this._handleFlagsContainerKeydown);var label=this._getClosestLabel();if(label){label.removeEventListener("click",this._handleLabelClick)}}if(this.hiddenInput&&form){form.removeEventListener("submit",this._handleHiddenInputSubmit)}if(this.options.autoInsertDialCode){if(form){form.removeEventListener("submit",this._handleSubmitOrBlurEvent)}this.telInput.removeEventListener("blur",this._handleSubmitOrBlurEvent)}this.telInput.removeEventListener("keyup",this._handleKeyupEvent);this.telInput.removeEventListener("cut",this._handleClipboardEvent);this.telInput.removeEventListener("paste",this._handleClipboardEvent);this.telInput.removeAttribute("data-intl-tel-input-id");var wrapper=this.telInput.parentNode;wrapper.parentNode.insertBefore(this.telInput,wrapper);wrapper.parentNode.removeChild(wrapper);delete window.intlTelInputGlobals.instances[this.id]}},{key:"getExtension",value:function getExtension(){if(window.intlTelInputUtils){return intlTelInputUtils.getExtension(this._getFullNumber(),this.selectedCountryData.iso2)}return""}},{key:"getNumber",value:function getNumber(format){if(window.intlTelInputUtils){var iso2=this.selectedCountryData.iso2;return intlTelInputUtils.formatNumber(this._getFullNumber(),iso2,format)}return""}},{key:"getNumberType",value:function getNumberType(){if(window.intlTelInputUtils){return intlTelInputUtils.getNumberType(this._getFullNumber(),this.selectedCountryData.iso2)}return-99}},{key:"getSelectedCountryData",value:function getSelectedCountryData(){return this.selectedCountryData}},{key:"getValidationError",value:function getValidationError(){if(window.intlTelInputUtils){var iso2=this.selectedCountryData.iso2;return intlTelInputUtils.getValidationError(this._getFullNumber(),iso2)}return-99}},{key:"isValidNumber",value:function isValidNumber(){var val=this._getFullNumber().trim();return window.intlTelInputUtils?intlTelInputUtils.isValidNumber(val,this.selectedCountryData.iso2):null}},{key:"isPossibleNumber",value:function isPossibleNumber(){var val=this._getFullNumber().trim();return window.intlTelInputUtils?intlTelInputUtils.isPossibleNumber(val,this.selectedCountryData.iso2):null}},{key:"setCountry",value:function setCountry(originalCountryCode){var countryCode=originalCountryCode.toLowerCase();if(this.selectedCountryData.iso2!==countryCode){this._setFlag(countryCode);this._updateDialCode(this.selectedCountryData.dialCode);this._triggerCountryChange()}}},{key:"setNumber",value:function setNumber(number){var flagChanged=this._updateFlagFromNumber(number);this._updateValFromNumber(number);if(flagChanged){this._triggerCountryChange()}}},{key:"setPlaceholderNumberType",value:function setPlaceholderNumberType(type){this.options.placeholderNumberType=type;this._updatePlaceholder()}}]);return Iti}();intlTelInputGlobals.getCountryData=function(){return allCountries};var injectScript=function injectScript(path,handleSuccess,handleFailure){var script=document.createElement("script");script.onload=function(){forEachInstance("handleUtils");if(handleSuccess){handleSuccess()}};script.onerror=function(){forEachInstance("rejectUtilsScriptPromise");if(handleFailure){handleFailure()}};script.className="iti-load-utils";script.async=true;script.src=path;document.body.appendChild(script)};intlTelInputGlobals.loadUtils=function(path){if(!window.intlTelInputUtils&&!window.intlTelInputGlobals.startedLoadingUtilsScript){window.intlTelInputGlobals.startedLoadingUtilsScript=true;if(typeof Promise!=="undefined"){return new Promise(function(resolve,reject){return injectScript(path,resolve,reject)})}injectScript(path)}return null};intlTelInputGlobals.defaults=defaults;intlTelInputGlobals.version="18.2.1";return function(input,options){var iti=new Iti(input,options);iti._init();input.setAttribute("data-intl-tel-input-id",iti.id);window.intlTelInputGlobals.instances[iti.id]=iti;return iti}}()});

	let getCountry = function() {
		var timezones = {
			"Africa/Abidjan": { c : ["CI", "BF", "GH", "GM", "GN", "ML", "MR", "SH", "SL", "SN", "TG"] },
			"Africa/Accra": { c : ["GH"] },
			"Africa/Addis_Ababa": { c : ["ET"] },
			"Africa/Algiers": { c : ["DZ"] },
			"Africa/Asmara": { c : ["ER"] },
			"Africa/Asmera": { c : ["ER"] },
			"Africa/Bamako": { c : ["ML"] },
			"Africa/Bangui": { c : ["CF"] },
			"Africa/Banjul": { c : ["GM"] },
			"Africa/Bissau": { c : ["GW"] },
			"Africa/Blantyre": { c : ["MW"] },
			"Africa/Brazzaville": { c : ["CG"] },
			"Africa/Bujumbura": { c : ["BI"] },
			"Africa/Cairo": { c : ["EG"] },
			"Africa/Casablanca": { c : ["MA"] },
			"Africa/Ceuta": { c : ["ES"] },
			"Africa/Conakry": { c : ["GN"] },
			"Africa/Dakar": { c : ["SN"] },
			"Africa/Dar_es_Salaam": { c : ["TZ"] },
			"Africa/Djibouti": { c : ["DJ"] },
			"Africa/Douala": { c : ["CM"] },
			"Africa/El_Aaiun": { c : ["EH"] },
			"Africa/Freetown": { c : ["SL"] },
			"Africa/Gaborone": { c : ["BW"] },
			"Africa/Harare": { c : ["ZW"] },
			"Africa/Johannesburg": { c : ["ZA", "LS", "SZ"] },
			"Africa/Juba": { c : ["SS"] },
			"Africa/Kampala": { c : ["UG"] },
			"Africa/Khartoum": { c : ["SD"] },
			"Africa/Kigali": { c : ["RW"] },
			"Africa/Kinshasa": { c : ["CD"] },
			"Africa/Lagos": { c : ["NG", "AO", "BJ", "CD", "CF", "CG", "CM", "GA", "GQ", "NE"] },
			"Africa/Libreville": { c : ["GA"] },
			"Africa/Lome": { c : ["TG"] },
			"Africa/Luanda": { c : ["AO"] },
			"Africa/Lubumbashi": { c : ["CD"] },
			"Africa/Lusaka": { c : ["ZM"] },
			"Africa/Malabo": { c : ["GQ"] },
			"Africa/Maputo": { c : ["MZ", "BI", "BW", "CD", "MW", "RW", "ZM", "ZW"] },
			"Africa/Maseru": { c : ["LS"] },
			"Africa/Mbabane": { c : ["SZ"] },
			"Africa/Mogadishu": { c : ["SO"] },
			"Africa/Monrovia": { c : ["LR"] },
			"Africa/Nairobi": { c : ["KE", "DJ", "ER", "ET", "KM", "MG", "SO", "TZ", "UG", "YT"] },
			"Africa/Ndjamena": { c : ["TD"] },
			"Africa/Niamey": { c : ["NE"] },
			"Africa/Nouakchott": { c : ["MR"] },
			"Africa/Ouagadougou": { c : ["BF"] },
			"Africa/Porto-Novo": { c : ["BJ"] },
			"Africa/Sao_Tome": { c : ["ST"] },
			"Africa/Timbuktu": { c : ["ML"] },
			"Africa/Tripoli": { c : ["LY"] },
			"Africa/Tunis": { c : ["TN"] },
			"Africa/Windhoek": { c : ["NA"] },
			"America/Adak": { c : ["US"] },
			"America/Anchorage": { c : ["US"] },
			"America/Anguilla": { c : ["AI"] },
			"America/Antigua": { c : ["AG"] },
			"America/Araguaina": { c : ["BR"] },
			"America/Argentina/Buenos_Aires": { c : ["AR"] },
			"America/Argentina/Catamarca": { c : ["AR"] },
			"America/Argentina/ComodRivadavia": { c : ["AR"] },
			"America/Argentina/Cordoba": { c : ["AR"] },
			"America/Argentina/Jujuy": { c : ["AR"] },
			"America/Argentina/La_Rioja": { c : ["AR"] },
			"America/Argentina/Mendoza": { c : ["AR"] },
			"America/Argentina/Rio_Gallegos": { c : ["AR"] },
			"America/Argentina/Salta": { c : ["AR"] },
			"America/Argentina/San_Juan": { c : ["AR"] },
			"America/Argentina/San_Luis": { c : ["AR"] },
			"America/Argentina/Tucuman": { c : ["AR"] },
			"America/Argentina/Ushuaia": { c : ["AR"] },
			"America/Aruba": { c : ["AW"] },
			"America/Asuncion": { c : ["PY"] },
			"America/Atikokan": { c : ["CA"] },
			"America/Atka": {},
			"America/Bahia": { c : ["BR"] },
			"America/Bahia_Banderas": { c : ["MX"] },
			"America/Barbados": { c : ["BB"] },
			"America/Belem": { c : ["BR"] },
			"America/Belize": { c : ["BZ"] },
			"America/Blanc-Sablon": { c : ["CA"] },
			"America/Boa_Vista": { c : ["BR"] },
			"America/Bogota": { c : ["CO"] },
			"America/Boise": { c : ["US"] },
			"America/Buenos_Aires": { c : ["AR"] },
			"America/Cambridge_Bay": { c : ["CA"] },
			"America/Campo_Grande": { c : ["BR"] },
			"America/Cancun": { c : ["MX"] },
			"America/Caracas": { c : ["VE"] },
			"America/Catamarca": {},
			"America/Cayenne": { c : ["GF"] },
			"America/Cayman": { c : ["KY"] },
			"America/Chicago": { c : ["US"] },
			"America/Chihuahua": { c : ["MX"] },
			"America/Coral_Harbour": { c : ["CA"] },
			"America/Cordoba": { c: ['AR'] },
			"America/Costa_Rica": { c : ["CR"] },
			"America/Creston": { c : ["CA"] },
			"America/Cuiaba": { c : ["BR"] },
			"America/Curacao": { c : ["CW"] },
			"America/Danmarkshavn": { c : ["GL"] },
			"America/Dawson": { c : ["CA"] },
			"America/Dawson_Creek": { c : ["CA"] },
			"America/Denver": { c : ["US"] },
			"America/Detroit": { c : ["US"] },
			"America/Dominica": { c : ["DM"] },
			"America/Edmonton": { c : ["CA"] },
			"America/Eirunepe": { c : ["BR"] },
			"America/El_Salvador": { c : ["SV"] },
			"America/Ensenada": {},
			"America/Fort_Nelson": { c : ["CA"] },
			"America/Fort_Wayne": {},
			"America/Fortaleza": { c : ["BR"] },
			"America/Glace_Bay": { c : ["CA"] },
			"America/Godthab": {},
			"America/Goose_Bay": { c : ["CA"] },
			"America/Grand_Turk": { c : ["TC"] },
			"America/Grenada": { c : ["GD"] },
			"America/Guadeloupe": { c : ["GP"] },
			"America/Guatemala": { c : ["GT"] },
			"America/Guayaquil": { c : ["EC"] },
			"America/Guyana": { c : ["GY"] },
			"America/Halifax": { c : ["CA"] },
			"America/Havana": { c : ["CU"] },
			"America/Hermosillo": { c : ["MX"] },
			"America/Indiana/Indianapolis": { c : ["US"] },
			"America/Indiana/Knox": { c : ["US"] },
			"America/Indiana/Marengo": { c : ["US"] },
			"America/Indiana/Petersburg": { c : ["US"] },
			"America/Indiana/Tell_City": { c : ["US"] },
			"America/Indiana/Vevay": { c : ["US"] },
			"America/Indiana/Vincennes": { c : ["US"] },
			"America/Indiana/Winamac": { c : ["US"] },
			"America/Indianapolis": {},
			"America/Inuvik": { c : ["CA"] },
			"America/Iqaluit": { c : ["CA"] },
			"America/Jamaica": { c : ["JM"] },
			"America/Jujuy": {},
			"America/Juneau": { c : ["US"] },
			"America/Kentucky/Louisville": { c : ["US"] },
			"America/Kentucky/Monticello": { c : ["US"] },
			"America/Knox_IN": {},
			"America/Kralendijk": { c : ["BQ"] },
			"America/La_Paz": { c : ["BO"] },
			"America/Lima": { c : ["PE"] },
			"America/Los_Angeles": { c : ["US"] },
			"America/Louisville": {},
			"America/Lower_Princes": { c : ["SX"] },
			"America/Maceio": { c : ["BR"] },
			"America/Managua": { c : ["NI"] },
			"America/Manaus": { c : ["BR"] },
			"America/Marigot": { c : ["MF"] },
			"America/Martinique": { c : ["MQ"] },
			"America/Matamoros": { c : ["MX"] },
			"America/Mazatlan": { c : ["MX"] },
			"America/Mendoza": {},
			"America/Menominee": { c : ["US"] },
			"America/Merida": { c : ["MX"] },
			"America/Metlakatla": { c : ["US"] },
			"America/Mexico_City": { c : ["MX"] },
			"America/Miquelon": { c : ["PM"] },
			"America/Moncton": { c : ["CA"] },
			"America/Monterrey": { c : ["MX"] },
			"America/Montevideo": { c : ["UY"] },
			"America/Montreal": { c : ["CA"] },
			"America/Montserrat": { c : ["MS"] },
			"America/Nassau": { c : ["BS"] },
			"America/New_York": { c : ["US"] },
			"America/Nipigon": { c : ["CA"] },
			"America/Nome": { c : ["US"] },
			"America/Noronha": { c : ["BR"] },
			"America/North_Dakota/Beulah": { c : ["US"] },
			"America/North_Dakota/Center": { c : ["US"] },
			"America/North_Dakota/New_Salem": { c : ["US"] },
			"America/Nuuk": { c : ["GL"] },
			"America/Ojinaga": { c : ["MX"] },
			"America/Panama": { c : ["PA", "CA", "KY"] },
			"America/Pangnirtung": { c : ["CA"] },
			"America/Paramaribo": { c : ["SR"] },
			"America/Phoenix": { c : ["US", "CA"] },
			"America/Port-au-Prince": { c : ["HT"] },
			"America/Port_of_Spain": { c : ["TT"] },
			"America/Porto_Acre": {},
			"America/Porto_Velho": { c : ["BR"] },
			"America/Puerto_Rico": { c : ["PR", "AG", "CA", "AI","AW","BL","BQ","CW","DM","GD","GP","KN","LC","MF","MS","SX","TT","VC","VG","VI"] },
			"America/Punta_Arenas": { c : ["CL"] },
			"America/Rainy_River": { c : ["CA"] },
			"America/Rankin_Inlet": { c : ["CA"] },
			"America/Recife": { c : ["BR"] },
			"America/Regina": { c : ["CA"] },
			"America/Resolute": { c : ["CA"] },
			"America/Rio_Branco": { c : ["BR"] },
			"America/Rosario": {},
			"America/Santa_Isabel": {},
			"America/Santarem": { c : ["BR"] },
			"America/Santiago": { c : ["CL"] },
			"America/Santo_Domingo": { c : ["DO"] },
			"America/Sao_Paulo": { c : ["BR"] },
			"America/Scoresbysund": { c : ["GL"] },
			"America/Shiprock": {},
			"America/Sitka": { c : ["US"] },
			"America/St_Barthelemy": { c : ["BL"] },
			"America/St_Johns": { c : ["CA"] },
			"America/St_Kitts": { c : ["KN"] },
			"America/St_Lucia": { c : ["LC"] },
			"America/St_Thomas": { c : ["VI"] },
			"America/St_Vincent": { c : ["VC"] },
			"America/Swift_Current": { c : ["CA"] },
			"America/Tegucigalpa": { c : ["HN"] },
			"America/Thule": { c : ["GL"] },
			"America/Thunder_Bay": { c : ["CA"] },
			"America/Tijuana": { c : ["MX"] },
			"America/Toronto": { c : ["CA", "BS"] },
			"America/Tortola": { c : ["VG"] },
			"America/Vancouver": { c : ["CA"] },
			"America/Virgin": { c : ["VI"] },
			"America/Whitehorse": { c : ["CA"] },
			"America/Winnipeg": { c : ["CA"] },
			"America/Yakutat": { c : ["US"] },
			"America/Yellowknife": { c : ["CA"] },
			"Antarctica/Casey": { c : ["AQ"] },
			"Antarctica/Davis": { c : ["AQ"] },
			"Antarctica/DumontDUrville": { c : ["AQ"] },
			"Antarctica/Macquarie": { c : ["AU"] },
			"Antarctica/Mawson": { c : ["AQ"] },
			"Antarctica/McMurdo": { c : ["AQ"] },
			"Antarctica/Palmer": { c : ["AQ"] },
			"Antarctica/Rothera": { c : ["AQ"] },
			"Antarctica/South_Pole": { c : ["AQ"] },
			"Antarctica/Syowa": { c : ["AQ"] },
			"Antarctica/Troll": { c : ["AQ"] },
			"Antarctica/Vostok": { c : ["AQ"] },
			"Arctic/Longyearbyen": { c : ["SJ"] },
			"Asia/Aden": { c : ["YE"] },
			"Asia/Almaty": { c : ["KZ"] },
			"Asia/Amman": { c : ["JO"] },
			"Asia/Anadyr": { c : ["RU"] },
			"Asia/Aqtau": { c : ["KZ"] },
			"Asia/Aqtobe": { c : ["KZ"] },
			"Asia/Ashgabat": { c : ["TM"] },
			"Asia/Ashkhabad": {},
			"Asia/Atyrau": { c : ["KZ"] },
			"Asia/Baghdad": { c : ["IQ"] },
			"Asia/Bahrain": { c : ["BH"] },
			"Asia/Baku": { c : ["AZ"] },
			"Asia/Bangkok": { c : ["TH", "KH", "LA", "VN"] },
			"Asia/Barnaul": { c : ["RU"] },
			"Asia/Beirut": { c : ["LB"] },
			"Asia/Bishkek": { c : ["KG"] },
			"Asia/Brunei": { c : ["BN"] },
			"Asia/Calcutta": {},
			"Asia/Chita": { c : ["RU"] },
			"Asia/Choibalsan": { c : ["MN"] },
			"Asia/Chongqing": {},
			"Asia/Chungking": {},
			"Asia/Colombo": { c : ["LK"] },
			"Asia/Dacca": {},
			"Asia/Damascus": { c : ["SY"] },
			"Asia/Dhaka": { c : ["BD"] },
			"Asia/Dili": { c : ["TL"] },
			"Asia/Dubai": { c : ["AE", "OM"] },
			"Asia/Dushanbe": { c : ["TJ"] },
			"Asia/Famagusta": { c : ["CY"] },
			"Asia/Gaza": { c : ["PS"] },
			"Asia/Harbin": {},
			"Asia/Hebron": { c : ["PS"] },
			"Asia/Ho_Chi_Minh": { c : ["VN"] },
			"Asia/Hong_Kong": { c : ["HK"] },
			"Asia/Hovd": { c : ["MN"] },
			"Asia/Irkutsk": { c : ["RU"] },
			"Asia/Istanbul": {},
			"Asia/Jakarta": { c : ["ID"] },
			"Asia/Jayapura": { c : ["ID"] },
			"Asia/Jerusalem": { c : ["IL"] },
			"Asia/Kabul": { c : ["AF"] },
			"Asia/Kamchatka": { c : ["RU"] },
			"Asia/Karachi": { c : ["PK"] },
			"Asia/Kashgar": {},
			"Asia/Kathmandu": { c : ["NP"] },
			"Asia/Katmandu": {},
			"Asia/Khandyga": { c : ["RU"] },
			"Asia/Kolkata": { c : ["IN"] },
			"Asia/Krasnoyarsk": { c : ["RU"] },
			"Asia/Kuala_Lumpur": { c : ["MY"] },
			"Asia/Kuching": { c : ["MY"] },
			"Asia/Kuwait": { c : ["KW"] },
			"Asia/Macao": {},
			"Asia/Macau": { c : ["MO"] },
			"Asia/Magadan": { c : ["RU"] },
			"Asia/Makassar": { c : ["ID"] },
			"Asia/Manila": { c : ["PH"] },
			"Asia/Muscat": { c : ["OM"] },
			"Asia/Nicosia": { c : ["CY"] },
			"Asia/Novokuznetsk": { c : ["RU"] },
			"Asia/Novosibirsk": { c : ["RU"] },
			"Asia/Omsk": { c : ["RU"] },
			"Asia/Oral": { c : ["KZ"] },
			"Asia/Phnom_Penh": { c : ["KH"] },
			"Asia/Pontianak": { c : ["ID"] },
			"Asia/Pyongyang": { c : ["KP"] },
			"Asia/Qatar": { c : ["QA", "BH"] },
			"Asia/Qostanay": { c : ["KZ"] },
			"Asia/Qyzylorda": { c : ["KZ"] },
			"Asia/Rangoon": {},
			"Asia/Riyadh": { c : ["SA", "AQ", "KW", "YE"] },
			"Asia/Saigon": {},
			"Asia/Sakhalin": { c : ["RU"] },
			"Asia/Samarkand": { c : ["UZ"] },
			"Asia/Seoul": { c : ["KR"] },
			"Asia/Shanghai": { c : ["CN"] },
			"Asia/Singapore": { c : ["SG", "MY"] },
			"Asia/Srednekolymsk": { c : ["RU"] },
			"Asia/Taipei": { c : ["TW"] },
			"Asia/Tashkent": { c : ["UZ"] },
			"Asia/Tbilisi": { c : ["GE"] },
			"Asia/Tehran": { c : ["IR"] },
			"Asia/Tel_Aviv": {},
			"Asia/Thimbu": {},
			"Asia/Thimphu": { c : ["BT"] },
			"Asia/Tokyo": { c : ["JP"] },
			"Asia/Tomsk": { c : ["RU"] },
			"Asia/Ujung_Pandang": {},
			"Asia/Ulaanbaatar": { c : ["MN"] },
			"Asia/Ulan_Bator": {},
			"Asia/Urumqi": { c : ["CN"] },
			"Asia/Ust-Nera": { c : ["RU"] },
			"Asia/Vientiane": { c : ["LA"] },
			"Asia/Vladivostok": { c : ["RU"] },
			"Asia/Yakutsk": { c : ["RU"] },
			"Asia/Yangon": { c : ["MM"] },
			"Asia/Yekaterinburg": { c : ["RU"] },
			"Asia/Yerevan": { c : ["AM"] },
			"Atlantic/Azores": { c : ["PT"] },
			"Atlantic/Bermuda": { c : ["BM"] },
			"Atlantic/Canary": { c : ["ES"] },
			"Atlantic/Cape_Verde": { c : ["CV"] },
			"Atlantic/Faeroe": {},
			"Atlantic/Faroe": { c : ["FO"] },
			"Atlantic/Jan_Mayen": { c : ["SJ"] },
			"Atlantic/Madeira": { c : ["PT"] },
			"Atlantic/Reykjavik": { c : ["IS"] },
			"Atlantic/South_Georgia": { c : ["GS"] },
			"Atlantic/St_Helena": { c : ["SH"] },
			"Atlantic/Stanley": { c : ["FK"] },
			"Australia/ACT": {},
			"Australia/Adelaide": { c : ["AU"] },
			"Australia/Brisbane": { c : ["AU"] },
			"Australia/Broken_Hill": { c : ["AU"] },
			"Australia/Canberra": {},
			"Australia/Currie": {},
			"Australia/Darwin": { c : ["AU"] },
			"Australia/Eucla": { c : ["AU"] },
			"Australia/Hobart": { c : ["AU"] },
			"Australia/LHI": {},
			"Australia/Lindeman": { c : ["AU"] },
			"Australia/Lord_Howe": { c : ["AU"] },
			"Australia/Melbourne": { c : ["AU"] },
			"Australia/NSW": {},
			"Australia/North": {},
			"Australia/Perth": { c : ["AU"] },
			"Australia/Queensland": {},
			"Australia/South": {},
			"Australia/Sydney": { c : ["AU"] },
			"Australia/Tasmania": {},
			"Australia/Victoria": {},
			"Australia/West": {},
			"Australia/Yancowinna": {},
			"Brazil/Acre": {},
			"Brazil/DeNoronha": {},
			"Brazil/East": {},
			"Brazil/West": {},
			CET: {},
			CST6CDT: {},
			"Canada/Atlantic": {},
			"Canada/Central": {},
			"Canada/Eastern": { c : ["CA"] },
			"Canada/Mountain": {},
			"Canada/Newfoundland": {},
			"Canada/Pacific": {},
			"Canada/Saskatchewan": {},
			"Canada/Yukon": {},
			"Chile/Continental": {},
			"Chile/EasterIsland": {},
			Cuba: {},
			EET: {},
			EST: {},
			EST5EDT: {},
			Egypt: {},
			Eire: {},
			"Etc/GMT": {},
			"Etc/GMT+0": {},
			"Etc/GMT+1": {},
			"Etc/GMT+10": {},
			"Etc/GMT+11": {},
			"Etc/GMT+12": {},
			"Etc/GMT+2": {},
			"Etc/GMT+3": {},
			"Etc/GMT+4": {},
			"Etc/GMT+5": {},
			"Etc/GMT+6": {},
			"Etc/GMT+7": {},
			"Etc/GMT+8": {},
			"Etc/GMT+9": {},
			"Etc/GMT-0": {},
			"Etc/GMT-1": {},
			"Etc/GMT-10": {},
			"Etc/GMT-11": {},
			"Etc/GMT-12": {},
			"Etc/GMT-13": {},
			"Etc/GMT-14": {},
			"Etc/GMT-2": {},
			"Etc/GMT-3": {},
			"Etc/GMT-4": {},
			"Etc/GMT-5": {},
			"Etc/GMT-6": {},
			"Etc/GMT-7": {},
			"Etc/GMT-8": {},
			"Etc/GMT-9": {},
			"Etc/GMT0": {},
			"Etc/Greenwich": {},
			"Etc/UCT": {},
			"Etc/UTC": {},
			"Etc/Universal": {},
			"Etc/Zulu": {},
			"Europe/Amsterdam": { c : ["NL"] },
			"Europe/Andorra": { c : ["AD"] },
			"Europe/Astrakhan": { c : ["RU"] },
			"Europe/Athens": { c : ["GR"] },
			"Europe/Belfast": { c : ["GB"] },
			"Europe/Belgrade": { c : ["RS", "BA", "HR", "ME", "MK", "SI"] },
			"Europe/Berlin": { c : ["DE"] },
			"Europe/Bratislava": { c : ["SK"] },
			"Europe/Brussels": { c : ["BE"] },
			"Europe/Bucharest": { c : ["RO"] },
			"Europe/Budapest": { c : ["HU"] },
			"Europe/Busingen": { c : ["DE"] },
			"Europe/Chisinau": { c : ["MD"] },
			"Europe/Copenhagen": { c : ["DK"] },
			"Europe/Dublin": { c : ["IE"] },
			"Europe/Gibraltar": { c : ["GI"] },
			"Europe/Guernsey": { c : ["GG"] },
			"Europe/Helsinki": { c : ["FI", "AX"] },
			"Europe/Isle_of_Man": { c : ["IM"] },
			"Europe/Istanbul": { c : ["TR"] },
			"Europe/Jersey": { c : ["JE"] },
			"Europe/Kaliningrad": { c : ["RU"] },
			"Europe/Kiev": { c : ["UA"] },
			"Europe/Kirov": { c : ["RU"] },
			"Europe/Lisbon": { c : ["PT"] },
			"Europe/Ljubljana": { c : ["SI"] },
			"Europe/London": { c : ["GB", "GG", "IM", "JE"] },
			"Europe/Luxembourg": { c : ["LU"] },
			"Europe/Madrid": { c : ["ES"] },
			"Europe/Malta": { c : ["MT"] },
			"Europe/Mariehamn": { c : ["AX"] },
			"Europe/Minsk": { c : ["BY"] },
			"Europe/Monaco": { c : ["MC"] },
			"Europe/Moscow": { c : ["RU"] },
			"Europe/Nicosia": {},
			"Europe/Oslo": { c : ["NO", "SJ", "BV"] },
			"Europe/Paris": { c : ["FR"] },
			"Europe/Podgorica": { c : ["ME"] },
			"Europe/Prague": { c : ["CZ", "SK"] },
			"Europe/Riga": { c : ["LV"] },
			"Europe/Rome": { c : ["IT", "SM", "VA"] },
			"Europe/Samara": { c : ["RU"] },
			"Europe/San_Marino": { c : ["SM"] },
			"Europe/Sarajevo": { c : ["BA"] },
			"Europe/Saratov": { c : ["RU"] },
			"Europe/Simferopol": { c : ["RU", "UA"] },
			"Europe/Skopje": { c : ["MK"] },
			"Europe/Sofia": { c : ["BG"] },
			"Europe/Stockholm": { c : ["SE"] },
			"Europe/Tallinn": { c : ["EE"] },
			"Europe/Tirane": { c : ["AL"] },
			"Europe/Tiraspol": {},
			"Europe/Ulyanovsk": { c : ["RU"] },
			"Europe/Uzhgorod": { c : ["UA"] },
			"Europe/Vaduz": { c : ["LI"] },
			"Europe/Vatican": { c : ["VA"] },
			"Europe/Vienna": { c : ["AT"] },
			"Europe/Vilnius": { c : ["LT"] },
			"Europe/Volgograd": { c : ["RU"] },
			"Europe/Warsaw": { c : ["PL"] },
			"Europe/Zagreb": { c : ["HR"] },
			"Europe/Zaporozhye": { c : ["UA"] },
			"Europe/Zurich": { c : ["CH", "DE", "LI"] },
			Factory: {},
			GB: { c : ["GB"] },
			"GB-Eire": { c : ["GB"] },
			GMT: {},
			"GMT+0": {},
			"GMT-0": {},
			GMT0: {},
			Greenwich: {},
			HST: {},
			Hongkong: {},
			Iceland: {},
			"Indian/Antananarivo": { c : ["MG"] },
			"Indian/Chagos": { c : ["IO"] },
			"Indian/Christmas": { c : ["CX"] },
			"Indian/Cocos": { c : ["CC"] },
			"Indian/Comoro": { c : ["KM"] },
			"Indian/Kerguelen": { c : ["TF", "HM"] },
			"Indian/Mahe": { c : ["SC"] },
			"Indian/Maldives": { c : ["MV"] },
			"Indian/Mauritius": { c : ["MU"] },
			"Indian/Mayotte": { c : ["YT"] },
			"Indian/Reunion": { c : ["RE", "TF"] },
			Iran: {},
			Israel: {},
			Jamaica: {},
			Japan: {},
			Kwajalein: {},
			Libya: {},
			MET: {},
			MST: {},
			MST7MDT: {},
			"Mexico/BajaNorte": {},
			"Mexico/BajaSur": {},
			"Mexico/General": {},
			NZ: { c : ["NZ"] },
			"NZ-CHAT": {},
			Navajo: {},
			PRC: {},
			PST8PDT: {},
			"Pacific/Apia": { c : ["WS"] },
			"Pacific/Auckland": { c : ["NZ", "AQ"] },
			"Pacific/Bougainville": { c : ["PG"] },
			"Pacific/Chatham": { c : ["NZ"] },
			"Pacific/Chuuk": { c : ["FM"] },
			"Pacific/Easter": { c : ["CL"] },
			"Pacific/Efate": { c : ["VU"] },
			"Pacific/Enderbury": {},
			"Pacific/Fakaofo": { c : ["TK"] },
			"Pacific/Fiji": { c : ["FJ"] },
			"Pacific/Funafuti": { c : ["TV"] },
			"Pacific/Galapagos": { c : ["EC"] },
			"Pacific/Gambier": { c : ["PF"] },
			"Pacific/Guadalcanal": { c : ["SB"] },
			"Pacific/Guam": { c : ["GU", "MP"] },
			"Pacific/Honolulu": { c : ["US", "UM"] },
			"Pacific/Johnston": { c : ["UM"] },
			"Pacific/Kanton": { c : ["KI"] },
			"Pacific/Kiritimati": { c : ["KI"] },
			"Pacific/Kosrae": { c : ["FM"] },
			"Pacific/Kwajalein": { c : ["MH"] },
			"Pacific/Majuro": { c : ["MH"] },
			"Pacific/Marquesas": { c : ["PF"] },
			"Pacific/Midway": { c : ["UM"] },
			"Pacific/Nauru": { c : ["NR"] },
			"Pacific/Niue": { c : ["NU"] },
			"Pacific/Norfolk": { c : ["NF"] },
			"Pacific/Noumea": { c : ["NC"] },
			"Pacific/Pago_Pago": { c : ["AS", "UM"] },
			"Pacific/Palau": { c : ["PW"] },
			"Pacific/Pitcairn": { c : ["PN"] },
			"Pacific/Pohnpei": { c : ["FM"] },
			"Pacific/Ponape": {},
			"Pacific/Port_Moresby": { c : ["PG", "AQ"] },
			"Pacific/Rarotonga": { c : ["CK"] },
			"Pacific/Saipan": { c : ["MP"] },
			"Pacific/Samoa": { c : ["WS"] },
			"Pacific/Tahiti": { c : ["PF"] },
			"Pacific/Tarawa": { c : ["KI"] },
			"Pacific/Tongatapu": { c : ["TO"] },
			"Pacific/Truk": {},
			"Pacific/Wake": { c : ["UM"] },
			"Pacific/Wallis": { c : ["WF"] },
			"Pacific/Yap": {},
			Poland: {},
			Portugal: {},
			ROC: {},
			ROK: {},
			Singapore: { c : ["SG"] },
			Turkey: {},
			UCT: {},
			"US/Alaska": {},
			"US/Aleutian": {},
			"US/Arizona": { c : ["US"] },
			"US/Central": {},
			"US/East-Indiana": {},
			"US/Eastern": {},
			"US/Hawaii": { c : ["US"] },
			"US/Indiana-Starke": {},
			"US/Michigan": {},
			"US/Mountain": {},
			"US/Pacific": {},
			"US/Samoa": { c : ["WS"] },
			UTC: {},
			Universal: {},
			"W-SU": {},
			WET: {},
			Zulu: {
			}
		};

		const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

		if (timezone === "" || !timezone) {
			return null;
		}

		return timezones[timezone].c[0];
	}

	document.querySelectorAll('form.em-booking-form').forEach( function( booking_form ){
		booking_form.querySelectorAll('[type="tel"]').forEach( function(input){
			// change name and allow a hidden field for submission
			let alt = document.createElement('input');
			let name = input.name;
			if( name ) {
				input.name = name + '_intl';
				alt.name = name;
			}
			input.classList.add('em-intl-tel');
			// copy all classes and remove ones we know we don't want
			alt.setAttribute('class', input.getAttribute('class') + ' em-intl-tel-full');
			alt.classList.remove('em-intl-tel');
			alt.type = 'hidden';
			if( input.id ) {
				alt.id = input.id + '-full'
			}
			alt.value = input.value;
			// add data-name to the full input if it exists, for use in dynamic input forms for JS submission within forms
			if( input.getAttribute('data-name') ) {
				alt.setAttribute('data-name', input.getAttribute('data-name'));
				input.removeAttribute('data-name');
			}
			input.after(alt);

			let options = Object.assign({
				utilsScript: EM.url + '/includes/external/intl-tel-input/js/utils.js',
				separateDialCode : true,
			}, EM.phone.options);

			if( EM.phone.detectJS ) {
				let country = getCountry();
				if( country ) {
					options.initialCountry = country;
				} else if ( EM.phone.initialCountry ) {
					options.initialCountry = EM.phone.initialCountry;
				}
			} else if ( EM.phone.initialCountry ) {
				options.initialCountry = EM.phone.initialCountry;
			}

			let iti = window.intlTelInput( input, options);
			input.addEventListener('change', function( e ){
				alt.value = iti.getNumber();
				if ( input.value.trim() ) {
					let wrapper = input.closest('.iti')
					if ( iti.isPossibleNumber() ) {
						wrapper.classList.remove("invalid-number");
						if ( wrapper.nextElementSibling && wrapper.nextElementSibling.classList.contains('em-inline-error') ) {
							wrapper.nextElementSibling.remove();
						}
					} else {
						wrapper.classList.add("invalid-number");
						const errorCode = iti.getValidationError();
						let errorMsg;
						if( !(wrapper.nextElementSibling && wrapper.nextElementSibling.classList.contains('em-inline-error')) ) {
							// create a div em-form-error class name and append after input
							errorMsg = document.createElement('div');
							errorMsg.classList.add('em-inline-error');
							wrapper.after(errorMsg);
						} else {
							errorMsg = wrapper.nextElementSibling;
						}
						errorMsg.innerHTML = '<span class="em-icon"></span> ' + EM.phone.error;
						errorMsg.classList.remove("hide");
					}
				}
			});
			// create a hidden input and populate it on change
			input.addEventListener('countrychange', function( e ){
				alt.value = iti.getNumber();
				// check input padding inline style and set it to important
				if( input.getAttribute('style') ) {
					input.setAttribute('style', input.getAttribute('style').replace('px', 'px !important'));
				}
			});
		});
	});

});

/*!
 * jquery-timepicker v1.13.16 - Copyright (c) 2020 Jon Thornton - https://www.jonthornton.com/jquery-timepicker/
 * Did a search/replace of timepicker to em_timepicker to prevent conflicts.
 */
(function(){"use strict";function _typeof(obj){"@babel/helpers - typeof";if(typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"){_typeof=function(obj){return typeof obj}}else{_typeof=function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj}}return _typeof(obj)}function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor)){throw new TypeError("Cannot call a class as a function")}}function _defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||false;descriptor.configurable=true;if("value"in descriptor)descriptor.writable=true;Object.defineProperty(target,descriptor.key,descriptor)}}function _createClass(Constructor,protoProps,staticProps){if(protoProps)_defineProperties(Constructor.prototype,protoProps);if(staticProps)_defineProperties(Constructor,staticProps);return Constructor}function _defineProperty(obj,key,value){if(key in obj){Object.defineProperty(obj,key,{value:value,enumerable:true,configurable:true,writable:true})}else{obj[key]=value}return obj}function ownKeys(object,enumerableOnly){var keys=Object.keys(object);if(Object.getOwnPropertySymbols){var symbols=Object.getOwnPropertySymbols(object);if(enumerableOnly)symbols=symbols.filter(function(sym){return Object.getOwnPropertyDescriptor(object,sym).enumerable});keys.push.apply(keys,symbols)}return keys}function _objectSpread2(target){for(var i=1;i<arguments.length;i++){var source=arguments[i]!=null?arguments[i]:{};if(i%2){ownKeys(Object(source),true).forEach(function(key){_defineProperty(target,key,source[key])})}else if(Object.getOwnPropertyDescriptors){Object.defineProperties(target,Object.getOwnPropertyDescriptors(source))}else{ownKeys(Object(source)).forEach(function(key){Object.defineProperty(target,key,Object.getOwnPropertyDescriptor(source,key))})}}return target}function _unsupportedIterableToArray(o,minLen){if(!o)return;if(typeof o==="string")return _arrayLikeToArray(o,minLen);var n=Object.prototype.toString.call(o).slice(8,-1);if(n==="Object"&&o.constructor)n=o.constructor.name;if(n==="Map"||n==="Set")return Array.from(n);if(n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return _arrayLikeToArray(o,minLen)}function _arrayLikeToArray(arr,len){if(len==null||len>arr.length)len=arr.length;for(var i=0,arr2=new Array(len);i<len;i++)arr2[i]=arr[i];return arr2}function _createForOfIteratorHelper(o){if(typeof Symbol==="undefined"||o[Symbol.iterator]==null){if(Array.isArray(o)||(o=_unsupportedIterableToArray(o))){var i=0;var F=function(){};return{s:F,n:function(){if(i>=o.length)return{done:true};return{done:false,value:o[i++]}},e:function(e){throw e},f:F}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var it,normalCompletion=true,didErr=false,err;return{s:function(){it=o[Symbol.iterator]()},n:function(){var step=it.next();normalCompletion=step.done;return step},e:function(e){didErr=true;err=e},f:function(){try{if(!normalCompletion&&it.return!=null)it.return()}finally{if(didErr)throw err}}}}var ONE_DAY=86400;var roundingFunction=function roundingFunction(seconds,settings){if(seconds===null){return null}else if(typeof settings.step!=="number"){return seconds}else{var offset=seconds%(settings.step*60);var start=settings.minTime||0;offset-=start%(settings.step*60);if(offset>=settings.step*30){seconds+=settings.step*60-offset}else{seconds-=offset}return _moduloSeconds(seconds,settings)}};function _moduloSeconds(seconds,settings){if(seconds==ONE_DAY&&settings.show2400){return seconds}return seconds%ONE_DAY}var DEFAULT_SETTINGS={appendTo:"body",className:null,closeOnWindowScroll:false,disableTextInput:false,disableTimeRanges:[],disableTouchKeyboard:false,durationTime:null,forceRoundTime:false,lang:{},listWidth:null,maxTime:null,minTime:null,noneOption:false,orientation:"l",roundingFunction:roundingFunction,scrollDefault:null,selectOnBlur:false,show2400:false,showDuration:false,showOn:["click","focus"],showOnFocus:true,step:30,stopScrollPropagation:false,timeFormat:"g:ia",typeaheadHighlight:true,useSelect:false,wrapHours:true};var DEFAULT_LANG={am:"am",pm:"pm",AM:"AM",PM:"PM",decimal:".",mins:"mins",hr:"hr",hrs:"hrs"};var Timepicker=function(){function Timepicker(targetEl){var options=arguments.length>1&&arguments[1]!==undefined?arguments[1]:{};_classCallCheck(this,Timepicker);this._handleFormatValue=this._handleFormatValue.bind(this);this._handleKeyUp=this._handleKeyUp.bind(this);this.targetEl=targetEl;var attrOptions=Timepicker.extractAttrOptions(targetEl,Object.keys(DEFAULT_SETTINGS));this.settings=this.parseSettings(_objectSpread2(_objectSpread2(_objectSpread2({},DEFAULT_SETTINGS),options),attrOptions))}_createClass(Timepicker,[{key:"hideMe",value:function hideMe(){if(this.settings.useSelect){this.targetEl.blur();return}if(!this.list||!Timepicker.isVisible(this.list)){return}if(this.settings.selectOnBlur){this._selectValue()}this.list.hide();var hideTimepickerEvent=new CustomEvent("hideTimepicker");this.targetEl.dispatchEvent(hideTimepickerEvent)}},{key:"_findRow",value:function _findRow(value){if(!value&&value!==0){return false}var out=false;var value=this.settings.roundingFunction(value,this.settings);if(!this.list){return false}this.list.find("li").each(function(i,obj){var parsed=Number.parseInt(obj.dataset.time);if(Number.isNaN(parsed)){return}if(parsed==value){out=obj;return false}});return out}},{key:"_hideKeyboard",value:function _hideKeyboard(){return(window.navigator.msMaxTouchPoints||"ontouchstart"in document)&&this.settings.disableTouchKeyboard}},{key:"_setTimeValue",value:function _setTimeValue(value,source){if(this.targetEl.nodeName==="INPUT"){if(value!==null||this.targetEl.value!=""){this.targetEl.value=value}var tp=this;var settings=tp.settings;if(settings.useSelect&&source!="select"&&tp.list){tp.list.val(tp._roundAndFormatTime(tp.time2int(value)))}}var selectTimeEvent=new Event("selectTime");if(this.selectedValue!=value){this.selectedValue=value;var changeTimeEvent=new Event("changeTime");var changeEvent=new CustomEvent("change",{detail:"em_timepicker"});if(source=="select"){this.targetEl.dispatchEvent(selectTimeEvent);this.targetEl.dispatchEvent(changeTimeEvent);this.targetEl.dispatchEvent(changeEvent)}else if(["error","initial"].indexOf(source)==-1){this.targetEl.dispatchEvent(changeTimeEvent)}return true}else{if(["error","initial"].indexOf(source)==-1){this.targetEl.dispatchEvent(selectTimeEvent)}return false}}},{key:"_getTimeValue",value:function _getTimeValue(){if(this.targetEl.nodeName==="INPUT"){return this.targetEl.value}else{return this.selectedValue}}},{key:"_selectValue",value:function _selectValue(){var tp=this;var settings=tp.settings;var list=tp.list;var cursor=list.find(".ui-em_timepicker-selected");if(cursor.hasClass("ui-em_timepicker-disabled")){return false}if(!cursor.length){return true}var timeValue=cursor.get(0).dataset.time;if(timeValue){var parsedTimeValue=Number.parseInt(timeValue);if(!Number.isNaN(parsedTimeValue)){timeValue=parsedTimeValue}}if(timeValue!==null){if(typeof timeValue!="string"){timeValue=tp._int2time(timeValue)}tp._setTimeValue(timeValue,"select")}return true}},{key:"time2int",value:function time2int(timeString){if(timeString===""||timeString===null||timeString===undefined)return null;if(timeString instanceof Date){return timeString.getHours()*3600+timeString.getMinutes()*60+timeString.getSeconds()}if(typeof timeString!="string"){return timeString}timeString=timeString.toLowerCase().replace(/[\s\.]/g,"");if(timeString.slice(-1)=="a"||timeString.slice(-1)=="p"){timeString+="m"}var pattern=/^(([^0-9]*))?([0-9]?[0-9])(([0-5][0-9]))?(([0-5][0-9]))?(([^0-9]*))$/;var hasDelimetersMatch=timeString.match(/\W/);if(hasDelimetersMatch){pattern=/^(([^0-9]*))?([0-9]?[0-9])(\W+([0-5][0-9]?))?(\W+([0-5][0-9]))?(([^0-9]*))$/}var time=timeString.match(pattern);if(!time){return null}var hour=parseInt(time[3]*1,10);var ampm=time[2]||time[9];var hours=hour;var minutes=time[5]*1||0;var seconds=time[7]*1||0;if(!ampm&&time[3].length==2&&time[3][0]=="0"){ampm="am"}if(hour<=12&&ampm){ampm=ampm.trim();var isPm=ampm==this.settings.lang.pm||ampm==this.settings.lang.PM;if(hour==12){hours=isPm?12:0}else{hours=hour+(isPm?12:0)}}else{var t=hour*3600+minutes*60+seconds;if(t>=ONE_DAY+(this.settings.show2400?1:0)){if(this.settings.wrapHours===false){return null}hours=hour%24}}var timeInt=hours*3600+minutes*60+seconds;if(hour<12&&!ampm&&this.settings._twelveHourTime&&this.settings.scrollDefault){var delta=timeInt-this.settings.scrollDefault();if(delta<0&&delta>=ONE_DAY/-2){timeInt=(timeInt+ONE_DAY/2)%ONE_DAY}}return timeInt}},{key:"parseSettings",value:function parseSettings(settings){var _this=this;settings.lang=_objectSpread2(_objectSpread2({},DEFAULT_LANG),settings.lang);this.settings=settings;if(settings.minTime){settings.minTime=this.time2int(settings.minTime)}if(settings.maxTime){settings.maxTime=this.time2int(settings.maxTime)}if(settings.listWidth){settings.listWidth=this.time2int(settings.listWidth)}if(settings.durationTime&&typeof settings.durationTime!=="function"){settings.durationTime=this.time2int(settings.durationTime)}if(settings.scrollDefault=="now"){settings.scrollDefault=function(){return settings.roundingFunction(_this.time2int(new Date),settings)}}else if(settings.scrollDefault&&typeof settings.scrollDefault!="function"){var val=settings.scrollDefault;settings.scrollDefault=function(){return settings.roundingFunction(_this.time2int(val),settings)}}else if(settings.minTime){settings.scrollDefault=function(){return settings.roundingFunction(settings.minTime,settings)}}if(typeof settings.timeFormat==="string"&&settings.timeFormat.match(/[gh]/)){settings._twelveHourTime=true}if(settings.showOnFocus===false&&settings.showOn.indexOf("focus")!=-1){settings.showOn.splice(settings.showOn.indexOf("focus"),1)}if(!settings.disableTimeRanges){settings.disableTimeRanges=[]}if(settings.disableTimeRanges.length>0){for(var i in settings.disableTimeRanges){settings.disableTimeRanges[i]=[this.time2int(settings.disableTimeRanges[i][0]),this.time2int(settings.disableTimeRanges[i][1])]}settings.disableTimeRanges=settings.disableTimeRanges.sort(function(a,b){return a[0]-b[0]});for(var i=settings.disableTimeRanges.length-1;i>0;i--){if(settings.disableTimeRanges[i][0]<=settings.disableTimeRanges[i-1][1]){settings.disableTimeRanges[i-1]=[Math.min(settings.disableTimeRanges[i][0],settings.disableTimeRanges[i-1][0]),Math.max(settings.disableTimeRanges[i][1],settings.disableTimeRanges[i-1][1])];settings.disableTimeRanges.splice(i,1)}}}return settings}},{key:"_disableTextInputHandler",value:function _disableTextInputHandler(e){switch(e.keyCode){case 13:case 9:return;default:e.preventDefault()}}},{key:"_int2duration",value:function _int2duration(seconds,step){seconds=Math.abs(seconds);var minutes=Math.round(seconds/60),duration=[],hours,mins;if(minutes<60){duration=[minutes,this.settings.lang.mins]}else{hours=Math.floor(minutes/60);mins=minutes%60;if(step==30&&mins==30){hours+=this.settings.lang.decimal+5}duration.push(hours);duration.push(hours==1?this.settings.lang.hr:this.settings.lang.hrs);if(step!=30&&mins){duration.push(mins);duration.push(this.settings.lang.mins)}}return duration.join(" ")}},{key:"_roundAndFormatTime",value:function _roundAndFormatTime(seconds){seconds=this.settings.roundingFunction(seconds,this.settings);if(seconds!==null){return this._int2time(seconds)}}},{key:"_int2time",value:function _int2time(timeInt){if(typeof timeInt!="number"){return null}var seconds=parseInt(timeInt%60),minutes=parseInt(timeInt/60%60),hours=parseInt(timeInt/(60*60)%24);var time=new Date(1970,0,2,hours,minutes,seconds,0);if(isNaN(time.getTime())){return null}if(typeof this.settings.timeFormat==="function"){return this.settings.timeFormat(time)}var output="";var hour,code;for(var i=0;i<this.settings.timeFormat.length;i++){code=this.settings.timeFormat.charAt(i);switch(code){case"a":output+=time.getHours()>11?this.settings.lang.pm:this.settings.lang.am;break;case"A":output+=time.getHours()>11?this.settings.lang.PM:this.settings.lang.AM;break;case"g":hour=time.getHours()%12;output+=hour===0?"12":hour;break;case"G":hour=time.getHours();if(timeInt===ONE_DAY)hour=this.settings.show2400?24:0;output+=hour;break;case"h":hour=time.getHours()%12;if(hour!==0&&hour<10){hour="0"+hour}output+=hour===0?"12":hour;break;case"H":hour=time.getHours();if(timeInt===ONE_DAY)hour=this.settings.show2400?24:0;output+=hour>9?hour:"0"+hour;break;case"i":var minutes=time.getMinutes();output+=minutes>9?minutes:"0"+minutes;break;case"s":seconds=time.getSeconds();output+=seconds>9?seconds:"0"+seconds;break;case"\\":i++;output+=this.settings.timeFormat.charAt(i);break;default:output+=code}}return output}},{key:"_setSelected",value:function _setSelected(){var list=this.list;list.find("li").removeClass("ui-em_timepicker-selected");var timeValue=this.time2int(this._getTimeValue());if(timeValue===null){return}var selected=this._findRow(timeValue);if(selected){var selectedRect=selected.getBoundingClientRect();var listRect=list.get(0).getBoundingClientRect();var topDelta=selectedRect.top-listRect.top;if(topDelta+selectedRect.height>listRect.height||topDelta<0){var newScroll=list.scrollTop()+(selectedRect.top-listRect.top)-selectedRect.height;list.scrollTop(newScroll)}var parsed=Number.parseInt(selected.dataset.time);if(this.settings.forceRoundTime||parsed===timeValue){selected.classList.add("ui-em_timepicker-selected")}}}},{key:"_isFocused",value:function _isFocused(el){return el===document.activeElement}},{key:"_handleFormatValue",value:function _handleFormatValue(e){if(e&&e.detail=="em_timepicker"){return}this._formatValue(e)}},{key:"_formatValue",value:function _formatValue(e,origin){if(this.targetEl.value===""){this._setTimeValue(null,origin);return}if(this._isFocused(this.targetEl)&&(!e||e.type!="change")){return}var settings=this.settings;var seconds=this.time2int(this.targetEl.value);if(seconds===null){var timeFormatErrorEvent=new CustomEvent("timeFormatError");this.targetEl.dispatchEvent(timeFormatErrorEvent);return}var rangeError=false;if(settings.minTime!==null&&settings.maxTime!==null&&(seconds<settings.minTime||seconds>settings.maxTime)){rangeError=true}var _iterator=_createForOfIteratorHelper(settings.disableTimeRanges),_step;try{for(_iterator.s();!(_step=_iterator.n()).done;){var range=_step.value;if(seconds>=range[0]&&seconds<range[1]){rangeError=true;break}}}catch(err){_iterator.e(err)}finally{_iterator.f()}if(settings.forceRoundTime){var roundSeconds=settings.roundingFunction(seconds,settings);if(roundSeconds!=seconds){seconds=roundSeconds;origin=null}}var prettyTime=this._int2time(seconds);if(rangeError){this._setTimeValue(prettyTime);var timeRangeErrorEvent=new CustomEvent("timeRangeError");this.targetEl.dispatchEvent(timeRangeErrorEvent)}else{this._setTimeValue(prettyTime,origin)}}},{key:"_generateNoneElement",value:function _generateNoneElement(optionValue,useSelect){var label,className,value;if(_typeof(optionValue)=="object"){label=optionValue.label;className=optionValue.className;value=optionValue.value}else if(typeof optionValue=="string"){label=optionValue;value=""}else{$.error("Invalid noneOption value")}var el;if(useSelect){el=document.createElement("option");el.value=value}else{el=document.createElement("li");el.dataset.time=String(value)}el.innerText=label;el.classList.add(className);return el}},{key:"_handleKeyUp",value:function _handleKeyUp(e){if(!this.list||!Timepicker.isVisible(this.list)||this.settings.disableTextInput){return true}if(e.type==="paste"||e.type==="cut"){setTimeout(function(){if(this.settings.typeaheadHighlight){this._setSelected()}else{this.list.hide()}},0);return}switch(e.keyCode){case 96:case 97:case 98:case 99:case 100:case 101:case 102:case 103:case 104:case 105:case 48:case 49:case 50:case 51:case 52:case 53:case 54:case 55:case 56:case 57:case 65:case 77:case 80:case 186:case 8:case 46:if(this.settings.typeaheadHighlight){this._setSelected()}else{this.list.hide()}break}}}],[{key:"extractAttrOptions",value:function extractAttrOptions(element,keys){var output={};var _iterator2=_createForOfIteratorHelper(keys),_step2;try{for(_iterator2.s();!(_step2=_iterator2.n()).done;){var key=_step2.value;if(key in element.dataset){output[key]=element.dataset[key]}}}catch(err){_iterator2.e(err)}finally{_iterator2.f()}return output}},{key:"isVisible",value:function isVisible(elem){var el=elem[0];return el.offsetWidth>0&&el.offsetHeight>0}},{key:"hideAll",value:function hideAll(){var _iterator3=_createForOfIteratorHelper(document.getElementsByClassName("ui-em_timepicker-input")),_step3;try{for(_iterator3.s();!(_step3=_iterator3.n()).done;){var el=_step3.value;var tp=el.em_timepickerObj;if(tp){tp.hideMe()}}}catch(err){_iterator3.e(err)}finally{_iterator3.f()}}}]);return Timepicker}();(function(factory){if((typeof exports==="undefined"?"undefined":_typeof(exports))==="object"&&exports&&(typeof module==="undefined"?"undefined":_typeof(module))==="object"&&module&&module.exports===exports){factory(require("jquery"))}else if(typeof define==="function"&&define.amd){define(["jquery"],factory)}else{factory(jQuery)}})(function($){var _lang={};var methods={init:function init(options){return this.each(function(){var self=$(this);var tp=new Timepicker(this,options);var settings=tp.settings;_lang=settings.lang;this.em_timepickerObj=tp;self.addClass("ui-em_timepicker-input");if(settings.useSelect){_render(self)}else{self.prop("autocomplete","off");if(settings.showOn){for(var i in settings.showOn){self.on(settings.showOn[i]+".em_timepicker",methods.show)}}self.on("change.em_timepicker",tp._handleFormatValue);self.on("keydown.em_timepicker",_keydownhandler);self.on("keyup.em_timepicker",tp._handleKeyUp);if(settings.disableTextInput){self.on("keydown.em_timepicker",tp._disableTextInputHandler)}self.on("cut.em_timepicker",tp._handleKeyUp);self.on("paste.em_timepicker",tp._handleKeyUp);tp._formatValue(null,"initial")}})},show:function show(e){var self=$(this);var tp=self[0].em_timepickerObj;var settings=tp.settings;if(e){e.preventDefault()}if(settings.useSelect){tp.list.trigger("focus");return}if(tp._hideKeyboard()){self.trigger("blur")}var list=tp.list;if(self.prop("readonly")){return}if(!list||list.length===0||typeof settings.durationTime==="function"){_render(self);list=tp.list}if(Timepicker.isVisible(list)){return}if(self.is("input")){tp.selectedValue=self.val()}tp._setSelected();Timepicker.hideAll();if(typeof settings.listWidth=="number"){list.width(self.outerWidth()*settings.listWidth)}list.show();var listOffset={};if(settings.orientation.match(/r/)){listOffset.left=self.offset().left+self.outerWidth()-list.outerWidth()+parseInt(list.css("marginLeft").replace("px",""),10)}else if(settings.orientation.match(/l/)){listOffset.left=self.offset().left+parseInt(list.css("marginLeft").replace("px",""),10)}else if(settings.orientation.match(/c/)){listOffset.left=self.offset().left+(self.outerWidth()-list.outerWidth())/2+parseInt(list.css("marginLeft").replace("px",""),10)}var verticalOrientation;if(settings.orientation.match(/t/)){verticalOrientation="t"}else if(settings.orientation.match(/b/)){verticalOrientation="b"}else if(self.offset().top+self.outerHeight(true)+list.outerHeight()>$(window).height()+$(window).scrollTop()){verticalOrientation="t"}else{verticalOrientation="b"}if(verticalOrientation=="t"){list.addClass("ui-em_timepicker-positioned-top");listOffset.top=self.offset().top-list.outerHeight()+parseInt(list.css("marginTop").replace("px",""),10)}else{list.removeClass("ui-em_timepicker-positioned-top");listOffset.top=self.offset().top+self.outerHeight()+parseInt(list.css("marginTop").replace("px",""),10)}list.offset(listOffset);var selected=list.find(".ui-em_timepicker-selected");if(!selected.length){var timeInt=tp.time2int(tp._getTimeValue());if(timeInt!==null){selected=$(tp._findRow(timeInt))}else if(settings.scrollDefault){selected=$(tp._findRow(settings.scrollDefault()))}}if(!selected.length||selected.hasClass("ui-em_timepicker-disabled")){selected=list.find("li:not(.ui-em_timepicker-disabled):first")}if(selected&&selected.length){var topOffset=list.scrollTop()+selected.position().top-selected.outerHeight();list.scrollTop(topOffset)}else{list.scrollTop(0)}if(settings.stopScrollPropagation){$(document).on("wheel.ui-em_timepicker",".ui-em_timepicker-wrapper",function(e){e.preventDefault();var currentScroll=$(this).scrollTop();$(this).scrollTop(currentScroll+e.originalEvent.deltaY)})}$(document).on("mousedown.ui-em_timepicker",_closeHandler);$(window).on("resize.ui-em_timepicker",_closeHandler);if(settings.closeOnWindowScroll){$(document).on("scroll.ui-em_timepicker",_closeHandler)}self.trigger("showTimepicker");return this},hide:function hide(e){var tp=this[0].em_timepickerObj;if(tp){tp.hideMe()}Timepicker.hideAll();return this},option:function option(key,value){if(typeof key=="string"&&typeof value=="undefined"){var tp=this[0].em_timepickerObj;return tp.settings[key]}return this.each(function(){var self=$(this);var tp=self[0].em_timepickerObj;var settings=tp.settings;var list=tp.list;if(_typeof(key)=="object"){settings=$.extend(settings,key)}else if(typeof key=="string"){settings[key]=value}settings=tp.parseSettings(settings);tp.settings=settings;tp._formatValue({type:"change"},"initial");if(list){list.remove();tp.list=null}if(settings.useSelect){_render(self)}})},getSecondsFromMidnight:function getSecondsFromMidnight(){var tp=this[0].em_timepickerObj;return tp.time2int(tp._getTimeValue())},getTime:function getTime(relative_date){var tp=this[0].em_timepickerObj;var time_string=tp._getTimeValue();if(!time_string){return null}var offset=tp.time2int(time_string);if(offset===null){return null}if(!relative_date){relative_date=new Date}var time=new Date(relative_date);time.setHours(offset/3600);time.setMinutes(offset%3600/60);time.setSeconds(offset%60);time.setMilliseconds(0);return time},isVisible:function isVisible(){var tp=this[0].em_timepickerObj;return!!(tp&&tp.list&&Timepicker.isVisible(tp.list))},setTime:function setTime(value){var tp=this[0].em_timepickerObj;var settings=tp.settings;if(settings.forceRoundTime){var prettyTime=tp._roundAndFormatTime(tp.time2int(value))}else{var prettyTime=tp._int2time(tp.time2int(value))}if(value&&prettyTime===null&&settings.noneOption){prettyTime=value}tp._setTimeValue(prettyTime,"initial");tp._formatValue({type:"change"},"initial");if(tp&&tp.list){tp._setSelected()}return this},remove:function remove(){var self=this;if(!self.hasClass("ui-em_timepicker-input")){return}var tp=self[0].em_timepickerObj;var settings=tp.settings;self.removeAttr("autocomplete","off");self.removeClass("ui-em_timepicker-input");self.removeData("em_timepicker-obj");self.off(".em_timepicker");if(tp.list){tp.list.remove()}if(settings.useSelect){self.show()}tp.list=null;return this}};function _render(self){var tp=self[0].em_timepickerObj;var list=tp.list;var settings=tp.settings;if(list&&list.length){list.remove();tp.list=null}if(settings.useSelect){list=$("<select></select>",{class:"ui-em_timepicker-select"});if(self.attr("name")){list.attr("name","ui-em_timepicker-"+self.attr("name"))}var wrapped_list=list}else{list=$("<ul></ul>",{class:"ui-em_timepicker-list"});var wrapped_list=$("<div></div>",{class:"ui-em_timepicker-wrapper",tabindex:-1});wrapped_list.css({display:"none",position:"absolute"}).append(list)}if(settings.noneOption){if(settings.noneOption===true){settings.noneOption=settings.useSelect?"Time...":"None"}if($.isArray(settings.noneOption)){for(var i in settings.noneOption){if(parseInt(i,10)==i){var noneElement=tp._generateNoneElement(settings.noneOption[i],settings.useSelect);list.append(noneElement)}}}else{var noneElement=tp._generateNoneElement(settings.noneOption,settings.useSelect);list.append(noneElement)}}if(settings.className){wrapped_list.addClass(settings.className)}if((settings.minTime!==null||settings.durationTime!==null)&&settings.showDuration){var stepval=typeof settings.step=="function"?"function":settings.step;wrapped_list.addClass("ui-em_timepicker-with-duration");wrapped_list.addClass("ui-em_timepicker-step-"+settings.step)}var durStart=settings.minTime;if(typeof settings.durationTime==="function"){durStart=tp.time2int(settings.durationTime())}else if(settings.durationTime!==null){durStart=settings.durationTime}var start=settings.minTime!==null?settings.minTime:0;var end=settings.maxTime!==null?settings.maxTime:start+ONE_DAY-1;if(end<start){end+=ONE_DAY}if(end===ONE_DAY-1&&$.type(settings.timeFormat)==="string"&&settings.show2400){end=ONE_DAY}var dr=settings.disableTimeRanges;var drCur=0;var drLen=dr.length;var stepFunc=settings.step;if(typeof stepFunc!="function"){stepFunc=function stepFunc(){return settings.step}}for(var i=start,j=0;i<=end;j++,i+=stepFunc(j)*60){var timeInt=i;var timeString=tp._int2time(timeInt);if(settings.useSelect){var row=$("<option></option>",{value:timeString});row.text(timeString)}else{var row=$("<li></li>");row.addClass(timeInt%ONE_DAY<ONE_DAY/2?"ui-em_timepicker-am":"ui-em_timepicker-pm");row.attr("data-time",roundingFunction(timeInt,settings));row.text(timeString)}if((settings.minTime!==null||settings.durationTime!==null)&&settings.showDuration){var durationString=tp._int2duration(i-durStart,settings.step);if(settings.useSelect){row.text(row.text()+" ("+durationString+")")}else{var duration=$("<span></span>",{class:"ui-em_timepicker-duration"});duration.text(" ("+durationString+")");row.append(duration)}}if(drCur<drLen){if(timeInt>=dr[drCur][1]){drCur+=1}if(dr[drCur]&&timeInt>=dr[drCur][0]&&timeInt<dr[drCur][1]){if(settings.useSelect){row.prop("disabled",true)}else{row.addClass("ui-em_timepicker-disabled")}}}list.append(row)}wrapped_list.data("em_timepicker-input",self);tp.list=wrapped_list;if(settings.useSelect){if(self.val()){list.val(tp._roundAndFormatTime(tp.time2int(self.val())))}list.on("focus",function(){$(this).data("em_timepicker-input").trigger("showTimepicker")});list.on("blur",function(){$(this).data("em_timepicker-input").trigger("hideTimepicker")});list.on("change",function(){tp._setTimeValue($(this).val(),"select")});tp._setTimeValue(list.val(),"initial");self.hide().after(list)}else{var appendTo=settings.appendTo;if(typeof appendTo==="string"){appendTo=$(appendTo)}else if(typeof appendTo==="function"){appendTo=appendTo(self)}appendTo.append(wrapped_list);tp._setSelected();list.on("mousedown click","li",function(e){self.off("focus.em_timepicker");self.on("focus.em_timepicker-ie-hack",function(){self.off("focus.em_timepicker-ie-hack");self.on("focus.em_timepicker",methods.show)});if(!tp._hideKeyboard()){self[0].focus()}list.find("li").removeClass("ui-em_timepicker-selected");$(this).addClass("ui-em_timepicker-selected");if(tp._selectValue()){self.trigger("hideTimepicker");list.on("mouseup.em_timepicker click.em_timepicker","li",function(e){list.off("mouseup.em_timepicker click.em_timepicker");wrapped_list.hide()})}})}}function _closeHandler(e){if(e.target==window){return}var target=$(e.target);if(target.closest(".ui-em_timepicker-input").length||target.closest(".ui-em_timepicker-wrapper").length){return}Timepicker.hideAll();$(document).off(".ui-em_timepicker");$(window).off(".ui-em_timepicker")}function _keydownhandler(e){var self=$(this);var tp=self[0].em_timepickerObj;var list=tp.list;if(!list||!Timepicker.isVisible(list)){if(e.keyCode==40){methods.show.call(self.get(0));list=tp.list;if(!tp._hideKeyboard()){self.trigger("focus")}}else{return true}}switch(e.keyCode){case 13:if(tp._selectValue()){tp._formatValue({type:"change"});tp.hideMe()}e.preventDefault();return false;case 38:var selected=list.find(".ui-em_timepicker-selected");if(!selected.length){list.find("li").each(function(i,obj){if($(obj).position().top>0){selected=$(obj);return false}});selected.addClass("ui-em_timepicker-selected")}else if(!selected.is(":first-child")){selected.removeClass("ui-em_timepicker-selected");selected.prev().addClass("ui-em_timepicker-selected");if(selected.prev().position().top<selected.outerHeight()){list.scrollTop(list.scrollTop()-selected.outerHeight())}}return false;case 40:selected=list.find(".ui-em_timepicker-selected");if(selected.length===0){list.find("li").each(function(i,obj){if($(obj).position().top>0){selected=$(obj);return false}});selected.addClass("ui-em_timepicker-selected")}else if(!selected.is(":last-child")){selected.removeClass("ui-em_timepicker-selected");selected.next().addClass("ui-em_timepicker-selected");if(selected.next().position().top+2*selected.outerHeight()>list.outerHeight()){list.scrollTop(list.scrollTop()+selected.outerHeight())}}return false;case 27:list.find("li").removeClass("ui-em_timepicker-selected");tp.hideMe();break;case 9:tp.hideMe();break;default:return true}}$.fn.em_timepicker=function(method){if(!this.length)return this;if(methods[method]){if(!this.hasClass("ui-em_timepicker-input")){return this}return methods[method].apply(this,Array.prototype.slice.call(arguments,1))}else if(_typeof(method)==="object"||!method){return methods.init.apply(this,arguments)}else{$.error("Method "+method+" does not exist on jQuery.em_timepicker")}};$.fn.em_timepicker.defaults=DEFAULT_SETTINGS})})();

/*!
 * flatpickr v4.6.13,, @license MIT
 */
!function(e,n){"object"==typeof exports&&"undefined"!=typeof module?module.exports=n():"function"==typeof define&&define.amd?define(n):(e="undefined"!=typeof globalThis?globalThis:e||self).flatpickr=n()}(this,(function(){"use strict";var e=function(){return(e=Object.assign||function(e){for(var n,t=1,a=arguments.length;t<a;t++)for(var i in n=arguments[t])Object.prototype.hasOwnProperty.call(n,i)&&(e[i]=n[i]);return e}).apply(this,arguments)};function n(){for(var e=0,n=0,t=arguments.length;n<t;n++)e+=arguments[n].length;var a=Array(e),i=0;for(n=0;n<t;n++)for(var o=arguments[n],r=0,l=o.length;r<l;r++,i++)a[i]=o[r];return a}var t=["onChange","onClose","onDayCreate","onDestroy","onKeyDown","onMonthChange","onOpen","onParseConfig","onReady","onValueUpdate","onYearChange","onPreCalendarPosition"],a={_disable:[],allowInput:!1,allowInvalidPreload:!1,altFormat:"F j, Y",altInput:!1,altInputClass:"form-control input",animate:"object"==typeof window&&-1===window.navigator.userAgent.indexOf("MSIE"),ariaDateFormat:"F j, Y",autoFillDefaultTime:!0,clickOpens:!0,closeOnSelect:!0,conjunction:", ",dateFormat:"Y-m-d",defaultHour:12,defaultMinute:0,defaultSeconds:0,disable:[],disableMobile:!1,enableSeconds:!1,enableTime:!1,errorHandler:function(e){return"undefined"!=typeof console&&console.warn(e)},getWeek:function(e){var n=new Date(e.getTime());n.setHours(0,0,0,0),n.setDate(n.getDate()+3-(n.getDay()+6)%7);var t=new Date(n.getFullYear(),0,4);return 1+Math.round(((n.getTime()-t.getTime())/864e5-3+(t.getDay()+6)%7)/7)},hourIncrement:1,ignoredFocusElements:[],inline:!1,locale:"default",minuteIncrement:5,mode:"single",monthSelectorType:"dropdown",nextArrow:"<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z' /></svg>",noCalendar:!1,now:new Date,onChange:[],onClose:[],onDayCreate:[],onDestroy:[],onKeyDown:[],onMonthChange:[],onOpen:[],onParseConfig:[],onReady:[],onValueUpdate:[],onYearChange:[],onPreCalendarPosition:[],plugins:[],position:"auto",positionElement:void 0,prevArrow:"<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z' /></svg>",shorthandCurrentMonth:!1,showMonths:1,static:!1,time_24hr:!1,weekNumbers:!1,wrap:!1},i={weekdays:{shorthand:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],longhand:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]},months:{shorthand:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],longhand:["January","February","March","April","May","June","July","August","September","October","November","December"]},daysInMonth:[31,28,31,30,31,30,31,31,30,31,30,31],firstDayOfWeek:0,ordinal:function(e){var n=e%100;if(n>3&&n<21)return"th";switch(n%10){case 1:return"st";case 2:return"nd";case 3:return"rd";default:return"th"}},rangeSeparator:" to ",weekAbbreviation:"Wk",scrollTitle:"Scroll to increment",toggleTitle:"Click to toggle",amPM:["AM","PM"],yearAriaLabel:"Year",monthAriaLabel:"Month",hourAriaLabel:"Hour",minuteAriaLabel:"Minute",time_24hr:!1},o=function(e,n){return void 0===n&&(n=2),("000"+e).slice(-1*n)},r=function(e){return!0===e?1:0};function l(e,n){var t;return function(){var a=this,i=arguments;clearTimeout(t),t=setTimeout((function(){return e.apply(a,i)}),n)}}var c=function(e){return e instanceof Array?e:[e]};function s(e,n,t){if(!0===t)return e.classList.add(n);e.classList.remove(n)}function d(e,n,t){var a=window.document.createElement(e);return n=n||"",t=t||"",a.className=n,void 0!==t&&(a.textContent=t),a}function u(e){for(;e.firstChild;)e.removeChild(e.firstChild)}function f(e,n){return n(e)?e:e.parentNode?f(e.parentNode,n):void 0}function m(e,n){var t=d("div","numInputWrapper"),a=d("input","numInput "+e),i=d("span","arrowUp"),o=d("span","arrowDown");if(-1===navigator.userAgent.indexOf("MSIE 9.0")?a.type="number":(a.type="text",a.pattern="\\d*"),void 0!==n)for(var r in n)a.setAttribute(r,n[r]);return t.appendChild(a),t.appendChild(i),t.appendChild(o),t}function g(e){try{return"function"==typeof e.composedPath?e.composedPath()[0]:e.target}catch(n){return e.target}}var p=function(){},h=function(e,n,t){return t.months[n?"shorthand":"longhand"][e]},v={D:p,F:function(e,n,t){e.setMonth(t.months.longhand.indexOf(n))},G:function(e,n){e.setHours((e.getHours()>=12?12:0)+parseFloat(n))},H:function(e,n){e.setHours(parseFloat(n))},J:function(e,n){e.setDate(parseFloat(n))},K:function(e,n,t){e.setHours(e.getHours()%12+12*r(new RegExp(t.amPM[1],"i").test(n)))},M:function(e,n,t){e.setMonth(t.months.shorthand.indexOf(n))},S:function(e,n){e.setSeconds(parseFloat(n))},U:function(e,n){return new Date(1e3*parseFloat(n))},W:function(e,n,t){var a=parseInt(n),i=new Date(e.getFullYear(),0,2+7*(a-1),0,0,0,0);return i.setDate(i.getDate()-i.getDay()+t.firstDayOfWeek),i},Y:function(e,n){e.setFullYear(parseFloat(n))},Z:function(e,n){return new Date(n)},d:function(e,n){e.setDate(parseFloat(n))},h:function(e,n){e.setHours((e.getHours()>=12?12:0)+parseFloat(n))},i:function(e,n){e.setMinutes(parseFloat(n))},j:function(e,n){e.setDate(parseFloat(n))},l:p,m:function(e,n){e.setMonth(parseFloat(n)-1)},n:function(e,n){e.setMonth(parseFloat(n)-1)},s:function(e,n){e.setSeconds(parseFloat(n))},u:function(e,n){return new Date(parseFloat(n))},w:p,y:function(e,n){e.setFullYear(2e3+parseFloat(n))}},D={D:"",F:"",G:"(\\d\\d|\\d)",H:"(\\d\\d|\\d)",J:"(\\d\\d|\\d)\\w+",K:"",M:"",S:"(\\d\\d|\\d)",U:"(.+)",W:"(\\d\\d|\\d)",Y:"(\\d{4})",Z:"(.+)",d:"(\\d\\d|\\d)",h:"(\\d\\d|\\d)",i:"(\\d\\d|\\d)",j:"(\\d\\d|\\d)",l:"",m:"(\\d\\d|\\d)",n:"(\\d\\d|\\d)",s:"(\\d\\d|\\d)",u:"(.+)",w:"(\\d\\d|\\d)",y:"(\\d{2})"},w={Z:function(e){return e.toISOString()},D:function(e,n,t){return n.weekdays.shorthand[w.w(e,n,t)]},F:function(e,n,t){return h(w.n(e,n,t)-1,!1,n)},G:function(e,n,t){return o(w.h(e,n,t))},H:function(e){return o(e.getHours())},J:function(e,n){return void 0!==n.ordinal?e.getDate()+n.ordinal(e.getDate()):e.getDate()},K:function(e,n){return n.amPM[r(e.getHours()>11)]},M:function(e,n){return h(e.getMonth(),!0,n)},S:function(e){return o(e.getSeconds())},U:function(e){return e.getTime()/1e3},W:function(e,n,t){return t.getWeek(e)},Y:function(e){return o(e.getFullYear(),4)},d:function(e){return o(e.getDate())},h:function(e){return e.getHours()%12?e.getHours()%12:12},i:function(e){return o(e.getMinutes())},j:function(e){return e.getDate()},l:function(e,n){return n.weekdays.longhand[e.getDay()]},m:function(e){return o(e.getMonth()+1)},n:function(e){return e.getMonth()+1},s:function(e){return e.getSeconds()},u:function(e){return e.getTime()},w:function(e){return e.getDay()},y:function(e){return String(e.getFullYear()).substring(2)}},b=function(e){var n=e.config,t=void 0===n?a:n,o=e.l10n,r=void 0===o?i:o,l=e.isMobile,c=void 0!==l&&l;return function(e,n,a){var i=a||r;return void 0===t.formatDate||c?n.split("").map((function(n,a,o){return w[n]&&"\\"!==o[a-1]?w[n](e,i,t):"\\"!==n?n:""})).join(""):t.formatDate(e,n,i)}},C=function(e){var n=e.config,t=void 0===n?a:n,o=e.l10n,r=void 0===o?i:o;return function(e,n,i,o){if(0===e||e){var l,c=o||r,s=e;if(e instanceof Date)l=new Date(e.getTime());else if("string"!=typeof e&&void 0!==e.toFixed)l=new Date(e);else if("string"==typeof e){var d=n||(t||a).dateFormat,u=String(e).trim();if("today"===u)l=new Date,i=!0;else if(t&&t.parseDate)l=t.parseDate(e,d);else if(/Z$/.test(u)||/GMT$/.test(u))l=new Date(e);else{for(var f=void 0,m=[],g=0,p=0,h="";g<d.length;g++){var w=d[g],b="\\"===w,C="\\"===d[g-1]||b;if(D[w]&&!C){h+=D[w];var M=new RegExp(h).exec(e);M&&(f=!0)&&m["Y"!==w?"push":"unshift"]({fn:v[w],val:M[++p]})}else b||(h+=".")}l=t&&t.noCalendar?new Date((new Date).setHours(0,0,0,0)):new Date((new Date).getFullYear(),0,1,0,0,0,0),m.forEach((function(e){var n=e.fn,t=e.val;return l=n(l,t,c)||l})),l=f?l:void 0}}if(l instanceof Date&&!isNaN(l.getTime()))return!0===i&&l.setHours(0,0,0,0),l;t.errorHandler(new Error("Invalid date provided: "+s))}}};function M(e,n,t){return void 0===t&&(t=!0),!1!==t?new Date(e.getTime()).setHours(0,0,0,0)-new Date(n.getTime()).setHours(0,0,0,0):e.getTime()-n.getTime()}var y=function(e,n,t){return 3600*e+60*n+t},x=864e5;function E(e){var n=e.defaultHour,t=e.defaultMinute,a=e.defaultSeconds;if(void 0!==e.minDate){var i=e.minDate.getHours(),o=e.minDate.getMinutes(),r=e.minDate.getSeconds();n<i&&(n=i),n===i&&t<o&&(t=o),n===i&&t===o&&a<r&&(a=e.minDate.getSeconds())}if(void 0!==e.maxDate){var l=e.maxDate.getHours(),c=e.maxDate.getMinutes();(n=Math.min(n,l))===l&&(t=Math.min(c,t)),n===l&&t===c&&(a=e.maxDate.getSeconds())}return{hours:n,minutes:t,seconds:a}}"function"!=typeof Object.assign&&(Object.assign=function(e){for(var n=[],t=1;t<arguments.length;t++)n[t-1]=arguments[t];if(!e)throw TypeError("Cannot convert undefined or null to object");for(var a=function(n){n&&Object.keys(n).forEach((function(t){return e[t]=n[t]}))},i=0,o=n;i<o.length;i++){var r=o[i];a(r)}return e});function k(p,v){var w={config:e(e({},a),I.defaultConfig),l10n:i};function k(){var e;return(null===(e=w.calendarContainer)||void 0===e?void 0:e.getRootNode()).activeElement||document.activeElement}function T(e){return e.bind(w)}function S(){var e=w.config;!1===e.weekNumbers&&1===e.showMonths||!0!==e.noCalendar&&window.requestAnimationFrame((function(){if(void 0!==w.calendarContainer&&(w.calendarContainer.style.visibility="hidden",w.calendarContainer.style.display="block"),void 0!==w.daysContainer){var n=(w.days.offsetWidth+1)*e.showMonths;w.daysContainer.style.width=n+"px",w.calendarContainer.style.width=n+(void 0!==w.weekWrapper?w.weekWrapper.offsetWidth:0)+"px",w.calendarContainer.style.removeProperty("visibility"),w.calendarContainer.style.removeProperty("display")}}))}function _(e){if(0===w.selectedDates.length){var n=void 0===w.config.minDate||M(new Date,w.config.minDate)>=0?new Date:new Date(w.config.minDate.getTime()),t=E(w.config);n.setHours(t.hours,t.minutes,t.seconds,n.getMilliseconds()),w.selectedDates=[n],w.latestSelectedDateObj=n}void 0!==e&&"blur"!==e.type&&function(e){e.preventDefault();var n="keydown"===e.type,t=g(e),a=t;void 0!==w.amPM&&t===w.amPM&&(w.amPM.textContent=w.l10n.amPM[r(w.amPM.textContent===w.l10n.amPM[0])]);var i=parseFloat(a.getAttribute("min")),l=parseFloat(a.getAttribute("max")),c=parseFloat(a.getAttribute("step")),s=parseInt(a.value,10),d=e.delta||(n?38===e.which?1:-1:0),u=s+c*d;if(void 0!==a.value&&2===a.value.length){var f=a===w.hourElement,m=a===w.minuteElement;u<i?(u=l+u+r(!f)+(r(f)&&r(!w.amPM)),m&&L(void 0,-1,w.hourElement)):u>l&&(u=a===w.hourElement?u-l-r(!w.amPM):i,m&&L(void 0,1,w.hourElement)),w.amPM&&f&&(1===c?u+s===23:Math.abs(u-s)>c)&&(w.amPM.textContent=w.l10n.amPM[r(w.amPM.textContent===w.l10n.amPM[0])]),a.value=o(u)}}(e);var a=w._input.value;O(),ye(),w._input.value!==a&&w._debouncedChange()}function O(){if(void 0!==w.hourElement&&void 0!==w.minuteElement){var e,n,t=(parseInt(w.hourElement.value.slice(-2),10)||0)%24,a=(parseInt(w.minuteElement.value,10)||0)%60,i=void 0!==w.secondElement?(parseInt(w.secondElement.value,10)||0)%60:0;void 0!==w.amPM&&(e=t,n=w.amPM.textContent,t=e%12+12*r(n===w.l10n.amPM[1]));var o=void 0!==w.config.minTime||w.config.minDate&&w.minDateHasTime&&w.latestSelectedDateObj&&0===M(w.latestSelectedDateObj,w.config.minDate,!0),l=void 0!==w.config.maxTime||w.config.maxDate&&w.maxDateHasTime&&w.latestSelectedDateObj&&0===M(w.latestSelectedDateObj,w.config.maxDate,!0);if(void 0!==w.config.maxTime&&void 0!==w.config.minTime&&w.config.minTime>w.config.maxTime){var c=y(w.config.minTime.getHours(),w.config.minTime.getMinutes(),w.config.minTime.getSeconds()),s=y(w.config.maxTime.getHours(),w.config.maxTime.getMinutes(),w.config.maxTime.getSeconds()),d=y(t,a,i);if(d>s&&d<c){var u=function(e){var n=Math.floor(e/3600),t=(e-3600*n)/60;return[n,t,e-3600*n-60*t]}(c);t=u[0],a=u[1],i=u[2]}}else{if(l){var f=void 0!==w.config.maxTime?w.config.maxTime:w.config.maxDate;(t=Math.min(t,f.getHours()))===f.getHours()&&(a=Math.min(a,f.getMinutes())),a===f.getMinutes()&&(i=Math.min(i,f.getSeconds()))}if(o){var m=void 0!==w.config.minTime?w.config.minTime:w.config.minDate;(t=Math.max(t,m.getHours()))===m.getHours()&&a<m.getMinutes()&&(a=m.getMinutes()),a===m.getMinutes()&&(i=Math.max(i,m.getSeconds()))}}A(t,a,i)}}function F(e){var n=e||w.latestSelectedDateObj;n&&n instanceof Date&&A(n.getHours(),n.getMinutes(),n.getSeconds())}function A(e,n,t){void 0!==w.latestSelectedDateObj&&w.latestSelectedDateObj.setHours(e%24,n,t||0,0),w.hourElement&&w.minuteElement&&!w.isMobile&&(w.hourElement.value=o(w.config.time_24hr?e:(12+e)%12+12*r(e%12==0)),w.minuteElement.value=o(n),void 0!==w.amPM&&(w.amPM.textContent=w.l10n.amPM[r(e>=12)]),void 0!==w.secondElement&&(w.secondElement.value=o(t)))}function N(e){var n=g(e),t=parseInt(n.value)+(e.delta||0);(t/1e3>1||"Enter"===e.key&&!/[^\d]/.test(t.toString()))&&ee(t)}function P(e,n,t,a){return n instanceof Array?n.forEach((function(n){return P(e,n,t,a)})):e instanceof Array?e.forEach((function(e){return P(e,n,t,a)})):(e.addEventListener(n,t,a),void w._handlers.push({remove:function(){return e.removeEventListener(n,t,a)}}))}function Y(){De("onChange")}function j(e,n){var t=void 0!==e?w.parseDate(e):w.latestSelectedDateObj||(w.config.minDate&&w.config.minDate>w.now?w.config.minDate:w.config.maxDate&&w.config.maxDate<w.now?w.config.maxDate:w.now),a=w.currentYear,i=w.currentMonth;try{void 0!==t&&(w.currentYear=t.getFullYear(),w.currentMonth=t.getMonth())}catch(e){e.message="Invalid date supplied: "+t,w.config.errorHandler(e)}n&&w.currentYear!==a&&(De("onYearChange"),q()),!n||w.currentYear===a&&w.currentMonth===i||De("onMonthChange"),w.redraw()}function H(e){var n=g(e);~n.className.indexOf("arrow")&&L(e,n.classList.contains("arrowUp")?1:-1)}function L(e,n,t){var a=e&&g(e),i=t||a&&a.parentNode&&a.parentNode.firstChild,o=we("increment");o.delta=n,i&&i.dispatchEvent(o)}function R(e,n,t,a){var i=ne(n,!0),o=d("span",e,n.getDate().toString());return o.dateObj=n,o.$i=a,o.setAttribute("aria-label",w.formatDate(n,w.config.ariaDateFormat)),-1===e.indexOf("hidden")&&0===M(n,w.now)&&(w.todayDateElem=o,o.classList.add("today"),o.setAttribute("aria-current","date")),i?(o.tabIndex=-1,be(n)&&(o.classList.add("selected"),w.selectedDateElem=o,"range"===w.config.mode&&(s(o,"startRange",w.selectedDates[0]&&0===M(n,w.selectedDates[0],!0)),s(o,"endRange",w.selectedDates[1]&&0===M(n,w.selectedDates[1],!0)),"nextMonthDay"===e&&o.classList.add("inRange")))):o.classList.add("flatpickr-disabled"),"range"===w.config.mode&&function(e){return!("range"!==w.config.mode||w.selectedDates.length<2)&&(M(e,w.selectedDates[0])>=0&&M(e,w.selectedDates[1])<=0)}(n)&&!be(n)&&o.classList.add("inRange"),w.weekNumbers&&1===w.config.showMonths&&"prevMonthDay"!==e&&a%7==6&&w.weekNumbers.insertAdjacentHTML("beforeend","<span class='flatpickr-day'>"+w.config.getWeek(n)+"</span>"),De("onDayCreate",o),o}function W(e){e.focus(),"range"===w.config.mode&&oe(e)}function B(e){for(var n=e>0?0:w.config.showMonths-1,t=e>0?w.config.showMonths:-1,a=n;a!=t;a+=e)for(var i=w.daysContainer.children[a],o=e>0?0:i.children.length-1,r=e>0?i.children.length:-1,l=o;l!=r;l+=e){var c=i.children[l];if(-1===c.className.indexOf("hidden")&&ne(c.dateObj))return c}}function J(e,n){var t=k(),a=te(t||document.body),i=void 0!==e?e:a?t:void 0!==w.selectedDateElem&&te(w.selectedDateElem)?w.selectedDateElem:void 0!==w.todayDateElem&&te(w.todayDateElem)?w.todayDateElem:B(n>0?1:-1);void 0===i?w._input.focus():a?function(e,n){for(var t=-1===e.className.indexOf("Month")?e.dateObj.getMonth():w.currentMonth,a=n>0?w.config.showMonths:-1,i=n>0?1:-1,o=t-w.currentMonth;o!=a;o+=i)for(var r=w.daysContainer.children[o],l=t-w.currentMonth===o?e.$i+n:n<0?r.children.length-1:0,c=r.children.length,s=l;s>=0&&s<c&&s!=(n>0?c:-1);s+=i){var d=r.children[s];if(-1===d.className.indexOf("hidden")&&ne(d.dateObj)&&Math.abs(e.$i-s)>=Math.abs(n))return W(d)}w.changeMonth(i),J(B(i),0)}(i,n):W(i)}function K(e,n){for(var t=(new Date(e,n,1).getDay()-w.l10n.firstDayOfWeek+7)%7,a=w.utils.getDaysInMonth((n-1+12)%12,e),i=w.utils.getDaysInMonth(n,e),o=window.document.createDocumentFragment(),r=w.config.showMonths>1,l=r?"prevMonthDay hidden":"prevMonthDay",c=r?"nextMonthDay hidden":"nextMonthDay",s=a+1-t,u=0;s<=a;s++,u++)o.appendChild(R("flatpickr-day "+l,new Date(e,n-1,s),0,u));for(s=1;s<=i;s++,u++)o.appendChild(R("flatpickr-day",new Date(e,n,s),0,u));for(var f=i+1;f<=42-t&&(1===w.config.showMonths||u%7!=0);f++,u++)o.appendChild(R("flatpickr-day "+c,new Date(e,n+1,f%i),0,u));var m=d("div","dayContainer");return m.appendChild(o),m}function U(){if(void 0!==w.daysContainer){u(w.daysContainer),w.weekNumbers&&u(w.weekNumbers);for(var e=document.createDocumentFragment(),n=0;n<w.config.showMonths;n++){var t=new Date(w.currentYear,w.currentMonth,1);t.setMonth(w.currentMonth+n),e.appendChild(K(t.getFullYear(),t.getMonth()))}w.daysContainer.appendChild(e),w.days=w.daysContainer.firstChild,"range"===w.config.mode&&1===w.selectedDates.length&&oe()}}function q(){if(!(w.config.showMonths>1||"dropdown"!==w.config.monthSelectorType)){var e=function(e){return!(void 0!==w.config.minDate&&w.currentYear===w.config.minDate.getFullYear()&&e<w.config.minDate.getMonth())&&!(void 0!==w.config.maxDate&&w.currentYear===w.config.maxDate.getFullYear()&&e>w.config.maxDate.getMonth())};w.monthsDropdownContainer.tabIndex=-1,w.monthsDropdownContainer.innerHTML="";for(var n=0;n<12;n++)if(e(n)){var t=d("option","flatpickr-monthDropdown-month");t.value=new Date(w.currentYear,n).getMonth().toString(),t.textContent=h(n,w.config.shorthandCurrentMonth,w.l10n),t.tabIndex=-1,w.currentMonth===n&&(t.selected=!0),w.monthsDropdownContainer.appendChild(t)}}}function $(){var e,n=d("div","flatpickr-month"),t=window.document.createDocumentFragment();w.config.showMonths>1||"static"===w.config.monthSelectorType?e=d("span","cur-month"):(w.monthsDropdownContainer=d("select","flatpickr-monthDropdown-months"),w.monthsDropdownContainer.setAttribute("aria-label",w.l10n.monthAriaLabel),P(w.monthsDropdownContainer,"change",(function(e){var n=g(e),t=parseInt(n.value,10);w.changeMonth(t-w.currentMonth),De("onMonthChange")})),q(),e=w.monthsDropdownContainer);var a=m("cur-year",{tabindex:"-1"}),i=a.getElementsByTagName("input")[0];i.setAttribute("aria-label",w.l10n.yearAriaLabel),w.config.minDate&&i.setAttribute("min",w.config.minDate.getFullYear().toString()),w.config.maxDate&&(i.setAttribute("max",w.config.maxDate.getFullYear().toString()),i.disabled=!!w.config.minDate&&w.config.minDate.getFullYear()===w.config.maxDate.getFullYear());var o=d("div","flatpickr-current-month");return o.appendChild(e),o.appendChild(a),t.appendChild(o),n.appendChild(t),{container:n,yearElement:i,monthElement:e}}function V(){u(w.monthNav),w.monthNav.appendChild(w.prevMonthNav),w.config.showMonths&&(w.yearElements=[],w.monthElements=[]);for(var e=w.config.showMonths;e--;){var n=$();w.yearElements.push(n.yearElement),w.monthElements.push(n.monthElement),w.monthNav.appendChild(n.container)}w.monthNav.appendChild(w.nextMonthNav)}function z(){w.weekdayContainer?u(w.weekdayContainer):w.weekdayContainer=d("div","flatpickr-weekdays");for(var e=w.config.showMonths;e--;){var n=d("div","flatpickr-weekdaycontainer");w.weekdayContainer.appendChild(n)}return G(),w.weekdayContainer}function G(){if(w.weekdayContainer){var e=w.l10n.firstDayOfWeek,t=n(w.l10n.weekdays.shorthand);e>0&&e<t.length&&(t=n(t.splice(e,t.length),t.splice(0,e)));for(var a=w.config.showMonths;a--;)w.weekdayContainer.children[a].innerHTML="\n      <span class='flatpickr-weekday'>\n        "+t.join("</span><span class='flatpickr-weekday'>")+"\n      </span>\n      "}}function Z(e,n){void 0===n&&(n=!0);var t=n?e:e-w.currentMonth;t<0&&!0===w._hidePrevMonthArrow||t>0&&!0===w._hideNextMonthArrow||(w.currentMonth+=t,(w.currentMonth<0||w.currentMonth>11)&&(w.currentYear+=w.currentMonth>11?1:-1,w.currentMonth=(w.currentMonth+12)%12,De("onYearChange"),q()),U(),De("onMonthChange"),Ce())}function Q(e){return w.calendarContainer.contains(e)}function X(e){if(w.isOpen&&!w.config.inline){var n=g(e),t=Q(n),a=!(n===w.input||n===w.altInput||w.element.contains(n)||e.path&&e.path.indexOf&&(~e.path.indexOf(w.input)||~e.path.indexOf(w.altInput)))&&!t&&!Q(e.relatedTarget),i=!w.config.ignoredFocusElements.some((function(e){return e.contains(n)}));a&&i&&(w.config.allowInput&&w.setDate(w._input.value,!1,w.config.altInput?w.config.altFormat:w.config.dateFormat),void 0!==w.timeContainer&&void 0!==w.minuteElement&&void 0!==w.hourElement&&""!==w.input.value&&void 0!==w.input.value&&_(),w.close(),w.config&&"range"===w.config.mode&&1===w.selectedDates.length&&w.clear(!1))}}function ee(e){if(!(!e||w.config.minDate&&e<w.config.minDate.getFullYear()||w.config.maxDate&&e>w.config.maxDate.getFullYear())){var n=e,t=w.currentYear!==n;w.currentYear=n||w.currentYear,w.config.maxDate&&w.currentYear===w.config.maxDate.getFullYear()?w.currentMonth=Math.min(w.config.maxDate.getMonth(),w.currentMonth):w.config.minDate&&w.currentYear===w.config.minDate.getFullYear()&&(w.currentMonth=Math.max(w.config.minDate.getMonth(),w.currentMonth)),t&&(w.redraw(),De("onYearChange"),q())}}function ne(e,n){var t;void 0===n&&(n=!0);var a=w.parseDate(e,void 0,n);if(w.config.minDate&&a&&M(a,w.config.minDate,void 0!==n?n:!w.minDateHasTime)<0||w.config.maxDate&&a&&M(a,w.config.maxDate,void 0!==n?n:!w.maxDateHasTime)>0)return!1;if(!w.config.enable&&0===w.config.disable.length)return!0;if(void 0===a)return!1;for(var i=!!w.config.enable,o=null!==(t=w.config.enable)&&void 0!==t?t:w.config.disable,r=0,l=void 0;r<o.length;r++){if("function"==typeof(l=o[r])&&l(a))return i;if(l instanceof Date&&void 0!==a&&l.getTime()===a.getTime())return i;if("string"==typeof l){var c=w.parseDate(l,void 0,!0);return c&&c.getTime()===a.getTime()?i:!i}if("object"==typeof l&&void 0!==a&&l.from&&l.to&&a.getTime()>=l.from.getTime()&&a.getTime()<=l.to.getTime())return i}return!i}function te(e){return void 0!==w.daysContainer&&(-1===e.className.indexOf("hidden")&&-1===e.className.indexOf("flatpickr-disabled")&&w.daysContainer.contains(e))}function ae(e){var n=e.target===w._input,t=w._input.value.trimEnd()!==Me();!n||!t||e.relatedTarget&&Q(e.relatedTarget)||w.setDate(w._input.value,!0,e.target===w.altInput?w.config.altFormat:w.config.dateFormat)}function ie(e){var n=g(e),t=w.config.wrap?p.contains(n):n===w._input,a=w.config.allowInput,i=w.isOpen&&(!a||!t),o=w.config.inline&&t&&!a;if(13===e.keyCode&&t){if(a)return w.setDate(w._input.value,!0,n===w.altInput?w.config.altFormat:w.config.dateFormat),w.close(),n.blur();w.open()}else if(Q(n)||i||o){var r=!!w.timeContainer&&w.timeContainer.contains(n);switch(e.keyCode){case 13:r?(e.preventDefault(),_(),fe()):me(e);break;case 27:e.preventDefault(),fe();break;case 8:case 46:t&&!w.config.allowInput&&(e.preventDefault(),w.clear());break;case 37:case 39:if(r||t)w.hourElement&&w.hourElement.focus();else{e.preventDefault();var l=k();if(void 0!==w.daysContainer&&(!1===a||l&&te(l))){var c=39===e.keyCode?1:-1;e.ctrlKey?(e.stopPropagation(),Z(c),J(B(1),0)):J(void 0,c)}}break;case 38:case 40:e.preventDefault();var s=40===e.keyCode?1:-1;w.daysContainer&&void 0!==n.$i||n===w.input||n===w.altInput?e.ctrlKey?(e.stopPropagation(),ee(w.currentYear-s),J(B(1),0)):r||J(void 0,7*s):n===w.currentYearElement?ee(w.currentYear-s):w.config.enableTime&&(!r&&w.hourElement&&w.hourElement.focus(),_(e),w._debouncedChange());break;case 9:if(r){var d=[w.hourElement,w.minuteElement,w.secondElement,w.amPM].concat(w.pluginElements).filter((function(e){return e})),u=d.indexOf(n);if(-1!==u){var f=d[u+(e.shiftKey?-1:1)];e.preventDefault(),(f||w._input).focus()}}else!w.config.noCalendar&&w.daysContainer&&w.daysContainer.contains(n)&&e.shiftKey&&(e.preventDefault(),w._input.focus())}}if(void 0!==w.amPM&&n===w.amPM)switch(e.key){case w.l10n.amPM[0].charAt(0):case w.l10n.amPM[0].charAt(0).toLowerCase():w.amPM.textContent=w.l10n.amPM[0],O(),ye();break;case w.l10n.amPM[1].charAt(0):case w.l10n.amPM[1].charAt(0).toLowerCase():w.amPM.textContent=w.l10n.amPM[1],O(),ye()}(t||Q(n))&&De("onKeyDown",e)}function oe(e,n){if(void 0===n&&(n="flatpickr-day"),1===w.selectedDates.length&&(!e||e.classList.contains(n)&&!e.classList.contains("flatpickr-disabled"))){for(var t=e?e.dateObj.getTime():w.days.firstElementChild.dateObj.getTime(),a=w.parseDate(w.selectedDates[0],void 0,!0).getTime(),i=Math.min(t,w.selectedDates[0].getTime()),o=Math.max(t,w.selectedDates[0].getTime()),r=!1,l=0,c=0,s=i;s<o;s+=x)ne(new Date(s),!0)||(r=r||s>i&&s<o,s<a&&(!l||s>l)?l=s:s>a&&(!c||s<c)&&(c=s));Array.from(w.rContainer.querySelectorAll("*:nth-child(-n+"+w.config.showMonths+") > ."+n)).forEach((function(n){var i,o,s,d=n.dateObj.getTime(),u=l>0&&d<l||c>0&&d>c;if(u)return n.classList.add("notAllowed"),void["inRange","startRange","endRange"].forEach((function(e){n.classList.remove(e)}));r&&!u||(["startRange","inRange","endRange","notAllowed"].forEach((function(e){n.classList.remove(e)})),void 0!==e&&(e.classList.add(t<=w.selectedDates[0].getTime()?"startRange":"endRange"),a<t&&d===a?n.classList.add("startRange"):a>t&&d===a&&n.classList.add("endRange"),d>=l&&(0===c||d<=c)&&(o=a,s=t,(i=d)>Math.min(o,s)&&i<Math.max(o,s))&&n.classList.add("inRange")))}))}}function re(){!w.isOpen||w.config.static||w.config.inline||de()}function le(e){return function(n){var t=w.config["_"+e+"Date"]=w.parseDate(n,w.config.dateFormat),a=w.config["_"+("min"===e?"max":"min")+"Date"];void 0!==t&&(w["min"===e?"minDateHasTime":"maxDateHasTime"]=t.getHours()>0||t.getMinutes()>0||t.getSeconds()>0),w.selectedDates&&(w.selectedDates=w.selectedDates.filter((function(e){return ne(e)})),w.selectedDates.length||"min"!==e||F(t),ye()),w.daysContainer&&(ue(),void 0!==t?w.currentYearElement[e]=t.getFullYear().toString():w.currentYearElement.removeAttribute(e),w.currentYearElement.disabled=!!a&&void 0!==t&&a.getFullYear()===t.getFullYear())}}function ce(){return w.config.wrap?p.querySelector("[data-input]"):p}function se(){"object"!=typeof w.config.locale&&void 0===I.l10ns[w.config.locale]&&w.config.errorHandler(new Error("flatpickr: invalid locale "+w.config.locale)),w.l10n=e(e({},I.l10ns.default),"object"==typeof w.config.locale?w.config.locale:"default"!==w.config.locale?I.l10ns[w.config.locale]:void 0),D.D="("+w.l10n.weekdays.shorthand.join("|")+")",D.l="("+w.l10n.weekdays.longhand.join("|")+")",D.M="("+w.l10n.months.shorthand.join("|")+")",D.F="("+w.l10n.months.longhand.join("|")+")",D.K="("+w.l10n.amPM[0]+"|"+w.l10n.amPM[1]+"|"+w.l10n.amPM[0].toLowerCase()+"|"+w.l10n.amPM[1].toLowerCase()+")",void 0===e(e({},v),JSON.parse(JSON.stringify(p.dataset||{}))).time_24hr&&void 0===I.defaultConfig.time_24hr&&(w.config.time_24hr=w.l10n.time_24hr),w.formatDate=b(w),w.parseDate=C({config:w.config,l10n:w.l10n})}function de(e){if("function"!=typeof w.config.position){if(void 0!==w.calendarContainer){De("onPreCalendarPosition");var n=e||w._positionElement,t=Array.prototype.reduce.call(w.calendarContainer.children,(function(e,n){return e+n.offsetHeight}),0),a=w.calendarContainer.offsetWidth,i=w.config.position.split(" "),o=i[0],r=i.length>1?i[1]:null,l=n.getBoundingClientRect(),c=window.innerHeight-l.bottom,d="above"===o||"below"!==o&&c<t&&l.top>t,u=window.pageYOffset+l.top+(d?-t-2:n.offsetHeight+2);if(s(w.calendarContainer,"arrowTop",!d),s(w.calendarContainer,"arrowBottom",d),!w.config.inline){var f=window.pageXOffset+l.left,m=!1,g=!1;"center"===r?(f-=(a-l.width)/2,m=!0):"right"===r&&(f-=a-l.width,g=!0),s(w.calendarContainer,"arrowLeft",!m&&!g),s(w.calendarContainer,"arrowCenter",m),s(w.calendarContainer,"arrowRight",g);var p=window.document.body.offsetWidth-(window.pageXOffset+l.right),h=f+a>window.document.body.offsetWidth,v=p+a>window.document.body.offsetWidth;if(s(w.calendarContainer,"rightMost",h),!w.config.static)if(w.calendarContainer.style.top=u+"px",h)if(v){var D=function(){for(var e=null,n=0;n<document.styleSheets.length;n++){var t=document.styleSheets[n];if(t.cssRules){try{t.cssRules}catch(e){continue}e=t;break}}return null!=e?e:(a=document.createElement("style"),document.head.appendChild(a),a.sheet);var a}();if(void 0===D)return;var b=window.document.body.offsetWidth,C=Math.max(0,b/2-a/2),M=D.cssRules.length,y="{left:"+l.left+"px;right:auto;}";s(w.calendarContainer,"rightMost",!1),s(w.calendarContainer,"centerMost",!0),D.insertRule(".flatpickr-calendar.centerMost:before,.flatpickr-calendar.centerMost:after"+y,M),w.calendarContainer.style.left=C+"px",w.calendarContainer.style.right="auto"}else w.calendarContainer.style.left="auto",w.calendarContainer.style.right=p+"px";else w.calendarContainer.style.left=f+"px",w.calendarContainer.style.right="auto"}}}else w.config.position(w,e)}function ue(){w.config.noCalendar||w.isMobile||(q(),Ce(),U())}function fe(){w._input.focus(),-1!==window.navigator.userAgent.indexOf("MSIE")||void 0!==navigator.msMaxTouchPoints?setTimeout(w.close,0):w.close()}function me(e){e.preventDefault(),e.stopPropagation();var n=f(g(e),(function(e){return e.classList&&e.classList.contains("flatpickr-day")&&!e.classList.contains("flatpickr-disabled")&&!e.classList.contains("notAllowed")}));if(void 0!==n){var t=n,a=w.latestSelectedDateObj=new Date(t.dateObj.getTime()),i=(a.getMonth()<w.currentMonth||a.getMonth()>w.currentMonth+w.config.showMonths-1)&&"range"!==w.config.mode;if(w.selectedDateElem=t,"single"===w.config.mode)w.selectedDates=[a];else if("multiple"===w.config.mode){var o=be(a);o?w.selectedDates.splice(parseInt(o),1):w.selectedDates.push(a)}else"range"===w.config.mode&&(2===w.selectedDates.length&&w.clear(!1,!1),w.latestSelectedDateObj=a,w.selectedDates.push(a),0!==M(a,w.selectedDates[0],!0)&&w.selectedDates.sort((function(e,n){return e.getTime()-n.getTime()})));if(O(),i){var r=w.currentYear!==a.getFullYear();w.currentYear=a.getFullYear(),w.currentMonth=a.getMonth(),r&&(De("onYearChange"),q()),De("onMonthChange")}if(Ce(),U(),ye(),i||"range"===w.config.mode||1!==w.config.showMonths?void 0!==w.selectedDateElem&&void 0===w.hourElement&&w.selectedDateElem&&w.selectedDateElem.focus():W(t),void 0!==w.hourElement&&void 0!==w.hourElement&&w.hourElement.focus(),w.config.closeOnSelect){var l="single"===w.config.mode&&!w.config.enableTime,c="range"===w.config.mode&&2===w.selectedDates.length&&!w.config.enableTime;(l||c)&&fe()}Y()}}w.parseDate=C({config:w.config,l10n:w.l10n}),w._handlers=[],w.pluginElements=[],w.loadedPlugins=[],w._bind=P,w._setHoursFromDate=F,w._positionCalendar=de,w.changeMonth=Z,w.changeYear=ee,w.clear=function(e,n){void 0===e&&(e=!0);void 0===n&&(n=!0);w.input.value="",void 0!==w.altInput&&(w.altInput.value="");void 0!==w.mobileInput&&(w.mobileInput.value="");w.selectedDates=[],w.latestSelectedDateObj=void 0,!0===n&&(w.currentYear=w._initialDate.getFullYear(),w.currentMonth=w._initialDate.getMonth());if(!0===w.config.enableTime){var t=E(w.config),a=t.hours,i=t.minutes,o=t.seconds;A(a,i,o)}w.redraw(),e&&De("onChange")},w.close=function(){w.isOpen=!1,w.isMobile||(void 0!==w.calendarContainer&&w.calendarContainer.classList.remove("open"),void 0!==w._input&&w._input.classList.remove("active"));De("onClose")},w.onMouseOver=oe,w._createElement=d,w.createDay=R,w.destroy=function(){void 0!==w.config&&De("onDestroy");for(var e=w._handlers.length;e--;)w._handlers[e].remove();if(w._handlers=[],w.mobileInput)w.mobileInput.parentNode&&w.mobileInput.parentNode.removeChild(w.mobileInput),w.mobileInput=void 0;else if(w.calendarContainer&&w.calendarContainer.parentNode)if(w.config.static&&w.calendarContainer.parentNode){var n=w.calendarContainer.parentNode;if(n.lastChild&&n.removeChild(n.lastChild),n.parentNode){for(;n.firstChild;)n.parentNode.insertBefore(n.firstChild,n);n.parentNode.removeChild(n)}}else w.calendarContainer.parentNode.removeChild(w.calendarContainer);w.altInput&&(w.input.type="text",w.altInput.parentNode&&w.altInput.parentNode.removeChild(w.altInput),delete w.altInput);w.input&&(w.input.type=w.input._type,w.input.classList.remove("flatpickr-input"),w.input.removeAttribute("readonly"));["_showTimeInput","latestSelectedDateObj","_hideNextMonthArrow","_hidePrevMonthArrow","__hideNextMonthArrow","__hidePrevMonthArrow","isMobile","isOpen","selectedDateElem","minDateHasTime","maxDateHasTime","days","daysContainer","_input","_positionElement","innerContainer","rContainer","monthNav","todayDateElem","calendarContainer","weekdayContainer","prevMonthNav","nextMonthNav","monthsDropdownContainer","currentMonthElement","currentYearElement","navigationCurrentMonth","selectedDateElem","config"].forEach((function(e){try{delete w[e]}catch(e){}}))},w.isEnabled=ne,w.jumpToDate=j,w.updateValue=ye,w.open=function(e,n){void 0===n&&(n=w._positionElement);if(!0===w.isMobile){if(e){e.preventDefault();var t=g(e);t&&t.blur()}return void 0!==w.mobileInput&&(w.mobileInput.focus(),w.mobileInput.click()),void De("onOpen")}if(w._input.disabled||w.config.inline)return;var a=w.isOpen;w.isOpen=!0,a||(w.calendarContainer.classList.add("open"),w._input.classList.add("active"),De("onOpen"),de(n));!0===w.config.enableTime&&!0===w.config.noCalendar&&(!1!==w.config.allowInput||void 0!==e&&w.timeContainer.contains(e.relatedTarget)||setTimeout((function(){return w.hourElement.select()}),50))},w.redraw=ue,w.set=function(e,n){if(null!==e&&"object"==typeof e)for(var a in Object.assign(w.config,e),e)void 0!==ge[a]&&ge[a].forEach((function(e){return e()}));else w.config[e]=n,void 0!==ge[e]?ge[e].forEach((function(e){return e()})):t.indexOf(e)>-1&&(w.config[e]=c(n));w.redraw(),ye(!0)},w.setDate=function(e,n,t){void 0===n&&(n=!1);void 0===t&&(t=w.config.dateFormat);if(0!==e&&!e||e instanceof Array&&0===e.length)return w.clear(n);pe(e,t),w.latestSelectedDateObj=w.selectedDates[w.selectedDates.length-1],w.redraw(),j(void 0,n),F(),0===w.selectedDates.length&&w.clear(!1);ye(n),n&&De("onChange")},w.toggle=function(e){if(!0===w.isOpen)return w.close();w.open(e)};var ge={locale:[se,G],showMonths:[V,S,z],minDate:[j],maxDate:[j],positionElement:[ve],clickOpens:[function(){!0===w.config.clickOpens?(P(w._input,"focus",w.open),P(w._input,"click",w.open)):(w._input.removeEventListener("focus",w.open),w._input.removeEventListener("click",w.open))}]};function pe(e,n){var t=[];if(e instanceof Array)t=e.map((function(e){return w.parseDate(e,n)}));else if(e instanceof Date||"number"==typeof e)t=[w.parseDate(e,n)];else if("string"==typeof e)switch(w.config.mode){case"single":case"time":t=[w.parseDate(e,n)];break;case"multiple":t=e.split(w.config.conjunction).map((function(e){return w.parseDate(e,n)}));break;case"range":t=e.split(w.l10n.rangeSeparator).map((function(e){return w.parseDate(e,n)}))}else w.config.errorHandler(new Error("Invalid date supplied: "+JSON.stringify(e)));w.selectedDates=w.config.allowInvalidPreload?t:t.filter((function(e){return e instanceof Date&&ne(e,!1)})),"range"===w.config.mode&&w.selectedDates.sort((function(e,n){return e.getTime()-n.getTime()}))}function he(e){return e.slice().map((function(e){return"string"==typeof e||"number"==typeof e||e instanceof Date?w.parseDate(e,void 0,!0):e&&"object"==typeof e&&e.from&&e.to?{from:w.parseDate(e.from,void 0),to:w.parseDate(e.to,void 0)}:e})).filter((function(e){return e}))}function ve(){w._positionElement=w.config.positionElement||w._input}function De(e,n){if(void 0!==w.config){var t=w.config[e];if(void 0!==t&&t.length>0)for(var a=0;t[a]&&a<t.length;a++)t[a](w.selectedDates,w.input.value,w,n);"onChange"===e&&(w.input.dispatchEvent(we("change")),w.input.dispatchEvent(we("input")))}}function we(e){var n=document.createEvent("Event");return n.initEvent(e,!0,!0),n}function be(e){for(var n=0;n<w.selectedDates.length;n++){var t=w.selectedDates[n];if(t instanceof Date&&0===M(t,e))return""+n}return!1}function Ce(){w.config.noCalendar||w.isMobile||!w.monthNav||(w.yearElements.forEach((function(e,n){var t=new Date(w.currentYear,w.currentMonth,1);t.setMonth(w.currentMonth+n),w.config.showMonths>1||"static"===w.config.monthSelectorType?w.monthElements[n].textContent=h(t.getMonth(),w.config.shorthandCurrentMonth,w.l10n)+" ":w.monthsDropdownContainer.value=t.getMonth().toString(),e.value=t.getFullYear().toString()})),w._hidePrevMonthArrow=void 0!==w.config.minDate&&(w.currentYear===w.config.minDate.getFullYear()?w.currentMonth<=w.config.minDate.getMonth():w.currentYear<w.config.minDate.getFullYear()),w._hideNextMonthArrow=void 0!==w.config.maxDate&&(w.currentYear===w.config.maxDate.getFullYear()?w.currentMonth+1>w.config.maxDate.getMonth():w.currentYear>w.config.maxDate.getFullYear()))}function Me(e){var n=e||(w.config.altInput?w.config.altFormat:w.config.dateFormat);return w.selectedDates.map((function(e){return w.formatDate(e,n)})).filter((function(e,n,t){return"range"!==w.config.mode||w.config.enableTime||t.indexOf(e)===n})).join("range"!==w.config.mode?w.config.conjunction:w.l10n.rangeSeparator)}function ye(e){void 0===e&&(e=!0),void 0!==w.mobileInput&&w.mobileFormatStr&&(w.mobileInput.value=void 0!==w.latestSelectedDateObj?w.formatDate(w.latestSelectedDateObj,w.mobileFormatStr):""),w.input.value=Me(w.config.dateFormat),void 0!==w.altInput&&(w.altInput.value=Me(w.config.altFormat)),!1!==e&&De("onValueUpdate")}function xe(e){var n=g(e),t=w.prevMonthNav.contains(n),a=w.nextMonthNav.contains(n);t||a?Z(t?-1:1):w.yearElements.indexOf(n)>=0?n.select():n.classList.contains("arrowUp")?w.changeYear(w.currentYear+1):n.classList.contains("arrowDown")&&w.changeYear(w.currentYear-1)}return function(){w.element=w.input=p,w.isOpen=!1,function(){var n=["wrap","weekNumbers","allowInput","allowInvalidPreload","clickOpens","time_24hr","enableTime","noCalendar","altInput","shorthandCurrentMonth","inline","static","enableSeconds","disableMobile"],i=e(e({},JSON.parse(JSON.stringify(p.dataset||{}))),v),o={};w.config.parseDate=i.parseDate,w.config.formatDate=i.formatDate,Object.defineProperty(w.config,"enable",{get:function(){return w.config._enable},set:function(e){w.config._enable=he(e)}}),Object.defineProperty(w.config,"disable",{get:function(){return w.config._disable},set:function(e){w.config._disable=he(e)}});var r="time"===i.mode;if(!i.dateFormat&&(i.enableTime||r)){var l=I.defaultConfig.dateFormat||a.dateFormat;o.dateFormat=i.noCalendar||r?"H:i"+(i.enableSeconds?":S":""):l+" H:i"+(i.enableSeconds?":S":"")}if(i.altInput&&(i.enableTime||r)&&!i.altFormat){var s=I.defaultConfig.altFormat||a.altFormat;o.altFormat=i.noCalendar||r?"h:i"+(i.enableSeconds?":S K":" K"):s+" h:i"+(i.enableSeconds?":S":"")+" K"}Object.defineProperty(w.config,"minDate",{get:function(){return w.config._minDate},set:le("min")}),Object.defineProperty(w.config,"maxDate",{get:function(){return w.config._maxDate},set:le("max")});var d=function(e){return function(n){w.config["min"===e?"_minTime":"_maxTime"]=w.parseDate(n,"H:i:S")}};Object.defineProperty(w.config,"minTime",{get:function(){return w.config._minTime},set:d("min")}),Object.defineProperty(w.config,"maxTime",{get:function(){return w.config._maxTime},set:d("max")}),"time"===i.mode&&(w.config.noCalendar=!0,w.config.enableTime=!0);Object.assign(w.config,o,i);for(var u=0;u<n.length;u++)w.config[n[u]]=!0===w.config[n[u]]||"true"===w.config[n[u]];t.filter((function(e){return void 0!==w.config[e]})).forEach((function(e){w.config[e]=c(w.config[e]||[]).map(T)})),w.isMobile=!w.config.disableMobile&&!w.config.inline&&"single"===w.config.mode&&!w.config.disable.length&&!w.config.enable&&!w.config.weekNumbers&&/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);for(u=0;u<w.config.plugins.length;u++){var f=w.config.plugins[u](w)||{};for(var m in f)t.indexOf(m)>-1?w.config[m]=c(f[m]).map(T).concat(w.config[m]):void 0===i[m]&&(w.config[m]=f[m])}i.altInputClass||(w.config.altInputClass=ce().className+" "+w.config.altInputClass);De("onParseConfig")}(),se(),function(){if(w.input=ce(),!w.input)return void w.config.errorHandler(new Error("Invalid input element specified"));w.input._type=w.input.type,w.input.type="text",w.input.classList.add("flatpickr-input"),w._input=w.input,w.config.altInput&&(w.altInput=d(w.input.nodeName,w.config.altInputClass),w._input=w.altInput,w.altInput.placeholder=w.input.placeholder,w.altInput.disabled=w.input.disabled,w.altInput.required=w.input.required,w.altInput.tabIndex=w.input.tabIndex,w.altInput.type="text",w.input.setAttribute("type","hidden"),!w.config.static&&w.input.parentNode&&w.input.parentNode.insertBefore(w.altInput,w.input.nextSibling));w.config.allowInput||w._input.setAttribute("readonly","readonly");ve()}(),function(){w.selectedDates=[],w.now=w.parseDate(w.config.now)||new Date;var e=w.config.defaultDate||("INPUT"!==w.input.nodeName&&"TEXTAREA"!==w.input.nodeName||!w.input.placeholder||w.input.value!==w.input.placeholder?w.input.value:null);e&&pe(e,w.config.dateFormat);w._initialDate=w.selectedDates.length>0?w.selectedDates[0]:w.config.minDate&&w.config.minDate.getTime()>w.now.getTime()?w.config.minDate:w.config.maxDate&&w.config.maxDate.getTime()<w.now.getTime()?w.config.maxDate:w.now,w.currentYear=w._initialDate.getFullYear(),w.currentMonth=w._initialDate.getMonth(),w.selectedDates.length>0&&(w.latestSelectedDateObj=w.selectedDates[0]);void 0!==w.config.minTime&&(w.config.minTime=w.parseDate(w.config.minTime,"H:i"));void 0!==w.config.maxTime&&(w.config.maxTime=w.parseDate(w.config.maxTime,"H:i"));w.minDateHasTime=!!w.config.minDate&&(w.config.minDate.getHours()>0||w.config.minDate.getMinutes()>0||w.config.minDate.getSeconds()>0),w.maxDateHasTime=!!w.config.maxDate&&(w.config.maxDate.getHours()>0||w.config.maxDate.getMinutes()>0||w.config.maxDate.getSeconds()>0)}(),w.utils={getDaysInMonth:function(e,n){return void 0===e&&(e=w.currentMonth),void 0===n&&(n=w.currentYear),1===e&&(n%4==0&&n%100!=0||n%400==0)?29:w.l10n.daysInMonth[e]}},w.isMobile||function(){var e=window.document.createDocumentFragment();if(w.calendarContainer=d("div","flatpickr-calendar"),w.calendarContainer.tabIndex=-1,!w.config.noCalendar){if(e.appendChild((w.monthNav=d("div","flatpickr-months"),w.yearElements=[],w.monthElements=[],w.prevMonthNav=d("span","flatpickr-prev-month"),w.prevMonthNav.innerHTML=w.config.prevArrow,w.nextMonthNav=d("span","flatpickr-next-month"),w.nextMonthNav.innerHTML=w.config.nextArrow,V(),Object.defineProperty(w,"_hidePrevMonthArrow",{get:function(){return w.__hidePrevMonthArrow},set:function(e){w.__hidePrevMonthArrow!==e&&(s(w.prevMonthNav,"flatpickr-disabled",e),w.__hidePrevMonthArrow=e)}}),Object.defineProperty(w,"_hideNextMonthArrow",{get:function(){return w.__hideNextMonthArrow},set:function(e){w.__hideNextMonthArrow!==e&&(s(w.nextMonthNav,"flatpickr-disabled",e),w.__hideNextMonthArrow=e)}}),w.currentYearElement=w.yearElements[0],Ce(),w.monthNav)),w.innerContainer=d("div","flatpickr-innerContainer"),w.config.weekNumbers){var n=function(){w.calendarContainer.classList.add("hasWeeks");var e=d("div","flatpickr-weekwrapper");e.appendChild(d("span","flatpickr-weekday",w.l10n.weekAbbreviation));var n=d("div","flatpickr-weeks");return e.appendChild(n),{weekWrapper:e,weekNumbers:n}}(),t=n.weekWrapper,a=n.weekNumbers;w.innerContainer.appendChild(t),w.weekNumbers=a,w.weekWrapper=t}w.rContainer=d("div","flatpickr-rContainer"),w.rContainer.appendChild(z()),w.daysContainer||(w.daysContainer=d("div","flatpickr-days"),w.daysContainer.tabIndex=-1),U(),w.rContainer.appendChild(w.daysContainer),w.innerContainer.appendChild(w.rContainer),e.appendChild(w.innerContainer)}w.config.enableTime&&e.appendChild(function(){w.calendarContainer.classList.add("hasTime"),w.config.noCalendar&&w.calendarContainer.classList.add("noCalendar");var e=E(w.config);w.timeContainer=d("div","flatpickr-time"),w.timeContainer.tabIndex=-1;var n=d("span","flatpickr-time-separator",":"),t=m("flatpickr-hour",{"aria-label":w.l10n.hourAriaLabel});w.hourElement=t.getElementsByTagName("input")[0];var a=m("flatpickr-minute",{"aria-label":w.l10n.minuteAriaLabel});w.minuteElement=a.getElementsByTagName("input")[0],w.hourElement.tabIndex=w.minuteElement.tabIndex=-1,w.hourElement.value=o(w.latestSelectedDateObj?w.latestSelectedDateObj.getHours():w.config.time_24hr?e.hours:function(e){switch(e%24){case 0:case 12:return 12;default:return e%12}}(e.hours)),w.minuteElement.value=o(w.latestSelectedDateObj?w.latestSelectedDateObj.getMinutes():e.minutes),w.hourElement.setAttribute("step",w.config.hourIncrement.toString()),w.minuteElement.setAttribute("step",w.config.minuteIncrement.toString()),w.hourElement.setAttribute("min",w.config.time_24hr?"0":"1"),w.hourElement.setAttribute("max",w.config.time_24hr?"23":"12"),w.hourElement.setAttribute("maxlength","2"),w.minuteElement.setAttribute("min","0"),w.minuteElement.setAttribute("max","59"),w.minuteElement.setAttribute("maxlength","2"),w.timeContainer.appendChild(t),w.timeContainer.appendChild(n),w.timeContainer.appendChild(a),w.config.time_24hr&&w.timeContainer.classList.add("time24hr");if(w.config.enableSeconds){w.timeContainer.classList.add("hasSeconds");var i=m("flatpickr-second");w.secondElement=i.getElementsByTagName("input")[0],w.secondElement.value=o(w.latestSelectedDateObj?w.latestSelectedDateObj.getSeconds():e.seconds),w.secondElement.setAttribute("step",w.minuteElement.getAttribute("step")),w.secondElement.setAttribute("min","0"),w.secondElement.setAttribute("max","59"),w.secondElement.setAttribute("maxlength","2"),w.timeContainer.appendChild(d("span","flatpickr-time-separator",":")),w.timeContainer.appendChild(i)}w.config.time_24hr||(w.amPM=d("span","flatpickr-am-pm",w.l10n.amPM[r((w.latestSelectedDateObj?w.hourElement.value:w.config.defaultHour)>11)]),w.amPM.title=w.l10n.toggleTitle,w.amPM.tabIndex=-1,w.timeContainer.appendChild(w.amPM));return w.timeContainer}());s(w.calendarContainer,"rangeMode","range"===w.config.mode),s(w.calendarContainer,"animate",!0===w.config.animate),s(w.calendarContainer,"multiMonth",w.config.showMonths>1),w.calendarContainer.appendChild(e);var i=void 0!==w.config.appendTo&&void 0!==w.config.appendTo.nodeType;if((w.config.inline||w.config.static)&&(w.calendarContainer.classList.add(w.config.inline?"inline":"static"),w.config.inline&&(!i&&w.element.parentNode?w.element.parentNode.insertBefore(w.calendarContainer,w._input.nextSibling):void 0!==w.config.appendTo&&w.config.appendTo.appendChild(w.calendarContainer)),w.config.static)){var l=d("div","flatpickr-wrapper");w.element.parentNode&&w.element.parentNode.insertBefore(l,w.element),l.appendChild(w.element),w.altInput&&l.appendChild(w.altInput),l.appendChild(w.calendarContainer)}w.config.static||w.config.inline||(void 0!==w.config.appendTo?w.config.appendTo:window.document.body).appendChild(w.calendarContainer)}(),function(){w.config.wrap&&["open","close","toggle","clear"].forEach((function(e){Array.prototype.forEach.call(w.element.querySelectorAll("[data-"+e+"]"),(function(n){return P(n,"click",w[e])}))}));if(w.isMobile)return void function(){var e=w.config.enableTime?w.config.noCalendar?"time":"datetime-local":"date";w.mobileInput=d("input",w.input.className+" flatpickr-mobile"),w.mobileInput.tabIndex=1,w.mobileInput.type=e,w.mobileInput.disabled=w.input.disabled,w.mobileInput.required=w.input.required,w.mobileInput.placeholder=w.input.placeholder,w.mobileFormatStr="datetime-local"===e?"Y-m-d\\TH:i:S":"date"===e?"Y-m-d":"H:i:S",w.selectedDates.length>0&&(w.mobileInput.defaultValue=w.mobileInput.value=w.formatDate(w.selectedDates[0],w.mobileFormatStr));w.config.minDate&&(w.mobileInput.min=w.formatDate(w.config.minDate,"Y-m-d"));w.config.maxDate&&(w.mobileInput.max=w.formatDate(w.config.maxDate,"Y-m-d"));w.input.getAttribute("step")&&(w.mobileInput.step=String(w.input.getAttribute("step")));w.input.type="hidden",void 0!==w.altInput&&(w.altInput.type="hidden");try{w.input.parentNode&&w.input.parentNode.insertBefore(w.mobileInput,w.input.nextSibling)}catch(e){}P(w.mobileInput,"change",(function(e){w.setDate(g(e).value,!1,w.mobileFormatStr),De("onChange"),De("onClose")}))}();var e=l(re,50);w._debouncedChange=l(Y,300),w.daysContainer&&!/iPhone|iPad|iPod/i.test(navigator.userAgent)&&P(w.daysContainer,"mouseover",(function(e){"range"===w.config.mode&&oe(g(e))}));P(w._input,"keydown",ie),void 0!==w.calendarContainer&&P(w.calendarContainer,"keydown",ie);w.config.inline||w.config.static||P(window,"resize",e);void 0!==window.ontouchstart?P(window.document,"touchstart",X):P(window.document,"mousedown",X);P(window.document,"focus",X,{capture:!0}),!0===w.config.clickOpens&&(P(w._input,"focus",w.open),P(w._input,"click",w.open));void 0!==w.daysContainer&&(P(w.monthNav,"click",xe),P(w.monthNav,["keyup","increment"],N),P(w.daysContainer,"click",me));if(void 0!==w.timeContainer&&void 0!==w.minuteElement&&void 0!==w.hourElement){var n=function(e){return g(e).select()};P(w.timeContainer,["increment"],_),P(w.timeContainer,"blur",_,{capture:!0}),P(w.timeContainer,"click",H),P([w.hourElement,w.minuteElement],["focus","click"],n),void 0!==w.secondElement&&P(w.secondElement,"focus",(function(){return w.secondElement&&w.secondElement.select()})),void 0!==w.amPM&&P(w.amPM,"click",(function(e){_(e)}))}w.config.allowInput&&P(w._input,"blur",ae)}(),(w.selectedDates.length||w.config.noCalendar)&&(w.config.enableTime&&F(w.config.noCalendar?w.latestSelectedDateObj:void 0),ye(!1)),S();var n=/^((?!chrome|android).)*safari/i.test(navigator.userAgent);!w.isMobile&&n&&de(),De("onReady")}(),w}function T(e,n){for(var t=Array.prototype.slice.call(e).filter((function(e){return e instanceof HTMLElement})),a=[],i=0;i<t.length;i++){var o=t[i];try{if(null!==o.getAttribute("data-fp-omit"))continue;void 0!==o._flatpickr&&(o._flatpickr.destroy(),o._flatpickr=void 0),o._flatpickr=k(o,n||{}),a.push(o._flatpickr)}catch(e){console.error(e)}}return 1===a.length?a[0]:a}"undefined"!=typeof HTMLElement&&"undefined"!=typeof HTMLCollection&&"undefined"!=typeof NodeList&&(HTMLCollection.prototype.flatpickr=NodeList.prototype.flatpickr=function(e){return T(this,e)},HTMLElement.prototype.flatpickr=function(e){return T([this],e)});var I=function(e,n){return"string"==typeof e?T(window.document.querySelectorAll(e),n):e instanceof Node?T([e],n):T(e,n)};return I.defaultConfig={},I.l10ns={en:e({},i),default:e({},i)},I.localize=function(n){I.l10ns.default=e(e({},I.l10ns.default),n)},I.setDefaults=function(n){I.defaultConfig=e(e({},I.defaultConfig),n)},I.parseDate=C({}),I.formatDate=b({}),I.compareDates=M,"undefined"!=typeof jQuery&&void 0!==jQuery.fn&&(jQuery.fn.flatpickr=function(e){return T(this,e)}),Date.prototype.fp_incr=function(e){return new Date(this.getFullYear(),this.getMonth(),this.getDate()+("string"==typeof e?parseInt(e,10):e))},"undefined"!=typeof window&&(window.flatpickr=I),I}));
(function(global,factory){typeof exports==="object"&&typeof module!=="undefined"?module.exports=factory():typeof define==="function"&&define.amd?define(factory):(global=typeof globalThis!=="undefined"?globalThis:global||self,global.monthSelectPlugin=factory())})(this,function(){"use strict";var __assign=function(){__assign=Object.assign||function __assign(t){for(var s,i=1,n=arguments.length;i<n;i++){s=arguments[i];for(var p in s)if(Object.prototype.hasOwnProperty.call(s,p))t[p]=s[p]}return t};return __assign.apply(this,arguments)};var monthToStr=function(monthNumber,shorthand,locale){return locale.months[shorthand?"shorthand":"longhand"][monthNumber]};function clearNode(node){while(node.firstChild)node.removeChild(node.firstChild)}function getEventTarget(event){try{if(typeof event.composedPath==="function"){var path=event.composedPath();return path[0]}return event.target}catch(error){return event.target}}var defaultConfig={shorthand:false,dateFormat:"F Y",altFormat:"F Y",theme:"light"};function monthSelectPlugin(pluginConfig){var config=__assign(__assign({},defaultConfig),pluginConfig);return function(fp){fp.config.dateFormat=config.dateFormat;fp.config.altFormat=config.altFormat;var self={monthsContainer:null};function clearUnnecessaryDOMElements(){if(!fp.rContainer)return;clearNode(fp.rContainer);for(var index=0;index<fp.monthElements.length;index++){var element=fp.monthElements[index];if(!element.parentNode)continue;element.parentNode.removeChild(element)}}function build(){if(!fp.rContainer)return;self.monthsContainer=fp._createElement("div","flatpickr-monthSelect-months");self.monthsContainer.tabIndex=-1;buildMonths();fp.rContainer.appendChild(self.monthsContainer);fp.calendarContainer.classList.add("flatpickr-monthSelect-theme-"+config.theme)}function buildMonths(){if(!self.monthsContainer)return;clearNode(self.monthsContainer);var frag=document.createDocumentFragment();for(var i=0;i<12;i++){var month=fp.createDay("flatpickr-monthSelect-month",new Date(fp.currentYear,i),0,i);if(month.dateObj.getMonth()===(new Date).getMonth()&&month.dateObj.getFullYear()===(new Date).getFullYear())month.classList.add("today");month.textContent=monthToStr(i,config.shorthand,fp.l10n);month.addEventListener("click",selectMonth);frag.appendChild(month)}self.monthsContainer.appendChild(frag);if(fp.config.minDate&&fp.currentYear===fp.config.minDate.getFullYear())fp.prevMonthNav.classList.add("flatpickr-disabled");else fp.prevMonthNav.classList.remove("flatpickr-disabled");if(fp.config.maxDate&&fp.currentYear===fp.config.maxDate.getFullYear())fp.nextMonthNav.classList.add("flatpickr-disabled");else fp.nextMonthNav.classList.remove("flatpickr-disabled")}function bindEvents(){fp._bind(fp.prevMonthNav,"click",function(e){e.preventDefault();e.stopPropagation();fp.changeYear(fp.currentYear-1);selectYear();buildMonths()});fp._bind(fp.nextMonthNav,"click",function(e){e.preventDefault();e.stopPropagation();fp.changeYear(fp.currentYear+1);selectYear();buildMonths()});fp._bind(self.monthsContainer,"mouseover",function(e){if(fp.config.mode==="range")fp.onMouseOver(getEventTarget(e),"flatpickr-monthSelect-month")})}function setCurrentlySelected(){if(!fp.rContainer)return;if(!fp.selectedDates.length)return;var currentlySelected=fp.rContainer.querySelectorAll(".flatpickr-monthSelect-month.selected");for(var index=0;index<currentlySelected.length;index++){currentlySelected[index].classList.remove("selected")}var targetMonth=fp.selectedDates[0].getMonth();var month=fp.rContainer.querySelector(".flatpickr-monthSelect-month:nth-child("+(targetMonth+1)+")");if(month){month.classList.add("selected")}}function selectYear(){var selectedDate=fp.selectedDates[0];if(selectedDate){selectedDate=new Date(selectedDate);selectedDate.setFullYear(fp.currentYear);if(fp.config.minDate&&selectedDate<fp.config.minDate){selectedDate=fp.config.minDate}if(fp.config.maxDate&&selectedDate>fp.config.maxDate){selectedDate=fp.config.maxDate}fp.currentYear=selectedDate.getFullYear()}fp.currentYearElement.value=String(fp.currentYear);if(fp.rContainer){var months=fp.rContainer.querySelectorAll(".flatpickr-monthSelect-month");months.forEach(function(month){month.dateObj.setFullYear(fp.currentYear);if(fp.config.minDate&&month.dateObj<fp.config.minDate||fp.config.maxDate&&month.dateObj>fp.config.maxDate){month.classList.add("flatpickr-disabled")}else{month.classList.remove("flatpickr-disabled")}})}setCurrentlySelected()}function selectMonth(e){e.preventDefault();e.stopPropagation();var eventTarget=getEventTarget(e);if(!(eventTarget instanceof Element))return;if(eventTarget.classList.contains("flatpickr-disabled"))return;if(eventTarget.classList.contains("notAllowed"))return;setMonth(eventTarget.dateObj);if(fp.config.closeOnSelect){var single=fp.config.mode==="single";var range=fp.config.mode==="range"&&fp.selectedDates.length===2;if(single||range)fp.close()}}function setMonth(date){var selectedDate=new Date(fp.currentYear,date.getMonth(),date.getDate());var selectedDates=[];switch(fp.config.mode){case"single":selectedDates=[selectedDate];break;case"multiple":selectedDates.push(selectedDate);break;case"range":if(fp.selectedDates.length===2){selectedDates=[selectedDate]}else{selectedDates=fp.selectedDates.concat([selectedDate]);selectedDates.sort(function(a,b){return a.getTime()-b.getTime()})}break}fp.setDate(selectedDates,true);setCurrentlySelected()}var shifts={37:-1,39:1,40:3,38:-3};function onKeyDown(_,__,___,e){var shouldMove=shifts[e.keyCode]!==undefined;if(!shouldMove&&e.keyCode!==13){return}if(!fp.rContainer||!self.monthsContainer)return;var currentlySelected=fp.rContainer.querySelector(".flatpickr-monthSelect-month.selected");var index=Array.prototype.indexOf.call(self.monthsContainer.children,document.activeElement);if(index===-1){var target=currentlySelected||self.monthsContainer.firstElementChild;target.focus();index=target.$i}if(shouldMove){self.monthsContainer.children[(12+index+shifts[e.keyCode])%12].focus()}else if(e.keyCode===13&&self.monthsContainer.contains(document.activeElement)){setMonth(document.activeElement.dateObj)}}function closeHook(){var _a;if(((_a=fp.config)===null||_a===void 0?void 0:_a.mode)==="range"&&fp.selectedDates.length===1)fp.clear(false);if(!fp.selectedDates.length)buildMonths()}function stubCurrentMonth(){config._stubbedCurrentMonth=fp._initialDate.getMonth();fp._initialDate.setMonth(config._stubbedCurrentMonth);fp.currentMonth=config._stubbedCurrentMonth}function unstubCurrentMonth(){if(!config._stubbedCurrentMonth)return;fp._initialDate.setMonth(config._stubbedCurrentMonth);fp.currentMonth=config._stubbedCurrentMonth;delete config._stubbedCurrentMonth}function destroyPluginInstance(){if(self.monthsContainer!==null){var months=self.monthsContainer.querySelectorAll(".flatpickr-monthSelect-month");for(var index=0;index<months.length;index++){months[index].removeEventListener("click",selectMonth)}}}return{onParseConfig:function(){fp.config.enableTime=false},onValueUpdate:setCurrentlySelected,onKeyDown:onKeyDown,onReady:[stubCurrentMonth,clearUnnecessaryDOMElements,build,bindEvents,setCurrentlySelected,function(){fp.config.onClose.push(closeHook);fp.loadedPlugins.push("monthSelect")}],onDestroy:[unstubCurrentMonth,destroyPluginInstance,function(){fp.config.onClose=fp.config.onClose.filter(function(hook){return hook!==closeHook})}]}}}return monthSelectPlugin});

/*!
 * popperjs v2.11.5 - MIT License - https://github.com/popperjs/popper-core
 */
!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?t(exports):"function"==typeof define&&define.amd?define(["exports"],t):t((e="undefined"!=typeof globalThis?globalThis:e||self).Popper={})}(this,(function(e){"use strict";function t(e){if(null==e)return window;if("[object Window]"!==e.toString()){var t=e.ownerDocument;return t&&t.defaultView||window}return e}function n(e){return e instanceof t(e).Element||e instanceof Element}function r(e){return e instanceof t(e).HTMLElement||e instanceof HTMLElement}function o(e){return"undefined"!=typeof ShadowRoot&&(e instanceof t(e).ShadowRoot||e instanceof ShadowRoot)}var i=Math.max,a=Math.min,s=Math.round;function f(e,t){void 0===t&&(t=!1);var n=e.getBoundingClientRect(),o=1,i=1;if(r(e)&&t){var a=e.offsetHeight,f=e.offsetWidth;f>0&&(o=s(n.width)/f||1),a>0&&(i=s(n.height)/a||1)}return{width:n.width/o,height:n.height/i,top:n.top/i,right:n.right/o,bottom:n.bottom/i,left:n.left/o,x:n.left/o,y:n.top/i}}function c(e){var n=t(e);return{scrollLeft:n.pageXOffset,scrollTop:n.pageYOffset}}function p(e){return e?(e.nodeName||"").toLowerCase():null}function u(e){return((n(e)?e.ownerDocument:e.document)||window.document).documentElement}function l(e){return f(u(e)).left+c(e).scrollLeft}function d(e){return t(e).getComputedStyle(e)}function h(e){var t=d(e),n=t.overflow,r=t.overflowX,o=t.overflowY;return/auto|scroll|overlay|hidden/.test(n+o+r)}function m(e,n,o){void 0===o&&(o=!1);var i,a,d=r(n),m=r(n)&&function(e){var t=e.getBoundingClientRect(),n=s(t.width)/e.offsetWidth||1,r=s(t.height)/e.offsetHeight||1;return 1!==n||1!==r}(n),v=u(n),g=f(e,m),y={scrollLeft:0,scrollTop:0},b={x:0,y:0};return(d||!d&&!o)&&(("body"!==p(n)||h(v))&&(y=(i=n)!==t(i)&&r(i)?{scrollLeft:(a=i).scrollLeft,scrollTop:a.scrollTop}:c(i)),r(n)?((b=f(n,!0)).x+=n.clientLeft,b.y+=n.clientTop):v&&(b.x=l(v))),{x:g.left+y.scrollLeft-b.x,y:g.top+y.scrollTop-b.y,width:g.width,height:g.height}}function v(e){var t=f(e),n=e.offsetWidth,r=e.offsetHeight;return Math.abs(t.width-n)<=1&&(n=t.width),Math.abs(t.height-r)<=1&&(r=t.height),{x:e.offsetLeft,y:e.offsetTop,width:n,height:r}}function g(e){return"html"===p(e)?e:e.assignedSlot||e.parentNode||(o(e)?e.host:null)||u(e)}function y(e){return["html","body","#document"].indexOf(p(e))>=0?e.ownerDocument.body:r(e)&&h(e)?e:y(g(e))}function b(e,n){var r;void 0===n&&(n=[]);var o=y(e),i=o===(null==(r=e.ownerDocument)?void 0:r.body),a=t(o),s=i?[a].concat(a.visualViewport||[],h(o)?o:[]):o,f=n.concat(s);return i?f:f.concat(b(g(s)))}function x(e){return["table","td","th"].indexOf(p(e))>=0}function w(e){return r(e)&&"fixed"!==d(e).position?e.offsetParent:null}function O(e){for(var n=t(e),i=w(e);i&&x(i)&&"static"===d(i).position;)i=w(i);return i&&("html"===p(i)||"body"===p(i)&&"static"===d(i).position)?n:i||function(e){var t=-1!==navigator.userAgent.toLowerCase().indexOf("firefox");if(-1!==navigator.userAgent.indexOf("Trident")&&r(e)&&"fixed"===d(e).position)return null;var n=g(e);for(o(n)&&(n=n.host);r(n)&&["html","body"].indexOf(p(n))<0;){var i=d(n);if("none"!==i.transform||"none"!==i.perspective||"paint"===i.contain||-1!==["transform","perspective"].indexOf(i.willChange)||t&&"filter"===i.willChange||t&&i.filter&&"none"!==i.filter)return n;n=n.parentNode}return null}(e)||n}var j="top",E="bottom",D="right",A="left",L="auto",P=[j,E,D,A],M="start",k="end",W="viewport",B="popper",H=P.reduce((function(e,t){return e.concat([t+"-"+M,t+"-"+k])}),[]),T=[].concat(P,[L]).reduce((function(e,t){return e.concat([t,t+"-"+M,t+"-"+k])}),[]),R=["beforeRead","read","afterRead","beforeMain","main","afterMain","beforeWrite","write","afterWrite"];function S(e){var t=new Map,n=new Set,r=[];function o(e){n.add(e.name),[].concat(e.requires||[],e.requiresIfExists||[]).forEach((function(e){if(!n.has(e)){var r=t.get(e);r&&o(r)}})),r.push(e)}return e.forEach((function(e){t.set(e.name,e)})),e.forEach((function(e){n.has(e.name)||o(e)})),r}function C(e){return e.split("-")[0]}function q(e,t){var n=t.getRootNode&&t.getRootNode();if(e.contains(t))return!0;if(n&&o(n)){var r=t;do{if(r&&e.isSameNode(r))return!0;r=r.parentNode||r.host}while(r)}return!1}function V(e){return Object.assign({},e,{left:e.x,top:e.y,right:e.x+e.width,bottom:e.y+e.height})}function N(e,r){return r===W?V(function(e){var n=t(e),r=u(e),o=n.visualViewport,i=r.clientWidth,a=r.clientHeight,s=0,f=0;return o&&(i=o.width,a=o.height,/^((?!chrome|android).)*safari/i.test(navigator.userAgent)||(s=o.offsetLeft,f=o.offsetTop)),{width:i,height:a,x:s+l(e),y:f}}(e)):n(r)?function(e){var t=f(e);return t.top=t.top+e.clientTop,t.left=t.left+e.clientLeft,t.bottom=t.top+e.clientHeight,t.right=t.left+e.clientWidth,t.width=e.clientWidth,t.height=e.clientHeight,t.x=t.left,t.y=t.top,t}(r):V(function(e){var t,n=u(e),r=c(e),o=null==(t=e.ownerDocument)?void 0:t.body,a=i(n.scrollWidth,n.clientWidth,o?o.scrollWidth:0,o?o.clientWidth:0),s=i(n.scrollHeight,n.clientHeight,o?o.scrollHeight:0,o?o.clientHeight:0),f=-r.scrollLeft+l(e),p=-r.scrollTop;return"rtl"===d(o||n).direction&&(f+=i(n.clientWidth,o?o.clientWidth:0)-a),{width:a,height:s,x:f,y:p}}(u(e)))}function I(e,t,o){var s="clippingParents"===t?function(e){var t=b(g(e)),o=["absolute","fixed"].indexOf(d(e).position)>=0&&r(e)?O(e):e;return n(o)?t.filter((function(e){return n(e)&&q(e,o)&&"body"!==p(e)})):[]}(e):[].concat(t),f=[].concat(s,[o]),c=f[0],u=f.reduce((function(t,n){var r=N(e,n);return t.top=i(r.top,t.top),t.right=a(r.right,t.right),t.bottom=a(r.bottom,t.bottom),t.left=i(r.left,t.left),t}),N(e,c));return u.width=u.right-u.left,u.height=u.bottom-u.top,u.x=u.left,u.y=u.top,u}function _(e){return e.split("-")[1]}function F(e){return["top","bottom"].indexOf(e)>=0?"x":"y"}function U(e){var t,n=e.reference,r=e.element,o=e.placement,i=o?C(o):null,a=o?_(o):null,s=n.x+n.width/2-r.width/2,f=n.y+n.height/2-r.height/2;switch(i){case j:t={x:s,y:n.y-r.height};break;case E:t={x:s,y:n.y+n.height};break;case D:t={x:n.x+n.width,y:f};break;case A:t={x:n.x-r.width,y:f};break;default:t={x:n.x,y:n.y}}var c=i?F(i):null;if(null!=c){var p="y"===c?"height":"width";switch(a){case M:t[c]=t[c]-(n[p]/2-r[p]/2);break;case k:t[c]=t[c]+(n[p]/2-r[p]/2)}}return t}function z(e){return Object.assign({},{top:0,right:0,bottom:0,left:0},e)}function X(e,t){return t.reduce((function(t,n){return t[n]=e,t}),{})}function Y(e,t){void 0===t&&(t={});var r=t,o=r.placement,i=void 0===o?e.placement:o,a=r.boundary,s=void 0===a?"clippingParents":a,c=r.rootBoundary,p=void 0===c?W:c,l=r.elementContext,d=void 0===l?B:l,h=r.altBoundary,m=void 0!==h&&h,v=r.padding,g=void 0===v?0:v,y=z("number"!=typeof g?g:X(g,P)),b=d===B?"reference":B,x=e.rects.popper,w=e.elements[m?b:d],O=I(n(w)?w:w.contextElement||u(e.elements.popper),s,p),A=f(e.elements.reference),L=U({reference:A,element:x,strategy:"absolute",placement:i}),M=V(Object.assign({},x,L)),k=d===B?M:A,H={top:O.top-k.top+y.top,bottom:k.bottom-O.bottom+y.bottom,left:O.left-k.left+y.left,right:k.right-O.right+y.right},T=e.modifiersData.offset;if(d===B&&T){var R=T[i];Object.keys(H).forEach((function(e){var t=[D,E].indexOf(e)>=0?1:-1,n=[j,E].indexOf(e)>=0?"y":"x";H[e]+=R[n]*t}))}return H}var G={placement:"bottom",modifiers:[],strategy:"absolute"};function J(){for(var e=arguments.length,t=new Array(e),n=0;n<e;n++)t[n]=arguments[n];return!t.some((function(e){return!(e&&"function"==typeof e.getBoundingClientRect)}))}function K(e){void 0===e&&(e={});var t=e,r=t.defaultModifiers,o=void 0===r?[]:r,i=t.defaultOptions,a=void 0===i?G:i;return function(e,t,r){void 0===r&&(r=a);var i,s,f={placement:"bottom",orderedModifiers:[],options:Object.assign({},G,a),modifiersData:{},elements:{reference:e,popper:t},attributes:{},styles:{}},c=[],p=!1,u={state:f,setOptions:function(r){var i="function"==typeof r?r(f.options):r;l(),f.options=Object.assign({},a,f.options,i),f.scrollParents={reference:n(e)?b(e):e.contextElement?b(e.contextElement):[],popper:b(t)};var s,p,d=function(e){var t=S(e);return R.reduce((function(e,n){return e.concat(t.filter((function(e){return e.phase===n})))}),[])}((s=[].concat(o,f.options.modifiers),p=s.reduce((function(e,t){var n=e[t.name];return e[t.name]=n?Object.assign({},n,t,{options:Object.assign({},n.options,t.options),data:Object.assign({},n.data,t.data)}):t,e}),{}),Object.keys(p).map((function(e){return p[e]}))));return f.orderedModifiers=d.filter((function(e){return e.enabled})),f.orderedModifiers.forEach((function(e){var t=e.name,n=e.options,r=void 0===n?{}:n,o=e.effect;if("function"==typeof o){var i=o({state:f,name:t,instance:u,options:r}),a=function(){};c.push(i||a)}})),u.update()},forceUpdate:function(){if(!p){var e=f.elements,t=e.reference,n=e.popper;if(J(t,n)){f.rects={reference:m(t,O(n),"fixed"===f.options.strategy),popper:v(n)},f.reset=!1,f.placement=f.options.placement,f.orderedModifiers.forEach((function(e){return f.modifiersData[e.name]=Object.assign({},e.data)}));for(var r=0;r<f.orderedModifiers.length;r++)if(!0!==f.reset){var o=f.orderedModifiers[r],i=o.fn,a=o.options,s=void 0===a?{}:a,c=o.name;"function"==typeof i&&(f=i({state:f,options:s,name:c,instance:u})||f)}else f.reset=!1,r=-1}}},update:(i=function(){return new Promise((function(e){u.forceUpdate(),e(f)}))},function(){return s||(s=new Promise((function(e){Promise.resolve().then((function(){s=void 0,e(i())}))}))),s}),destroy:function(){l(),p=!0}};if(!J(e,t))return u;function l(){c.forEach((function(e){return e()})),c=[]}return u.setOptions(r).then((function(e){!p&&r.onFirstUpdate&&r.onFirstUpdate(e)})),u}}var Q={passive:!0};var Z={name:"eventListeners",enabled:!0,phase:"write",fn:function(){},effect:function(e){var n=e.state,r=e.instance,o=e.options,i=o.scroll,a=void 0===i||i,s=o.resize,f=void 0===s||s,c=t(n.elements.popper),p=[].concat(n.scrollParents.reference,n.scrollParents.popper);return a&&p.forEach((function(e){e.addEventListener("scroll",r.update,Q)})),f&&c.addEventListener("resize",r.update,Q),function(){a&&p.forEach((function(e){e.removeEventListener("scroll",r.update,Q)})),f&&c.removeEventListener("resize",r.update,Q)}},data:{}};var $={name:"popperOffsets",enabled:!0,phase:"read",fn:function(e){var t=e.state,n=e.name;t.modifiersData[n]=U({reference:t.rects.reference,element:t.rects.popper,strategy:"absolute",placement:t.placement})},data:{}},ee={top:"auto",right:"auto",bottom:"auto",left:"auto"};function te(e){var n,r=e.popper,o=e.popperRect,i=e.placement,a=e.variation,f=e.offsets,c=e.position,p=e.gpuAcceleration,l=e.adaptive,h=e.roundOffsets,m=e.isFixed,v=f.x,g=void 0===v?0:v,y=f.y,b=void 0===y?0:y,x="function"==typeof h?h({x:g,y:b}):{x:g,y:b};g=x.x,b=x.y;var w=f.hasOwnProperty("x"),L=f.hasOwnProperty("y"),P=A,M=j,W=window;if(l){var B=O(r),H="clientHeight",T="clientWidth";if(B===t(r)&&"static"!==d(B=u(r)).position&&"absolute"===c&&(H="scrollHeight",T="scrollWidth"),B=B,i===j||(i===A||i===D)&&a===k)M=E,b-=(m&&B===W&&W.visualViewport?W.visualViewport.height:B[H])-o.height,b*=p?1:-1;if(i===A||(i===j||i===E)&&a===k)P=D,g-=(m&&B===W&&W.visualViewport?W.visualViewport.width:B[T])-o.width,g*=p?1:-1}var R,S=Object.assign({position:c},l&&ee),C=!0===h?function(e){var t=e.x,n=e.y,r=window.devicePixelRatio||1;return{x:s(t*r)/r||0,y:s(n*r)/r||0}}({x:g,y:b}):{x:g,y:b};return g=C.x,b=C.y,p?Object.assign({},S,((R={})[M]=L?"0":"",R[P]=w?"0":"",R.transform=(W.devicePixelRatio||1)<=1?"translate("+g+"px, "+b+"px)":"translate3d("+g+"px, "+b+"px, 0)",R)):Object.assign({},S,((n={})[M]=L?b+"px":"",n[P]=w?g+"px":"",n.transform="",n))}var ne={name:"computeStyles",enabled:!0,phase:"beforeWrite",fn:function(e){var t=e.state,n=e.options,r=n.gpuAcceleration,o=void 0===r||r,i=n.adaptive,a=void 0===i||i,s=n.roundOffsets,f=void 0===s||s,c={placement:C(t.placement),variation:_(t.placement),popper:t.elements.popper,popperRect:t.rects.popper,gpuAcceleration:o,isFixed:"fixed"===t.options.strategy};null!=t.modifiersData.popperOffsets&&(t.styles.popper=Object.assign({},t.styles.popper,te(Object.assign({},c,{offsets:t.modifiersData.popperOffsets,position:t.options.strategy,adaptive:a,roundOffsets:f})))),null!=t.modifiersData.arrow&&(t.styles.arrow=Object.assign({},t.styles.arrow,te(Object.assign({},c,{offsets:t.modifiersData.arrow,position:"absolute",adaptive:!1,roundOffsets:f})))),t.attributes.popper=Object.assign({},t.attributes.popper,{"data-popper-placement":t.placement})},data:{}};var re={name:"applyStyles",enabled:!0,phase:"write",fn:function(e){var t=e.state;Object.keys(t.elements).forEach((function(e){var n=t.styles[e]||{},o=t.attributes[e]||{},i=t.elements[e];r(i)&&p(i)&&(Object.assign(i.style,n),Object.keys(o).forEach((function(e){var t=o[e];!1===t?i.removeAttribute(e):i.setAttribute(e,!0===t?"":t)})))}))},effect:function(e){var t=e.state,n={popper:{position:t.options.strategy,left:"0",top:"0",margin:"0"},arrow:{position:"absolute"},reference:{}};return Object.assign(t.elements.popper.style,n.popper),t.styles=n,t.elements.arrow&&Object.assign(t.elements.arrow.style,n.arrow),function(){Object.keys(t.elements).forEach((function(e){var o=t.elements[e],i=t.attributes[e]||{},a=Object.keys(t.styles.hasOwnProperty(e)?t.styles[e]:n[e]).reduce((function(e,t){return e[t]="",e}),{});r(o)&&p(o)&&(Object.assign(o.style,a),Object.keys(i).forEach((function(e){o.removeAttribute(e)})))}))}},requires:["computeStyles"]};var oe={name:"offset",enabled:!0,phase:"main",requires:["popperOffsets"],fn:function(e){var t=e.state,n=e.options,r=e.name,o=n.offset,i=void 0===o?[0,0]:o,a=T.reduce((function(e,n){return e[n]=function(e,t,n){var r=C(e),o=[A,j].indexOf(r)>=0?-1:1,i="function"==typeof n?n(Object.assign({},t,{placement:e})):n,a=i[0],s=i[1];return a=a||0,s=(s||0)*o,[A,D].indexOf(r)>=0?{x:s,y:a}:{x:a,y:s}}(n,t.rects,i),e}),{}),s=a[t.placement],f=s.x,c=s.y;null!=t.modifiersData.popperOffsets&&(t.modifiersData.popperOffsets.x+=f,t.modifiersData.popperOffsets.y+=c),t.modifiersData[r]=a}},ie={left:"right",right:"left",bottom:"top",top:"bottom"};function ae(e){return e.replace(/left|right|bottom|top/g,(function(e){return ie[e]}))}var se={start:"end",end:"start"};function fe(e){return e.replace(/start|end/g,(function(e){return se[e]}))}function ce(e,t){void 0===t&&(t={});var n=t,r=n.placement,o=n.boundary,i=n.rootBoundary,a=n.padding,s=n.flipVariations,f=n.allowedAutoPlacements,c=void 0===f?T:f,p=_(r),u=p?s?H:H.filter((function(e){return _(e)===p})):P,l=u.filter((function(e){return c.indexOf(e)>=0}));0===l.length&&(l=u);var d=l.reduce((function(t,n){return t[n]=Y(e,{placement:n,boundary:o,rootBoundary:i,padding:a})[C(n)],t}),{});return Object.keys(d).sort((function(e,t){return d[e]-d[t]}))}var pe={name:"flip",enabled:!0,phase:"main",fn:function(e){var t=e.state,n=e.options,r=e.name;if(!t.modifiersData[r]._skip){for(var o=n.mainAxis,i=void 0===o||o,a=n.altAxis,s=void 0===a||a,f=n.fallbackPlacements,c=n.padding,p=n.boundary,u=n.rootBoundary,l=n.altBoundary,d=n.flipVariations,h=void 0===d||d,m=n.allowedAutoPlacements,v=t.options.placement,g=C(v),y=f||(g===v||!h?[ae(v)]:function(e){if(C(e)===L)return[];var t=ae(e);return[fe(e),t,fe(t)]}(v)),b=[v].concat(y).reduce((function(e,n){return e.concat(C(n)===L?ce(t,{placement:n,boundary:p,rootBoundary:u,padding:c,flipVariations:h,allowedAutoPlacements:m}):n)}),[]),x=t.rects.reference,w=t.rects.popper,O=new Map,P=!0,k=b[0],W=0;W<b.length;W++){var B=b[W],H=C(B),T=_(B)===M,R=[j,E].indexOf(H)>=0,S=R?"width":"height",q=Y(t,{placement:B,boundary:p,rootBoundary:u,altBoundary:l,padding:c}),V=R?T?D:A:T?E:j;x[S]>w[S]&&(V=ae(V));var N=ae(V),I=[];if(i&&I.push(q[H]<=0),s&&I.push(q[V]<=0,q[N]<=0),I.every((function(e){return e}))){k=B,P=!1;break}O.set(B,I)}if(P)for(var F=function(e){var t=b.find((function(t){var n=O.get(t);if(n)return n.slice(0,e).every((function(e){return e}))}));if(t)return k=t,"break"},U=h?3:1;U>0;U--){if("break"===F(U))break}t.placement!==k&&(t.modifiersData[r]._skip=!0,t.placement=k,t.reset=!0)}},requiresIfExists:["offset"],data:{_skip:!1}};function ue(e,t,n){return i(e,a(t,n))}var le={name:"preventOverflow",enabled:!0,phase:"main",fn:function(e){var t=e.state,n=e.options,r=e.name,o=n.mainAxis,s=void 0===o||o,f=n.altAxis,c=void 0!==f&&f,p=n.boundary,u=n.rootBoundary,l=n.altBoundary,d=n.padding,h=n.tether,m=void 0===h||h,g=n.tetherOffset,y=void 0===g?0:g,b=Y(t,{boundary:p,rootBoundary:u,padding:d,altBoundary:l}),x=C(t.placement),w=_(t.placement),L=!w,P=F(x),k="x"===P?"y":"x",W=t.modifiersData.popperOffsets,B=t.rects.reference,H=t.rects.popper,T="function"==typeof y?y(Object.assign({},t.rects,{placement:t.placement})):y,R="number"==typeof T?{mainAxis:T,altAxis:T}:Object.assign({mainAxis:0,altAxis:0},T),S=t.modifiersData.offset?t.modifiersData.offset[t.placement]:null,q={x:0,y:0};if(W){if(s){var V,N="y"===P?j:A,I="y"===P?E:D,U="y"===P?"height":"width",z=W[P],X=z+b[N],G=z-b[I],J=m?-H[U]/2:0,K=w===M?B[U]:H[U],Q=w===M?-H[U]:-B[U],Z=t.elements.arrow,$=m&&Z?v(Z):{width:0,height:0},ee=t.modifiersData["arrow#persistent"]?t.modifiersData["arrow#persistent"].padding:{top:0,right:0,bottom:0,left:0},te=ee[N],ne=ee[I],re=ue(0,B[U],$[U]),oe=L?B[U]/2-J-re-te-R.mainAxis:K-re-te-R.mainAxis,ie=L?-B[U]/2+J+re+ne+R.mainAxis:Q+re+ne+R.mainAxis,ae=t.elements.arrow&&O(t.elements.arrow),se=ae?"y"===P?ae.clientTop||0:ae.clientLeft||0:0,fe=null!=(V=null==S?void 0:S[P])?V:0,ce=z+ie-fe,pe=ue(m?a(X,z+oe-fe-se):X,z,m?i(G,ce):G);W[P]=pe,q[P]=pe-z}if(c){var le,de="x"===P?j:A,he="x"===P?E:D,me=W[k],ve="y"===k?"height":"width",ge=me+b[de],ye=me-b[he],be=-1!==[j,A].indexOf(x),xe=null!=(le=null==S?void 0:S[k])?le:0,we=be?ge:me-B[ve]-H[ve]-xe+R.altAxis,Oe=be?me+B[ve]+H[ve]-xe-R.altAxis:ye,je=m&&be?function(e,t,n){var r=ue(e,t,n);return r>n?n:r}(we,me,Oe):ue(m?we:ge,me,m?Oe:ye);W[k]=je,q[k]=je-me}t.modifiersData[r]=q}},requiresIfExists:["offset"]};var de={name:"arrow",enabled:!0,phase:"main",fn:function(e){var t,n=e.state,r=e.name,o=e.options,i=n.elements.arrow,a=n.modifiersData.popperOffsets,s=C(n.placement),f=F(s),c=[A,D].indexOf(s)>=0?"height":"width";if(i&&a){var p=function(e,t){return z("number"!=typeof(e="function"==typeof e?e(Object.assign({},t.rects,{placement:t.placement})):e)?e:X(e,P))}(o.padding,n),u=v(i),l="y"===f?j:A,d="y"===f?E:D,h=n.rects.reference[c]+n.rects.reference[f]-a[f]-n.rects.popper[c],m=a[f]-n.rects.reference[f],g=O(i),y=g?"y"===f?g.clientHeight||0:g.clientWidth||0:0,b=h/2-m/2,x=p[l],w=y-u[c]-p[d],L=y/2-u[c]/2+b,M=ue(x,L,w),k=f;n.modifiersData[r]=((t={})[k]=M,t.centerOffset=M-L,t)}},effect:function(e){var t=e.state,n=e.options.element,r=void 0===n?"[data-popper-arrow]":n;null!=r&&("string"!=typeof r||(r=t.elements.popper.querySelector(r)))&&q(t.elements.popper,r)&&(t.elements.arrow=r)},requires:["popperOffsets"],requiresIfExists:["preventOverflow"]};function he(e,t,n){return void 0===n&&(n={x:0,y:0}),{top:e.top-t.height-n.y,right:e.right-t.width+n.x,bottom:e.bottom-t.height+n.y,left:e.left-t.width-n.x}}function me(e){return[j,D,E,A].some((function(t){return e[t]>=0}))}var ve={name:"hide",enabled:!0,phase:"main",requiresIfExists:["preventOverflow"],fn:function(e){var t=e.state,n=e.name,r=t.rects.reference,o=t.rects.popper,i=t.modifiersData.preventOverflow,a=Y(t,{elementContext:"reference"}),s=Y(t,{altBoundary:!0}),f=he(a,r),c=he(s,o,i),p=me(f),u=me(c);t.modifiersData[n]={referenceClippingOffsets:f,popperEscapeOffsets:c,isReferenceHidden:p,hasPopperEscaped:u},t.attributes.popper=Object.assign({},t.attributes.popper,{"data-popper-reference-hidden":p,"data-popper-escaped":u})}},ge=K({defaultModifiers:[Z,$,ne,re]}),ye=[Z,$,ne,re,oe,pe,le,de,ve],be=K({defaultModifiers:ye});e.applyStyles=re,e.arrow=de,e.computeStyles=ne,e.createPopper=be,e.createPopperLite=ge,e.defaultModifiers=ye,e.detectOverflow=Y,e.eventListeners=Z,e.flip=pe,e.hide=ve,e.offset=oe,e.popperGenerator=K,e.popperOffsets=$,e.preventOverflow=le,Object.defineProperty(e,"__esModule",{value:!0})}));

/*!
 * tippy.js v6.3.7 - MIT License - https://github.com/atomiks/tippyjs
 */
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e(require("@popperjs/core")):"function"==typeof define&&define.amd?define(["@popperjs/core"],e):(t=t||self).tippy=e(t.Popper)}(this,(function(t){"use strict";var e="undefined"!=typeof window&&"undefined"!=typeof document,n=!!e&&!!window.msCrypto,r={passive:!0,capture:!0},o=function(){return document.body};function i(t,e,n){if(Array.isArray(t)){var r=t[e];return null==r?Array.isArray(n)?n[e]:n:r}return t}function a(t,e){var n={}.toString.call(t);return 0===n.indexOf("[object")&&n.indexOf(e+"]")>-1}function s(t,e){return"function"==typeof t?t.apply(void 0,e):t}function u(t,e){return 0===e?t:function(r){clearTimeout(n),n=setTimeout((function(){t(r)}),e)};var n}function p(t,e){var n=Object.assign({},t);return e.forEach((function(t){delete n[t]})),n}function c(t){return[].concat(t)}function f(t,e){-1===t.indexOf(e)&&t.push(e)}function l(t){return t.split("-")[0]}function d(t){return[].slice.call(t)}function v(t){return Object.keys(t).reduce((function(e,n){return void 0!==t[n]&&(e[n]=t[n]),e}),{})}function m(){return document.createElement("div")}function g(t){return["Element","Fragment"].some((function(e){return a(t,e)}))}function h(t){return a(t,"MouseEvent")}function b(t){return!(!t||!t._tippy||t._tippy.reference!==t)}function y(t){return g(t)?[t]:function(t){return a(t,"NodeList")}(t)?d(t):Array.isArray(t)?t:d(document.querySelectorAll(t))}function w(t,e){t.forEach((function(t){t&&(t.style.transitionDuration=e+"ms")}))}function x(t,e){t.forEach((function(t){t&&t.setAttribute("data-state",e)}))}function E(t){var e,n=c(t)[0];return null!=n&&null!=(e=n.ownerDocument)&&e.body?n.ownerDocument:document}function O(t,e,n){var r=e+"EventListener";["transitionend","webkitTransitionEnd"].forEach((function(e){t[r](e,n)}))}function C(t,e){for(var n=e;n;){var r;if(t.contains(n))return!0;n=null==n.getRootNode||null==(r=n.getRootNode())?void 0:r.host}return!1}var T={isTouch:!1},A=0;function L(){T.isTouch||(T.isTouch=!0,window.performance&&document.addEventListener("mousemove",D))}function D(){var t=performance.now();t-A<20&&(T.isTouch=!1,document.removeEventListener("mousemove",D)),A=t}function k(){var t=document.activeElement;if(b(t)){var e=t._tippy;t.blur&&!e.state.isVisible&&t.blur()}}var R=Object.assign({appendTo:o,aria:{content:"auto",expanded:"auto"},delay:0,duration:[300,250],getReferenceClientRect:null,hideOnClick:!0,ignoreAttributes:!1,interactive:!1,interactiveBorder:2,interactiveDebounce:0,moveTransition:"",offset:[0,10],onAfterUpdate:function(){},onBeforeUpdate:function(){},onCreate:function(){},onDestroy:function(){},onHidden:function(){},onHide:function(){},onMount:function(){},onShow:function(){},onShown:function(){},onTrigger:function(){},onUntrigger:function(){},onClickOutside:function(){},placement:"top",plugins:[],popperOptions:{},render:null,showOnCreate:!1,touch:!0,trigger:"mouseenter focus",triggerTarget:null},{animateFill:!1,followCursor:!1,inlinePositioning:!1,sticky:!1},{allowHTML:!1,animation:"fade",arrow:!0,content:"",inertia:!1,maxWidth:350,role:"tooltip",theme:"",zIndex:9999}),P=Object.keys(R);function j(t){var e=(t.plugins||[]).reduce((function(e,n){var r,o=n.name,i=n.defaultValue;o&&(e[o]=void 0!==t[o]?t[o]:null!=(r=R[o])?r:i);return e}),{});return Object.assign({},t,e)}function M(t,e){var n=Object.assign({},e,{content:s(e.content,[t])},e.ignoreAttributes?{}:function(t,e){return(e?Object.keys(j(Object.assign({},R,{plugins:e}))):P).reduce((function(e,n){var r=(t.getAttribute("data-tippy-"+n)||"").trim();if(!r)return e;if("content"===n)e[n]=r;else try{e[n]=JSON.parse(r)}catch(t){e[n]=r}return e}),{})}(t,e.plugins));return n.aria=Object.assign({},R.aria,n.aria),n.aria={expanded:"auto"===n.aria.expanded?e.interactive:n.aria.expanded,content:"auto"===n.aria.content?e.interactive?null:"describedby":n.aria.content},n}function V(t,e){t.innerHTML=e}function I(t){var e=m();return!0===t?e.className="tippy-arrow":(e.className="tippy-svg-arrow",g(t)?e.appendChild(t):V(e,t)),e}function S(t,e){g(e.content)?(V(t,""),t.appendChild(e.content)):"function"!=typeof e.content&&(e.allowHTML?V(t,e.content):t.textContent=e.content)}function B(t){var e=t.firstElementChild,n=d(e.children);return{box:e,content:n.find((function(t){return t.classList.contains("tippy-content")})),arrow:n.find((function(t){return t.classList.contains("tippy-arrow")||t.classList.contains("tippy-svg-arrow")})),backdrop:n.find((function(t){return t.classList.contains("tippy-backdrop")}))}}function N(t){var e=m(),n=m();n.className="tippy-box",n.setAttribute("data-state","hidden"),n.setAttribute("tabindex","-1");var r=m();function o(n,r){var o=B(e),i=o.box,a=o.content,s=o.arrow;r.theme?i.setAttribute("data-theme",r.theme):i.removeAttribute("data-theme"),"string"==typeof r.animation?i.setAttribute("data-animation",r.animation):i.removeAttribute("data-animation"),r.inertia?i.setAttribute("data-inertia",""):i.removeAttribute("data-inertia"),i.style.maxWidth="number"==typeof r.maxWidth?r.maxWidth+"px":r.maxWidth,r.role?i.setAttribute("role",r.role):i.removeAttribute("role"),n.content===r.content&&n.allowHTML===r.allowHTML||S(a,t.props),r.arrow?s?n.arrow!==r.arrow&&(i.removeChild(s),i.appendChild(I(r.arrow))):i.appendChild(I(r.arrow)):s&&i.removeChild(s)}return r.className="tippy-content",r.setAttribute("data-state","hidden"),S(r,t.props),e.appendChild(n),n.appendChild(r),o(t.props,t.props),{popper:e,onUpdate:o}}N.$$tippy=!0;var H=1,U=[],_=[];function z(e,a){var p,g,b,y,A,L,D,k,P=M(e,Object.assign({},R,j(v(a)))),V=!1,I=!1,S=!1,N=!1,z=[],F=u(wt,P.interactiveDebounce),W=H++,X=(k=P.plugins).filter((function(t,e){return k.indexOf(t)===e})),Y={id:W,reference:e,popper:m(),popperInstance:null,props:P,state:{isEnabled:!0,isVisible:!1,isDestroyed:!1,isMounted:!1,isShown:!1},plugins:X,clearDelayTimeouts:function(){clearTimeout(p),clearTimeout(g),cancelAnimationFrame(b)},setProps:function(t){if(Y.state.isDestroyed)return;at("onBeforeUpdate",[Y,t]),bt();var n=Y.props,r=M(e,Object.assign({},n,v(t),{ignoreAttributes:!0}));Y.props=r,ht(),n.interactiveDebounce!==r.interactiveDebounce&&(pt(),F=u(wt,r.interactiveDebounce));n.triggerTarget&&!r.triggerTarget?c(n.triggerTarget).forEach((function(t){t.removeAttribute("aria-expanded")})):r.triggerTarget&&e.removeAttribute("aria-expanded");ut(),it(),J&&J(n,r);Y.popperInstance&&(Ct(),At().forEach((function(t){requestAnimationFrame(t._tippy.popperInstance.forceUpdate)})));at("onAfterUpdate",[Y,t])},setContent:function(t){Y.setProps({content:t})},show:function(){var t=Y.state.isVisible,e=Y.state.isDestroyed,n=!Y.state.isEnabled,r=T.isTouch&&!Y.props.touch,a=i(Y.props.duration,0,R.duration);if(t||e||n||r)return;if(et().hasAttribute("disabled"))return;if(at("onShow",[Y],!1),!1===Y.props.onShow(Y))return;Y.state.isVisible=!0,tt()&&($.style.visibility="visible");it(),dt(),Y.state.isMounted||($.style.transition="none");if(tt()){var u=rt(),p=u.box,c=u.content;w([p,c],0)}L=function(){var t;if(Y.state.isVisible&&!N){if(N=!0,$.offsetHeight,$.style.transition=Y.props.moveTransition,tt()&&Y.props.animation){var e=rt(),n=e.box,r=e.content;w([n,r],a),x([n,r],"visible")}st(),ut(),f(_,Y),null==(t=Y.popperInstance)||t.forceUpdate(),at("onMount",[Y]),Y.props.animation&&tt()&&function(t,e){mt(t,e)}(a,(function(){Y.state.isShown=!0,at("onShown",[Y])}))}},function(){var t,e=Y.props.appendTo,n=et();t=Y.props.interactive&&e===o||"parent"===e?n.parentNode:s(e,[n]);t.contains($)||t.appendChild($);Y.state.isMounted=!0,Ct()}()},hide:function(){var t=!Y.state.isVisible,e=Y.state.isDestroyed,n=!Y.state.isEnabled,r=i(Y.props.duration,1,R.duration);if(t||e||n)return;if(at("onHide",[Y],!1),!1===Y.props.onHide(Y))return;Y.state.isVisible=!1,Y.state.isShown=!1,N=!1,V=!1,tt()&&($.style.visibility="hidden");if(pt(),vt(),it(!0),tt()){var o=rt(),a=o.box,s=o.content;Y.props.animation&&(w([a,s],r),x([a,s],"hidden"))}st(),ut(),Y.props.animation?tt()&&function(t,e){mt(t,(function(){!Y.state.isVisible&&$.parentNode&&$.parentNode.contains($)&&e()}))}(r,Y.unmount):Y.unmount()},hideWithInteractivity:function(t){nt().addEventListener("mousemove",F),f(U,F),F(t)},enable:function(){Y.state.isEnabled=!0},disable:function(){Y.hide(),Y.state.isEnabled=!1},unmount:function(){Y.state.isVisible&&Y.hide();if(!Y.state.isMounted)return;Tt(),At().forEach((function(t){t._tippy.unmount()})),$.parentNode&&$.parentNode.removeChild($);_=_.filter((function(t){return t!==Y})),Y.state.isMounted=!1,at("onHidden",[Y])},destroy:function(){if(Y.state.isDestroyed)return;Y.clearDelayTimeouts(),Y.unmount(),bt(),delete e._tippy,Y.state.isDestroyed=!0,at("onDestroy",[Y])}};if(!P.render)return Y;var q=P.render(Y),$=q.popper,J=q.onUpdate;$.setAttribute("data-tippy-root",""),$.id="tippy-"+Y.id,Y.popper=$,e._tippy=Y,$._tippy=Y;var G=X.map((function(t){return t.fn(Y)})),K=e.hasAttribute("aria-expanded");return ht(),ut(),it(),at("onCreate",[Y]),P.showOnCreate&&Lt(),$.addEventListener("mouseenter",(function(){Y.props.interactive&&Y.state.isVisible&&Y.clearDelayTimeouts()})),$.addEventListener("mouseleave",(function(){Y.props.interactive&&Y.props.trigger.indexOf("mouseenter")>=0&&nt().addEventListener("mousemove",F)})),Y;function Q(){var t=Y.props.touch;return Array.isArray(t)?t:[t,0]}function Z(){return"hold"===Q()[0]}function tt(){var t;return!(null==(t=Y.props.render)||!t.$$tippy)}function et(){return D||e}function nt(){var t=et().parentNode;return t?E(t):document}function rt(){return B($)}function ot(t){return Y.state.isMounted&&!Y.state.isVisible||T.isTouch||y&&"focus"===y.type?0:i(Y.props.delay,t?0:1,R.delay)}function it(t){void 0===t&&(t=!1),$.style.pointerEvents=Y.props.interactive&&!t?"":"none",$.style.zIndex=""+Y.props.zIndex}function at(t,e,n){var r;(void 0===n&&(n=!0),G.forEach((function(n){n[t]&&n[t].apply(n,e)})),n)&&(r=Y.props)[t].apply(r,e)}function st(){var t=Y.props.aria;if(t.content){var n="aria-"+t.content,r=$.id;c(Y.props.triggerTarget||e).forEach((function(t){var e=t.getAttribute(n);if(Y.state.isVisible)t.setAttribute(n,e?e+" "+r:r);else{var o=e&&e.replace(r,"").trim();o?t.setAttribute(n,o):t.removeAttribute(n)}}))}}function ut(){!K&&Y.props.aria.expanded&&c(Y.props.triggerTarget||e).forEach((function(t){Y.props.interactive?t.setAttribute("aria-expanded",Y.state.isVisible&&t===et()?"true":"false"):t.removeAttribute("aria-expanded")}))}function pt(){nt().removeEventListener("mousemove",F),U=U.filter((function(t){return t!==F}))}function ct(t){if(!T.isTouch||!S&&"mousedown"!==t.type){var n=t.composedPath&&t.composedPath()[0]||t.target;if(!Y.props.interactive||!C($,n)){if(c(Y.props.triggerTarget||e).some((function(t){return C(t,n)}))){if(T.isTouch)return;if(Y.state.isVisible&&Y.props.trigger.indexOf("click")>=0)return}else at("onClickOutside",[Y,t]);!0===Y.props.hideOnClick&&(Y.clearDelayTimeouts(),Y.hide(),I=!0,setTimeout((function(){I=!1})),Y.state.isMounted||vt())}}}function ft(){S=!0}function lt(){S=!1}function dt(){var t=nt();t.addEventListener("mousedown",ct,!0),t.addEventListener("touchend",ct,r),t.addEventListener("touchstart",lt,r),t.addEventListener("touchmove",ft,r)}function vt(){var t=nt();t.removeEventListener("mousedown",ct,!0),t.removeEventListener("touchend",ct,r),t.removeEventListener("touchstart",lt,r),t.removeEventListener("touchmove",ft,r)}function mt(t,e){var n=rt().box;function r(t){t.target===n&&(O(n,"remove",r),e())}if(0===t)return e();O(n,"remove",A),O(n,"add",r),A=r}function gt(t,n,r){void 0===r&&(r=!1),c(Y.props.triggerTarget||e).forEach((function(e){e.addEventListener(t,n,r),z.push({node:e,eventType:t,handler:n,options:r})}))}function ht(){var t;Z()&&(gt("touchstart",yt,{passive:!0}),gt("touchend",xt,{passive:!0})),(t=Y.props.trigger,t.split(/\s+/).filter(Boolean)).forEach((function(t){if("manual"!==t)switch(gt(t,yt),t){case"mouseenter":gt("mouseleave",xt);break;case"focus":gt(n?"focusout":"blur",Et);break;case"focusin":gt("focusout",Et)}}))}function bt(){z.forEach((function(t){var e=t.node,n=t.eventType,r=t.handler,o=t.options;e.removeEventListener(n,r,o)})),z=[]}function yt(t){var e,n=!1;if(Y.state.isEnabled&&!Ot(t)&&!I){var r="focus"===(null==(e=y)?void 0:e.type);y=t,D=t.currentTarget,ut(),!Y.state.isVisible&&h(t)&&U.forEach((function(e){return e(t)})),"click"===t.type&&(Y.props.trigger.indexOf("mouseenter")<0||V)&&!1!==Y.props.hideOnClick&&Y.state.isVisible?n=!0:Lt(t),"click"===t.type&&(V=!n),n&&!r&&Dt(t)}}function wt(t){var e=t.target,n=et().contains(e)||$.contains(e);"mousemove"===t.type&&n||function(t,e){var n=e.clientX,r=e.clientY;return t.every((function(t){var e=t.popperRect,o=t.popperState,i=t.props.interactiveBorder,a=l(o.placement),s=o.modifiersData.offset;if(!s)return!0;var u="bottom"===a?s.top.y:0,p="top"===a?s.bottom.y:0,c="right"===a?s.left.x:0,f="left"===a?s.right.x:0,d=e.top-r+u>i,v=r-e.bottom-p>i,m=e.left-n+c>i,g=n-e.right-f>i;return d||v||m||g}))}(At().concat($).map((function(t){var e,n=null==(e=t._tippy.popperInstance)?void 0:e.state;return n?{popperRect:t.getBoundingClientRect(),popperState:n,props:P}:null})).filter(Boolean),t)&&(pt(),Dt(t))}function xt(t){Ot(t)||Y.props.trigger.indexOf("click")>=0&&V||(Y.props.interactive?Y.hideWithInteractivity(t):Dt(t))}function Et(t){Y.props.trigger.indexOf("focusin")<0&&t.target!==et()||Y.props.interactive&&t.relatedTarget&&$.contains(t.relatedTarget)||Dt(t)}function Ot(t){return!!T.isTouch&&Z()!==t.type.indexOf("touch")>=0}function Ct(){Tt();var n=Y.props,r=n.popperOptions,o=n.placement,i=n.offset,a=n.getReferenceClientRect,s=n.moveTransition,u=tt()?B($).arrow:null,p=a?{getBoundingClientRect:a,contextElement:a.contextElement||et()}:e,c=[{name:"offset",options:{offset:i}},{name:"preventOverflow",options:{padding:{top:2,bottom:2,left:5,right:5}}},{name:"flip",options:{padding:5}},{name:"computeStyles",options:{adaptive:!s}},{name:"$$tippy",enabled:!0,phase:"beforeWrite",requires:["computeStyles"],fn:function(t){var e=t.state;if(tt()){var n=rt().box;["placement","reference-hidden","escaped"].forEach((function(t){"placement"===t?n.setAttribute("data-placement",e.placement):e.attributes.popper["data-popper-"+t]?n.setAttribute("data-"+t,""):n.removeAttribute("data-"+t)})),e.attributes.popper={}}}}];tt()&&u&&c.push({name:"arrow",options:{element:u,padding:3}}),c.push.apply(c,(null==r?void 0:r.modifiers)||[]),Y.popperInstance=t.createPopper(p,$,Object.assign({},r,{placement:o,onFirstUpdate:L,modifiers:c}))}function Tt(){Y.popperInstance&&(Y.popperInstance.destroy(),Y.popperInstance=null)}function At(){return d($.querySelectorAll("[data-tippy-root]"))}function Lt(t){Y.clearDelayTimeouts(),t&&at("onTrigger",[Y,t]),dt();var e=ot(!0),n=Q(),r=n[0],o=n[1];T.isTouch&&"hold"===r&&o&&(e=o),e?p=setTimeout((function(){Y.show()}),e):Y.show()}function Dt(t){if(Y.clearDelayTimeouts(),at("onUntrigger",[Y,t]),Y.state.isVisible){if(!(Y.props.trigger.indexOf("mouseenter")>=0&&Y.props.trigger.indexOf("click")>=0&&["mouseleave","mousemove"].indexOf(t.type)>=0&&V)){var e=ot(!1);e?g=setTimeout((function(){Y.state.isVisible&&Y.hide()}),e):b=requestAnimationFrame((function(){Y.hide()}))}}else vt()}}function F(t,e){void 0===e&&(e={});var n=R.plugins.concat(e.plugins||[]);document.addEventListener("touchstart",L,r),window.addEventListener("blur",k);var o=Object.assign({},e,{plugins:n}),i=y(t).reduce((function(t,e){var n=e&&z(e,o);return n&&t.push(n),t}),[]);return g(t)?i[0]:i}F.defaultProps=R,F.setDefaultProps=function(t){Object.keys(t).forEach((function(e){R[e]=t[e]}))},F.currentInput=T;var W=Object.assign({},t.applyStyles,{effect:function(t){var e=t.state,n={popper:{position:e.options.strategy,left:"0",top:"0",margin:"0"},arrow:{position:"absolute"},reference:{}};Object.assign(e.elements.popper.style,n.popper),e.styles=n,e.elements.arrow&&Object.assign(e.elements.arrow.style,n.arrow)}}),X={mouseover:"mouseenter",focusin:"focus",click:"click"};var Y={name:"animateFill",defaultValue:!1,fn:function(t){var e;if(null==(e=t.props.render)||!e.$$tippy)return{};var n=B(t.popper),r=n.box,o=n.content,i=t.props.animateFill?function(){var t=m();return t.className="tippy-backdrop",x([t],"hidden"),t}():null;return{onCreate:function(){i&&(r.insertBefore(i,r.firstElementChild),r.setAttribute("data-animatefill",""),r.style.overflow="hidden",t.setProps({arrow:!1,animation:"shift-away"}))},onMount:function(){if(i){var t=r.style.transitionDuration,e=Number(t.replace("ms",""));o.style.transitionDelay=Math.round(e/10)+"ms",i.style.transitionDuration=t,x([i],"visible")}},onShow:function(){i&&(i.style.transitionDuration="0ms")},onHide:function(){i&&x([i],"hidden")}}}};var q={clientX:0,clientY:0},$=[];function J(t){var e=t.clientX,n=t.clientY;q={clientX:e,clientY:n}}var G={name:"followCursor",defaultValue:!1,fn:function(t){var e=t.reference,n=E(t.props.triggerTarget||e),r=!1,o=!1,i=!0,a=t.props;function s(){return"initial"===t.props.followCursor&&t.state.isVisible}function u(){n.addEventListener("mousemove",f)}function p(){n.removeEventListener("mousemove",f)}function c(){r=!0,t.setProps({getReferenceClientRect:null}),r=!1}function f(n){var r=!n.target||e.contains(n.target),o=t.props.followCursor,i=n.clientX,a=n.clientY,s=e.getBoundingClientRect(),u=i-s.left,p=a-s.top;!r&&t.props.interactive||t.setProps({getReferenceClientRect:function(){var t=e.getBoundingClientRect(),n=i,r=a;"initial"===o&&(n=t.left+u,r=t.top+p);var s="horizontal"===o?t.top:r,c="vertical"===o?t.right:n,f="horizontal"===o?t.bottom:r,l="vertical"===o?t.left:n;return{width:c-l,height:f-s,top:s,right:c,bottom:f,left:l}}})}function l(){t.props.followCursor&&($.push({instance:t,doc:n}),function(t){t.addEventListener("mousemove",J)}(n))}function d(){0===($=$.filter((function(e){return e.instance!==t}))).filter((function(t){return t.doc===n})).length&&function(t){t.removeEventListener("mousemove",J)}(n)}return{onCreate:l,onDestroy:d,onBeforeUpdate:function(){a=t.props},onAfterUpdate:function(e,n){var i=n.followCursor;r||void 0!==i&&a.followCursor!==i&&(d(),i?(l(),!t.state.isMounted||o||s()||u()):(p(),c()))},onMount:function(){t.props.followCursor&&!o&&(i&&(f(q),i=!1),s()||u())},onTrigger:function(t,e){h(e)&&(q={clientX:e.clientX,clientY:e.clientY}),o="focus"===e.type},onHidden:function(){t.props.followCursor&&(c(),p(),i=!0)}}}};var K={name:"inlinePositioning",defaultValue:!1,fn:function(t){var e,n=t.reference;var r=-1,o=!1,i=[],a={name:"tippyInlinePositioning",enabled:!0,phase:"afterWrite",fn:function(o){var a=o.state;t.props.inlinePositioning&&(-1!==i.indexOf(a.placement)&&(i=[]),e!==a.placement&&-1===i.indexOf(a.placement)&&(i.push(a.placement),t.setProps({getReferenceClientRect:function(){return function(t){return function(t,e,n,r){if(n.length<2||null===t)return e;if(2===n.length&&r>=0&&n[0].left>n[1].right)return n[r]||e;switch(t){case"top":case"bottom":var o=n[0],i=n[n.length-1],a="top"===t,s=o.top,u=i.bottom,p=a?o.left:i.left,c=a?o.right:i.right;return{top:s,bottom:u,left:p,right:c,width:c-p,height:u-s};case"left":case"right":var f=Math.min.apply(Math,n.map((function(t){return t.left}))),l=Math.max.apply(Math,n.map((function(t){return t.right}))),d=n.filter((function(e){return"left"===t?e.left===f:e.right===l})),v=d[0].top,m=d[d.length-1].bottom;return{top:v,bottom:m,left:f,right:l,width:l-f,height:m-v};default:return e}}(l(t),n.getBoundingClientRect(),d(n.getClientRects()),r)}(a.placement)}})),e=a.placement)}};function s(){var e;o||(e=function(t,e){var n;return{popperOptions:Object.assign({},t.popperOptions,{modifiers:[].concat(((null==(n=t.popperOptions)?void 0:n.modifiers)||[]).filter((function(t){return t.name!==e.name})),[e])})}}(t.props,a),o=!0,t.setProps(e),o=!1)}return{onCreate:s,onAfterUpdate:s,onTrigger:function(e,n){if(h(n)){var o=d(t.reference.getClientRects()),i=o.find((function(t){return t.left-2<=n.clientX&&t.right+2>=n.clientX&&t.top-2<=n.clientY&&t.bottom+2>=n.clientY})),a=o.indexOf(i);r=a>-1?a:r}},onHidden:function(){r=-1}}}};var Q={name:"sticky",defaultValue:!1,fn:function(t){var e=t.reference,n=t.popper;function r(e){return!0===t.props.sticky||t.props.sticky===e}var o=null,i=null;function a(){var s=r("reference")?(t.popperInstance?t.popperInstance.state.elements.reference:e).getBoundingClientRect():null,u=r("popper")?n.getBoundingClientRect():null;(s&&Z(o,s)||u&&Z(i,u))&&t.popperInstance&&t.popperInstance.update(),o=s,i=u,t.state.isMounted&&requestAnimationFrame(a)}return{onMount:function(){t.props.sticky&&a()}}}};function Z(t,e){return!t||!e||(t.top!==e.top||t.right!==e.right||t.bottom!==e.bottom||t.left!==e.left)}return e&&function(t){var e=document.createElement("style");e.textContent=t,e.setAttribute("data-tippy-stylesheet","");var n=document.head,r=document.querySelector("head>style,head>link");r?n.insertBefore(e,r):n.appendChild(e)}('.tippy-box[data-animation=fade][data-state=hidden]{opacity:0}[data-tippy-root]{max-width:calc(100vw - 10px)}.tippy-box{position:relative;background-color:#333;color:#fff;border-radius:4px;font-size:14px;line-height:1.4;white-space:normal;outline:0;transition-property:transform,visibility,opacity}.tippy-box[data-placement^=top]>.tippy-arrow{bottom:0}.tippy-box[data-placement^=top]>.tippy-arrow:before{bottom:-7px;left:0;border-width:8px 8px 0;border-top-color:initial;transform-origin:center top}.tippy-box[data-placement^=bottom]>.tippy-arrow{top:0}.tippy-box[data-placement^=bottom]>.tippy-arrow:before{top:-7px;left:0;border-width:0 8px 8px;border-bottom-color:initial;transform-origin:center bottom}.tippy-box[data-placement^=left]>.tippy-arrow{right:0}.tippy-box[data-placement^=left]>.tippy-arrow:before{border-width:8px 0 8px 8px;border-left-color:initial;right:-7px;transform-origin:center left}.tippy-box[data-placement^=right]>.tippy-arrow{left:0}.tippy-box[data-placement^=right]>.tippy-arrow:before{left:-7px;border-width:8px 8px 8px 0;border-right-color:initial;transform-origin:center right}.tippy-box[data-inertia][data-state=visible]{transition-timing-function:cubic-bezier(.54,1.5,.38,1.11)}.tippy-arrow{width:16px;height:16px;color:#333}.tippy-arrow:before{content:"";position:absolute;border-color:transparent;border-style:solid}.tippy-content{position:relative;padding:5px 9px;z-index:1}'),F.setDefaultProps({plugins:[Y,G,K,Q],render:N}),F.createSingleton=function(t,e){var n;void 0===e&&(e={});var r,o=t,i=[],a=[],s=e.overrides,u=[],f=!1;function l(){a=o.map((function(t){return c(t.props.triggerTarget||t.reference)})).reduce((function(t,e){return t.concat(e)}),[])}function d(){i=o.map((function(t){return t.reference}))}function v(t){o.forEach((function(e){t?e.enable():e.disable()}))}function g(t){return o.map((function(e){var n=e.setProps;return e.setProps=function(o){n(o),e.reference===r&&t.setProps(o)},function(){e.setProps=n}}))}function h(t,e){var n=a.indexOf(e);if(e!==r){r=e;var u=(s||[]).concat("content").reduce((function(t,e){return t[e]=o[n].props[e],t}),{});t.setProps(Object.assign({},u,{getReferenceClientRect:"function"==typeof u.getReferenceClientRect?u.getReferenceClientRect:function(){var t;return null==(t=i[n])?void 0:t.getBoundingClientRect()}}))}}v(!1),d(),l();var b={fn:function(){return{onDestroy:function(){v(!0)},onHidden:function(){r=null},onClickOutside:function(t){t.props.showOnCreate&&!f&&(f=!0,r=null)},onShow:function(t){t.props.showOnCreate&&!f&&(f=!0,h(t,i[0]))},onTrigger:function(t,e){h(t,e.currentTarget)}}}},y=F(m(),Object.assign({},p(e,["overrides"]),{plugins:[b].concat(e.plugins||[]),triggerTarget:a,popperOptions:Object.assign({},e.popperOptions,{modifiers:[].concat((null==(n=e.popperOptions)?void 0:n.modifiers)||[],[W])})})),w=y.show;y.show=function(t){if(w(),!r&&null==t)return h(y,i[0]);if(!r||null!=t){if("number"==typeof t)return i[t]&&h(y,i[t]);if(o.indexOf(t)>=0){var e=t.reference;return h(y,e)}return i.indexOf(t)>=0?h(y,t):void 0}},y.showNext=function(){var t=i[0];if(!r)return y.show(0);var e=i.indexOf(r);y.show(i[e+1]||t)},y.showPrevious=function(){var t=i[i.length-1];if(!r)return y.show(t);var e=i.indexOf(r),n=i[e-1]||t;y.show(n)};var x=y.setProps;return y.setProps=function(t){s=t.overrides||s,x(t)},y.setInstances=function(t){v(!0),u.forEach((function(t){return t()})),o=t,v(!1),d(),l(),u=g(y),y.setProps({triggerTarget:a})},u=g(y),y},F.delegate=function(t,e){var n=[],o=[],i=!1,a=e.target,s=p(e,["target"]),u=Object.assign({},s,{trigger:"manual",touch:!1}),f=Object.assign({touch:R.touch},s,{showOnCreate:!0}),l=F(t,u);function d(t){if(t.target&&!i){var n=t.target.closest(a);if(n){var r=n.getAttribute("data-tippy-trigger")||e.trigger||R.trigger;if(!n._tippy&&!("touchstart"===t.type&&"boolean"==typeof f.touch||"touchstart"!==t.type&&r.indexOf(X[t.type])<0)){var s=F(n,f);s&&(o=o.concat(s))}}}}function v(t,e,r,o){void 0===o&&(o=!1),t.addEventListener(e,r,o),n.push({node:t,eventType:e,handler:r,options:o})}return c(l).forEach((function(t){var e=t.destroy,a=t.enable,s=t.disable;t.destroy=function(t){void 0===t&&(t=!0),t&&o.forEach((function(t){t.destroy()})),o=[],n.forEach((function(t){var e=t.node,n=t.eventType,r=t.handler,o=t.options;e.removeEventListener(n,r,o)})),n=[],e()},t.enable=function(){a(),o.forEach((function(t){return t.enable()})),i=!1},t.disable=function(){s(),o.forEach((function(t){return t.disable()})),i=!0},function(t){var e=t.reference;v(e,"touchstart",d,r),v(e,"mouseover",d),v(e,"focusin",d),v(e,"click",d)}(t)})),l},F.hideAll=function(t){var e=void 0===t?{}:t,n=e.exclude,r=e.duration;_.forEach((function(t){var e=!1;if(n&&(e=b(n)?t.reference===n:t.popper===n.popper),!e){var o=t.props.duration;t.setProps({duration:r}),t.hide(),t.state.isDestroyed||t.setProps({duration:o})}}))},F.roundArrow='<svg width="16" height="6" xmlns="http://www.w3.org/2000/svg"><path d="M0 6s1.796-.013 4.67-3.615C5.851.9 6.93.006 8 0c1.07-.006 2.148.887 3.343 2.385C14.233 6.005 16 6 16 6H0z"></svg>',F}));

/**
 * Selectize (v0.15.2)
 * https://selectize.dev
 */
(function(root,factory){if(typeof define==="function"&&define.amd){define(["jquery"],factory)}else if(typeof module==="object"&&typeof module.exports==="object"){module.exports=factory(require("jquery"))}else{root.Selectize=factory(root.jQuery)}})(this,function($){"use strict";var highlight=function($element,pattern){if(typeof pattern==="string"&&!pattern.length)return;var regex=typeof pattern==="string"?new RegExp(pattern,"i"):pattern;var highlight=function(node){var skip=0;if(node.nodeType===3){var pos=node.data.search(regex);if(pos>=0&&node.data.length>0){var match=node.data.match(regex);var spannode=document.createElement("span");spannode.className="highlight";var middlebit=node.splitText(pos);var endbit=middlebit.splitText(match[0].length);var middleclone=middlebit.cloneNode(true);spannode.appendChild(middleclone);middlebit.parentNode.replaceChild(spannode,middlebit);skip=1}}else if(node.nodeType===1&&node.childNodes&&!/(script|style)/i.test(node.tagName)&&(node.className!=="highlight"||node.tagName!=="SPAN")){for(var i=0;i<node.childNodes.length;++i){i+=highlight(node.childNodes[i])}}return skip};return $element.each(function(){highlight(this)})};$.fn.removeHighlight=function(){return this.find("span.highlight").each(function(){this.parentNode.firstChild.nodeName;var parent=this.parentNode;parent.replaceChild(this.firstChild,this);parent.normalize()}).end()};var MicroEvent=function(){};MicroEvent.prototype={on:function(event,fct){this._events=this._events||{};this._events[event]=this._events[event]||[];this._events[event].push(fct)},off:function(event,fct){var n=arguments.length;if(n===0)return delete this._events;if(n===1)return delete this._events[event];this._events=this._events||{};if(event in this._events===false)return;this._events[event].splice(this._events[event].indexOf(fct),1)},trigger:function(event){const events=this._events=this._events||{};if(event in events===false)return;for(var i=0;i<events[event].length;i++){events[event][i].apply(this,Array.prototype.slice.call(arguments,1))}}};MicroEvent.mixin=function(destObject){var props=["on","off","trigger"];for(var i=0;i<props.length;i++){destObject.prototype[props[i]]=MicroEvent.prototype[props[i]]}};var MicroPlugin={};MicroPlugin.mixin=function(Interface){Interface.plugins={};Interface.prototype.initializePlugins=function(plugins){var i,n,key;var self=this;var queue=[];self.plugins={names:[],settings:{},requested:{},loaded:{}};if(utils.isArray(plugins)){for(i=0,n=plugins.length;i<n;i++){if(typeof plugins[i]==="string"){queue.push(plugins[i])}else{self.plugins.settings[plugins[i].name]=plugins[i].options;queue.push(plugins[i].name)}}}else if(plugins){for(key in plugins){if(plugins.hasOwnProperty(key)){self.plugins.settings[key]=plugins[key];queue.push(key)}}}while(queue.length){self.require(queue.shift())}};Interface.prototype.loadPlugin=function(name){var self=this;var plugins=self.plugins;var plugin=Interface.plugins[name];if(!Interface.plugins.hasOwnProperty(name)){throw new Error('Unable to find "'+name+'" plugin')}plugins.requested[name]=true;plugins.loaded[name]=plugin.fn.apply(self,[self.plugins.settings[name]||{}]);plugins.names.push(name)};Interface.prototype.require=function(name){var self=this;var plugins=self.plugins;if(!self.plugins.loaded.hasOwnProperty(name)){if(plugins.requested[name]){throw new Error('Plugin has circular dependency ("'+name+'")')}self.loadPlugin(name)}return plugins.loaded[name]};Interface.define=function(name,fn){Interface.plugins[name]={name:name,fn:fn}}};var utils={isArray:Array.isArray||function(vArg){return Object.prototype.toString.call(vArg)==="[object Array]"}};var Sifter=function(items,settings){this.items=items;this.settings=settings||{diacritics:true}};Sifter.prototype.tokenize=function(query,respect_word_boundaries){query=trim(String(query||"").toLowerCase());if(!query||!query.length)return[];var i,n,regex,letter;var tokens=[];var words=query.split(/ +/);for(i=0,n=words.length;i<n;i++){regex=escape_regex(words[i]);if(this.settings.diacritics){for(letter in DIACRITICS){if(DIACRITICS.hasOwnProperty(letter)){regex=regex.replace(new RegExp(letter,"g"),DIACRITICS[letter])}}}if(respect_word_boundaries)regex="\\b"+regex;tokens.push({string:words[i],regex:new RegExp(regex,"i")})}return tokens};Sifter.prototype.iterator=function(object,callback){var iterator;if(is_array(object)){iterator=Array.prototype.forEach||function(callback){for(var i=0,n=this.length;i<n;i++){callback(this[i],i,this)}}}else{iterator=function(callback){for(var key in this){if(this.hasOwnProperty(key)){callback(this[key],key,this)}}}}iterator.apply(object,[callback])};Sifter.prototype.getScoreFunction=function(search,options){var self,fields,tokens,token_count,nesting;self=this;search=self.prepareSearch(search,options);tokens=search.tokens;fields=search.options.fields;token_count=tokens.length;nesting=search.options.nesting;var scoreValue=function(value,token){var score,pos;if(!value)return 0;value=String(value||"");pos=value.search(token.regex);if(pos===-1)return 0;score=token.string.length/value.length;if(pos===0)score+=.5;return score};var scoreObject=function(){var field_count=fields.length;if(!field_count){return function(){return 0}}if(field_count===1){return function(token,data){return scoreValue(getattr(data,fields[0],nesting),token)}}return function(token,data){for(var i=0,sum=0;i<field_count;i++){sum+=scoreValue(getattr(data,fields[i],nesting),token)}return sum/field_count}}();if(!token_count){return function(){return 0}}if(token_count===1){return function(data){return scoreObject(tokens[0],data)}}if(search.options.conjunction==="and"){return function(data){var score;for(var i=0,sum=0;i<token_count;i++){score=scoreObject(tokens[i],data);if(score<=0)return 0;sum+=score}return sum/token_count}}else{return function(data){for(var i=0,sum=0;i<token_count;i++){sum+=scoreObject(tokens[i],data)}return sum/token_count}}};Sifter.prototype.getSortFunction=function(search,options){var i,n,self,field,fields,fields_count,multiplier,multipliers,get_field,implicit_score,sort;self=this;search=self.prepareSearch(search,options);sort=!search.query&&options.sort_empty||options.sort;get_field=function(name,result){if(name==="$score")return result.score;return getattr(self.items[result.id],name,options.nesting)};fields=[];if(sort){for(i=0,n=sort.length;i<n;i++){if(search.query||sort[i].field!=="$score"){fields.push(sort[i])}}}if(search.query){implicit_score=true;for(i=0,n=fields.length;i<n;i++){if(fields[i].field==="$score"){implicit_score=false;break}}if(implicit_score){fields.unshift({field:"$score",direction:"desc"})}}else{for(i=0,n=fields.length;i<n;i++){if(fields[i].field==="$score"){fields.splice(i,1);break}}}multipliers=[];for(i=0,n=fields.length;i<n;i++){multipliers.push(fields[i].direction==="desc"?-1:1)}fields_count=fields.length;if(!fields_count){return null}else if(fields_count===1){field=fields[0].field;multiplier=multipliers[0];return function(a,b){return multiplier*cmp(get_field(field,a),get_field(field,b))}}else{return function(a,b){var i,result,a_value,b_value,field;for(i=0;i<fields_count;i++){field=fields[i].field;result=multipliers[i]*cmp(get_field(field,a),get_field(field,b));if(result)return result}return 0}}};Sifter.prototype.prepareSearch=function(query,options){if(typeof query==="object")return query;options=extend({},options);var option_fields=options.fields;var option_sort=options.sort;var option_sort_empty=options.sort_empty;if(option_fields&&!is_array(option_fields))options.fields=[option_fields];if(option_sort&&!is_array(option_sort))options.sort=[option_sort];if(option_sort_empty&&!is_array(option_sort_empty))options.sort_empty=[option_sort_empty];return{options:options,query:String(query||"").toLowerCase(),tokens:this.tokenize(query,options.respect_word_boundaries),total:0,items:[]}};Sifter.prototype.search=function(query,options){var self=this,value,score,search,calculateScore;var fn_sort;var fn_score;search=this.prepareSearch(query,options);options=search.options;query=search.query;fn_score=options.score||self.getScoreFunction(search);if(query.length){self.iterator(self.items,function(item,id){score=fn_score(item);if(options.filter===false||score>0){search.items.push({score:score,id:id})}})}else{self.iterator(self.items,function(item,id){search.items.push({score:1,id:id})})}fn_sort=self.getSortFunction(search,options);if(fn_sort)search.items.sort(fn_sort);search.total=search.items.length;if(typeof options.limit==="number"){search.items=search.items.slice(0,options.limit)}return search};var cmp=function(a,b){if(typeof a==="number"&&typeof b==="number"){return a>b?1:a<b?-1:0}a=asciifold(String(a||""));b=asciifold(String(b||""));if(a>b)return 1;if(b>a)return-1;return 0};var extend=function(a,b){var i,n,k,object;for(i=1,n=arguments.length;i<n;i++){object=arguments[i];if(!object)continue;for(k in object){if(object.hasOwnProperty(k)){a[k]=object[k]}}}return a};var getattr=function(obj,name,nesting){if(!obj||!name)return;if(!nesting)return obj[name];var names=name.split(".");while(names.length&&(obj=obj[names.shift()]));return obj};var trim=function(str){return(str+"").replace(/^\s+|\s+$|/g,"")};var escape_regex=function(str){return(str+"").replace(/([.?*+^$[\]\\(){}|-])/g,"\\$1")};var is_array=Array.isArray||typeof $!=="undefined"&&$.isArray||function(object){return Object.prototype.toString.call(object)==="[object Array]"};var DIACRITICS={a:"[aḀḁĂăÂâǍǎȺⱥȦȧẠạÄäÀàÁáĀāÃãÅåąĄÃąĄ]",b:"[b␢βΒB฿𐌁ᛒ]",c:"[cĆćĈĉČčĊċC̄c̄ÇçḈḉȻȼƇƈɕᴄＣｃ]",d:"[dĎďḊḋḐḑḌḍḒḓḎḏĐđD̦d̦ƉɖƊɗƋƌᵭᶁᶑȡᴅＤｄð]",e:"[eÉéÈèÊêḘḙĚěĔĕẼẽḚḛẺẻĖėËëĒēȨȩĘęᶒɆɇȄȅẾếỀềỄễỂểḜḝḖḗḔḕȆȇẸẹỆệⱸᴇＥｅɘǝƏƐε]",f:"[fƑƒḞḟ]",g:"[gɢ₲ǤǥĜĝĞğĢģƓɠĠġ]",h:"[hĤĥĦħḨḩẖẖḤḥḢḣɦʰǶƕ]",i:"[iÍíÌìĬĭÎîǏǐÏïḮḯĨĩĮįĪīỈỉȈȉȊȋỊịḬḭƗɨɨ̆ᵻᶖİiIıɪＩｉ]",j:"[jȷĴĵɈɉʝɟʲ]",k:"[kƘƙꝀꝁḰḱǨǩḲḳḴḵκϰ₭]",l:"[lŁłĽľĻļĹĺḶḷḸḹḼḽḺḻĿŀȽƚⱠⱡⱢɫɬᶅɭȴʟＬｌ]",n:"[nŃńǸǹŇňÑñṄṅŅņṆṇṊṋṈṉN̈n̈ƝɲȠƞᵰᶇɳȵɴＮｎŊŋ]",o:"[oØøÖöÓóÒòÔôǑǒŐőŎŏȮȯỌọƟɵƠơỎỏŌōÕõǪǫȌȍՕօ]",p:"[pṔṕṖṗⱣᵽƤƥᵱ]",q:"[qꝖꝗʠɊɋꝘꝙq̃]",r:"[rŔŕɌɍŘřŖŗṘṙȐȑȒȓṚṛⱤɽ]",s:"[sŚśṠṡṢṣꞨꞩŜŝŠšŞşȘșS̈s̈]",t:"[tŤťṪṫŢţṬṭƮʈȚțṰṱṮṯƬƭ]",u:"[uŬŭɄʉỤụÜüÚúÙùÛûǓǔŰűŬŭƯưỦủŪūŨũŲųȔȕ∪]",v:"[vṼṽṾṿƲʋꝞꝟⱱʋ]",w:"[wẂẃẀẁŴŵẄẅẆẇẈẉ]",x:"[xẌẍẊẋχ]",y:"[yÝýỲỳŶŷŸÿỸỹẎẏỴỵɎɏƳƴ]",z:"[zŹźẐẑŽžŻżẒẓẔẕƵƶ]"};var asciifold=function(){var i,n,k,chunk;var foreignletters="";var lookup={};for(k in DIACRITICS){if(DIACRITICS.hasOwnProperty(k)){chunk=DIACRITICS[k].substring(2,DIACRITICS[k].length-1);foreignletters+=chunk;for(i=0,n=chunk.length;i<n;i++){lookup[chunk.charAt(i)]=k}}}var regexp=new RegExp("["+foreignletters+"]","g");return function(str){return str.replace(regexp,function(foreignletter){return lookup[foreignletter]}).toLowerCase()}}();function uaDetect(platform,re){if(navigator.userAgentData){return platform===navigator.userAgentData.platform}return re.test(navigator.userAgent)}var IS_MAC=uaDetect("macOS",/Mac/);var KEY_A=65;var KEY_COMMA=188;var KEY_RETURN=13;var KEY_ESC=27;var KEY_LEFT=37;var KEY_UP=38;var KEY_P=80;var KEY_RIGHT=39;var KEY_DOWN=40;var KEY_N=78;var KEY_BACKSPACE=8;var KEY_DELETE=46;var KEY_SHIFT=16;var KEY_CMD=IS_MAC?91:17;var KEY_CTRL=IS_MAC?18:17;var KEY_TAB=9;var TAG_SELECT=1;var TAG_INPUT=2;var SUPPORTS_VALIDITY_API=!uaDetect("Android",/android/i)&&!!document.createElement("input").validity;var isset=function(object){return typeof object!=="undefined"};var hash_key=function(value){if(typeof value==="undefined"||value===null)return null;if(typeof value==="boolean")return value?"1":"0";return value+""};var escape_html=function(str){return(str+"").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;")};var escape_replace=function(str){return(str+"").replace(/\$/g,"$$$$")};var hook={};hook.before=function(self,method,fn){var original=self[method];self[method]=function(){fn.apply(self,arguments);return original.apply(self,arguments)}};hook.after=function(self,method,fn){var original=self[method];self[method]=function(){var result=original.apply(self,arguments);fn.apply(self,arguments);return result}};var once=function(fn){var called=false;return function(){if(called)return;called=true;fn.apply(this,arguments)}};var debounce=function(fn,delay){var timeout;return function(){var self=this;var args=arguments;window.clearTimeout(timeout);timeout=window.setTimeout(function(){fn.apply(self,args)},delay)}};var debounce_events=function(self,types,fn){var type;var trigger=self.trigger;var event_args={};self.trigger=function(){var type=arguments[0];if(types.indexOf(type)!==-1){event_args[type]=arguments}else{return trigger.apply(self,arguments)}};fn.apply(self,[]);self.trigger=trigger;for(type in event_args){if(event_args.hasOwnProperty(type)){trigger.apply(self,event_args[type])}}};var watchChildEvent=function($parent,event,selector,fn){$parent.on(event,selector,function(e){var child=e.target;while(child&&child.parentNode!==$parent[0]){child=child.parentNode}e.currentTarget=child;return fn.apply(this,[e])})};var getInputSelection=function(input){var result={};if(input===undefined){console.warn("WARN getInputSelection cannot locate input control");return result}if("selectionStart"in input){result.start=input.selectionStart;result.length=input.selectionEnd-result.start}else if(document.selection){input.focus();var sel=document.selection.createRange();var selLen=document.selection.createRange().text.length;sel.moveStart("character",-input.value.length);result.start=sel.text.length-selLen;result.length=selLen}return result};var transferStyles=function($from,$to,properties){var i,n,styles={};if(properties){for(i=0,n=properties.length;i<n;i++){styles[properties[i]]=$from.css(properties[i])}}else{styles=$from.css()}$to.css(styles)};var measureString=function(str,$parent){if(!str){return 0}if(!Selectize.$testInput){Selectize.$testInput=$("<span />").css({position:"absolute",width:"auto",padding:0,whiteSpace:"pre"});$("<div />").css({position:"absolute",width:0,height:0,overflow:"hidden"}).append(Selectize.$testInput).appendTo("body")}Selectize.$testInput.text(str);transferStyles($parent,Selectize.$testInput,["letterSpacing","fontSize","fontFamily","fontWeight","textTransform"]);return Selectize.$testInput.width()};var autoGrow=function($input){var currentWidth=null;var update=function(e,options){var value,keyCode,printable,width;var placeholder,placeholderWidth;var shift,character,selection;e=e||window.event||{};options=options||{};if(e.metaKey||e.altKey)return;if(!options.force&&$input.data("grow")===false)return;value=$input.val();if(e.type&&e.type.toLowerCase()==="keydown"){keyCode=e.keyCode;printable=keyCode>=48&&keyCode<=57||keyCode>=65&&keyCode<=90||keyCode>=96&&keyCode<=111||keyCode>=186&&keyCode<=222||keyCode===32;if(keyCode===KEY_DELETE||keyCode===KEY_BACKSPACE){selection=getInputSelection($input[0]);if(selection.length){value=value.substring(0,selection.start)+value.substring(selection.start+selection.length)}else if(keyCode===KEY_BACKSPACE&&selection.start){value=value.substring(0,selection.start-1)+value.substring(selection.start+1)}else if(keyCode===KEY_DELETE&&typeof selection.start!=="undefined"){value=value.substring(0,selection.start)+value.substring(selection.start+1)}}else if(printable){shift=e.shiftKey;character=String.fromCharCode(e.keyCode);if(shift)character=character.toUpperCase();else character=character.toLowerCase();value+=character}}placeholder=$input.attr("placeholder");if(placeholder){placeholderWidth=measureString(placeholder,$input)+4}else{placeholderWidth=0}width=Math.max(measureString(value,$input),placeholderWidth)+4;if(width!==currentWidth){currentWidth=width;$input.width(width);$input.triggerHandler("resize")}};$input.on("keydown keyup update blur",update);update()};var domToString=function(d){var tmp=document.createElement("div");tmp.appendChild(d.cloneNode(true));return tmp.innerHTML};var logError=function(message,options){if(!options)options={};var component="Selectize";console.error(component+": "+message);if(options.explanation){if(console.group)console.group();console.error(options.explanation);if(console.group)console.groupEnd()}};var isJSON=function(data){try{JSON.parse(str)}catch(e){return false}return true};var Selectize=function($input,settings){var key,i,n,dir,input,self=this;input=$input[0];input.selectize=self;var computedStyle=window.getComputedStyle&&window.getComputedStyle(input,null);dir=computedStyle?computedStyle.getPropertyValue("direction"):input.currentStyle&&input.currentStyle.direction;dir=dir||$input.parents("[dir]:first").attr("dir")||"";$.extend(self,{order:0,settings:settings,$input:$input,tabIndex:$input.attr("tabindex")||"",tagType:input.tagName.toLowerCase()==="select"?TAG_SELECT:TAG_INPUT,rtl:/rtl/i.test(dir),eventNS:".selectize"+ ++Selectize.count,highlightedValue:null,isBlurring:false,isOpen:false,isDisabled:false,isRequired:$input.is("[required]"),isInvalid:false,isLocked:false,isFocused:false,isInputHidden:false,isSetup:false,isShiftDown:false,isCmdDown:false,isCtrlDown:false,ignoreFocus:false,ignoreBlur:false,ignoreHover:false,hasOptions:false,currentResults:null,lastValue:"",lastValidValue:"",lastOpenTarget:false,caretPos:0,loading:0,loadedSearches:{},isDropdownClosing:false,$activeOption:null,$activeItems:[],optgroups:{},options:{},userOptions:{},items:[],renderCache:{},onSearchChange:settings.loadThrottle===null?self.onSearchChange:debounce(self.onSearchChange,settings.loadThrottle)});self.sifter=new Sifter(this.options,{diacritics:settings.diacritics});if(self.settings.options){for(i=0,n=self.settings.options.length;i<n;i++){self.registerOption(self.settings.options[i])}delete self.settings.options}if(self.settings.optgroups){for(i=0,n=self.settings.optgroups.length;i<n;i++){self.registerOptionGroup(self.settings.optgroups[i])}delete self.settings.optgroups}self.settings.mode=self.settings.mode||(self.settings.maxItems===1?"single":"multi");if(typeof self.settings.hideSelected!=="boolean"){self.settings.hideSelected=self.settings.mode==="multi"}self.initializePlugins(self.settings.plugins);self.setupCallbacks();self.setupTemplates();self.setup()};MicroEvent.mixin(Selectize);MicroPlugin.mixin(Selectize);$.extend(Selectize.prototype,{setup:function(){var self=this;var settings=self.settings;var eventNS=self.eventNS;var $window=$(window);var $document=$(document);var $input=self.$input;var $wrapper;var $control;var $control_input;var $dropdown;var $dropdown_content;var $dropdown_parent;var inputMode;var timeout_blur;var timeout_focus;var classes;var classes_plugins;var inputId;inputMode=self.settings.mode;classes=$input.attr("class")||"";$wrapper=$("<div>").addClass(settings.wrapperClass).addClass(classes+" selectize-control").addClass(inputMode);$control=$("<div>").addClass(settings.inputClass+" selectize-input items").appendTo($wrapper);$control_input=$('<input type="select-one" autocomplete="new-password" autofill="no" />').appendTo($control).attr("tabindex",$input.is(":disabled")?"-1":self.tabIndex);$dropdown_parent=$(settings.dropdownParent||$wrapper);$dropdown=$("<div>").addClass(settings.dropdownClass).addClass(inputMode+" selectize-dropdown").hide().appendTo($dropdown_parent);$dropdown_content=$("<div>").addClass(settings.dropdownContentClass+" selectize-dropdown-content").attr("tabindex","-1").appendTo($dropdown);if(inputId=$input.attr("id")){$control_input.attr("id",inputId+"-selectized");$("label[for='"+inputId+"']").attr("for",inputId+"-selectized")}if(self.settings.copyClassesToDropdown){$dropdown.addClass(classes)}$wrapper.css({width:$input[0].style.width});if(self.plugins.names.length){classes_plugins="plugin-"+self.plugins.names.join(" plugin-");$wrapper.addClass(classes_plugins);$dropdown.addClass(classes_plugins)}if((settings.maxItems===null||settings.maxItems>1)&&self.tagType===TAG_SELECT){$input.attr("multiple","multiple")}if(self.settings.placeholder){$control_input.attr("placeholder",settings.placeholder)}if(!self.settings.search){$control_input.attr("readonly",true);$control_input.attr("inputmode","none");$control.css("cursor","pointer")}if(!self.settings.splitOn&&self.settings.delimiter){var delimiterEscaped=self.settings.delimiter.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&");self.settings.splitOn=new RegExp("\\s*"+delimiterEscaped+"+\\s*")}if($input.attr("autocorrect")){$control_input.attr("autocorrect",$input.attr("autocorrect"))}if($input.attr("autocapitalize")){$control_input.attr("autocapitalize",$input.attr("autocapitalize"))}if($input.is("input")){$control_input[0].type=$input[0].type}self.$wrapper=$wrapper;self.$control=$control;self.$control_input=$control_input;self.$dropdown=$dropdown;self.$dropdown_content=$dropdown_content;$dropdown.on("mouseenter mousedown mouseup click","[data-disabled]>[data-selectable]",function(e){e.stopImmediatePropagation()});$dropdown.on("mouseenter","[data-selectable]",function(){return self.onOptionHover.apply(self,arguments)});$dropdown.on("mouseup click","[data-selectable]",function(){return self.onOptionSelect.apply(self,arguments)});watchChildEvent($control,"mouseup","*:not(input)",function(){return self.onItemSelect.apply(self,arguments)});autoGrow($control_input);$control.on({mousedown:function(){return self.onMouseDown.apply(self,arguments)},click:function(){return self.onClick.apply(self,arguments)}});$control_input.on({mousedown:function(e){if(self.$control_input.val()!==""||self.settings.openOnFocus){e.stopPropagation()}},keydown:function(){return self.onKeyDown.apply(self,arguments)},keypress:function(){return self.onKeyPress.apply(self,arguments)},input:function(){return self.onInput.apply(self,arguments)},resize:function(){self.positionDropdown.apply(self,[])},focus:function(){self.ignoreBlur=false;return self.onFocus.apply(self,arguments)},paste:function(){return self.onPaste.apply(self,arguments)}});$document.on("keydown"+eventNS,function(e){self.isCmdDown=e[IS_MAC?"metaKey":"ctrlKey"];self.isCtrlDown=e[IS_MAC?"altKey":"ctrlKey"];self.isShiftDown=e.shiftKey});$document.on("keyup"+eventNS,function(e){if(e.keyCode===KEY_CTRL)self.isCtrlDown=false;if(e.keyCode===KEY_SHIFT)self.isShiftDown=false;if(e.keyCode===KEY_CMD)self.isCmdDown=false});$document.on("mousedown"+eventNS,function(e){if(self.isFocused){if(e.target===self.$dropdown[0]||e.target.parentNode===self.$dropdown[0]){return false}if(!self.$dropdown.has(e.target).length&&e.target!==self.$control[0]){self.blur(e.target)}}});$window.on(["scroll"+eventNS,"resize"+eventNS].join(" "),function(){if(self.isOpen){self.positionDropdown.apply(self,arguments)}});$window.on("mousemove"+eventNS,function(){self.ignoreHover=self.settings.ignoreHover});var inputPlaceholder=$("<div></div>");var inputChildren=$input.children().detach();$input.replaceWith(inputPlaceholder);inputPlaceholder.replaceWith($input);this.revertSettings={$children:inputChildren,tabindex:$input.attr("tabindex")};$input.attr("tabindex",-1).hide().after(self.$wrapper);if(Array.isArray(settings.items)){self.lastValidValue=settings.items;self.setValue(settings.items);delete settings.items}if(SUPPORTS_VALIDITY_API){$input.on("invalid"+eventNS,function(e){e.preventDefault();self.isInvalid=true;self.refreshState()})}self.updateOriginalInput();self.refreshItems();self.refreshState();self.updatePlaceholder();self.isSetup=true;if($input.is(":disabled")){self.disable()}self.on("change",this.onChange);$input.data("selectize",self);$input.addClass("selectized");self.trigger("initialize");if(settings.preload===true){self.onSearchChange("")}},setupTemplates:function(){var self=this;var field_label=self.settings.labelField;var field_value=self.settings.valueField;var field_optgroup=self.settings.optgroupLabelField;var templates={optgroup:function(data){return'<div class="optgroup">'+data.html+"</div>"},optgroup_header:function(data,escape){return'<div class="optgroup-header">'+escape(data[field_optgroup])+"</div>"},option:function(data,escape){var classes=data.classes?" "+data.classes:"";classes+=data[field_value]===""?" selectize-dropdown-emptyoptionlabel":"";var styles=data.styles?' style="'+data.styles+'"':"";return"<div"+styles+' class="option'+classes+'">'+escape(data[field_label])+"</div>"},item:function(data,escape){return'<div class="item">'+escape(data[field_label])+"</div>"},option_create:function(data,escape){return'<div class="create">Add <strong>'+escape(data.input)+"</strong>&#x2026;</div>"}};self.settings.render=$.extend({},templates,self.settings.render)},setupCallbacks:function(){var key,fn,callbacks={initialize:"onInitialize",change:"onChange",item_add:"onItemAdd",item_remove:"onItemRemove",clear:"onClear",option_add:"onOptionAdd",option_remove:"onOptionRemove",option_clear:"onOptionClear",optgroup_add:"onOptionGroupAdd",optgroup_remove:"onOptionGroupRemove",optgroup_clear:"onOptionGroupClear",dropdown_open:"onDropdownOpen",dropdown_close:"onDropdownClose",type:"onType",load:"onLoad",focus:"onFocus",blur:"onBlur",dropdown_item_activate:"onDropdownItemActivate",dropdown_item_deactivate:"onDropdownItemDeactivate"};for(key in callbacks){if(callbacks.hasOwnProperty(key)){fn=this.settings[callbacks[key]];if(fn)this.on(key,fn)}}},onClick:function(e){var self=this;if(self.isDropdownClosing){return}if(!self.isFocused||!self.isOpen){self.focus();e.preventDefault()}},onMouseDown:function(e){var self=this;var defaultPrevented=e.isDefaultPrevented();var $target=$(e.target);if(!self.isFocused){if(!defaultPrevented){window.setTimeout(function(){self.focus()},0)}}if(e.target!==self.$control_input[0]||self.$control_input.val()===""){if(self.settings.mode==="single"){self.isOpen?self.close():self.open()}else{if(!defaultPrevented){self.setActiveItem(null)}if(!self.settings.openOnFocus){if(self.isOpen&&e.target===self.lastOpenTarget){self.close();self.lastOpenTarget=false}else if(!self.isOpen){self.refreshOptions();self.open();self.lastOpenTarget=e.target}else{self.lastOpenTarget=e.target}}}return false}},onChange:function(){var self=this;if(self.getValue()!==""){self.lastValidValue=self.getValue()}this.$input.trigger("input");this.$input.trigger("change")},onPaste:function(e){var self=this;if(self.isFull()||self.isInputHidden||self.isLocked){e.preventDefault();return}if(self.settings.splitOn){setTimeout(function(){var pastedText=self.$control_input.val();if(!pastedText.match(self.settings.splitOn)){return}var splitInput=pastedText.trim().split(self.settings.splitOn);for(var i=0,n=splitInput.length;i<n;i++){self.createItem(splitInput[i])}},0)}},onKeyPress:function(e){if(this.isLocked)return e&&e.preventDefault();var character=String.fromCharCode(e.keyCode||e.which);if(this.settings.create&&this.settings.mode==="multi"&&character===this.settings.delimiter){this.createItem();e.preventDefault();return false}},onKeyDown:function(e){var isInput=e.target===this.$control_input[0];var self=this;if(self.isLocked){if(e.keyCode!==KEY_TAB){e.preventDefault()}return}switch(e.keyCode){case KEY_A:if(self.isCmdDown){self.selectAll();return}break;case KEY_ESC:if(self.isOpen){e.preventDefault();e.stopPropagation();self.close()}return;case KEY_N:if(!e.ctrlKey||e.altKey)break;case KEY_DOWN:if(!self.isOpen&&self.hasOptions){self.open()}else if(self.$activeOption){self.ignoreHover=true;var $next=self.getAdjacentOption(self.$activeOption,1);if($next.length)self.setActiveOption($next,true,true)}e.preventDefault();return;case KEY_P:if(!e.ctrlKey||e.altKey)break;case KEY_UP:if(self.$activeOption){self.ignoreHover=true;var $prev=self.getAdjacentOption(self.$activeOption,-1);if($prev.length)self.setActiveOption($prev,true,true)}e.preventDefault();return;case KEY_RETURN:if(self.isOpen&&self.$activeOption){self.onOptionSelect({currentTarget:self.$activeOption});e.preventDefault()}return;case KEY_LEFT:self.advanceSelection(-1,e);return;case KEY_RIGHT:self.advanceSelection(1,e);return;case KEY_TAB:if(self.settings.selectOnTab&&self.isOpen&&self.$activeOption){self.onOptionSelect({currentTarget:self.$activeOption});if(!self.isFull()){e.preventDefault()}}if(self.settings.create&&self.createItem()&&self.settings.showAddOptionOnCreate){e.preventDefault()}return;case KEY_BACKSPACE:case KEY_DELETE:self.deleteSelection(e);return}if((self.isFull()||self.isInputHidden)&&!(IS_MAC?e.metaKey:e.ctrlKey)){e.preventDefault();return}},onInput:function(e){var self=this;var value=self.$control_input.val()||"";if(self.lastValue!==value){self.lastValue=value;self.onSearchChange(value);self.refreshOptions();self.trigger("type",value)}},onSearchChange:function(value){var self=this;var fn=self.settings.load;if(!fn)return;if(self.loadedSearches.hasOwnProperty(value))return;self.loadedSearches[value]=true;self.load(function(callback){fn.apply(self,[value,callback])})},onFocus:function(e){var self=this;var wasFocused=self.isFocused;if(self.isDisabled){self.blur();e&&e.preventDefault();return false}if(self.ignoreFocus)return;self.isFocused=true;if(self.settings.preload==="focus")self.onSearchChange("");if(!wasFocused)self.trigger("focus");if(!self.$activeItems.length){self.showInput();self.setActiveItem(null);self.refreshOptions(!!self.settings.openOnFocus)}self.refreshState()},onBlur:function(e,dest){var self=this;if(!self.isFocused)return;self.isFocused=false;if(self.ignoreFocus){return}var deactivate=function(){self.close();self.setTextboxValue("");self.setActiveItem(null);self.setActiveOption(null);self.setCaret(self.items.length);self.refreshState();dest&&dest.focus&&dest.focus();self.isBlurring=false;self.ignoreFocus=false;self.trigger("blur")};self.isBlurring=true;self.ignoreFocus=true;if(self.settings.create&&self.settings.createOnBlur){self.createItem(null,false,deactivate)}else{deactivate()}},onOptionHover:function(e){if(this.ignoreHover)return;this.setActiveOption(e.currentTarget,false)},onOptionSelect:function(e){var value,$target,$option,self=this;if(e.preventDefault){e.preventDefault();e.stopPropagation()}$target=$(e.currentTarget);if($target.hasClass("create")){self.createItem(null,function(){if(self.settings.closeAfterSelect){self.close()}})}else{value=$target.attr("data-value");if(typeof value!=="undefined"){self.lastQuery=null;self.setTextboxValue("");self.addItem(value);if(self.settings.closeAfterSelect){self.close()}else if(!self.settings.hideSelected&&e.type&&/mouse/.test(e.type)){self.setActiveOption(self.getOption(value))}}}},onItemSelect:function(e){var self=this;if(self.isLocked)return;if(self.settings.mode==="multi"){e.preventDefault();self.setActiveItem(e.currentTarget,e)}},load:function(fn){var self=this;var $wrapper=self.$wrapper.addClass(self.settings.loadingClass);self.loading++;fn.apply(self,[function(results){self.loading=Math.max(self.loading-1,0);if(results&&results.length){self.addOption(results);self.refreshOptions(self.isFocused&&!self.isInputHidden)}if(!self.loading){$wrapper.removeClass(self.settings.loadingClass)}self.trigger("load",results)}])},getTextboxValue:function(){var $input=this.$control_input;return $input.val()},setTextboxValue:function(value){var $input=this.$control_input;var changed=$input.val()!==value;if(changed){$input.val(value).triggerHandler("update");this.lastValue=value}},getValue:function(){if(this.tagType===TAG_SELECT&&this.$input.attr("multiple")){return this.items}else{return this.items.join(this.settings.delimiter)}},setValue:function(value,silent){const items=Array.isArray(value)?value:[value];if(items.join("")===this.items.join("")){return}var events=silent?[]:["change"];debounce_events(this,events,function(){this.clear(silent);this.addItems(value,silent)})},setMaxItems:function(value){if(value===0)value=null;this.settings.maxItems=value;this.settings.mode=this.settings.mode||(this.settings.maxItems===1?"single":"multi");this.refreshState()},setActiveItem:function($item,e){var self=this;var eventName;var i,idx,begin,end,item,swap;var $last;if(self.settings.mode==="single")return;$item=$($item);if(!$item.length){$(self.$activeItems).removeClass("active");self.$activeItems=[];if(self.isFocused){self.showInput()}return}eventName=e&&e.type.toLowerCase();if(eventName==="mousedown"&&self.isShiftDown&&self.$activeItems.length){$last=self.$control.children(".active:last");begin=Array.prototype.indexOf.apply(self.$control[0].childNodes,[$last[0]]);end=Array.prototype.indexOf.apply(self.$control[0].childNodes,[$item[0]]);if(begin>end){swap=begin;begin=end;end=swap}for(i=begin;i<=end;i++){item=self.$control[0].childNodes[i];if(self.$activeItems.indexOf(item)===-1){$(item).addClass("active");self.$activeItems.push(item)}}e.preventDefault()}else if(eventName==="mousedown"&&self.isCtrlDown||eventName==="keydown"&&this.isShiftDown){if($item.hasClass("active")){idx=self.$activeItems.indexOf($item[0]);self.$activeItems.splice(idx,1);$item.removeClass("active")}else{self.$activeItems.push($item.addClass("active")[0])}}else{$(self.$activeItems).removeClass("active");self.$activeItems=[$item.addClass("active")[0]]}self.hideInput();if(!this.isFocused){self.focus()}},setActiveOption:function($option,scroll,animate){var height_menu,height_item,y;var scroll_top,scroll_bottom;var self=this;if(self.$activeOption){self.$activeOption.removeClass("active");self.trigger("dropdown_item_deactivate",self.$activeOption.attr("data-value"))}self.$activeOption=null;$option=$($option);if(!$option.length)return;self.$activeOption=$option.addClass("active");if(self.isOpen)self.trigger("dropdown_item_activate",self.$activeOption.attr("data-value"));if(scroll||!isset(scroll)){height_menu=self.$dropdown_content.height();height_item=self.$activeOption.outerHeight(true);scroll=self.$dropdown_content.scrollTop()||0;y=self.$activeOption.offset().top-self.$dropdown_content.offset().top+scroll;scroll_top=y;scroll_bottom=y-height_menu+height_item;if(y+height_item>height_menu+scroll){self.$dropdown_content.stop().animate({scrollTop:scroll_bottom},animate?self.settings.scrollDuration:0)}else if(y<scroll){self.$dropdown_content.stop().animate({scrollTop:scroll_top},animate?self.settings.scrollDuration:0)}}},selectAll:function(){var self=this;if(self.settings.mode==="single")return;self.$activeItems=Array.prototype.slice.apply(self.$control.children(":not(input)").addClass("active"));if(self.$activeItems.length){self.hideInput();self.close()}self.focus()},hideInput:function(){var self=this;self.setTextboxValue("");self.$control_input.css({opacity:0,position:"absolute",left:self.rtl?1e4:0});self.isInputHidden=true},showInput:function(){this.$control_input.css({opacity:1,position:"relative",left:0});this.isInputHidden=false},focus:function(){var self=this;if(self.isDisabled)return self;self.ignoreFocus=true;self.$control_input[0].focus();window.setTimeout(function(){self.ignoreFocus=false;self.onFocus()},0);return self},blur:function(dest){this.$control_input[0].blur();this.onBlur(null,dest);return this},getScoreFunction:function(query){return this.sifter.getScoreFunction(query,this.getSearchOptions())},getSearchOptions:function(){var settings=this.settings;var sort=settings.sortField;if(typeof sort==="string"){sort=[{field:sort}]}return{fields:settings.searchField,conjunction:settings.searchConjunction,sort:sort,nesting:settings.nesting,filter:settings.filter,respect_word_boundaries:settings.respect_word_boundaries}},search:function(query){var i,value,score,result,calculateScore;var self=this;var settings=self.settings;var options=this.getSearchOptions();if(settings.score){calculateScore=self.settings.score.apply(this,[query]);if(typeof calculateScore!=="function"){throw new Error('Selectize "score" setting must be a function that returns a function')}}if(query!==self.lastQuery){if(settings.normalize)query=query.normalize("NFD").replace(/[\u0300-\u036f]/g,"");self.lastQuery=query;result=self.sifter.search(query,$.extend(options,{score:calculateScore}));self.currentResults=result}else{result=$.extend(true,{},self.currentResults)}if(settings.hideSelected){for(i=result.items.length-1;i>=0;i--){if(self.items.indexOf(hash_key(result.items[i].id))!==-1){result.items.splice(i,1)}}}return result},refreshOptions:function(triggerDropdown){var i,j,k,n,groups,groups_order,option,option_html,optgroup,optgroups,html,html_children,has_create_option;var $active,$active_before,$create;if(typeof triggerDropdown==="undefined"){triggerDropdown=true}var self=this;var query=self.$control_input.val().trim();var results=self.search(query);var $dropdown_content=self.$dropdown_content;var active_before=self.$activeOption&&hash_key(self.$activeOption.attr("data-value"));n=results.items.length;if(typeof self.settings.maxOptions==="number"){n=Math.min(n,self.settings.maxOptions)}groups={};groups_order=[];for(i=0;i<n;i++){option=self.options[results.items[i].id];option_html=self.render("option",option);optgroup=option[self.settings.optgroupField]||"";optgroups=Array.isArray(optgroup)?optgroup:[optgroup];for(j=0,k=optgroups&&optgroups.length;j<k;j++){optgroup=optgroups[j];if(!self.optgroups.hasOwnProperty(optgroup)&&typeof self.settings.optionGroupRegister==="function"){var regGroup;if(regGroup=self.settings.optionGroupRegister.apply(self,[optgroup])){self.registerOptionGroup(regGroup)}}if(!self.optgroups.hasOwnProperty(optgroup)){optgroup=""}if(!groups.hasOwnProperty(optgroup)){groups[optgroup]=document.createDocumentFragment();groups_order.push(optgroup)}groups[optgroup].appendChild(option_html)}}if(this.settings.lockOptgroupOrder){groups_order.sort(function(a,b){var a_order=self.optgroups[a]&&self.optgroups[a].$order||0;var b_order=self.optgroups[b]&&self.optgroups[b].$order||0;return a_order-b_order})}html=document.createDocumentFragment();for(i=0,n=groups_order.length;i<n;i++){optgroup=groups_order[i];if(self.optgroups.hasOwnProperty(optgroup)&&groups[optgroup].childNodes.length){html_children=document.createDocumentFragment();html_children.appendChild(self.render("optgroup_header",self.optgroups[optgroup]));html_children.appendChild(groups[optgroup]);html.appendChild(self.render("optgroup",$.extend({},self.optgroups[optgroup],{html:domToString(html_children),dom:html_children})))}else{html.appendChild(groups[optgroup])}}$dropdown_content.html(html);if(self.settings.highlight){$dropdown_content.removeHighlight();if(results.query.length&&results.tokens.length){for(i=0,n=results.tokens.length;i<n;i++){highlight($dropdown_content,results.tokens[i].regex)}}}if(!self.settings.hideSelected){self.$dropdown.find(".selected").removeClass("selected");for(i=0,n=self.items.length;i<n;i++){self.getOption(self.items[i]).addClass("selected")}}if(self.settings.dropdownSize.sizeType!=="auto"&&self.isOpen){self.setupDropdownHeight()}has_create_option=self.canCreate(query);if(has_create_option){if(self.settings.showAddOptionOnCreate){$dropdown_content.prepend(self.render("option_create",{input:query}));$create=$($dropdown_content[0].childNodes[0])}}self.hasOptions=results.items.length>0||has_create_option&&self.settings.showAddOptionOnCreate||self.settings.setFirstOptionActive;if(self.hasOptions){if(results.items.length>0){$active_before=active_before&&self.getOption(active_before);if(results.query!==""&&self.settings.setFirstOptionActive){$active=$dropdown_content.find("[data-selectable]:first")}else if(results.query!==""&&$active_before&&$active_before.length){$active=$active_before}else if(self.settings.mode==="single"&&self.items.length){$active=self.getOption(self.items[0])}if(!$active||!$active.length){if($create&&!self.settings.addPrecedence){$active=self.getAdjacentOption($create,1)}else{$active=$dropdown_content.find("[data-selectable]:first")}}}else{$active=$create}self.setActiveOption($active);if(triggerDropdown&&!self.isOpen){self.open()}}else{self.setActiveOption(null);if(triggerDropdown&&self.isOpen){self.close()}}},addOption:function(data){var i,n,value,self=this;if(Array.isArray(data)){for(i=0,n=data.length;i<n;i++){self.addOption(data[i])}return}if(value=self.registerOption(data)){self.userOptions[value]=true;self.lastQuery=null;self.trigger("option_add",value,data)}},registerOption:function(data){var key=hash_key(data[this.settings.valueField]);if(typeof key==="undefined"||key===null||this.options.hasOwnProperty(key))return false;data.$order=data.$order||++this.order;this.options[key]=data;return key},registerOptionGroup:function(data){var key=hash_key(data[this.settings.optgroupValueField]);if(!key)return false;data.$order=data.$order||++this.order;this.optgroups[key]=data;return key},addOptionGroup:function(id,data){data[this.settings.optgroupValueField]=id;if(id=this.registerOptionGroup(data)){this.trigger("optgroup_add",id,data)}},removeOptionGroup:function(id){if(this.optgroups.hasOwnProperty(id)){delete this.optgroups[id];this.renderCache={};this.trigger("optgroup_remove",id)}},clearOptionGroups:function(){this.optgroups={};this.renderCache={};this.trigger("optgroup_clear")},updateOption:function(value,data){var self=this;var $item,$item_new;var value_new,index_item,cache_items,cache_options,order_old;value=hash_key(value);value_new=hash_key(data[self.settings.valueField]);if(value===null)return;if(!self.options.hasOwnProperty(value))return;if(typeof value_new!=="string")throw new Error("Value must be set in option data");order_old=self.options[value].$order;if(value_new!==value){delete self.options[value];index_item=self.items.indexOf(value);if(index_item!==-1){self.items.splice(index_item,1,value_new)}}data.$order=data.$order||order_old;self.options[value_new]=data;cache_items=self.renderCache["item"];cache_options=self.renderCache["option"];if(cache_items){delete cache_items[value];delete cache_items[value_new]}if(cache_options){delete cache_options[value];delete cache_options[value_new]}if(self.items.indexOf(value_new)!==-1){$item=self.getItem(value);$item_new=$(self.render("item",data));if($item.hasClass("active"))$item_new.addClass("active");$item.replaceWith($item_new)}self.lastQuery=null;if(self.isOpen){self.refreshOptions(false)}},removeOption:function(value,silent){var self=this;value=hash_key(value);var cache_items=self.renderCache["item"];var cache_options=self.renderCache["option"];if(cache_items)delete cache_items[value];if(cache_options)delete cache_options[value];delete self.userOptions[value];delete self.options[value];self.lastQuery=null;self.trigger("option_remove",value);self.removeItem(value,silent)},clearOptions:function(silent){var self=this;self.loadedSearches={};self.userOptions={};self.renderCache={};var options=self.options;$.each(self.options,function(key,value){if(self.items.indexOf(key)==-1){delete options[key]}});self.options=self.sifter.items=options;self.lastQuery=null;self.trigger("option_clear");self.clear(silent)},getOption:function(value){return this.getElementWithValue(value,this.$dropdown_content.find("[data-selectable]"))},getFirstOption:function(){var $options=this.$dropdown.find("[data-selectable]");return $options.length>0?$options.eq(0):$()},getAdjacentOption:function($option,direction){var $options=this.$dropdown.find("[data-selectable]");var index=$options.index($option)+direction;return index>=0&&index<$options.length?$options.eq(index):$()},getElementWithValue:function(value,$els){value=hash_key(value);if(typeof value!=="undefined"&&value!==null){for(var i=0,n=$els.length;i<n;i++){if($els[i].getAttribute("data-value")===value){return $($els[i])}}}return $()},getElementWithTextContent:function(textContent,ignoreCase,$els){textContent=hash_key(textContent);if(typeof textContent!=="undefined"&&textContent!==null){for(var i=0,n=$els.length;i<n;i++){var eleTextContent=$els[i].textContent;if(ignoreCase==true){eleTextContent=eleTextContent!==null?eleTextContent.toLowerCase():null;textContent=textContent.toLowerCase()}if(eleTextContent===textContent){return $($els[i])}}}return $()},getItem:function(value){return this.getElementWithValue(value,this.$control.children())},getFirstItemMatchedByTextContent:function(textContent,ignoreCase){ignoreCase=ignoreCase!==null&&ignoreCase===true?true:false;return this.getElementWithTextContent(textContent,ignoreCase,this.$dropdown_content.find("[data-selectable]"))},addItems:function(values,silent){this.buffer=document.createDocumentFragment();var childNodes=this.$control[0].childNodes;for(var i=0;i<childNodes.length;i++){this.buffer.appendChild(childNodes[i])}var items=Array.isArray(values)?values:[values];for(var i=0,n=items.length;i<n;i++){this.isPending=i<n-1;this.addItem(items[i],silent)}var control=this.$control[0];control.insertBefore(this.buffer,control.firstChild);this.buffer=null},addItem:function(value,silent){var events=silent?[]:["change"];debounce_events(this,events,function(){var $item,$option,$options;var self=this;var inputMode=self.settings.mode;var i,active,value_next,wasFull;value=hash_key(value);if(self.items.indexOf(value)!==-1){if(inputMode==="single")self.close();return}if(!self.options.hasOwnProperty(value))return;if(inputMode==="single")self.clear(silent);if(inputMode==="multi"&&self.isFull())return;$item=$(self.render("item",self.options[value]));wasFull=self.isFull();self.items.splice(self.caretPos,0,value);self.insertAtCaret($item);if(!self.isPending||!wasFull&&self.isFull()){self.refreshState()}if(self.isSetup){$options=self.$dropdown_content.find("[data-selectable]");if(!self.isPending){$option=self.getOption(value);value_next=self.getAdjacentOption($option,1).attr("data-value");self.refreshOptions(self.isFocused&&inputMode!=="single");if(value_next){self.setActiveOption(self.getOption(value_next))}}if(!$options.length||self.isFull()){self.close()}else if(!self.isPending){self.positionDropdown()}self.updatePlaceholder();self.trigger("item_add",value,$item);if(!self.isPending){self.updateOriginalInput({silent:silent})}}})},removeItem:function(value,silent){var self=this;var $item,i,idx;$item=value instanceof $?value:self.getItem(value);value=hash_key($item.attr("data-value"));i=self.items.indexOf(value);if(i!==-1){self.trigger("item_before_remove",value,$item);$item.remove();if($item.hasClass("active")){$item.removeClass("active");idx=self.$activeItems.indexOf($item[0]);self.$activeItems.splice(idx,1);$item.removeClass("active")}self.items.splice(i,1);self.lastQuery=null;if(!self.settings.persist&&self.userOptions.hasOwnProperty(value)){self.removeOption(value,silent)}if(i<self.caretPos){self.setCaret(self.caretPos-1)}self.refreshState();self.updatePlaceholder();self.updateOriginalInput({silent:silent});self.positionDropdown();self.trigger("item_remove",value,$item)}},createItem:function(input,triggerDropdown){var self=this;var caret=self.caretPos;input=input||(self.$control_input.val()||"").trim();var callback=arguments[arguments.length-1];if(typeof callback!=="function")callback=function(){};if(typeof triggerDropdown!=="boolean"){triggerDropdown=true}if(!self.canCreate(input)){callback();return false}self.lock();var setup=typeof self.settings.create==="function"?this.settings.create:function(input){var data={};data[self.settings.labelField]=input;var key=input;if(self.settings.formatValueToKey&&typeof self.settings.formatValueToKey==="function"){key=self.settings.formatValueToKey.apply(this,[key]);if(key===null||typeof key==="undefined"||typeof key==="object"||typeof key==="function"){throw new Error('Selectize "formatValueToKey" setting must be a function that returns a value other than object or function.')}}data[self.settings.valueField]=key;return data};var create=once(function(data){self.unlock();if(!data||typeof data!=="object")return callback();var value=hash_key(data[self.settings.valueField]);if(typeof value!=="string")return callback();self.setTextboxValue("");self.addOption(data);self.setCaret(caret);self.addItem(value);self.refreshOptions(triggerDropdown&&self.settings.mode!=="single");callback(data)});var output=setup.apply(this,[input,create]);if(typeof output!=="undefined"){create(output)}return true},refreshItems:function(silent){this.lastQuery=null;if(this.isSetup){this.addItem(this.items,silent)}this.refreshState();this.updateOriginalInput({silent:silent})},refreshState:function(){this.refreshValidityState();this.refreshClasses()},refreshValidityState:function(){if(!this.isRequired)return false;var invalid=!this.items.length;this.isInvalid=invalid;this.$control_input.prop("required",invalid);this.$input.prop("required",!invalid)},refreshClasses:function(){var self=this;var isFull=self.isFull();var isLocked=self.isLocked;self.$wrapper.toggleClass("rtl",self.rtl);self.$control.toggleClass("focus",self.isFocused).toggleClass("disabled",self.isDisabled).toggleClass("required",self.isRequired).toggleClass("invalid",self.isInvalid).toggleClass("locked",isLocked).toggleClass("full",isFull).toggleClass("not-full",!isFull).toggleClass("input-active",self.isFocused&&!self.isInputHidden).toggleClass("dropdown-active",self.isOpen).toggleClass("has-options",!$.isEmptyObject(self.options)).toggleClass("has-items",self.items.length>0);self.$control_input.data("grow",!isFull&&!isLocked)},isFull:function(){return this.settings.maxItems!==null&&this.items.length>=this.settings.maxItems},updateOriginalInput:function(opts){var i,n,existing,fresh,old,$options,label,value,values,self=this;opts=opts||{};if(self.tagType===TAG_SELECT){$options=self.$input.find("option");existing=[];fresh=[];old=[];values=[];$options.get().forEach(function(option){existing.push(option.value)});self.items.forEach(function(item){label=self.options[item][self.settings.labelField]||"";values.push(item);if(existing.indexOf(item)!=-1){return}fresh.push('<option value="'+escape_html(item)+'" selected="selected">'+escape_html(label)+"</option>")});old=existing.filter(function(value){return values.indexOf(value)<0}).map(function(value){return'option[value="'+value+'"]'});if(existing.length-old.length+fresh.length===0&&!self.$input.attr("multiple")){fresh.push('<option value="" selected="selected"></option>')}self.$input.find(old.join(", ")).remove();self.$input.append(fresh.join(""))}else{self.$input.val(self.getValue());self.$input.attr("value",self.$input.val())}if(self.isSetup){if(!opts.silent){self.trigger("change",self.$input.val())}}},updatePlaceholder:function(){if(!this.settings.placeholder)return;var $input=this.$control_input;if(this.items.length){$input.removeAttr("placeholder")}else{$input.attr("placeholder",this.settings.placeholder)}$input.triggerHandler("update",{force:true})},open:function(){var self=this;if(self.isLocked||self.isOpen||self.settings.mode==="multi"&&self.isFull())return;self.focus();self.isOpen=true;self.refreshState();self.$dropdown.css({visibility:"hidden",display:"block"});self.setupDropdownHeight();self.positionDropdown();self.$dropdown.css({visibility:"visible"});self.trigger("dropdown_open",self.$dropdown)},close:function(){var self=this;var trigger=self.isOpen;if(self.settings.mode==="single"&&self.items.length){self.hideInput();if(self.isBlurring){self.$control_input[0].blur()}}self.isOpen=false;self.$dropdown.hide();self.setActiveOption(null);self.refreshState();if(trigger)self.trigger("dropdown_close",self.$dropdown)},positionDropdown:function(){var $control=this.$control;var offset=this.settings.dropdownParent==="body"?$control.offset():$control.position();offset.top+=$control.outerHeight(true);var w=$control[0].getBoundingClientRect().width;if(this.settings.minWidth&&this.settings.minWidth>w){w=this.settings.minWidth}this.$dropdown.css({width:w,top:offset.top,left:offset.left})},setupDropdownHeight:function(){if(typeof this.settings.dropdownSize==="object"&&this.settings.dropdownSize.sizeType!=="auto"){var height=this.settings.dropdownSize.sizeValue;if(this.settings.dropdownSize.sizeType==="numberItems"){var $items=this.$dropdown_content.find("*").not(".optgroup, .highlight").not(this.settings.ignoreOnDropwdownHeight);var totalHeight=0;var marginTop=0;var marginBottom=0;var separatorHeight=0;for(var i=0;i<height;i++){var $item=$($items[i]);if($item.length===0){break}totalHeight+=$item.outerHeight(true);if(typeof $item.data("selectable")=="undefined"){if($item.hasClass("optgroup-header")){var styles=window.getComputedStyle($item.parent()[0],":before");if(styles){marginTop=styles.marginTop?Number(styles.marginTop.replace(/\W*(\w)\w*/g,"$1")):0;marginBottom=styles.marginBottom?Number(styles.marginBottom.replace(/\W*(\w)\w*/g,"$1")):0;separatorHeight=styles.borderTopWidth?Number(styles.borderTopWidth.replace(/\W*(\w)\w*/g,"$1")):0}}height++}}var paddingTop=this.$dropdown_content.css("padding-top")?Number(this.$dropdown_content.css("padding-top").replace(/\W*(\w)\w*/g,"$1")):0;var paddingBottom=this.$dropdown_content.css("padding-bottom")?Number(this.$dropdown_content.css("padding-bottom").replace(/\W*(\w)\w*/g,"$1")):0;height=totalHeight+paddingTop+paddingBottom+marginTop+marginBottom+separatorHeight+"px"}else if(this.settings.dropdownSize.sizeType!=="fixedHeight"){console.warn('Selectize.js - Value of "sizeType" must be "fixedHeight" or "numberItems');return}this.$dropdown_content.css({height:height,maxHeight:"none"})}},clear:function(silent){var self=this;if(!self.items.length)return;self.$control.children(":not(input)").remove();self.items=[];self.lastQuery=null;self.setCaret(0);self.setActiveItem(null);self.updatePlaceholder();self.updateOriginalInput({silent:silent});self.refreshState();self.showInput();self.trigger("clear")},insertAtCaret:function($el){var caret=Math.min(this.caretPos,this.items.length);var el=$el[0];var target=this.buffer||this.$control[0];if(caret===0){target.insertBefore(el,target.firstChild)}else{target.insertBefore(el,target.childNodes[caret])}this.setCaret(caret+1)},deleteSelection:function(e){var i,n,direction,selection,values,caret,option_select,$option_select,$tail;var self=this;direction=e&&e.keyCode===KEY_BACKSPACE?-1:1;selection=getInputSelection(self.$control_input[0]);if(self.$activeOption&&!self.settings.hideSelected){if(typeof self.settings.deselectBehavior==="string"&&self.settings.deselectBehavior==="top"){option_select=self.getFirstOption().attr("data-value")}else{option_select=self.getAdjacentOption(self.$activeOption,-1).attr("data-value")}}values=[];if(self.$activeItems.length){$tail=self.$control.children(".active:"+(direction>0?"last":"first"));caret=self.$control.children(":not(input)").index($tail);if(direction>0){caret++}for(i=0,n=self.$activeItems.length;i<n;i++){values.push($(self.$activeItems[i]).attr("data-value"))}if(e){e.preventDefault();e.stopPropagation()}}else if((self.isFocused||self.settings.mode==="single")&&self.items.length){if(direction<0&&selection.start===0&&selection.length===0){values.push(self.items[self.caretPos-1])}else if(direction>0&&selection.start===self.$control_input.val().length){values.push(self.items[self.caretPos])}}if(!values.length||typeof self.settings.onDelete==="function"&&self.settings.onDelete.apply(self,[values])===false){return false}if(typeof caret!=="undefined"){self.setCaret(caret)}while(values.length){self.removeItem(values.pop())}self.showInput();self.positionDropdown();self.refreshOptions(true);if(option_select){$option_select=self.getOption(option_select);if($option_select.length){self.setActiveOption($option_select)}}return true},advanceSelection:function(direction,e){var tail,selection,idx,valueLength,cursorAtEdge,$tail;var self=this;if(direction===0)return;if(self.rtl)direction*=-1;tail=direction>0?"last":"first";selection=getInputSelection(self.$control_input[0]);if(self.isFocused&&!self.isInputHidden){valueLength=self.$control_input.val().length;cursorAtEdge=direction<0?selection.start===0&&selection.length===0:selection.start===valueLength;if(cursorAtEdge&&!valueLength){self.advanceCaret(direction,e)}}else{$tail=self.$control.children(".active:"+tail);if($tail.length){idx=self.$control.children(":not(input)").index($tail);self.setActiveItem(null);self.setCaret(direction>0?idx+1:idx)}}},advanceCaret:function(direction,e){var self=this,fn,$adj;if(direction===0)return;fn=direction>0?"next":"prev";if(self.isShiftDown){$adj=self.$control_input[fn]();if($adj.length){self.hideInput();self.setActiveItem($adj);e&&e.preventDefault()}}else{self.setCaret(self.caretPos+direction)}},setCaret:function(i){var self=this;if(self.settings.mode==="single"){i=self.items.length}else{i=Math.max(0,Math.min(self.items.length,i))}if(!self.isPending){var j,n,fn,$children,$child;$children=self.$control.children(":not(input)");for(j=0,n=$children.length;j<n;j++){$child=$($children[j]).detach();if(j<i){self.$control_input.before($child)}else{self.$control.append($child)}}}self.caretPos=i},lock:function(){this.close();this.isLocked=true;this.refreshState()},unlock:function(){this.isLocked=false;this.refreshState()},disable:function(){var self=this;self.$input.prop("disabled",true);self.$control_input.prop("disabled",true).prop("tabindex",-1);self.isDisabled=true;self.lock()},enable:function(){var self=this;self.$input.prop("disabled",false);self.$control_input.prop("disabled",false).prop("tabindex",self.tabIndex);self.isDisabled=false;self.unlock()},destroy:function(){var self=this;var eventNS=self.eventNS;var revertSettings=self.revertSettings;self.trigger("destroy");self.off();self.$wrapper.remove();self.$dropdown.remove();self.$input.html("").append(revertSettings.$children).removeAttr("tabindex").removeClass("selectized").attr({tabindex:revertSettings.tabindex}).show();self.$control_input.removeData("grow");self.$input.removeData("selectize");if(--Selectize.count==0&&Selectize.$testInput){Selectize.$testInput.remove();Selectize.$testInput=undefined}$(window).off(eventNS);$(document).off(eventNS);$(document.body).off(eventNS);delete self.$input[0].selectize},render:function(templateName,data){var value,id,label;var html="";var cache=false;var self=this;var regex_tag=/^[\t \r\n]*<([a-z][a-z0-9\-_]*(?:\:[a-z][a-z0-9\-_]*)?)/i;if(templateName==="option"||templateName==="item"){value=hash_key(data[self.settings.valueField]);cache=!!value}if(cache){if(!isset(self.renderCache[templateName])){self.renderCache[templateName]={}}if(self.renderCache[templateName].hasOwnProperty(value)){return self.renderCache[templateName][value]}}html=$(self.settings.render[templateName].apply(this,[data,escape_html]));if(templateName==="option"||templateName==="option_create"){if(!data[self.settings.disabledField]){html.attr("data-selectable","")}}else if(templateName==="optgroup"){id=data[self.settings.optgroupValueField]||"";html.attr("data-group",id);if(data[self.settings.disabledField]){html.attr("data-disabled","")}}if(templateName==="option"||templateName==="item"){html.attr("data-value",value||"")}if(cache){self.renderCache[templateName][value]=html[0]}return html[0]},clearCache:function(templateName){var self=this;if(typeof templateName==="undefined"){self.renderCache={}}else{delete self.renderCache[templateName]}},canCreate:function(input){var self=this;if(!self.settings.create)return false;var filter=self.settings.createFilter;return input.length&&(typeof filter!=="function"||filter.apply(self,[input]))&&(typeof filter!=="string"||new RegExp(filter).test(input))&&(!(filter instanceof RegExp)||filter.test(input))}});Selectize.count=0;Selectize.defaults={options:[],optgroups:[],plugins:[],delimiter:",",splitOn:null,persist:true,diacritics:true,create:false,showAddOptionOnCreate:true,createOnBlur:false,createFilter:null,highlight:true,openOnFocus:true,maxOptions:1e3,maxItems:null,hideSelected:null,addPrecedence:false,selectOnTab:true,preload:false,allowEmptyOption:false,showEmptyOptionInDropdown:false,emptyOptionLabel:"--",setFirstOptionActive:false,closeAfterSelect:false,closeDropdownThreshold:250,scrollDuration:60,deselectBehavior:"previous",loadThrottle:300,loadingClass:"loading",dataAttr:"data-data",optgroupField:"optgroup",valueField:"value",labelField:"text",disabledField:"disabled",optgroupLabelField:"label",optgroupValueField:"value",lockOptgroupOrder:false,sortField:"$order",searchField:["text"],searchConjunction:"and",respect_word_boundaries:true,mode:null,wrapperClass:"",inputClass:"",dropdownClass:"",dropdownContentClass:"",dropdownParent:null,copyClassesToDropdown:true,dropdownSize:{sizeType:"auto",sizeValue:"auto"},normalize:false,ignoreOnDropwdownHeight:"img, i",search:true,render:{}};$.fn.selectize=function(settings_user){var defaults=$.fn.selectize.defaults;var settings=$.extend({},defaults,settings_user);var attr_data=settings.dataAttr;var field_label=settings.labelField;var field_value=settings.valueField;var field_disabled=settings.disabledField;var field_optgroup=settings.optgroupField;var field_optgroup_label=settings.optgroupLabelField;var field_optgroup_value=settings.optgroupValueField;var init_textbox=function($input,settings_element){var i,n,values,option;var data_raw=$input.attr(attr_data);if(!data_raw){var value=($input.val()||"").trim();if(!settings.allowEmptyOption&&!value.length)return;values=value.split(settings.delimiter);for(i=0,n=values.length;i<n;i++){option={};option[field_label]=values[i];option[field_value]=values[i];settings_element.options.push(option)}settings_element.items=values}else{settings_element.options=JSON.parse(data_raw);for(i=0,n=settings_element.options.length;i<n;i++){settings_element.items.push(settings_element.options[i][field_value])}}};var init_select=function($input,settings_element){var i,n,tagName,$children,order=0;var options=settings_element.options;var optionsMap={};var readData=function($el){var data=attr_data&&$el.attr(attr_data);var allData=$el.data();var obj={};if(typeof data==="string"&&data.length){if(isJSON(data)){Object.assign(obj,JSON.parse(data))}else{obj[data]=data}}Object.assign(obj,allData);return obj||null};var addOption=function($option,group){$option=$($option);var value=hash_key($option.val());if(!value&&!settings.allowEmptyOption)return;if(optionsMap.hasOwnProperty(value)){if(group){var arr=optionsMap[value][field_optgroup];if(!arr){optionsMap[value][field_optgroup]=group}else if(!Array.isArray(arr)){optionsMap[value][field_optgroup]=[arr,group]}else{arr.push(group)}}return}var option=readData($option)||{};option[field_label]=option[field_label]||$option.text();option[field_value]=option[field_value]||value;option[field_disabled]=option[field_disabled]||$option.prop("disabled");option[field_optgroup]=option[field_optgroup]||group;option.styles=$option.attr("style")||"";option.classes=$option.attr("class")||"";optionsMap[value]=option;options.push(option);if($option.is(":selected")){settings_element.items.push(value)}};var addGroup=function($optgroup){var i,n,id,optgroup,$options;$optgroup=$($optgroup);id=$optgroup.attr("label");if(id){optgroup=readData($optgroup)||{};optgroup[field_optgroup_label]=id;optgroup[field_optgroup_value]=id;optgroup[field_disabled]=$optgroup.prop("disabled");settings_element.optgroups.push(optgroup)}$options=$("option",$optgroup);for(i=0,n=$options.length;i<n;i++){addOption($options[i],id)}};settings_element.maxItems=$input.attr("multiple")?null:1;$children=$input.children();for(i=0,n=$children.length;i<n;i++){tagName=$children[i].tagName.toLowerCase();if(tagName==="optgroup"){addGroup($children[i])}else if(tagName==="option"){addOption($children[i])}}};return this.each(function(){if(this.selectize)return;var instance;var $input=$(this);var tag_name=this.tagName.toLowerCase();var placeholder=$input.attr("placeholder")||$input.attr("data-placeholder");if(!placeholder&&!settings.allowEmptyOption){placeholder=$input.children('option[value=""]').text()}if(settings.allowEmptyOption&&settings.showEmptyOptionInDropdown&&!$input.children('option[value=""]').length){var input_html=$input.html();var label=escape_html(settings.emptyOptionLabel||"--");$input.html('<option value="">'+label+"</option>"+input_html)}var settings_element={placeholder:placeholder,options:[],optgroups:[],items:[]};if(tag_name==="select"){init_select($input,settings_element)}else{init_textbox($input,settings_element)}instance=new Selectize($input,$.extend(true,{},defaults,settings_element,settings_user));instance.settings_user=settings_user})};$.fn.selectize.defaults=Selectize.defaults;$.fn.selectize.support={validity:SUPPORTS_VALIDITY_API};Selectize.define("auto_position",function(){var self=this;const POSITION={top:"top",bottom:"bottom"};self.positionDropdown=function(){return function(){const $control=this.$control;const offset=this.settings.dropdownParent==="body"?$control.offset():$control.position();offset.top+=$control.outerHeight(true);const dropdownHeight=this.$dropdown.prop("scrollHeight")+5;const controlPosTop=this.$control.get(0).getBoundingClientRect().top;const wrapperHeight=this.$wrapper.height();const position=controlPosTop+dropdownHeight+wrapperHeight>window.innerHeight?POSITION.top:POSITION.bottom;const styles={width:$control.outerWidth(),left:offset.left};if(position===POSITION.top){const styleToAdd={bottom:offset.top,top:"unset"};if(this.settings.dropdownParent==="body"){styleToAdd.top=offset.top-this.$dropdown.outerHeight(true)-$control.outerHeight(true);styleToAdd.bottom="unset"}Object.assign(styles,styleToAdd);this.$dropdown.addClass("selectize-position-top");this.$control.addClass("selectize-position-top")}else{Object.assign(styles,{top:offset.top,bottom:"unset"});this.$dropdown.removeClass("selectize-position-top");this.$control.removeClass("selectize-position-top")}this.$dropdown.css(styles)}}()});Selectize.define("auto_select_on_type",function(options){var self=this;self.onBlur=function(){var originalBlur=self.onBlur;return function(e){var $matchedItem=self.getFirstItemMatchedByTextContent(self.lastValue,true);if(typeof $matchedItem.attr("data-value")!=="undefined"&&self.getValue()!==$matchedItem.attr("data-value")){self.setValue($matchedItem.attr("data-value"))}return originalBlur.apply(this,arguments)}}()});Selectize.define("autofill_disable",function(options){var self=this;self.setup=function(){var original=self.setup;return function(){original.apply(self,arguments);self.$control_input.attr({autocomplete:"new-password",autofill:"no"})}}()});Selectize.define("clear_button",function(options){var self=this;options=$.extend({title:"Clear",className:"clear",label:"×",html:function(data){return'<a class="'+data.className+'" title="'+data.title+'"> '+data.label+"</a>"}},options);self.setup=function(){var original=self.setup;return function(){original.apply(self,arguments);self.$button_clear=$(options.html(options));if(self.settings.mode==="single")self.$wrapper.addClass("single");self.$wrapper.append(self.$button_clear);if(self.getValue()===""||self.getValue().length===0){self.$wrapper.find("."+options.className).css("display","none")}self.on("change",function(){if(self.getValue()===""||self.getValue().length===0){self.$wrapper.find("."+options.className).css("display","none")}else{self.$wrapper.find("."+options.className).css("display","")}});self.$wrapper.on("click","."+options.className,function(e){e.preventDefault();e.stopImmediatePropagation();e.stopPropagation();if(self.isLocked)return;self.clear();self.$wrapper.find("."+options.className).css("display","none")})}}()});Selectize.define("drag_drop",function(options){if(!$.fn.sortable)throw new Error('The "drag_drop" plugin requires jQuery UI "sortable".');if(this.settings.mode!=="multi")return;var self=this;self.lock=function(){var original=self.lock;return function(){var sortable=self.$control.data("sortable");if(sortable)sortable.disable();return original.apply(self,arguments)}}();self.unlock=function(){var original=self.unlock;return function(){var sortable=self.$control.data("sortable");if(sortable)sortable.enable();return original.apply(self,arguments)}}();self.setup=function(){var original=self.setup;return function(){original.apply(this,arguments);var $control=self.$control.sortable({items:"[data-value]",forcePlaceholderSize:true,disabled:self.isLocked,start:function(e,ui){ui.placeholder.css("width",ui.helper.css("width"));$control.addClass("dragging")},stop:function(){$control.removeClass("dragging");var active=self.$activeItems?self.$activeItems.slice():null;var values=[];$control.children("[data-value]").each(function(){values.push($(this).attr("data-value"))});self.isFocused=false;self.setValue(values);self.isFocused=true;self.setActiveItem(active);self.positionDropdown()}})}}()});Selectize.define("dropdown_header",function(options){var self=this;options=$.extend({title:"Untitled",headerClass:"selectize-dropdown-header",titleRowClass:"selectize-dropdown-header-title",labelClass:"selectize-dropdown-header-label",closeClass:"selectize-dropdown-header-close",html:function(data){return'<div class="'+data.headerClass+'">'+'<div class="'+data.titleRowClass+'">'+'<span class="'+data.labelClass+'">'+data.title+"</span>"+'<a href="javascript:void(0)" class="'+data.closeClass+'">&#xd7;</a>'+"</div>"+"</div>"}},options);self.setup=function(){var original=self.setup;return function(){original.apply(self,arguments);self.$dropdown_header=$(options.html(options));self.$dropdown.prepend(self.$dropdown_header);self.$dropdown_header.find("."+options.closeClass).on("click",function(){self.close()})}}()});Selectize.define("optgroup_columns",function(options){var self=this;options=$.extend({equalizeWidth:true,equalizeHeight:true},options);this.getAdjacentOption=function($option,direction){var $options=$option.closest("[data-group]").find("[data-selectable]");var index=$options.index($option)+direction;return index>=0&&index<$options.length?$options.eq(index):$()};this.onKeyDown=function(){var original=self.onKeyDown;return function(e){var index,$option,$options,$optgroup;if(this.isOpen&&(e.keyCode===KEY_LEFT||e.keyCode===KEY_RIGHT)){self.ignoreHover=true;$optgroup=this.$activeOption.closest("[data-group]");index=$optgroup.find("[data-selectable]").index(this.$activeOption);if(e.keyCode===KEY_LEFT){$optgroup=$optgroup.prev("[data-group]")}else{$optgroup=$optgroup.next("[data-group]")}$options=$optgroup.find("[data-selectable]");$option=$options.eq(Math.min($options.length-1,index));if($option.length){this.setActiveOption($option)}return}return original.apply(this,arguments)}}();var getScrollbarWidth=function(){var div;var width=getScrollbarWidth.width;var doc=document;if(typeof width==="undefined"){div=doc.createElement("div");div.innerHTML='<div style="width:50px;height:50px;position:absolute;left:-50px;top:-50px;overflow:auto;"><div style="width:1px;height:100px;"></div></div>';div=div.firstChild;doc.body.appendChild(div);width=getScrollbarWidth.width=div.offsetWidth-div.clientWidth;doc.body.removeChild(div)}return width};var equalizeSizes=function(){var i,n,height_max,width,width_last,width_parent,$optgroups;$optgroups=$("[data-group]",self.$dropdown_content);n=$optgroups.length;if(!n||!self.$dropdown_content.width())return;if(options.equalizeHeight){height_max=0;for(i=0;i<n;i++){height_max=Math.max(height_max,$optgroups.eq(i).height())}$optgroups.css({height:height_max})}if(options.equalizeWidth){width_parent=self.$dropdown_content.innerWidth()-getScrollbarWidth();width=Math.round(width_parent/n);$optgroups.css({width:width});if(n>1){width_last=width_parent-width*(n-1);$optgroups.eq(n-1).css({width:width_last})}}};if(options.equalizeHeight||options.equalizeWidth){hook.after(this,"positionDropdown",equalizeSizes);hook.after(this,"refreshOptions",equalizeSizes)}});Selectize.define("remove_button",function(options){if(this.settings.mode==="single")return;options=$.extend({label:"&#xd7;",title:"Remove",className:"remove",append:true},options);var multiClose=function(thisRef,options){var self=thisRef;var html='<a href="javascript:void(0)" class="'+options.className+'" tabindex="-1" title="'+escape_html(options.title)+'">'+options.label+"</a>";var append=function(html_container,html_element){var pos=html_container.search(/(<\/[^>]+>\s*)$/);return html_container.substring(0,pos)+html_element+html_container.substring(pos)};thisRef.setup=function(){var original=self.setup;return function(){if(options.append){var render_item=self.settings.render.item;self.settings.render.item=function(data){return append(render_item.apply(thisRef,arguments),html)}}original.apply(thisRef,arguments);thisRef.$control.on("click","."+options.className,function(e){e.preventDefault();if(self.isLocked)return;var $item=$(e.currentTarget).parent();self.setActiveItem($item);if(self.deleteSelection()){self.setCaret(self.items.length)}return false})}}()};multiClose(this,options)});Selectize.define("restore_on_backspace",function(options){var self=this;options.text=options.text||function(option){return option[this.settings.labelField]};this.onKeyDown=function(){var original=self.onKeyDown;return function(e){var index,option;if(e.keyCode===KEY_BACKSPACE&&this.$control_input.val()===""&&!this.$activeItems.length){index=this.caretPos-1;if(index>=0&&index<this.items.length){option=this.options[this.items[index]];if(this.deleteSelection(e)){this.setTextboxValue(options.text.apply(this,[option]));this.refreshOptions(true)}e.preventDefault();return}}return original.apply(this,arguments)}}()});Selectize.define("select_on_focus",function(options){var self=this;self.on("focus",function(){var originalFocus=self.onFocus;return function(e){var value=self.getItem(self.getValue()).text();self.clear();self.setTextboxValue(value);self.$control_input.select();setTimeout(function(){if(self.settings.selectOnTab){self.setActiveOption(self.getFirstItemMatchedByTextContent(value))}self.settings.score=null},0);return originalFocus.apply(this,arguments)}}());self.onBlur=function(){var originalBlur=self.onBlur;return function(e){if(self.getValue()===""&&self.lastValidValue!==self.getValue()){self.setValue(self.lastValidValue)}setTimeout(function(){self.settings.score=function(){return function(){return 1}}},0);return originalBlur.apply(this,arguments)}}();self.settings.score=function(){return function(){return 1}}});Selectize.define("tag_limit",function(options){const self=this;options.tagLimit=options.tagLimit;this.onBlur=function(e){const original=self.onBlur;return function(e){original.apply(this,e);if(!e)return;const $control=this.$control;const $items=$control.find(".item");const limit=options.tagLimit;if(limit===undefined||$items.length<=limit)return;$items.toArray().forEach(function(item,index){if(index<limit)return;$(item).hide()});$control.append("<span><b>"+($items.length-limit)+"</b></span>")}}();this.onFocus=function(e){const original=self.onFocus;return function(e){original.apply(this,e);if(!e)return;const $control=this.$control;const $items=$control.find(".item");$items.show();$control.find("span").remove()}}()});return Selectize});

/*!
 * selectize click2deselect (custom)
 */
Selectize.define("click2deselect",function(options){var self=this;var setup=self.setup;this.setup=function(){setup.apply(self,arguments);let just_added;self.$dropdown.each(function(){this.addEventListener("click",function(e){let target=e.target.matches(".selected[data-selectable]")?e.target:e.target.closest(".selected[data-selectable]");if(target!==null){let value=target.getAttribute("data-value");if(value!==just_added){self.removeItem(value);self.refreshItems();self.refreshOptions()}}just_added=false;return false})});self.on("item_remove",function(value){self.getOption(value).removeClass("selected")});self.on("item_add",function(value){just_added=value})}});

/*!
 * International Telephone Input v18.2.1
 * https://github.com/jackocnr/intl-tel-input.git
 */
(function(factory){if(typeof module==="object"&&module.exports)module.exports=factory();else window.intlTelInput=factory()})(function(undefined){"use strict";return function(){var allCountries=[["Afghanistan (‫افغانستان‬‎)","af","93"],["Albania (Shqipëri)","al","355"],["Algeria (‫الجزائر‬‎)","dz","213"],["American Samoa","as","1",5,["684"]],["Andorra","ad","376"],["Angola","ao","244"],["Anguilla","ai","1",6,["264"]],["Antigua and Barbuda","ag","1",7,["268"]],["Argentina","ar","54"],["Armenia (Հայաստան)","am","374"],["Aruba","aw","297"],["Ascension Island","ac","247"],["Australia","au","61",0],["Austria (Österreich)","at","43"],["Azerbaijan (Azərbaycan)","az","994"],["Bahamas","bs","1",8,["242"]],["Bahrain (‫البحرين‬‎)","bh","973"],["Bangladesh (বাংলাদেশ)","bd","880"],["Barbados","bb","1",9,["246"]],["Belarus (Беларусь)","by","375"],["Belgium (België)","be","32"],["Belize","bz","501"],["Benin (Bénin)","bj","229"],["Bermuda","bm","1",10,["441"]],["Bhutan (འབྲུག)","bt","975"],["Bolivia","bo","591"],["Bosnia and Herzegovina (Босна и Херцеговина)","ba","387"],["Botswana","bw","267"],["Brazil (Brasil)","br","55"],["British Indian Ocean Territory","io","246"],["British Virgin Islands","vg","1",11,["284"]],["Brunei","bn","673"],["Bulgaria (България)","bg","359"],["Burkina Faso","bf","226"],["Burundi (Uburundi)","bi","257"],["Cambodia (កម្ពុជា)","kh","855"],["Cameroon (Cameroun)","cm","237"],["Canada","ca","1",1,["204","226","236","249","250","263","289","306","343","354","365","367","368","382","387","403","416","418","428","431","437","438","450","584","468","474","506","514","519","548","579","581","584","587","604","613","639","647","672","683","705","709","742","753","778","780","782","807","819","825","867","873","902","905"]],["Cape Verde (Kabu Verdi)","cv","238"],["Caribbean Netherlands","bq","599",1,["3","4","7"]],["Cayman Islands","ky","1",12,["345"]],["Central African Republic (République centrafricaine)","cf","236"],["Chad (Tchad)","td","235"],["Chile","cl","56"],["China (中国)","cn","86"],["Christmas Island","cx","61",2,["89164"]],["Cocos (Keeling) Islands","cc","61",1,["89162"]],["Colombia","co","57"],["Comoros (‫جزر القمر‬‎)","km","269"],["Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)","cd","243"],["Congo (Republic) (Congo-Brazzaville)","cg","242"],["Cook Islands","ck","682"],["Costa Rica","cr","506"],["Côte d’Ivoire","ci","225"],["Croatia (Hrvatska)","hr","385"],["Cuba","cu","53"],["Curaçao","cw","599",0],["Cyprus (Κύπρος)","cy","357"],["Czech Republic (Česká republika)","cz","420"],["Denmark (Danmark)","dk","45"],["Djibouti","dj","253"],["Dominica","dm","1",13,["767"]],["Dominican Republic (República Dominicana)","do","1",2,["809","829","849"]],["Ecuador","ec","593"],["Egypt (‫مصر‬‎)","eg","20"],["El Salvador","sv","503"],["Equatorial Guinea (Guinea Ecuatorial)","gq","240"],["Eritrea","er","291"],["Estonia (Eesti)","ee","372"],["Eswatini","sz","268"],["Ethiopia","et","251"],["Falkland Islands (Islas Malvinas)","fk","500"],["Faroe Islands (Føroyar)","fo","298"],["Fiji","fj","679"],["Finland (Suomi)","fi","358",0],["France","fr","33"],["French Guiana (Guyane française)","gf","594"],["French Polynesia (Polynésie française)","pf","689"],["Gabon","ga","241"],["Gambia","gm","220"],["Georgia (საქართველო)","ge","995"],["Germany (Deutschland)","de","49"],["Ghana (Gaana)","gh","233"],["Gibraltar","gi","350"],["Greece (Ελλάδα)","gr","30"],["Greenland (Kalaallit Nunaat)","gl","299"],["Grenada","gd","1",14,["473"]],["Guadeloupe","gp","590",0],["Guam","gu","1",15,["671"]],["Guatemala","gt","502"],["Guernsey","gg","44",1,["1481","7781","7839","7911"]],["Guinea (Guinée)","gn","224"],["Guinea-Bissau (Guiné Bissau)","gw","245"],["Guyana","gy","592"],["Haiti","ht","509"],["Honduras","hn","504"],["Hong Kong (香港)","hk","852"],["Hungary (Magyarország)","hu","36"],["Iceland (Ísland)","is","354"],["India (भारत)","in","91"],["Indonesia","id","62"],["Iran (‫ایران‬‎)","ir","98"],["Iraq (‫العراق‬‎)","iq","964"],["Ireland","ie","353"],["Isle of Man","im","44",2,["1624","74576","7524","7924","7624"]],["Israel (‫ישראל‬‎)","il","972"],["Italy (Italia)","it","39",0],["Jamaica","jm","1",4,["876","658"]],["Japan (日本)","jp","81"],["Jersey","je","44",3,["1534","7509","7700","7797","7829","7937"]],["Jordan (‫الأردن‬‎)","jo","962"],["Kazakhstan (Казахстан)","kz","7",1,["33","7"]],["Kenya","ke","254"],["Kiribati","ki","686"],["Kosovo","xk","383"],["Kuwait (‫الكويت‬‎)","kw","965"],["Kyrgyzstan (Кыргызстан)","kg","996"],["Laos (ລາວ)","la","856"],["Latvia (Latvija)","lv","371"],["Lebanon (‫لبنان‬‎)","lb","961"],["Lesotho","ls","266"],["Liberia","lr","231"],["Libya (‫ليبيا‬‎)","ly","218"],["Liechtenstein","li","423"],["Lithuania (Lietuva)","lt","370"],["Luxembourg","lu","352"],["Macau (澳門)","mo","853"],["Madagascar (Madagasikara)","mg","261"],["Malawi","mw","265"],["Malaysia","my","60"],["Maldives","mv","960"],["Mali","ml","223"],["Malta","mt","356"],["Marshall Islands","mh","692"],["Martinique","mq","596"],["Mauritania (‫موريتانيا‬‎)","mr","222"],["Mauritius (Moris)","mu","230"],["Mayotte","yt","262",1,["269","639"]],["Mexico (México)","mx","52"],["Micronesia","fm","691"],["Moldova (Republica Moldova)","md","373"],["Monaco","mc","377"],["Mongolia (Монгол)","mn","976"],["Montenegro (Crna Gora)","me","382"],["Montserrat","ms","1",16,["664"]],["Morocco (‫المغرب‬‎)","ma","212",0],["Mozambique (Moçambique)","mz","258"],["Myanmar (Burma) (မြန်မာ)","mm","95"],["Namibia (Namibië)","na","264"],["Nauru","nr","674"],["Nepal (नेपाल)","np","977"],["Netherlands (Nederland)","nl","31"],["New Caledonia (Nouvelle-Calédonie)","nc","687"],["New Zealand","nz","64"],["Nicaragua","ni","505"],["Niger (Nijar)","ne","227"],["Nigeria","ng","234"],["Niue","nu","683"],["Norfolk Island","nf","672"],["North Korea (조선 민주주의 인민 공화국)","kp","850"],["North Macedonia (Северна Македонија)","mk","389"],["Northern Mariana Islands","mp","1",17,["670"]],["Norway (Norge)","no","47",0],["Oman (‫عُمان‬‎)","om","968"],["Pakistan (‫پاکستان‬‎)","pk","92"],["Palau","pw","680"],["Palestine (‫فلسطين‬‎)","ps","970"],["Panama (Panamá)","pa","507"],["Papua New Guinea","pg","675"],["Paraguay","py","595"],["Peru (Perú)","pe","51"],["Philippines","ph","63"],["Poland (Polska)","pl","48"],["Portugal","pt","351"],["Puerto Rico","pr","1",3,["787","939"]],["Qatar (‫قطر‬‎)","qa","974"],["Réunion (La Réunion)","re","262",0],["Romania (România)","ro","40"],["Russia (Россия)","ru","7",0],["Rwanda","rw","250"],["Saint Barthélemy","bl","590",1],["Saint Helena","sh","290"],["Saint Kitts and Nevis","kn","1",18,["869"]],["Saint Lucia","lc","1",19,["758"]],["Saint Martin (Saint-Martin (partie française))","mf","590",2],["Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)","pm","508"],["Saint Vincent and the Grenadines","vc","1",20,["784"]],["Samoa","ws","685"],["San Marino","sm","378"],["São Tomé and Príncipe (São Tomé e Príncipe)","st","239"],["Saudi Arabia (‫المملكة العربية السعودية‬‎)","sa","966"],["Senegal (Sénégal)","sn","221"],["Serbia (Србија)","rs","381"],["Seychelles","sc","248"],["Sierra Leone","sl","232"],["Singapore","sg","65"],["Sint Maarten","sx","1",21,["721"]],["Slovakia (Slovensko)","sk","421"],["Slovenia (Slovenija)","si","386"],["Solomon Islands","sb","677"],["Somalia (Soomaaliya)","so","252"],["South Africa","za","27"],["South Korea (대한민국)","kr","82"],["South Sudan (‫جنوب السودان‬‎)","ss","211"],["Spain (España)","es","34"],["Sri Lanka (ශ්‍රී ලංකාව)","lk","94"],["Sudan (‫السودان‬‎)","sd","249"],["Suriname","sr","597"],["Svalbard and Jan Mayen","sj","47",1,["79"]],["Sweden (Sverige)","se","46"],["Switzerland (Schweiz)","ch","41"],["Syria (‫سوريا‬‎)","sy","963"],["Taiwan (台灣)","tw","886"],["Tajikistan","tj","992"],["Tanzania","tz","255"],["Thailand (ไทย)","th","66"],["Timor-Leste","tl","670"],["Togo","tg","228"],["Tokelau","tk","690"],["Tonga","to","676"],["Trinidad and Tobago","tt","1",22,["868"]],["Tunisia (‫تونس‬‎)","tn","216"],["Turkey (Türkiye)","tr","90"],["Turkmenistan","tm","993"],["Turks and Caicos Islands","tc","1",23,["649"]],["Tuvalu","tv","688"],["U.S. Virgin Islands","vi","1",24,["340"]],["Uganda","ug","256"],["Ukraine (Україна)","ua","380"],["United Arab Emirates (‫الإمارات العربية المتحدة‬‎)","ae","971"],["United Kingdom","gb","44",0],["United States","us","1",0],["Uruguay","uy","598"],["Uzbekistan (Oʻzbekiston)","uz","998"],["Vanuatu","vu","678"],["Vatican City (Città del Vaticano)","va","39",1,["06698"]],["Venezuela","ve","58"],["Vietnam (Việt Nam)","vn","84"],["Wallis and Futuna (Wallis-et-Futuna)","wf","681"],["Western Sahara (‫الصحراء الغربية‬‎)","eh","212",1,["5288","5289"]],["Yemen (‫اليمن‬‎)","ye","967"],["Zambia","zm","260"],["Zimbabwe","zw","263"],["Åland Islands","ax","358",1,["18"]]];for(var i=0;i<allCountries.length;i++){var c=allCountries[i];allCountries[i]={name:c[0],iso2:c[1],dialCode:c[2],priority:c[3]||0,areaCodes:c[4]||null}}"use strict";function _objectSpread(target){for(var i=1;i<arguments.length;i++){var source=arguments[i]!=null?Object(arguments[i]):{};var ownKeys=Object.keys(source);if(typeof Object.getOwnPropertySymbols==="function"){ownKeys.push.apply(ownKeys,Object.getOwnPropertySymbols(source).filter(function(sym){return Object.getOwnPropertyDescriptor(source,sym).enumerable}))}ownKeys.forEach(function(key){_defineProperty(target,key,source[key])})}return target}function _defineProperty(obj,key,value){key=_toPropertyKey(key);if(key in obj){Object.defineProperty(obj,key,{value:value,enumerable:true,configurable:true,writable:true})}else{obj[key]=value}return obj}function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor)){throw new TypeError("Cannot call a class as a function")}}function _defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||false;descriptor.configurable=true;if("value"in descriptor)descriptor.writable=true;Object.defineProperty(target,_toPropertyKey(descriptor.key),descriptor)}}function _createClass(Constructor,protoProps,staticProps){if(protoProps)_defineProperties(Constructor.prototype,protoProps);if(staticProps)_defineProperties(Constructor,staticProps);Object.defineProperty(Constructor,"prototype",{writable:false});return Constructor}function _toPropertyKey(arg){var key=_toPrimitive(arg,"string");return typeof key==="symbol"?key:String(key)}function _toPrimitive(input,hint){if(typeof input!=="object"||input===null)return input;var prim=input[Symbol.toPrimitive];if(prim!==undefined){var res=prim.call(input,hint||"default");if(typeof res!=="object")return res;throw new TypeError("@@toPrimitive must return a primitive value.")}return(hint==="string"?String:Number)(input)}var intlTelInputGlobals={getInstance:function getInstance(input){var id=input.getAttribute("data-intl-tel-input-id");return window.intlTelInputGlobals.instances[id]},instances:{},documentReady:function documentReady(){return document.readyState==="complete"}};if(typeof window==="object"){window.intlTelInputGlobals=intlTelInputGlobals}var id=0;var defaults={allowDropdown:true,autoInsertDialCode:false,autoPlaceholder:"polite",customContainer:"",customPlaceholder:null,dropdownContainer:null,excludeCountries:[],formatOnDisplay:true,geoIpLookup:null,hiddenInput:"",initialCountry:"",localizedCountries:null,nationalMode:true,onlyCountries:[],placeholderNumberType:"MOBILE",preferredCountries:["us","gb"],separateDialCode:false,showFlags:true,utilsScript:""};var regionlessNanpNumbers=["800","822","833","844","855","866","877","880","881","882","883","884","885","886","887","888","889"];var forEachProp=function forEachProp(obj,callback){var keys=Object.keys(obj);for(var i=0;i<keys.length;i++){callback(keys[i],obj[keys[i]])}};var forEachInstance=function forEachInstance(method){forEachProp(window.intlTelInputGlobals.instances,function(key){window.intlTelInputGlobals.instances[key][method]()})};var Iti=function(){function Iti(input,options){var _this=this;_classCallCheck(this,Iti);this.id=id++;this.telInput=input;this.activeItem=null;this.highlightedItem=null;var customOptions=options||{};this.options={};forEachProp(defaults,function(key,value){_this.options[key]=customOptions.hasOwnProperty(key)?customOptions[key]:value});this.hadInitialPlaceholder=Boolean(input.getAttribute("placeholder"))}_createClass(Iti,[{key:"_init",value:function _init(){var _this2=this;if(this.options.nationalMode){this.options.autoInsertDialCode=false}if(this.options.separateDialCode){this.options.autoInsertDialCode=false}var forceShowFlags=this.options.allowDropdown&&!this.options.separateDialCode;if(!this.options.showFlags&&forceShowFlags){this.options.showFlags=true}this.isMobile=/Android.+Mobile|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);if(this.isMobile){document.body.classList.add("iti-mobile");if(!this.options.dropdownContainer){this.options.dropdownContainer=document.body}}this.isRTL=!!this.telInput.closest("[dir=rtl]");if(typeof Promise!=="undefined"){var autoCountryPromise=new Promise(function(resolve,reject){_this2.resolveAutoCountryPromise=resolve;_this2.rejectAutoCountryPromise=reject});var utilsScriptPromise=new Promise(function(resolve,reject){_this2.resolveUtilsScriptPromise=resolve;_this2.rejectUtilsScriptPromise=reject});this.promise=Promise.all([autoCountryPromise,utilsScriptPromise])}else{this.resolveAutoCountryPromise=this.rejectAutoCountryPromise=function(){};this.resolveUtilsScriptPromise=this.rejectUtilsScriptPromise=function(){}}this.selectedCountryData={};this._processCountryData();this._generateMarkup();this._setInitialState();this._initListeners();this._initRequests()}},{key:"_processCountryData",value:function _processCountryData(){this._processAllCountries();this._processCountryCodes();this._processPreferredCountries();if(this.options.localizedCountries){this._translateCountriesByLocale()}if(this.options.onlyCountries.length||this.options.localizedCountries){this.countries.sort(this._countryNameSort)}}},{key:"_addCountryCode",value:function _addCountryCode(iso2,countryCode,priority){if(countryCode.length>this.countryCodeMaxLen){this.countryCodeMaxLen=countryCode.length}if(!this.countryCodes.hasOwnProperty(countryCode)){this.countryCodes[countryCode]=[]}for(var i=0;i<this.countryCodes[countryCode].length;i++){if(this.countryCodes[countryCode][i]===iso2){return}}var index=priority!==undefined?priority:this.countryCodes[countryCode].length;this.countryCodes[countryCode][index]=iso2}},{key:"_processAllCountries",value:function _processAllCountries(){if(this.options.onlyCountries.length){var lowerCaseOnlyCountries=this.options.onlyCountries.map(function(country){return country.toLowerCase()});this.countries=allCountries.filter(function(country){return lowerCaseOnlyCountries.indexOf(country.iso2)>-1})}else if(this.options.excludeCountries.length){var lowerCaseExcludeCountries=this.options.excludeCountries.map(function(country){return country.toLowerCase()});this.countries=allCountries.filter(function(country){return lowerCaseExcludeCountries.indexOf(country.iso2)===-1})}else{this.countries=allCountries}}},{key:"_translateCountriesByLocale",value:function _translateCountriesByLocale(){for(var i=0;i<this.countries.length;i++){var iso=this.countries[i].iso2.toLowerCase();if(this.options.localizedCountries.hasOwnProperty(iso)){this.countries[i].name=this.options.localizedCountries[iso]}}}},{key:"_countryNameSort",value:function _countryNameSort(a,b){if(a.name<b.name){return-1}if(a.name>b.name){return 1}return 0}},{key:"_processCountryCodes",value:function _processCountryCodes(){this.countryCodeMaxLen=0;this.dialCodes={};this.countryCodes={};for(var i=0;i<this.countries.length;i++){var c=this.countries[i];if(!this.dialCodes[c.dialCode]){this.dialCodes[c.dialCode]=true}this._addCountryCode(c.iso2,c.dialCode,c.priority)}for(var _i=0;_i<this.countries.length;_i++){var _c=this.countries[_i];if(_c.areaCodes){var rootCountryCode=this.countryCodes[_c.dialCode][0];for(var j=0;j<_c.areaCodes.length;j++){var areaCode=_c.areaCodes[j];for(var k=1;k<areaCode.length;k++){var partialDialCode=_c.dialCode+areaCode.substr(0,k);this._addCountryCode(rootCountryCode,partialDialCode);this._addCountryCode(_c.iso2,partialDialCode)}this._addCountryCode(_c.iso2,_c.dialCode+areaCode)}}}}},{key:"_processPreferredCountries",value:function _processPreferredCountries(){this.preferredCountries=[];for(var i=0;i<this.options.preferredCountries.length;i++){var countryCode=this.options.preferredCountries[i].toLowerCase();var countryData=this._getCountryData(countryCode,false,true);if(countryData){this.preferredCountries.push(countryData)}}}},{key:"_createEl",value:function _createEl(name,attrs,container){var el=document.createElement(name);if(attrs){forEachProp(attrs,function(key,value){return el.setAttribute(key,value)})}if(container){container.appendChild(el)}return el}},{key:"_generateMarkup",value:function _generateMarkup(){if(!this.telInput.hasAttribute("autocomplete")&&!(this.telInput.form&&this.telInput.form.hasAttribute("autocomplete"))){this.telInput.setAttribute("autocomplete","off")}var _this$options=this.options,allowDropdown=_this$options.allowDropdown,separateDialCode=_this$options.separateDialCode,showFlags=_this$options.showFlags,customContainer=_this$options.customContainer,hiddenInput=_this$options.hiddenInput,dropdownContainer=_this$options.dropdownContainer;var parentClass="iti";if(allowDropdown){parentClass+=" iti--allow-dropdown"}if(separateDialCode){parentClass+=" iti--separate-dial-code"}if(showFlags){parentClass+=" iti--show-flags"}if(customContainer){parentClass+=" ".concat(customContainer)}var wrapper=this._createEl("div",{class:parentClass});this.telInput.parentNode.insertBefore(wrapper,this.telInput);var showFlagsContainer=allowDropdown||showFlags||separateDialCode;if(showFlagsContainer){this.flagsContainer=this._createEl("div",{class:"iti__flag-container"},wrapper)}wrapper.appendChild(this.telInput);if(showFlagsContainer){this.selectedFlag=this._createEl("div",_objectSpread({class:"iti__selected-flag"},allowDropdown&&{role:"combobox","aria-haspopup":"listbox","aria-controls":"iti-".concat(this.id,"__country-listbox"),"aria-expanded":"false","aria-label":"Telephone country code"}),this.flagsContainer)}if(showFlags){this.selectedFlagInner=this._createEl("div",{class:"iti__flag"},this.selectedFlag)}if(this.selectedFlag&&this.telInput.disabled){this.selectedFlag.setAttribute("aria-disabled","true")}if(separateDialCode){this.selectedDialCode=this._createEl("div",{class:"iti__selected-dial-code"},this.selectedFlag)}if(allowDropdown){if(!this.telInput.disabled){this.selectedFlag.setAttribute("tabindex","0")}this.dropdownArrow=this._createEl("div",{class:"iti__arrow"},this.selectedFlag);this.countryList=this._createEl("ul",{class:"iti__country-list iti__hide",id:"iti-".concat(this.id,"__country-listbox"),role:"listbox","aria-label":"List of countries"});if(this.preferredCountries.length){this._appendListItems(this.preferredCountries,"iti__preferred",true);this._createEl("li",{class:"iti__divider",role:"separator","aria-disabled":"true"},this.countryList)}this._appendListItems(this.countries,"iti__standard");if(dropdownContainer){this.dropdown=this._createEl("div",{class:"iti iti--container"});this.dropdown.appendChild(this.countryList)}else{this.flagsContainer.appendChild(this.countryList)}}if(hiddenInput){var hiddenInputName=hiddenInput;var name=this.telInput.getAttribute("name");if(name){var i=name.lastIndexOf("[");if(i!==-1){hiddenInputName="".concat(name.substr(0,i),"[").concat(hiddenInputName,"]")}}this.hiddenInput=this._createEl("input",{type:"hidden",name:hiddenInputName});wrapper.appendChild(this.hiddenInput)}}},{key:"_appendListItems",value:function _appendListItems(countries,className,preferred){var tmp="";for(var i=0;i<countries.length;i++){var c=countries[i];var idSuffix=preferred?"-preferred":"";tmp+="<li class='iti__country ".concat(className,"' tabIndex='-1' id='iti-").concat(this.id,"__item-").concat(c.iso2).concat(idSuffix,"' role='option' data-dial-code='").concat(c.dialCode,"' data-country-code='").concat(c.iso2,"' aria-selected='false'>");if(this.options.showFlags){tmp+="<div class='iti__flag-box'><div class='iti__flag iti__".concat(c.iso2,"'></div></div>")}tmp+="<span class='iti__country-name'>".concat(c.name,"</span>");tmp+="<span class='iti__dial-code'>+".concat(c.dialCode,"</span>");tmp+="</li>"}this.countryList.insertAdjacentHTML("beforeend",tmp)}},{key:"_setInitialState",value:function _setInitialState(){var attributeValue=this.telInput.getAttribute("value");var inputValue=this.telInput.value;var useAttribute=attributeValue&&attributeValue.charAt(0)==="+"&&(!inputValue||inputValue.charAt(0)!=="+");var val=useAttribute?attributeValue:inputValue;var dialCode=this._getDialCode(val);var isRegionlessNanp=this._isRegionlessNanp(val);var _this$options2=this.options,initialCountry=_this$options2.initialCountry,autoInsertDialCode=_this$options2.autoInsertDialCode;if(dialCode&&!isRegionlessNanp){this._updateFlagFromNumber(val)}else if(initialCountry!=="auto"){if(initialCountry){this._setFlag(initialCountry.toLowerCase())}else{if(dialCode&&isRegionlessNanp){this._setFlag("us")}else{this.defaultCountry=this.preferredCountries.length?this.preferredCountries[0].iso2:this.countries[0].iso2;if(!val){this._setFlag(this.defaultCountry)}}}if(!val&&autoInsertDialCode){this.telInput.value="+".concat(this.selectedCountryData.dialCode)}}if(val){this._updateValFromNumber(val)}}},{key:"_initListeners",value:function _initListeners(){this._initKeyListeners();if(this.options.autoInsertDialCode){this._initBlurListeners()}if(this.options.allowDropdown){this._initDropdownListeners()}if(this.hiddenInput){this._initHiddenInputListener()}}},{key:"_initHiddenInputListener",value:function _initHiddenInputListener(){var _this3=this;this._handleHiddenInputSubmit=function(){_this3.hiddenInput.value=_this3.getNumber()};if(this.telInput.form){this.telInput.form.addEventListener("submit",this._handleHiddenInputSubmit)}}},{key:"_getClosestLabel",value:function _getClosestLabel(){var el=this.telInput;while(el&&el.tagName!=="LABEL"){el=el.parentNode}return el}},{key:"_initDropdownListeners",value:function _initDropdownListeners(){var _this4=this;this._handleLabelClick=function(e){if(_this4.countryList.classList.contains("iti__hide")){_this4.telInput.focus()}else{e.preventDefault()}};var label=this._getClosestLabel();if(label){label.addEventListener("click",this._handleLabelClick)}this._handleClickSelectedFlag=function(){if(_this4.countryList.classList.contains("iti__hide")&&!_this4.telInput.disabled&&!_this4.telInput.readOnly){_this4._showDropdown()}};this.selectedFlag.addEventListener("click",this._handleClickSelectedFlag);this._handleFlagsContainerKeydown=function(e){var isDropdownHidden=_this4.countryList.classList.contains("iti__hide");if(isDropdownHidden&&["ArrowUp","Up","ArrowDown","Down"," ","Enter"].indexOf(e.key)!==-1){e.preventDefault();e.stopPropagation();_this4._showDropdown()}if(e.key==="Tab"){_this4._closeDropdown()}};this.flagsContainer.addEventListener("keydown",this._handleFlagsContainerKeydown)}},{key:"_initRequests",value:function _initRequests(){var _this5=this;if(this.options.utilsScript&&!window.intlTelInputUtils){if(window.intlTelInputGlobals.documentReady()){window.intlTelInputGlobals.loadUtils(this.options.utilsScript)}else{window.addEventListener("load",function(){window.intlTelInputGlobals.loadUtils(_this5.options.utilsScript)})}}else{this.resolveUtilsScriptPromise()}if(this.options.initialCountry==="auto"){this._loadAutoCountry()}else{this.resolveAutoCountryPromise()}}},{key:"_loadAutoCountry",value:function _loadAutoCountry(){if(window.intlTelInputGlobals.autoCountry){this.handleAutoCountry()}else if(!window.intlTelInputGlobals.startedLoadingAutoCountry){window.intlTelInputGlobals.startedLoadingAutoCountry=true;if(typeof this.options.geoIpLookup==="function"){this.options.geoIpLookup(function(countryCode){window.intlTelInputGlobals.autoCountry=countryCode.toLowerCase();setTimeout(function(){return forEachInstance("handleAutoCountry")})},function(){return forEachInstance("rejectAutoCountryPromise")})}}}},{key:"_initKeyListeners",value:function _initKeyListeners(){var _this6=this;this._handleKeyupEvent=function(){if(_this6._updateFlagFromNumber(_this6.telInput.value)){_this6._triggerCountryChange()}};this.telInput.addEventListener("keyup",this._handleKeyupEvent);this._handleClipboardEvent=function(){setTimeout(_this6._handleKeyupEvent)};this.telInput.addEventListener("cut",this._handleClipboardEvent);this.telInput.addEventListener("paste",this._handleClipboardEvent)}},{key:"_cap",value:function _cap(number){var max=this.telInput.getAttribute("maxlength");return max&&number.length>max?number.substr(0,max):number}},{key:"_initBlurListeners",value:function _initBlurListeners(){var _this7=this;this._handleSubmitOrBlurEvent=function(){_this7._removeEmptyDialCode()};if(this.telInput.form){this.telInput.form.addEventListener("submit",this._handleSubmitOrBlurEvent)}this.telInput.addEventListener("blur",this._handleSubmitOrBlurEvent)}},{key:"_removeEmptyDialCode",value:function _removeEmptyDialCode(){if(this.telInput.value.charAt(0)==="+"){var numeric=this._getNumeric(this.telInput.value);if(!numeric||this.selectedCountryData.dialCode===numeric){this.telInput.value=""}}}},{key:"_getNumeric",value:function _getNumeric(s){return s.replace(/\D/g,"")}},{key:"_trigger",value:function _trigger(name){var e=document.createEvent("Event");e.initEvent(name,true,true);this.telInput.dispatchEvent(e)}},{key:"_showDropdown",value:function _showDropdown(){this.countryList.classList.remove("iti__hide");this.selectedFlag.setAttribute("aria-expanded","true");this._setDropdownPosition();if(this.activeItem){this._highlightListItem(this.activeItem,false);this._scrollTo(this.activeItem,true)}this._bindDropdownListeners();this.dropdownArrow.classList.add("iti__arrow--up");this._trigger("open:countrydropdown")}},{key:"_toggleClass",value:function _toggleClass(el,className,shouldHaveClass){if(shouldHaveClass&&!el.classList.contains(className)){el.classList.add(className)}else if(!shouldHaveClass&&el.classList.contains(className)){el.classList.remove(className)}}},{key:"_setDropdownPosition",value:function _setDropdownPosition(){var _this8=this;if(this.options.dropdownContainer){this.options.dropdownContainer.appendChild(this.dropdown)}if(!this.isMobile){var pos=this.telInput.getBoundingClientRect();var windowTop=window.pageYOffset||document.documentElement.scrollTop;var inputTop=pos.top+windowTop;var dropdownHeight=this.countryList.offsetHeight;var dropdownFitsBelow=inputTop+this.telInput.offsetHeight+dropdownHeight<windowTop+window.innerHeight;var dropdownFitsAbove=inputTop-dropdownHeight>windowTop;this._toggleClass(this.countryList,"iti__country-list--dropup",!dropdownFitsBelow&&dropdownFitsAbove);if(this.options.dropdownContainer){var extraTop=!dropdownFitsBelow&&dropdownFitsAbove?0:this.telInput.offsetHeight;this.dropdown.style.top="".concat(inputTop+extraTop,"px");this.dropdown.style.left="".concat(pos.left+document.body.scrollLeft,"px");this._handleWindowScroll=function(){return _this8._closeDropdown()};window.addEventListener("scroll",this._handleWindowScroll)}}}},{key:"_getClosestListItem",value:function _getClosestListItem(target){var el=target;while(el&&el!==this.countryList&&!el.classList.contains("iti__country")){el=el.parentNode}return el===this.countryList?null:el}},{key:"_bindDropdownListeners",value:function _bindDropdownListeners(){var _this9=this;this._handleMouseoverCountryList=function(e){var listItem=_this9._getClosestListItem(e.target);if(listItem){_this9._highlightListItem(listItem,false)}};this.countryList.addEventListener("mouseover",this._handleMouseoverCountryList);this._handleClickCountryList=function(e){var listItem=_this9._getClosestListItem(e.target);if(listItem){_this9._selectListItem(listItem)}};this.countryList.addEventListener("click",this._handleClickCountryList);var isOpening=true;this._handleClickOffToClose=function(){if(!isOpening){_this9._closeDropdown()}isOpening=false};document.documentElement.addEventListener("click",this._handleClickOffToClose);var query="";var queryTimer=null;this._handleKeydownOnDropdown=function(e){e.preventDefault();if(e.key==="ArrowUp"||e.key==="Up"||e.key==="ArrowDown"||e.key==="Down"){_this9._handleUpDownKey(e.key)}else if(e.key==="Enter"){_this9._handleEnterKey()}else if(e.key==="Escape"){_this9._closeDropdown()}else if(/^[a-zA-ZÀ-ÿа-яА-Я ]$/.test(e.key)){if(queryTimer){clearTimeout(queryTimer)}query+=e.key.toLowerCase();_this9._searchForCountry(query);queryTimer=setTimeout(function(){query=""},1e3)}};document.addEventListener("keydown",this._handleKeydownOnDropdown)}},{key:"_handleUpDownKey",value:function _handleUpDownKey(key){var next=key==="ArrowUp"||key==="Up"?this.highlightedItem.previousElementSibling:this.highlightedItem.nextElementSibling;if(next){if(next.classList.contains("iti__divider")){next=key==="ArrowUp"||key==="Up"?next.previousElementSibling:next.nextElementSibling}this._highlightListItem(next,true)}}},{key:"_handleEnterKey",value:function _handleEnterKey(){if(this.highlightedItem){this._selectListItem(this.highlightedItem)}}},{key:"_searchForCountry",value:function _searchForCountry(query){for(var i=0;i<this.countries.length;i++){if(this._startsWith(this.countries[i].name,query)){var listItem=this.countryList.querySelector("#iti-".concat(this.id,"__item-").concat(this.countries[i].iso2));this._highlightListItem(listItem,false);this._scrollTo(listItem,true);break}}}},{key:"_startsWith",value:function _startsWith(a,b){return a.substr(0,b.length).toLowerCase()===b}},{key:"_updateValFromNumber",value:function _updateValFromNumber(originalNumber){var number=originalNumber;if(this.options.formatOnDisplay&&window.intlTelInputUtils&&this.selectedCountryData){var useNational=this.options.nationalMode||number.charAt(0)!=="+"&&!this.options.separateDialCode;var _intlTelInputUtils$nu=intlTelInputUtils.numberFormat,NATIONAL=_intlTelInputUtils$nu.NATIONAL,INTERNATIONAL=_intlTelInputUtils$nu.INTERNATIONAL;var format=useNational?NATIONAL:INTERNATIONAL;number=intlTelInputUtils.formatNumber(number,this.selectedCountryData.iso2,format)}number=this._beforeSetNumber(number);this.telInput.value=number}},{key:"_updateFlagFromNumber",value:function _updateFlagFromNumber(originalNumber){var number=originalNumber;var selectedDialCode=this.selectedCountryData.dialCode;var isNanp=selectedDialCode==="1";if(number&&isNanp&&number.charAt(0)!=="+"){if(number.charAt(0)!=="1"){number="1".concat(number)}number="+".concat(number)}if(this.options.separateDialCode&&selectedDialCode&&number.charAt(0)!=="+"){number="+".concat(selectedDialCode).concat(number)}var dialCode=this._getDialCode(number,true);var numeric=this._getNumeric(number);var countryCode=null;if(dialCode){var countryCodes=this.countryCodes[this._getNumeric(dialCode)];var alreadySelected=countryCodes.indexOf(this.selectedCountryData.iso2)!==-1&&numeric.length<=dialCode.length-1;var isRegionlessNanpNumber=selectedDialCode==="1"&&this._isRegionlessNanp(numeric);if(!isRegionlessNanpNumber&&!alreadySelected){for(var j=0;j<countryCodes.length;j++){if(countryCodes[j]){countryCode=countryCodes[j];break}}}}else if(number.charAt(0)==="+"&&numeric.length){countryCode=""}else if(!number||number==="+"){countryCode=this.defaultCountry}if(countryCode!==null){return this._setFlag(countryCode)}return false}},{key:"_isRegionlessNanp",value:function _isRegionlessNanp(number){var numeric=this._getNumeric(number);if(numeric.charAt(0)==="1"){var areaCode=numeric.substr(1,3);return regionlessNanpNumbers.indexOf(areaCode)!==-1}return false}},{key:"_highlightListItem",value:function _highlightListItem(listItem,shouldFocus){var prevItem=this.highlightedItem;if(prevItem){prevItem.classList.remove("iti__highlight")}this.highlightedItem=listItem;this.highlightedItem.classList.add("iti__highlight");this.selectedFlag.setAttribute("aria-activedescendant",listItem.getAttribute("id"));if(shouldFocus){this.highlightedItem.focus()}}},{key:"_getCountryData",value:function _getCountryData(countryCode,ignoreOnlyCountriesOption,allowFail){var countryList=ignoreOnlyCountriesOption?allCountries:this.countries;for(var i=0;i<countryList.length;i++){if(countryList[i].iso2===countryCode){return countryList[i]}}if(allowFail){return null}throw new Error("No country data for '".concat(countryCode,"'"))}},{key:"_setFlag",value:function _setFlag(countryCode){var _this$options3=this.options,allowDropdown=_this$options3.allowDropdown,separateDialCode=_this$options3.separateDialCode,showFlags=_this$options3.showFlags;var prevCountry=this.selectedCountryData.iso2?this.selectedCountryData:{};this.selectedCountryData=countryCode?this._getCountryData(countryCode,false,false):{};if(this.selectedCountryData.iso2){this.defaultCountry=this.selectedCountryData.iso2}if(showFlags){this.selectedFlagInner.setAttribute("class","iti__flag iti__".concat(countryCode))}this._setSelectedCountryFlagTitleAttribute(countryCode,separateDialCode);if(separateDialCode){var dialCode=this.selectedCountryData.dialCode?"+".concat(this.selectedCountryData.dialCode):"";this.selectedDialCode.innerHTML=dialCode;var selectedFlagWidth=this.selectedFlag.offsetWidth||this._getHiddenSelectedFlagWidth();if(this.isRTL){this.telInput.style.paddingRight="".concat(selectedFlagWidth+6,"px")}else{this.telInput.style.paddingLeft="".concat(selectedFlagWidth+6,"px")}}this._updatePlaceholder();if(allowDropdown){var prevItem=this.activeItem;if(prevItem){prevItem.classList.remove("iti__active");prevItem.setAttribute("aria-selected","false")}if(countryCode){var nextItem=this.countryList.querySelector("#iti-".concat(this.id,"__item-").concat(countryCode,"-preferred"))||this.countryList.querySelector("#iti-".concat(this.id,"__item-").concat(countryCode));nextItem.setAttribute("aria-selected","true");nextItem.classList.add("iti__active");this.activeItem=nextItem}}return prevCountry.iso2!==countryCode}},{key:"_setSelectedCountryFlagTitleAttribute",value:function _setSelectedCountryFlagTitleAttribute(countryCode,separateDialCode){if(!this.selectedFlag){return}var title;if(countryCode&&!separateDialCode){title="".concat(this.selectedCountryData.name,": +").concat(this.selectedCountryData.dialCode)}else if(countryCode){title=this.selectedCountryData.name}else{title="Unknown"}this.selectedFlag.setAttribute("title",title)}},{key:"_getHiddenSelectedFlagWidth",value:function _getHiddenSelectedFlagWidth(){var containerClone=this.telInput.parentNode.cloneNode();containerClone.style.visibility="hidden";document.body.appendChild(containerClone);var flagsContainerClone=this.flagsContainer.cloneNode();containerClone.appendChild(flagsContainerClone);var selectedFlagClone=this.selectedFlag.cloneNode(true);flagsContainerClone.appendChild(selectedFlagClone);var width=selectedFlagClone.offsetWidth;containerClone.parentNode.removeChild(containerClone);return width}},{key:"_updatePlaceholder",value:function _updatePlaceholder(){var shouldSetPlaceholder=this.options.autoPlaceholder==="aggressive"||!this.hadInitialPlaceholder&&this.options.autoPlaceholder==="polite";if(window.intlTelInputUtils&&shouldSetPlaceholder){var numberType=intlTelInputUtils.numberType[this.options.placeholderNumberType];var placeholder=this.selectedCountryData.iso2?intlTelInputUtils.getExampleNumber(this.selectedCountryData.iso2,this.options.nationalMode,numberType):"";placeholder=this._beforeSetNumber(placeholder);if(typeof this.options.customPlaceholder==="function"){placeholder=this.options.customPlaceholder(placeholder,this.selectedCountryData)}this.telInput.setAttribute("placeholder",placeholder)}}},{key:"_selectListItem",value:function _selectListItem(listItem){var flagChanged=this._setFlag(listItem.getAttribute("data-country-code"));this._closeDropdown();this._updateDialCode(listItem.getAttribute("data-dial-code"));this.telInput.focus();var len=this.telInput.value.length;this.telInput.setSelectionRange(len,len);if(flagChanged){this._triggerCountryChange()}}},{key:"_closeDropdown",value:function _closeDropdown(){this.countryList.classList.add("iti__hide");this.selectedFlag.setAttribute("aria-expanded","false");this.selectedFlag.removeAttribute("aria-activedescendant");this.dropdownArrow.classList.remove("iti__arrow--up");document.removeEventListener("keydown",this._handleKeydownOnDropdown);document.documentElement.removeEventListener("click",this._handleClickOffToClose);this.countryList.removeEventListener("mouseover",this._handleMouseoverCountryList);this.countryList.removeEventListener("click",this._handleClickCountryList);if(this.options.dropdownContainer){if(!this.isMobile){window.removeEventListener("scroll",this._handleWindowScroll)}if(this.dropdown.parentNode){this.dropdown.parentNode.removeChild(this.dropdown)}}this._trigger("close:countrydropdown")}},{key:"_scrollTo",value:function _scrollTo(element,middle){var container=this.countryList;var windowTop=window.pageYOffset||document.documentElement.scrollTop;var containerHeight=container.offsetHeight;var containerTop=container.getBoundingClientRect().top+windowTop;var containerBottom=containerTop+containerHeight;var elementHeight=element.offsetHeight;var elementTop=element.getBoundingClientRect().top+windowTop;var elementBottom=elementTop+elementHeight;var newScrollTop=elementTop-containerTop+container.scrollTop;var middleOffset=containerHeight/2-elementHeight/2;if(elementTop<containerTop){if(middle){newScrollTop-=middleOffset}container.scrollTop=newScrollTop}else if(elementBottom>containerBottom){if(middle){newScrollTop+=middleOffset}var heightDifference=containerHeight-elementHeight;container.scrollTop=newScrollTop-heightDifference}}},{key:"_updateDialCode",value:function _updateDialCode(newDialCodeBare){var inputVal=this.telInput.value;var newDialCode="+".concat(newDialCodeBare);var newNumber;if(inputVal.charAt(0)==="+"){var prevDialCode=this._getDialCode(inputVal);if(prevDialCode){newNumber=inputVal.replace(prevDialCode,newDialCode)}else{newNumber=newDialCode}this.telInput.value=newNumber}else if(this.options.autoInsertDialCode){if(inputVal){newNumber=newDialCode+inputVal}else{newNumber=newDialCode}this.telInput.value=newNumber}}},{key:"_getDialCode",value:function _getDialCode(number,includeAreaCode){var dialCode="";if(number.charAt(0)==="+"){var numericChars="";for(var i=0;i<number.length;i++){var c=number.charAt(i);if(!isNaN(parseInt(c,10))){numericChars+=c;if(includeAreaCode){if(this.countryCodes[numericChars]){dialCode=number.substr(0,i+1)}}else{if(this.dialCodes[numericChars]){dialCode=number.substr(0,i+1);break}}if(numericChars.length===this.countryCodeMaxLen){break}}}}return dialCode}},{key:"_getFullNumber",value:function _getFullNumber(){var val=this.telInput.value.trim();var dialCode=this.selectedCountryData.dialCode;var prefix;var numericVal=this._getNumeric(val);if(this.options.separateDialCode&&val.charAt(0)!=="+"&&dialCode&&numericVal){prefix="+".concat(dialCode)}else{prefix=""}return prefix+val}},{key:"_beforeSetNumber",value:function _beforeSetNumber(originalNumber){var number=originalNumber;if(this.options.separateDialCode){var dialCode=this._getDialCode(number);if(dialCode){dialCode="+".concat(this.selectedCountryData.dialCode);var start=number[dialCode.length]===" "||number[dialCode.length]==="-"?dialCode.length+1:dialCode.length;number=number.substr(start)}}return this._cap(number)}},{key:"_triggerCountryChange",value:function _triggerCountryChange(){this._trigger("countrychange")}},{key:"handleAutoCountry",value:function handleAutoCountry(){if(this.options.initialCountry==="auto"){this.defaultCountry=window.intlTelInputGlobals.autoCountry;if(!this.telInput.value){this.setCountry(this.defaultCountry)}this.resolveAutoCountryPromise()}}},{key:"handleUtils",value:function handleUtils(){if(window.intlTelInputUtils){if(this.telInput.value){this._updateValFromNumber(this.telInput.value)}this._updatePlaceholder()}this.resolveUtilsScriptPromise()}},{key:"destroy",value:function destroy(){var form=this.telInput.form;if(this.options.allowDropdown){this._closeDropdown();this.selectedFlag.removeEventListener("click",this._handleClickSelectedFlag);this.flagsContainer.removeEventListener("keydown",this._handleFlagsContainerKeydown);var label=this._getClosestLabel();if(label){label.removeEventListener("click",this._handleLabelClick)}}if(this.hiddenInput&&form){form.removeEventListener("submit",this._handleHiddenInputSubmit)}if(this.options.autoInsertDialCode){if(form){form.removeEventListener("submit",this._handleSubmitOrBlurEvent)}this.telInput.removeEventListener("blur",this._handleSubmitOrBlurEvent)}this.telInput.removeEventListener("keyup",this._handleKeyupEvent);this.telInput.removeEventListener("cut",this._handleClipboardEvent);this.telInput.removeEventListener("paste",this._handleClipboardEvent);this.telInput.removeAttribute("data-intl-tel-input-id");var wrapper=this.telInput.parentNode;wrapper.parentNode.insertBefore(this.telInput,wrapper);wrapper.parentNode.removeChild(wrapper);delete window.intlTelInputGlobals.instances[this.id]}},{key:"getExtension",value:function getExtension(){if(window.intlTelInputUtils){return intlTelInputUtils.getExtension(this._getFullNumber(),this.selectedCountryData.iso2)}return""}},{key:"getNumber",value:function getNumber(format){if(window.intlTelInputUtils){var iso2=this.selectedCountryData.iso2;return intlTelInputUtils.formatNumber(this._getFullNumber(),iso2,format)}return""}},{key:"getNumberType",value:function getNumberType(){if(window.intlTelInputUtils){return intlTelInputUtils.getNumberType(this._getFullNumber(),this.selectedCountryData.iso2)}return-99}},{key:"getSelectedCountryData",value:function getSelectedCountryData(){return this.selectedCountryData}},{key:"getValidationError",value:function getValidationError(){if(window.intlTelInputUtils){var iso2=this.selectedCountryData.iso2;return intlTelInputUtils.getValidationError(this._getFullNumber(),iso2)}return-99}},{key:"isValidNumber",value:function isValidNumber(){var val=this._getFullNumber().trim();return window.intlTelInputUtils?intlTelInputUtils.isValidNumber(val,this.selectedCountryData.iso2):null}},{key:"isPossibleNumber",value:function isPossibleNumber(){var val=this._getFullNumber().trim();return window.intlTelInputUtils?intlTelInputUtils.isPossibleNumber(val,this.selectedCountryData.iso2):null}},{key:"setCountry",value:function setCountry(originalCountryCode){var countryCode=originalCountryCode.toLowerCase();if(this.selectedCountryData.iso2!==countryCode){this._setFlag(countryCode);this._updateDialCode(this.selectedCountryData.dialCode);this._triggerCountryChange()}}},{key:"setNumber",value:function setNumber(number){var flagChanged=this._updateFlagFromNumber(number);this._updateValFromNumber(number);if(flagChanged){this._triggerCountryChange()}}},{key:"setPlaceholderNumberType",value:function setPlaceholderNumberType(type){this.options.placeholderNumberType=type;this._updatePlaceholder()}}]);return Iti}();intlTelInputGlobals.getCountryData=function(){return allCountries};var injectScript=function injectScript(path,handleSuccess,handleFailure){var script=document.createElement("script");script.onload=function(){forEachInstance("handleUtils");if(handleSuccess){handleSuccess()}};script.onerror=function(){forEachInstance("rejectUtilsScriptPromise");if(handleFailure){handleFailure()}};script.className="iti-load-utils";script.async=true;script.src=path;document.body.appendChild(script)};intlTelInputGlobals.loadUtils=function(path){if(!window.intlTelInputUtils&&!window.intlTelInputGlobals.startedLoadingUtilsScript){window.intlTelInputGlobals.startedLoadingUtilsScript=true;if(typeof Promise!=="undefined"){return new Promise(function(resolve,reject){return injectScript(path,resolve,reject)})}injectScript(path)}return null};intlTelInputGlobals.defaults=defaults;intlTelInputGlobals.version="18.2.1";return function(input,options){var iti=new Iti(input,options);iti._init();input.setAttribute("data-intl-tel-input-id",iti.id);window.intlTelInputGlobals.instances[iti.id]=iti;return iti}}()});
//# sourceMappingURL=events-manager.js.map