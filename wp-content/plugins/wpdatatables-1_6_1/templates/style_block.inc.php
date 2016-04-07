<style>

/* table font color */
<?php if(!empty($wdtFontColorSettings['wdtTableFontColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable { 
	color: <?php echo $wdtFontColorSettings['wdtTableFontColor'] ?>;
}
<?php } ?>

/* th background color */
<?php if(!empty($wdtFontColorSettings['wdtHeaderBaseColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable thead th {
	background-color: <?php echo $wdtFontColorSettings['wdtHeaderBaseColor'] ?> !important;
}
<?php } ?>

/* th border color */
<?php if(!empty($wdtFontColorSettings['wdtHeaderBorderColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable thead th {
	border-color: <?php echo $wdtFontColorSettings['wdtHeaderBorderColor'] ?> !important;
}
<?php } ?>

/* th font color */
<?php if(!empty($wdtFontColorSettings['wdtHeaderFontColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable thead th {
	color: <?php echo $wdtFontColorSettings['wdtHeaderFontColor'] ?> !important;
}

.wpDataTablesWrapper table.wpDataTable thead th.sorting:after,
.wpDataTablesWrapper table.wpDataTable thead th.sorting_asc:after {
	border-bottom-color: <?php echo $wdtFontColorSettings['wdtHeaderFontColor'] ?> !important;
}

.wpDataTablesWrapper table.wpDataTable thead th.sorting_desc:after {
	border-top-color: <?php echo $wdtFontColorSettings['wdtHeaderFontColor'] ?> !important;
}

<?php } ?>

/* th active/hover background color */
<?php if(!empty($wdtFontColorSettings['wdtHeaderActiveColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable thead th.sorting_asc,
.wpDataTablesWrapper table.wpDataTable thead th.sorting_desc,
.wpDataTablesWrapper table.wpDataTable thead th.sorting:hover {
	background-color: <?php echo $wdtFontColorSettings['wdtHeaderActiveColor'] ?> !important;
}
<?php } ?>

/* td inner border color */
<?php if(!empty($wdtFontColorSettings['wdtTableInnerBorderColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable td {
	background-color: <?php echo $wdtFontColorSettings['wdtTableInnerBorderColor'] ?> !important;
}
<?php } ?>

/* table outer border color */
<?php if(!empty($wdtFontColorSettings['wdtTableOuterBorderColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable tr:last-child td {
	border-bottom-color: <?php echo $wdtFontColorSettings['wdtTableOuterBorderColor'] ?> !important;
}

.wpDataTablesWrapper table.wpDataTable tr td:first-child {
	border-left-color: <?php echo $wdtFontColorSettings['wdtTableOuterBorderColor'] ?> !important;
}

.wpDataTablesWrapper table.wpDataTable tr td:last-child {
	border-right-color: <?php echo $wdtFontColorSettings['wdtTableOuterBorderColor'] ?> !important;
}
<?php } ?>

/* odd rows background color */
<?php if(!empty($wdtFontColorSettings['wdtOddRowColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable tr.odd td {
	background-color: <?php echo $wdtFontColorSettings['wdtOddRowColor'] ?> !important;
}
<?php } ?>

/* even rows background color */
<?php if(!empty($wdtFontColorSettings['wdtEvenRowColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable tr.even td,
.wpDataTablesWrapper table.has-columns-hidden tr.row-detail > td {
	background-color: <?php echo $wdtFontColorSettings['wdtEvenRowColor'] ?> !important;
}
<?php } ?>

/* odd rows active background color */
<?php if(!empty($wdtFontColorSettings['wdtActiveOddCellColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable tr.odd td.sorting_1 {
	background-color: <?php echo $wdtFontColorSettings['wdtActiveOddCellColor'] ?> !important;
}
<?php } ?>

/* even rows active background color */
<?php if(!empty($wdtFontColorSettings['wdtActiveEvenCellColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable tr.even td.sorting_1 {
	background-color: <?php echo $wdtFontColorSettings['wdtActiveEvenCellColor'] ?> !important;
}
<?php } ?>

/* rows hover background color */
<?php if(!empty($wdtFontColorSettings['wdtHoverRowColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable tr.odd:hover > td,
.wpDataTablesWrapper table.wpDataTable tr.odd:hover > td.sorting_1,
.wpDataTablesWrapper table.wpDataTable tr.even:hover > td,
.wpDataTablesWrapper table.wpDataTable tr.even:hover > td.sorting_1 {
	background-color: <?php echo $wdtFontColorSettings['wdtHoverRowColor'] ?> !important;
}
<?php } ?>

/* selected rows background color */
<?php if(!empty($wdtFontColorSettings['wdtSelectedRowColor'])){ ?>
.wpDataTablesWrapper table.wpDataTable tr.odd.selected > td,
.wpDataTablesWrapper table.wpDataTable tr.odd.selected > td.sorting_1,
.wpDataTablesWrapper table.wpDataTable tr.even.selected > td,
.wpDataTablesWrapper table.wpDataTable tr.even.selected > td.sorting_1 {
	background-color: <?php echo $wdtFontColorSettings['wdtSelectedRowColor'] ?> !important;
}
<?php } ?>

/* buttons background color */
<?php if(!empty($wdtFontColorSettings['wdtButtonColor'])){ ?>
.wpDataTables .selecter .selecter-selected,
.remodal.wdtRemodal .btn,
.wpDataTables .selecter .selecter-options,
.wpDataTables .picker__day--today,
.wpDataTables .picker__day--infocus:hover,
.wpDataTables .picker__day--outfocus:hover,
.wpDataTables .picker__footer button,
div.dt-button-collection a.dt-button.active:not(.disabled) {
	background-color: <?php echo $wdtFontColorSettings['wdtButtonColor'] ?> !important;
}
<?php } ?>


/* buttons border color */
<?php if(!empty($wdtFontColorSettings['wdtButtonBorderColor'])){ ?>
.wpDataTables .selecter .selecter-selected,
.remodal.wdtRemodal .btn,
.wpDataTables .selecter .selecter-options,
.wpDataTables .picker__day--today,
.wpDataTables .picker__day--infocus:hover,
.wpDataTables .picker__day--outfocus:hover,
.wpDataTables .picker__footer button,
div.dt-button-collection a.dt-button.active:not(.disabled) {
	border-color: <?php echo $wdtFontColorSettings['wdtButtonBorderColor'] ?> !important;
}
<?php } ?>

/* buttons font color */
<?php if(!empty($wdtFontColorSettings['wdtButtonFontColor'])){ ?>
.wpDataTables .selecter .selecter-selected,
.remodal.wdtRemodal .btn,
.wpDataTables .selecter .selecter-item,
.wpDataTables .picker__day--today,
.wpDataTables .picker__day--infocus:hover,
.wpDataTables .picker__day--outfocus:hover,
.wpDataTables .picker__footer button,
div.dt-button-collection a.dt-button.active:not(.disabled) {
	color: <?php echo $wdtFontColorSettings['wdtButtonFontColor'] ?> !important;
}

.wpDataTables .picker__button--clear:before {
	color: <?php echo $wdtFontColorSettings['wdtButtonFontColor'] ?> !important;
}

.wpDataTables .selecter .selecter-selected:after,
.wpDataTables .selecter.open .selecter-selected:after,
.wpDataTables .selecter.focus .selecter-selected:after {
	border-top-color: <?php echo $wdtFontColorSettings['wdtButtonFontColor'] ?>;
}

.wpDataTables .picker__day--today:before,
.wpDataTables .picker__button--today:before {
	border-top-color: <?php echo $wdtFontColorSettings['wdtButtonFontColor'] ?> !important;
}
<?php } ?>

/* buttons and inputs border radius */
<?php if(!empty($wdtFontColorSettings['wdtBorderRadius'])){ ?>
<?php $wdtBorderRadius = (int)$wdtFontColorSettings['wdtBorderRadius']; ?>
.wpDataTables .selecter .selecter-selected,
.remodal.wdtRemodal .btn,
.wpDataTables .picker__day--infocus,
.wpDataTables .picker__day--outfocus,
.wpDataTables .picker__footer button {
	border-radius: <?php echo $wdtBorderRadius ?>px !important;
}

.wpDataTables .selecter .selecter-options {
	border-radius: 0px 0px <?php echo $wdtBorderRadius ?>px <?php echo $wdtBorderRadius ?>px !important;
}

<?php echo $wdtSelecterRadius = $wdtBorderRadius-1 > 0 ? $wdtBorderRadius-1 : 0; ?>
.wpDataTables .selecter .selecter-item:last-child {
	border-radius: 0px 0px <?php echo $wdtSelecterRadius ?>px <?php echo $wdtSelecterRadius ?>px !important;
}
<?php } ?>

/** buttons background hover color */
<?php if(!empty($wdtFontColorSettings['wdtButtonBackgroundHoverColor'])){ ?>
.wpDataTables .selecter .selecter-selected:hover,
.wpDataTables .selecter.open .selecter-selected,
.remodal.wdtRemodal .btn:hover,
.wpDataTables .selecter.open .selecter-item:hover,
.wpDataTables .selecter .selecter-item.selected,
.wpDataTables .picker__day--today:hover,
.wpDataTables .picker--focused .picker__day--highlighted,
.wpDataTables .picker__day--highlighted:hover,
.wpDataTables .picker__day--outfocus.picker__day--selected,
.wpDataTables .picker__footer button:hover,
div.dt-button-collection a.dt-button.active:not(.disabled):hover{
	background-color: <?php echo $wdtFontColorSettings['wdtButtonBackgroundHoverColor'] ?> !important;
}
<?php } ?>

/** buttons hover border color */
<?php if(!empty($wdtFontColorSettings['wdtButtonBorderHoverColor'])){ ?>
.wpDataTables .selecter .selecter-selected:hover,
.wpDataTables .selecter.open .selecter-selected,
.remodal.wdtRemodal .btn:hover,
.wpDataTables .picker__day--today:hover,
.wpDataTables .picker--focused .picker__day--highlighted,
.wpDataTables .picker__day--highlighted:hover,
.wpDataTables .picker__day--outfocus.picker__day--selected,
.wpDataTables .picker__footer button:hover {
	border-color: <?php echo $wdtFontColorSettings['wdtButtonBorderHoverColor'] ?> !important;
}
<?php } ?>

/** buttons hover font color */
<?php if(!empty($wdtFontColorSettings['wdtButtonFontHoverColor'])){ ?>
.wpDataTables .selecter .selecter-selected:hover,
.wpDataTables .selecter.open .selecter-selected,
.remodal.wdtRemodal .btn:hover,
.wpDataTables .selecter.open .selecter-item:hover,
.wpDataTables .selecter .selecter-item.selected,
div.dt-button-collection a.dt-button.active:not(.disabled):hover {
	color: <?php echo $wdtFontColorSettings['wdtButtonFontHoverColor'] ?> !important;
}
<?php } ?>

/** buttons hover font color */
<?php if(!empty($wdtFontColorSettings['wdtButtonFontHoverColor'])){ ?>
.wpDataTables .selecter .selecter-selected:hover,
.wpDataTables .selecter.open .selecter-selected,
.remodal.wdtRemodal .btn:hover,
.wpDataTables .selecter.open .selecter-item:hover,
.wpDataTables .selecter .selecter-item.selected {
	color: <?php echo $wdtFontColorSettings['wdtButtonFontHoverColor'] ?> !important;
}

.wpDataTables .picker__day--today:hover,
.wpDataTables .picker--focused .picker__day--highlighted,
.wpDataTables .picker__day--highlighted:hover,
.wpDataTables .picker__day--outfocus.picker__day--selected,
.wpDataTables .picker__footer button:hover,
.wpDataTables .picker__button--clear:hover:before {
	color: <?php echo $wdtFontColorSettings['wdtButtonFontHoverColor'] ?> !important;
}

.wpDataTables .picker__day--today:hover:after,
.wpDataTables .picker--focused .picker__day--today.picker__day--highlighted:after,
.wpDataTables .picker__day--today.picker__day--selected:after {
	border-top-color: <?php echo $wdtFontColorSettings['wdtButtonFontHoverColor'] ?> !important;
}

.wpDataTables .picker__button--today:hover:before {
	border-top-color: <?php echo $wdtFontColorSettings['wdtButtonFontHoverColor'] ?> !important;
}
<?php } ?>

/** modals font color */
<?php if(!empty($wdtFontColorSettings['wdtModalFontColor'])){ ?>
.wpDataTables .picker .picker-handle,
.wpDataTables .picker.focus .picker-handle {
	border-color: <?php echo $wdtFontColorSettings['wdtModalFontColor'] ?> !important;
}

.wpDataTables .picker.picker-checkbox .picker-flag,
.wpDataTables .picker .picker-label,
.remodal.wdtRemodal,
.wpDataTables .picker__box,
.wpDataTables .picker__weekday {
	color: <?php echo $wdtFontColorSettings['wdtModalFontColor'] ?> !important;
}
<?php } ?>

/** modals background color */
<?php if(!empty($wdtFontColorSettings['wdtModalBackgroundColor'])){ ?>
.remodal.wdtRemodal,
.wpDataTables .picker__box {
	background-color: <?php echo $wdtFontColorSettings['wdtModalBackgroundColor'] ?> !important;
}
<?php } ?>

/** overlays background color */
<?php if(!empty($wdtFontColorSettings['wdtOverlayColor'])){ ?>
<?php
	list($overlayR,$overlayG,$overlayB) = array_map('hexdec',str_split(ltrim($wdtFontColorSettings['wdtOverlayColor'],'#'),2));
?>
.remodal-overlay,
.wpDataTablesWrapper .picker--opened .picker__holder {
	background-color: rgba(<?php echo (int)$overlayR ?>,<?php echo (int)$overlayG ?>,<?php echo (int)$overlayB ?>,0.8) !important;
}
<?php } ?>
<?php if( get_option('wdtRenderFilter') == 'header')  { ?>
.wpDataTablesWrapper table.wpDataTable thead tr:nth-child(2) th {
	overflow: visible;
}
<?php } ?>
</style>