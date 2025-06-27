<?php
if (!defined('ABSPATH')) {
    exit;
}

$embedding_provider = get_option('mii_embedding_provider', 'openai');
$openai_api_key = get_option('mii_openai_api_key', '');
$gemini_api_key = get_option('mii_gemini_api_key', '');
$openai_model = get_option('mii_openai_model', 'text-embedding-3-small');
$gemini_model = get_option('mii_gemini_model', 'text-embedding-004');
$auto_update_post_types = get_option('mii_auto_update_post_types', array('post', 'page'));

$post_types = get_post_types(array('public' => true), 'objects');
?>

<div class="wrap">
    <h1><?php _e('Gestion des Embeddings', MII_TEXT_DOMAIN); ?></h1>

    <div class="mii-admin-tabs">
        <nav class="nav-tab-wrapper">
            <a href="#api-settings" class="nav-tab nav-tab-active"><?php _e('Clés API', MII_TEXT_DOMAIN); ?></a>
            <a href="#embeddings-generation" class="nav-tab"><?php _e('Calcul des embeddings', MII_TEXT_DOMAIN); ?></a>
            <a href="#embeddings-stats" class="nav-tab"><?php _e('Statistiques', MII_TEXT_DOMAIN); ?></a>
        </nav>

        <!-- Onglet Clés API -->
        <div id="api-settings" class="tab-content">
            <h2><?php _e('Configuration des API', MII_TEXT_DOMAIN); ?></h2>

            <form id="mii-api-form">
                <?php wp_nonce_field('mii_nonce', 'mii_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label><?php _e('Fournisseur d\'embedding', MII_TEXT_DOMAIN); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="radio" name="provider" value="openai" <?php checked($embedding_provider, 'openai'); ?> />
                                OpenAI
                            </label><br>
                            <label>
                                <input type="radio" name="provider" value="gemini" <?php checked($embedding_provider, 'gemini'); ?> />
                                Google Gemini
                            </label>
                        </td>
                    </tr>
                </table>

                <!-- Configuration OpenAI -->
                <div id="openai-config" class="provider-config" style="<?php echo $embedding_provider !== 'openai' ? 'display:none;' : ''; ?>">
                    <h3><?php _e('Configuration OpenAI', MII_TEXT_DOMAIN); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="openai_api_key"><?php _e('Clé API OpenAI', MII_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <input type="password" id="openai_api_key" name="openai_api_key" value="<?php echo esc_attr($openai_api_key); ?>" class="regular-text" />
                                <p class="description"><?php _e('Obtenez votre clé API sur platform.openai.com', MII_TEXT_DOMAIN); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="openai_model"><?php _e('Modèle OpenAI', MII_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <select id="openai_model" name="openai_model">
                                    <option value="text-embedding-3-small" <?php selected($openai_model, 'text-embedding-3-small'); ?>>text-embedding-3-small</option>
                                    <option value="text-embedding-3-large" <?php selected($openai_model, 'text-embedding-3-large'); ?>>text-embedding-3-large</option>
                                    <option value="text-embedding-ada-002" <?php selected($openai_model, 'text-embedding-ada-002'); ?>>text-embedding-ada-002</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Configuration Gemini -->
                <div id="gemini-config" class="provider-config" style="<?php echo $embedding_provider !== 'gemini' ? 'display:none;' : ''; ?>">
                    <h3><?php _e('Configuration Google Gemini', MII_TEXT_DOMAIN); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="gemini_api_key"><?php _e('Clé API Gemini', MII_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <input type="password" id="gemini_api_key" name="gemini_api_key" value="<?php echo esc_attr($gemini_api_key); ?>" class="regular-text" />
                                <p class="description"><?php _e('Obtenez votre clé API sur ai.google.dev', MII_TEXT_DOMAIN); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="gemini_model"><?php _e('Modèle Gemini', MII_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <select id="gemini_model" name="gemini_model">
                                    <option value="text-embedding-004" <?php selected($gemini_model, 'text-embedding-004'); ?>>text-embedding-004</option>
                                    <option value="embedding-001" <?php selected($gemini_model, 'embedding-001'); ?>>embedding-001</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Mise à jour automatique -->
                <h3><?php _e('Mise à jour automatique', MII_TEXT_DOMAIN); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label><?php _e('Types de contenu à mettre à jour automatiquement', MII_TEXT_DOMAIN); ?></label>
                        </th>
                        <td>
                            <?php foreach ($post_types as $post_type): ?>
                                <label>
                                    <input type="checkbox" name="auto_update_post_types[]" value="<?php echo esc_attr($post_type->name); ?>" <?php checked(in_array($post_type->name, $auto_update_post_types)); ?> />
                                    <?php echo esc_html($post_type->label); ?>
                                </label><br>
                            <?php endforeach; ?>
                            <p class="description"><?php _e('Les embeddings seront recalculés automatiquement lors de la création/mise à jour de ces types de contenu', MII_TEXT_DOMAIN); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="button" id="test-api-connection" class="button"><?php _e('Tester la connexion', MII_TEXT_DOMAIN); ?></button>
                    <button type="submit" class="button-primary"><?php _e('Sauvegarder les paramètres API', MII_TEXT_DOMAIN); ?></button>
                </p>
            </form>
        </div>

        <!-- Onglet Calcul des embeddings -->
        <div id="embeddings-generation" class="tab-content" style="display:none;">
            <h2><?php _e('Calcul des embeddings', MII_TEXT_DOMAIN); ?></h2>

            <form id="mii-generate-form">
                <?php wp_nonce_field('mii_nonce', 'mii_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label><?php _e('Types de contenu à traiter', MII_TEXT_DOMAIN); ?></label>
                        </th>
                        <td>
                            <?php foreach ($post_types as $post_type): ?>
                                <label>
                                    <input type="checkbox" name="post_types[]" value="<?php echo esc_attr($post_type->name); ?>" checked />
                                    <?php echo esc_html($post_type->label); ?>
                                </label><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="force_regenerate"><?php _e('Forcer la régénération', MII_TEXT_DOMAIN); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="force_regenerate" name="force_regenerate" value="1" />
                                <?php _e('Recalculer même si les embeddings existent déjà', MII_TEXT_DOMAIN); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button-primary"><?php _e('Calculer les embeddings', MII_TEXT_DOMAIN); ?></button>
                    <button type="button" id="clear-embeddings" class="button button-secondary"><?php _e('Supprimer tous les embeddings', MII_TEXT_DOMAIN); ?></button>
                </p>
            </form>

            <div id="embeddings-progress" style="display:none;">
                <h3><?php _e('Progression', MII_TEXT_DOMAIN); ?></h3>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%;"></div>
                </div>
                <p class="progress-text"></p>
            </div>
        </div>

        <!-- Onglet Statistiques -->
        <div id="embeddings-stats" class="tab-content" style="display:none;">
            <h2><?php _e('Statistiques des embeddings', MII_TEXT_DOMAIN); ?></h2>

            <div id="stats-container">
                <button type="button" id="refresh-stats" class="button"><?php _e('Actualiser les statistiques', MII_TEXT_DOMAIN); ?></button>
                <div id="stats-content">
                    <!-- Les statistiques seront chargées ici via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.mii-admin-tabs .tab-content {
    border: 1px solid #ccd0d4;
    border-top: none;
    padding: 20px;
    background: #fff;
}

.provider-config {
    border-left: 4px solid #0073aa;
    padding-left: 15px;
    margin: 20px 0;
}

.progress-bar {
    width: 100%;
    height: 20px;
    background-color: #f1f1f1;
    border-radius: 10px;
    overflow: hidden;
    margin: 10px 0;
}

.progress-fill {
    height: 100%;
    background-color: #0073aa;
    transition: width 0.3s ease;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stats-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
}

.stats-number {
    font-size: 2em;
    font-weight: bold;
    color: #0073aa;
}
</style>