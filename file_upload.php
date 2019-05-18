<?php 
use aks\UploadFile;
session_start();
require_once 'src/aks/UploadFile.php';
if(!isset($_SESSION['maxfiles']))
{
    $_SESSION['maxfiles'] = ini_get('max_file_uploads');
    $_SESSION['postmax'] = UploadFile::convertToBytes(ini_get('post_max_size'));
    $_SESSION['displaymax'] = UploadFile::convertFromBytes($_SESSION['postmax'] );
}
$max = 500 * 1024; // 500kb , our maximum file size value when using setMaxSize() method ... for each file
$message = array();
if(isset($_POST['submit']))
{    
    
    $destination = __DIR__ . '/uploaded/';
    
    try{
        $upload = new UploadFile($destination);

        $upload->setMaxSize($max); // set maximum file size that can be uploaded
       // $upload->allowAllTypes(); // set allow all types to false or unused method. you can add suffix as a string argument..
        $upload->upload(true); // renameDuplicate file is set to true , if u dont want to rename with suffix , u can set it to false..
        $message = $upload->getMessages();
    }
    catch(Exception $e)
    {
        $message[] = $e->getMessage() . "<br>";
    }
    
}
$error = error_get_last();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Upload </title>
</head>
<body>
   <?php
    if($message || $error)
    {
        ?>
  <ul>  
      
  <?php 
         if($error){
  echo "<li>{$error['message']}</li>";
  }
        if($message)
        {
        foreach($message as $error_code)
    {
        echo "<li>$error_code</li>";
    }
        }
    
    ?>
    </ul>
    <?php } ?>
    <label for="avatar">Choose a File To Upload</label>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">
        <input type="file" id="avatar" name="avatar[]" 
        data-maxfiles = "<?php echo $_SESSION['maxfiles'] ?>"
        data-postmax = "<?php echo $_SESSION['postmax'] ?>"
        data-displaymax = "<?php echo $_SESSION['displaymax'] ?>"
        accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,.png, .jpg, .jpeg,image/*" 
        multiple>
       <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max; ?>" >
       <input type="submit" name="submit" value="Upload">
    </form>
</body>
</html>
