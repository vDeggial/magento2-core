<?php
namespace Hapex\Core\Helper;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;
use Zend\Log\Formatter;

class BaseHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
	public function printLog($filename, $log)
	{
		try
		{
			$writer = new Stream(BP . "/var/log/$filename.log");
			$logger = new Logger();
			$formatter = new Formatter\Simple();
			$formatter->setDateTimeFormat("Y-m-d H:i:s T");
			$writer->setFormatter($formatter);
			$logger->addWriter($writer);
			$logger->info($log);
			return true;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	public function sendOutput($output)
	{
		try
		{
			print_r($output);
			return true;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	protected function generateClassObject($class = "")
	{
		$object = null;
		try
		{
			$objectManager = !empty($class) ? \Magento\Framework\App\ObjectManager::getInstance() : null;
			$object = $objectManager->get($class);
		}
		catch(\Exception $e)
		{
			$object = null;
		}
		finally
		{
			return $object;
		}
	}

	protected function getCurrentDate()
	{
		$date = null;
		try
		{
			$timezone = $this->generateClassObject("Magento\Framework\Stdlib\DateTime\TimezoneInterface");
			$date = $timezone->date();
		}
		catch(\Exception $e)
		{
			$date = null;
		}
		finally
		{
			return $date;
		}
	}

	protected function getSqlTableName($name = null)
	{
		$tableName = null;
		$tableexists = false;
		try
		{
			$resource = $this->getSqlResource();
			$tableName = $resource->getTableName($name);
			$tableExists = $resource->getConnection()->isTableExists($tableName);
		}
		catch(\Exception $e)
		{
			$tableName = null;
			$tableExists = false;
		}
		finally
		{
			return $tableExists ? $tableName : null;
		}
	}

	public function isCurrentDateWithinRange($fromDate, $toDate)
	{
		$isWithinRange = false;
		$afterFromDate = false;
		$beforeToDate = false;
		$currentDate = null;

		try
		{
			$currentDate = $this->getCurrentDate()->format('Y-m-d');
			$afterFromDate = $fromDate ? strtotime($currentDate) >= strtotime($fromDate) ? true : false : true;
			$beforeToDate = $toDate ? strtotime($currentDate) <= strtotime($toDate) ? true : false : true;
			$isWithinRange = $afterFromDate && $beforeToDate;
		}
		catch(\Exception $e)
		{
			$isWithinRange = false;
		}
		finally
		{
			return $isWithinRange;
		}
	}

	protected function sqlQuery($sql)
	{
		return $this->queryExecute($sql);
	}

	protected function sqlQueryFetchAll($sql, $limit = 0)
	{
		$sql .= ($limit > 0) ? " LIMIT $limit" : "";
		return $this->queryExecute($sql, "fetchAll");
	}

	protected function sqlQueryFetchOne($sql)
	{
		return $this->queryExecute($sql, "fetchOne");
	}

	protected function sqlQueryFetchRow($sql)
	{
		return $this->queryExecute($sql, "fetchRow");
	}

	protected function urlExists($remoteUrl = "")
	{
		$exists = false;
		try
		{
			$exists = strpos(@get_headers($remoteUrl) [0], '404') === false;
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

	private function getSqlResource()
	{
		return $this->generateClassObject("Magento\Framework\App\ResourceConnection");
	}

	private function queryExecute($sql = null, $command = null)
	{
		try
		{
			$resource = $sql ? $this->getSqlResource() : null;
			$connection = $resource ? $resource->getConnection() : null;
			$result = null;

			switch ($connection !== null)
			{
				case true:
					switch ($command)
					{
						case "fetchOne":
							$result = $connection->fetchOne($sql);
						break;

						case "fetchAll":
							$result = $connection->fetchAll($sql);
						break;

						case "fetchRow":
							$result = $connection->fetchRow($sql);
						break;

						default:
							$result = $connection->query($sql);
						break;
					}
				break;
			}
			return $result;
		}
		catch(\Exception $e)
		{
			return null;
		}
	}
}
