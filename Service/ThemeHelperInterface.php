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

namespace CoreShop\Bundle\ThemeBundle\Service;

interface ThemeHelperInterface
{
    /**
     * @param string   $themeName
     * @param \Closure $function
     *
     * @return mixed
     */
    public function useTheme($themeName, \Closure $function);
}

class_alias(ThemeHelperInterface::class, 'CoreShop\Bundle\StoreBundle\Theme\ThemeHelperInterface');
