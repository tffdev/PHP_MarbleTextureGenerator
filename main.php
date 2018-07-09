<?php

/**
 * ~~~ Marble Generator ~~~
 * This was made to be a Twitter bot or something that
 * posts cool marble art. This was created using the following
 * resource as a reference. It was super useful, please check it out!
 * [https://lodev.org/cgtutor/randomnoise.html]
 * This program is licenced under MIT, etc.
 */

/**
 * The image will always be sqaure, 
 * set width and height with this constant! 
 */
const size = 300;

/* Main procedure */
function main() {
  /* Create image */
  $canvas = imagecreatetruecolor(size, size);

  $linesScale = 3;
  $noise = generateNoise(size*2, size*2);

  /* Generate the marble texture on the clean canvas */
  marblate($canvas, $noise);

  /* 
  Adds a little color, 
  remove if you want just black/white natural marble 
  */
  colorswap($canvas);
  
  /* 
  Uncomment the following code if you want this to be 
  hosted on a server, it'll return this image upon a request.
  `sudo php -S localhost:80`
  */  
  
  //header("Content-type: image/png");
  //imagepng($canvas);


  /* Output the image as a file */
  imagepng($canvas, 'output.png');
}

function colorswap($canvas) {
  switch (floor(rand(0,1.999))) {
    case 0:
      $c1 = array(rand(0,255),rand(0,255),rand(0,255));
      $c2 = array(255,255,255);
      break;
    case 1: 
      $c2 = array(rand(0,255),rand(0,255),rand(0,255));
      $c1 = array(255,255,255);
      break;
  }


  for ($y = 0; $y < size; $y++){
    for ($x = 0; $x < size; $x++){
      $rgb = imagecolorat($canvas, $x, $y);
      $r = ($rgb >> 16) & 0xFF;
      $g = ($rgb >> 8) & 0xFF;
      $b = $rgb & 0xFF;
      $avg = ($r+$g+$b)/3/255;

      $color = imagecolorallocate($canvas, 
        $avg*$c1[0] + (1-$avg)*$c2[0], 
        $avg*$c1[1] + (1-$avg)*$c2[1], 
        $avg*$c1[2] + (1-$avg)*$c2[2]);

      imagesetpixel($canvas, $x, $y, $color);
    }
  }
}

function generateNoise($noiseWidth, $noiseHeight)
{
  for ($y = 0; $y < $noiseWidth; $y++){
    for ($x = 0; $x < $noiseHeight; $x++){
      $noise[$y][$x] = (rand() % 32768) / 32768.0;
    }
  }
  return $noise;
}

function marblate($canvas, $noise) {
  $xPeriod = rand(-10,10);
  $yPeriod = rand(-10,10);
  $turbPower = rand(4,6); 
  $turbSize = rand(30,34);

  for ($y = 0; $y < size; $y++){
    for ($x = 0; $x < size; $x++){
      $xyValue = $x * $xPeriod / size + $y * $yPeriod / size + $turbPower * turbulence($x, $y, $turbSize, $noise) / 256;
      $sineValue = 256 * abs(sin($xyValue * 3.14159));
      $r = $g = $b = $sineValue;
      $color = imagecolorallocate($canvas, $r, $g, $b);
      imagesetpixel($canvas, $x, $y, $color);
    }
  }
}

function turbulence($x, $y, $size, $noise) {
  $value = 0;
  $initialSize = $size;
  while($size >= 1)
  {
    $value += smoothNoise($x / $size, $y / $size, $noise) * $size;
    $size /= 2.0;
  }
  return(128.0 * $value / $initialSize);
}

function smoothNoise($x, $y, $noise) {

   //get fractional part of x and y
   $fractX = $x - floor($x);
   $fractY = $y - floor($y);

   //wrap around
   $x1 = (floor($x) + size) % size;
   $y1 = (floor($y) + size) % size;

   //neighbor values
   $x2 = ($x1 + size - 1) % size;
   $y2 = ($y1 + size - 1) % size;

   //smooth the noise with bilinear interpolation
   $value = 0.0;
   $value += $fractX     * $fractY     * $noise[$y1][$x1];
   $value += (1 - $fractX) * $fractY     * $noise[$y1][$x2];
   $value += $fractX     * (1 - $fractY) * $noise[$y2][$x1];
   $value += (1 - $fractX) * (1 - $fractY) * $noise[$y2][$x2];

   return $value;
}

/* Lmao */
main();

?>