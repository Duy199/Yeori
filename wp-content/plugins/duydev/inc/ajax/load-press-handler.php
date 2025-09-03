<?php

add_action('wp_ajax_press_load_handler', 'press_load_handler');
add_action('wp_ajax_nopriv_press_load_handler', 'press_load_handler');

function press_load_handler() {
    $data = $_POST['data'];

    $search = isset($data['search']) ? sanitize_text_field($data['search']) : '';
    $page   = isset($data['page']) ? max(1, intval($data['page'])) : 1;
    $tag_slug = isset($data['tag']) ? sanitize_text_field($data['tag']) : '';

    $args = [
        'post_type'      => 'press',
        'posts_per_page' => 4,
        'paged'          => $page,
        's'              => $search,
    ];


    if ($tag_slug) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'post_tag',
                'field'    => 'slug',
                'terms'    => $tag_slug,
            )
        );
    }


    $query = new WP_Query($args);

    // Render HTML post using string buffer
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $terms = get_field('press_tag', get_the_ID());
            
            include plugin_dir_path(dirname(__DIR__)) . '/components/press-item.php';
        }
    }
    $html = ob_get_clean();

    // Render pagination using string buffer
    ob_start();

    $current_page = $query->get('paged') ? $query->get('paged') : 1;
    $total_pages = $query->max_num_pages;

    include plugin_dir_path(dirname(__DIR__)) . '/components/press-pagination.php';

    $pagination_html = ob_get_clean();

    wp_reset_postdata();
    wp_send_json_success([
        'html'         => $html,
        'pagination'   => $pagination_html,
        'current_page' => $page,
        'total_pages'  => $query->max_num_pages,
        'tag' => $tag_slug,
    ]);
}

