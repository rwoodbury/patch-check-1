<?php

class MagentoVersionList extends \Diskerror\Typed\TypedArray
{
    /**
     * Location where individual versions of Magento are saved.
     * @var string
     */
    protected $_versionsRoot = '../versions';

    /**
     * Override type from \Diskerror\Typed\TypedArray.
     * @var array
     */
    protected $_type = 'MagentoVersion';

    /**
     * Constructor.
     * @param string $versionsRoot
     */
    public function __construct($versionsRoot='')
    {
        if (is_string($versionsRoot) && $versionsRoot!=='') {
            $this->_versionsRoot = $versionsRoot;
        }

        if (!is_dir($this->_versionsRoot)) {
            throw new Exception('problem with versions directory');
        }

        //  [..., '../versions/ee/1.14.2.0/', '../versions/ee/1.14.2.1/', ...]
        $paths = glob($this->_versionsRoot . '/?e/*', GLOB_ONLYDIR | GLOB_MARK);

        if (count($paths) < 1) {
            throw new Exception('no release or version directories found');
        }

        natsort($paths);

        $versions = [];
        foreach ($paths as $path) {
            $parts = explode(DIRECTORY_SEPARATOR, $path);
            $c = count($parts);
            $versions[] = [
                'path'      => $path,
                'release'   => $parts[$c-3],
                'version'   => $parts[$c-2]
            ];
        }

        parent::__construct($versions);
    }

}
