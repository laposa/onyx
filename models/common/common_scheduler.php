<?php
/**
 * class common_node
 *
 * Copyright (c) 2009-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_scheduler extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	
	/**
	 * @access private
	 */
	var $node_id;

	/**
	 * @access private
	 */
	var $node_type;
	/**
	 * @access private
	 */
	var $controller;
	/**
	 * @access private
	 */
	var $parameters;
	/**
	 * @access private
	 */
	var $scheduled_time;
	/**
	 * @access private
	 */
	var $status;
	/**
	 * @access private
	 */
	var $result;
	/**
	 * @access private
	 */
	var $start_time;
	/**
	 * @access private
	 */
	var $completed_time;
	/**
	 * @access private
	 */
	var $created;
	/**
	 * @access private
	 */
	var $modified;

	
	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'node_id'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'node_type'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'controller'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'parameters'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'scheduled_time'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'status'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'result'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'start_time'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'completed_time'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false)
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE common_scheduler (
    id integer DEFAULT nextval('common_scheduler_id_seq'::regclass) NOT NULL,
    node_id integer NOT NULL,
    node_type character varying(255),
    controller character varying(255),
    parameters text,
    scheduled_time timestamp without time zone,
    status smallint,
    result text,
    start_time timestamp without time zone,
    completed_time timestamp without time zone,
    created timestamp without time zone,
    modified timestamp without time zone DEFAULT now()
);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('common_scheduler', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['common_node'];
		else $conf = array();
		
		return $conf;
	}


	public function getScheduleForNode($node_id, $node_type)
	{
		if (!is_numeric($node_id)) return false;

		$node_type = pg_escape_string($node_type);
		return $this->listing("node_type = '$node_type' AND node_id = " . $node_id, "scheduled_time ASC");
	}

	/**
	 * Look for jobs to be executed
	 */
	public function anyPendingJobs()
	{
		$count = $this->count("scheduled_time <= NOW() AND status = 0");
		return ($count > 0);
	}

	/**
	 * Look for jobs to be executed, lock them and return the lock token
	 */
	public function lockPendingJobs()
	{
		$lock_token = rand(0, 99999999);

		$this->db->beginTransaction();

		try {

			$result = $this->db->query("LOCK TABLE common_scheduler IN ACCESS EXCLUSIVE MODE");
			$result = $this->db->query("UPDATE common_scheduler 
				SET status = 1, lock_token = $lock_token
				WHERE scheduled_time <= NOW() AND status = 0");
			$this->db->commit();
			$num_locked = (int) $result->rowCount();

		} catch (Exception $e) {
		
			$db->rollBack();
			msg($e->getMessage(), 'error', 1);
			$num_locked = 0;

		}

		return ($num_locked > 0 ? $lock_token : false);
	}


	/**
	 * Get locked jobs
	 * @param  int $lock_token Lock token
	 */
	public function getLockedJobs($lock_token)
	{
		if (!is_numeric($lock_token)) return false;
		return $this->listing("status = 1 AND lock_token = $lock_token");
	}



	/**
	 * Schedule new job
	 */
	public function scheduleNewJob($data)
	{
		$controllers = $this->getSchedulerActions();

		if (!in_array($data['controller'], $controllers)) {
			msg("Invalid controller `{$data['controller']}`");
			return false;
		}

		if ($data['scheduled_time'] > time()) {

			$validated_data = array(
				'node_id' => $data['node_id'] > 0 ? $data['node_id'] : null,
				'node_type' => $data['node_type'],
				'controller' => $data['controller'],
				'scheduled_time' => date("c", $data['scheduled_time']),
				'status' => 0,
				'created' =>  date('c'),
				'modified' =>  date('c')
			);

			$id = $this->insert($validated_data);

			return $id;

		} else {

			msg("Scheduled date or time is invalid.");

		}
	}



	/**
	 * Set job start timestamp to current date/time
	 */
	public function setJobStart($id)
	{
		if (!is_numeric($id)) return false;

		$this->update(array(
			'id' => $id,
			'start_time' => date("c")
		));
	}


	/**
	 * Set job complete timestamp to current date/time
	 * and save job result
	 */
	public function setJobCompleted($id, $status, $result)
	{
		if (!is_numeric($id)) return false;
		if (!is_bool($status)) return false;

		$this->update(array(
			'id' => $id,
	 		'status' => $status ? 2 : 3,
			'result' => $result,
			'completed_time' => date("c")
		));
	}


	/**
	 * Cancel job
	 */
	public function cancelJob($id)
	{
		if (!is_numeric($id)) return false;

		$this->update(array(
			'id' => $id,
			'status' => 4,
			'modified' => date("c")
		));
	}


	/**
	 * Get list of available scheduler actions, i.e. content
	 * of bo/scheduler directory
	 */

	public function getSchedulerActions()
	{
		$local_files = @scandir(ONXSHOP_PROJECT_DIR . "/controllers/bo/scheduler");
		$files = @scandir(ONXSHOP_DIR . "/controllers/bo/scheduler");

		if (!is_array($local_files)) $local_files = array();
		if (!is_array($files)) $files = array();

		$files = array_merge($files, $local_files);
		$result = array();

		foreach ($files as $file) {
			if ($file != 'action_base.php' && strpos($file, ".php") !== FALSE) {
				$result[] = str_replace(".php", "", $file);
			}
		}

		return $result;
	}

}
