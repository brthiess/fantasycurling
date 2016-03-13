<?php 

class ImageUpload {
	function __construct(){
		ini_set('memory_limit','200M');
	}
	/**
      * 
      * Loads an image into memory
      *
      * @param string $filename  The path to the image
	  *
      * @return boolean  whether it was able to load the file or not
      */	
	public function load($filename) {
		$this->filename = $filename;
		if(file_exists($filename)) {
			$image_info = getimagesize($filename);
			$this->image_type = $image_info[2];
			$this->image_tmp = $filename;
			if($this->image_type == IMAGETYPE_JPEG) {
	 			$this->image = imagecreatefromjpeg($filename);
	 			$this->default_image_save_type = IMAGETYPE_JPEG;
			} else if($this->image_type == IMAGETYPE_GIF) {
	 			$this->image = imagecreatefromgif($filename);
	 			$this->default_image_save_type = IMAGETYPE_GIF;
			} else if($this->image_type == IMAGETYPE_PNG) {
		        $this->image = imagecreatefrompng($filename);
		        imagealphablending($this->image, false);
		        imagesavealpha($this->image, true);
		        $this->default_image_save_type = IMAGETYPE_PNG;
			} else {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}
	/**
      * 
      * Determines if the image is a valid image, and is under the maximum size limits
      *
      * @param integer $maxsize  The maximum file size of the image
      * @param string $type  The desired image type (PHP mime type)
	  *
      * @return string|boolean  error on fail, true on success
      */	
	public function valid_image($maxsize=2097152,$type=IMAGETYPE_JPEG){
		if ($this->image_type!=$type){
			return 'type error';
		}else if (filesize>$maxsize){
			return 'size error';
		}else{
			return true;
		}
	}
	/**
      * 
      * Saves the image
      *
      * @param string $dir  The folder to save the image to
      * @param string $filename  The desired filename
      * @param boolean $overwrite  Whether or not to overwrite the file is duplicate detected
      *	@param string $image_type  could be PHP mime type, or string. Will refactor image to correct type.
      *	@param integer $compression  Desired compression of a JPG image. 
      * @param integer $permissions  Desired permissions on the file
	  *
      * @return string|boolean  error on fail, true on success
      */
	public function save($dir, $filename, $overwrite=true, $image_type='', $compression=100, $permissions=null) {
		
		// Create a new UNIQUE name
		if(file_exists($dir.$filename) && !$overwrite) {
			$file_extension = substr($filename, strrpos($filename, '.'));
			$filename = str_replace($file_extension, '', $filename) . '_' . microtime() . $file_extension;
		}
		if($image_type == '') { 
			$image_type = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
		 }
		
		// Save the image with the specific format
		if($image_type == IMAGETYPE_JPEG || $image_type=='jpg') {
			imagejpeg($this->image,$dir.$filename,$compression);
		} else if($image_type == IMAGETYPE_GIF  || $image_type== 'gif') {
			imagegif($this->image,$dir.$filename);
		} else if($image_type == IMAGETYPE_PNG  || $image_type== 'png') {
			imagepng($this->image,$dir.$filename);
		}
		if($permissions != null) {
			chmod($dir.$filename,$permissions);
		}
		
		// Return the image name that was saved
		return $filename;
  	}
	
  	/**
      * 
      * Outputs the image directly to the buffer
      *
      * @param string $image_type  The image type of the image. PHP mime type or string extension
      * @param bool $headers  Whether to set correct headers here, or to exlude them.
	  *
      * @return null  but will output the image to the buffer
      */
	public function output($image_type=IMAGETYPE_JPEG,$headers=false) {
		
		if($image_type == IMAGETYPE_JPEG || $image_type=='jpg') {
			if($headers)
				header('Content-Type: image/jpeg');
				
			imagejpeg($this->image);
		} else if($image_type == IMAGETYPE_GIF ||  $image_type=='gif') {
			if($headers)
				header('Content-Type: image/gif');
				
			imagegif($this->image);
		} else if($image_type == IMAGETYPE_PNG || $image_type=='png') {
			if($headers)
				header('Content-Type: image/png');
				
			imagepng($this->image);
		}
	}
	
	/**
      * 
      * Gets the width of the loaded image
      *
      * @return int
      */
	public function getWidth() {
      	return imagesx($this->image);
	}
	
	/**
      * 
      * Gets the height of the loaded image
	  *
      * @return int
      */
	public function getHeight() {
		return imagesy($this->image);
	}
	
	/**
      * 
      * Gets the filesize of the loaded image
      *
      * @return int
      */
	public function getBytes() {
		return filesize($this->image_tmp);
	}
	/**
      * 
      * resizes the image to a specific height
      *
      * @param int $height  the desired height
      *
      * @return null
      */
	public function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}
	
 	/**
      * 
      * resizes the image to a specific width
      *
      * @param int $width  the desired width
      *
      * @return null
      */
	public function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}
	
 	/**
      * 
      * scales the image to a specific ratio
      *
      * @param int $scale  the desired scale
      *
      * @return null
      */
	public function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}

	/**
      * 
      * resizes the image to ensure it at least fills a certain box.
      *
      * @param int $w  the width of the box to fill
      * @param int $h  the height of the box to fill
      *
      * @return null
      */
	public function fill($w,$h) {
		$dims = $this->dynamicScaleToFill($w,$h);
		$this->resize($dims['w'],$dims['h']);
	}
	
	/**
      * 
      * Will determine the dimensions to at least fill a certain box, but wont resize the image. 
      *     Will pass back the required dimensions and offsets to center the image
      *
      * @param int $w  the width of the box to fill
      * @param int $h  the height of the box to fill
      *	@param string $file  file to dynamically scale to fill. If empty, will use the currently loaded file.
      *
      * @return array  w=> new width, h=> new height, offset_w => horizontal offset to center in box, offset_h => vertical offset to center in box
      */
	public function dynamicScaleToFill($w,$h, $file='') {
		if($file=='') {
			$img_w = $this->getWidth();
			$img_h = $this->getHeight();
		} else {
			$dims = getimagesize($file);	
			$img_w = $dims[0];
			$img_h = $dims[1];
		}
		
		// Calculate new widths/heights
		if($img_w >= $img_h) {
			$ratio = $w/$img_w;
			$new_w = $w;
			$new_h = $img_h*$ratio;
			
			if($new_h < $h) {
				// New height is smaller than box allowance
				$ratio = $h/$img_h;
				$new_h = $h;
				$new_w = $img_w*$ratio;
			}
			
		} else if($img_h > $img_w) {
			$ratio = $h/$img_h;
			$new_h = $h;
			$new_w = $img_w*$ratio;
			
			if($new_w < $w) {
				// New width is smaller than box allowance	
				$ratio = $w/$img_w;
				$new_w = $w;
				$new_h = $img_h*$ratio;
			}
		}
		
		// Find offset numbers		
		return array('w'=>$new_w, 'h'=>$new_h, 'offset_w'=>(($new_w-$w)/2),'offset_h'=>(($new_h-$h)/2));
	}
	
	/**
      * 
      * Fits the image inside of a box
      *
      * @param int $w  the width of the box
      * @param int $h  the height of the box
      *
      * @return null
      */
	public function fit($w,$h) {
		if($this->getWidth() > $h) {
			$this->resizeToWidth($w);	
			if($this->getHeight() > $h) {
				$this->resizeToHeight($h);	
			}
		} else {
			$this->resizeToHeight($h);
			if($this->getWidth() > $w) {
				$this->resizeToWidth($w);	
			}
		}

	}
	
	/**
      * 
      * Determines the smallest size possible while maintaining aspect ratio to fill a certain box. It centers the image inside that box, and crops the image accordingly.
      *
      * @param int $w  the width of the box
      * @param int $h  the height of the box
      *
      * @return null
      */
	public function smartCrop($w,$h) {
		// Shrinks image, but ensures box is filled.
		$this->fill($w,$h);
		$woffset = ($this->getWidth()-$w)/2;
		$hoffset = ($this->getHeight()-$h)/2;
		
		if($this->getWidth()>$w) {
			// Width larger
			$this->crop($woffset,0,($this->getWidth()-$woffset),$this->getHeight());
		} else if($this->getHeight()>$h) {
			$this->crop(0,$hoffset,$this->getWidth(),($this->getHeight()-$hoffset));
		}
	}
	
	/**
      * 
      * Crops the image at the specified coordinates
      *
      * @param int $x 
      * @param int $y
      * @param int $x2
      * @param int $y2
      *
      * @return null
      */
	public function crop($x,$y,$x2,$y2) {
	
		// Get crop width;
		$w = $x2 - $x;
		if($x2 < $x)
			$w = $x-$x2;
			
		// Get crop height;
		$h = $y2 - $y;
		if($y2 < $y)
			$h = $y-$y2;
		
		$new_image = imagecreatetruecolor($w,$h);
		if($this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG) {
			$current_transparent = imagecolortransparent($this->image);
			if($current_transparent != -1) {
				$transparent_color = imagecolorforindex($this->image,$current_transparent);
				$current_transparent = imagecolorallocate($new_image,$transparent_color['red'],$transparent_color['green'], $transparent_color['blue']);
				imagefill($new_image,($x*-1),($y*-1),$current_transparent);
			} else if($this->image_type == IMAGETYPE_PNG) {
				imagealphablending($new_image,false);
				$color = imagecolorallocatealpha($new_image,0,0,0,127);
				imagefill($new_image,($x*-1),($y*-1),$color);
				imagesavealpha($new_image,true);
			}
				
		}		
		imagecopyresampled($new_image,$this->image,0,0,$x,$y,$w,$h,$w,$h);
		$this->image = $new_image;													 
	}	
	
	/**
      * 
      * Simple resize of the image
      *
      * @param int $width
      * @param int $height
      *
      * @return null
      */
	public function resize($width,$height) {
		$new_image = imagecreatetruecolor($width, $height);
		if($this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG) {
			$current_transparent = imagecolortransparent($this->image);
			if($current_transparent != -1) {
				$transparent_color = imagecolorsforindex($this->image, $current_transparent);
				$current_transparent = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
				imagefill($new_image, 0, 0, $current_transparent);
				imagecolortransparent($new_image, $current_transparent);
			} else if($this->image_type == IMAGETYPE_PNG) {
				imagealphablending($new_image, false);
				$color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
				imagefill($new_image, 0, 0, $color);
				imagesavealpha($new_image, true);
			}
		}
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;	
	}
}

?>