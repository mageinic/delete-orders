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
 * along with this program. If not, see https:gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_DeleteOrders
 * @copyright Copyright (c) 2023 MageINIC (https:
 * @license https:gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\DeleteOrders\Ui\Component\Control;

use MageINIC\DeleteOrders\Helper\Data as DataHelper;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\App\Request\Http;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Control\Action;

/**
 * class DeleteAction To check Configuration value,
 * and redirect to specified url action Delete Order, Invoice, Shipment And Credit memo
 */
class DeleteAction extends Action
{
    /**
     * Mass Delete Orders Url
     */
    public const DELETE_ORDER_URL = 'deleteorder/order/massDelete';

    /**
     * Mass Delete Credit Memo Url
     */
    public const DELETE_CREDIT_MEMO_URL = 'deleteorder/creditmemo/massDelete';

    /**
     * Mass Delete Shipment Url
     */
    public const DELETE_SHIPMENT_URL = 'deleteorder/shipment/massDelete';

    /**
     * Mass Delete Invoice Url
     */
    public const DELETE_INVOICE_URL = 'deleteorder/invoice/massDelete';

    /**
     * @var ScopeConfig
     */
    protected ScopeConfig $scopeConfig;

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var DataHelper
     */
    protected DataHelper $dataHelper;

    /**
     * DeleteAction constructor.
     *
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param ScopeConfig $scopeConfig
     * @param Http $request
     * @param DataHelper $dataHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface     $urlBuilder,
        ScopeConfig      $scopeConfig,
        Http             $request,
        DataHelper       $dataHelper,
        array            $components = [],
        array            $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $components,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    public function prepare(): void
    {
        $config = $this->getConfiguration();

        foreach ($this->getChildComponents() as $actionComponent) {
            $config['actions'][] = $actionComponent->getConfiguration();
        }

        $origConfig = $this->getConfiguration();
        if ($origConfig !== $config) {
            $config = array_replace_recursive($config, $origConfig);
        }

        $isEnable = $this->dataHelper->isEnable();
        $isEnableShipment = $this->dataHelper->isDeleteOrderShipment();
        $isEnableInvoice = $this->dataHelper->isDeleteOrderInvoice();
        $isEnableCreditMemo = $this->dataHelper->isDeleteOrderCreditmemo();

        $newConfigActions = [];
        if (isset($config['actions'])) {
            foreach ($config['actions'] as $configItem) {
                $newConfigActions[] = $configItem;
            }
        }
        if ($isEnable) {
            if (($isEnableInvoice) && "sales_invoice_index" == $this->request->getFullActionName()) {
                $newConfigActions[] = $this->invoiceGrid();
            } elseif (($isEnableShipment) && "sales_shipment_index" == $this->request->getFullActionName()) {
                $newConfigActions[] = $this->shipmentGrid();
            } elseif (($isEnableCreditMemo) && "sales_creditmemo_index" == $this->request->getFullActionName()) {
                $newConfigActions[] = $this->creditMemoGrid();
            } elseif ("sales_order_index" == $this->request->getFullActionName()) {
                $newConfigActions = array_merge($newConfigActions, $this->orderGrid());
            }
            $config['actions'] = $newConfigActions;
        }
        $this->setData('config', $config);
        $this->components = [];

        parent::prepare();
    }

    /**
     * Get delete functionality on invoice Grid
     *
     * @return array
     */
    protected function invoiceGrid(): array
    {
        return [
            'type' => 'delete_invoices',
            'label' => __('Delete invoices'),
            'url' => $this->urlBuilder->getUrl(self::DELETE_INVOICE_URL),
            'confirm' =>
                [
                    'title' => __('Delete invoices'),
                    'message' => __('Are you sure you want to delete selected invoice(s)?')
                ]
        ];
    }

    /**
     * Get delete functionality on shipment Grid
     *
     * @return array
     */
    protected function shipmentGrid(): array
    {
        return [
            'type' => 'delete_shipments',
            'label' => __('Delete Shipments'),
            'url' => $this->urlBuilder->getUrl(self::DELETE_SHIPMENT_URL),
            'confirm' =>
                [
                    'title' => __('Delete Shipments'),
                    'message' => __('Are you sure you want to delete selected Shipment(s)?')
                ]
        ];
    }

    /**
     * Get delete functionality on credit Grid
     *
     * @return array
     */
    protected function creditMemoGrid(): array
    {
        return [
            'type' => 'delete_credit_memos',
            'label' => __('Delete Credit Memos'),
            'url' => $this->urlBuilder->getUrl(self::DELETE_CREDIT_MEMO_URL),
            'confirm' =>
                [
                    'title' => __('Delete Credit Memo'),
                    'message' => __('Are you sure you want to delete selected Credit Memo(s)?')
                ]
        ];
    }

    /**
     * Get delete functionality on order Grid
     *
     * @return array
     */
    protected function orderGrid(): array
    {
        $order[] = [
            'type' => 'delete_orders',
            'label' => __('Delete Orders'),
            'url' => $this->urlBuilder->getUrl(self::DELETE_ORDER_URL),
            'confirm' =>
                [
                    'title' => __('Delete Orders'),
                    'message' => '<b>' . __('Note: ') . '</b>'
                        . '<span style="color:#FF0000; display:table;">'
                        . __('Invoices, Credit Memos and Shipments related to this order(s) will also be deleted.')
                        . '</span>' . '<br>'
                        . __('Are you sure you want to delete selected order(s)?')
                ]
        ];

        return $order;
    }
}
