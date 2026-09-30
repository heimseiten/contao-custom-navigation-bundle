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
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

final class CustomNavigationRuntime implements RuntimeExtensionInterface
{
    /**
     * Name des Asset-Pakets, unter dem Contao den Ordner public/ der Erweiterung anmeldet.
     */
    private const PACKAGE = 'heimseiten_contao_custom_navigation';

    private const SYMBOLS = ['chevron', 'pfeil', 'dreieck', 'plus', 'keins', 'eigenes'];

    private const SHADOWS = ['mit', 'ohne'];

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly Packages $packages,
        private readonly RequestStack $requestStack,
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
        $look = false;
        $inlineStyle = false;

        if (!empty($context['customNavDreieck'])) {
            $attributes->set('data-nav-dreieck');
            $behaviour = true;
        }

        if (!empty($context['customNavWiedereinblenden'])) {
            $attributes->set('data-nav-wiedereinblenden', $this->getDuration($context['customNavWiedereinblendenDauer'] ?? null, 100, 5000, 1000));
            $behaviour = true;
        }

        // Auch „an“ braucht das Skript: Liegen die Unterlisten in einer zusätzlichen Hülle
        // (etwa <div class="ul-wrapper">), misst Contao den Platz nicht selbst.
        if (isset($context['customNavLinksBeiPlatzmangel'])) {
            $attributes->set('data-nav-richtung', $context['customNavLinksBeiPlatzmangel'] ? 'auto' : 'rechts');
            $behaviour = true;
        }

        // Die Dauer braucht das Stylesheet (Stilvariable), das Skript liest sie aus dem
        // Datenattribut: Es hält die Position eines Untermenüs fest, solange es ausblendet.
        if (!empty($context['customNavAnimation'])) {
            $duration = $this->getDuration($context['customNavAnimationDauer'] ?? null, 50, 1000, 150);
            $attributes->set('data-nav-animation', $duration);
            $attributes->addStyle(['--custom-navigation-dauer' => $duration.'ms']);
            $behaviour = true;
            $look = true;
            $inlineStyle = true;
        }

        $shadow = (string) ($context['customNavSchatten'] ?? '');

        if (\in_array($shadow, self::SHADOWS, true)) {
            $attributes->set('data-nav-schatten', $shadow);
            $look = true;
        }

        $name = (string) ($context['customNavSymbol'] ?? '');

        if ('eigenes' === $name) {
            if (null !== ($url = $this->getFileUrl($context['customNavSymbolDatei'] ?? null))) {
                $attributes->set('data-nav-symbol', $name);
                $attributes->addStyle(['--custom-navigation-symbol' => 'url("'.addcslashes($url, '"\\').'")']);
                $look = true;
                $inlineStyle = true;
            }
        } elseif (\in_array($name, self::SYMBOLS, true)) {
            $attributes->set('data-nav-symbol', $name);
            $look = true;
        }

        if (!$behaviour && !$look) {
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

    private function getDuration(mixed $value, int $min, int $max, int $default): int
    {
        if (!is_numeric($value)) {
            return $default;
        }

        return max($min, min($max, (int) $value));
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

        // Bewusst vom eigenen Ursprung und nicht über eine Static-URL: Browser laden
        // CSS-Masken im CORS-Modus, von einem anderen Host bliebe das Symbol sonst leer.
        $path = implode('/', array_map(rawurlencode(...), explode('/', $file->path)));

        return ($this->requestStack->getCurrentRequest()?->getBasePath() ?? '').'/'.$path;
    }

    private function getAssetUrl(string $file): string
    {
        $url = $this->packages->getUrl($file, self::PACKAGE);

        return $url.(str_contains($url, '?') ? '&' : '?').'v='.$this->assetVersion;
    }
}
