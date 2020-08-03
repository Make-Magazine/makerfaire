<script>
    import { setContext, beforeUpdate, afterUpdate } from 'svelte';
    import { writable } from 'svelte/store';
    import Conditions from './Components/Conditions.svelte';
    import smoothscroll from 'smoothscroll-polyfill';

    smoothscroll.polyfill();

    // Map field keys to their positions in the array - makes it easier to search for multiple input fields (e.g., Address)
    const mapFieldKeyToIndex = ( fields ) => {
        let results = [];

        if ( ! fields ) {
            return results;
        }

        fields.forEach( ( field, i ) => results[ field.key ] = i );

        return results;
    };

    let conditionsStore = writable( $$props.conditions && $$props.conditions.hasOwnProperty( 'conditions' ) && $$props.conditions.conditions.length ? $$props.conditions : null );
    let fieldsStore = writable( $$props.fields );
    let fieldKeyToIndexMapStore = writable( mapFieldKeyToIndex( $$props.fields ) );
    let { onConditionsUpdate } = $$props;
    let conditionsToExport;
    let div = document.getElementById( 'gravityview-metabox-content-container' );
    let autoscroll;

    const isInternetExplorer = document.documentMode;

    // Determine if autoscrolling should be enabled
    beforeUpdate( () => {
        autoscroll = div && ( div.offsetHeight + div.scrollTop ) > ( div.scrollHeight - 20 );
    } );

    // Scroll to the bottom of the metabox container when conditions are added and autoscroll is enabled
    afterUpdate( () => {
        if ( ! autoscroll ) { return;}

        div.scrollTo( {
            top: div.scrollHeight,
            behavior: 'smooth',
        } );
    } );

    // Share data across all components
    setContext( 'app', {
        fieldsStore,
        fieldKeyToIndexMapStore,
        conditionsStore,
        translations: $$props.translations || {},
    } );

    // Export conditions
    conditionsStore.subscribe( ( data ) => {
        conditionsToExport = JSON.stringify( data );

        if ( typeof onConditionsUpdate === 'function' ) {
            onConditionsUpdate( data );
        }
    } );

    // Allow updating fields from the outside
    export function updateFields( data ) {
        if ( ! data ) {
            $fieldsStore = null;
            return;
        }

        $fieldsStore = data;
        $fieldKeyToIndexMapStore = mapFieldKeyToIndex( data );
        $conditionsStore = {};
    };
</script>
{#if isInternetExplorer}
    <div class="notice inline notice-error">
		{ $$props.translations.internet_explorer_notice }
    </div>
{:else if !$fieldsStore}
    <div class="notice inline notice-error">
		{ $$props.translations.fields_not_available }
    </div>
{:else}
    <div class="conditions">
        <Conditions conditionsData={$conditionsStore ? $conditionsStore.conditions : $conditionsStore} />
    </div>

    <input type="hidden" name="gv_af_conditions" bind:value={conditionsToExport} />
{/if}

<style type="text/scss">
    :global(#gv-advanced-filter) {
        th {
            vertical-align: top !important;
        }

        &.alternate {
            background: white;
        }
    }

    .conditions {
        margin: 0 auto;
        display: flex;
        flex-direction: column;

        @media screen and (max-width: 782px) {
            width: calc(100% - 1em);
        }
    }
</style>
