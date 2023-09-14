<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  require('db.php');
  require('functions.php');

  $data = json_decode(file_get_contents('php://input'));
  $imgData = $data->{'imgData'};
  if($imgData){
    $tempFile_ = "temp/" . md5($imgData);
    file_put_contents($tempFile_, base64_decode($imgData));
    $name_ = "imgDataFile.png";
    $type_ = "image/png";
    $shortName=getNewName($name_, $type_);
    $fileName='uploads/'.$shortName.suffix($type_);
    rename(getcwd() . "/$tempFile_", $fileName);
    
    $id=alphaToDec($shortName);
    $hash=hash_file("md5",$fileName);
    $sql="SELECT base FROM images WHERE hash=\"$hash\"";
    $res=$link->query($sql);
    if(mysqli_num_rows($res)){
      $row=mysqli_fetch_assoc($res);
      $base=$row['base'];
      unlink($fileName);
    }else{
      $base=$shortName;
      switch($_FILES['file']['type']){
        case "image/jpeg":
          //if(strpos(strtoupper($fileName, '.JFIF')) === false) stripEXIF($fileName);
        case "image/png":
        case "image/gif":
        case "image/bmp":
          makeImageThumb($fileName,$fileName.".jpg");
          break;
        case "video/mp4":
        case "video/webm":
        case "video/ogg":
          makeVideoThumb($fileName,$fileName.".jpg",$shortName);
          break;
      }
    }
    $url="";
    $artist="";
    $description="";
    if(isset($_POST['origin']))$origin=htmlspecialchars(mysqli_real_escape_string($link,$_POST['origin']), ENT_QUOTES, 'utf-8');
    if(isset($_POST['artist']))$artist=htmlspecialchars(mysqli_real_escape_string($link,$_POST['artist']), ENT_QUOTES, 'utf-8');
    if(isset($_POST['description']))$description=htmlspecialchars(mysqli_real_escape_string($link,$_POST['description']), ENT_QUOTES, 'utf-8');
    $sql="UPDATE images SET base = \"$base\", hash=\"$hash\", origin=\"$origin\", artist=\"$artist\", description=\"$description\", autodelete=0 WHERE id = $id";
    //mysqli_query($link, $sql);
    $link->query($sql);
    $url=prefix($shortName).$shortName;
    echo json_encode([true, $url]);
  }
?>