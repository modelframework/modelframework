<?php
/**
 * Class FileServiceInterface
 * @package ModelFramework\FileService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FileService;

interface FileServiceInterface
{
    public function saveFile( $filename, $tmpname, $ispublic = FALSE, $userdir = null );

//    private function setDestenation( $filename, $ispublic = FALSE, $userdir = null );

    public function getFileExtension( $filename );

    public function checkDestenation( $filename, $ispublic = FALSE, $userdir = null );

    public function checkBucket( $filename, $bucketname, $ispublic = FALSE );

    public function getFileStream ( $filename, $ispublic = false );

    public function downloadFile( $filename, $ispublic = false );

    public function deleteFile( $filename );

    public function getServerUrl();

}