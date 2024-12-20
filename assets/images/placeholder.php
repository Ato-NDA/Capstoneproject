<?php
// Set the content type header to image/png
header('Content-Type: image/png');

// Create a new image
$width = 400;
$height = 300;
$image = imagecreatetruecolor($width, $height);

// Define colors
$bg = imagecolorallocate($image, 240, 240, 240);
$text_color = imagecolorallocate($image, 139, 69, 19);
$border_color = imagecolorallocate($image, 255, 182, 193);

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bg);

// Add border
imagesetthickness($image, 5);
imagerectangle($image, 0, 0, $width-1, $height-1, $border_color);

// Add text
$text = "Camera Image";
$font_size = 5;
$text_box = imagettfbbox($font_size, 0, 'arial.ttf', $text);
$text_width = abs($text_box[4] - $text_box[0]);
$text_height = abs($text_box[5] - $text_box[1]);
$x = ($width - $text_width) / 2;
$y = ($height - $text_height) / 2;
imagestring($image, $font_size, $x, $y, $text, $text_color);

// Output the image
imagepng($image);
imagedestroy($image);
?>
