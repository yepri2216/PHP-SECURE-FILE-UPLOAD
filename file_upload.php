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
$max = 3072 * 1024; // 3mb , our max file size value when using setMaxSize() method ...
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
    <link rel="stylesheet" href="css/bootstrap.min.css" >
 <!--   <script src="js/jquery.min.js"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <script>
    $(document).ready(function(){
  $("#close").click(function(){
    $("#end").hide();
  });
     $("#drop_files").on('drop',function(e){
       e.preventDefault();
         var formData = new FormData();
         var files_list = e.originalEvent.dataTransfer.files;
         for(var i =0 ; i < files_list.length;i++)
             {
                 formData.append('file[]',files_list[i]);
             }
         alert(file[1]);
     });   
        $.ajax({
           url:"file_upload.php",
            method:"POST",
            data: formData,
            contentType:false,
            processData:false;
        });
    });
    
      
    </script>
    <style>
        #drop_files{
            cursor: url(cursor.png), auto;
        }
    </style>
</head>
<body>
  
  
  <div class="row">
        <div class="col-md-12  text-center">
        <h3 style=""><br><br>Drag and Drop File Upload ...</h3>
        </div>
    </div>
  
<div class="container" id="drop_files" style="margin-top:50px; padding:25px; border:5px dashed silver;">
   
    <div class="row" style="padding-top:25px;">
        <div class="col-md-3">
            
        </div>
        <div class="col-md-6 text-center" style="">
          
            <i class="fas fa-file-upload  fa-10x"></i>
           
        </div>
        <div class="col-md-3">
            
        </div>
    </div>
     <div class="row" style="padding-top:25px;" >
        <div class="col-md-3 col-xs-12">
            
        </div>
        <div class="col-md-6 col-xs-12 text-center" style="">
            <label for="avatar"><h3>Choose a File To Upload</h3></label><br>
            <form method="post"  action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data" id="form1">
          
           <input type="file" name="avatar[]" class="btn btn-primary" id = "avatar" style="font-size:20px;" 
data-maxfiles = "<?php echo $_SESSION['maxfiles'] ?>"
data-postmax = "<?php echo $_SESSION['postmax'] ?>"
data-displaymax = "<?php echo $_SESSION['displaymax'] ?>"
accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,.png, .jpg, .jpeg,image/*" 
multiple>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max; ?>" >
            <input type="submit" name="submit" class="btn btn-success" style="font-size:20px;" value="Upload">
               </form>
        </div>
        <div class="col-md-3">
                
        </div>
    </div>
    <div class="row" style="padding-top:25px;" >
        <div class="col-md-3">
            
        </div>
        <div class="col-md-6 text-center" style="">
            <p style="font-weight:bold; font-size:25px;"> or drag and drop them here </p>
        </div>
        <div class="col-md-3">
            
        </div>
    </div>
       <div class="row">
           <div class="col-md-12">
                <?php
    if($message || $error)
    {
        $li = count($error) ;
        $li2 = count($message);
        ?>
  <ul style="list-style:none;" class="list-group">  
      
  <?php 
         if($error){
           
  echo "<li class='list-group-item list-group-item-warning' style='font-weight:bold;' id='end'><img class='' src='error2.png'  id='close' alt=''>   <i style='color:lightgreen;' class='fas fa-file-upload fa-2x'></i> {$error['message']} </li>";
  }
        if($message)
        {
        foreach($message as $key => $error_code)
    {
        echo " <li class='list-group-item' style='font-weight:bold;' id='end'><img class='' src='error2.png'  id='close' alt=''>  <i style='color:lightgreen;' class='fas fa-file-upload fa-2x'></i> $error_code </li>";
    }
        }
    
    ?>
    </ul>
    <?php  } ?>
  
           </div>
       </div>
       
        </div>
 
</body>
</html>
