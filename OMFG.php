<?php
/*
	 O. M. F. G. !
	 One Main File Gallery (or not)
	 
	 Copyright ©2013 Justin Pearce (whitefox@guardianfox.net)
	 This program is free software: you can redistribute it and/or modify
	 it under the terms of the GNU Affero General Public License as published by
	 the Free Software Foundation, either version 3 of the License, or
	 (at your option) any later version.

	 This program is distributed in the hope that it will be useful,
	 but WITHOUT ANY WARRANTY; without even the implied warranty of
	 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 GNU Affero General Public License for more details.

	 You should have received a copy of the GNU Affero General Public License
	 along with this program.  If not, see <http://www.gnu.org/licenses/>.         
	 
	 ...that said, if you like this send some thanks (money or beer works)! :D
	 
	 Description:
	 This script is an adaptation (read: rewrite) of the Descending Explorer script (by the same author)
	 to make the script work in a more OOP fashion and provide a more configurable, customizable, 
	 and efficient script. The goal is to provide a simple to use gallery/file list that can be dropped 
	 into a directory tree and just work while being able to be customized to be significantly more efficient.
	 
*/

/*
 Class: omfgConfig
 This class provides a method for loading a configuration for the gallery system.
 configuration options are stored in a 'config.ini' file in a '.conf' subdirectory to the
 one that contains this script. If no such file exists, then load a hard-coded array of values.
*/
class omfgConfig{
	//Container for configuration values
    var $configuration;
	
	/* Default Constructor */
	function __construct(){
		//Initialize container
		$this->configuration = array();
		//Check for a configuration file
		if(file_exists(dirname(__FILE__).'/.conf/config.ini')){
		    //Load the configuration file and merge in any missing values into the configuration
			$this->configuration = parse_ini_file(dirname(__FILE__).'/.conf/config.ini', true)
			$this->mergeDefaultConfig();
		}else{
		    //No configuration exists, so initialize the config to default values.
			$this->configuration = $this->getDefaultConfig();
		}
	}

	/*
	  Function: getDefaultConfig
	  This function generates a configuration array containing the default configuration options
	  for the program.
	  @Args: None
	  @Returns: array Configuration
	*/
	private function getDefaultConfig(){
		//Pretty simple. Multidimensional array of configuration values gets returned
	    $defaultConfig = array();
		$defaultConfig['general'] = array('start_path'=>'.', 'disallowed'=>'php, php4, php5, phps',
										  'page_title'=>'OMFG!', 'files_label'=>'', 'folders_label'=>'');
		$defaultConfig['options'] = array('thumb_height'=>75, 'files_per_page'=>25, 'max_columns'=>3, 
										  'enable_ajax'=>false, 'memory_limit'=>'256M', 'cache_thumbs'=>false);
		return $defaultConfig;
	}
	
	/*
	  Function: mergeDefaultSettings
	  This function grabs a copy of the default configuration and compares it to the currently loaded configuration,
	  filling in missing values with their defaults. This way, if the user does something such as putting a blank
	  config.ini file in the configuration, the program will load the values it needs.
	  @Args: None
	  @Returns: None
	*/
	private function mergeDefaultSettings(){
		//Get a copy of the default configuration.
		$defaultConfig = $this->getDefaultConfig();
		//Check for a 'general' section
		if(array_key_exists('general', $this->configuration)){
			//If it exists, compare the default keys to the loaded config for missing values
			$missingGeneral = array_diff_key($defaultConfig['general'], $this->configuration['general']);
			//Iterate through the missing values and attach them to the running configuration.
			if(count($missingGeneral) > 0){
				foreach($missingGeneral as $key=>$parameter){
					$this->configuration['general'][$key] = $parameter;
				}
			}
		}else{
			//Section doesn't exist, so we need to set it to the defaults.
			$this->configuration['general'] = $defaultConfig['general'];
		}
		//Check for a 'options' section
		if(array_key_exists('options', $this->configuration)){
			//If it exists, compare the default keys to the loaded config for missing values
			$missingOptions = array_diff_key($defaultConfig['options'], $this->configuration['options']);
			//Iterate through the missing values and attach them to the running configuration.
			if(count($missionOptions) > 0){
				foreach($missingOptions as $key=>$parameter){
					$this->configuration['options'][$key] = $parameter;
				}
			}
		}else{
			//Section doesn't exist, so we need to set it to the defaults.
			$this->configuration['options'] = $defaultConfig['options'];
		}
	}
	
	/* Default Destructor */
	function __destruct(){
		//Clean-up class variables.
		unset($this->configuration);
	}
} //End omfgConfig

/*
  Class: omfgJson
  This class provides methods for accessing server callbacks for new files, changed options, etc.
*/
class omfgJson{
	/* Default Constructor */
	function __construct(){
	}
	
	/* Default Destructor */
	function __destruct(){
	}
} //End omfgJson

/*
  Class: omfgThumbnail
  This class provides a method for generating thumbnails, preferably via Imagick, but falling back to GD
  if Imagick is not installed as a module. We try to use Imagick first because it handles images of a 
  variety of colour spaces, as opposed to GD and it's limitation to just RGB colour space. This class will 
  either generate image data and return it directly or it will generate a cached copy of the image for later 
  retrieval, depending on the value of enableCaching (defaults to false)
*/
class omfgThumbnail{
  //Boolean feature flags
	
	//Imagick library support
	var $imagickSupport;
	//ImageMagick command line support
	var $imagickCmdSupport;
	//Path to the ImageMagick command line programs
	var $imagickCmdPath;
	//GD library support
	var $gdSupport;
	//Create cached images?
	var $createCache;
	//Cache Directory
	var $cacheDir;
	//Thumbnail Height
	var $thumbHeight;
	
	/* Default Constructor */
	function __construct($thumbHeight, $cacheDir='', $enableCaching=false, $imagickPath=''){
		//Set the path to the command line programs for ImageMagick
		$this->imagickCmdPath = $imagickPath.'/';
		//Check what our options are 
		$this->imagickSupport = $this->checkImagickLibrary();
		$this->imagickCmdSupport = $this->checkImagickCmd();
		$this->gdSupport = $this->checkGDLibrary();
		
		//Set caching
		$this->createCache = $enableCaching;
		//Set cache directory
		$this->cacheDir = $cacheDir;
		//Set thumbnail height
		$this->thumbHeight = $thumbheight;
	}
	
/*
	Function: getThumbnail
	This function generates an image thumbnail and will return that thumbnail. If 
	caching is enabled, it will use the cached image instead of generating a new 
	thumbnail.
*/
	public function getThumbnail(){
		if($this->imagickSupport){
			//Generate thumbnail as Imagick library
		}else if($this->imagickCmdSupport){
			//Generate thumbnail as ImageMagick program
		}else if($this->gdSupport){
			//Generate thumbnail as GD library
		}else{
			/* Fail! */
		}
	}

/*
	Function: generateThumbnailImagick
	This function generates a thumbnail of the given image using the Imagick 
	library	and, if necessary, will	create a cached copy of the image.
	@Args:
	@Returns:
*/	
	private function generateThumbnailImagick(){
	}

/*
	Function: generateThumbnailCmd
	This function generates a thumbnail of the given image using the ImageMagick 
	command line programs and, if necessary, will	create a cached copy of the 
	image.
	@Args:
	@Returns:
*/
	private function generateThumbnailCmd(){
	}
	
/*
	Function: generateThumbnailGD
	This function generates a thumbnail of the given image using the GD library 
	and, if necessary, will	create a cached copy of the image.
	@Args:
	@Returns:
*/
	private function generateThumbnailGD(){
		
	}
	
/*
	Function: checkImagickLibrary
	This function simply returns the results for whether or not the class for 
	Imagick exists.
	@Args: None
	@Returns: boolean Imagick Exists
*/	
	private function checkImagickLibrary(){
		//Check for Imagick support
		return class_exists('Imagick');
	}

/*
	Function: checkImagickCmd
	This function first attempts to determine if we can execute external commands 
	before moving on to determine if the ImageMagick program is installed.
	@Args: None
	@Returns: boolean ImageMagick Exists
*/
	private function checkImagickCmd(){
		//First, we need to see if we can run anything externally.
		if($this->execCheck()){
			 $output='';
			 $returnCode = -1;
			 //Execute 'convert -version' to see if it does anything.	
			 exec($this->imagickCmdPath."convert -version", $output, $returnCode);
			 if(intval($returnCode) == 0){
			 		//Return code 0 means the file exists and works
			 		return true;
			 }else{
			 		//Anything else means there is no program there
			 	  return false;
			 }
		}else{
			//Since we can't execute things on the commant line with exec(), we can't 
			//use this method.
			return false;
		}
	}

/*
	Function: checkGDLibrary
	This program simply returns the results of whether the GD extension is loaded 
	by PHP.
	@Args: None
	@Returns: boolean GD Exists
*/	
	private function checkGDLibrary(){
	//Check for GD support
		return extension_loaded('gd');
	}
	
/*
	Function: execCheck
	This function was copied from an answer on Stack Overflow 
	(http://stackoverflow.com/a/3938155/557748)because it is an elegant solution 
	to the problem of checking to see if we can use exec() in	PHP.
	@Args: None
	@Returns: boolean The Function does/does not exist. 
*/
	private function execCheck() {
	  $disabled = explode(', ', ini_get('disable_functions'));
	  return !in_array('exec', $disabled);
	}
	
	/* Default Destructor*/
	function __destruct(){
	}
} //End omfgThumbnail
?>