<?php

namespace ModelFramework\FilesystemService\Adapter;
set_time_limit(200);
use League\Flysystem\Config;
use SplFileInfo;
use FilesystemIterator;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use League\Flysystem\Util;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Adapter\AbstractAdapter;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Client\Exception;
use Zend\Json\Json;
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
     * @var Client
     */
    protected $client;


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

        $this->client = new Client();


        $adapter = new Curl();
        $adapter->setOptions([
            'curloptions' => [
                CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]
        ]);

        $this->client->setAdapter($adapter);

    }

    protected function getAuthToken($actionUrl)
    {
        // Generate authentication token first...
        if (!$this->authToken) {
            $apiUrl = $this->pydioRestApi . self::$generateAuthTokenUrl . "/" . self::$deviceId;

            $response = $this->client->setUri($apiUrl)
                ->setAuth($this->pydioRestUser, $this->pydioRestPw)
                ->send();

            if ($response->getStatusCode() >= 400) {
                throw new \Exception ($response->getContent());
            }

            $jsonResponse = json_decode($response->getContent());
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
//        prn($action,$path,$response,curl_getinfo($curl),$curlPostData);
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

//
//        while (! feof($resource)) {
//            $postData = [
//                "xhr_uploader"                      => urlencode("true"),
//                "auto_rename"                       => urlencode("false"),
//                "urlencoded_filename"               => urlencode(basename($path)),
//                'userfile_0"; filename="fake-name"' => fread($resource, 1024),
//                "appendto_urlencoded_part"          => urlencode(basename($path)),
//            ];
//            echo ftell($resource)."<br/>";
//            $response = $this->request('upload', dirname($path), $postData);
//        }
//
//        return $path;
        $stream = '';
        while (!feof($resource)) {
            $stream .= fread($resource, 1024);
        }


        $postData = [
            "xhr_uploader"                      => urlencode("true"),
            "auto_rename"                       => urlencode("false"),
            "urlencoded_filename"               => urlencode(basename($path)),
            'userfile_0"; filename="fake-name"' => $stream,
        ];

        $this->request('upload', dirname($path), $postData);

        return $path;
    }

    /**
     * Get a read-stream for a file
     *
     * @param $path
     * @return array|bool
     */
    public function readStream($path)
    {
        exit;
        $response = $this->request('get_content', $path);

//        $stream = \GuzzleHttp\Stream\Stream::factory($response);
//  //      stream_get_contents(fopen('data://,' . $response, 'r'));
        return \GuzzleHttp\Stream\Stream::factory($response);
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
        $location = $this->applyPathPrefix($path);
        $finfo = new Finfo(FILEINFO_MIME_TYPE);

        return ['mimetype' => $finfo->file($location)];
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
        $location = $this->applyPathPrefix($path);
        clearstatcache(false, $location);
        $permissions = octdec(substr(sprintf('%o', fileperms($location)), -4));
        $visibility = $permissions & 0044 ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE;

        return compact('visibility');
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
        $location = $this->applyPathPrefix($path);
        chmod($location, static::$permissions[$visibility]);

        return compact('visibility');
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

        $dirnames = explode('/', $dirname);

        $create = '/';
        foreach ($dirnames as $dirname) {
            $create .= '/' . $dirname;

            $actionUrl = self::$actions['mkdir'] . $create;

            $apiUrl = $this->pydioRestApi . $this->workspaceId . $actionUrl;
            $authHash = $this->getAuthToken($actionUrl);


            $curl = curl_init($apiUrl);
            $curlPostData = [
                "force_post" => urlencode("true"),
                "auth_hash"  => $authHash,
                "auth_token" => $this->authToken,
            ];

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPostData);
            $response = curl_exec($curl);
            curl_close($curl);
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
        $location = $this->applyPathPrefix($dirname);

        if (!is_dir($location)) {
            return false;
        }

        $contents = $this->listContents($dirname, true);
        $contents = array_reverse($contents);

        foreach ($contents as $file) {
            if ($file['type'] === 'file') {
                unlink($this->applyPathPrefix($file['path']));
            } else {
                rmdir($this->applyPathPrefix($file['path']));
            }
        }

        return rmdir($location);
    }

    /**
     * Normalize the file info
     *
     * @param SplFileInfo $file
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
