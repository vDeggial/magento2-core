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

    protected function getProduct($productId)
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

    protected function getProductDescription($productId)
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

    protected function getProductImages($productId = 0, $maxSize = "500")
    {
        $imageList = [];
        try {
            $product = $this->getProduct($productId);
            $images = $product->getMediaGalleryImages();
            $_imageHelper = $this->generateClassObject('Magento\Catalog\Helper\Image');
            foreach ($images as $image) {
                array_push($imageList, $_imageHelper !== null ? $_imageHelper->init($product, 'product_page_image_large')->keepAspectRatio(true)->setImageFile($image->getFile())->resize($maxSize, null)->getUrl() : "");
            }
        } catch (\Exception $e) {
            $imageList = [];
        } finally {
            return $imageList;
        }
    }

    protected function isProductEnabled($productId)
    {
        $attributeId = 97;
        $enabled = false;
        try {
            $tableName = $this->getSqlTableName('catalog_product_entity_int');
            $sql = "SELECT entity_id FROM $tableName WHERE attribute_id = $attributeId AND $tableName.value = 1 and entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $enabled = $result && !empty($result);
        } catch (\Exception $e) {
            $enabled = false;
        } finally {
            return $enabled;
        }
    }

    protected function productExists($productId)
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
