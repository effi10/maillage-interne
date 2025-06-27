<?php

if (!defined('ABSPATH')) {
    exit;
}

class MII_Ajax {

    public function __construct() {
        // Actions AJAX pour les utilisateurs connectés
        add_action('wp_ajax_mii_generate_embeddings', array($this, 'generate_embeddings'));
        add_action('wp_ajax_mii_clear_embeddings', array($this, 'clear_embeddings'));
        add_action('wp_ajax_mii_test_api_connection', array($this, 'test_api_connection'));
        add_action('wp_ajax_mii_get_embedding_stats', array($this, 'get_embedding_stats'));
        add_action('wp_ajax_mii_save_api_settings', array($this, 'save_api_settings'));
        add_action('wp_ajax_mii_get_taxonomy_terms', array($this, 'get_taxonomy_terms'));

        // Actions AJAX pour les utilisateurs non connectés (si nécessaire)
        // add_action('wp_ajax_nopriv_mii_action', array($this, 'action'));
    }

    public function generate_embeddings() {
        // Vérification de sécurité
        if (!wp_verify_nonce($_POST['nonce'], 'mii_nonce') || !current_user_can('manage_options')) {
            wp_die(__('Accès non autorisé', MII_TEXT_DOMAIN));
        }

        $post_types = isset($_POST['post_types']) ? (array) $_POST['post_types'] : array();
        $force_regenerate = isset($_POST['force_regenerate']) && $_POST['force_regenerate'] === 'true';

        if (empty($post_types)) {
            wp_send_json_error(__('Aucun type de contenu sélectionné', MII_TEXT_DOMAIN));
        }

        $embeddings = new MII_Embeddings();
        $result = $embeddings->bulk_generate_embeddings($post_types, $force_regenerate);

        $message = sprintf(
            __('%d embeddings générés sur %d publications au total.', MII_TEXT_DOMAIN),
            $result['processed'],
            $result['total']
        );

        if (!empty($result['errors'])) {
            $message .= ' ' . sprintf(
                __('%d erreurs rencontrées.', MII_TEXT_DOMAIN),
                count($result['errors'])
            );
        }

        wp_send_json_success(array(
            'message' => $message,
            'stats' => $result
        ));
    }

    public function clear_embeddings() {
        // Vérification de sécurité
        if (!wp_verify_nonce($_POST['nonce'], 'mii_nonce') || !current_user_can('manage_options')) {
            wp_die(__('Accès non autorisé', MII_TEXT_DOMAIN));
        }

        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '';

        $embeddings = new MII_Embeddings();

        if ($post_type === 'all') {
            $result = $embeddings->clear_all_embeddings();
            $message = __('Tous les embeddings ont été supprimés.', MII_TEXT_DOMAIN);
        } else {
            $result = $embeddings->clear_embeddings_by_post_type($post_type);
            $message = sprintf(
                __('Embeddings supprimés pour le type de contenu: %s', MII_TEXT_DOMAIN),
                $post_type
            );
        }

        if ($result !== false) {
            wp_send_json_success($message);
        } else {
            wp_send_json_error(__('Erreur lors de la suppression', MII_TEXT_DOMAIN));
        }
    }

    public function test_api_connection() {
        // Vérification de sécurité
        if (!wp_verify_nonce($_POST['nonce'], 'mii_nonce') || !current_user_can('manage_options')) {
            wp_die(__('Accès non autorisé', MII_TEXT_DOMAIN));
        }

        $provider = sanitize_text_field($_POST['provider']);
        $api_key = sanitize_text_field($_POST['api_key']);

        if (empty($provider) || empty($api_key)) {
            wp_send_json_error(__('Fournisseur ou clé API manquante', MII_TEXT_DOMAIN));
        }

        $result = MII_API_Client::validate_api_key($provider, $api_key);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    public function get_embedding_stats() {
        // Vérification de sécurité
        if (!wp_verify_nonce($_POST['nonce'], 'mii_nonce') || !current_user_can('manage_options')) {
            wp_die(__('Accès non autorisé', MII_TEXT_DOMAIN));
        }

        $embeddings = new MII_Embeddings();
        $stats = $embeddings->get_stats();

        wp_send_json_success($stats);
    }

    public function save_api_settings() {
        // Vérification de sécurité
        if (!wp_verify_nonce($_POST['nonce'], 'mii_nonce') || !current_user_can('manage_options')) {
            wp_die(__('Accès non autorisé', MII_TEXT_DOMAIN));
        }

        $provider = sanitize_text_field($_POST['provider']);
        $api_key = sanitize_text_field($_POST['api_key']);
        $model = sanitize_text_field($_POST['model']);
        $auto_update_post_types = isset($_POST['auto_update_post_types']) ? (array) $_POST['auto_update_post_types'] : array();

        // Valider la clé API
        $validation = MII_API_Client::validate_api_key($provider, $api_key);

        if (!$validation['success']) {
            wp_send_json_error(__('Clé API invalide', MII_TEXT_DOMAIN));
        }

        // Sauvegarder les paramètres
        update_option('mii_embedding_provider', $provider);
        update_option('mii_' . $provider . '_api_key', $api_key);
        update_option('mii_' . $provider . '_model', $model);
        update_option('mii_auto_update_post_types', $auto_update_post_types);

        wp_send_json_success(__('Paramètres API sauvegardés', MII_TEXT_DOMAIN));
    }

    public function get_taxonomy_terms() {
        // Vérification de sécurité basique
        if (!wp_verify_nonce($_POST['nonce'], 'mii_blocks_nonce')) {
            wp_die(__('Accès non autorisé', MII_TEXT_DOMAIN));
        }

        $taxonomy = sanitize_text_field($_POST['taxonomy']);

        if (empty($taxonomy)) {
            wp_send_json_error(__('Taxonomie non spécifiée', MII_TEXT_DOMAIN));
        }

        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));

        if (is_wp_error($terms)) {
            wp_send_json_error($terms->get_error_message());
        }

        $result = array();
        foreach ($terms as $term) {
            $result[] = array(
                'value' => $term->slug,
                'label' => $term->name . ' (' . $term->count . ')'
            );
        }

        wp_send_json_success($result);
    }
}
?>