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

$GLOBALS['TL_LANG']['tl_module']['custom_navigation_legend'] = 'Untermenüs (Desktop)';

$GLOBALS['TL_LANG']['tl_module']['customNavDreieck'] = [
    'Sicherheitsdreieck',
    'Das Untermenü bleibt offen, während die Maus schräg darauf zufährt und dabei andere Menüpunkte streift.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavWiedereinblenden'] = [
    'Nach kurzem Verlassen wieder einblenden',
    'Das Untermenü verschwindet sofort und erscheint wieder, wenn die Maus rechtzeitig zurückkehrt.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavWiedereinblendenDauer'] = [
    'Zeitspanne in Millisekunden',
    '100 bis 5000 Millisekunden, Standard 1000.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavLinksBeiPlatzmangel'] = [
    'Bei Platzmangel nach links öffnen',
    'Seitliche Untermenüs öffnen links, wenn rechts im Fenster der Platz fehlt; ohne Häkchen immer rechts.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavAnimation'] = [
    'Ein- und ausblenden',
    'Untermenüs erscheinen und verschwinden mit einer kurzen Überblendung statt schlagartig.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavAnimationDauer'] = [
    'Dauer in Millisekunden',
    '50 bis 1000 Millisekunden, Standard 150.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSchatten'] = [
    'Schatten',
    '„Contao-Standard“ lässt den Schatten des Themes unverändert.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSchattenStandard'] = 'Contao-Standard';
$GLOBALS['TL_LANG']['tl_module']['customNavSchattenOptionen'] = [
    'mit' => 'Mit Schatten',
    'ohne' => 'Ohne Schatten',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbol'] = [
    'Symbol für Untermenüs',
    'Kennzeichnet Menüpunkte mit Untermenü und übernimmt deren Schriftfarbe.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolStandard'] = 'Contao-Standard';
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolOptionen'] = [
    'chevron' => 'Winkel',
    'pfeil' => 'Pfeil',
    'dreieck' => 'Dreieck',
    'plus' => 'Plus und Minus',
    'keins' => 'Kein Symbol (nur bei Bedienung per Tastatur sichtbar)',
    'eigenes' => 'Eigenes SVG',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolDatei'] = [
    'SVG-Datei',
    'Einfarbiges Symbol, das nach rechts zeigt. Die Farbe kommt von den Menüpunkten.',
];

$GLOBALS['TL_LANG']['tl_module']['customNavNurBarrierefrei'] = 'Die Einstellungen unter „%s“ wirken nur mit dem Template „mod_navigation_accessible“.';
$GLOBALS['TL_LANG']['tl_module']['customNavDateiGeschuetzt'] = 'Die Datei liegt in einem geschützten Ordner und wäre auf der Webseite nicht abrufbar. Der Ordner muss in der Dateiverwaltung öffentlich sein.';
