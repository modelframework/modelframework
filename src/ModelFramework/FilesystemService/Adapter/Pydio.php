<?php

namespace ModelFramework\FilesystemService\Adapter;

use League\Flysystem\Config;
use SplFileInfo;
use FilesystemIterator;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use League\Flysystem\Util;
use League\Flysystem\Adapter\AbstractAdapter;
use Zend\Config\Reader;


class Pydio extends AbstractAdapter
{
    protected static $permissions = [
        'public'  => 0744,
        'private' => 0700,
    ];

    protected static $actions = [
        'upload'      => '/upload/put/',
        'mkdir'       => '/mkdir/',
        'ls'          => '/ls/',
        'download'    => '/download/',
        'get_content' => '/get_content/',
        'rename'      => '/rename/',
        'copy'        => '/copy/',
        'move'        => '/move/',
        'delete'      => '/delete/',


    ];

    /**
     * We will need the following two actions "KEYSTORE_GENERATE_AUTH_TOKEN" and "UPLOAD"
     * @var string generateAuthTokenUrl
     */
    protected static $generateAuthTokenUrl = "pydio/keystore_generate_auth_token";

    /**
     * The current version of the python client is appending a device-id
     * @var string deviceId
     */
    protected static $deviceId = "";

    /**
     * @var
     */
    protected $pydioRestUser;

    /**
     * @var
     */
    protected $pydioRestPw;

    /**
     * @var
     */
    protected $pydioRestApi;


    /**
     * @var string authToken
     */
    protected $authToken = null;

    /**
     * @var
     */
    protected $authPrivate = null;

    /**
     * Target workspace-id
     * @var string workspaceId
     */
    protected $workspaceId = '';

    /**
     * Constructor.
     *
     * @param AuthService $auth
     * @param string $api_url
     * @param string $key
     */
    public function __construct(\ModelFramework\AuthService\AuthService $auth, $pydioRestUser, $pydioRestPw, $pydioRestApi, $workspaceId)
    {
        $this->workspaceId = $workspaceId;
        $this->pydioRestUser = $pydioRestUser;
        $this->pydioRestPw = $pydioRestPw;
        $this->pydioRestApi = $pydioRestApi;
    }

    protected function getAuthToken($actionUrl)
    {

        // Generate authentication token first...
        if (!$this->authToken) {

            $apiUrl = $this->pydioRestApi . self::$generateAuthTokenUrl . "/" . self::$deviceId;

            $curl = curl_init($apiUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_USERPWD, $this->pydioRestUser.':'.$this->pydioRestPw);
            $response = curl_exec($curl);

            if (curl_getinfo($curl, CURLINFO_HTTP_CODE) >= 400) {
                throw new \Exception ($response);
            }
            curl_close($curl);

            $jsonResponse = json_decode($response);
            $this->authToken = $jsonResponse->t;
            $this->authPrivate = $jsonResponse->p;
        }
        // Build the authentication hash...
        $nonce = sha1(mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax());
        $uri = "/api/" . $this->workspaceId . $actionUrl;
        $message = $uri . ":" . $nonce . ":" . $this->authPrivate;
        $hash = hash_hmac("sha256", $message, $this->authToken);
        $authHash = $nonce . ":" . $hash;
        return $authHash;
    }


    protected function request($action, $path, $postData = [])
    {
        $actionUrl = self::$actions[$action] . $path;

        $apiUrl = $this->pydioRestApi . $this->workspaceId . $actionUrl;

        $authHash = $this->getAuthToken($actionUrl);

        $curl = curl_init($apiUrl);
        $curlPostData = [
            "force_post"  => urlencode("true"),
            "auth_hash"   => $authHash,
            "auth_token"  => $this->authToken,
            "auto_rename" => urlencode("false"),
        ];
        $curlPostData = array_merge($curlPostData, $postData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPostData);
        $response = curl_exec($curl);

        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) >= 400) {
            throw new \Exception ($response);
        }
        curl_close($curl);
        return $response;

    }

    /**
     * Ensure the root directory exists.
     *
     * @param   string $root root directory path
     * @return  string  real path to root
     */
    protected function ensureDirectory($root)
    {
    }

    /**
     * Check whether a file is present
     *
     * @param   string $path
     * @return  boolean
     */
    public function has($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Write a file
     *
     * @param $path
     * @param $contents
     * @param null $config
     * @return array|bool
     */
    public function write($path, $contents, Config $config)
    {
        $this->createDir(dirname($path), $config);
        $postData = [
            "xhr_uploader"                       => urlencode("true"),
            "urlencoded_filename"                => basename($path),
            '@userfile_0"; filename="fake-name"' => $contents,
        ];
        $this->request('upload', dirname($path), $postData);
        return $path;
    }

    /**
     * Write using a stream
     *
     * @param $path
     * @param $resource
     * @param null $config
     * @return array|bool
     */
    public function writeStream($path, $resource, Config $config)
    {
        if (!$this->has(dirname($path))) {
            $this->createDir(dirname($path), $config);
        }

        $string = '';
        while (!feof($resource)) {
            $string .= fread($resource, 1024);
        }

        $postData = [
            "xhr_uploader"                      => urlencode("true"),
            "auto_rename"                       => urlencode("false"),
            "urlencoded_filename"               => urlencode(basename($path)),
            'userfile_0"; filename="fake-name"' => $string,
        ];

        $this->request('upload', dirname($path), $postData);

        if ($visibility = $config->get('visibility')) {
        //    $this->setVisibility($path, $visibility);
        }

        return compact('path', 'visibility');
    }

    /**
     * Get a read-stream for a file
     *
     * @param $path
     * @return array|bool
     */
    public function readStream($path)
    {
        $response = $this->request('get_content', $path);

        $stream = fopen('data://text/plain;base64,' . base64_encode($response), 'r');

        return compact('stream', 'path');
    }

    /**
     * Update a file using a stream
     *
     * @param   string $path
     * @param   resource $resource
     * @param   mixed $config Config object or visibility setting
     * @return  array|bool
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * Update a file
     *
     * @param   string $path
     * @param   string $contents
     * @param   mixed $config Config object or visibility setting
     * @return  array|bool
     */
    public function update($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $mimetype = Util::guessMimeType($path, $contents);

        if (($size = file_put_contents($location, $contents, LOCK_EX)) === false) {
            return false;
        }

        return compact('path', 'size', 'contents', 'mimetype');
    }

    /**
     * Read a file
     *
     * @param   string $path
     * @return  array|bool
     */
    public function read($path)
    {

        $contents = $this->request('download', $path);
        return compact('contents', 'path');
    }

    /**
     * Rename a file
     *
     * @param $path
     * @param $newpath
     * @return bool
     */
    public function rename($path, $new_name)
    {

        $postData = [
            "file"         => '/' . $path,
            "filename_new" => '/' . basename($new_name),
            "dest"         => '/' . dirname($new_name),
        ];
        $contents = $this->request('rename', $path, $postData);
        return compact('contents', 'path');

    }

    /**
     * Copy a file
     *
     * @param $path
     * @param $newpath
     * @return bool
     */
    public function copy($path, $newpath)
    {
        $location = $this->applyPathPrefix($path);
        $destination = $this->applyPathPrefix($newpath);
        $this->ensureDirectory(dirname($destination));

        return copy($location, $destination);
    }

    /**
     * Delete a file
     *
     * @param $path
     * @return bool
     */
    public function delete($path)
    {
        $postData = [
            "file" => '/' . $path,
        ];
        $contents = $this->request('delete', $path, $postData);

        return compact('contents', 'path');
    }

    /**
     * List contents of a directory
     *
     * @param string $directory
     * @param bool $recursive
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        $response = $this->request('ls', $directory);

        if ($xml = simplexml_load_string($response)) {
            $result = $this->normalizeFileInfo($response);
        } else {
            throw new \Exception ('Request error');
        }

        return $result;
    }

    /**
     * Get the metadata of a file
     *
     * @param $path
     * @return array
     */
    public function getMetadata($path)
    {
        $response = $this->request('ls', dirname($path), ["file" => basename($path)]);

        $xml = simplexml_load_string($response);
        if (count($xml)) {
            return $this->normalizeFileInfo($xml);
        } else {
            return false;
        }
    }

    /**
     * Get the size of a file
     *
     * @param $path
     * @return array
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the mimetype of a file
     *
     * @param $path
     * @return array
     */
    public function getMimetype($path)
    {
//        $location = $this->applyPathPrefix($path);
//        $finfo = new Finfo(FILEINFO_MIME_TYPE);
//
//        return ['mimetype' => $finfo->file($location)];
    }

    /**
     * Get the timestamp of a file
     *
     * @param $path
     * @return array
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the visibility of a file
     *
     * @param $path
     * @return array|void
     */
    public function getVisibility($path)
    {
        return;
    }

    /**
     * Set the visibility of a file
     *
     * @param $path
     * @param $visibility
     * @return array|void
     */
    public function setVisibility($path, $visibility)
    {
        return;
    }

    /**
     * Create a directory
     *
     * @param   string $dirname directory name
     * @param   array|Config $options
     *
     * @return  bool
     */
    public function createDir($dirname, Config $config)
    {

        $create = '/';
        foreach (explode('/', $dirname) as $dirname) {
            $create .= '/' . $dirname;
            if(!$this->request('mkdir', $create)){
                return false;
            }
        }

        return ['path' => $create, 'type' => 'dir'];
    }

    /**
     * Delete a directory
     *
     * @param $dirname
     * @return bool
     */
    public function deleteDir($dirname)
    {

//        if (!$this->has($dirname)) {
//            return false;
//        }
//
//        $contents = $this->listContents($dirname, true);
//        $contents = array_reverse($contents);
//
//        foreach ($contents as $file) {
//            if ($file['type'] === 'file') {
//                unlink($this->applyPathPrefix($file['path']));
//            } else {
//                rmdir($this->applyPathPrefix($file['path']));
//            }
//        }
//
//        return rmdir($dirname);
    }

    /**
     * Normalize the file info
     * @param xml $xml
     * @return array
     */
    protected function normalizeFileInfo($xml)
    {
        $normalized = [
            'type'      => ($xml->tree['is_file'] == 'true') ? 'file' : 'dir',
            'path'      => (string)$xml->tree['filename'],
            'timestamp' => (string)$xml->tree['ajxp_modiftime']
        ];
        if ($normalized['type'] === 'file') {
            $normalized['size'] = (string)$xml->tree['bytesize'];
        }

        return $normalized;
    }

    /**
     * Get the normalized path from a SplFileInfo object
     *
     * @param   SplFileInfo $file
     * @return  string
     */
    protected function getFilePath(SplFileInfo $file)
    {
        $path = $file->getPathname();
        $path = $this->removePathPrefix($path);

        return trim($path, '\\/');
    }

    /**
     * @param $path
     * @return RecursiveIteratorIterator
     */
    protected function getRecursiveDirectoryIterator($path)
    {
        $directory = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

        return $iterator;
    }

    /**
     * @param $path
     * @return DirectoryIterator
     */
    protected function getDirectoryIterator($path)
    {
        $iterator = new DirectoryIterator($path);

        return $iterator;
    }

}
