<?php

/**
 * Class UserShardedRecord
 *
 * @property int $user_id Must be defined to perform queries
 */
class UserShardedRecord extends ShardedRecord
{
	protected $shardAttribute = 'user_id';

	protected $dbNamePrefix = 'dbUserData';

	/**
	 * @return UserShardMapper
	 */
	protected function createMapper()
	{
		return new UserShardMapper($this->getAttribute($this->shardAttribute));
	}

	/**
	 * Named scope for queries with specific user ID
	 * @param int $user_id
	 * @return $this
	 */
	public function user($user_id)
	{
		return $this->shardKey($user_id);
	}

}
