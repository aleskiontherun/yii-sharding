<?php

/**
 * Class UserShardMapper is used to determine shard location based on user ID
 */
class UserShardMapper extends ShardMapper
{
	/**
	 * @return int Shard database connection ID
	 */
	protected function calculateConnectionId()
	{
		return 0;
		//return $this->getTableId() > 49 ? 1 : 0; // For 2 servers
	}

	/**
	 * @return int Shard table ID
	 */
	protected function calculateTableId()
	{
		return $this->getShardId() % 100;
	}

	/**
	 * @return int Shard ID
	 */
	protected function calculateShardId()
	{
		return $this->getKey() % 10000;
	}

	/**
	 * @return array The full scope of shard attribute keys
	 */
	protected function calculateKeysScope()
	{
		return range(1, 100);
	}

}
