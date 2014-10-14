<?php

/**
 * Class ShardMapper is a base abstract class for shard mappers
 */
abstract class ShardMapper
{

	private $_key;
	private $_shardId;
	private $_dbId;
	private $_tableId;
	private $_keysScope;

	/**
	 * @param mixed $key The key to determine its shard location
	 */
	public function __construct($key)
	{
		$this->_key = $key;
	}

	/**
	 * @return mixed The key to determine its shard location
	 */
	public function getKey()
	{
		return $this->_key;
	}

	/**
	 * @return int Shard ID
	 */
	protected function getShardId()
	{
		if ($this->_shardId === null)
			$this->_shardId = $this->calculateShardId();
		return $this->_shardId;
	}

	/**
	 * @return int Shard connection ID
	 */
	public function getConnectionId()
	{
		if ($this->_dbId === null)
			$this->_dbId = $this->calculateConnectionId();
		return $this->_dbId;
	}

	/**
	 * @return int Shard table ID
	 */
	public function getTableId()
	{
		if ($this->_tableId === null)
			$this->_tableId = $this->calculateTableId();
		return $this->_tableId;
	}

	/**
	 * @return array The full scope of shard attribute keys
	 */
	public function getKeysScope()
	{
		if ($this->_keysScope === null)
			$this->_keysScope = $this->calculateKeysScope();
		return $this->_keysScope;
	}

	/**
	 * @return int Shard ID
	 */
	abstract protected function calculateShardId();

	/**
	 * @return int Shard database connection ID
	 */
	abstract protected function calculateConnectionId();

	/**
	 * @return int Shard table ID
	 */
	abstract protected function calculateTableId();

	/**
	 * @return array The full scope of shard attribute keys
	 */
	abstract protected function calculateKeysScope();

}
