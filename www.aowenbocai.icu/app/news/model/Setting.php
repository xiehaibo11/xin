<?php
namespace app\news\model;

use think\Model;

class Setting extends Model
{
    protected $connection = [
        'type'        => 'sqlite',
        'database'    => APP_PATH . 'news/db/data.db',
        'charset'     => 'utf8',
        'auto_timestamp' => false,
        'prefix'      => 'kr_'
    ];
    protected $pk = 'name';

    private static $config = [];

    public function setValue($name, $value, $title = '')
    {
        $data = [
            'name' => $name,
            'value' => $value
        ];
        if (!empty($title)) {
            $data['title'] = $title;
        }

        if (!$this->hasValue($name)) {
            return $this->save($data);
        }

        self::$config[$name] = $value;
        return $this->save($data, ['name' => $name]);
    }

    public function getValue($name = '')
    {
        if (empty($name)) {
            $data = $this->column('value','name');
            return $data;
        }

        $this->setConfig($name);
        return self::$config[$name];
    }

    public function removeValue($name)
    {
        $data = $this->destroy($name);
        return $data;
    }

    public function hasValue($name)
    {
        return $this->setConfig($name);
    }

    private function setConfig($name)
    {
        if (!isset(self::$config[$name])) {
            $data = $this->get($name);
            if (!$data) {
                return;
            }
            self::$config[$name] = $data->value;
        }
        return true;
    }
}
