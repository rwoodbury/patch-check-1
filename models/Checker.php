<?php

class Checker
{
    protected $_eeDeploymentList = array(
        array('1.14.3.1'    => 'ee11431'),
        array('1.14.3.0'    => 'ee11430'),
        array('1.14.2.4'    => 'ee11424'),
        array('1.14.2.3'    => 'ee11423'),
        array('1.14.2.2'    => 'ee11422'),
        array('1.14.2.1'    => 'ee11421'),
        array('1.14.2.0'    => 'ee11420'),
        array('1.14.1.0'    => 'ee11410'),
        array('1.14.0.1'    => 'ee11401'),
        array('1.14.0.0'    => 'ee11400'),
        array('1.13.1.0'    => 'ee11310'),
        array('1.13.0.2'    => 'ee11302'),
        array('1.13.0.1'    => 'ee11301'),
        array('1.13.0.0'    => 'ee11300'),
        array('1.12.0.2'    => 'ee11202'),
        array('1.12.0.1'    => 'ee11201'),
        array('1.12.0.0'    => 'ee11200'),
        array('1.11.2.0'    => 'ee11120'),
        array('1.11.1.0'    => 'ee11110'),
        array('1.11.0.2'    => 'ee11102'),
        array('1.11.0.1'    => 'ee11101'),
        array('1.11.0.0'    => 'ee11100'),
        array('1.10.1.1'    => 'ee11011'),
        array('1.10.1.0'    => 'ee11010'),
        array('1.10.0.2'    => 'ee11002'),
        array('1.10.0.1'    => 'ee11001'),
        array('1.10.0.0'    => 'ee11000'),
        array('1.9.1.1'     => 'ee1911'),
        array('1.9.1.0'     => 'ee1910'),
        array('1.9.0.0'     => 'ee1900'),
        array('1.8.0.0'     => 'ee1800'),
        array('1.7.1.0'     => 'ee1710'),
        array('1.7.0.0'     => 'ee1700'),
        array('1.6.0.0'     => 'EE/1.6.0.0'),
        array('1.3.2.4'     => 'EE/1.3.2.4')
    );

    protected $_ceDeploymentList = array(
        array('1.9.2.4'     => 'CE/1.9.2.4'),
        array('1.9.2.3'     => 'CE/1.9.2.3'),
        array('1.9.2.0'     => 'CE/1.9.2.0'),
        array('1.9.1.1'     => 'CE/1.9.1.1'),
        array('1.9.1.0'     => 'CE/1.9.1.0'),
        array('1.9.0.1'     => 'CE/1.9.0.1'),
        array('1.9.0.0'     => 'CE/1.9.0.0'),
        array('1.8.1.0'     => 'CE/1.8.1.0'),
        array('1.8.0.0'     => 'CE/1.8.0.0'),
        array('1.7.0.2'     => 'CE/1.7.0.2'),
        array('1.7.0.1'     => 'CE/1.7.0.1'),
        array('1.7.0.0'     => 'CE/1.7.0.0'),
        array('1.6.2.0'     => 'CE/1.6.2.0'),
        array('1.6.1.0'     => 'CE/1.6.1.0'),
        array('1.6.0.0'     => 'CE/1.6.0.0'),
        array('1.5.1.0'     => 'CE/1.5.1.0'),
        array('1.5.0.1'     => 'CE/1.5.0.1'),
        array('1.5.0.0'     => 'CE/1.5.0.0'),
        array('1.4.2.0'     => 'CE/1.4.2.0'),
        array('1.4.1.1'     => 'CE/1.4.1.1'),
        array('1.4.1.0'     => 'CE/1.4.1.0'),
        array('1.4.0.1'     => 'CE/1.4.0.1'),
        array('1.4.0.0'     => 'CE/1.4.0.0')
    );

    protected $_peDeploymentList = array(
        array('1.12.0.0'    => 'PE/1.12.0.0'),
        array('1.11.1.0'    => 'PE/1.11.1.0'),
        array('1.11.0.0'    => 'PE/1.11.0.0'),
        array('1.10.1.0'    => 'PE/1.10.1.0'),
        array('1.10.0.2'    => 'PE/1.10.0.2'),
        array('1.10.0.1'    => 'PE/1.10.0.1'),
        array('1.10.0.0'    => 'PE/1.10.0.0'),
        array('1.9.1.0'     => 'PE/1.9.1.0'),
        array('1.9.0.0'     => 'PE/1.9.0.0'),
        array('1.8.0.0'     => 'PE/1.8.0.0')
    );

    public function checkPatchForRelease($patchName, $releasePath)
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

    public function checkMergedPathForRelease($patchName, $release)
    {
        if (!count($patchName['files'])) {
            return 'n/a';
        }

        $sourceFolder = DEPLOYMENTS_BASE_PATH . $release[1] . DIRECTORY_SEPARATOR;
        if (!is_dir($sourceFolder)) {
            return 'n/a';
        }

        foreach ($patchName['files'] as $file) {
            $dirname = dirname($file);
            if (!empty($dirname) && $dirname != '.' && !is_dir($dirname)) {
                mkdir($dirname, 0777, true);
            }
            $sourcePath = $sourceFolder . $file;
            if (!file_exists($sourcePath) || file_exists($file)) {
                continue;
            }
            copy($sourcePath, $file);
        }

        $returnStatus = 0;
        $output = array();

        exec('patch -p0 < ' . UPLOAD_PATH . $patchName['name'], $output, $returnStatus);
        exec('rm -Rf *');

        return !$returnStatus;
    }

    public function checkPatchForAllReleases($patchName, $withoutDryRun = false)
    {
        $result = array();

        if (!$withoutDryRun) {
            foreach ($this->_eeDeploymentList as $release) {
                $release = each($release);
                $result['ee'][] = array(
                    'release_name' => $release[0],
                    'check_result' => $this->checkPatchForRelease($patchName, $release[1])
                );
            }
            foreach ($this->_ceDeploymentList as $release) {
                $release = each($release);
                $result['ce'][] = array(
                    'release_name' => $release[0],
                    'check_result' => $this->checkPatchForRelease($patchName, $release[1])
                );
            }
            foreach ($this->_peDeploymentList as $release) {
                $release = each($release);
                $result['pe'][] = array(
                    'release_name'  => $release[0],
                    'check_result'  => $this->checkPatchForRelease($patchName, $release[1])
                );
            }
        } else {
            $workDirectory = $this->_initWorkDirectory();
            chdir($workDirectory);
            foreach ($this->_eeDeploymentList as $release) {
                $release = each($release);
                $result['ee'][] = array(
                    'release_name' => $release[0],
                    'check_result' => $this->checkMergedPathForRelease($patchName, $release)
                );
            }
            foreach ($this->_ceDeploymentList as $release) {
                $release = each($release);
                $result['ce'][] = array(
                    'release_name' => $release[0],
                    'check_result' => $this->checkMergedPathForRelease($patchName, $release),
                );
            }
            foreach ($this->_peDeploymentList as $release) {
                $release = each($release);
                $result['pe'][] = array(
                    'release_name' => $release[0],
                    'check_result' => $this->checkMergedPathForRelease($patchName, $release),
                );
            }
            exec('rm -Rf ' . $workDirectory);
        }

        return $result;
    }

    public function mergePatchesToOne($patches, $names)
    {
        $temporaryPatchName = md5(uniqid(time())) . '.merged.patch';
        exec('touch ' . UPLOAD_PATH . $temporaryPatchName);

        foreach ($patches as $fileId => $patch) {
            exec(sprintf(
                'cat %s >> %s',
                UPLOAD_PATH . $patch,
                UPLOAD_PATH . $temporaryPatchName
            ));
            exec('rm -Rf ' . UPLOAD_PATH . $patch);
        }

        exec('grep "diff --git" ' . UPLOAD_PATH . $temporaryPatchName . ' | awk \'{print $3}\'', $files);

        return array(
            'name' => $temporaryPatchName,
            'files' => $files
        );
    }

    protected function _initWorkDirectory()
    {
        $workDirectory = UPLOAD_PATH . md5(uniqid(time()));
        mkdir($workDirectory, 0777);
        return $workDirectory;
    }

    public function drawResults($results) {
        $output = '';
        foreach ($results as $release) {
            $output .= '<tr><td';
            if ($release['check_result'] === 'n/a') {
                if ($release['release_name'] === 'n/a') {
                    $column_content = '&nbsp;';
                } else {
                    $column_content = $release['release_name'];
                }
                $output .= ' colspan="2">' . $column_content;
            } else {
                $output .= '>' . $release['release_name'] . '</td>';
                if ($release['check_result'] == true) {
                    $output .= '<td class="td_ok">Ok';
                } else {
                    $output .= '<td class="td_fail">No';
                }
            }
            $output .= '</td></tr>';
        }
        return $output;
    }
}
