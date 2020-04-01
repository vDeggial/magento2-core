<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class ProductHelper extends BaseHelper
{
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
    }

    public function getProduct($productId)
    {
        $product = null;
        try {
            $productFactory = $this->generateClassObject("Magento\Catalog\Model\ProductFactory");
            $product = $this->productExists($productId) ? $productFactory->create()->load($productId) : null;
        } catch (\Exception $e) {
            $product = null;
        } finally {
            return $product;
        }
    }

    public function getProductBySku($productSku = null)
    {
        return $this->getProduct($this->getProductIdBySku($productSku));
    }

    public function getProductDescription($productId)
    {
        $description = null;
        $attributeId = 75;
        try {
            $tableName = $this->getSqlTableName('catalog_product_entity_text');
            $sql = "SELECT value FROM $tableName WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $description = (string)$result;
        } catch (\Exception $e) {
            $description = null;
        } finally {
            return $description;
        }
    }

    public function getProductIdBySku($productSku = null)
    {
        $productId = 0;
        try {
            $tableName = $this->getSqlTableName('catalog_product_entity');
            $sql  = "SELECT entity_id FROM $tableName WHERE sku LIKE '$productSku'";
            $result = $this->sqlQueryFetchOne($sql);
            $productId = (int)$result;
        } catch (\Exception $e) {
            $productId = 0;
        } finally {
            return $productId;
        }
    }

    public function getProductImages($productId = 0, $width = 500)
    {
        $imageList = [];
        try {
            $product = $this->getProduct($productId);
            $images = $product->getMediaGalleryImages();
            $_imageHelper = $this->generateClassObject('Magento\Catalog\Helper\Image');
            foreach ($images as $image) {
                array_push($imageList, $_imageHelper !== null ? $_imageHelper->init($product, 'product_page_image_large')->keepAspectRatio(true)->setImageFile($image->getFile())->resize($width, null)->getUrl() : "");
            }
        } catch (\Exception $e) {
            $imageList = [];
        } finally {
            return $imageList;
        }
    }

    public function getProductName($productId)
    {
        $name = null;
        $attributeId = 73;
        try {
            $tableName = $this->getSqlTableName('catalog_product_entity_varchar');
            $sql = "SELECT value FROM $tableName WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $name = (string)$result;
        } catch (\Exception $e) {
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getProductStatus($productId)
    {
        $attributeId = 97;
        $status = 0;
        try {
            $tableName = $this->getSqlTableName('catalog_product_entity_int');
            $sql = "SELECT value FROM $tableName WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $status = (int)$result;
        } catch (\Exception $e) {
            $status = 0;
        } finally {
            return $status;
        }
    }

    public function getProductStockQty($productId)
    {
        $qty = 0;
        try {
            $itemStockTable = $this->getSqlTableName('cataloginventory_stock_item');
            $productEntityTable = $this->getSqlTableName('catalog_product_entity');
            $sql = "SELECT stock.qty as qty FROM $itemStockTable stock join $productEntityTable product on stock.product_id = product.entity_id where product.entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $qty = (int)$result;
        } catch (\Exception $e) {
            $qty = - 1;
        } finally {
            return $qty;
        }
    }

    public function getProductUrl($productId)
    {
        $urlFactory = $this->generateClassObject("Magento\Framework\Url");
        $storeManager = $this->generateClassObject("Magento\Store\Model\StoreManagerInterface");
        $storeId = $storeManager->getStore()->getStoreId();
        $productUrl = null;
        try {
            //$product = $this->getProduct($productId);
            //$productUrl = $urlFactory->getUrl($product->getUrlKey());
            $productUrl = $urlFactory->getUrl('catalog/product/view', ['id' => $productId, '_nosid' => true, '_query' => ['___store' => $storeId]]);
        } catch (\Exception $e) {
            $productUrl = null;
        } finally {
            return $productUrl;
        }
    }

    public function productExists($productId)
    {
        $exists = false;
        try {
            $productEntityTable = $this->getSqlTableName('catalog_product_entity');
            $sql = "SELECT * FROM $productEntityTable product where product.entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Exception $e) {
            $exists = false;
        } finally {
            return $exists;
        }
    }
}
