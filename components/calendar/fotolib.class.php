<?php
/*
FOTOLIB - концепция обработки фоток в компонентах. НЕзавершённое решение и вобше его бы выгнать в отдельный компонент.
в модель передаётся 2 параметра ТИП и ID галеери. Выдаётся масив. 
Путь к фотографиям считаем так:
$ROOT/upload/userfiles/fotolib/$Размер_$MD5оригинала.jpg
Размер может быть:
S - превью 
L - размер для показа
оришинал - без префикса размера.
AND $place['type_id'] != 1 - Запрещаем добавлять фотографии к точкам пользователей - надо будет в базу внести но пока пусть будет так :)
*/

class FotoLib
{
  function __construct()
  {
    $this->inDB = cmsDatabase::getInstance();
    $this->inCore = cmsCore::getInstance();
    $this->inPage = cmsPage::getInstance();
    $this->inUser = cmsUser::getInstance();
    $this->root = $_SERVER['DOCUMENT_ROOT'];
  }
  //Возвращает массив всех фоток
  public function loadImages($type, $id)
  {
    $sql = "SELECT * FROM cms_fotolib WHERE type = '$type' AND photo_id = $id";
    $result = $this->inDB->query($sql);
    if ($this->inDB->error())
    {
      return false;
    }
      
    $images = array();
    while ($image = $this->inDB->fetch_assoc($result))
    {
      $images[] = $image;
    }
    return $images;
  }
  
  public function addAcces($type,$event)
  {
    $cfg = $this->inCore->loadComponentConfig($type);
    $allow_add_foto = FALSE; //Ставим изначально запрет на добавление
//Если мы вошли и всем разрешено добавлять фото    
    if($this->inUser->id != 0 AND $cfg['calendar_image_acces'] == "all") 
    {
      $allow_add_foto = TRUE;
    }
//Если мы автор и разрешено добавлять только авторам
    if($event['author_id'] == $this->inUser->id AND $cfg['calendar_image_acces'] == "author")
    {
      $allow_add_foto = TRUE;
    }
//Если разрешено добавлять только админам
    if($this->inUser->is_admin AND $cfg['calendar_image_acces'] == "admin")
    {
      $allow_add_foto = TRUE;
    }
    return $allow_add_foto;
  }
  
  public function uploadFoto($files, $type, $photo_id)
  {
    foreach($files as $file)
    {
      if(mb_ereg("image",$file['type']))
      {
	$md5 = md5_file($file['tmp_name']);
	if(!file_exists($this->root."/upload/userfiles/$md5.jpg"))
	{
	  copy($file['tmp_name'],$this->root."/upload/userfiles/$md5.jpg");
	  $this->Resize($this->root."/upload/userfiles/$md5.jpg",$this->root."/upload/userfiles/S_$md5.jpg",640, 480);
	  $this->Resize($this->root."/upload/userfiles/$md5.jpg",$this->root."/upload/userfiles/L_$md5.jpg",128, 128);
	  $user_id = $this->inUser->id;
	  $time = time();
	  if(file_exists($this->root."/upload/userfiles/L_$md5.jpg") AND file_exists($this->root."/upload/userfiles/L_$md5.jpg"))
	  {
	    $sql = "INSERT INTO cms_fotolib (`user_id`, `type`, `photo_id`, `name`, `time`) 
				   VALUES ('$user_id', '$type', '$photo_id', '$md5', '$time')";
	    $result = $this->inDB->query($sql);
	  }
	  else
	  {
	    unlink($this->root."/upload/userfiles/$md5.jpg");
	    cmsCore::addSessionMessage('Ошибка добавления!', 'error');
	  }
	}
      }
    }
  }
  
  public function getImage($name)
  {
    $sql = "SELECT * FROM cms_fotolib WHERE name = '$name' LIMIT 1";
    $result = $this->inDB->query($sql);
    
    if ($this->inDB->error())
    {
      return false;
    }
    else
    {
      return $this->inDB->fetch_assoc($result);
    }
  }
  
  public function Rotate($side, $image_id)
  {
    if($side == "left")
    {
      $deg = "90";
    }
    
    if($side == "right")
    {
      $deg = "-90";
    }
    
    $sql = "SELECT * FROM cms_fotolib WHERE id = $image_id";
    $result = $this->inDB->query($sql);
    
    $image = $this->inDB->fetch_assoc($result);
    if($this->inUser->id == $image['user_id'] OR $this->inUser->is_admin)
    {
      $this->RotateImg($this->root."/upload/userfiles/fotolib/".$image['name'].".jpg", $deg);
      $md5 = md5_file($this->root."/upload/userfiles/fotolib/".$image['name'].".jpg");
    
      if(copy($this->root."/upload/userfiles/fotolib/".$image['name'].".jpg", $this->root."/upload/userfiles/fotolib/".$md5.".jpg"))
      {
	unlink($this->root."/upload/userfiles/fotolib/".$image['name'].".jpg");
	unlink($this->root."/upload/userfiles/fotolib/S_".$image['name'].".jpg");
	unlink($this->root."/upload/userfiles/fotolib/L_".$image['name'].".jpg");
	$this->Resize($this->root."/upload/userfiles/fotolib/$md5.jpg",$this->root."/upload/userfiles/fotolib/S_$md5.jpg",640, 480);
	$this->Resize($this->root."/upload/userfiles/fotolib/$md5.jpg",$this->root."/upload/userfiles/fotolib/L_$md5.jpg",128, 128);
	$sql = "UPDATE cms_fotolib SET `name` = '$md5' WHERE id = $image_id";
	$result = $this->inDB->query($sql);
      }
    }
    return;
  }
  
  public function Delete($image_id)
  {
    $sql = "SELECT * FROM cms_fotolib WHERE id = $image_id";
    $result = $this->inDB->query($sql);

    $image = $this->inDB->fetch_assoc($result);

    if($this->inUser->id == $image['user_id'] OR $this->inUser->is_admin)
    {
      unlink($this->root."/upload/userfiles/fotolib/".$image['name'].".jpg");
      unlink($this->root."/upload/userfiles/fotolib/S_".$image['name'].".jpg");
      unlink($this->root."/upload/userfiles/fotolib/L_".$image['name'].".jpg");
      $sql = "DELETE FROM cms_fotolib WHERE name = '".$image['name']."' LIMIT 1";
      $result = $this->inDB->query($sql);
    }
    return;
  }
  
  private function Resize($infile,$outfile,$max_x, $max_y)
  {
    list($width, $height, $t, $attr) = getimagesize($infile);
    if ($t == IMAGETYPE_GIF)
    {
      $im=imagecreatefromgif($infile);
    }
    else if ($t == IMAGETYPE_JPEG)
    {
      $im=imagecreatefromjpeg($infile);
    }
    else if ($t == IMAGETYPE_PNG)
    {
      $im=imagecreatefrompng($infile);
    }
    else
    {
      return;    
    }
  
    if ($width > $max_x or $height > $max_y)
    {
      if ($width >= $height)
      {
	$k=$width/$max_x;
      }
      else
      {
	$k=$height/$max_y;
      }
    
      $width = round($width/$k);
      $height = round($height/$k);
    }

    $im1=imagecreatetruecolor($width,$height);
    imagecopyresampled($im1,$im,0,0,0,0,$width,$height,imagesx($im),imagesy($im));

    imagejpeg($im1,$outfile,100);
    imagedestroy($im);
    imagedestroy($im1);
  }
  
  private function RotateImg($file,$deg)
  {
    $source = imagecreatefromjpeg($file);
    $rotate = imagerotate($source, $deg, 0);
    imagejpeg($rotate,$file,100);
    
    imagedestroy($source);
    imagedestroy($rotate);
  }
}
?>