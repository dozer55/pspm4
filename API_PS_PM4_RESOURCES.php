<?php

/**
 * PmtResource is class for accessing the ProcessMaker API via Guzzle
 *
 * Example usage:
 * ```php
 * $api = getenv()['API_HOST'];
 * $pmtResource = new PmtResource($api);
 * $collection = $pmtResource->getCollectionByName("GET", "My Collections");
 * ```
 */
class PmtResource
{
    public $apiBaseUri;
    public $endpoint;
    public $response;

    function __construct($apiBaseUri, $endpoint)
    {
        $this->apiBaseUri = $apiBaseUri;
        $this->endpoint = (substr($endpoint, 0, 1) != '/' ?  '/' : '')  . $endpoint;
    }

    /**
     * Post data to a ProcessMaker Resource
     *
     * @param array $payload
     *
     */
    function pmtPost($payload, $resource = '')
    {
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $guzzleHttpMethod = "POST";
        $guzzleApi = $this->apiBaseUri . $this->endpoint . $resource;
        $guzzleOptions["headers"]["Accept"] = "application/json";
        $guzzleOptions["headers"]["Authorization"] = "Bearer " . getenv('API_TOKEN');
        $guzzleOptions["body"] = json_encode($payload);

        try {
            $guzzleResponse = $guzzleClient->request($guzzleHttpMethod, $guzzleApi, $guzzleOptions);
            $response = json_decode($guzzleResponse->getBody(), true);
        } catch (\Throwable $th) {
            $response = [
                'error' => [
                    'code' => $th->getCode(),
                    'message' => $th->getMessage()
                ]
            ];
        }

        $this->response = $response;

        return !isset($this->response['error']);
    }

    /**
     * Get data from a ProcessMaker Resource
     *
     * @param array $payload
     *
     */
    function pmtGet($payload)
    {
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $guzzleHttpMethod = "GET";
        $guzzleApi = $this->apiBaseUri . $this->endpoint;
        $guzzleOptions["headers"]["Accept"] = "application/json";
        $guzzleOptions["headers"]["Authorization"] = "Bearer " . getenv('API_TOKEN');
        $guzzleOptions["json"] = $payload;

        try {
            $guzzleResponse = $guzzleClient->request($guzzleHttpMethod, $guzzleApi . $payload, $guzzleOptions);
            $response = json_decode($guzzleResponse->getBody(), true);
        } catch (\Throwable $th) {
            $response = [
                'error' => [
                    'code' => $th->getCode(),
                    'message' => $th->getMessage()
                ]
            ];
        }

        $this->response = $response;

        return !isset($this->response['error']);
    }

    /**
     * Get data from a ProcessMaker Resource
     *
     * @param array $payload
     *
     */
    function pmtPut($payload, $resource = '')
    {
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $guzzleHttpMethod = "PUT";
        $guzzleApi = $this->apiBaseUri . $this->endpoint . $resource;
        $guzzleOptions["headers"]["Accept"] = "application/json";
        $guzzleOptions["headers"]["Authorization"] = "Bearer " . getenv('API_TOKEN');
        $guzzleOptions["json"] = $payload;

        try {
            $guzzleResponse = $guzzleClient->request($guzzleHttpMethod, $guzzleApi, $guzzleOptions);
            $response = json_decode($guzzleResponse->getBody(), true);
        } catch (\Throwable $th) {
            $response = [
                'error' => [
                    'code' => $th->getCode(),
                    'message' => $th->getMessage()
                ]
            ];
        }

        $this->response = $response;

        return !isset($this->response['error']);
    }

    /**
     * Get data from a ProcessMaker Resource
     *
     * @param array $payload
     *
     */
    function pmtDelete($payload)
    {
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $guzzleHttpMethod = "DELETE";
        $guzzleApi = $this->apiBaseUri . $this->endpoint . $payload;
        $guzzleOptions["headers"]["Accept"] = "application/json";
        $guzzleOptions["headers"]["Authorization"] = "Bearer " . getenv('API_TOKEN');
        $guzzleOptions["json"] = $payload;

        try {
            $guzzleResponse = $guzzleClient->request($guzzleHttpMethod, $guzzleApi, $guzzleOptions);
            $response = json_decode($guzzleResponse->getBody(), true);
        } catch (\Throwable $th) {
            $response = [
                'error' => [
                    'code' => $th->getCode(),
                    'message' => $th->getMessage()
                ]
            ];
        }

        $this->response = $response;

        return !isset($this->response['error']);
    }

    function isError()
    {
        return isset($this->response["error"]);
    }

    function getResponse()
    {
        return $this->response;
    }
}

/**
 * Collection is a class for accessing PM Collection
 *
 * Example usage:
 * ```php
 * $collection = new Collection();
 * $response = $collection->getCollectionByName('collectionName');
 * $result = $collection->getCollectionById('8');
 * $result = $collection->getCollectionRecords('8',"pmql=data.PROCESS_ID =19 and data.DATA_NAME = \"always cc\"");
 * $result = $collection->isError();
 * $response = $collection->response();
 * ```
 * $result true if the response has no errors, false if an error is found when the function get/post/put/delte are called
 * $response contains the response returned by the collection
 */

class Collection extends PmtResource
{
    function __construct()
    {
        parent::__construct(getenv("API_HOST"), "/collections");
    }

    /**
     * Create collection
     *
     * @param array $payload
     *        associative array with param name and value
     *        ie parameterList["pmql"] = "data.PROCESS_NAME = \"Grade Change\" and data.DATA_NAME = \"always cc\""
     *
     */
    function create($payload)
    {
        $result = $this->pmtPost($payload);
        return $result;
    }

    /**
     * Get collection by Name
     *
     * @param string $name
     *
     */
    function getCollectionByName($name)
    {
        $result = $this->pmtGet("?" . 'filter=' . urlencode("$name"));
        $collection = null;
        foreach ($this->response['data'] as $coll) {
            if ($coll['name'] == $name) {
                $collection = $coll;
            }
        }

        return $collection;
    }

    /**
     * Get collection by Id
     *
     * @param string $id
     *
     */
    function getCollectionById($id)
    {
        $result = $this->pmtGet("/$id");
        return $result;
    }

    /**
     * Get collection records
     *
     * @param string $id
     * @param array $parameterList optional
     *        associative array with param name and value
     *        ie parameterList["pmql"] = "data.PROCESS_NAME = \"Grade Change\" and data.DATA_NAME = \"always cc\""
     *
     */
    function getCollectionRecords($id, $parameterList = [])
    {
        $parameters = [];
        foreach ($parameterList as $name => $value) {
            $parameters[] = $name . '=' . urlencode($value);
        }
        $strParams = implode('&', $parameters);
        $result = $this->pmtGet("/$id/records?" .  $strParams);
        return $result;
    }

    /**
     * Get collection records by collection name
     *
     * @param string $name
     * @param array $parameterList optional
     *        associative array with param name and value
     *        ie parameterList["pmql"] = "data.PROCESS_NAME = \"Grade Change\" and data.DATA_NAME = \"always cc\""
     *
     */
    function getCollectionRecordsByName($name, $parameterList = [])
    {
        $response = $this->getCollectionByName($name);
        $id = $response['id'];
        if (isset($id)) {
            $result = $this->getCollectionRecords($id, $parameterList);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Add collection records
     *
     * @param string $id
     * @param array $parameterList optional
     *        associative array with param name and value
     *        ie parameterList["pmql"] = "data.PROCESS_NAME = \"Grade Change\" and data.DATA_NAME = \"always cc\""
     *
     */
    function addRecordById($id, $payload)
    {
        $result = $this->pmtPost($payload, "/$id/records");
        return $result;
    }

    /**
     * Add collection records by collection name
     *
     * @param string $name
     * @param array $parameterList optional
     *        associative array with param name and value
     *        ie parameterList["pmql"] = "data.PROCESS_NAME = \"Grade Change\" and data.DATA_NAME = \"always cc\""
     *
     */
    function addRecordByName($name, $payload)
    {
        $response = $this->getCollectionByName($name);
        $id = $response['id'];
        if (isset($id)) {
            $result = $this->addRecordById($id, $payload);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Delete collection records
     *
     * @param string $id
     * @param integer $recordId
     *
     */
    function deleteRecordById($id, $recordId)
    {
        $result = $this->pmtDelete("/$id/records/" . $recordId);
        return $result;
    }

    /**
     * Delete collection records by collection name and record id
     *
     * @param string $name
     * @param integer $recordId
     *
     */
    function deleteRecordByName($name, $recordId)
    {
        $response = $this->getCollectionByName($name);
        $id = $response['id'];
        if (isset($id)) {
            $result = $this->deleteRecordById($id, $recordId, null);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Update collection records
     *
     * @param string $id
     * @param array $parameterList optional
     *        associative array with param name and value
     *        ie parameterList["pmql"] = "data.PROCESS_NAME = \"Grade Change\" and data.DATA_NAME = \"always cc\""
     *
     */
    function updateCollectionRecords($id, $recordId, $payload)
    {
        $result = $this->pmtPut($payload, "/$id/records/$recordId");
        return $result;
    }

    /**
     * Add collection records by collection name
     *
     * @param string $name
     * @param array $parameterList optional
     *        associative array with param name and value
     *        ie parameterList["pmql"] = "data.PROCESS_NAME = \"Grade Change\" and data.DATA_NAME = \"always cc\""
     *
     */
    function updateCollectionRecordsByName($name, $recordId, $payload)
    {
        $response = $this->getCollectionByName($name);
        $id = $response['id'];
        if (isset($id)) {
            $result = $this->updateCollectionRecords($id, $recordId, $payload);
        } else {
            $result = false;
        }
        return $result;
    }
}


/**
 * Group is a class for accessing PM Groups
 *
 * Example usage:
 * ```php
 * $group = new Group();
 * $response = $Group->getGroupByName('groupName');
 * $result = $Group->getGroupById('8');
 * $result = $Group->getGroupUsers('8',"filter=userName");
 * $result = $Group->isError();
 * $response = $Group->response();
 * ```
 * $result true if the response has no errors, false if an error is found when the function get/post/put/delte are called
 * $response contains the response returned by the Group
 */
class Group extends PmtResource
{
    function __construct()
    {
        parent::__construct(getenv("API_HOST"), "/groups");
    }

    /**
     * Get group by Name
     *
     * @param string $name
     *
     */
    function getGroupByName($name)
    {
        $result = $this->pmtGet("?" . "filter=$name");
        $result = null;
        foreach ($this->response['data'] as $item) {
            if ($item['name'] == $name) {
                $result = $item;
            }
        }

        return $result;
    }

    /**
     * Get group by Id
     *
     * @param string $id
     *
     */
    function getGroupById($id)
    {
        $result = $this->pmtGet("/$id");
        return $result;
    }

    /**
     * Get group users
     *
     * @param string $id
     * @param string $parameters optional
     *
     */
    function getGroupUsers($id, $parameters = null)
    {
        $result = $this->pmtGet("/$id/users?" .  $parameters);
        return $result;
    }

    /**
     * Get group users by group Name
     *
     * @param string $name
     * @param string $parameters optional
     *
     */
    function getGroupUsersByName($name, $parameters = null)
    {
        $group = $this->getGroupByName($name);
        $id = $group['id'];
        $result = $this->pmtGet("/$id/users?" .  $parameters);

        return $result;
    }
}

/**
 * EwfLogger is a singleton class for log information to Logging - Ellucian Workflow collection
 *
 * Example usage:
 * ```php
 *  $ewfLogger = EwfLogger::getEwfLogger();
 *
 *  $logInfo = (object)  [
 *      'date' => '',               // autopopulated before add the record to the collection
 *      'logLevel' => '',           // autopopulated by the function called logError, logInfo, lofDebug, logWarning
 *      'requestId' => '',          // autopopulated from data["_request"]["id"]
 *      'processName' => '',        // autopopulated from data["_request"]["name"]
 *      'message' => '',
 *      'functionName' => '',
 *      'functionArguments' => '',
 *      'apiDataModel' => '',
 *      'log' => (object) [
 *          'count' => 0,
 *          'totalCount' => 0,
 *          'elapsed' => 0,
 *          'version' => '',
 *          'errorMessage' => ''
 *      ]
 *  ];
 *
 *  $result = $ewfLogger->log(EwfLogger::ERROR, $logInfo);
 *
 * ```
 * $result true if the information was logged, false if an error is found when the information is logged
 */
class EwfLogger
{
    protected $collectionName = 'Logging - Ellucian Workflow';
    protected $collection = null;
    protected $collectionId = null;
    protected $data = null;
    protected $logLevel = null;
    protected static $ewfLogger = null;
    protected const LOG_LEVELS = [
        'error' => 1,
        'warning' => 2,
        'debug' => 3,
        'info' => 4
    ];
    public const ERROR = 'error';
    public const WARNING = 'warning';
    public const DEBUG = 'debug';
    public const INFO = 'info';


    protected function __construct()
    {
        $this->collection = new Collection();
        $coll = $this->collection->getCollectionByName($this->collectionName);
        $this->collectionId = $coll['id'];
        $this->data = $GLOBALS['data'];
        $this->logLevel = self::LOG_LEVELS[getenv("LOG_LEVEL")] ?? self::LOG_LEVELS['error'];
    }

    /**
     * Get getEwfLogger
     *
     * get the EwfLogger instance
     */
    public static function getEwfLogger()
    {
        if (!isset(self::$ewfLogger)) {
            self::$ewfLogger = new EwfLogger();
        }

        return self::$ewfLogger;
    }

    /**
     * log
     *
     * @param object $logLevel use EwfLogger::DEBUG, EwfLogger::ERROR, EwfLogger::INFO, EwfLogger::WARNING,
     * @param object $info
     *        object containing the information to log, see example in EwfLogger class information section
     *
     */
    public function log($logLevel, $info)
    {
        $result = false;
        if ($this->isLoggingLevel($logLevel)) {
            if (isset($this->collectionId)) {
                $info->logLevel = $logLevel;
                $info->date = date_create()->format(DATE_ISO8601);
                $info->requestId = $this->data["_request"]["id"] ?? "";
                $info->processName = $this->data["_request"]["name"] ?? "";

                $result = $this->collection->addRecordById($this->collectionId, $info);
            }
        } else {
            $result = true;
        }
        return $result;
    }

    /**
     * isLoggingLevel return true if the log level passed as parameter will be logged, else return false
     *
     * @param object $logLevel use EwfLogger::DEBUG, EwfLogger::ERROR, EwfLogger::INFO, EwfLogger::WARNING,
     *
     */
    public function isLoggingLevel($logLevel)
    {
        return self::LOG_LEVELS[$logLevel] <= $this->logLevel;
    }
}

class WFUser
{
    public $id;

    function __construct($id)
    {
        $this->id = $id;
    }

    public function getWFUser()
    {
        $api = $GLOBALS['api'];
        $apiInstance = $api->users();
        $user = $apiInstance->getUserById($this->id);

        return [
            'user' => [
                'id' => $user->getId(),
                'userName' => $user->getUsername(),
                'firstName' => $user->getFirstname(),
                'lastName' => $user->getLastname(),
                'email' => $user->getEmail(),
                'status' => $user->getStatus(),
            ]
        ];
    }
}
