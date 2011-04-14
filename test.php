<?php



$blah = array(5 => 'ss', 10 => 'ss', 11 => 'ff', 1 => 'gjj', 'asdfs' => 'ss');

$string = array('asdf',444,33);

$merged = array_merge($blah, $string);
ksort($blah, SORT_NUMERIC);

echo '<pre>', var_dump($blah), '</pre>';

$bbb = 'sss';

echo $bbb['Type'];

switch ($bbb['Type'])
{
	
}

unset($lknlv);
unset($blah['hhhh']);

echo 'No output buffer<br /><br />';

ob_start();

?>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vel mauris in magna tempor accumsan eu id risus. Morbi ut nisl urna, at rhoncus urna. Ut sodales, mauris vehicula malesuada adipiscing, dui orci commodo erat, in adipiscing justo justo sit amet ante. In volutpat, leo id rutrum consectetur, turpis elit dapibus libero, sit amet imperdiet nisl erat a ipsum. Phasellus nisi orci, pulvinar sed blandit quis, faucibus nec elit. In tincidunt elementum odio a posuere. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla quis mi quis neque facilisis iaculis. Donec libero ante, scelerisque et eleifend in, feugiat vel nulla. Aliquam erat volutpat. Aliquam erat volutpat. Nunc non adipiscing elit. Nunc volutpat dictum quam. Sed urna velit, scelerisque ac dapibus ut, rhoncus vitae risus. Sed fringilla mollis nulla ac dignissim. Aenean tortor tortor, blandit quis facilisis ut, lacinia et mi. Maecenas nec ligula ut lectus dictum pellentesque. Nullam hendrerit commodo libero, id vulputate leo pulvinar nec. Ut nunc libero, rutrum ac egestas vel, mollis quis erat.</p>

<p>Aliquam erat volutpat. Donec mollis accumsan gravida. Phasellus facilisis varius pharetra. Nullam sit amet est et dolor vehicula varius. Maecenas ipsum sem, sagittis at sollicitudin et, condimentum eu nibh. Phasellus placerat lectus sit amet magna accumsan vitae dignissim erat commodo. Nam pulvinar adipiscing commodo. Nullam pretium, nisi in vehicula tincidunt, tortor sapien dictum nulla, eget condimentum turpis magna et est. Curabitur ut quam nibh, at sodales enim. Etiam ut quam sit amet magna cursus pellentesque sed a risus.</p>

<p>Duis eget nunc ultricies enim aliquam volutpat. Quisque auctor ultrices pellentesque. Cras tincidunt interdum condimentum. Nulla placerat hendrerit mattis. Praesent hendrerit, augue sit amet luctus interdum, tellus enim rutrum velit, at vehicula magna arcu in odio. Pellentesque scelerisque lacinia orci, eget semper odio accumsan in. Sed condimentum, metus a porta convallis, enim metus vestibulum nibh, eu congue urna augue sit amet arcu. Phasellus aliquet, erat imperdiet elementum malesuada, est mi sodales nunc, eu tincidunt tortor velit vel massa. Pellentesque feugiat justo sed sapien aliquet sit amet rutrum tellus consequat. Morbi euismod laoreet elit aliquam rutrum. Nam pretium odio quis ante venenatis laoreet. Nullam laoreet tortor id orci commodo ultricies.</p>
<?php

echo 'blah => ', ob_get_level(), '<br />';

ob_start();

echo 'blah2 => ', ob_get_level(), '<br />';

$b = ob_get_contents();

ob_end_flush();
ob_end_clean();

echo '<pre>', var_dump($_SERVER), '</pre>';