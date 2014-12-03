<?php
/**
 *
 * HTML5 Image uploader with Jcrop
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 */
//if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');
include('sessiontest.inc.php');
include('includes.inc.php'); 

function uploadImageFile() { // Note: GD library is required for this function

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $iWidth = $iHeight = 300; // desired image result dimensions
        $iJpgQuality = 90;

        if ($_FILES) {

            // if no errors and size less than 600kb
            if (! $_FILES['image_file']['error'] && $_FILES['image_file']['size'] < 250 * 1024) {
                if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {

                    // new unique filename
                    $sTempFileName = 'cache/' . md5(time().rand());

                    // move uploaded file into cache folder
                    move_uploaded_file($_FILES['image_file']['tmp_name'], $sTempFileName);

                    // change file permission to 644
                    @chmod($sTempFileName, 0644);

                    if (file_exists($sTempFileName) && filesize($sTempFileName) > 0) {
                        $aSize = getimagesize($sTempFileName); // try to obtain image info
                        if (!$aSize) {
                            @unlink($sTempFileName);
                            return;
                        }

                        // check for image type
                        switch($aSize[2]) {
                            case IMAGETYPE_JPEG:
                                $sExt = '.jpg';

                                // create a new image from file 
                                $vImg = @imagecreatefromjpeg($sTempFileName);
                                break;
                         /*   case IMAGETYPE_GIF:
                                $sExt = '.gif';

                                // create a new image from file 
                                $vImg = @imagecreatefromgif($sTempFileName);
                                break;*/
                            case IMAGETYPE_PNG:
                                $sExt = '.png';

                                // create a new image from file 
                                $vImg = @imagecreatefrompng($sTempFileName);
                                break;
                            default:
                                @unlink($sTempFileName);
                                return;
                        }

                        // create a new true color image
                        $vDstImg = @imagecreatetruecolor( $iWidth, $iHeight );

                        // copy and resize part of an image with resampling
                        imagecopyresampled($vDstImg, $vImg, 0, 0, (int)$_POST['x1'], (int)$_POST['y1'], $iWidth, $iHeight, (int)$_POST['w'], (int)$_POST['h']);

                        // define a result image filename
                        $sResultFileName = $sTempFileName . $sExt;

                        // output image to file
                        imagejpeg($vDstImg, $sResultFileName, $iJpgQuality);
                        @unlink($sTempFileName);

                        return $sResultFileName;
                    }
					else
						$fehler .= "size & is_uploaded_file false";
                }
				else
					$fehler .= "is_uploaded_file false";
            }
			else
				$fehler .= "size false";
        }
		else
			$fehler .= "_FILES false";		
    }
	else
		$fehler .= "_POST false";		
}

$sImage = uploadImageFile();

$abfrage = "UPDATE `local_users` SET `profilbild` = '".$sImage."' WHERE `user_id` =".$s_user_id." LIMIT 1 ;";	
//do query
$eintragen = mysql_query( $abfrage );
if (( $eintragen == true ) AND ($sImage<>""))
{
	header('Location: ./framework.php?id=profile&aktion=bearbeiten&msg=erfolg');
}
else
{
	$_SESSION["post_fehler"]='Bild ändern fehlgeschlagen. Bitte Support kontaktieren (Info: '.$fehler.')';
	header('Location: ./framework.php?id=profile&aktion=bearbeiten&msg=fehler');
}
