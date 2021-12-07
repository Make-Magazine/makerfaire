<?php

namespace Elementor;

class Two_Column_Video extends Widget_Base {
    public function get_name() {
        return 'two_column_video';
    }

    public function get_title() {
        return __('Make: 2 Column Video', 'makerfaire');
    }

	public function get_icon() {
		return 'fa fa-video';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'makerfaire'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'video_title',
            [
                'label' => __('Video Title', 'makerfaire'),
                'type' => Controls_Manager::TEXT,
                'description' => __('Enter the Video title', 'makerfaire'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'video_text',
            [
                'label' => __('Video Text', 'makerfaire'),
                'type'  => Controls_Manager::TEXTAREA,
                'description' => __('Enter the text that appears under the title', 'makerfaire'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'video_button_link',
            [
                'label' => __('Video Button Link', 'makerfaire'),
                'type' => Controls_Manager::URL,
        		'description' => __('Enter the link for the button', 'makerfaire'),
                'default' => [
                    'url' => '',
                ]
            ]
        );


        $repeater->add_control(
            'video_button_text',
            [
		        'label' => __('Video Button Text', 'makerfaire'),
		        'type' => Controls_Manager::TEXT,
		        'description' => __('Enter the text displayed in the button', 'makerfaire'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'video_code',
            [
		        'label' => __('Video Code', 'makerfaire'),
		        'type' => Controls_Manager::TEXT,
		        'description' => __('YouTube video code determines what video to show. e.g. sjDJ1ZwGpq4 is the code to enter for the video: https://youtu.be/sjDJ1ZwGpq4', 'makerfaire'),
		        'label_block' => true,
            ]
        );

        $this->add_control(
            'video_list',
            [
                'label' => __('Video Panel Rows', 'makerfaire'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'video_title' => __('Video Row #1', 'makerfaire'),
                        'list_content' => __('Item content. Click the edit button to change this text.', 'makerfaire'),
                    ],
                ],
                'title_field' => '{{{ video_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $return = '<section class="video-panel container-fluid">';    // create content-panel section
        if ($settings['video_list']) {
            $videoRowNum = 0;

            foreach ($settings['video_list'] as $video) {
                $videoRowNum += 1;
                if ($videoRowNum % 2 != 0) {
                    $return .= '<div class="row">';
                    $return .= '  <div class="col-sm-4 col-xs-12">
                            <h4>' . $video['video_title'] . '</h4>
                            <p>' . $video['video_text'] . '</p>';
                    if ($video['video_button_link']) {
                        $return .= '<a href="' . $video['video_button_link']['url'] . '">' . $video['video_button_text'] . '</a>';
                    }
                    $return .= '  </div>';
                    $return .= '  <div class="col-sm-8 col-xs-12">
                            <div class="embed-youtube">
                              <iframe class="lazyload" src="https://www.youtube.com/embed/' . $video['video_code'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                            </div>
                          </div>';
                    $return .= '</div>';
                } else {
                    $return .= '<div class="row">';
                    $return .= '  <div class="col-sm-8 col-xs-12">
                                <div class="embed-youtube">
                                  <iframe class="lazyload" src="https://www.youtube.com/embed/' . $video['video_code'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                </div>
                              </div>';
                    $return .= '  <div class="col-sm-4 col-xs-12">
                                <h4>' . $video['video_title'] . '</h4>
                                <p>' . $video['video_text'] . '</p>';
                    if ($video['video_button_link']) {
                        $return .= '<a href="' . $video['video_button_link']['url'] . '">' . $video['video_button_text'] . '</a>';
                    }
                    $return .= '  </div>';
                    $return .= '</div>';
                }
            }
        }
        $return .='</section>';
        echo $return;
    } //end render function

} //end class
