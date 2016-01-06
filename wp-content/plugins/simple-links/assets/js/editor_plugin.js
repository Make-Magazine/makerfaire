/**
 * The MCE plugin to add Simple LInks Shortcodes
 * @author Mat Lipe <mat@matlipe.com>
 */

(function(){
	tinymce.create( 'tinymce.plugins.simpleLinks', {
		init : function( ed, url ){
			ed.addButton( 'simpleLinks', {    //The buttons name and title and icon
				title : SL_locale.add_links,
				image : url + '/../img/mce-icon.png',
				cmd : 'mceHighlight' //Match the addCommand
			} );

			// Register commands
			ed.addCommand( 'mceHighlight', function(){
				ed.windowManager.open( {
					file : ed.documentBaseUrl.replace( 'wp-admin/', '' ) + '?simple_links_shortcode=form',
					width : 550 + parseInt( ed.getLang( 'highlight.delta_width', 0 ) ),
					height : 650 + parseInt( ed.getLang( 'highlight.delta_height', 0 ) ),
					inline : 1,
					title : SL_locale.shortcode
				}, {

					plugin_url : url

				} );

			} );

		}, createControl : function( n, cm ){
			return null;

		}, getInfo : function(){  //The plugin Buttons Details
			return {
				longname : SL_locale.shortcode_generator,
				author : 'Mat Lipe',
				authorurl : 'https://matlipe.com',
				inforurl : 'https://matlipe.com',
				version : '2.0'
			};
		}
	} );
	tinymce.PluginManager.add( 'simpleLinks', tinymce.plugins.simpleLinks );  //Name it the same as above
})();