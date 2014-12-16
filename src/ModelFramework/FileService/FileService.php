<?php

namespace ModelFramework\FileService;

class FileService implements FileServiceInterface
{

    private $service = null;

    public function __construct( \Zend\ServiceManager\ServiceManager $serviceManager )
    {
        $this -> service = $serviceManager;
    }

    public function saveFile( $filename, $tmpname, $ispublic = FALSE, $userdir = null )
    {
        $destenation = $this -> setDestenation( $filename, $ispublic, $userdir );
        if(!$destenation || !@copy( $tmpname, $destenation ))
        {
            return FALSE;
        }
        return $destenation;
    }

    private function setDestenation( $filename, $ispublic = FALSE, $userdir = null )
    {
        $auth           = $this -> service -> get( 'ModelFramework\AuthService' );
        if ($ispublic)
        {
            $companydirname = './public/' . (string) $auth -> getMainUser() -> company_id;
        }
        else
        {
            $companydirname = './upload/' . (string) $auth -> getMainUser() -> company_id;
        }
        if ($userdir == null)
        {
            $userdir = (string) $auth -> getUser() -> id();
        }
        if ( !file_exists( $companydirname ) )
        {
            if (!mkdir( $companydirname, 0777, true ))
            {
                return FALSE;
            }
        }
        $userdirname = $companydirname . '/' . $userdir;
        if ( !file_exists( $userdirname ) )
        {
            if(!mkdir( $userdirname, 0777, true))
            {
                return FALSE;
            }
        }
        $destenation = $userdirname . '/' . uniqid() . $filename;
        return $destenation;
    }

    public function getFileExtension( $filename )
    {
        return strtolower(@pathinfo( $filename )[ 'extension' ]);
    }

    public function checkDestenation( $filename, $ispublic = FALSE, $userdir = null )
    {
        $auth = $this -> service -> get( 'ModelFramework\AuthService' );
        if ($userdir == null)
        {
            $userdir = (string) $auth -> getUser() -> id();
        }
        if ( $ispublic )
        {
            $destenation = './public/' . (string) $auth -> getMainUser() -> company_id . '/' . $userdir . '/' . $filename;
        }
        else
        {
            $destenation = './upload/' . (string) $auth -> getMainUser() -> company_id . '/' . $userdir . '/' . $filename;
        }
        if ( file_exists( $destenation ) )
        {
            return $destenation;
        }
        return FALSE;
    }

    public function checkBucket( $filename, $bucketname, $ispublic = FALSE )
    {
        $auth = $this -> service -> get( 'ModelFramework\AuthService' );
        if ( $ispublic )
        {
            $destenation = './public/' . $bucketname . '/' . $filename;
        }
        else
        {
            $destenation = './upload/' . (string) $auth -> getMainUser() -> company_id . '/' . (string) $auth -> getUser() -> id() . '/' . $filename;
        }
        if ( file_exists( $destenation ) )
        {
            return $destenation;
        }
        return FALSE;
    }

    public function getFileStream ( $filename, $bucketname, $ispublic = false )
    {
      $destenation = $this -> checkBucket( $filename, $bucketname, $ispublic );
      if ( !$destenation )
        {
            return FALSE;
        }

        $response = new \Zend\Http\Response\Stream();
        $headers  = new \Zend\Http\Headers();

        $headers -> addHeaderLine( 'Content-Type', 'application/octet-stream' )
            -> addHeaderLine( 'Content-Disposition', 'attachment; filename="' . $filename . '"' )
            -> addHeaderLine( 'Content-Length', filesize( $destenation ) );

        $response -> setHeaders( $headers );

        $response -> setStream( $stream = fopen( $destenation, 'r' ) );
        $response -> setStatusCode( 200 );
      return $response;
    }

    public function downloadFile( $filename, $ispublic = false )
    {
        $destenation = $this -> checkDestenation( $filename, $ispublic );
        if ( !$destenation )
        {
            return FALSE;
        }

        $response = new \Zend\Http\Response\Stream();
        $headers  = new \Zend\Http\Headers();

        $headers -> addHeaderLine( 'Content-Type', 'application/octet-stream' )
            -> addHeaderLine( 'Content-Disposition', 'attachment; filename="' . $filename . '"' )
            -> addHeaderLine( 'Content-Length', filesize( $destenation ) );

        $response -> setHeaders( $headers );

        $response -> setStream( $stream = fopen( $destenation, 'r' ) );
        $response -> setStatusCode( 200 );

        return $response;
    }

    public function deleteFile( $filename )
    {
        $destenation = $this -> checkDestenation( $filename );
        if ( !$destenation )
        {
            return FALSE;
        }

        @unlink( $destenation );
        return TRUE;
    }

    public function getServerUrl()
    {
        return $this -> service ->  get('ViewHelperManager') -> get('serverUrl') -> __invoke();
    }
}
