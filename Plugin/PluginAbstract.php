<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_DeleteOrders
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\DeleteOrders\Plugin;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Backend\Model\Auth\Session;

/**
 * class PluginAbstract To check ACL
 */
class PluginAbstract
{
    /**
     * @var AclRetriever
     */
    protected AclRetriever $aclRetriever;

    /**
     * @var Session
     */
    protected Session $authSession;

    /**
     * PluginAbstract constructor.
     *
     * @param AclRetriever $aclRetriever
     * @param Session $authSession
     */
    public function __construct(
        AclRetriever $aclRetriever,
        Session      $authSession
    ) {
        $this->aclRetriever = $aclRetriever;
        $this->authSession = $authSession;
    }

    /**
     * Allowed Resources
     *
     * @return bool
     */
    public function isAllowedResources(): bool
    {
        $user = $this->authSession->getUser();
        $role = $user->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        if (in_array("Magento_Backend::all", $resources)
            || in_array("MageINIC_DeleteOrders::mageinic_delete_orders", $resources)) {
            return true;
        }

        return false;
    }
}
