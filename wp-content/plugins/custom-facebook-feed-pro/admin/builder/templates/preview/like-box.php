<script type="text/x-template" id="cff-post-likebox-component">
	<div id="cff-like-box-section" class="cff-preview-likebox-ctn cff-fb-fs cff-preview-section" :data-dimmed="!$parent.isSectionHighLighted('likeBox')"  v-if="$parent.valueIsEnabled(customizerFeedData.settings.showlikebox)">
		<iframe :src="$parent.displayLikeBoxIframe()"></iframe>
	</div>
</script>