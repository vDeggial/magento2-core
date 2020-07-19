<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class ProductHelper extends BaseHelper
{
    protected $tableProduct;
    protected $tableProductInt;
    protected $tableProductOption;
    protected $tableProductText;
    protected $tableProductVarchar;
    protected $tableProductStock;
    protected $tableAttribute;
    protected $tableGallery;
    protected $tableGalleryValue;
    protected $tableGalleryToEntity;
    protected $attributeTypeId;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->attributeTypeId = 4;
        $this->tableProduct = $this->getSqlTableName('catalog_product_entity');
        $this->tableProductInt = $this->getSqlTableName('catalog_product_entity_int');
        $this->tableProductOption = $this->getSqlTableName("eav_attribute_option_value");
        $this->tableProductText = $this->getSqlTableName('catalog_product_entity_text');
        $this->tableProductVarchar = $this->getSqlTableName('catalog_product_entity_varchar');
        $this->tableProductStock = $this->getSqlTableName('cataloginventory_stock_item');
        $this->tableAttribute = $this->getSqlTableName("eav_attribute");
        $this->tableGallery = $this->getSqlTableName("catalog_product_entity_media_gallery");
        $this->tableGalleryValue = $this->getSqlTableName("catalog_product_entity_media_gallery_value");
        $this->tableGalleryToEntity = $this->getSqlTableName("catalog_product_entity_media_gallery_value_to_entity");
    }

    public function getProduct($productId)
    {
        return $this->getProductById($productId);
    }

    public function getProductAttributeData($productId = 0, $attributeCode = null)
    {
        return $this->getProductAttributeValue($productId, $attributeCode);
    }

    public function getProductAttributeSelect($productId = 0, $attributeCode = null)
    {
        $optionId = (int)$this->getProductAttributeValue($productId, $attributeCode);
        return $this->getProductAttributeOptionValue($optionId);
    }

    public function getProductAttributeSet($productId = 0)
    {
        $productAttributeSet = 0;
        try {
            $sql  = "SELECT attribute_set_id FROM " . $this->tableProduct ." WHERE entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $productAttributeSet = (int)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $productAttributeSet = 0;
        } finally {
            return $productAttributeSet;
        }
    }

    public function getProductBySku($productSku = null)
    {
        return $this->getProductById($this->getProductId($productSku));
    }

    public function getProductCreatedDate($productId = 0)
    {
        $productDate = null;
        try {
            $sql  = "SELECT created_at FROM " . $this->tableProduct ." WHERE entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $productDate = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $productDate = null;
        } finally {
            return $productDate;
        }
    }

    public function getProductUpdatedDate($productId = 0)
    {
        $productDate = null;
        try {
            $sql  = "SELECT updated_at FROM " . $this->tableProduct ." WHERE entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $productDate = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $productDate = null;
        } finally {
            return $productDate;
        }
    }

    public function getProductDescription($productId)
    {
        $description = null;
        $attributeId = $this->getProductAttributeId("description");
        try {
            $sql = "SELECT value FROM " . $this->tableProductText ." WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $description = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $description = null;
        } finally {
            return $description;
        }
    }

    public function getProductGalleryImages($productId = 0, $width = 500)
    {
        $imageList = [];
        try {
            $images = $this->getProductMediaGalleryImages($productId);
            foreach ($images as $image) {
                array_push($imageList, $this->getProductImageUrl($productId, $image->getFile(), $width));
            }
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $imageList = [];
        } finally {
            return $imageList;
        }
    }

    public function getProductId($productSku = null)
    {
        $productId = 0;
        try {
            $sql  = "SELECT entity_id FROM " . $this->tableProduct ." WHERE sku LIKE '$productSku'";
            $result = $this->sqlQueryFetchOne($sql);
            $productId = (int)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
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
            $image = $this->getProductImageUrl($productId, $imageFilename, $width);
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $image = null;
        } finally {
            return $image;
        }
    }

    public function getProductImages($productId = 0, $width = 500)
    {
        $imageList = [];
        try {
            $images = $this->getProductImagesFilenames($productId);
            foreach ($images as $imageFilename) {
                array_push($imageList, $this->getProductImageUrl($productId, $imageFilename, $width));
            }
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $imageList = [];
        } finally {
            return $imageList;
        }
    }

    public function getProductName($productId)
    {
        $name = null;
        $attributeId = $this->getProductAttributeId("name");
        try {
            $sql = "SELECT value FROM " . $this->tableProductVarchar . " WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $name = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getProductSku($productId = 0)
    {
        $productSku = null;
        try {
            $sql  = "SELECT sku FROM " . $this->tableProduct ." WHERE entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $productSku = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $productSku = null;
        } finally {
            return $productSku;
        }
    }

    public function getProductStatus($productId)
    {
        $attributeId = $this->getProductAttributeId("status");
        $status = 0;
        try {
            $sql = "SELECT value FROM " . $this->tableProductInt . " WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $status = (int)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $status = 0;
        } finally {
            return $status;
        }
    }

    public function getProductStockQty($productId)
    {
        $qty = 0;
        try {
            $sql = "SELECT stock.qty as qty FROM " . $this->tableProductStock . " stock join " . $this->tableProduct . " product on stock.product_id = product.entity_id where product.entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $qty = (int)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $qty = - 1;
        } finally {
            return $qty;
        }
    }

    public function getProductType($productId = 0)
    {
        $productType = null;
        try {
            $sql  = "SELECT type_id FROM " . $this->tableProduct ." WHERE entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $productType = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $productType = null;
        } finally {
            return $productType;
        }
    }

    public function getProductUrl($productId)
    {
        $urlFactory = $this->generateClassObject("Magento\Framework\Url");
        //$storeManager = $this->generateClassObject("Magento\Store\Model\StoreManagerInterface");
        //$storeId = $storeManager->getStore()->getStoreId();
        $productUrl = null;
        try {
            //$product = $this->getProductById($productId);
            //$productUrl = $urlFactory->getUrl($product->getUrlKey());
            $urlKey = $this->getProductUrlKey($productId);
            $productUrl = $urlFactory->getUrl($urlKey);
            //$productUrl = $urlFactory->getUrl('catalog/product/view', ['id' => $productId, '_nosid' => true, '_query' => ['___store' => $storeId]]);
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $productUrl = null;
        } finally {
            return $productUrl;
        }
    }

    public function getProductUrlKey($productId)
    {
        $urlKey = null;
        $attributeId = $this->getProductAttributeId("url_key");
        try {
            $sql = "SELECT value FROM " . $this->tableProductVarchar . " WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $urlKey = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $urlKey = null;
        } finally {
            return $urlKey;
        }
    }

    public function productExists($productId)
    {
        $exists = false;
        try {
            $sql = "SELECT * FROM " . $this->tableProduct ." product where product.entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    private function getProductAttributeId($attributeCode)
    {
        $attributeId = 0;
        try {
            $sql = "SELECT attribute_id from " . $this->tableAttribute . " WHERE entity_type_id = " . $this->attributeTypeId . " AND attribute_code LIKE '$attributeCode'";
            $result = (int)$this->sqlQueryFetchOne($sql);
            $attributeId = $result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $attributeId = 0;
        } finally {
            return $attributeId;
        }
    }

    private function getProductAttributeTable($attributeId)
    {
        $tableName = $this->tableProduct;
        try {
            $attributeType = $this->getProductAttributeType($attributeId);
            $tableName .= "_" . $attributeType;
            $tableName = $this->getSqlTableName($tableName);
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $tableName = $this->tableProduct;
        } finally {
            return $tableName;
        }
    }

    private function getProductAttributeOptionValue($optionId)
    {
        $optionValue = null;
        try {
            $sql = "SELECT value from " . $this->tableProductOption . " WHERE option_id = $optionId";
            $result = (string)$this->sqlQueryFetchOne($sql);
            $optionValue = $result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $optionValue = null;
        } finally {
            return $optionValue;
        }
    }

    private function getProductAttributeType($attributeId)
    {
        $attributeType = null;
        try {
            $sql = "SELECT backend_type from " . $this->tableAttribute . " WHERE entity_type_id = " . $this->attributeTypeId . " AND attribute_id = $attributeId";
            $result = (string)$this->sqlQueryFetchOne($sql);
            $attributeType = $result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $attributeType = null;
        } finally {
            return $attributeType;
        }
    }

    private function getProductAttributeValue($productId, $attributeCode)
    {
        $value = null;
        $attributeId = $this->getProductAttributeId($attributeCode);
        try {
            $tableName = $this->getProductAttributeTable($attributeId);
            $sql = "SELECT value FROM $tableName WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $value = $result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }

    private function getProductById($productId)
    {
        $product = null;
        try {
            $productFactory = $this->generateClassObject("Magento\Catalog\Model\ProductFactory");
            $product = $this->productExists($productId) ? $productFactory->create()->load($productId) : null;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $product = null;
        } finally {
            return $product;
        }
    }

    private function getProductImageFilename($productId)
    {
        $image = null;
        $attributeId = 87;
        try {
            $sql = "SELECT value FROM " . $this->tableProductVarchar . " WHERE attribute_id = $attributeId AND entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $image = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $image = null;
        } finally {
            return $image;
        }
    }

    private function getProductImagesFilenames($productId = 0)
    {
        $images = [];
        try {
            $sql = "SELECT gal.value AS fileName FROM " . $this->tableGalleryToEntity . " ent LEFT JOIN " . $this->tableGalleryValue . " val ON ent.entity_id= val.entity_id LEFT JOIN " . $this->tableGallery . " gal ON val.value_id = gal.value_id WHERE ent.entity_id = $productId GROUP BY gal.value";
            $result = $this->sqlQueryFetchAll($sql);
            foreach ($result as $entry) {
                array_push($images, $entry["fileName"]);
            }
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $images = [];
        } finally {
            return $images;
        }
    }

    private function getProductImageUrl($productId = 0, $imageFilename = null, $width = 500)
    {
        $imageUrl = null;
        try {
            $product = $this->getProductById($productId);
            $_imageHelper = $this->generateClassObject("Magento\Catalog\Helper\Image");
            $imageUrl = $_imageHelper->init($product, 'product_page_image_large')->keepAspectRatio(true)->setImageFile($imageFilename)->resize($width, null)->getUrl();
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $imageUrl = null;
        } finally {
            return $imageUrl;
        }
    }

    private function getProductMediaGalleryImages($productId)
    {
        $images = [];
        try {
            $product = $this->getProductById($productId);
            $galleryReadHandler = $this->generateClassObject("Magento\Catalog\Model\Product\Gallery\ReadHandler");
            $galleryReadHandler->execute($product);
            $images = $product->getMediaGalleryImages();
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__ . " | " . $e->getMessage());
            $images = [];
        } finally {
            return $images;
        }
    }
}
