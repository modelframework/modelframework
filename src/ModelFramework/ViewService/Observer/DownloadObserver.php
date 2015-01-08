<?php
/**
 * Class DownloadObserver
 * @package ModelFramework\ModelViewService
 * @author  Ilia Davydenko di.nekto@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

class DownloadObserver extends AbstractObserver
{
    public function process($model)
    {
        $subject = $this->getSubject();
        $fs = $subject->getFileServiceVerify();
        $filename = basename($model->document);
        $response = $fs->downloadFile($filename);
        $subject->setResponse($response);
    }
}
