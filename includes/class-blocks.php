<?php

if (!defined('ABSPATH')) {
    exit;
}

class MII_Blocks {

    public function __construct() {
        add_action('init', array($this, 'register_blocks'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
        add_action('enqueue_block_assets', array($this, 'enqueue_block_assets'));
    }

    public function register_blocks() {
        // Bloc 1 : Pages associées
        register_block_type('mii/pages-associees', array(
            'editor_script' => 'mii-blocks-js',
            'editor_style' => 'mii-blocks-editor-css',
            'style' => 'mii-blocks-css',
            'render_callback' => array($this, 'render_pages_associees'),
            'attributes' => array(
                'relationType' => array(
                    'type' => 'string',
                    'default' => 'children'
                ),
                'numberOfElements' => array(
                    'type' => 'number',
                    'default' => 0
                ),
                'showFeaturedImage' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'imageSize' => array(
                    'type' => 'string',
                    'default' => 'medium'
                ),
                'imageRatio' => array(
                    'type' => 'string',
                    'default' => '16:9'
                ),
                'cropImage' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'imagePosition' => array(
                    'type' => 'string',
                    'default' => 'top'
                ),
                'titleTag' => array(
                    'type' => 'string',
                    'default' => 'h3'
                ),
                'titleColor' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'showExcerpt' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'excerptLength' => array(
                    'type' => 'number',
                    'default' => 20
                ),
                'excerptColor' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'displayType' => array(
                    'type' => 'string',
                    'default' => 'grid'
                ),
                'columns' => array(
                    'type' => 'number',
                    'default' => 2
                ),
                'marginTop' => array(
                    'type' => 'number',
                    'default' => 0
                ),
                'marginBottom' => array(
                    'type' => 'number',
                    'default' => 0
                ),
                'paddingTop' => array(
                    'type' => 'number',
                    'default' => 0
                ),
                'paddingBottom' => array(
                    'type' => 'number',
                    'default' => 0
                )
            )
        ));

        // Bloc 2 : Publications dynamiques
        register_block_type('mii/publications-dynamiques', array(
            'editor_script' => 'mii-blocks-js',
            'editor_style' => 'mii-blocks-editor-css',
            'style' => 'mii-blocks-css',
            'render_callback' => array($this, 'render_publications_dynamiques'),
            'attributes' => array(
                'selectionMode' => array(
                    'type' => 'string',
                    'default' => 'search'
                ),
                'postType' => array(
                    'type' => 'string',
                    'default' => 'post'
                ),
                'searchQuery' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'taxonomy' => array(
                    'type' => 'string',
                    'default' => 'category'
                ),
                'term' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'similarityCount' => array(
                    'type' => 'number',
                    'default' => 5
                ),
                // Attributs communs avec le bloc 1
                'numberOfElements' => array(
                    'type' => 'number',
                    'default' => 0
                ),
                'showFeaturedImage' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'imageSize' => array(
                    'type' => 'string',
                    'default' => 'medium'
                ),
                'imageRatio' => array(
                    'type' => 'string',
                    'default' => '16:9'
                ),
                'cropImage' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'imagePosition' => array(
                    'type' => 'string',
                    'default' => 'top'
                ),
                'titleTag' => array(
                    'type' => 'string',
                    'default' => 'h3'
                ),
                'titleColor' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'showExcerpt' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'excerptLength' => array(
                    'type' => 'number',
                    'default' => 20
                ),
                'excerptColor' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'displayType' => array(
                    'type' => 'string',
                    'default' => 'grid'
                ),
                'columns' => array(
                    'type' => 'number',
                    'default' => 2
                ),
                'marginTop' => array(
                    'type' => 'number',
                    'default' => 0
                ),
                'marginBottom' => array(
                    'type' => 'number',
                    'default' => 0
                ),
                'paddingTop' => array(
                    'type' => 'number',
                    'default' => 0
                ),
                'paddingBottom' => array(
                    'type' => 'number',
                    'default' => 0
                )
            )
        ));
    }

    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'mii-blocks-js',
            MII_PLUGIN_URL . 'assets/js/blocks.js',
            array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-block-editor'),
            MII_PLUGIN_VERSION,
            true
        );

        wp_enqueue_style(
            'mii-blocks-editor-css',
            MII_PLUGIN_URL . 'assets/css/blocks-editor.css',
            array('wp-edit-blocks'),
            MII_PLUGIN_VERSION
        );

        wp_localize_script('mii-blocks-js', 'miiBlocks', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mii_blocks_nonce'),
            'postTypes' => $this->get_post_types(),
            'taxonomies' => $this->get_taxonomies(),
        ));
    }

    public function enqueue_block_assets() {
        wp_enqueue_style(
            'mii-blocks-css',
            MII_PLUGIN_URL . 'assets/css/blocks.css',
            array(),
            MII_PLUGIN_VERSION
        );
    }

    public function render_pages_associees($attributes) {
        global $post;

        if (!$post) {
            return '';
        }

        $relation_type = $attributes['relationType'];
        $number_of_elements = $attributes['numberOfElements'];

        $query_args = array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'posts_per_page' => $number_of_elements > 0 ? $number_of_elements : -1,
            'post__not_in' => array($post->ID)
        );

        if ($relation_type === 'children') {
            $query_args['post_parent'] = $post->ID;
        } elseif ($relation_type === 'siblings') {
            $query_args['post_parent'] = $post->post_parent;
        }

        $posts = get_posts($query_args);

        if (empty($posts)) {
            return '<p>' . __('Aucune page trouvée.', MII_TEXT_DOMAIN) . '</p>';
        }

        return $this->render_posts_list($posts, $attributes);
    }

    public function render_publications_dynamiques($attributes) {
        global $post;

        $selection_mode = $attributes['selectionMode'];
        $post_type = $attributes['postType'];
        $number_of_elements = $attributes['numberOfElements'];

        $posts = array();

        switch ($selection_mode) {
            case 'search':
                $posts = $this->get_posts_by_search($attributes);
                break;
            case 'taxonomy':
                $posts = $this->get_posts_by_taxonomy($attributes);
                break;
            case 'similarity':
                $posts = $this->get_posts_by_similarity($attributes);
                break;
        }

        if (empty($posts)) {
            return '<p>' . __('Aucune publication trouvée.', MII_TEXT_DOMAIN) . '</p>';
        }

        return $this->render_posts_list($posts, $attributes);
    }

    private function get_posts_by_search($attributes) {
        $search_query = $attributes['searchQuery'];
        $post_type = $attributes['postType'];
        $number_of_elements = $attributes['numberOfElements'];

        if (empty($search_query)) {
            return array();
        }

        $query_args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            's' => $search_query,
            'posts_per_page' => $number_of_elements > 0 ? $number_of_elements : -1
        );

        return get_posts($query_args);
    }

    private function get_posts_by_taxonomy($attributes) {
        $taxonomy = $attributes['taxonomy'];
        $term = $attributes['term'];
        $post_type = $attributes['postType'];
        $number_of_elements = $attributes['numberOfElements'];

        if (empty($term)) {
            return array();
        }

        $query_args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $number_of_elements > 0 ? $number_of_elements : -1,
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $term
                )
            )
        );

        return get_posts($query_args);
    }

    private function get_posts_by_similarity($attributes) {
        global $post;

        if (!$post) {
            return array();
        }

        $similarity_count = $attributes['similarityCount'];
        $post_type = $attributes['postType'];

        $similarity = new MII_Similarity();
        return $similarity->get_similar_posts($post->ID, $post_type, $similarity_count);
    }

    private function render_posts_list($posts, $attributes) {
        $display_type = $attributes['displayType'];
        $columns = $attributes['columns'];
        $show_image = $attributes['showFeaturedImage'];
        $image_size = $attributes['imageSize'];
        $image_ratio = $attributes['imageRatio'];
        $image_position = $attributes['imagePosition'];
        $title_tag = $attributes['titleTag'];
        $title_color = $attributes['titleColor'];
        $show_excerpt = $attributes['showExcerpt'];
        $excerpt_length = $attributes['excerptLength'];
        $excerpt_color = $attributes['excerptColor'];

        $css_class = 'mii-posts-list mii-' . $display_type;
        if ($display_type === 'grid') {
            $css_class .= ' mii-columns-' . $columns;
        }

        $style = '';
        if ($attributes['marginTop']) {
            $style .= 'margin-top: ' . $attributes['marginTop'] . 'px; ';
        }
        if ($attributes['marginBottom']) {
            $style .= 'margin-bottom: ' . $attributes['marginBottom'] . 'px; ';
        }
        if ($attributes['paddingTop']) {
            $style .= 'padding-top: ' . $attributes['paddingTop'] . 'px; ';
        }
        if ($attributes['paddingBottom']) {
            $style .= 'padding-bottom: ' . $attributes['paddingBottom'] . 'px; ';
        }

        $output = '<div class="' . esc_attr($css_class) . '"' . ($style ? ' style="' . esc_attr($style) . '"' : '') . '>';

        foreach ($posts as $post_item) {
            $output .= '<div class="mii-post-item">';

            // Image à la une
            if ($show_image && has_post_thumbnail($post_item->ID)) {
                $image_html = get_the_post_thumbnail($post_item->ID, $image_size, array(
                    'class' => 'mii-post-image mii-image-' . $image_position . ' mii-ratio-' . str_replace(':', '-', $image_ratio)
                ));
                $output .= '<div class="mii-post-image-wrapper">' . $image_html . '</div>';
            }

            // Contenu
            $output .= '<div class="mii-post-content">';

            // Titre
            $title_style = $title_color ? ' style="color: ' . esc_attr($title_color) . '"' : '';
            $output .= '<' . $title_tag . ' class="mii-post-title"' . $title_style . '>';
            $output .= '<a href="' . get_permalink($post_item->ID) . '">' . get_the_title($post_item->ID) . '</a>';
            $output .= '</' . $title_tag . '>';

            // Excerpt
            if ($show_excerpt) {
                $excerpt = wp_trim_words(get_the_excerpt($post_item->ID), $excerpt_length);
                $excerpt_style = $excerpt_color ? ' style="color: ' . esc_attr($excerpt_color) . '"' : '';
                $output .= '<div class="mii-post-excerpt"' . $excerpt_style . '>' . $excerpt . '</div>';
            }

            $output .= '</div>'; // .mii-post-content
            $output .= '</div>'; // .mii-post-item
        }

        $output .= '</div>'; // .mii-posts-list

        return $output;
    }

    private function get_post_types() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $result = array();

        foreach ($post_types as $post_type) {
            $result[] = array(
                'value' => $post_type->name,
                'label' => $post_type->label
            );
        }

        return $result;
    }

    private function get_taxonomies() {
        $taxonomies = get_taxonomies(array('public' => true), 'objects');
        $result = array();

        foreach ($taxonomies as $taxonomy) {
            $result[] = array(
                'value' => $taxonomy->name,
                'label' => $taxonomy->label
            );
        }

        return $result;
    }
}
?>