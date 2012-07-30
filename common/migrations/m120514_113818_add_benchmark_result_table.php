<?php

class m120514_113818_add_benchmark_result_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('benchmark_result',array(
			'id'=>'pk',
			'benchmarkId'=>'integer',
			'initialLoadAverage'=>'float',
			'finalLoadAverage'=>'float',
			'serverSoftware'=>'string',
			'serverHostname'=>'string',
			'serverPort'=>'integer',
			'documentPath'=>'text',
			'documentSize'=>'integer',
			'concurrency'=>'integer',
			'duration'=>'float',
			'completedRequests'=>'integer',
			'failedRequests'=>'integer',
			'failedOnConnect'=>'integer',
			'failedOnReceive'=>'integer',
			'failedOnLength'=>'integer',
			'failedOnException'=>'integer',
			'writeErrors'=>'integer',
			'totalTransferred'=>'integer',
			'htmlTransferred'=>'integer',
			'requestsPerSecond'=>'float',
			'timePerRequest'=>'float',
			'longestRequest'=>'float',
			'transferRate'=>'float',
			'timeAdded'=>'datetime'
		));
	}

	public function down()
	{
		$this->delete('benchmark_result');
		return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}