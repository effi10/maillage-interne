<?php

if (!defined('ABSPATH')) {
    exit;
}

class MII_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('manage_posts_columns', array($this, 'add_embeddings_column'));
        add_action('manage_pages_columns', array($this, 'add_embeddings_column'));
        add_action('manage_posts_custom_column', array($this, 'show_embeddings_column'), 10, 2);
        add_action('manage_pages_custom_column', array($this, 'show_embeddings_column'), 10, 2);
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Maillage Interne', MII_TEXT_DOMAIN),
            __('Maillage Interne', MII_TEXT_DOMAIN),
            'manage_options',
            'maillage-interne',
            array($this, 'admin_page'),
            'dashicons-admin-links',
            30
        );

        add_submenu_page(
            'maillage-interne',
            __('Paramètres', MII_TEXT_DOMAIN),
            __('Paramètres', MII_TEXT_DOMAIN),
            'manage_options',
            'maillage-interne',
            array($this, 'admin_page')
        );

        add_submenu_page(
            'maillage-interne',
            __('Embeddings', MII_TEXT_DOMAIN),
            __('Embeddings', MII_TEXT_DOMAIN),
            'manage_options',
            'maillage-interne-embeddings',
            array($this, 'embeddings_page')
        );
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'maillage-interne') === false) {
            return;
        }

        wp_enqueue_script(
            'mii-admin-js',
            MII_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            MII_PLUGIN_VERSION,
            true
        );

        wp_enqueue_style(
            'mii-admin-css',
            MII_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            MII_PLUGIN_VERSION
        );

        wp_localize_script('mii-admin-js', 'miiAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mii_nonce'),
            'strings' => array(
                'processing' => __('Traitement en cours...', MII_TEXT_DOMAIN),
                'success' => __('Succès', MII_TEXT_DOMAIN),
                'error' => __('Erreur', MII_TEXT_DOMAIN),
                'confirm_delete' => __('Êtes-vous sûr de vouloir supprimer tous les embeddings ?', MII_TEXT_DOMAIN),
            )
        ));
    }

    public function admin_page() {
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['mii_nonce'], 'mii_settings')) {
            $this->save_settings();
        }

        include MII_PLUGIN_PATH . 'admin/settings-page.php';
    }

    public function embeddings_page() {
        include MII_PLUGIN_PATH . 'admin/embeddings-page.php';
    }

    private function save_settings() {
        $settings = array(
            'mii_default_elements' => intval($_POST['default_elements']),
            'mii_default_image_size' => sanitize_text_field($_POST['default_image_size']),
            'mii_default_image_ratio' => sanitize_text_field($_POST['default_image_ratio']),
            'mii_default_title_tag' => sanitize_text_field($_POST['default_title_tag']),
            'mii_default_columns' => intval($_POST['default_columns']),
            'mii_responsive_breakpoint' => intval($_POST['responsive_breakpoint']),
            'mii_title_color' => sanitize_hex_color($_POST['title_color']),
            'mii_excerpt_color' => sanitize_hex_color($_POST['excerpt_color']),
            'mii_auto_apply_defaults' => isset($_POST['auto_apply_defaults']) ? 1 : 0,
        );

        foreach ($settings as $key => $value) {
            update_option($key, $value);
        }

        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>' . 
                 __('Paramètres sauvegardés avec succès.', MII_TEXT_DOMAIN) . '</p></div>';
        });
    }

    public function add_embeddings_column($columns) {
        $columns['mii_embeddings'] = __('Embeddings', MII_TEXT_DOMAIN);
        return $columns;
    }

    public function show_embeddings_column($column, $post_id) {
        if ($column === 'mii_embeddings') {
            global $wpdb;
            $table_name = $wpdb->prefix . 'mii_embeddings';

            $result = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE post_id = %d",
                $post_id
            ));

            if ($result) {
                echo '<span style="color: green;">✓ ' . __('Oui', MII_TEXT_DOMAIN) . '</span>';
            } else {
                echo '<span style="color: red;">✗ ' . __('Non', MII_TEXT_DOMAIN) . '</span>';
            }
        }
    }
}
?>