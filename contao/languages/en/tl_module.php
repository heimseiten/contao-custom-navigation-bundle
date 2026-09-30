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

$GLOBALS['TL_LANG']['tl_module']['custom_navigation_bedienung_legend'] = 'Submenu behavior (desktop)';
$GLOBALS['TL_LANG']['tl_module']['custom_navigation_darstellung_legend'] = 'Submenu appearance (desktop)';

$GLOBALS['TL_LANG']['tl_module']['customNavDreieck'] = [
    'Safe triangle',
    'The submenu stays open while the mouse moves diagonally towards it.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavLinksBeiPlatzmangel'] = [
    'Open to the left if space is short',
    'Side submenus open to the left if there is no space on the right.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavWiedereinblenden'] = [
    'Show again after briefly leaving',
    'If the mouse returns in time, the submenu opens again.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavWiedereinblendenDauer'] = [
    'Time span in milliseconds',
    '100 to 5000, default 1000.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbol'] = [
    'Submenu icon',
    'Marks menu items that have a submenu.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolOptionen'] = [
    'chevron' => 'Chevron',
    'pfeil' => 'Arrow',
    'dreieck' => 'Triangle',
    'plus' => 'Plus and minus',
    'keins' => 'No icon (only visible when using the keyboard)',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSchatten'] = [
    'Shadow',
    'Without a selection, the theme shadow remains.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSchattenOptionen'] = [
    'mit' => 'With shadow',
    'ohne' => 'Without shadow',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolEigenes'] = [
    'Custom icon (SVG)',
    'Replaces the selected icon with a custom SVG file.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolDatei'] = [
    'SVG file',
    'Single-colored icon pointing to the right; only its shape is used.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavAnimation'] = [
    'Fade in and out',
    'Submenus appear and disappear with a short fade.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavAnimationDauer'] = [
    'Duration in milliseconds',
    '50 to 1000, default 150.',
];

$GLOBALS['TL_LANG']['tl_module']['customNavNurBarrierefrei'] = 'The submenu settings only take effect with the "mod_navigation_accessible" template.';
$GLOBALS['TL_LANG']['tl_module']['customNavDateiGeschuetzt'] = 'The file is located in a protected folder and could not be loaded on the website. The folder has to be public in the file manager.';
