<?php

declare(strict_types=1);

/*
 * This file is part of contao-custom-navigation-bundle.
 *
 * (c) heimseiten.de - Webdesign aus Köln 2026 <info@heimseiten.de>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/heimseiten/contao-custom-navigation-bundle
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/*
 * Einstellungen für die Untermenüs des Navigationsmoduls. Sie wirken mit dem Template
 * mod_navigation_accessible (oder einer Variante davon) und nur in der Desktop-Ansicht.
 */
PaletteManipulator::create()
    ->addLegend('custom_navigation_legend', 'template_legend', PaletteManipulator::POSITION_AFTER)
    ->addField(
        ['customNavDreieck', 'customNavWiedereinblenden', 'customNavLinksBeiPlatzmangel', 'customNavAnimation', 'customNavSchatten', 'customNavSymbol'],
        'custom_navigation_legend',
        PaletteManipulator::POSITION_APPEND,
    )
    ->applyToPalette('navigation', 'tl_module')
;

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'customNavWiedereinblenden';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'customNavAnimation';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'customNavSymbol';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['customNavWiedereinblenden'] = 'customNavWiedereinblendenDauer';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['customNavAnimation'] = 'customNavAnimationDauer';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['customNavSymbol_eigenes'] = 'customNavSymbolDatei';

$GLOBALS['TL_DCA']['tl_module']['fields']['customNavDreieck'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => true],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['customNavWiedereinblenden'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => true],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['customNavWiedereinblendenDauer'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'rgxp' => 'natural', 'minval' => 100, 'maxval' => 5000, 'maxlength' => 4, 'tl_class' => 'w50'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 1000],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['customNavLinksBeiPlatzmangel'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => true],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['customNavAnimation'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['customNavAnimationDauer'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'rgxp' => 'natural', 'minval' => 50, 'maxval' => 1000, 'maxlength' => 4, 'tl_class' => 'w50'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 150],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['customNavSchatten'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['mit', 'ohne'],
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['customNavSchattenOptionen'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50 clr'],
    'sql' => ['type' => 'string', 'length' => 8, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['customNavSymbol'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['chevron', 'pfeil', 'dreieck', 'plus', 'keins', 'eigenes'],
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['customNavSymbolOptionen'],
    'eval' => ['includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50 clr'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => 'chevron'],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['customNavSymbolDatei'] = [
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'extensions' => 'svg', 'mandatory' => true, 'tl_class' => 'clr'],
    'sql' => 'binary(16) NULL',
];
