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
		try
		{
			$itemStockTable = $this->getSqlTableName('cataloginventory_stock_item');
			$productEntityTable = $this->getSqlTableName('catalog_product_entity');
			$sql = "SELECT stock.qty as qty FROM $itemStockTable stock join $productEntityTable product on stock.product_id = product.entity_id where product.entity_id = $productId";
			$result = $this->sqlQueryFetchOne($sql);
			$qty = (int)$result;
		}
		catch(\Exception $e)
		{
			$qty = - 1;
		}
		finally
		{
			return $qty;
		}
	}

	protected function getProduct($productId)
	{
		$product = null;
		try
		{
			$productFactory = $this->generateClassObject("Magento\Catalog\Model\ProductFactory");
			$product = $this->productExists($productId) ? $productFactory->create()->load($productId) : null;
		}
		catch(\Exception $e)
		{
			$product = null;
		}
		finally
		{
			return $product;
		}
	}

	protected function getProductImages($product, $maxSize = "500")
	{
		$imageList = [];
		try
		{
			$images = $product->getMediaGalleryImages();
			$_imageHelper = $this->generateClassObject('Magento\Catalog\Helper\Image');
			foreach ($images as $image)
			{
				array_push($imageList, $_imageHelper !== null ? $_imageHelper->init($product, 'product_page_image_large')->keepAspectRatio(true)->setImageFile($image->getFile())->resize($maxSize, null)->getUrl() : "");
			}
		}
		catch(\Exception $e)
		{
			$imageList = [];
		}
		finally
		{
			return $imageList;
		}
	}

	protected function productExists($productId)
	{
		$exists = false;
		try
		{
			$productEntityTable = $this->getSqlTableName('catalog_product_entity');
			$sql = "SELECT * FROM $productEntityTable product where product.entity_id = $productId";
			$result = $this->sqlQueryFetchOne($sql);
			$exists = $result && !empty($result);
		}
		catch(\Exception $e)
		{
			$exists = false;
		}
		finally
		{
			return $exists;
		}
	}

}
