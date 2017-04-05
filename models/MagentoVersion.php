<?php

class MagentoVersion extends Diskerror\Typed\TypedClass
{
    /**
     * Full path to Magento version directory.
     * @var string
     */
    protected $path = '';

    /**
     * Release group; 'ce', 'ee', or 'pe'.
     * @var string
     */
    protected $release = '';

    /**
     * ie. '1.14.3.2'.
     * @var string
     */
    protected $version = '';


    protected function _set_path($v)
    {
        $v = realpath($v);

        if (!is_dir($v)) {
            throw new Exception('directory ' . $v . 'does not exist');
        }

        $this->path = $v;
    }

    protected function _set_release($v)
    {
        $v = trim($v, "\x00..\x20");
        $v = strtolower($v);

        switch ($v) {
            case 'ce':
            case 'ee':
            case 'pe':
            $this->release = $v;
            break;

            default:
            throw new Exception('bad release name: ' . $v);
        }
    }

    protected function _set_version($v)
    {
        $v = trim($v, "\x00..\x20\\/");

        if (!preg_match('/^1\.\d\d?\.\d\.\d$/', $v)) {
            throw new Exception('bad version number: ' . $v);
        }

        $this->version = $v;
    }

}
