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

namespace Heimseiten\ContaoCustomNavigationBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\File;
use Contao\FilesModel;
use Contao\Message;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NavigationModuleListener
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Weist beim Bearbeiten eines Navigationsmoduls darauf hin, wenn die Einstellungen
     * der Erweiterung wegen des gewählten Templates nicht greifen würden.
     */
    #[AsCallback(table: 'tl_module', target: 'config.onload')]
    public function addTemplateNotice(DataContainer|null $dc = null): void
    {
        if (!$dc?->id || 'edit' !== $this->requestStack->getCurrentRequest()?->query->get('act')) {
            return;
        }

        $record = $dc->getCurrentRecord();

        if (null === $record || 'navigation' !== ($record['type'] ?? null)) {
            return;
        }

        // Varianten wie „mod_navigation_accessible_kopfzeile“ erweitern das Template
        // und übernehmen die Einstellungen damit ebenfalls.
        if (str_starts_with((string) ($record['customTpl'] ?? ''), 'mod_navigation_accessible')) {
            return;
        }

        $legend = $this->translator->trans('tl_module.custom_navigation_legend', [], 'contao_tl_module');

        $this->framework->getAdapter(Message::class)->addInfo(
            $this->translator->trans('tl_module.customNavNurBarrierefrei', [$legend], 'contao_tl_module'),
        );
    }

    /**
     * Das Symbol wird als CSS-Maske direkt vom Browser geladen und muss deshalb in einem
     * öffentlichen Ordner liegen.
     */
    #[AsCallback(table: 'tl_module', target: 'fields.customNavSymbolDatei.save')]
    public function checkFileIsPublic(mixed $value): mixed
    {
        if (!$value) {
            return $value;
        }

        $this->framework->initialize();

        $file = $this->framework->getAdapter(FilesModel::class)->findByUuid($value);

        if (null !== $file && !$this->framework->createInstance(File::class, [$file->path])->isUnprotected()) {
            throw new \RuntimeException($this->translator->trans('tl_module.customNavDateiGeschuetzt', [], 'contao_tl_module'));
        }

        return $value;
    }
}
