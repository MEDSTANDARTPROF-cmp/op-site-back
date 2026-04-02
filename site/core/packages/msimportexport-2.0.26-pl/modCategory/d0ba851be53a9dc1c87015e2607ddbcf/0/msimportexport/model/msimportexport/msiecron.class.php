<?php

class MsieCron extends xPDOSimpleObject
{

    /**
     * @param Msie $msie
     */
    public static function revise(Msie &$msie)
    {
        /** @var modX $modx */
        $modx = &$msie->modx;
        $classKey = 'MsieCron';

        $q = $modx->newQuery($classKey);
        $time = time();
        $q->where(
            array(
                'active' => 1,
                "DATE_FORMAT(FROM_UNIXTIME(date_last_run),'%Y-%m-%d %H:%i') < DATE_FORMAT(FROM_UNIXTIME({$time}),'%Y-%m-%d %H:%i')"
            )
        );

        if ($tasks = $modx->getCollection($classKey, $q)) {
            foreach ($tasks as $task) {
                $cron = Cron\CronExpression::factory($task->get('schedule'));
                if ($cron->isDue()) {
                    $task->set('date_last_run', time());
                    if ($task->save()) {
                        $options = array(
                            'cron_id' => $task->get('id')
                        );
                        $msie->getTaskManager()->add($task->get('preset_id'), $options);
                    }
                }
            }
        }
    }

}