<script>
    import { getContext } from 'svelte';
    import flatpickr from 'flatpickr';

    import '../Styles/Flatpickr.scss';

    export let fieldData = {};
    export let onUpdate = false;
    export let conditionFieldPath = '';

    const { fieldsStore, fieldKeyToIndexMapStore, translations } = getContext( 'app' );

    let selectedField = getFieldObjectByKey( fieldData.key );
    let selectedOperator = fieldData.operator;
    let selectedValue = fieldData.value;
    if ( selectedField.values && ! String( selectedValue ).length ) {
        selectedValue = selectedField.values[ 0 ].value;
    }
    let datePickerInstance;

    /**
     * Initialize date picker
     */
    function datePicker( node ) {
        const altFormat = 'Y-m-d';

        datePickerInstance = flatpickr( node, {
            allowInput: true,
            altInput: true,
            altFormat,
            placeholder: selectedField.placeholder,
            onChange: ( selectedDates, value ) => {
                selectedValue = value;
                updateField();
            },
            onReady: function() {
                // When Flatpickr is initialized and input contains an invalid date (e.g., relative date such as "today"),
                // it converts it into today's date and updates input field's value; let's reverse it back to the original value
                if ( this._input.value !== selectedValue ) {
                    this._input.value = selectedValue;
                }
            },
        } );

        // Workaround to update calendar display when date is manually inputted
        datePickerInstance._input.addEventListener( 'input', () => {
            const value = datePickerInstance._input.value;
            const parsedDate = datePickerInstance.parseDate( value, altFormat );

            if ( parsedDate ) {

                const formattedDate = datePickerInstance.formatDate( parsedDate, altFormat );

                if ( value === formattedDate ) {
                    datePickerInstance.setDate( value, true, altFormat );
                }
            }

            selectedValue = value;
            updateField();
        }, true );
    }

    /**
     * Get field object using its key from global app context
     *
     * @param {string} key Field key
     *
     * @return {Object}
     */
    function getFieldObjectByKey( key ) {
        let field;

        key = key + '';

        if ( key.match( /\d+\.\d+/ ) ) {
            field = $fieldsStore[ $fieldKeyToIndexMapStore[ key.split( '.' )[ 0 ] ] ];
            field = field.filters.filter( ( _field ) => _field.key === key )[ 0 ];
        } else {
            field = $fieldsStore[ $fieldKeyToIndexMapStore[ key ] ];
        }

        return field;
    }

    /**
     * Change field selection
     *
     * @param {Object} e Change event
     */
    function changeField( e ) {
        let { target: { value: key } } = e;

        selectedField = getFieldObjectByKey( key );
        selectedOperator = selectedField.operators[ 0 ];
        selectedValue = selectedField.values ? selectedField.values[ 0 ].value : '';

        // Clean up memory by destroying a date picker instance
        if ( datePickerInstance && ! ( selectedField.cssClass || '' ).match( /datepicker/ ) ) {
            datePickerInstance.destroy();
            datePickerInstance = null;
        }

        updateField();
    }

    /**
     * Call onUpdate() prop function to communicate field change
     */
    function updateField() {
        if ( typeof onUpdate !== 'function' ) {
            return;
        }

        onUpdate( {
            conditionFieldPath,
            key: selectedField.key,
            operator: selectedOperator,
            value: selectedValue,
        } );
    }

    /**
     * Get operator name translation (some fields may have custom names, such as dates where "is" = "Is On")
     *
     * @param {Object} field Field object
     * @param {string} operator Operator name
     *
     * @return {string} Translated operator name
     */
    function translateFieldOperator( field, operator ) {
        const datePickerOperatorMap = {
            '<': 'isbefore',
            '>': 'isafter',
            'is': 'ison',
            'isnot': 'isnoton',
        };

        if ( ( field.cssClass || '' ).match( /datepicker/ ) ) {
            return translations[ datePickerOperatorMap[ operator ] || operator ];
        }

        return translations[ operator ];
    }
</script>

<div class="field">
    <select value={selectedField.key} on:change={changeField}>
		{#each $fieldsStore as field (field.key)}
			{#if field.filters }
                <optgroup label={field.text}>
					{#each field.filters as filter}
                        <option value={filter.key}>
							{filter.text}
                        </option>
					{/each}
                </optgroup>
			{:else }
                <option value={field.key}>
					{field.text}
                </option>
			{/if}
		{/each}
    </select>
    <select bind:value={selectedOperator} on:change={updateField}>
		{#each selectedField.operators as operator}
            <option value={operator}>
				{translateFieldOperator(selectedField, operator)}
            </option>
		{/each}
    </select>

	{#if selectedOperator !== 'isempty' && selectedOperator !== 'isnotempty'}
		{#if selectedField.values}
            <select bind:value={selectedValue} on:change={updateField}>
				{#each selectedField.values as value}
                    <option value={value.value}>
						{value.text}
                    </option>
				{/each}
            </select>
		{:else if (selectedField.cssClass || '').match(/datepicker/)}
            <input type="text" placeholder={selectedField.placeholder} bind:value={selectedValue} on:blur={updateField} use:datePicker />
		{:else}
            <input type="text" bind:value={selectedValue} on:keyup={updateField} />
		{/if}
	{/if}

    <slot name="remove_field" />
</div>


<style type="text/scss">
    .field {
        width: 100%;
        display: flex;
        flex-direction: row;

        select {
            width: 33.33%;
        }

        input {
            width: 40%;
        }

        *:not(:first-child) {
            margin-left: 1em !important;
        }

        @media screen and (max-width: 782px) {
            flex-direction: column;


            select, input {
                width: 100%;
            }

            *:not(:first-child) {
                margin: 1em 0 0 0 !important;
            }
        }
    }

</style>
