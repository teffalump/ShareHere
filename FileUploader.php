<?php
    /* A class for file uploading */
class FileUploader
{
    protected $_max_file_size = 524288; //Filesize in bytes
    protected $_upload_path = "/uploads/"; //Directory to store files
    protected $_allowed_mimes = array("image/jpeg", "image/bmp", "image/gif", "image/png", "image/svg+xml"); //Change this, if we want
    
    public function __construct         ($max_file_size=NULL, $upload_path=NULL, $allowed_mimes=NULL)
    {
        if ($max_file_size !== NULL && is_int($max_file_size))
        {
            $this->_max_file_size = $max_file_size;
        }
        if ($upload_path !== NULL && is_writable($upload_path))
        {
            $this->_upload_path = $upload_path;    
        }
        if ($allowed_mimes !== NULL && is_array($allowed_mimes))
        {
            $this->_allowed_mimes = $allowed_mimes;
        }
    }

    public function fits                ($file)
    {
        /* Whether file is smaller than max allowed size
                Returns boolean
        */

        if (filesize($_FILES[$file]['tmp_name']) <= $this->_max_file_size)
        {
                return True;
        }
        else
        {
                return False;
        }
    }
    public function set_UploadPath      ($path)
    {
        /* Set upload path
                Returns new upload path or False if there was an error
        */

        if (!is_writable($path))
        {
            throw new Exception("Can't write to that directory");
        }
        else
        {
            $this->_upload_path = $path;
            return ($this->_upload_path);
        }
    }
    public function set_MaxFileSize     ($size)
    {
        /* Set max file size */

        $this->_max_file_size = $size;
        return ($this->_max_file_size);
    }
    public function uploadFile          ($file)
    {
        /* Probably going to be most used function - saves file on server
           Checks if file fits, uploaded ok, and is an allowable file format.
                Returns:
                  array(
                    path -      absolute path to file
                    mime -      mime of file
                    )
        */

        $hash=hash_file("sha256", $_FILES[$file]['tmp_name']);
        if ($this->fits($file) && $this->upload_Ok($file) && in_array($this->_mime($file), $this->_allowed_mimes))
        {
            if (move_uploaded_file($_FILES[$file]['tmp_name'], $this->_upload_path . $hash))
            {
                return array('path' => $this->_upload_path . $hash, 'mime' => $mime);
            }
            
        }
        unlink($_FILES[$file]['tmp_name'];
        return False;
    }
    public function upload_Ok           ($file)
    {
        /* If file uploaded with no errors */

        if ($_FILES[$file]['error'] === ERR_UPLOAD_OK)
        {
            return True;
        }
        else
        {
            return False;
        }
    }
    protected function _mime            ($file)
    {
        /* Retrieves file format of uploaded file.
           This function requires either the finfo extension or, in a really bad case, a *nix machine.
           Returns mime of file or false if failed
        */

        if ($finfo = finfo_open(FILEINFO_MIME))
        {
            $mime = finfo_file($finfo, $_FILES[$file]['tmp_name']);
        }
        else 
        /*
            //I've read that forking is shitty and I don't like executing commands like this, but it should work

            $path = escapeshellarg( $_FILES[$file]['tmp_name'] );
            $mime = system("file -bi " . $path);
        */
        {
            //Without using the finfo extension...we assume it is an image
            if ($info = getimagesize($_FILES[$file]['tmp_name']))
            {
                $mime=$info['mime'];
            }
            else
            {
                return False;
            }
        }
        return ($mime);            
    }
       
}           
?>
