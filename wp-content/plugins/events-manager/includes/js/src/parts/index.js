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