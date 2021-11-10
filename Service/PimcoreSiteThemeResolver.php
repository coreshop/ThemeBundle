<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ThemeBundle\Service;

use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\Document;
use Pimcore\Tool\Frontend;
use Symfony\Component\HttpFoundation\RequestStack;

final class PimcoreSiteThemeResolver implements ThemeResolverInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private DocumentResolver $documentResolver
    ) {
    }


    public function resolveTheme(): string
    {
        $request = $this->requestStack->getMainRequest();

        if (!$request) {
            throw new ThemeNotResolvedException();
        }

        $isAjaxBrickRendering = $request->attributes->get('_route') === 'pimcore_admin_document_page_areabrick-render-index-editmode';

        if ($isAjaxBrickRendering) {
            $document = Document::getById($request->request->get('documentId'));
        }
        else {
            $document = $this->documentResolver->getDocument($request);
        }

        if ($document instanceof Document) {
            $site = Frontend::getSiteForDocument($document);

            if ($site && $theme = $site->getRootDocument()->getKey()) {
                return $theme;
            }
        }

        throw new ThemeNotResolvedException();
    }
}
