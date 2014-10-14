<?php
/**
 * Class ShardDataReader represents a forward-only stream of rows from a query result set through all of the shards used in a model.
 */
class ShardDataReader extends CApplicationComponent implements Iterator //, Countable
{
	/** @var ShardedRecord */
	private $model;
	private $keys;
	private $sql;
	private $params;
	private $index = -1;
	/** @var CDbDataReader */
	private $query;
	private $shardId;


	/**
	 * @param ShardedRecord $model
	 * @param string $sql
	 * @param array $params
	 */
	public function __construct(ShardedRecord $model, $sql, $params = array())
	{
		$model_class_name = get_class($model);
		$this->model = new $model_class_name;
		$this->keys = $model->getShardMapper()->getKeysScope();
		$this->sql = $sql;
		$this->params = $params;
	}

	/**
	 * Returns the current row.
	 * This method is required by the interface Iterator.
	 * @return mixed the current row.
	 */
	public function current()
	{
		return $this->query->current();
	}

	/**
	 * Moves the internal pointer to the next row through all results in all shards
	 * This method is required by the interface Iterator.
	 */
	public function next()
	{
		// Init a query to the first shard
		if ($this->query === null)
			$this->nextShard();

		do
		{
			$this->query->next();
		}
		// Move to the next shard after the last row from the current shard
		while (!$this->valid() && $this->nextShard());

		$this->index++;
	}

	/**
	 * Returns the index of the current row.
	 * This method is required by the interface Iterator.
	 * @return integer the index of the current row.
	 */
	public function key()
	{
		return $this->index;
	}

	/**
	 * Returns whether there is a row of data at current position.
	 * This method is required by the interface Iterator.
	 * @return boolean whether there is a row of data at current position.
	 */
	public function valid()
	{
		return $this->query->valid();
	}

	/**
	 * Resets the iterator to the initial state.
	 * This method is required by the interface Iterator.
	 * @throws CException if this method is invoked twice
	 */
	public function rewind()
	{
		if ($this->index < 0)
			$this->next();
		else
			throw new CDbException('ShardDataReader cannot rewind. It is a forward-only reader.');
	}

	/**
	 * @return mixed Current shard ID
	 */
	public function currentShardId()
	{
		return $this->shardId;
	}

	/**
	 * Creates a query to the next shard
	 * @return bool If the next shard exists
	 */
	private function nextShard()
	{
		$key = current($this->keys);
		next($this->keys);
		if ($key === false)
			return false;

		$this->model->setShardAttribute($key);
		$sql = str_replace('{{table}}', $this->model->tableName(), $this->sql);
		$this->query = $this->model->getDbConnection()->createCommand($sql)->query($this->params);

		return true;
	}
}