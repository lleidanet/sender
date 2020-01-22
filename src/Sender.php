<?php
/**
 * Sender.php
 * API PHP v4
 * @author David Tapia (c) 2018 - Lleida.net
 * @version 4.0
 *
 */

namespace lnst;

use Psr\Log\LoggerInterface;
use lnst\Logger;

const HOST = 'https://api.lleida.net/';

const SERVICE_SMS = HOST.'sms/v2/';
const SERVICE_MMS = HOST.'mms/v2/';
const SERVICE_MSG = HOST.'messages/v3/';

const MAX_LENGTH_PREMIUM_NUMBERS = 5;

class Sender
{
    private $user;
    private $password;
    private $lang;

    public $error;
    public $errno;

    public $logger;

    // https://api.lleida.net/dtd/messages/v3/en/#filtros
    public static $statuses = array(
        'N' => array( 'code' =>	 1, 'description' => 'New. The message has not been processed yet.'),
        'P' => array( 'code' =>	 2, 'description' => 'Pending. The message has not been sent yet.'),
        'S' => array( 'code' =>	 3, 'description' => 'Sent. The message has been sent.'),
        'D' => array( 'code' =>	 4, 'description' => 'Delivered. The message has been delivered to the addressee (mobile phone confirmation has been received). This status is only available if delivery receipt was activated in the sending.'),
        'B' => array( 'code' =>	 5, 'description' => 'Buffered. Message has been sent to operator yet not to addresse. Mobile switched off or out of range. Operator will retry sending until recipient receives sms. This status is only available if delivery receipt was activated in the sending.'),
        'F' => array( 'code' =>	 6, 'description' => 'Failed. The message has not been sent.'),
        'I' => array( 'code' =>	 7, 'description' => 'Invalid. The message is invalid.'),
        'C' => array( 'code' =>	 8, 'description' => 'Cancelled. The message has been canceled.'),
        'X' => array( 'code' =>	 9, 'description' => 'Scheduled. The message is scheduled and undelivered.'),
        'E' => array( 'code' =>	10, 'description' => 'Expired. The message has been expired.'),
        'L' => array( 'code' =>	11, 'description' => 'Deleted. The message has been deleted by Operator.'),
        'V' => array( 'code' =>	12, 'description' => 'Undeliverable. The message has not been delivered.'),
        'U' => array( 'code' =>	13, 'description' => 'Unknown.'),
        'R' => array( 'code' =>	14, 'description' => 'Received. The MO has been received.'),
        'A' => array( 'code' =>	15, 'description' => 'Notified. The MO has been notified.'),
        'W' => array( 'code' =>	16, 'description' => 'Waiting. The MO is waiting proccess.'),
        'Z' => array( 'code' =>	17, 'description' => 'Processed. The MO has been processed.')
    );

    public static $languages = array(
        "ES", "CA", "EN", "FR", "DE",
        "IT", "NL", "PT", "PL", "SE"
    );

    public static $registered_types = array(
        'D', 'T'
    );

    // $sender = new Sender($user, $pass);
    public function __construct($user, $password, $lang = 'EN')
    {
        if (empty($user)) {
            throw new \InvalidArgumentException("Empty user!");
        }
        if (empty($password)) {
            throw new \InvalidArgumentException("Empty password!");
        }
        
        $this->user = $user;
        $this->password = $password;

        // Registered lang
        $this->setLang($lang);
    }

    // $queued = $sender->sms($id, $dst, $text, $options);
    public function sms($id, $dst, $text, $options = array())
    {
        return $this->mt($id, $dst, $text, $options);
    }

    // $queued = $sender->registeredSMS($id, $dst, $text, $options);
    public function registeredSMS($id, $dst, $text, $options = array())
    {
        if (empty($options)) {
            // Default
            $options = array('delivery_receipt' => array(
                'lang' => $this->lang,
                'cert_type' => 'D',
                'email' => 'INTERNALID'
            ));
        } else {
            // Ensure delivery_receipt is defined
            if (!array_key_exists('delivery_receipt', $options)) {
                // Default
                $options['delivery_receipt'] = array(
                    'lang' => $this->lang,
                    'cert_type' => 'D',
                    'email' => 'INTERNALID'
                );
            }
        }

        // Id, dst and text are verified inside
        return $this->mt($id, $dst, $text, $options);
    }

    // $queued = $sender->scheduledSMS($id, $dst, $text, $options);
    public function scheduledSMS($id, $dst, $text, $options = array())
    {
        // Default
        if (empty($options)) {
            throw new \InvalidArgumentException("Empty schedule option!");
        } else {
            // Ensure schedule is defined
            if (!array_key_exists('schedule', $options)) {
                throw new \InvalidArgumentException("Empty schedule option!");
            }
        }

        // Id, dst and text are verified inside
        return $this->mt($id, $dst, $text, $options);
    }

    // $queued = $sender->mt($id, $dst, $text, $options);
    public function mt($id, $dst, $text, $options = array())
    {
        $json = $this->make_json_mt($id, $dst, $text, $options);
        $this->logger->debug('json: '. $this->protect_json($json) ."\n");
        return $this->response_parser($this->do_request(SERVICE_SMS, urlencode($json)));
    }

    // mmt alias
    public function mms($id, $dst, $text, $subject, $attachment, $options = array())
    {
        return $this->mmt($id, $dst, $text, $subject, $attachment, $options);
    }

    // $queued = $sender->mmt($id, $dst, $text, $subject, $attachment, $options='');
    public function mmt($id, $dst, $text, $subject, $attachment, $options = array())
    {
        $json = $this->make_json_mmt($id, $dst, $text, $subject, $attachment, $options);
        $this->logger->debug('json: '. $this->protect_json($json) ."\n");
        return $this->response_parser($this->do_request(SERVICE_MMS, urlencode($json)));
    }

    /************************************
     * Get status of a message request  *
     *                                  *
     ************************************/
    public function getStatusSMS($id)
    {
        $json = $this->make_json_status('mt', $id);
        $this->logger->debug('json: '. $this->protect_json($json) ."\n");
        return $this->response_parser_status('mt', $id, $this->do_request(SERVICE_MSG, urlencode($json)));
    }

    public function getStatusMMS($id)
    {
        $json = $this->make_json_status('mmt', $id);
        $this->logger->debug('json: '. $this->protect_json($json) ."\n");
        return $this->response_parser_status('mmt', $id, $this->do_request(SERVICE_MSG, urlencode($json)));
    }

    // $status = $sender->getStatusScheduled($id);
    public function getStatusScheduled($id)
    {
        $json = $this->make_json_status('sched', $id);
        $this->logger->debug('json: '. $this->protect_json($json) ."\n");
        return $this->response_parser_status('sched', $id, $this->do_request(SERVICE_MSG, urlencode($json)));
    }

    public function getStatusDescription($status)
    {
        if (!array_key_exists($status, self::$statuses)) {
            throw new \InvalidArgumentException("Undefined status value: ". $status);
        }
        return self::$statuses[$status]['description'];
    }

    public function getStatusCode($status)
    {
        if (!array_key_exists($status, self::$statuses)) {
            throw new \InvalidArgumentException("Undefined status value: ". $status);
        }
        return self::$statuses[$status]['code'];
    }

    // Registered lang
    public function setLang($lang)
    {
        $this->lang = $this->check_lang($lang);
    }

    public function getLang()
    {
        return $this->lang;
    }

    // $filename with path
    public function setLogger($logger)
    {
        $this->logger = new Logger($logger);
    }

    // Attachment content must be encoded in base64
    public function getFileContentBase64($file)
    {
        $file_content = file_get_contents($file);

        if ($file_content === false) {
            throw new \InvalidArgumentException("Invalid file!");
        } elseif ($file_content === null) {
            throw new \InvalidArgumentException("Function file_get_contents is disabled!");
        } elseif (empty($file_content)) {
            throw new \InvalidArgumentException("Empty file!");
        }

        return base64_encode($file_content);
    }

    /**
     * Protected functions
     */

    // Return json sms object
    // Throw InvalidArgumentException
    protected function make_json_mt($id, $dst, $text, $options = array())
    {
        if (empty($id)) {
            throw new \InvalidArgumentException("Empty user_id!");
        }

        if (empty($dst)) {
            throw new \InvalidArgumentException("Empty recipient!");
        }

        if (empty($text)) {
            throw new \InvalidArgumentException("Empty text!");
        }

        if (!empty($options)) {
            $this->check_options($options);

            if (array_key_exists('src', $options)) {
                $options['src'] = $this->check_src($options['src']);
            } else {
                // If not custom src => enable allow_answer
                $options['allow_answer'] = '1';
            }

            if (array_key_exists('schedule', $options)) {
                $options['schedule'] = $this->check_schedule($options['schedule']);
            }

            if (array_key_exists('unicode', $options)) {
                $options['unicode'] = $this->toBool($options['unicode']);
            }
        }

        $options['user'] = $this->user;
        $options['password'] = $this->password;
        $options['user_id'] = $id;
        $options['dst'] = $this->make_dst($dst);

        $options = array_merge($options, $this->make_text($text, $options));

        return json_encode(array('sms' => $options));
    }

    // Return json mms object
    // Throw InvalidArgumentException
    protected function make_json_mmt($id, $dst, $text, $subject, $attachment, $options = array())
    {
        if (empty($id)) {
            throw new \InvalidArgumentException("Empty user_id!");
        }

        if (empty($dst)) {
            throw new \InvalidArgumentException("Empty recipient!");
        }

        if (empty($text)) {
            throw new \InvalidArgumentException("Empty text!");
        }

        if (empty($subject)) {
            throw new \InvalidArgumentException("Empty subject!");
        }

        if (empty($attachment)) {
            throw new \InvalidArgumentException("Empty attachment!");
        } elseif (!is_array($attachment)) {
            throw new \InvalidArgumentException("Invalid attachment format!");
        } else {
            // Throw an error if invalid format
            $this->check_attachment($attachment);
        }

        if (!empty($options)) {
            $this->check_options($options);
        }

        if (!is_string($text)) {
            throw new \InvalidArgumentException("Unknown text format!");
        }

        if (!is_string($subject)) {
            throw new \InvalidArgumentException("Unknown subject format!");
        }

        $options = array_merge($options, $this->make_text($text, $options));

        $options['user'] = $this->user;
        $options['password'] = $this->password;
        $options['user_id'] = $id;
        $options['dst'] = $this->make_dst($dst);
        $options['subject'] = $subject;
        $options['attachment'] = $attachment;

        return json_encode(array('mms' => $options));
    }

    protected function make_json_status($request, $id)
    {
        $options = array();
        $options['user'] = $this->user;
        $options['password'] = $this->password;
        $options['user_id'] = $id;
        $options['request'] = $request;
        return json_encode($options);
    }

    protected function make_dst($dst)
    {
        $value = '';
        $aNum = array();
        if (is_array($dst)) {
            foreach ($dst as $value) {
                $value = $this->check_number($value);
                if (!empty($value)) {
                    array_push($aNum, $value);
                }
            }
        } elseif (is_string($dst)) {
            $value = $this->check_number($dst);
            if (!empty($value)) {
                array_push($aNum, $value);
            }
        } else {
            throw new \InvalidArgumentException("Unknown recipient format!");
        }

        if (empty($aNum)) {
            throw new \InvalidArgumentException("Empty recipient!");
        }

        return array('num' => $aNum);
    }

    protected function make_text($text, $options)
    {
        if (!function_exists("mb_convert_encoding")) {
            throw new \Exception("No php-mbstring module found!");
        }
        if (!is_string($text)) {
            throw new \InvalidArgumentException("Unknown text format!");
        }

        $data_coding = '';

        if (array_key_exists('unicode', $options)) {
            if ($options['unicode'] == true) {
                $data_coding = 'unicode';
            }
        }

        // We'll send base64encoded texts with a charset.
        $encoding = 'base64';

        if ($data_coding == '') {
            $charset = 'iso-8859-1';
            $text = base64_encode(mb_convert_encoding($text, "ISO-8859-1", mb_detect_encoding($text, "UTF-8, ISO-8859-1, ISO-8859-15", true)));
            return array('txt' => $text, 'encoding' => $encoding, 'charset' => $charset);
        } else {
            $charset = 'UTF-16';
            $text = base64_encode(mb_convert_encoding($text, "UTF-16", mb_detect_encoding($text, "UTF-8, ISO-8859-1, ISO-8859-15", true)));
            return array('txt' => $text, 'encoding' => $encoding, 'charset' => $charset, 'data_coding' => $data_coding);
        }
    }

    /*
    "delivery_receipt":array(
        "lang":"ES",
        "cert_type":"D",
        "email":"my@mail.com" // if not valid => set internalid
    )
    */
    protected function check_options(&$options)
    {
        if (array_key_exists('delivery_receipt', $options)) {
            if (array_key_exists('lang', $options['delivery_receipt'])) {
                $options['delivery_receipt']['lang'] = $this->check_lang($options['delivery_receipt']['lang']);
            } else {
                $options['delivery_receipt']['lang'] = $this->lang;
            }

            if (array_key_exists('email', $options['delivery_receipt'])) {
                $options['delivery_receipt']['email'] = $this->check_email($options['delivery_receipt']['email']);
            } else {
                $options['delivery_receipt']['email'] = 'INTERNALID';
            }

            if (array_key_exists('cert_type', $options['delivery_receipt'])) {
                $options['delivery_receipt']['cert_type'] = $this->check_registered_type($options['delivery_receipt']['cert_type']);
            } else {
                // $options['delivery_receipt']['cert_type'] = 'D';
            }
        }
    }

    /*
    "attachment":{
        "mime":"image/jpeg",
        "content":"CmFzZgphc2QKYXNkZgphc2RmCmFzZApmYQpzZGY="
    }
    */
    protected function check_attachment($attachment)
    {
        if (is_array($attachment)) {
            if (!array_key_exists('mime', $attachment)) {
                throw new \InvalidArgumentException("Invalid attachment format, unknown mimetype!");
            } else {
                // Supported MMS content types MMS may include the following content formats:
                switch ($attachment['mime']) {
                    case "image/gif":
                    case "image/png":
                    case "image/jpeg":
                    case "audio/amr":
                    case "audio/x-wav":
                    case "audio/mpeg":
                    case "audio/midi":
                    case "video/3gpp":
                    case "video/mpeg":
                        break;
                    default:
                        throw new \InvalidArgumentException("Invalid mimetype!");
                }
            }

            if (!array_key_exists('content', $attachment)) {
                throw new \InvalidArgumentException("Invalid attachment format, unknown content!");
            } else {
                if (empty($attachment['content'])) {
                    throw new \InvalidArgumentException("Empty attachment content!");
                } elseif (!$this->isBase64Encoded($attachment['content'])) {
                    throw new \InvalidArgumentException("Invalid attachment format, unknown content format!");
                }
            }
        } else {
            throw new \InvalidArgumentException("Invalid attachment format!");
        }
    }

    protected function isBase64Encoded($data)
    {
        if (strlen($data) < 15) {
            return false;
        }

        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
            return true;
        } else {
            return false;
        }
    }

    // D -> Registered
    // T -> Registered Contract
    protected function check_registered_type($type)
    {
        $ul = strtoupper($type);
        if (in_array($ul, self::$registered_types)) {
            return $ul;
        }
        throw new \InvalidArgumentException("Invalid registered type!");
    }

    protected function check_lang($lang)
    {
        $ul = strtoupper($lang);
        if (in_array($ul, self::$languages)) {
            return $ul;
        }
        throw new \InvalidArgumentException("Invalid lang!");
    }

    protected function check_src($newCustomizedSender)
    {
        $nCustomizedSender = str_replace(array("\n", "\r", "'", ".", "(", ")", "+"), "", trim($newCustomizedSender));

        if (strlen($nCustomizedSender) > 20) {
            $nCustomizedSender = substr($nCustomizedSender, 0, 20);
        }

        return $nCustomizedSender;
    }

    // Date format should be YYYYMMDDhhmm[+-]ZZzz
    protected function check_schedule($schedule)
    {
        if (!is_string($schedule)) {
            throw new \InvalidArgumentException("Unknown schedule format!");
        }

        // FORMAT YYYYMMDDHHmm
        if (preg_match('#^[0-9]{12}[\-\+]+[0-9]{4}$#', $schedule)) {
            $utc = date('O'); // system UTC
            if (intval($utc) >= 0) {
                $schedule = str_replace("+", "-", $schedule);
            } else {
                $schedule = str_replace("-", "+", $schedule);
            }
        } elseif (preg_match('#[0-9]{12}#', $schedule)) {
            // Transform the dateTime to UTC [+-] HHMM
            $utc = date('O'); // system UTC
            if (intval($utc) >= 0) {
                $utc = str_replace("+", "-", $utc);
            } else {
                $utc = str_replace("-", "+", $utc);
            }

            $schedule = $schedule . $utc;
        } else {
            throw new \InvalidArgumentException("Invalid schedule format");
        }

        return $schedule;
    }

    protected function check_prefix($prefix)
    {
        if (!is_string($prefix) && is_numeric($prefix)) {
            $prefix = "". $prefix;
        }

        if (strlen($prefix) == 0) {
            return false;
        }
        
        if ($prefix[0] == "+" || $prefix[0] == " ") {
            $prefix = substr($prefix, 1);
        } elseif (substr($prefix, 0, 2) == "00") {
            $prefix = substr($prefix, 2);
        }
        
        if (strlen($prefix) != 0 && is_numeric($prefix)) {
            return $prefix;
        }
        return false;
    }

    protected function check_number($num, $prefix = "+34")
    {
        if (!is_string($num) && is_numeric($num)) {
            $num = "". $num;
        }

        if (strlen($num) == 0) {
            return "";
        }

        //we need to check this before "trimming" the string.
        if ($num[0] == " ") {
            $firstWasSpace = true;
            $num = substr($num, 1);
        }

        $replaces = array("\n", "\r", "'", "\"", " ", ".", ";", ",", "(", ")", "-");
        $num = str_replace($replaces, "", trim($num));

        if (strlen($num) < MAX_LENGTH_PREMIUM_NUMBERS) {
            return "";
        }

        if ($num[0] == "+") {
            $num = substr($num, 1);
        } elseif ($num[0] . $num[1] == "00") {
            $num = substr($num, 2);
        } elseif (!isset($firstWasSpace)) {
            /**
             * As long as we haven't found "+", " " or "00" in the number
             * we'll assume they missed the prefix and we'll add it if
             * there is not.
             */
            $prefix = $this->check_prefix($prefix);
            if ($prefix !== false && substr($num, 0, strlen($prefix)) != $prefix) {
                $num = $prefix . $num;
            }
        }

        if (preg_match('/^[0-9]+$/', $num)) {
            return "+" . $num;
        }
        return "";
    }

    protected function check_email($email)
    {
        $email = trim($email);
        if (strtoupper($email) == 'INTERNAL') {
            return 'INTERNALID';
        }

        if (strtoupper($email) == 'INTERNALID') {
            return 'INTERNALID';
        }

        if (function_exists('filter_var')) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return 'INTERNALID'; // Not an email
            }
        } else {
            $r = preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
            if (!$r || $r === false) {
                return 'INTERNALID';
            }
        }

        return $email;
    }

    protected function toBool($var)
    {
        if (!is_string($var)) {
            return (bool) $var;
        }

        switch (strtolower($var)) {
            case '1':
            case 'true':
            case 'on':
            case 'yes':
            case 'y':
                return true;
            default:
                return false;
        }
    }

    protected function protect_json($json)
    {
        return str_replace($this->password, "censored password", $json);
    }

    protected function do_request($service, $json)
    {
        $http_options = array('http' => array(
            'method'  => 'POST',
            'header'  => array('Content-type: application/json', 'Accept: application/json'),
            'timeout' => 30,
            'content' => $json
        ));
        $context  = stream_context_create($http_options);
        return file_get_contents($service, false, $context);
    }

    protected function response_parser($response)
    {
        $result = @json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE || is_null($result)) {
            throw new \Exception("Invalid JSON response. Error:". json_last_error() .":". json_last_error_msg());
        }
        $this->logger->debug('response_parser type: '. $result->request .' code: '. $result->code ."\n");

        if ($result->code == 200) {
            $this->set_error(false);
            return true;
        }
        $this->set_error($result->status, $result->code);
        return false;
    }

    protected function set_error($error, $code = 0)
    {
        $this->error = $error; /* false or description */
        $this->errno = $code;  /* 0 or error code */
    }

    protected function response_parser_status($request, $id, $response)
    {
        $state = 'U'; // Default status: Unknown
        $result = @json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE || is_null($result)) {
            throw new \Exception("Invalid JSON response. Error:". json_last_error() .":". json_last_error_msg());
        }

        $this->logger->debug('id: '.$id.' type: '.$request.' code: '. $result->code .":". $result->status ."\n");

        if ($result->code == 200) {
            if (
                property_exists($result, "messages") &&
                gettype($result->messages) == "array" &&
                sizeof($result->messages) > 0
            ) {
                $state = $result->messages[0]->state;
            }
            $this->set_error(false);
        } else {
            $this->set_error($result->status, $result->code);
        }

        $this->logger->debug('id: '.$id.' type: '.$request.' state: '.$state."\n");
        return $state;
    }
}
