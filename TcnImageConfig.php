<?php 
//Variables needing security
//Base folder containing herbarium folder ; read access needed
$sourcePathBase = '';
//Folder where images are to be placed; write access needed
$targetPathBase = '';
//Url base needed to build image URL that will be save in DB
$imgUrlBase = 'http://storage.idigbio.org/';
//Path to where log files will be placed
$logPath = $sourcePathBase;

//If silent is set, script will produce not produce a log file.
$silent = 0;
//If record matching PK is not found, should a new blank record be created?
$createNewRec = 1;
//Weather to copyover images with matching names (includes path) or rename new image and keep both
$copyOverImg = 1;

$webPixWidth = 800;
$tnPixWidth = 130;
$lgPixWidth = 2000;

//Whether to use ImageMagick for creating thumbnails and web images. ImageMagick must be installed on server.
// 0 = use GD library (default), 1 = use ImageMagick
$useImageMagick = 0;
//Value between 0 and 100
$jpgCompression = 80;

//Create thumbnail versions of image
$createTnImg = 1;
//Create large version of image, given source image is large enough
$createLgImg = 1;
$keepOrig = 1;

//0 = write image metadata to file; 1 = write metadata to Symbiota database
$dbMetadata = 1;

$collArr = array(
	'asu:lichens' => array('pmterm' => '/^(ASU\d{7})\D*/', 'collid' => 7)
	,'bry:lichens' => array('pmterm' => '/^(BRY-L-\d{7})\D*/', 'collid' => 13)
	,'dbg:mycology' => array('pmterm' => '/^(\d{1,5}[A-Z]{0,1})\D*/', 'collid' => 1) 
	,'duke:bryophytes' => array('pmterm' => '/^(\d{7})\D*/', 'collid' => 6)
	,'duke:lichens' => array('pmterm' => '/^(\d{7})\D*/', 'collid' => 28)
	,'f:bryophytes' => array('pmterm' => '/^(C\d{7}F)\D*/', 'collid' => 1)
	,'ill:bryophytes' => array('pmterm' => '/^(ILL\d{8})\D*/', 'collid' => 20)
	,'lsu:bryophytes' => array('pmterm' => '/^(LSU\d{8})\D*/', 'collid' => 18)
	,'mich:bryophytes' => array('pmterm' => '/^(\d{1,7})\D*/', 'collid' => 7)
	,'mich:lichens' => array('pmterm' => '/^(\d{1,7})\D*/', 'collid' => 32)
	,'mich:mycology' => array('pmterm' => '/^MICH-F-(\d{1,7}[A-Z]{0,1})\D*/', 'collid' => 10)
	,'mo:bryophytes' => array('pmterm' => '/^(MO-\d{7})/', 'collid' => 4)
	,'mont:lichens' => array('pmterm' => '/^(MONT-L-\d{7})\D*/', 'collid' => 42)
	,'msc:bryophytes' => array('pmterm' => '/^(MSC-B-\d{7})\D*/', 'collid' => 16)
	,'ncu:mycology' => array('pmterm' => '/^(NCU-F-\d{7})\D*/', 'collid' => 14)
	,'ny:lichens' => array('pmterm' => '/0*([1-9]{1}\d{0,7})\D*/', 'collid' => 2)
	,'ny:bryophytes' => array('pmterm' => '/0*([1-9]{1}\d{0,7})\D*/', 'collid' => 3)
	,'ny:mycology' => array('pmterm' => '/^NY-F-(\d{8})\D*/', 'collid' => 3)
	,'os:bryophytes' => array('pmterm' => '/^OS\d{7}\D*/', 'collid' => 19)
	,'os:lichens' => array('pmterm' => '/^OS\d{7}\D*/', 'collid' => 38)
	,'sfsu:mycology' => array('pmterm' => '/^(SFSU-F-\d{6}[A-Z]{0,1})\D*/', 'collid' => 18)
	,'srp:lichens' => array('pmterm' => '/^(SRP-L-\d{7})\D*/', 'collid' => 23)
	,'tenn:bryophytes' => array('pmterm' => '/^(TENN-B-\d{7})\D*/', 'collid' => 15)
	,'tenn:lichens' => array('pmterm' => '/^(TENN-L-\d{7})\D*/', 'collid' => 31)
	,'tenn:mycology' => array('pmterm' => '/^TENN\s*(\d{6}[A-Z]{0,1})\D*/', 'collid' => 7)
	,'vsc:bryophytes' => array('pmterm' => '/(VSC-L\d{5})\D*/', 'collid' => 13)
	,'vt:bryophytes' => array('pmterm' => '/(UVMVT\d{4,6})\D*/i', 'collid' => 9)
	,'vt:lichens' => array('pmterm' => '/(UVMVT\d{6})\D*/i', 'collid' => 39)
	,'wis:lichens' => array('pmterm' => '/^(WIS-L-\d{7})\D*/', 'collid' => 22)
	,'wtu:bryophytes' => array('pmterm' => '/^(WTU-B-\d{6})\D*/', 'collid' => 8)
	,'wtu:lichens' => array('pmterm' => '/^(WTU-L-\d{6})\D*/', 'collid' => 21)
	,'wtu:mycology' => array('pmterm' => '/^(WTU-F-\d{6}[A-Z]{0,1})\D*/', 'collid' => 9)
);
