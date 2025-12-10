<?php
    namespace Config\Utils;
    
    use Exception;

    class CustomExceptions extends Exception
    {

    private $_options = "unknown_error";

    private $_error_code = "000";

    public function __construct(...$errorCode)
    {
        if (!isset($errorCode[0])) return;
        if (!array_key_exists($errorCode[0], self::MESSAGE_CATALOGUE)) return;
        $this->_options = self::MESSAGE_CATALOGUE[$errorCode[0]]; 
        $this->_error_code = $errorCode[0]; 
    }

    public function GetOptions() { return ["error"=> true, "msg" => $this->_options, "error_code" => $this->_error_code]; }
    const MESSAGE_CATALOGUE = [
        "001" => "incorrect_request_method",
        "002" => "incorrect_class",
        "003" => "method_not_exist",
        "006" => "not_token",
        "007" => "empty_params",
        "008" => "incorrect_insert",
        "009" => "incorrect_update",
        "004" => "no_user",
        "005" => "invalid_credentials"
    ];
    }
?>