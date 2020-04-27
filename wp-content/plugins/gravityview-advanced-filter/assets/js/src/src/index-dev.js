import App from './App.svelte';

const AdvancedFilter = new App( {
	target: document.body,
	props: {
		conditions: [],
		fields: [ // Data from window.gvAdvFilterVar.gformFieldFilters (see advanced-filter.php:admin_enqueue_scripts())
			{
				'key': '0',
				'text': 'Any form field',
				'operators': [ 'contains', 'is' ],
				'preventMultiple': false,
			},
			{
				'key': 2,
				'text': 'Untitled',
				'preventMultiple': false,
				'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
			},
			{
				'key': 1,
				'text': 'Address',
				'group': true,
				'filters': [
					{
						'key': '1.1',
						'text': 'Street Address',
						'preventMultiple': false,
						'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
					},
					{
						'key': '1.2',
						'text': 'Address Line 2',
						'preventMultiple': false,
						'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
					},
					{
						'key': '1.3',
						'text': 'City',
						'preventMultiple': false,
						'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
					},
					{
						'key': '1.4',
						'text': 'State / Province',
						'preventMultiple': false,
						'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
					},
					{
						'key': '1.5',
						'text': 'ZIP / Postal Code',
						'preventMultiple': false,
						'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
					},
					{
						'key': '1.6',
						'text': 'Country',
						'preventMultiple': false,
						'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
					},
				],
			},
			{ 'text': 'Entry ID', 'operators': [ 'is', 'isnot', '>', '<' ], 'key': 'entry_id', 'preventMultiple': false },
			{
				'text': 'Entry Date',
				'operators': [ 'is', '>', '<' ],
				'placeholder': 'yyyy-mm-dd',
				'cssClass': 'datepicker ymd_dash',
				'key': 'date_created',
				'preventMultiple': false,
			},
			{
				'text': 'Starred',
				'operators': [ 'is', 'isnot' ],
				'values': [ { 'text': 'Yes', 'value': '1' }, { 'text': 'No', 'value': '0' } ],
				'key': 'is_starred',
				'preventMultiple': false,
			},
			{
				'text': 'IP Address',
				'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
				'key': 'ip',
				'preventMultiple': false,
			},
			{
				'text': 'Source URL',
				'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
				'key': 'source_url',
				'preventMultiple': false,
			},
			{
				'text': 'Payment Status',
				'operators': [ 'is', 'isnot' ],
				'values': [
					{ 'text': 'Authorized', 'value': 'Authorized' },
					{ 'text': 'Paid', 'value': 'Paid' },
					{ 'text': 'Processing', 'value': 'Processing' },
					{ 'text': 'Failed', 'value': 'Failed' },
					{ 'text': 'Active', 'value': 'Active' },
					{ 'text': 'Cancelled', 'value': 'Cancelled' },
					{ 'text': 'Pending', 'value': 'Pending' },
					{ 'text': 'Refunded', 'value': 'Refunded' },
					{ 'text': 'Voided', 'value': 'Voided' },
				],
				'key': 'payment_status',
				'preventMultiple': false,
			},
			{
				'text': 'Payment Date',
				'operators': [ 'is', 'isnot', '>', '<' ],
				'placeholder': 'yyyy-mm-dd',
				'cssClass': 'datepicker ymd_dash',
				'key': 'payment_date',
				'preventMultiple': false,
			},
			{
				'text': 'Payment Amount',
				'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
				'key': 'payment_amount',
				'preventMultiple': false,
			},
			{
				'text': 'Transaction ID',
				'operators': [ 'is', 'isnot', '>', '<', 'contains' ],
				'key': 'transaction_id',
				'preventMultiple': false,
			},
			{
				'text': 'Created By',
				'operators': [ 'is', 'isnot' ],
				'values': [
					{ 'text': 'Currently Logged-in User', 'value': 'created_by' },
					{ 'text': 'Currently Logged-in User (Disabled for Administrators)', 'value': 'created_by_or_admin' },
					{ 'text': 'moi', 'value': '1' },
				],
				'key': 'created_by',
				'preventMultiple': false,
			},
			{
				'key': 'created_by_user_role',
				'text': 'Created By User Role',
				'operators': [ 'is' ],
				'values': [
					{ 'text': 'Any Role of Current User', 'value': 'current_user' },
					{ 'text': 'Subscriber', 'value': 'subscriber' },
					{ 'text': 'Contributor', 'value': 'contributor' },
					{ 'text': 'Author', 'value': 'author' },
					{ 'text': 'Editor', 'value': 'editor' },
					{ 'text': 'Administrator', 'value': 'administrator' },
				],
			},
			{
				'text': 'Entry Approval Status',
				'key': 'is_approved',
				'operators': [ 'is', 'isnot' ],
				'values': [
					{ 'text': 'Disapproved', 'value': 2 },
					{ 'text': 'Approved', 'value': 1 },
					{ 'text': 'Unapproved', 'value': 3 },
				],
			},
		],
	},
} );

export default AdvancedFilter;
