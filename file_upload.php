<?php 
use aks\UploadFile;
$max = 200 * 1024; // 200kb , our max file size value when using setMaxSize() method ...
$message = array();
if(isset($_POST['submit']))
{    
    require_once 'src/aks/UploadFile.php';
    $destination = __DIR__ . '/uploaded/';
    
    try{
        $upload = new UploadFile($destination);

        $upload->setMaxSize($max); // set maximum file size that can be uploaded
        //$upload->allowAllTypes();
        $upload->upload();
        $message = $upload->getMessages();
    }
    catch(Exception $e)
    {
        $message[] = $e->getMessage() . "<br>";
    }
    
    
//    echo __DIR__;
   /* switch($_FILES["avatar"]["error"])
    {
        case 0 :
            $result = move_uploaded_file($_FILES["avatar"]["tmp_name"],$destination . $_FILES["avatar"]["name"]);
            if($result){
            $message = $_FILES["avatar"]["name"] . ' was uploaded Sucessfully' . '<br>';
            }
            else{
                $message = ' There was problem uploading the file' . $_FILES["avatar"]["name"] . '<br>' ; 
            }
            break;
        case 2 :
             $message = $_FILES["avatar"]["name"] . ' is too big to upload' . '<br>';
             break;
        case 4 :
             $message = ' No files Selected' . '<br>';
            break;
        default :
               $message = ' There was problem uploading the file' . $_FILES["avatar"]["name"] . '<br>' ;
            break;
    }*/
//$file_name = $_FILES["avatar"]["name"];
//echo "FILE NAME : ".$file_name . "<br>";
//$file_type = $_FILES["avatar"]["type"];
//echo "FILE TYPE : ".$file_type . "<br>";
//$file_size = round($_FILES["avatar"]["size"]/1048576,2);
//echo "FILE SIZE : ".$file_size . " MB<br>";
//$file_dir = $_FILES["avatar"]["tmp_name"];
//echo "FILE TEMPORARY DIRECTORY : ".$file_dir . "<br>";
//$file_error = $_FILES["avatar"]["error"];
//echo "FILE ERROR : ".$file_error . "<br>";
//echo '<pre>';
  //  print_r($_FILES);
    //echo '</pre>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Upload </title>
</head>
<body>
   <?php
    if($message)
    {
    foreach($message as $error_code)
    {
        ?>
        <ul>
        <?php
        echo "<li>$error_code</li>";
    }
    }
    ?>
    </ul>
    
    <label for="avatar">Choose a File To Upload</label>
<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">
<input type="file"
id="avatar" name="avatar"
 accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,.png, .jpg, .jpeg,image/*"  multiple>
       <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max; ?>" >
       <input type="submit" name="submit" value="Upload">
    </form>
</body>
</html>