<?php

/**
 * @property MsieTask $Task
 * @see MsieTask
 * @package msimportexport
 */
class MsiePreset extends xPDOSimpleObject
{
    /**
     * @return array
     */
    public function getFields()
    {
        $result = array();
        $fields = $this->get('fields');
        if ($fields) {
            foreach ($fields as $key => $field) {
                if (empty($field) || $field == '-1') continue;
                $result[$key] = trim($field);
            }
        }
        return $result;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getSetting($key, $default = null)
    {
        $settings = $this->getSettings();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;

    }


    /**
     * @param array $settings
     * @param bool $save
     * @return bool
     */
    public function setSettings(array $settings, $save = false)
    {
        $result = true;
        $key = 'msimportexport_' . $this->get('mode') . '_default_settings';
        $defaultSettings = $this->xpdo->getOption($key, null, '[]', true);
        $defaultSettings = $this->xpdo->fromJSON($defaultSettings);
        $defaultSettings = is_array($defaultSettings) ? $defaultSettings : array();
        $settings = array_merge(
            $defaultSettings,
            $settings
        );
        $this->set('settings', $settings);
        if ($save) {
            $result = $this->save();
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        $key = 'msimportexport_' . $this->get('mode') . '_default_settings';
        $defaultSettings = $this->xpdo->getOption($key, null, '[]', true);
        $defaultSettings = $this->xpdo->fromJSON($defaultSettings);
        $defaultSettings = is_array($defaultSettings) ? $defaultSettings : array();
        return array_merge(
            $defaultSettings,
            $this->get('settings')
        );
    }


    public function save($cacheFlag = null)
    {
        if ($this->isNew()) {
            $token = $this->generateToken();
            $this->set('token', $token);
        }
        return parent::save($cacheFlag);
    }

    /**
     * @return string
     */
    public function generateToken()
    {
        $token = uniqid('preset_' . rand(), true) . $this->get('id');
        return md5($token);
    }

}