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

    public function getProductAttributeData($productId, $attribute)
    {
        $product = $this->getProduct($productId);
        return $product ? $product->getData($attribute) : null;
    }

    public function getProductAttributeText($productId, $attribute)
    {
        $product = $this->getProduct($productId);
        return $product ? $product->getAttributeText($attribute) : null;
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

    public function getProductImage($productId = 0, $width = 500)
    {
        $image = null;
        try {
            $imageFilename = $this->getProductImageFilename($productId);
            $image = $this->getProductImageUrl($productid, $imageFilename, $width);
        } catch (\Exception $e) {
            $image = null;
        } finally {
            return $image;
        }
    }

    public function getProductImages($productId = 0, $width = 500)
    {
        $imageList = [];
        try {
            //$images = $this->getProductMediaGalleryImages($productId);
            $images = $this->getProductImagesList($productId);
            foreach ($images as $image) {
                $this->printLog("hapex_product_images", $image);
                //array_push($imageList, $this->getProductImageUrl($productId, $image->getFile(), $width));
                array_push($imageList, $this->getProductImageUrl($productId, $image, $width));
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

    private function getProductImagesList($productId = 0)
    {
        $images = [];
        try {
            $tableValueToEntity = $this->getSqlTableName("catalog_product_entity_media_gallery_value_to_entity");
            $tableValue = $this->getSqlTableName("catalog_product_entity_media_gallery_value");
            $tableGallery = $this->getSqlTableName("catalog_product_entity_media_gallery");
            $sql = "SELECT gal.value AS fileName FROM $tableValueToEntity ent LEFT JOIN $tableValue val ON ent.entity_id= val.entity_id LEFT JOIN $tableGallery gal ON val.value_id = gal.value_id WHERE ent.entity_id = $productId GROUP BY gal.value";
            $result = $this->sqlQueryFetchAll($sql);
            foreach ($result as $entry) {
                array_push($images, $entry["fileName"]);
            }
        } catch (\Exception $e) {
            $images = [];
        } finally {
            return $images;
        }
    }

    private function getProductImageFilename($productId)
    {
        $image = null;
        $attributeId = 87;
        try {
            $tableName = $this->getSqlTableName('catalog_product_entity_varchar');
            $sql = "SELECT value FROM $tableName WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $image = (string)$result;
        } catch (\Exception $e) {
            $image = null;
        } finally {
            return $image;
        }
    }

    private function getProductImageUrl($productId = 0, $imageFilename = null, $width = 500)
    {
        $imageUrl = null;
        try {
            $product = $this->getProduct($productId);
            $_imageHelper = $this->generateClassObject("Magento\Catalog\Helper\Image");
            $imageUrl = $_imageHelper->init($product, 'product_page_image_large')->keepAspectRatio(true)->setImageFile($imageFilename)->resize($width, null)->getUrl();
        } catch (\Exception $e) {
            $imageUrl = null;
        } finally {
            return $imageUrl;
        }
    }

    private function getProductMediaGalleryImages($productId)
    {
        $images = [];
        try {
            $product = $this->getProduct($productId);
            $galleryReadHandler = $this->generateClassObject("Magento\Catalog\Model\Product\Gallery\ReadHandler");
            $galleryReadHandler->execute($product);
            $images = $product->getMediaGalleryImages();
        } catch (\Exception $e) {
            $images = [];
        } finally {
            return $images;
        }
    }
}
