<?php

/**
 * Class ShardedRecord
 */
abstract class ShardedRecord extends CActiveRecord
{
	/**
	 * @var string Name of the attribute to use as a key to determine a shard location
	 */
	protected $shardAttribute;

	/**
	 * @var string Database name prefix
	 */
	protected $dbNamePrefix = 'db';

	/** @var ShardMapper */
	private $_mapper;

	/**
	 * @return string The associated database table name
	 */
	public function tableName()
	{
		return get_class($this) . $this->getShardTableId();
	}

	/**
	 * @return CDbConnection
	 */
	public function getDbConnection()
	{
		return Yii::app()->getComponent($this->dbNamePrefix . $this->getShardMapper()->getConnectionId());
	}

	/**
	 * Named scope for queries with specific sharding attribute
	 * @param mixed $key
	 * @return $this
	 */
	public function shardKey($key)
	{
		$this->{$this->shardAttribute} = $key;
		$this->getDbCriteria()->compare($this->shardAttribute, $key);
		return $this;
	}

	/**
	 * Returns table ID or empty string if the sharding attribute is empty, mainly for table schema caching mechanism
	 * @return int|string The table ID
	 */
	public function getShardTableId()
	{
		if ($this->getAttribute($this->shardAttribute))
			return $this->getShardMapper()->getTableId();
		else
			return '';
	}

	/**
	 * Returns the meta-data for this AR.
	 * This hack is to rewrite the table name in queries.
	 * @return CActiveRecordMetaData the meta for this AR class.
	 */
	public function getMetaData()
	{
		$md = parent::getMetaData();
		$md->tableSchema->rawName = $this->getDbConnection()->quoteTableName($this->tableName());
		return $md;
	}

	/**
	 * @param mixed $value
	 * @return $this
	 */
	public function setShardAttribute($value)
	{
		$this->{$this->shardAttribute} = $value;
		return $this;
	}

	/**
	 * @return ShardMapper
	 */
	public function getShardMapper()
	{
		if ($this->_mapper === null || $this->getAttribute($this->shardAttribute) != $this->_mapper->getKey())
			$this->_mapper = $this->createMapper();
		return $this->_mapper;
	}

	/**
	 * Get query results from all model shards
	 * @param string $sql
	 * @param array $params
	 * @return ShardDataReader
	 */
	public function queryShards($sql, $params = array())
	{
		//$class = get_class($this);
		//$model = new $class(null);
		return new ShardDataReader($this, $sql, $params);
	}

	/**
	 * @param callable $callback
	 * @param array $arguments
	 */
	public function walkShards($callback, $arguments = array())
	{
		$class_name = get_class($this);
		/** @var ShardedRecord $model */
		$model = new $class_name;
		$keys = $model->getShardMapper()->getKeysScope();

		foreach ($keys as $key)
		{
			$model->setShardAttribute($key);
			$db = $model->getDbConnection();
			$table = $model->tableName();
			call_user_func_array($callback, array_merge(array($db, $table), $arguments));
		}
	}

	protected function beforeFind()
	{
		parent::beforeFind();

		if ($this->{$this->shardAttribute} === null)
			throw new CException("Unable to get shard id. Attribute " . $this->shardAttribute . ' is not set.');
	}

	protected function beforeSave()
	{
		if ($this->{$this->shardAttribute} === null)
			throw new CException("Unable to get shard id. Attribute " . $this->shardAttribute . ' is not set.');

		return parent::beforeSave();
	}

	protected function beforeDelete()
	{
		if ($this->{$this->shardAttribute} === null)
			throw new CException("Unable to get shard id. Attribute " . $this->shardAttribute . ' is not set.');

		return parent::beforeSave();
	}

	/**
	 * Define this method to initialize a new mapper class instance. The class must extend the ShardMapper class
	 * @return ShardMapper
	 */
	abstract protected function createMapper();

}
