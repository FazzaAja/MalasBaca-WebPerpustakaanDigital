<?php
session_start();

// Generate random CAPTCHA code (6 characters)
$captcha_code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
$_SESSION['captcha'] = $captcha_code;

// Create image with GD
$width = 180;
$height = 60;
$image = imagecreatetruecolor($width, $height);

// Colors
$bg_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
$line_color = imagecolorallocate($image, 64, 64, 64);
$noise_color = imagecolorallocate($image, 100, 120, 180);

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Add noise (dots)
for ($i = 0; $i < 50; $i++) {
    imagesetpixel($image, rand(0, $width), rand(0, $height), $noise_color);
}

// Add random lines for distortion
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
}

// Draw CAPTCHA text with distortion
$font_size = 20;
$x = 10;
for ($i = 0; $i < strlen($captcha_code); $i++) {
    $angle = rand(-15, 15);
    $y = rand(35, 45);
    $x += rand(22, 28);
    
    // Use built-in font (font 5 is the largest built-in)
    // For better results, you can use TTF fonts with imagettftext()
    imagestring($image, 5, $x, $y + rand(-5, 5), $captcha_code[$i], $text_color);
}

// Add more distortion lines on top
for ($i = 0; $i < 3; $i++) {
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
}

// Output image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>
