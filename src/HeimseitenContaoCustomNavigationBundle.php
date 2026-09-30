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

namespace Heimseiten\ContaoCustomNavigationBundle;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class HeimseitenContaoCustomNavigationBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        // Versionskennung für Skript und Stylesheet (?v=…): Sie ergibt sich aus dem Inhalt
        // der Dateien und ändert sich damit bei jeder Aktualisierung der Erweiterung von
        // selbst — Browser laden dann die neue Fassung statt der zwischengespeicherten.
        $hash = hash_init('xxh3');

        foreach (['custom-navigation.js', 'custom-navigation.css'] as $file) {
            $path = $this->getPath().'/public/'.$file;
            $builder->addResource(new FileResource($path));
            hash_update_file($hash, $path);
        }

        $builder->setParameter('heimseiten_custom_navigation.asset_version', substr(hash_final($hash), 0, 8));
    }
}
