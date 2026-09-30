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

namespace Heimseiten\ContaoCustomNavigationBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Heimseiten\ContaoCustomNavigationBundle\HeimseitenContaoCustomNavigationBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            // Nach dem Core laden: Das Template dieser Erweiterung erweitert das des Cores
            // und muss in der Template-Hierarchie deshalb über ihm stehen.
            BundleConfig::create(HeimseitenContaoCustomNavigationBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
