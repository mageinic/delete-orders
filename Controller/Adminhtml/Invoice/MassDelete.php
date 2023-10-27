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

namespace MageINIC\DeleteOrders\Controller\Adminhtml\Invoice;

use Exception;
use MageINIC\DeleteOrders\Helper\Data as DataHelper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\InvoiceRepositoryInterface as InvoiceRepository;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * class MassDelete for delete invoice
 */
class MassDelete extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'MageINIC_DeleteOrders::invoice';

    /**
     * @var OrderManagementInterface
     */
    protected OrderManagementInterface $orderManagement;

    /**
     * @var InvoiceCollectionFactory
     */
    protected InvoiceCollectionFactory $invoiceCollectionFactory;

    /**
     * @var InvoiceRepository
     */
    protected InvoiceRepository $invoiceRepository;

    /**
     * @var DataHelper
     */
    protected DataHelper $dataHelper;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     * @param InvoiceCollectionFactory $invoiceCollectionFactory
     * @param InvoiceRepository $invoiceRepository
     * @param DataHelper $dataHelper
     */
    public function __construct(
        Context                  $context,
        Filter                   $filter,
        CollectionFactory        $collectionFactory,
        OrderManagementInterface $orderManagement,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        InvoiceRepository        $invoiceRepository,
        DataHelper               $dataHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $filter);
    }

    /**
     * @inheritdoc
     */
    protected function massAction(AbstractCollection $collection)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getParams();
        $orderId = $this->getRequest()->getParam('order_id');

        if ($this->dataHelper->isEnable()) {
            if ($this->dataHelper->isDeleteOrderInvoice()) {
                $selected = [];
                $collectionInvoice = $this->filter->getCollection($this->invoiceCollectionFactory->create());
                foreach ($collectionInvoice as $invoice) {
                    $selected[] = $invoice->getId();
                }
                if ($selected) {
                    foreach ($selected as $invoiceId) {
                        $invoice = $this->invoiceRepository->get($invoiceId);
                        try {
                            $this->invoiceRepository->delete($invoice);
                            $this->messageManager->addSuccessMessage(
                                __('Successfully deleted invoice #%1.', $invoice->getIncrementId())
                            );
                        } catch (Exception $e) {
                            $this->messageManager->addErrorMessage(
                                __('Error delete invoice #%1.', $invoice->getIncrementId())
                            );
                        }
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('Delete Order Invoice is Disabled'));
                return $resultRedirect->setPath('sales/invoice/index');
            }
        } else {
            $this->messageManager->addErrorMessage(__('Delete Order module is Disabled'));
        }

        if ($params['namespace'] == 'sales_order_view_invoice_grid') {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        } else {
            return $resultRedirect->setPath('sales/invoice/index');
        }

        return $resultRedirect;
    }
}
