<?php

namespace aks;

class UploadFile{
    
   protected $destination;
   protected $message = array();  
   protected $maxSize = 50 * 1024;     //default 50kb maxsize value... UploadFile Class member
   protected $permitted_types = array(
     "image/jpeg","image/png","image/gif","image/pjpeg","image/webp"
     );  
   protected $new_file_name;
   protected $typeCheckingOn = true;
   protected $notTrusted = array("bin","cgi","exe","js","pl","php","py","sh","bat","html","png");
   protected $suffix = '.upload';
   protected $renameDuplicates; 
   public function __construct($uploadFolder)
     {
      if(!is_dir($uploadFolder) || !is_writable($uploadFolder))
      {
      throw new \Exception("$uploadFolder must be a valid , writable folder ... ");
      }
      if($uploadFolder[strlen($uploadFolder)-1] != '/')
      {
       $uploadFolder .= '/'; 
      }
      $this->destination = $uploadFolder;

     }
    
    public function setMaxSize($bytes)
    {

     $serverMax = self::convertToBytes(ini_get('upload_max_filesize'));

          if($bytes >  $serverMax)
          {
              throw new \Exception("Maximum size cannot exceed server limit for individual files : " . self::convertFromBytes($serverMax));
          }
          if(is_numeric($bytes) && $bytes > 0) // bytes are number and is positive
          {
              $this->maxSize = $bytes;
          }
    }
    
    public static function convertToBytes($bytes)
    {
       
    $bytes = trim($bytes);
      
    $last = strtolower($bytes[strlen($bytes)-1]);
        
    if(in_array($last,array('g','m','k')))
                    {
    switch($last)
            {
    case 'g' :
    $bytes *= 1024;
           
    case 'm' :
           
    $bytes *= 1024;
           
    case 'k' :
    $bytes *= 1024;
           
            }
                    }
       
    return $bytes;
    }

    public static function convertFromBytes($bytes)
    {
    $bytes = $bytes/1024;
    if($bytes > 1024)
    {
    return number_format($bytes/1024, 1) . ' MB';
    }
    else
    {
    return number_format($bytes, 1) . ' KB';
    }
    }
    
    
    
    public function allowAllTypes($suffix = null)
    {
        $this->typeCheckingOn = false;
        if(!is_null($suffix))
        {
            if(strpos($suffix,'.') === 0 || $suffix == '')
            {
              $this->suffix = $suffix;  
            }
            else
            {
                $this->suffix = '.' . $suffix;
            }
        }
    }
    
    public function upload($renameDuplicates = true)
    {
        $this->renameDuplicates = $renameDuplicates;
        $uploaded = current($_FILES); // single argument array ..
   /* It is used to return the value of the element in an array which the internal pointer is currently pointing to.
    The current() function does not increment or decrement the internal pointer after returning the value.
    In PHP, all arrays have an internal pointer. This internal pointer points to some element in that array which is called as the current element of the array.
    Usually, the current element is the first inserted element in the array.*/
        if(is_array($uploaded['name']))
        {
            foreach($uploaded['name'] as $key => $value)
            {
                $currentFile['name'] = $uploaded['name'][$key];
                $currentFile['type'] = $uploaded['type'][$key];
                $currentFile['tmp_name'] = $uploaded['tmp_name'][$key];
                $currentFile['error'] = $uploaded['error'][$key];
                $currentFile['size'] = $uploaded['size'][$key];
                
        if($this->checkFile($currentFile))
        {
             $this->moveFile($currentFile);
        }
            }
       
            
        }
       else
       {

        if($this->checkFile($uploaded))
        {
             $this->moveFile($uploaded);
        }

       } 
    }
    
    protected function checkFile($uploaded)
    {
        if($uploaded["error"] != 0)
        {
            $this->getErrorMessage($uploaded);
            return false;
        }
        if(!$this->checkSize($uploaded))
        {
            return false;
        }
        if($this->typeCheckingOn)
        {
            if(!$this->checkType($uploaded))
            {
                return false;
            }
        }
        $this->check_name($uploaded);
        return true;
    }
    
    protected function getErrorMessage($uploaded)
    {
        switch($uploaded["error"]) // same as $_FILES array but not superglobal.
    {
        case 2 :
        $this->messages[] = $uploaded["name"] . ' is too big to upload MAX : ' . self::covertFromBytes($this->maxSize).'<br>'; // exceeds limit of file upload , so it cannit be uploaded...
             break;
        case 3 :
        $this->messages[] = $uploaded["name"] . ' is partially uploaded' . '<br>';
            break;
        case 4 :
        $this->messages[] = ' No files Selected' . '<br>';
            break;
        default :
        $this->messages[] = ' There was problem uploading the file' . $uploaded['name'] . '<br>' ;
            break;
    }
        
    }
    
    protected function checkSize($uploaded)
    {
        if($uploaded['size'] == 0)
        {
            $this->messages[] = $uploaded["name"] . ' is empty' . '<br>';
            return false;
        }
        elseif($uploaded['size'] > $this->maxSize)
        {
            $this->messages[] = $uploaded["name"] . ' is too big , limit exceeds '. self::convertFromBytes($this->maxSize) . '<br>';
            return false;
        }
        else
        {
            return true;
        }
    }
    
    protected function checkType($uploaded)
    {
        if(in_array($uploaded['type'],$this->permitted_types))
        {
            return true;
        }
        else
        {
            $this->messages[] = $uploaded['name'] . " is not a permitted file type to upload ..";
            return false;
        }
    }
    
    protected function check_name($uploaded)
    {
        $this->new_file_name = null; // i will add strlen($uploaded['name']) file name to big condition
        $no_spaces = str_replace(" ","_",$uploaded['name']);
        if($no_spaces != $uploaded['name'])
        {
            $this->new_file_name = $no_spaces;
        }
        $name_parts = pathinfo($no_spaces);
        $extension = isset($name_parts['extension']) ? $name_parts['extension'] : '';
        if(!$this->typeCheckingOn && !empty($this->suffix))
        {
            if(in_array($extension,$this->notTrusted) || empty($extension))
            {
                $this->new_file_name = $no_spaces . $this->suffix;
            }
        }
            if($this->renameDuplicates)
            {
              $name = isset($this->new_file_name) ? $this->new_file_name : $uploaded['name'];
                $existing = scandir($this->destination);
                if(in_array($name,$existing))
                {
                    $i = 1;
                    do{
                        $this->new_file_name = $name_parts['filename'] . '_' . $i++ ;
                        if(!empty($extension))
                        {
                           $this->new_file_name .= ".$extension"; 
                        }
                        if(in_array($extension,$this->notTrusted))
                        {
                           $this->new_file_name .= $this->suffix; 
                        }
                      }
                    while(in_array($this->new_file_name,$existing));
                
                }
        }
    }

    public function getMessages()
    {
        return $this->messages;
    }
    
    protected function moveFile($uploaded)
    {
        $filename = isset($this->new_file_name) ? $this->new_file_name : $uploaded['name'];
        $success = move_uploaded_file($uploaded['tmp_name'],$this->destination . $filename );
        if($success)
        {
        $result = $uploaded["name"] . ' was uploaded Sucessfully' . '<br>';
        if($this->new_file_name != null)
        {
            $result .= ' and was renamed ' . $this->new_file_name; 
        }
        $result .= '.';
        
        $this->messages[] = $result;
        }
        else
        {
            $this->messages[] = 'Could not Upload' . $uploaded['name'];
        }
    }
    
    }

?>
