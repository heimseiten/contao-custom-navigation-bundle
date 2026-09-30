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

$GLOBALS['TL_LANG']['tl_module']['custom_navigation_bedienung_legend'] = 'Bedienung der Untermenüs (Desktop)';
$GLOBALS['TL_LANG']['tl_module']['custom_navigation_darstellung_legend'] = 'Darstellung der Untermenüs (Desktop)';

$GLOBALS['TL_LANG']['tl_module']['customNavDreieck'] = [
    'Sicherheitsdreieck',
    'Untermenü bleibt offen, während die Maus schräg darauf zufährt.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavLinksBeiPlatzmangel'] = [
    'Bei Platzmangel nach links öffnen',
    'Seitliche Untermenüs öffnen links, wenn rechts der Platz fehlt.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavWiedereinblenden'] = [
    'Nach kurzem Verlassen wieder einblenden',
    'Kehrt die Maus rechtzeitig zurück, öffnet sich das Untermenü erneut.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavWiedereinblendenDauer'] = [
    'Zeitspanne in Millisekunden',
    '100 bis 5000, Standard 1000.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbol'] = [
    'Symbol für Untermenüs',
    'Kennzeichnet Menüpunkte mit Untermenü.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolOptionen'] = [
    'chevron' => 'Winkel',
    'pfeil' => 'Pfeil',
    'dreieck' => 'Dreieck',
    'plus' => 'Plus und Minus',
    'keins' => 'Kein Symbol (nur bei Bedienung per Tastatur sichtbar)',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSchatten'] = [
    'Schatten',
    'Ohne Auswahl bleibt der Schatten des Themes.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSchattenOptionen'] = [
    'mit' => 'Mit Schatten',
    'ohne' => 'Ohne Schatten',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolEigenes'] = [
    'Eigenes Symbol (SVG)',
    'Ersetzt das gewählte Symbol durch eine eigene SVG-Datei.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavSymbolDatei'] = [
    'SVG-Datei',
    'Einfarbiges Symbol, das nach rechts zeigt; verwendet wird nur seine Form.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavAnimation'] = [
    'Ein- und ausblenden',
    'Untermenüs erscheinen und verschwinden mit kurzer Überblendung.',
];
$GLOBALS['TL_LANG']['tl_module']['customNavAnimationDauer'] = [
    'Dauer in Millisekunden',
    '50 bis 1000, Standard 150.',
];

$GLOBALS['TL_LANG']['tl_module']['customNavNurBarrierefrei'] = 'Die Einstellungen für die Untermenüs wirken nur mit dem Template „mod_navigation_accessible“.';
$GLOBALS['TL_LANG']['tl_module']['customNavDateiGeschuetzt'] = 'Die Datei liegt in einem geschützten Ordner und wäre auf der Webseite nicht abrufbar. Der Ordner muss in der Dateiverwaltung öffentlich sein.';
