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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\String\HtmlAttributes;
use Contao\FilesModel;
use Symfony\Component\Asset\Context\ContextInterface;
use Symfony\Component\Asset\Packages;
use Twig\Extension\RuntimeExtensionInterface;

final class CustomNavigationRuntime implements RuntimeExtensionInterface
{
    /**
     * Name des Asset-Pakets, unter dem Contao den Ordner public/ der Erweiterung anmeldet.
     */
    private const PACKAGE = 'heimseiten_contao_custom_navigation';

    private const SYMBOLS = ['chevron', 'pfeil', 'dreieck', 'plus', 'keins', 'eigenes'];

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly Packages $packages,
        private readonly ContextInterface $filesContext,
        private readonly string $assetVersion,
    ) {
    }

    /**
     * Übersetzt die Einstellungen des Navigationsmoduls in Attribute für den Wrapper des
     * Moduls sowie die Adressen von Skript und Stylesheet. Ist keine Einstellung aktiv,
     * bleibt alles leer und das Menü verhält sich wie ohne die Erweiterung.
     *
     * @param array<string, mixed> $context
     *
     * @return array{attributes: HtmlAttributes|null, script: string|null, stylesheet: string|null, inline_style: bool}
     */
    public function getSettings(array $context): array
    {
        $attributes = new HtmlAttributes();
        $behaviour = false;
        $symbol = false;
        $inlineStyle = false;

        if (!empty($context['customNavDreieck'])) {
            $attributes->set('data-nav-dreieck');
            $behaviour = true;
        }

        if (!empty($context['customNavWiedereinblenden'])) {
            $attributes->set('data-nav-wiedereinblenden', $this->getDuration($context['customNavWiedereinblendenDauer'] ?? null));
            $behaviour = true;
        }

        // Auch „an“ braucht das Skript: Liegen die Unterlisten in einer zusätzlichen Hülle
        // (etwa <div class="ul-wrapper">), misst Contao den Platz nicht selbst.
        if (isset($context['customNavLinksBeiPlatzmangel'])) {
            $attributes->set('data-nav-richtung', $context['customNavLinksBeiPlatzmangel'] ? 'auto' : 'rechts');
            $behaviour = true;
        }

        $name = (string) ($context['customNavSymbol'] ?? '');

        if ('eigenes' === $name) {
            if (null !== ($url = $this->getFileUrl($context['customNavSymbolDatei'] ?? null))) {
                $attributes->set('data-nav-symbol', $name);
                $attributes->addStyle(['--custom-navigation-symbol' => 'url("'.addcslashes($url, '"\\').'")']);
                $symbol = true;
                $inlineStyle = true;
            }
        } elseif (\in_array($name, self::SYMBOLS, true)) {
            $attributes->set('data-nav-symbol', $name);
            $symbol = true;
        }

        if (!$behaviour && !$symbol) {
            return ['attributes' => null, 'script' => null, 'stylesheet' => null, 'inline_style' => false];
        }

        $attributes->addClass('custom-navigation');

        return [
            'attributes' => $attributes,
            'script' => $behaviour ? $this->getAssetUrl('custom-navigation.js') : null,
            'stylesheet' => $this->getAssetUrl('custom-navigation.css'),
            'inline_style' => $inlineStyle,
        ];
    }

    private function getDuration(mixed $value): int
    {
        if (!is_numeric($value)) {
            return 1000;
        }

        return max(100, min(5000, (int) $value));
    }

    private function getFileUrl(mixed $uuid): string|null
    {
        if (!\is_string($uuid) || '' === $uuid) {
            return null;
        }

        $this->framework->initialize();

        $file = $this->framework->getAdapter(FilesModel::class)->findByUuid($uuid);

        if (null === $file || 'file' !== $file->type) {
            return null;
        }

        // Wie bei Contaos eigenen Dateiverweisen: Basispfad der Installation bzw. die
        // eingestellte Static-URL für Dateien, dazu der Pfad mit kodierten Abschnitten.
        $path = implode('/', array_map(rawurlencode(...), explode('/', $file->path)));

        return rtrim($this->filesContext->getBasePath(), '/').'/'.$path;
    }

    private function getAssetUrl(string $file): string
    {
        $url = $this->packages->getUrl($file, self::PACKAGE);

        return $url.(str_contains($url, '?') ? '&' : '?').'v='.$this->assetVersion;
    }
}
