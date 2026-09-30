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

namespace Heimseiten\ContaoCustomNavigationBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CustomNavigationExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            // Liest die Einstellungen aus den Daten des Navigationsmoduls, die dem Template
            // ohnehin vorliegen, deshalb der Zugriff auf den Kontext.
            new TwigFunction('custom_navigation', [CustomNavigationRuntime::class, 'getSettings'], ['needs_context' => true]),
        ];
    }
}
