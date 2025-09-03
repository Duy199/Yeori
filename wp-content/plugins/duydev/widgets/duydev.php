<?php
class DuyDev_Base extends \Elementor\Widget_Base {
    public function get_name() {
        return 'duydev_';
    }

    public function get_title() {
        return 'DuyDev';
    }

    public function get_icon() {
        return 'eicon-inner-section';
    }

    public function get_categories() {
        return ['duydev-cat'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Content',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
    }

}