<?php

namespace Hapex\JRIHelper\Helper;

use Hapex\Core\Helper\CsvHelper;
use Hapex\Core\Helper\ProductHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class Inventory extends Data
{
    protected const XML_PATH_CONFIG_INVENTORY_ENABLED = "hapex_jrihelper/inventory_sync/enable";
    protected const XML_PATH_CONFIG_INVENTORY_RELIST_ENABLED = "hapex_jrihelper/inventory_sync/enable-relist-check";
    protected const XML_PATH_CONFIG_INVENTORY_SYNC_TYPE = "hapex_jrihelper/inventory_sync/sync-type";
    protected const XML_PATH_CONFIG_INVENTORY_ORDER_SYNC_TYPE = "hapex_jrihelper/inventory_sync/order-sync-type";
    protected const XML_PATH_CONFIG_INVENTORY_ORDER_SYNC_TIME = "hapex_jrihelper/inventory_sync/order-sync-time";
    protected const XML_PATH_CONFIG_INVENTORY_ORDER_SYNC_START = "hapex_jrihelper/inventory_sync/order-sync-start";
    protected const XML_PATH_CONFIG_INVENTORY_ORDER_SYNC_END = "hapex_jrihelper/inventory_sync/order-sync-end";
    protected const XML_PATH_CONFIG_INVENTORY_WEBHOOK_URL = "hapex_jrihelper/inventory_sync/webhook_url";
    protected $helperProduct;
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CsvHelper $helperCsv,
        ProductHelper $helperProduct
    ) {
        parent::__construct($context, $objectManager, $helperCsv);
        $this->helperProduct = $helperProduct;
    }

    public function isInventorySyncEnabled()
    {
        return $this->getConfigFlag(self::XML_PATH_CONFIG_INVENTORY_ENABLED);
    }

    public function isInventoryRelistCheckEnabled()
    {
        return $this->getConfigFlag(self::XML_PATH_CONFIG_INVENTORY_RELIST_ENABLED);
    }

    public function getSyncType()
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG_INVENTORY_SYNC_TYPE);
    }

    public function getOrderSyncType()
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG_INVENTORY_ORDER_SYNC_TYPE);
    }


    public function getOrderSyncTime()
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG_INVENTORY_ORDER_SYNC_TIME);
    }

    public function getOrderSyncStart()
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG_INVENTORY_ORDER_SYNC_START);
    }

    public function getOrderSyncEnd()
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG_INVENTORY_ORDER_SYNC_END);
    }

    public function getWebhookUrl()
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG_INVENTORY_WEBHOOK_URL);
    }

    public function isPackProduct($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        return $this->helperProduct->isProductAttributeSetName($productId, "Packs");
    }

    public function isGiveawayProduct($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        return $this->helperProduct->isProductAttributeSetName($productId, "Giveaways");
    }

    public function sendToWebhook($data = [])
    {
        $url = $this->getWebhookUrl();
        $json = json_encode($data);
        $type = "text/plain";
        $this->log("hapex_inventory_sync", "- Webhook URL: $url");
        return $this->getUrlHelper()->sendWebhook($url, $json, $type);
    }

    public function getListedSkus()
    {
        //return $this->helperProduct->getSkusByStatus(1);
        return $this->helperProduct->getSkusListed();
    }

    public function getSkuDescription($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $description = $this->helperProduct->getDescription($productId);
        return $description;
    }

    public function getSkuShortDescription($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $description = $this->helperProduct->getShortDescription($productId);
        return $description;
    }

    public function getSkuB1G1F($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $value = isset($productId) ? $this->helperProduct->getProductAttributeValue($productId, "b1g1f") : 0;
        return $value;
    }

    public function getSkuB1G2F($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $value = isset($productId) ? $this->helperProduct->getProductAttributeValue($productId, "b1g2f") : 0;
        return $value;
    }

    public function getSkuB2G1F($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $value = isset($productId) ? $this->helperProduct->getProductAttributeValue($productId, "b2g1f") : 0;
        return $value;
    }

    public function getSkuSale($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $value = isset($productId) ? $this->helperProduct->getProductAttributeValue($productId, "sale") : 0;
        return $value;
    }

    public function getSkuProductId($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        return $productId;
    }


    public function getSkuStock($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $stock = $this->helperProduct->getStockQty($productId);
        //$stock = $this->helperProduct->getStockListedQty($productId);
        return $stock;
    }

    public function getSkuStockStatus($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $status = $this->helperProduct->getStockStatus($productId);
        //$status = $this->helperProduct->getStockListedStatus($productId);
        return $status;
    }

    public function getProductBySku($sku = null)
    {
        return $this->helperProduct->getBySku($sku);
    }

    public function productSkuExists($sku = null)
    {
        return $this->helperProduct->productSkuExists($sku);
    }

    public function getSkuCategories($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $product = $this->helperProduct->getProduct($productId);
        $categories = $this->helperProduct->getProductCategories($productId);
        //$categories = isset($product) ? $product->getCategoryIds() : []; /*will return category ids array*/
        return $categories;
    }

    public function getSkuLink($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $url = $this->helperProduct->getUrl($productId);
        return $url;
    }

    public function getSkuImages($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $images = $this->helperProduct->getImages($productId);
        return $images;
    }

    public function getSkuPrice($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $price = $this->helperProduct->getPrice($productId);
        return $price;
    }

    public function getSkuSpecialPrice($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $price = $this->helperProduct->getSpecialPrice($productId);
        return $price;
    }

    public function getSkuSpecialPriceDateFrom($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $date = $this->helperProduct->getSpecialDateFrom($productId);
        return $date;
    }

    public function getSkuSpecialPriceDateTo($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $date = $this->helperProduct->getSpecialDateTo($productId);
        return $date;
    }

    public function getSkuName($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $name = $this->helperProduct->getName($productId);
        return $name;
    }

    public function getSkuProductType($sku = null)
    {
        $productId = $this->helperProduct->getId($sku);
        $type = $this->helperProduct->getType($productId);
        return $type;
    }

    public function getSkuOrdersCount($sku = null)
    {
        $count = 0;
        $data = $this->getSkuOrderData($sku);
        $count = count($data["orders"]);
        return $count;
    }

    public function getSkusOrdersBetweenDates($dateFrom = "2022-07-01", $dateTo = null)
    {
        $skus = [];
        $helperOrderItem = $this->generateClassObject(\Hapex\Core\Helper\OrderItemHelper::class);
        $helperOrderGrid = $this->generateClassObject(\Hapex\Core\Helper\OrderGridHelper::class);
        $helperOrder = $this->generateClassObject(\Hapex\Core\Helper\OrderHelper::class);

        $orderIds = $helperOrder->getOrderIdsUpdatedBetweenDates($dateFrom, $dateTo);
        $skus = $helperOrderItem->getItemSkusFromOrders($orderIds);
        return $skus;
    }

    public function getSkuProductData($sku = null, &$entry = [], $mode = 1)
    {
        $product = $this->helperProduct->getBySku($sku);
        switch (isset($product)) {
            case true:
                switch ($mode) {
                    case 1:
                        $stockItem = $product->getExtensionAttributes()->getStockItem();
                        switch (isset($stockItem)) {
                            case true:
                                $stockData = $stockItem->getData();
                                $stock = (int)$stockData["qty"];
                                $stockStatus = (int)$stockData["is_in_stock"];
                                $price = (float)$product->getPrice();
                                $priceSpecial = $product->getSpecialPrice();
                                $priceSpecialDateFrom = $product->getSpecialFromDate();
                                $priceSpecialDateTo = $product->getSpecialToDate();
                                $name = $product->getName();
                                $link = $this->getSkuLink($sku);
                                //$link = $product->getProductUrl();
                                $images = $this->getSkuImages($sku);
                                $product_id = $product->getId();
                                $categories = $product->getCategoryIds();
                                $b1g1f = (int)$product->getData("b1g1f");
                                $b1g2f = (int)$product->getData("b1g2f");
                                $b2g1f = (int)$product->getData("b2g1f");
                                $is_sale = (int)$product->getData("sale");
                                $dateCreated = $this->getDateHelper()->getDateFormatted($product->getCreatedAt(), "Y-m-d H:i:s");
                                $dateUpdated = $this->getDateHelper()->getDateFormatted($product->getUpdatedAt(), "Y-m-d H:i:s");
                                break;
                        }
                        break;

                    case 2:
                        $stock = $this->getSkuStock($sku);
                        $stockStatus = $this->getSkuStockStatus($sku);
                        $price = $this->getSkuPrice($sku);
                        $priceSpecial = $this->getSkuSpecialPrice($sku);
                        $priceSpecialDateFrom = $this->getSkuSpecialPriceDateFrom($sku);
                        $priceSpecialDateTo = $this->getSkuSpecialPriceDateTo($sku);
                        $name = $this->getSkuName($sku);
                        $link = $this->getSkuLink($sku);
                        $images = $this->getSkuImages($sku);
                        $product_id = $this->getSkuProductId($sku);
                        $categories = $this->getSkuCategories($sku);
                        $b1g1f = (int)$this->getSkuB1G1F($sku);
                        $b1g2f = (int)$this->getSkuB1G2F($sku);
                        $b2g1f = (int)$this->getSkuB2G1F($sku);
                        $is_sale = (int)$this->getSkuSale($sku);
                        $dateCreated = $this->getDateHelper()->getDateFormatted($this->helperProduct->getCreatedDate($product_id), "Y-m-d H:i:s");
                        $dateUpdated = $this->getDateHelper()->getDateFormatted($this->helperProduct->getCreatedDate($product_id), "Y-m-d H:i:s");
                        break;
                }

                $entry["stock"] = $stock;
                $entry["stock_status"] = $stockStatus;
                $entry["price"] = $price;
                $entry["price_special"] = $priceSpecial;
                $entry["price_special_date_from"] = $priceSpecialDateFrom;
                $entry["price_special_date_to"] = $priceSpecialDateTo;
                $entry["is_b1g1f"] = $b1g1f;
                $entry["is_b1g2f"] = $b1g2f;
                $entry["is_b2g1f"] = $b2g1f;
                $entry["is_sale"] = $is_sale;
                $entry["name"] = $name;
                $entry["product_id"] = $product_id;
                $entry["categories"] = is_array($categories) ? implode(",", $categories) : null;
                $entry["link"] = $link;
                $entry["images"] = $images;
                $entry["product_type"] = $this->getSkuProductType($sku);
                //$entry["date_created"] = $dateCreated;
                //$entry["date_updated"] = $dateUpdated;

                break;
        }
    }

    public function getSkuOrderData($sku = null, &$entry = [])
    {
        $data = [];
        $helperOrderItem = $this->generateClassObject(\Hapex\Core\Helper\OrderItemHelper::class);
        $helperOrderGrid = $this->generateClassObject(\Hapex\Core\Helper\OrderGridHelper::class);
        $helperOrderAddress = $this->generateClassObject(\Hapex\Core\Helper\OrderAddressHelper::class);
        $helperOrder = $this->generateClassObject(\Hapex\Core\Helper\OrderHelper::class);
        $helperCustomer = $this->generateClassObject(\Hapex\Core\Helper\CustomerHelper::class);

        $data["orders"] = [];

        /*$orderIds = $helperOrderItem->getOrderIdsWithSku($sku);
        $orderIds = array_filter($orderIds, function ($id) use (&$helperOrder) {
            $states = ["processing", "complete"];
            $orderState = $helperOrder->getState($id);
            return in_array($orderState, $states, true);
        });*/
        
        $orders = $helperOrder->getOrderRowsWithSku($sku);
        $orders = array_filter($orders, function ($order) use (&$helperOrder) {
            $states = ["processing", "complete"];
            return in_array($order["state"], $states, true);
        });
        

        foreach ($orders as $order) {
            //$order = $helperOrder->getOrderRow($orderId);
            switch (isset($order)) {
                case true:
                    $items = $helperOrderItem->getItemsWithSku($order["entity_id"], $sku);
                    switch (isset($items) && is_array($items)) {
                        case true:
                            foreach ($items as $item) {
                                switch (isset($item)) {
                                    case true:
                                        $info = [];
                                        $info["order_id"] = $order["entity_id"];
                                        $info["order_increment_id"] = $order["increment_id"];

                                        $info["subtotal"] = (float)$order["subtotal"];
                                        $info["state"] = $order["state"];;
                                        $info["status"] = $order["status"];
                                        $info["grandtotal"] = (float)$order["grand_total"];
                                        $info["item_id"] = $item["item_id"];
                                        $info["price"] = (float)$item["price"];
                                        $info["discount"] = (float)$item["discount_amount"];
                                        $info["tax"] = (float)$item["tax_amount"];
                                        $info["tax_refunded"] = (float)$item["tax_refunded"];
                                        $info["discount_reward"] = (float)$item["mp_reward_discount"];
                                        $info["discount_refunded"] = (float)$item["discount_refunded"];
                                        $info["row_total"] = (float)$item["row_total"];
                                        $info["amount_refunded"] = (float)$item["amount_refunded"];
                                        $info["product_id"] = $item["product_id"];
                                        $info["sku"] = $item["sku"];
                                        $info["applied_rule_ids"] = $item["applied_rule_ids"];
                                        $info["date_created"] = $this->getDateHelper()->getDateFormatted($item["created_at"], "Y-m-d H:i:s");
                                        $info["date_updated"] = $this->getDateHelper()->getDateFormatted($item["updated_at"], "Y-m-d H:i:s");
                                        $info["qty_ordered"] = (int)$item["qty_ordered"];
                                        $info["qty_invoiced"] = (int)$item["qty_invoiced"];
                                        $info["qty_refunded"] = (int)$item["qty_refunded"];
                                        $info["qty_canceled"] = (int)$item["qty_canceled"];
                                        $info["qty_shipped"] = (int)$item["qty_shipped"];
                                        $info["qty"] = $info["qty_ordered"];
                                        $info["qty"] -= $info["qty_refunded"];
                                        $info["qty"] -= $info["qty_canceled"];
                                        $info["total"] = 0;

                                        $customerName = $helperOrderAddress->getOrderIdCustomerName($info["order_id"]);
                                        $customerName = !empty($customerName) ? $customerName : $helperOrderGrid->getOrderName($info["order_id"]);
                                        $info["fullname"] = $this->getNameCase($customerName);

                                        $info["email"] = strtolower($helperOrderGrid->getCustomerEmail($info["order_id"]));
                                        $info["customer_id"] = $order["customer_id"];
                                        $info["customer_date_created"] = isset($info["customer_id"]) ? $this->getDateHelper()->getDateFormatted($helperCustomer->getCustomerCreatedDate($info["customer_id"]), "Y-m-d H:i:s") : null;
                                        $info["customer_group_id"] = $order["customer_group_id"];

                                        $orderItems = $helperOrderItem->getItemsFromOrder($info["order_id"]);
                                        $zeroItemsTotal = 0;
                                        switch (!empty($orderItems)) {
                                            case true:
                                                $zeroTotalItems = array_filter($orderItems, function ($item) {
                                                    return isset($item["row_total"]) && $item["row_total"] == 0;
                                                });
                                                $zeroItemsTotal = !empty($zeroTotalItems) ? array_sum(array_column($zeroTotalItems, "qty_ordered")) : 0;
                                                break;
                                        }
                                        $orderTotalQty = $order["total_qty_ordered"] - $zeroItemsTotal;

                                        $ratio = $info["subtotal"] > 0 ? $info["row_total"] / $info["subtotal"] : 0;
                                        $giftCardAmount = $info["row_total"] > 0 && $orderTotalQty > 0 ? $order["gift_card_amount"] * $ratio : 0;
                                        $giftCreditAmount = $info["row_total"] > 0 && $orderTotalQty > 0 ? $order["gift_credit_amount"] * $ratio : 0;
                                        $info["discount"] = $info["discount"] + abs($giftCardAmount) + abs($giftCreditAmount);

                                        $orderRefund = $order["total_refunded"];

                                        if ($info["amount_refunded"] == 0 && $orderRefund  > 0) {
                                            $info["amount_refunded"] = $info["row_total"] > 0 && $orderTotalQty > 0 ? $orderRefund  * $ratio : 0;
                                        }

                                        if ($info["tax_refunded"] == 0 && $orderRefund  > 0) {
                                            $info["tax_refunded"] = $info["tax"] > 0 && $orderTotalQty > 0 ? $info["tax"] * $ratio : 0;
                                        }

                                        if ($info["discount_refunded"] == 0 && $orderRefund  > 0) {
                                            $info["discount_refunded"] = $info["discount"] > 0 && $orderTotalQty > 0 ? $info["discount"] * $ratio : 0;
                                        }


                                        if ($info["qty"] > 0) {
                                            $info["total"] = ($info["row_total"] - $info["amount_refunded"]);
                                            $info["total"] -= $info["discount_reward"];
                                            $info["total"] -= ($info["discount"] - $info["discount_refunded"]);
                                            $info["total"] += ($info["tax"] - $info["tax_refunded"]);
                                            if ($info["total"] < 0) $info["total"] = 0;
                                            array_push($data["orders"], $info);
                                        }
                                        break;
                                }
                            }
                            break;
                    }
                    break;
            }
        }
        $entry["orders"] = $data["orders"];
        $entry["qty_sold"] = array_sum(array_column($data["orders"], "qty"));
    }

    protected function log($filename, $message)
    {
        $this->getLogHelper()->printLog($filename, $message);
    }
}
