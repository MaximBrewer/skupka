<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorInFolderSubfolders extends N2GeneratorAbstract {

    function getSubFolders($folders = array(), $ready = array()) {
        $subFolders = array();
        foreach ($folders AS $folder) {
            $ready[]          = $folder;
            $subFoldersHelper = N2Filesystem::folders($folder);
            foreach ($subFoldersHelper AS $helper) {
                $subFolders[] = $folder . '/' . $helper;
            }
        }
        if (!empty($subFolders)) {
            return $this->getSubFolders($subFolders, $ready);
        } else {
            return $ready;
        }
    }

    protected function _getData($count, $startIndex) {
        $root       = N2Filesystem::getImagesFolder();
        $source = $this->data->get('sourcefolder', '');
        if (substr($source, 0, 1) == '*') {
            $source = substr($source, 1);
            if (!N2Filesystem::existsFolder($source)) {
                N2Message::error(n2_('Wrong path. This is the default image folder path, so try to navigate from here:') . '<br>*' . $root);
                return null;
            } else {
                $root   = '';
            }
        }
        $baseFolder = N2Filesystem::realpath($root . '/' . ltrim(rtrim($source, '/'), '/'));

        $folders = $this->getSubFolders(array( $baseFolder ));

        $allFiles = array();
        foreach ($folders AS $f) {
            $allFiles[$f] = N2Filesystem::files($f);
        }

        $return  = array();
        $counter = 0;
        foreach ($allFiles AS $folder => $files) {
            $counter++;
            for ($i = count($files) - 1; $i >= 0; $i--) {
                $ext = strtolower(pathinfo($files[$i], PATHINFO_EXTENSION));
                if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif') {
                    array_splice($files, $i, 1);
                }
            }

            $IPTC = $this->data->get('iptc', 0) && function_exists('exif_read_data');

            $data = array();
            for ($i = 0; $i < $count && isset($files[$i]); $i++) {
                $image    = N2ImageHelper::dynamic(N2Uri::pathToUri($folder . '/' . $files[$i]));
                $data[$i] = array(
                    'image'      => $image,
                    'thumbnail'  => $image,
                    'title'      => $files[$i],
                    'name'       => preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]),
                    'folder'     => $folder,
                    'foldername' => basename($folder)
                );
                if ($IPTC) {
                    $properties = @exif_read_data($folder . '/' . $files[$i]);
                    if ($properties) {
                        foreach ($properties AS $key => $property) {
                            if (!is_array($property) && $property != '' && preg_match('/^[a-zA-Z]+$/', $key)) {
                                $data[$i][$key] = $property;
                            }
                        }
                    }
                }
            }
            $return = array_merge($return, $data);
        }
        $return = array_slice($return, $startIndex, $count);

        $order = explode("|*|", $this->data->get('order', '0|*|asc'));
        if ($order[0] != 0) {
            usort($return, 'N2GeneratorInFolderSubfolders::' . $order[1]);
        }

        return $return;
    }

    public static function asc($a, $b) {
        return ($b['title'] < $a['title'] ? 1 : -1);
    }

    public static function desc($a, $b) {
        return ($a['title'] < $b['title'] ? 1 : -1);
    }
}