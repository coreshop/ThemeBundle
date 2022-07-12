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

use Laminas\Stdlib\PriorityQueue;
use Pimcore\Model\Document;

final class CompositeThemeResolver implements ThemeResolverInterface, DocumentThemeResolverInterface
{
    private PriorityQueue $themeResolvers;

    public function __construct()
    {
        $this->themeResolvers = new PriorityQueue();
    }

    public function register(ThemeResolverInterface $themeResolver, int $priority = 0): void
    {
        $this->themeResolvers->insert($themeResolver, $priority);
    }

    public function resolveTheme(): string
    {
        foreach ($this->themeResolvers as $themeResolver) {
            try {
                return $themeResolver->resolveTheme();
            } catch (ThemeNotResolvedException) {
                continue;
            }
        }

        throw new ThemeNotResolvedException();
    }

    public function resolveThemeForDocument(Document $document): string
    {
        foreach ($this->themeResolvers as $themeResolver) {
            try {
                if ($themeResolver instanceof DocumentThemeResolverInterface) {
                    return $themeResolver->resolveThemeForDocument($document);
                }
            } catch (ThemeNotResolvedException) {
                continue;
            }
        }

        throw new ThemeNotResolvedException();
    }
}
