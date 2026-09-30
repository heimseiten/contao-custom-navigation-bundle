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

$GLOBALS['TL_LANG']['tl_module']['custom_navigation_legend'] = 'Submenus (desktop)';

$GLOBALS['TL_LANG']['tl_module']['customNavDreieck'] = [
    'Safe triangle',
    'The submenu stays open while the mouse moves diagonally towards it and brushes over other menu items.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavWiedereinblenden'] = [
    'Show again after briefly leaving',
    'The submenu disappears immediately and reappears if the mouse returns in time.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavWiedereinblendenDauer'] = [
    'Time span in milliseconds',
    '100 to 5000 milliseconds, default 1000.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavLinksBeiPlatzmangel'] = [
    'Open to the left if there is not enough space',
    'Side submenus open to the left if there is not enough space on the right; otherwise always to the right.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavAnimation'] = [
    'Fade in and out',
    'Submenus appear and disappear with a short fade instead of abruptly.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavAnimationDauer'] = [
    'Duration in milliseconds',
    '50 to 1000 milliseconds, default 150.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSchatten'] = [
    'Shadow',
    '"Contao default" leaves the shadow of the theme unchanged.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSchattenStandard'] = 'Contao default';
$GLOBALS['TL_LANG']['tl_module']['customNavSchattenOptionen'] = [
    'mit' => 'With shadow',
    'ohne' => 'Without shadow',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbol'] = [
    'Submenu icon',
    'Marks menu items with a submenu and adopts their text color.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolStandard'] = 'Contao default';
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolOptionen'] = [
    'chevron' => 'Chevron',
    'pfeil' => 'Arrow',
    'dreieck' => 'Triangle',
    'plus' => 'Plus and minus',
    'keins' => 'No icon (only visible when using the keyboard)',
    'eigenes' => 'Custom SVG',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolDatei'] = [
    'SVG file',
    'Single-colored icon pointing to the right. The color comes from the menu items.',
];

$GLOBALS['TL_LANG']['tl_module']['customNavNurBarrierefrei'] = 'The settings under "%s" only take effect with the "mod_navigation_accessible" template.';
$GLOBALS['TL_LANG']['tl_module']['customNavDateiGeschuetzt'] = 'The file is located in a protected folder and could not be loaded on the website. The folder has to be public in the file manager.';
