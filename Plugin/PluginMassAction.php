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

use MageINIC\DeleteOrders\Helper\Data as DataHelper;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction;

/**
 * class PluginMassAction To check action
 * and set enable disable delete button functionality on order view,invoice, shipment, credit memo page
 */
class PluginMassAction
{
    /**
     * @var ScopeConfig
     */
    protected ScopeConfig $scopeConfig;

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @var UiComponentInterface[]
     */
    protected array $components;

    /**
     * @var DataHelper
     */
    protected DataHelper $dataHelper;

    /**
     * PluginMassAction constructor.
     *
     * @param ScopeConfig $scopeConfig
     * @param Http $request
     * @param DataHelper $dataHelper
     */
    public function __construct(
        ScopeConfig $scopeConfig,
        Http        $request,
        DataHelper  $dataHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Around Plugin on prepare Function
     *
     * @param MassAction $subject
     * @param callable $proceed
     * @return void
     */
    public function aroundPrepare(MassAction $subject, callable $proceed): void
    {
        $config = $subject->getConfiguration();
        foreach ($subject->getChildComponents() as $actionComponent) {
            $componentConfig = $actionComponent->getConfiguration();
            $disabledAction = $componentConfig['actionDisable'] ?? false;
            if ($disabledAction) {
                continue;
            }

            $isEnable = $this->dataHelper->isEnable();
            $isEnableShipment = $this->dataHelper->isDeleteOrderShipment();
            $isEnableInvoice = $this->dataHelper->isDeleteOrderInvoice();
            $isEnableCreditMemo = $this->dataHelper->isDeleteOrderCreditmemo();

            $actionType = $componentConfig['type'];
            $defaultActions = in_array(
                $actionType,
                ['deleteShipment', 'deleteInvoice', 'deleteCreditmemo']
            );
            if ($defaultActions) {
                if (($isEnable && $isEnableShipment && $actionType == "deleteShipment") ||
                    ($isEnable && $isEnableInvoice && $actionType == "deleteInvoice") ||
                    ($isEnable && $isEnableCreditMemo && $actionType == "deleteCreditmemo")
                ) {
                    $config['actions'][] = $componentConfig;
                }
            } else {
                $config['actions'][] = $componentConfig;
            }
        }

        $origConfig = $subject->getConfiguration();
        if ($origConfig !== $config) {
            $config = array_replace_recursive($config, $origConfig);
        }

        $subject->setData('config', $config);
        $this->components = [];
    }
}
