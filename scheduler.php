<?php
/**
 * Copyright (c) 2009-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_scheduler.php');

class Onyx_Controller_Scheduler extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction()
    {

        $Scheduler = new common_scheduler();
        $Scheduler->setCacheable(false);

        // quick fix to reactivate zombie job
        // todo: convert scheduler to async script
        if (rand(0, 20) == 1) $Scheduler->executeSql("UPDATE common_scheduler SET status = 0 WHERE status = 1 AND (now() - start_time) > INTERVAL '10 minutes'");

        if ($Scheduler->anyPendingJobs() && ($lock_token = $Scheduler->lockPendingJobs()) > 0) {

            $jobs = $Scheduler->getLockedJobs($lock_token);

            if (is_array($jobs)) {

                foreach ($jobs as $job_data) {

                    $Scheduler->setJobStart($job_data['id']);
                    $result = $this->runJob($job_data);
                    $messages = trim(strip_tags(urldecode($result->messages)));
                    $Scheduler->setJobCompleted($job_data['id'], $result->status, $messages);

                }

            } else {
                msg("Scheduler: Invalid lock token!");
            }

        }

        return true;

    }


    public function runJob($job_data)
    {
        // preprare request URI
        $request = "bo/scheduler/" . $job_data['controller'] . "@bo/scheduler/action_base~node_id={$job_data['node_id']}:" .
            "node_type={$job_data['node_type']}";
        if (strlen($job_data['parameters']) > 0) $request .= ":" . $job_data['parameters'];
        $request .= "~";

        $job = new Onyx_Request($request);
        $content = $job->getContent();
        $result = json_decode($content);
        if ($result === null) msg("Invalid JSON response from bo/scheduler/" . $job_data['controller'], 2);
        return $result;
    }

}
