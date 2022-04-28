<?php
/**
 * Copyright (c) 2009-2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_file extends Onyx_Model {

    /**
     * PRIMARY KEY
     * @access private
     */
    var $id;
    /**
     * @access private
     */
    var $src;
    /**
     * @access private
     * e.g. main (default), teaser, RTE
     */
    var $role;
    
    /**
     * NOT NULL REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $node_id;
    /**
     * @access private
     */
    var $title;
    /**
     * @access private
     */
    var $description;
    /**
     * @access private
     */
    var $priority;
    /**
     * @access private
     */
    var $modified;
    /**
     * @access private
     */
    var $author;
    /**
     * @access private
     */
    var $customer_id;

    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'src'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'role'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'author'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'content'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
        'link_to_node_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>false)
    );
    
    /**
     * create table sql
     * 
     * @return string
     * SQL command for table creating
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE common_file ( 

    id serial NOT NULL PRIMARY KEY,
    src varchar(255),
    role character varying(255),
    node_id int NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
    title varchar(255) ,
    description text ,
    priority int DEFAULT 0 NOT NULL,
    modified timestamp(0) ,
    author int,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT

);
        ";
        
        return $sql;
    }

    /**
     * get file detail
     * 
     * @param integer $id
     * file ID
     * 
     * @return array
     * file detail
     */
     
    public function getFileDetail($id) {
        
        if (!is_numeric($id)) return false;
        
        $file_detail = $this->detail($id);
        
        $file_detail = $this->populateAdditionalInfo($file_detail);
        
        return $file_detail;
    }
    
    /**
     * get detail alias to getFileDetail
     * 
     * @param integer $id
     * file ID
     * 
     * @return array
     * file detail
     */
     
    public function getDetail($id) {
        
        return $this->getFileDetail($id);
        
    }
    
    /**
     * update file
     * 
     * @param array $data
     * file data
     * 
     * @return bool
     * on error return false
     */
     
    public function updateFile($data) {
        
        if (!is_numeric($data['link_to_node_id'])) $data['link_to_node_id'] = 0;
        
        return $this->update($data);
        
    }

    /**
     * get additional info
     * 
     * @param array $file_detail
     * file information
     * @param boolean $include_image_size
     * 
     * @return array
     * extended file detail
     */
     
    public function populateAdditionalInfo($file_detail, $include_image_size = false) {
        
        $full_path = ONYX_PROJECT_DIR . $file_detail['src'];
        
        $file_detail['file_path_encoded'] = $this->encode_file_path($full_path);
            
        if (file_exists($full_path)) {
            $file_detail['info'] = $this->getFileInfo($full_path);
        } else {
            msg("File $full_path", 'error', 1);
        }
        
        if ($include_image_size && preg_match('/^image/', $file_detail['info']['mime-type'])) {
            $file_detail['imagesize'] = $this->getImageSize($full_path);
        }
            
        return $file_detail;
    }
    
    /**
     * list files
     * 
     * @param integer $node_id
     * ID of node with files
     * 
     * @param string $role
     * role name or false for all
     *
     * @param string $order
     * sorting part of SQL
     * 
     * @return array
     * list of files
     */
     
    function listFiles($node_id , $role = false, $order = "priority DESC, id ASC", $limit = '') {
    
        $result = array();
        
        if (is_numeric($node_id)) {
        
            if ($role) {
        
                $files = $this->listing("node_id = $node_id AND role = '$role'", $order, $limit);
        
            } else {
        
                $files = $this->listing("node_id = $node_id", $order, $limit);
        
            }
        
        } else {
        
            msg("File->listFiles: node_id is not numeric", 'error');
        
            return false;
            
        }

        foreach ($files as $file_detail) {
            
            $file_detail = $this->populateAdditionalInfo($file_detail);
            
            $result[] = $file_detail;
        }
        
        return $result;
    }
    
    /**
     * get file link
     * 
     * @param string $src
     * src of file
     * 
     * @return array
     * files with this src
     */
     
    function getFileLink($src) {
    
        $file_list = $this->listing("src='$src'");
        return $file_list;
    }
    
    /**
     * insert file
     * 
     * @param array $file
     * information of file for insert
     * 
     * @return integer
     * ID of inserted file or false
     */
     
    function insertFile($file = array()) {
    
        $src = ONYX_PROJECT_DIR . $file['src'];
        
        if (is_readable($src)) {
            
            if (!is_numeric($file['priority'])) $file['priority'] = 0;
            $file['modified'] = date('c');
            if (!is_numeric($file['author'])) $file['author'] = 0; // deprecated as of Onyx 1.7
            if (!is_numeric($file['customer_id'])) {
                $bo_user_id = Onyx_Bo_Authentication::getInstance()->getUserId();
                if (is_numeric($bo_user_id)) $file['customer_id'] = $bo_user_id;
                else $file['customer_id'] = (int) $_SESSION['client']['customer']['id'];
            }
            
            if ($id = $this->insert($file)) {
                msg('File Inserted', 'ok', 2);
                return $id;
            } else {
                msg("Can't insert file $src", 'error');
                return false;
            }
            
        } else {
            msg("$src does not exists!", 'error');
            return false;
        }
    }
    
    /**
     * Copy single uploaded file
     * return string filename on success, array when file exists, otherwise false 
     *
     * @param array $file
     * information of uploaded file
     * 
     * @param string $save_dir
     * directory for save file
     * 
     * @param unknown_type $overwrite
     * not used
     * 
     * @return mixed
     * string returned when upload to the save_dir was successfull
     * array returned when saved to temporary folder with saved file information 
     * false returned on failure
     */
     
    function getSingleUpload($file = array(), $save_dir, $overwrite = true) {
    
        $upload_file = $file['tmp_name'];
        $safe_filename = $this->nameToSafe($file['name']);
        $save_file = $save_dir . $safe_filename;
        $save_file_full = ONYX_PROJECT_DIR . $save_file;
        $save_dir_full = ONYX_PROJECT_DIR . $save_dir;
    
        if (!file_exists($save_dir_full)) {
            if (!mkdir($save_dir_full)) {
                msg("common_file.getSingleUpload(): Cannot create folder $save_dir_full", 'error');
            }
        }
    
        if (is_uploaded_file($upload_file)) {
        
            if (file_exists($save_file_full)) {
            
                if (!$overwrite) {
                    msg("File already exists and ovewrite is not allowed.", "error");
                    return false;
                }
                
                msg("File '$save_file_full' already exists and is being overwritten", 'error', 1);
                msg("Saving as $save_file", 'ok', 2);
                
                if (copy($upload_file, ONYX_PROJECT_DIR . $save_file)) {
                
                    //array type for result (indicates overwrite)
                    $result = array( 'filename'=> $safe_filename, 'save_dir'=> $save_dir, "temp_file"=>$save_file);
                    return $result;
                
                } else {
                
                    msg("common_file.getSingleUpload(): Cannot copy $upload_file to temp location " . ONYX_PROJECT_DIR . $save_file, 'error', 1);
                    return false;
                    
                }
                
            } else {
            
                if (copy($upload_file, $save_file_full)) {
            
                    msg("File '$save_file_full' saved.", 'ok', 2);
                    //chmod($save_file_full, 0666);
                    //string type for result
                    return $save_file;
            
                } else {
            
                    msg("common_file.getSingleUpload(): Cannot copy $upload_file to $save_file_full", 'error');
                    return false;
            
                }
                
            }
    
        } else {

            msg("File '$upload_file' not saved.", "error");
            return false;
        
        }
    }
    
    /**
     * safe filename convertor
     * 
     * @param string $name
     * original file name
     * 
     * @param integer $maxlen
     * maximal file name length
     * 
     * @return string
     * converted file name
     */
     
    static function nameToSafe($name, $maxlen=250) {
    
        $name = self::recodeUTF8ToAscii($name);
        $name = preg_replace('/%20/', '-', $name);
        return preg_replace('/[^a-zA-Z0-9._-]/', '-', $name);
    }

    /**
     * 
     * @param string $string
     * text in UTF8 encoding
     * 
     * @return string
     * text recoded into ASCII
     */
     
    static function recodeUTF8ToAscii($string) {
        return recodeUTF8ToAscii($string);
    }
    
    
    /**
     * Ovewrite file
     * 
     * @param string $filename
     * destination file name
     * 
     * @param string $save_dir
     * destination directory
     * 
     * @param string $temp_file
     * source file name
     * 
     * @return boolean
     * is file copied successfully?
     */
     
    function overwriteFile($filename, $save_dir, $temp_file) {
    
        $result = $this->_overwriteFile($filename, $save_dir, $temp_file);
        
        if ($result) {
            
            $thumbnails_dir = ONYX_PROJECT_DIR . "var/thumbnails/";
            $sizes = scandir($thumbnails_dir);
            
            foreach ($sizes as $size) {
            
                if (preg_match("/^[0-9]*x?([0-9]*)?$/", $size)) {
                    
                    $file_full_path = $thumbnails_dir . "$size/" . md5($save_dir . $filename);
                    
                    // get all files starting with the filename (i.e. including params method, gravity, fill)
                    foreach (glob($file_full_path . "*") as $filename_to_delete) {
                        
                        if (file_exists($filename_to_delete) && is_file($filename_to_delete)) {
                        
                            if (unlink($filename_to_delete)) msg("Deleted $filename_to_delete", 'ok', 2);
                            else msg("common_file.overwriteFile(): Cannot delete $filename_to_delete");
                        
                        } else {
                            
                            msg("File $filename_to_delete was found by glob(), but it's not a valid file", 'error');
                            
                        }
                    
                    }
                    
                }
            }
            
            return $result;
            
        } else {
        
            msg("Cannot overwrite file $filename, please check file permissions on the server", 'error');
            
            return false;
        }
    }
    
    /**
     * Overwrite existing file on the filesystem
     *
     * @param string $filename
     * destination file name
     * 
     * @param string $save_dir
     * destination directory
     * 
     * @param string $temp_file
     * source file name
     * 
     * @return boolean
     * is file copied successfully?
     */
     
    function _overwriteFile($filename, $save_dir, $temp_file) {
    
        //$src_file_full = ONYX_PROJECT_DIR . "var/tmp/" . $filename;
        $src_file_full = ONYX_PROJECT_DIR . $temp_file;
        $save_file_full = ONYX_PROJECT_DIR . $save_dir . $filename;

        if (copy($src_file_full, $save_file_full)) {
            if (is_readable($save_file_full)) return true;
            else return false;
        } else {
            return false;
        }
    }
    
    /**
     * Unlink File from database
     *
     * @param integer $id
     * ID of file
     * 
     * @return boolean
     * unlinked successfully?
     */
     
    function unlinkFile( $id ) {
    
        $file = $this->detail($id);
        
        if ($this->delete($id)) {
            msg("File ID $id has been unlinked from the database", 'ok', 2);
            return true;
        } else {
            msg("Deletion of file ID $id from the database has failed", 'error');
            return false;
        }
    }
    
    /**
     * remove file from the filesystem
     *
     * @param string $file
     * file name
     * 
     * @return boolean
     * deleted successfully?
     */
     
    function deleteFile( $file ) {
    
        $file_full_path = ONYX_PROJECT_DIR . $file;
        if (file_exists($file_full_path)) {
            if (is_dir($file_full_path)) {
                if (rmdir($file_full_path)) msg("Directory has been removed");
                else msg("Can't remove directory", "error");
            } else {
                //check if it's not used from other records
                $relations_list = $this->getRelations($file);
                if ($relations_list['count'] === 0) {
                    msg("File $file has been deleted from database", 'ok', 2);
                    if (unlink($file_full_path)) msg("File $file_full_path has been deleted from filesystem", 'ok', 2);
                    else return false;
        
                    msg("File $file has been deleted successfully", 'ok', 2);
                    
                    return true;
                } else {
                    msg("Can't delete. File $file is in use.", 'error');
                    return false;
                }
            }
        } else {
            msg("Can't delete. File doesn't exists.", 'error');
            return false;
        }
    }
    
    
    /**
     * rm() -- Vigorously erase files and directories.
     *
     * @param $fileglob mixed If string, must be a file name (foo.txt), glob pattern (*.txt), or directory name.
     *                        If array, must be an array of file names, glob patterns, or directories.
     *                        
     * @return boolean
     * erased successfully?
     */
     
    function rm($fileglob) {
    
       if (is_string($fileglob)) {
           if (is_file($fileglob)) {
               return unlink($fileglob);
           } else if (is_dir($fileglob)) {
               $ok = $this->rm("$fileglob/*");
               if (! $ok) {
                   return false;
               }
               return $this->unlinkRecursive($fileglob);
           } else {
               $matching = glob($fileglob);
               if ($matching === false) {
                   trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
                   return false;
               }     
               $rcs = array_map(array($this, 'rm'), $matching);
               if (in_array(false, $rcs)) {
                   return false;
               }
           }     
       } else if (is_array($fileglob)) {
           $rcs = array_map(array($this, 'rm'), $fileglob);
           if (in_array(false, $rcs)) {
               return false;
           }
       } else {
           trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
           return false;
       }
    
       return true;
    }
    
    /**
     * Recursively delete a directory
     *
     * @param string $dir Directory name
     * @param boolean $deleteRootToo Delete specified top-level directory as well
     * 
     * @return boolean
     * erased successfully?
     */
     
    function unlinkRecursive($dir, $deleteRootToo = true) {
    
        if(!$dh = opendir($dir))
        {
            return; //TODO: return false?
        }
        while (false !== ($obj = readdir($dh)))
        {
            if($obj == '.' || $obj == '..')
            {
                continue;
            }
    
            if (!unlink($dir . '/' . $obj))
            {
                $this->unlinkRecursive($dir.'/'.$obj, true);
            }
        }
    
        closedir($dh);
       
        if ($deleteRootToo)
        {
            rmdir($dir);
        }
       
        return true;
    } 

    /**
     * Find where the file is used
     *
     * @param string $file
     * file src
     * 
     * @return array
     * file using places list
     */
     
    function getRelations($file) {

        /**
         * standard tables
         */
         
        require_once('models/common/common_file.php');
        $CommonFile = new common_file();
        $file_list['file'] = $CommonFile->getFileLink($file);
        
        require_once('models/common/common_image.php');
        $CommonImage = new common_image();
        $file_list['node'] = $CommonImage->getFileLink($file);
        
        require_once('models/common/common_taxonomy_label_image.php');
        $TaxonomyImage = new common_taxonomy_label_image();
        $file_list['taxonomy'] = $TaxonomyImage->getFileLink($file);
        
        
        require_once('models/education/education_survey_image.php');
        $SurveyImage = new education_survey_image();
        $file_list['survey'] = $SurveyImage->getFileLink($file);
        
        /**
         * ecommerce tables
         */
         
        if (ONYX_ECOMMERCE) {
            
            require_once('models/ecommerce/ecommerce_product_image.php');
            $ProductImage = new ecommerce_product_image();
            $file_list['product'] = $ProductImage->getFileLink($file);
        
            require_once('models/ecommerce/ecommerce_product_variety_image.php');
            $ProductVarietyImage = new ecommerce_product_variety_image();
            $file_list['product_variety'] = $ProductVarietyImage->getFileLink($file);
            
            require_once('models/ecommerce/ecommerce_recipe_image.php');
            $RecipeImage = new ecommerce_recipe_image();
            $file_list['recipe'] = $RecipeImage->getFileLink($file);
            
            require_once('models/ecommerce/ecommerce_store_image.php');
            $StoreImage = new ecommerce_store_image();
            $file_list['store'] = $StoreImage->getFileLink($file);
        
        } else {
            
            $file_list['product'] = [];
            $file_list['product_variety'] = [];
            $file_list['recipe'] = [];
            $file_list['store'] = [];
            
        }

        /**
         * summary
         */
         
        $file_list['count'] = count($file_list['file']) + count($file_list['node']) + 
            count($file_list['store']) + count($file_list['survey']) +
            count($file_list['product']) + count($file_list['product_variety']) + 
            count($file_list['taxonomy']) + count($file_list['recipe']);

        return $file_list;
    }

    /**
     * Get detailed file info
     *
     * @param string $fp
     * file name
     * @param boolean $extra_info
     * @param boolean $fast
     * 
     * @return array
     * file info
     */
     
    static function getFileInfo($fp, $extra_detail = false, $fast = true) {
    
        if (trim($fp) == '' || !file_exists($fp)) return false;
        
        $file_info = array();
        
        if ($fast) $file_info['mime-type'] = mime_content_type_fast($fp);
        else $file_info['mime-type'] = mime_content_type($fp);
        
        $file_info['modified'] = strftime("%c", filemtime($fp));
        $file_info['file_path'] = str_replace(ONYX_PROJECT_DIR . 'var/files/', '', $fp);
        $file_info['size'] = self::resize_bytes(filesize($fp));
        
        if ($extra_detail) {
            $file_info['mime-type'] = local_exec("file -bi " . escapeshellarg($fp)); // overwrite the above mime-type with more details
            $file_info['type-detail'] = local_exec("file -b " . escapeshellarg($fp));
            if (trim($file_info['mime-type']) == 'application/pdf') {
                $file_info['extra-detail'] = local_exec("pdfinfo " . escapeshellarg($fp));
            } else if (preg_match("/^image/", $file_info['mime-type'])) {
                $file_info['extra-detail'] = local_exec("identify " . escapeshellarg($fp));
            }
        }
        
        //find filename
        $file_path_segments = explode('/', $fp);
        $file_info['filename'] = $file_path_segments[count($file_path_segments)-1];

        return $file_info;
    }
    
    /**
     * encode file path to base64
     * 
     * @param string $string
     * input text
     * 
     * @return string
     * encoded text
     */
     
    function encode_file_path($string) {
    
        return str_replace('=', '_XXX_', base64_encode($string));
    }

    /**
     * decode file path from base64
     * 
     * @param string $string
     * input text
     * 
     * @return string
     * encoded text
     */
     
    function decode_file_path($string) {
    
        return base64_decode(str_replace('_XXX_', '=', $string));
    }

    /**
     * function for replace of bin/csv_from_fs
     * use the php glob() function
     * 
     * @param string $directory
     * start directory
     * 
     * @param string $type
     * type of items (default '' for all, 'f' for files, 'd' for directories)
     * 
     * @param boolean $recursive
     * walk into subdirectories?
     * 
     * @return string
     * files information formatted as from bin/csv_from_fs
     */
     
    private function csv_from_glob($directory, $type = '', $recursive = true) {
    
        $path[] = "$directory/*";
        $out = array();

        while(count($path) != 0) {
        
            $v = array_shift($path);
            
            foreach(glob($v) as $item) {
            
                if( ($type == '') || (($type == 'f') && (filetype($item) == 'file')) || (($type == 'd') && (filetype($item) == 'dir')) ) {
                    $out[] = $this->getFileFindFormat($item);
                }
                
                if ($recursive && is_dir($item)) {
                    $path[] = $item . '/*';
                }
            }
        }

        sort($out);
        $text = '';
        
        foreach ($out as $line) {
        
            $text .= "$line\n";
        
        }
        
        return $text;
    }
    
    /**
     * get file information in find format "%h/%f;%h;%f;%s;%c"
     * 
     * @param string $file
     * file name with path
     * 
     * @return string
     * formatted file informations
     */
     
    private function getFileFindFormat($file) {
    
        $fpath = "./$file";
        $path_parts = pathinfo($fpath);
        $dirname = $path_parts['dirname'];
        $basename = $path_parts['basename'];
        $filesize = filesize($fpath);
        $filectime = gmdate('D M j H:i:s Y', filectime($fpath));
    
        return "$fpath;$dirname;$basename;$filesize;$filectime";
    }

    /**
     * get file list using unix file command
     * TODO: use PHP glob() instead
     *
     * @param string $directory
     * from this directory
     * 
     * @param string $attrs
     * files attributes
     * 
     * @param integer $display_hidden
     * with hidden files (1) or not (0)
     * 
     * @return mixed
     * files array or false
     */
     
    function getFlatArrayFromFs($directory, $attrs = '', $display_hidden = 0) {
    //FIND2GLOB PATCH: function getFlatArrayFromFs($directory, $type = '', $recursive = true, $display_hidden = 0) {
    
        msg("calling getFlatArrayFromFs($directory)", 'ok', 3);
        if (!file_exists($directory)) {
            msg("Directory $directory does not exists!", 'error', 1); 
            return false;
        }
        
        $csv_list = local_exec("csv_from_fs " . escapeshellarg($directory) . " " . escapeshellarg($attrs));
        //FIND2GLOB PATCH:  $csv_list = $this->csv_from_glob($directory, $type, $recursive);
        
        $csv_list = str_replace(rtrim($directory, '/'), '', $csv_list);
        $csv_array = explode("\n", $csv_list);
    
        $basename = '/' . basename($directory) . '/';
        foreach ($csv_array as $c) {
            $x = explode(';', $c);
            //dont populate base directory
            if ($x[0] != $basename) $csv[] = $x; 
        }
        
        array_pop($csv);
    
        foreach ($csv as $c) {
        
            $l['id'] = ltrim($c[0], '/');
            $l['parent'] = ltrim($c[1], '/');
            $l['name'] = $c[2];
            $l['title'] = $c[2];
            if (is_dir($directory . $l['id'])) $l['node_group'] = 'folder';
            else $l['node_group'] = 'file';
            $l['publish'] = 1;
            $l['size'] = $this->resize_bytes($c[3]);
            $l['modified'] = str_replace('.0000000000', '', $c[4]); //remove seconds fraction
            
            if ($display_hidden) {
                $csvf[] = $l;
            } else {
                // don't display hidden files files beginning with "."
                if (!preg_match("/^\./", $l['name'])) $csvf[] = $l;
            }
        }
        
        if (!is_array($csvf)) $csvf = array();
        
        return $csvf;
    }
    
    /**
     * get joined list of files from ONYX_DIR and ONYX_PROJECT_DIR
     *
     * @param string $directory
     * from this subdirectory
     * 
     * @return array
     * merged files list
     */
     
    function getFlatArrayFromFsJoin ($directory) {
        
        $global_templates_dir = ONYX_DIR . $directory;
        $global_templates = $this->getFlatArrayFromFs($global_templates_dir);
    
        $application_templates_dir = ONYX_PROJECT_DIR . $directory;
        if (is_dir($application_templates_dir)) $application_templates = $this->getFlatArrayFromFs($application_templates_dir);
        else $application_templates = array();
    
        //merge
        $templates1 = array_merge($application_templates, $global_templates);
    
        //remove duplicates
        $ids = array();
        foreach ($templates1 as $t) {
            if (!in_array($t['id'], $ids)) {
                $ids[] = $t['id'];
                $templates[] = $t;
            }
        }
        
        return $templates;
    
    }
    
        
    /**
     * Get File List from file system
     * 
     * @param string $directory
     * from this directory
     * 
     * @param string $attrs
     * files attributes
     * 
     * @return mixed
     * files array or false
     */
     
    function getTree($directory, $type = '') {

        $list = $this->getFlatArrayFromFs($directory, $type);
        
        return $list;
    }
    
    
    /**
     * byte format
     *
     * @param integer $size
     * size in numeric format
     * 
     * @return string
     * text representation of input size
     */
     
    static function resize_bytes($size) {
    
       $count = 0;
       $format = array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
       while(($size/1024)>1 && $count<8)
       {
           $size=$size/1024;
           $count++;
       }
       $return = number_format($size,0,'','.')." ".$format[$count];
       return $return;
    }

    /**
     * get image size
     * 
     * @param string $file
     * file name
     * 
     * @return mixed
     * array with image dimensions, or false if not found
     */
     
    static function getImageSize($file) {
        
        if (is_readable($file)) {
        
            $size = getimagesize($file);
            
            if ($size) {
            
                $result['width'] = $size[0];
                $result['height'] = $size[1];
                $result['proportion'] = $result['width']/$result['height'];
                
                msg("Image has size {$result['width']}x{$result['height']}. Ratio of image sides is {$result['proportion']}", 'ok', 3);
                
                return $result;
            } else {
                msg("common_image.getImageSize(): $file is not a bitmap image (perhaps SVG?)", 'error', 2);
                return false;
            }
        } else {
            msg("common_image.getImageSize(): $file isn't readable", 'error');
            return false;
        }
    }
    
    /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer value
     * @param    string   $value
     * @return   int
     */
    static function convertBytes($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = substr($value, 0, $value_length - 1);
            $unit = strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }
            return $qty;
        }
    }
    
}
