<div class="cff-fb-types-ctn cff-fb-templates-ctn cff-fb-fs sb-box-shadow" v-if="viewsActive.selectedFeedSection == 'selectTemplate'">
	<div class="cff-fb-types cff-fb-fs">
		<h4>{{selectFeedTemplateScreen.feedTemplateHeading}}</h4>
		<p class="sb-caption sb-lighter">{{selectFeedTemplateScreen.feedTemplateDescription}}</p>
		<div class="cff-fb-templates-list">
			<div class="cff-fb-type-el" v-for="(feedTemplateEl, feedTemplateIn) in feedTemplates" :data-active="selectedFeedTemplate === feedTemplateEl.type" @click.prevent.default="chooseFeedTemplate(feedTemplateEl)">
				<div :class="['cff-fb-type-el-img cff-fb-fs', 'cff-feedtemplate-' + feedTemplateEl.type]" v-html="svgIcons[selectedFeed][feedTemplateEl.icon]"></div>
				<div class="cff-fb-type-el-info cff-fb-fs">
					<p class="sb-small-p sb-bold sb-dark-text" v-html="getFeedTemplateElTitle(feedTemplateEl)"></p>
				</div>
			</div>
		</div>
	</div>
</div>