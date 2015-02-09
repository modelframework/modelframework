<?php

namespace ModelFramework\FileService;

use Zend\Filter\Null;

class FileService implements FileServiceInterface
{
    private $service = null;

    public function __construct(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $this->service = $serviceManager;
    }

    public function saveFile($filename, $tmpname, $ispublic = false, $userdir = null)
    {
        $destenation = $this->setDestenation($filename, $ispublic, $userdir);
        if (!$destenation || !@copy($tmpname, $destenation)) {
            return false;
        }

        return $destenation;
    }

    /**
     * @param $filename
     * @param $stream
     * @param bool $ispublic
     * @param null $userdir
     * @return bool|string
     */
    public function saveStramToFile($filename, $stream, $ispublic = false, $userdir = null)
    {
        $destenation = $this->setDestenation($filename, $ispublic, $userdir);
        if (!$destenation || !@file_put_contents($destenation, $stream)) {
            return false;
        }

        return $destenation;
    }

    public function moveFile($from, $to)
    {
        if (!@rename($from, $to)) {
            return false;
        }

        return true;
    }

    public function setDestenation($filename, $ispublic = false, $userdir = null)
    {
        $auth = $this->service->get('ModelFramework\AuthService');
        if ($ispublic) {
            $companydirname = './public/'.(string) $auth->getMainUser()->company_id;
        } else {
            $companydirname = './upload/'.(string) $auth->getMainUser()->company_id;
        }
        if ($userdir == null) {
            $userdir = (string) $auth->getUser()->id();
        }
        if (!file_exists($companydirname)) {
            if (!mkdir($companydirname, 0777, true)) {
                return false;
            }
        }
        $userdirname = $companydirname.'/'.$userdir;
        if (!file_exists($userdirname)) {
            if (!mkdir($userdirname, 0777, true)) {
                return false;
            }
        }
        $destenation = $userdirname.'/'.uniqid().$filename;

        return $destenation;
    }

    public function getFileExtension($filename)
    {
        return strtolower(@pathinfo($filename)[ 'extension' ]);
    }

    public function getBucket()
    {
        return $this->service->get('ModelFramework\AuthService')->getMainUser()->company_id;
    }

    public function checkDestenation($filename, $ispublic = false, $userdir = null)
    {
        $auth = $this->service->get('ModelFramework\AuthService');
        if ($userdir == null) {
            $userdir = (string) $auth->getUser()->id();
        }
        if ($ispublic) {
            $destenation = './public/'.(string) $auth->getMainUser()->company_id.'/'.$userdir.'/'.$filename;
        } else {
            $destenation = './upload/'.(string) $auth->getMainUser()->company_id.'/'.$userdir.'/'.$filename;
        }
        if (file_exists($destenation) && !empty($filename)) {
            return $destenation;
        }

        return false;
    }

    public function checkBucket($filename, $bucketname, $ispublic = false)
    {
        $auth = $this->service->get('ModelFramework\AuthService');
        if ($ispublic) {
            $destenation = './public/'.$bucketname.'/'.$filename;
        } else {
            $destenation =
                './upload/'.(string) $auth->getMainUser()->company_id.'/'.(string) $auth->getUser()->id().'/'.
                $filename;
        }
        if (file_exists($destenation)) {
            return $destenation;
        }

        return false;
    }

    public function getFileStream($filename, $bucketname = null, $ispublic = false)
    {
        if ($bucketname == null) {
            $bucketname = $this->getBucket();
        }
        $destenation = $this->checkBucket($filename, $bucketname, $ispublic);
        if (!$destenation) {
            return false;
        }

        $response = new \Zend\Http\Response\Stream();
        $headers  = new \Zend\Http\Headers();

        $headers->addHeaderLine('Content-Type', 'application/octet-stream')
                ->addHeaderLine('Content-Disposition', 'attachment; filename="'.$filename.'"')
                ->addHeaderLine('Content-Length', filesize($destenation));

        $response->setHeaders($headers);

        $response->setStream($stream = fopen($destenation, 'r'));
        $response->setStatusCode(200);

        return $response;
    }

    public function downloadFile($filename, $ispublic = false, $userdir = null)
    {
        $destenation = $this->checkDestenation($filename, $ispublic, $userdir);
        if (!$destenation) {
            return false;
        }

        $response = new \Zend\Http\Response\Stream();
        $headers  = new \Zend\Http\Headers();

        $headers->addHeaderLine('Content-Type', 'application/octet-stream')
                ->addHeaderLine('Content-Disposition', 'attachment; filename="'.$filename.'"')
                ->addHeaderLine('Content-Length', filesize($destenation));

        $response->setHeaders($headers);

        $response->setStream($stream = fopen($destenation, 'r'));
        $response->setStatusCode(200);

        return $response;
    }

    public function deleteFile($filename)
    {
        $destenation = $this->checkDestenation($filename);
        if (!$destenation) {
            return false;
        }

        @unlink($destenation);

        return true;
    }

    public function getServerUrl()
    {
        return $this->service->get('ViewHelperManager')->get('serverUrl')->__invoke();
    }
}
