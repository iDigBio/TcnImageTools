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
//Full path to Symbiota project root folder
$serverRoot = '/var/www/html/symbiota';				
// Path to Symbiota Class Files, set to null if class files are 
// not available.  Correct path is required for the processing of 
// xml batch files.
// $symbiotaClassPath = $serverRoot."/classes/";
$symbiotaClassPath = null;

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

/**
 * Array of parameters for collections to process.
 * 'collection:project' => array( 
 *     'pmterm' => '/A(\d{8})\D+/', // regular expression to match collectionCode and catalogNumber in filename, first backreference is used as the catalogNumber. 
 *     'collid' => 0,               // symbiota collid corresponding with the pterm pattern.
 *     'prpatt' => '/^/',           // optional regular expression for match on catalogNumber to be replaced with prrepl. 
 *     'prrepl' => 'barcode-'       // optional replacement to apply for prpatt matches on catalogNumber.
 *     // given above description, 'A01234567.jpg' will yield catalogNumber = 'barcode-01234567'
 * )
 * 
 */

$collArr = array(
	'asu:lichens' => array('pmterm' => '/^(ASU\d{7})\D*/', 'collid' => 7)
	,'bry:lichens' => array('pmterm' => '/^(BRY-L-\d{7})\D*/', 'collid' => 13)
	,'chrb:lichens' => array('pmterm' => '/^(CHRB-L-\d{7})\D*/', 'collid' => 47)
	,'colo:bryophytes' => array('pmterm' => '/^(COLO-B-\d{7})\D*/', 'collid' => 23)
	,'conn:bryophytes' => array('pmterm' => '/^(CONN\d{8})\D*/', 'collid' => 30)
	,'conn:lichens' => array('pmterm' => '/^(CONN\d{8})\D*/', 'collid' => 49)
	,'dbg:mycology' => array('pmterm' => '/^(\d{1,5}[A-Z]{0,1})\D*/', 'collid' => 1) 
	,'duke:bryophytes' => array('pmterm' => '/^(\d{7})\D*/', 'collid' => 6)
	,'duke:lichens' => array('pmterm' => '/^(\d{7})\D*/', 'collid' => 28)
	,'f:bryophytes' => array('pmterm' => '/^(C\d{7}F)\D*/', 'collid' => 1)
	,'fh:bryophytes' => array('pmterm' => '/^FH(\d{8})\D*/', 'collid' => 22, 'prpatt' => '/^/', 'prrepl' => 'barcode-')
	,'fh:lichens' => array('pmterm' => '/^FH(\d{8})\D*/', 'collid' => 40, 'prpatt' => '/^/', 'prrepl' => 'barcode-')
	,'fh:mycology' => array('pmterm' => '/^FH(\d{8})\D*/', 'collid' => 22, 'prpatt' => '/^/', 'prrepl' => 'barcode-')
	,'flas:bryophytes' => array('pmterm' => '/^(FLAS\s{1}B\d{1,7})\D*/', 'collid' => 14)
	,'flas:lichens' => array('pmterm' => '/^(FLAS\s{1}L\d{1,7})\D*/', 'collid' => 35)
	,'flas:mycology' => array('pmterm' => '/^(FLAS-F-\d{5})\D*/', 'collid' => 13) 
	,'ill:bryophytes' => array('pmterm' => '/^(ILL\d{8})\D*/', 'collid' => 20)
	,'lsu:bryophytes' => array('pmterm' => '/^(LSU\d{8})\D*/', 'collid' => 18)
	,'lsu:mycology' => array('pmterm' => '/^(LSU\d{8})\D*/', 'collid' => 15)
	,'mich:bryophytes' => array('pmterm' => '/^(\d{1,7})\D*/', 'collid' => 7)
	,'mich:lichens' => array('pmterm' => '/^(\d{1,7})\D*/', 'collid' => 32)
	,'mich:mycology' => array('pmterm' => '/^MICH-F-(\d{1,7}[A-Z]{0,1})\D*/', 'collid' => 10)
	,'mo:bryophytes' => array('pmterm' => '/^(MO-\d{7})/', 'collid' => 4)
	,'mont:lichens' => array('pmterm' => '/^(MONT-L-\d{7})\D*/', 'collid' => 42)
	,'mor:lichens' => array('pmterm' => '/^(L-\d{7}-MOR)/', 'collid' => 48)
	,'msc:bryophytes' => array('pmterm' => '/^(MSC-B-\d{7})\D*/', 'collid' => 16)
	,'ncu:bryophytes' => array('pmterm' => '/^(NCU-B-\d{7})\D*/', 'collid' => 26)
	,'ncu:mycology' => array('pmterm' => '/^(NCU-F-\d{7})\D*/', 'collid' => 14)
	,'nebk:bryophytes' => array('pmterm' => '/(NEBK\d{8})\D*/', 'collid' => 31)
	,'nebk:lichens' => array('pmterm' => '/(NEBK\d{8})\D*/', 'collid' => 50)
	,'nha:bryophytes' => array('pmterm' => '/^(NHA-\d{6,7})\D*/', 'collid' => 28)
	,'nha:lichens' => array('pmterm' => '/^(NHA-\d{6,7})\D*/', 'collid' => 45)
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
	,'ttu:scan' => array('pmterm' => '/^(TTU-Z_\d{6})\D*/', 'collid' => 7)
	,'uaic:scan' => array('pmterm' => '/^(UAIC\d{7})\D*/', 'collid' => 11)
	,'uc:lichens' => array('pmterm' => '/^(UC\d{5,7})\D*/', 'collid' => 36)
	,'uc:mycology' => array('pmterm' => '/^(UC\d{5,7})\D*/', 'collid' => 17)
	,'vsc:bryophytes' => array('pmterm' => '/(VSC-L\d{5})\D*/', 'collid' => 13)
	,'vt:bryophytes' => array('pmterm' => '/(UVMVT\d{4,6})\D*/i', 'collid' => 9)
	,'vt:lichens' => array('pmterm' => '/(UVMVT\d{6})\D*/i', 'collid' => 39)
	,'wis:lichens' => array('pmterm' => '/^(WIS-L-\d{7})\D*/', 'collid' => 22)
	,'wtu:bryophytes' => array('pmterm' => '/^(WTU-B-\d{6})\D*/', 'collid' => 8)
	,'wtu:lichens' => array('pmterm' => '/^(WTU-L-\d{6})\D*/', 'collid' => 21)
	,'wtu:mycology' => array('pmterm' => '/^(WTU-F-\d{6}[A-Z]{0,1})\D*/', 'collid' => 9)
);
