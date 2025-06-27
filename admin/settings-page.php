<?php
if (!defined('ABSPATH')) {
    exit;
}

$default_elements = get_option('mii_default_elements', 3);
$default_image_size = get_option('mii_default_image_size', 'medium');
$default_image_ratio = get_option('mii_default_image_ratio', '16:9');
$default_title_tag = get_option('mii_default_title_tag', 'h3');
$default_columns = get_option('mii_default_columns', 2);
$responsive_breakpoint = get_option('mii_responsive_breakpoint', 768);
$title_color = get_option('mii_title_color', '#333333');
$excerpt_color = get_option('mii_excerpt_color', '#666666');
$auto_apply_defaults = get_option('mii_auto_apply_defaults', 0);
?>

<div class="wrap">
    <h1><?php _e('Paramètres du Maillage Interne', MII_TEXT_DOMAIN); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('mii_settings', 'mii_nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="default_elements"><?php _e('Nombre d\'éléments par défaut', MII_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="number" id="default_elements" name="default_elements" value="<?php echo esc_attr($default_elements); ?>" min="0" class="small-text" />
                    <p class="description"><?php _e('0 = tous les éléments', MII_TEXT_DOMAIN); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="default_image_size"><?php _e('Taille d\'image par défaut', MII_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <select id="default_image_size" name="default_image_size">
                        <option value="thumbnail" <?php selected($default_image_size, 'thumbnail'); ?>><?php _e('Miniature', MII_TEXT_DOMAIN); ?></option>
                        <option value="medium" <?php selected($default_image_size, 'medium'); ?>><?php _e('Moyenne', MII_TEXT_DOMAIN); ?></option>
                        <option value="large" <?php selected($default_image_size, 'large'); ?>><?php _e('Grande', MII_TEXT_DOMAIN); ?></option>
                        <option value="full" <?php selected($default_image_size, 'full'); ?>><?php _e('Pleine taille', MII_TEXT_DOMAIN); ?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="default_image_ratio"><?php _e('Ratio d\'image par défaut', MII_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <select id="default_image_ratio" name="default_image_ratio">
                        <option value="1:1" <?php selected($default_image_ratio, '1:1'); ?>>1:1</option>
                        <option value="4:3" <?php selected($default_image_ratio, '4:3'); ?>>4:3</option>
                        <option value="16:9" <?php selected($default_image_ratio, '16:9'); ?>>16:9</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="default_title_tag"><?php _e('Balise HTML par défaut pour les titres', MII_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <select id="default_title_tag" name="default_title_tag">
                        <option value="span" <?php selected($default_title_tag, 'span'); ?>>span</option>
                        <option value="h2" <?php selected($default_title_tag, 'h2'); ?>>h2</option>
                        <option value="h3" <?php selected($default_title_tag, 'h3'); ?>>h3</option>
                        <option value="h4" <?php selected($default_title_tag, 'h4'); ?>>h4</option>
                        <option value="h5" <?php selected($default_title_tag, 'h5'); ?>>h5</option>
                        <option value="h6" <?php selected($default_title_tag, 'h6'); ?>>h6</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="default_columns"><?php _e('Nombre de colonnes par défaut', MII_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <select id="default_columns" name="default_columns">
                        <option value="1" <?php selected($default_columns, 1); ?>>1</option>
                        <option value="2" <?php selected($default_columns, 2); ?>>2</option>
                        <option value="3" <?php selected($default_columns, 3); ?>>3</option>
                        <option value="4" <?php selected($default_columns, 4); ?>>4</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="responsive_breakpoint"><?php _e('Point de bascule responsive (px)', MII_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="number" id="responsive_breakpoint" name="responsive_breakpoint" value="<?php echo esc_attr($responsive_breakpoint); ?>" min="320" max="1200" class="small-text" />
                    <p class="description"><?php _e('En dessous de cette largeur, l\'affichage passe en liste', MII_TEXT_DOMAIN); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="title_color"><?php _e('Couleur des titres', MII_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="color" id="title_color" name="title_color" value="<?php echo esc_attr($title_color); ?>" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="excerpt_color"><?php _e('Couleur des excerpts', MII_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="color" id="excerpt_color" name="excerpt_color" value="<?php echo esc_attr($excerpt_color); ?>" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="auto_apply_defaults"><?php _e('Application automatique', MII_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <label for="auto_apply_defaults">
                        <input type="checkbox" id="auto_apply_defaults" name="auto_apply_defaults" value="1" <?php checked($auto_apply_defaults, 1); ?> />
                        <?php _e('Appliquer ces réglages par défaut aux nouveaux blocs automatiquement', MII_TEXT_DOMAIN); ?>
                    </label>
                </td>
            </tr>
        </table>

        <?php submit_button(__('Sauvegarder les paramètres', MII_TEXT_DOMAIN)); ?>
    </form>
</div>