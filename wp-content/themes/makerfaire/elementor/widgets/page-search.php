<?php

namespace Elementor;

class Page_Search extends Widget_Base {

    public function get_name() {
        return 'page_search';
    }

    public function get_title() {
        return __('Make: Page Search', 'makerfaire');
    }

	public function get_icon() {
		return 'fa fa-search';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Page Search', 'makerfaire' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'placeholder',
			[
				'label' => __( 'Placeholder', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Search', 'makerfaire' ),
			]
		);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
		wp_enqueue_script('jquery-mark', get_stylesheet_directory_uri() . '/js/libs/jquery.mark.min.js');
		?>
		<style>
			mark {
				background: yellow;
			}
			mark.current {
				background: orange;
			}
			.make-searchbar {
				position: fixed;
				flex-wrap: wrap;
				display: flex;
				top: 155px;
				right: 0px;
				z-index: 99999;
				padding: 15px;
				background: #f1f1f1;
				border: solid 1px #ccc;
				border-radius: 5px;
			}
			@media screen and (max-width: 768px) {
				.make-searchbar {
					top: auto;
					bottom: 0px;
				}
			}
			.make-searchbar h4 {
				width: 100%;
				margin-top: 0px;
				font-weight: bold;
			}
			.make-searchbar input[type='search'] {
				margin-right: 5px;
			}
			.make-searchbar button {
				color: #fff;
				background: #005e9a;
				width: 30px;
				margin: 0 5px;
				border: none;
				border-radius: 3px;
			}
		</style>
		<script>
		jQuery(document).ready(function() {
			// the input field
			var $input = jQuery("input[type='search']"),
				// clear button
				$clearBtn = jQuery("button[data-search='clear']"),
				// prev button
				$prevBtn = jQuery("button[data-search='prev']"),
				// next button
				$nextBtn = jQuery("button[data-search='next']"),
				// the context where to search
				$content = jQuery("div[data-elementor-type='wp-page']"),
				// jQuery object to save <mark> elements
				$results,
				// the class that will be appended to the current
				// focused element
				currentClass = "current",
				// top offset for the jump (the search bar)
				offsetTop = 50,
				// the current index of the focused element
				currentIndex = 0;
			/**
			 * Jumps to the element matching the currentIndex
			 */
			function jumpTo() {
				if ($results.length) {
					var position,
						$current = $results.eq(currentIndex);
					$results.removeClass(currentClass);
					if ($current.length) {
						$current.addClass(currentClass);
						position = $current.offset().top - offsetTop - 121;
						window.scrollTo(0, position);
					}
				}
			}
			/**
			 * Searches for the entered keyword in the
			 * specified context on input
			 */
			$input.on("input", function() {
				// if there are accordions closed on the page, open them, otherwise we can't search
				if(jQuery(".elementor-tab-content:hidden")) {
					jQuery('.elementor-tab-title').addClass('elementor-active');
					jQuery('.elementor-tab-content').css('display', 'block');
				}
				var searchVal = this.value;
				$content.unmark({
				done: function() {
					$content.mark(searchVal, {
					separateWordSearch: false,
					done: function() {
						$results = $content.find("mark");
						currentIndex = 0;
						jumpTo();
					}
					});
				}
				});
			});
			/**
			 * Clears the search
			 */
			$clearBtn.on("click", function() {
				$content.unmark();
				$input.val("").focus();
			});
			/**
			 * Next and previous search jump to
			 */
			$nextBtn.add($prevBtn).on("click", function() {
				if ($results.length) {
				currentIndex += jQuery(this).is($prevBtn) ? -1 : 1;
				if (currentIndex < 0) {
					currentIndex = $results.length - 1;
				}
				if (currentIndex > $results.length - 1) {
					currentIndex = 0;
				}
				jumpTo();
				}
			});
		});
		</script>
		<div id="searchbar" class="make-searchbar">
			<?php if(isset($settings['title'])) { echo("<h4>" . $settings['title'] . "</h4>"); } ?>
			<input type="search" placeholder="<?php echo $settings['placeholder']; ?>">
			<button data-search="next">↓</button>
			<button data-search="prev">↑</button>
			<button data-search="clear">✖</button>
		</div>
		<?php
    } //end render function

} //end class
