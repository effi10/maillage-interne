<?php

if (!defined('ABSPATH')) {
    exit;
}

class MII_API_Client {

    private $provider;
    private $api_key;
    private $model;

    public function __construct() {
        $this->provider = get_option('mii_embedding_provider', 'openai');
        $this->api_key = get_option('mii_' . $this->provider . '_api_key', '');
        $this->model = $this->get_model();
    }

    private function get_model() {
        switch ($this->provider) {
            case 'openai':
                return get_option('mii_openai_model', 'text-embedding-3-small');
            case 'gemini':
                return get_option('mii_gemini_model', 'text-embedding-004');
            default:
                return 'text-embedding-3-small';
        }
    }

    public function generate_embedding($text) {
        if (empty($this->api_key)) {
            error_log('MII: API key not configured for ' . $this->provider);
            return false;
        }

        switch ($this->provider) {
            case 'openai':
                return $this->generate_openai_embedding($text);
            case 'gemini':
                return $this->generate_gemini_embedding($text);
            default:
                return false;
        }
    }

    private function generate_openai_embedding($text) {
        $url = 'https://api.openai.com/v1/embeddings';

        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type' => 'application/json',
        );

        $body = array(
            'input' => $text,
            'model' => $this->model,
            'encoding_format' => 'float'
        );

        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 60
        ));

        if (is_wp_error($response)) {
            error_log('MII OpenAI API Error: ' . $response->get_error_message());
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        if ($response_code !== 200) {
            error_log('MII OpenAI API Error: HTTP ' . $response_code . ' - ' . $response_body);
            return false;
        }

        $data = json_decode($response_body, true);

        if (!isset($data['data'][0]['embedding'])) {
            error_log('MII OpenAI API Error: Invalid response format');
            return false;
        }

        return $data['data'][0]['embedding'];
    }

    private function generate_gemini_embedding($text) {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $this->model . ':embedContent?key=' . $this->api_key;

        $headers = array(
            'Content-Type' => 'application/json',
        );

        $body = array(
            'content' => array(
                'parts' => array(
                    array('text' => $text)
                )
            )
        );

        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 60
        ));

        if (is_wp_error($response)) {
            error_log('MII Gemini API Error: ' . $response->get_error_message());
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        if ($response_code !== 200) {
            error_log('MII Gemini API Error: HTTP ' . $response_code . ' - ' . $response_body);
            return false;
        }

        $data = json_decode($response_body, true);

        if (!isset($data['embedding']['values'])) {
            error_log('MII Gemini API Error: Invalid response format');
            return false;
        }

        return $data['embedding']['values'];
    }

    public function test_connection() {
        $test_text = "Test de connexion à l'API";
        $result = $this->generate_embedding($test_text);

        return array(
            'success' => $result !== false,
            'message' => $result !== false 
                ? __('Connexion réussie', MII_TEXT_DOMAIN)
                : __('Erreur de connexion', MII_TEXT_DOMAIN),
            'provider' => $this->provider,
            'model' => $this->model
        );
    }

    public function get_available_models() {
        switch ($this->provider) {
            case 'openai':
                return array(
                    'text-embedding-3-small' => 'Text Embedding 3 Small (1536 dimensions)',
                    'text-embedding-3-large' => 'Text Embedding 3 Large (3072 dimensions)',
                    'text-embedding-ada-002' => 'Text Embedding Ada 002 (1536 dimensions)'
                );
            case 'gemini':
                return array(
                    'text-embedding-004' => 'Text Embedding 004',
                    'embedding-001' => 'Embedding 001'
                );
            default:
                return array();
        }
    }

    public function get_rate_limits() {
        switch ($this->provider) {
            case 'openai':
                return array(
                    'requests_per_minute' => 3000,
                    'tokens_per_minute' => 1000000,
                    'batch_size' => 2048
                );
            case 'gemini':
                return array(
                    'requests_per_minute' => 1500,
                    'requests_per_day' => 50000,
                    'batch_size' => 100
                );
            default:
                return array();
        }
    }

    public static function validate_api_key($provider, $api_key) {
        $temp_client = new self();
        $temp_client->provider = $provider;
        $temp_client->api_key = $api_key;
        $temp_client->model = $temp_client->get_model();

        return $temp_client->test_connection();
    }
}
?>