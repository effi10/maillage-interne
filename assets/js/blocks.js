import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { 
    PanelBody, 
    SelectControl, 
    RangeControl, 
    ToggleControl, 
    TextControl,
    ColorPalette,
    RadioControl,
    CheckboxControl,
    Button
} from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { Fragment, useState, useEffect } from '@wordpress/element';
import { apiFetch } from '@wordpress/api-fetch';

// Bloc 1 : Pages associées
registerBlockType('mii/pages-associees', {
    title: __('Pages Associées', 'maillage-interne-intelligente'),
    description: __('Affiche les pages enfants ou sœurs de la page actuelle', 'maillage-interne-intelligente'),
    category: 'widgets',
    icon: 'admin-links',
    keywords: [__('pages', 'maillage-interne-intelligente'), __('enfants', 'maillage-interne-intelligente'), __('navigation', 'maillage-interne-intelligente')],

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const {
            relationType,
            numberOfElements,
            showFeaturedImage,
            imageSize,
            imageRatio,
            cropImage,
            imagePosition,
            titleTag,
            titleColor,
            showExcerpt,
            excerptLength,
            excerptColor,
            displayType,
            columns,
            marginTop,
            marginBottom,
            paddingTop,
            paddingBottom
        } = attributes;

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title={__('Paramètres de relation', 'maillage-interne-intelligente')}>
                        <RadioControl
                            label={__('Type de relation', 'maillage-interne-intelligente')}
                            selected={relationType}
                            options={[
                                { label: __('Pages enfants', 'maillage-interne-intelligente'), value: 'children' },
                                { label: __('Pages sœurs', 'maillage-interne-intelligente'), value: 'siblings' }
                            ]}
                            onChange={(value) => setAttributes({ relationType: value })}
                        />

                        <RangeControl
                            label={__('Nombre d'éléments', 'maillage-interne-intelligente')}
                            value={numberOfElements}
                            onChange={(value) => setAttributes({ numberOfElements: value })}
                            min={0}
                            max={20}
                            help={__('0 = tous les éléments', 'maillage-interne-intelligente')}
                        />
                    </PanelBody>

                    <PanelBody title={__('Image à la une', 'maillage-interne-intelligente')} initialOpen={false}>
                        <ToggleControl
                            label={__('Afficher l'image à la une', 'maillage-interne-intelligente')}
                            checked={showFeaturedImage}
                            onChange={(value) => setAttributes({ showFeaturedImage: value })}
                        />

                        {showFeaturedImage && (
                            <Fragment>
                                <SelectControl
                                    label={__('Taille d'image', 'maillage-interne-intelligente')}
                                    value={imageSize}
                                    options={[
                                        { label: __('Miniature', 'maillage-interne-intelligente'), value: 'thumbnail' },
                                        { label: __('Moyenne', 'maillage-interne-intelligente'), value: 'medium' },
                                        { label: __('Grande', 'maillage-interne-intelligente'), value: 'large' },
                                        { label: __('Pleine taille', 'maillage-interne-intelligente'), value: 'full' }
                                    ]}
                                    onChange={(value) => setAttributes({ imageSize: value })}
                                />

                                <SelectControl
                                    label={__('Ratio d'image', 'maillage-interne-intelligente')}
                                    value={imageRatio}
                                    options={[
                                        { label: '1:1', value: '1:1' },
                                        { label: '4:3', value: '4:3' },
                                        { label: '16:9', value: '16:9' }
                                    ]}
                                    onChange={(value) => setAttributes({ imageRatio: value })}
                                />

                                <ToggleControl
                                    label={__('Recadrage', 'maillage-interne-intelligente')}
                                    checked={cropImage}
                                    onChange={(value) => setAttributes({ cropImage: value })}
                                />

                                <SelectControl
                                    label={__('Position de l'image', 'maillage-interne-intelligente')}
                                    value={imagePosition}
                                    options={[
                                        { label: __('Haut', 'maillage-interne-intelligente'), value: 'top' },
                                        { label: __('Bas', 'maillage-interne-intelligente'), value: 'bottom' },
                                        { label: __('Gauche', 'maillage-interne-intelligente'), value: 'left' },
                                        { label: __('Droite', 'maillage-interne-intelligente'), value: 'right' }
                                    ]}
                                    onChange={(value) => setAttributes({ imagePosition: value })}
                                />
                            </Fragment>
                        )}
                    </PanelBody>

                    <PanelBody title={__('Titre', 'maillage-interne-intelligente')} initialOpen={false}>
                        <SelectControl
                            label={__('Balise HTML', 'maillage-interne-intelligente')}
                            value={titleTag}
                            options={[
                                { label: 'span', value: 'span' },
                                { label: 'h2', value: 'h2' },
                                { label: 'h3', value: 'h3' },
                                { label: 'h4', value: 'h4' },
                                { label: 'h5', value: 'h5' },
                                { label: 'h6', value: 'h6' }
                            ]}
                            onChange={(value) => setAttributes({ titleTag: value })}
                        />

                        <div>
                            <label>{__('Couleur du titre', 'maillage-interne-intelligente')}</label>
                            <ColorPalette
                                value={titleColor}
                                onChange={(value) => setAttributes({ titleColor: value })}
                            />
                        </div>
                    </PanelBody>

                    <PanelBody title={__('Extrait', 'maillage-interne-intelligente')} initialOpen={false}>
                        <ToggleControl
                            label={__('Afficher l'extrait', 'maillage-interne-intelligente')}
                            checked={showExcerpt}
                            onChange={(value) => setAttributes({ showExcerpt: value })}
                        />

                        {showExcerpt && (
                            <Fragment>
                                <RangeControl
                                    label={__('Nombre de mots maximum', 'maillage-interne-intelligente')}
                                    value={excerptLength}
                                    onChange={(value) => setAttributes({ excerptLength: value })}
                                    min={5}
                                    max={100}
                                />

                                <div>
                                    <label>{__('Couleur de l'extrait', 'maillage-interne-intelligente')}</label>
                                    <ColorPalette
                                        value={excerptColor}
                                        onChange={(value) => setAttributes({ excerptColor: value })}
                                    />
                                </div>
                            </Fragment>
                        )}
                    </PanelBody>

                    <PanelBody title={__('Affichage', 'maillage-interne-intelligente')} initialOpen={false}>
                        <RadioControl
                            label={__('Type d'affichage', 'maillage-interne-intelligente')}
                            selected={displayType}
                            options={[
                                { label: __('Liste', 'maillage-interne-intelligente'), value: 'list' },
                                { label: __('Grille', 'maillage-interne-intelligente'), value: 'grid' }
                            ]}
                            onChange={(value) => setAttributes({ displayType: value })}
                        />

                        {displayType === 'grid' && (
                            <RangeControl
                                label={__('Nombre de colonnes', 'maillage-interne-intelligente')}
                                value={columns}
                                onChange={(value) => setAttributes({ columns: value })}
                                min={1}
                                max={4}
                            />
                        )}
                    </PanelBody>

                    <PanelBody title={__('Espacement', 'maillage-interne-intelligente')} initialOpen={false}>
                        <RangeControl
                            label={__('Marge haute (px)', 'maillage-interne-intelligente')}
                            value={marginTop}
                            onChange={(value) => setAttributes({ marginTop: value })}
                            min={0}
                            max={100}
                        />

                        <RangeControl
                            label={__('Marge basse (px)', 'maillage-interne-intelligente')}
                            value={marginBottom}
                            onChange={(value) => setAttributes({ marginBottom: value })}
                            min={0}
                            max={100}
                        />

                        <RangeControl
                            label={__('Padding haut (px)', 'maillage-interne-intelligente')}
                            value={paddingTop}
                            onChange={(value) => setAttributes({ paddingTop: value })}
                            min={0}
                            max={100}
                        />

                        <RangeControl
                            label={__('Padding bas (px)', 'maillage-interne-intelligente')}
                            value={paddingBottom}
                            onChange={(value) => setAttributes({ paddingBottom: value })}
                            min={0}
                            max={100}
                        />
                    </PanelBody>
                </InspectorControls>

                <div className="mii-block-preview">
                    <h4>{__('Pages Associées', 'maillage-interne-intelligente')}</h4>
                    <p>{__('Type:', 'maillage-interne-intelligente')} {relationType === 'children' ? __('Pages enfants', 'maillage-interne-intelligente') : __('Pages sœurs', 'maillage-interne-intelligente')}</p>
                    <p>{__('Affichage:', 'maillage-interne-intelligente')} {displayType === 'grid' ? __('Grille', 'maillage-interne-intelligente') + ' (' + columns + ' ' + __('colonnes', 'maillage-interne-intelligente') + ')' : __('Liste', 'maillage-interne-intelligente')}</p>
                    <p>{__('Éléments:', 'maillage-interne-intelligente')} {numberOfElements === 0 ? __('Tous', 'maillage-interne-intelligente') : numberOfElements}</p>
                </div>
            </Fragment>
        );
    },

    save: function() {
        // Le rendu se fait côté serveur
        return null;
    }
});

// Bloc 2 : Publications dynamiques
registerBlockType('mii/publications-dynamiques', {
    title: __('Publications Dynamiques', 'maillage-interne-intelligente'),
    description: __('Affiche des publications selon différents critères', 'maillage-interne-intelligente'),
    category: 'widgets',
    icon: 'admin-post',
    keywords: [__('posts', 'maillage-interne-intelligente'), __('articles', 'maillage-interne-intelligente'), __('similaire', 'maillage-interne-intelligente')],

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const {
            selectionMode,
            postType,
            searchQuery,
            taxonomy,
            term,
            similarityCount,
            numberOfElements,
            showFeaturedImage,
            imageSize,
            imageRatio,
            cropImage,
            imagePosition,
            titleTag,
            titleColor,
            showExcerpt,
            excerptLength,
            excerptColor,
            displayType,
            columns,
            marginTop,
            marginBottom,
            paddingTop,
            paddingBottom
        } = attributes;

        const [taxonomyTerms, setTaxonomyTerms] = useState([]);

        // Charger les termes de taxonomie
        useEffect(() => {
            if (taxonomy && selectionMode === 'taxonomy') {
                wp.ajax.post('mii_get_taxonomy_terms', {
                    nonce: miiBlocks.nonce,
                    taxonomy: taxonomy
                }).done(function(response) {
                    setTaxonomyTerms(response);
                });
            }
        }, [taxonomy, selectionMode]);

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title={__('Paramètres de sélection', 'maillage-interne-intelligente')}>
                        <SelectControl
                            label={__('Type de contenu', 'maillage-interne-intelligente')}
                            value={postType}
                            options={miiBlocks.postTypes}
                            onChange={(value) => setAttributes({ postType: value })}
                        />

                        <RadioControl
                            label={__('Mode de sélection', 'maillage-interne-intelligente')}
                            selected={selectionMode}
                            options={[
                                { label: __('Recherche', 'maillage-interne-intelligente'), value: 'search' },
                                { label: __('Taxonomie', 'maillage-interne-intelligente'), value: 'taxonomy' },
                                { label: __('Similarité sémantique', 'maillage-interne-intelligente'), value: 'similarity' }
                            ]}
                            onChange={(value) => setAttributes({ selectionMode: value })}
                        />

                        {selectionMode === 'search' && (
                            <TextControl
                                label={__('Mot-clé de recherche', 'maillage-interne-intelligente')}
                                value={searchQuery}
                                onChange={(value) => setAttributes({ searchQuery: value })}
                                placeholder={__('Entrer un mot-clé...', 'maillage-interne-intelligente')}
                            />
                        )}

                        {selectionMode === 'taxonomy' && (
                            <Fragment>
                                <SelectControl
                                    label={__('Taxonomie', 'maillage-interne-intelligente')}
                                    value={taxonomy}
                                    options={miiBlocks.taxonomies}
                                    onChange={(value) => setAttributes({ taxonomy: value })}
                                />

                                {taxonomyTerms.length > 0 && (
                                    <SelectControl
                                        label={__('Terme', 'maillage-interne-intelligente')}
                                        value={term}
                                        options={[
                                            { label: __('Sélectionner un terme...', 'maillage-interne-intelligente'), value: '' },
                                            ...taxonomyTerms
                                        ]}
                                        onChange={(value) => setAttributes({ term: value })}
                                    />
                                )}
                            </Fragment>
                        )}

                        {selectionMode === 'similarity' && (
                            <RangeControl
                                label={__('Nombre d'éléments similaires', 'maillage-interne-intelligente')}
                                value={similarityCount}
                                onChange={(value) => setAttributes({ similarityCount: value })}
                                min={1}
                                max={20}
                            />
                        )}

                        <RangeControl
                            label={__('Nombre d'éléments à afficher', 'maillage-interne-intelligente')}
                            value={numberOfElements}
                            onChange={(value) => setAttributes({ numberOfElements: value })}
                            min={0}
                            max={20}
                            help={__('0 = tous les résultats', 'maillage-interne-intelligente')}
                        />
                    </PanelBody>

                    {/* Panneaux identiques au bloc 1 pour l'affichage */}
                    <PanelBody title={__('Image à la une', 'maillage-interne-intelligente')} initialOpen={false}>
                        <ToggleControl
                            label={__('Afficher l'image à la une', 'maillage-interne-intelligente')}
                            checked={showFeaturedImage}
                            onChange={(value) => setAttributes({ showFeaturedImage: value })}
                        />

                        {showFeaturedImage && (
                            <Fragment>
                                <SelectControl
                                    label={__('Taille d'image', 'maillage-interne-intelligente')}
                                    value={imageSize}
                                    options={[
                                        { label: __('Miniature', 'maillage-interne-intelligente'), value: 'thumbnail' },
                                        { label: __('Moyenne', 'maillage-interne-intelligente'), value: 'medium' },
                                        { label: __('Grande', 'maillage-interne-intelligente'), value: 'large' },
                                        { label: __('Pleine taille', 'maillage-interne-intelligente'), value: 'full' }
                                    ]}
                                    onChange={(value) => setAttributes({ imageSize: value })}
                                />

                                <SelectControl
                                    label={__('Ratio d'image', 'maillage-interne-intelligente')}
                                    value={imageRatio}
                                    options={[
                                        { label: '1:1', value: '1:1' },
                                        { label: '4:3', value: '4:3' },
                                        { label: '16:9', value: '16:9' }
                                    ]}
                                    onChange={(value) => setAttributes({ imageRatio: value })}
                                />

                                <SelectControl
                                    label={__('Position de l'image', 'maillage-interne-intelligente')}
                                    value={imagePosition}
                                    options={[
                                        { label: __('Haut', 'maillage-interne-intelligente'), value: 'top' },
                                        { label: __('Bas', 'maillage-interne-intelligente'), value: 'bottom' },
                                        { label: __('Gauche', 'maillage-interne-intelligente'), value: 'left' },
                                        { label: __('Droite', 'maillage-interne-intelligente'), value: 'right' }
                                    ]}
                                    onChange={(value) => setAttributes({ imagePosition: value })}
                                />
                            </Fragment>
                        )}
                    </PanelBody>

                    <PanelBody title={__('Affichage', 'maillage-interne-intelligente')} initialOpen={false}>
                        <RadioControl
                            label={__('Type d'affichage', 'maillage-interne-intelligente')}
                            selected={displayType}
                            options={[
                                { label: __('Liste', 'maillage-interne-intelligente'), value: 'list' },
                                { label: __('Grille', 'maillage-interne-intelligente'), value: 'grid' }
                            ]}
                            onChange={(value) => setAttributes({ displayType: value })}
                        />

                        {displayType === 'grid' && (
                            <RangeControl
                                label={__('Nombre de colonnes', 'maillage-interne-intelligente')}
                                value={columns}
                                onChange={(value) => setAttributes({ columns: value })}
                                min={1}
                                max={4}
                            />
                        )}
                    </PanelBody>
                </InspectorControls>

                <div className="mii-block-preview">
                    <h4>{__('Publications Dynamiques', 'maillage-interne-intelligente')}</h4>
                    <p>{__('Type:', 'maillage-interne-intelligente')} {postType}</p>
                    <p>{__('Mode:', 'maillage-interne-intelligente')} {
                        selectionMode === 'search' ? __('Recherche', 'maillage-interne-intelligente') + (searchQuery ? ' ("' + searchQuery + '")' : '') :
                        selectionMode === 'taxonomy' ? __('Taxonomie', 'maillage-interne-intelligente') + (term ? ' (' + term + ')' : '') :
                        __('Similarité sémantique', 'maillage-interne-intelligente')
                    }</p>
                    <p>{__('Affichage:', 'maillage-interne-intelligente')} {displayType === 'grid' ? __('Grille', 'maillage-interne-intelligente') + ' (' + columns + ' ' + __('colonnes', 'maillage-interne-intelligente') + ')' : __('Liste', 'maillage-interne-intelligente')}</p>
                </div>
            </Fragment>
        );
    },

    save: function() {
        // Le rendu se fait côté serveur
        return null;
    }
});