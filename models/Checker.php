<?php

class Checker
{
    /**
     * Listing of each version path to check patch against.
     * @var array
     */
    protected $_versions;

    /**
     * Constructor.
     * Requires path string to directies to test patch against.
     *
     * @param MagentoVersions $versions
     */
    public function __construct(MagentoVersions $versions)
    {
        $this->_versions = $versions;
    }

    public function checkPatchForReleaseVersion($patchName, $releasePath)
    {
        if ($releasePath == '') {
            return 'n/a';
        }

        $targetDir = ($releasePath{0} == '/')
            ? $releasePath
            : DEPLOYMENTS_BASE_PATH . $releasePath;

        if (!is_dir($targetDir)) {
            return 'n/a';
        }

        chdir($targetDir);

        exec('patch --dry-run -p0 < ' . UPLOAD_PATH . $patchName, $output, $returnStatus);

        return !$returnStatus;
    }

//     public function checkMergedPathForRelease($patchName, $release)
//     {
//         if (!count($patchName['files'])) {
//             return 'n/a';
//         }
//
//         $sourceFolder = DEPLOYMENTS_BASE_PATH . $release[1] . DIRECTORY_SEPARATOR;
//         if (!is_dir($sourceFolder)) {
//             return 'n/a';
//         }
//
//         foreach ($patchName['files'] as $file) {
//             $dirname = dirname($file);
//             if (!empty($dirname) && $dirname != '.' && !is_dir($dirname)) {
//                 mkdir($dirname, 0777, true);
//             }
//             $sourcePath = $sourceFolder . $file;
//             if (!file_exists($sourcePath) || file_exists($file)) {
//                 continue;
//             }
//             copy($sourcePath, $file);
//         }
//
//         $returnStatus = 0;
//         $output = array();
//
//         exec('patch -p0 < ' . UPLOAD_PATH . $patchName['name'], $output, $returnStatus);
//         exec('rm -Rf *');
//
//         return !$returnStatus;
//     }

    /**
     * Check patch against all versions.
     *
     * @param string $patch
     * @return array
     */
    public function checkAllVersions($patch)
    {
        $results = [];
        foreach ($this->_versions as $version) {
            if (!isset($results[$version['release']])) {
                $results[$version['release']] = [];
            }

            $results[$version['release']][$version['version']] =
                $this->checkPatchForReleaseVersion($patch, $version['path']);
        }

        return $results;
    }

//     public function mergePatchesToOne($patches, $names)
//     {
//         $temporaryPatchName = md5(uniqid(time())) . '.merged.patch';
//         exec('touch ' . UPLOAD_PATH . $temporaryPatchName);
//
//         foreach ($patches as $fileId => $patch) {
//             exec(sprintf(
//                 'cat %s >> %s',
//                 UPLOAD_PATH . $patch,
//                 UPLOAD_PATH . $temporaryPatchName
//             ));
//             exec('rm -Rf ' . UPLOAD_PATH . $patch);
//         }
//
//         exec('grep "diff --git" ' . UPLOAD_PATH . $temporaryPatchName . ' | awk \'{print $3}\'', $files);
//
//         return array(
//             'name' => $temporaryPatchName,
//             'files' => $files
//         );
//     }

    protected function _initWorkDirectory()
    {
        $workDirectory = UPLOAD_PATH . md5(uniqid(time()));
        mkdir($workDirectory, 0777);
        return $workDirectory;
    }

}
