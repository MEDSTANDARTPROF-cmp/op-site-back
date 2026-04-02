<?php

require_once dirname(__FILE__) . '/content.class.php';

class modQuickViewSnippetProcessor extends modQuickViewContentProcessor
{
    public function process()
    {
        $response = $this->QuickView->invokeEvent('QuickViewOnBeforeProcessContent', array(
            'self' => $this,
        ));
        if (!$response['success']) {
            return $this->QuickView->failure($response['message']);
        }

        $data = parent::process();

        $response = $this->QuickView->invokeEvent('QuickViewOnProcessContent', array(
            'self'  => $this,
            'data'  => $data,
            'extra' => null,
        ));
        if (!$response['success']) {
            return $this->QuickView->failure($response['message']);
        }

        $data = $response['data']['data'];
        $extra = $response['data']['extra'];
        if ($data === false) {
            return $this->QuickView->failure('');
        }

        return $this->QuickView->success('', array('output' => $data, 'extra' => $extra));
    }
}

return 'modQuickViewSnippetProcessor';