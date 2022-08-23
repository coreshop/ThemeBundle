<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ThemeBundle\Context;

use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Pimcore\Model\Document;
use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class ThemeContext implements ThemeContextInterface
{
    public function __construct(
        private ThemeResolverInterface $resolver,
        private ThemeRepositoryInterface $themeRepository,
        private PimcoreContextResolver $pimcoreContext,
        private RequestStack $requestStack,
        private SettableThemeContext $settableThemeContext
    )
    {
    }

    public function getTheme(): ?ThemeInterface
    {
        $request = $this->requestStack->getMainRequest();

        if (!$request || null !== $this->settableThemeContext->getTheme()) {
            return $this->settableThemeContext->getTheme();
        }

        $isAjaxBrickRendering = $request->attributes->get('_route') === 'pimcore_admin_document_page_areabrick-render-index-editmode';

        if (!$isAjaxBrickRendering && $this->pimcoreContext->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_ADMIN)) {
            return $this->settableThemeContext->getTheme();
        }

        try {
            return $this->themeRepository->findOneByName($this->resolver->resolveTheme());
        } catch (ThemeNotResolvedException) {
            return $this->settableThemeContext->getTheme();
        }
    }
}
