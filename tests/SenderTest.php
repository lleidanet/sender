<?php
/**
 * SenderTest.php
 * API PHP v4
 * @author Javier Sanahuja <jsanahuja@lleida.net>
 * @version 4.0
 *
 */

namespace lnst\Tests;

use PHPUnit\Framework\TestCase;
use lnst\Sender;
use lnst\Logger;

/**
 * Class to access protected methods and override 
 * not reproducible behaviors
 */
class SenderExt extends Sender
{
    /**
     * Customizing do_request response
     */
    private $response;

    private function set_response($response){
        $this->response = $response;
    }

    protected function do_request($service, $json)
    {
        return $this->response;
    }

    /**
     * Access to protected methods
     */
    public function public_make_json_mt($id, $dst, $text, $options = array())
    {
        return $this->make_json_mt($id, $dst, $text, $options);
    }
    
    public function public_make_json_mmt($id, $dst, $text, $subject, $attachment, $options = array())
    {
        return $this->make_json_mmt($id, $dst, $text, $subject, $attachment, $options);
    }

    public function public_make_json_status($request, $id)
    {
        return $this->make_json_status($request, $id);
    }

    public function public_make_dst($dst)
    {
        return $this->make_dst($dst);
    }

    public function public_make_text($text, $options)
    {
        return $this->make_text($text, $options);
    }

    public function public_check_options(&$options)
    {
        return $this->check_options($options);
    }

    public function public_check_attachment($attachment)
    {
        return $this->check_attachment($attachment);
    }

    public function public_isBase64Encoded($data)
    {
        return $this->isBase64Encoded($data);
    }
    
    public function public_check_registered_type($type)
    {
        return $this->check_registered_type($type);
    }
    
    public function public_check_lang($lang)
    {
        return $this->check_lang($lang);
    }

    public function public_check_src($newCustomizedSender)
    {
        return $this->check_src($newCustomizedSender);
    }

    public function public_check_schedule($schedule)
    {
        return $this->check_schedule($schedule);
    }

    public function public_check_prefix($prefix)
    {
        return $this->check_prefix($prefix);
    }

    public function public_check_number($num, $prefix = "+34")
    {
        return $this->check_number($num, $prefix = "+34");
    }

    public function public_check_email($email)
    {
        return $this->check_email($email);
    }

    public function public_toBool($var)
    {
        return $this->toBool($var);
    }

    public function public_response_parser($response){
        return $this->response_parser($response);
    }

    public function public_response_parser_status($request, $id, $response){
        return $this->response_parser_status($request, $id, $response);
    }
}

/**
 * Test class
 */
class SenderTest extends TestCase
{
    protected static $username;
    protected static $password;
    protected static $img;
    protected static $b64img;
    protected $instance;

    public static function setUpBeforeClass()
    {
        self::$username = "username";
        self::$password = "password";
        self::$img = dirname(__DIR__) . "/examples/image.png";
        self::$b64img = "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";
    }

    protected function setUp()
    {
        $this->instance = new SenderExt(self::$username, self::$password);
        $this->instance->setLogger("tests.log");
        $this->instance->setLogger(new Logger("tests.log"));
    }

    /** @test **/
    public function test_constructor()
    {
        
        try {
            new SenderExt("", self::$password);
            $this->fail("Empty username: No exception thrown");
        } catch (\Exception $e) {
        }
        
        try {
            new SenderExt(self::$username, "");
            $this->fail("Empty password: No exception thrown");
        } catch (\Exception $e) {
        }
    }

    /** @test **/
    public function test_status_getters()
    {
        foreach (SenderExt::$statuses as $key => $status) {
            $this->assertEquals(
                $this->instance->getStatusCode($key),
                $status["code"]
            );
            $this->assertEquals(
                $this->instance->getStatusDescription($key),
                $status["description"]
            );
        }

        try {
            $this->instance->getStatusCode("invalidStatusKey");
            $this->fail("Invalid status: No exception thrown");
        } catch (\Exception $e) {
        }

        try {
            $this->instance->getStatusDescription("invalidStatusKey");
            $this->fail("Invalid status: No exception thrown");
        } catch (\Exception $e) {
        }
    }

    /** @test **/
    public function test_base64()
    {
        $b64 = "";
        $this->assertEquals(
            $this->instance->getFileContentBase64(self::$img),
            self::$b64img
        );
        $this->assertTrue(
            $this->instance->public_isBase64Encoded(
                $this->instance->getFileContentBase64(self::$img)
            )
        );
        $this->assertFalse(
            $this->instance->public_isBase64Encoded("Not base64")
        );
        $this->assertFalse(
            $this->instance->public_isBase64Encoded("Neither a base64 encoded string")
        );
        $this->assertTrue(
            $this->instance->public_isBase64Encoded(self::$b64img)
        );
        $this->assertFalse(
            $this->instance->public_isBase64Encoded(file_get_contents(self::$img))
        );
    }

    /** @test */
    public function test_make_json_mt()
    {
        $id = time();
        $dst = array("+34666666666");
        $txt = "Test message";
        
        $options = array(
            "delivery_receipt" => array(
                "lang" => "EN",
                "email" => "test@domain.com",
                "cert_type" => "D"
            )
        );
        $options_src = array(
            "src" => "Sender",
            "delivery_receipt" => array(
                "lang" => "EN",
                "email" => "test@domain.com",
                "cert_type" => "D"
            )
        );
        $options_unicode = array(
            "unicode" => true,
            "delivery_receipt" => array(
                "lang" => "EN",
                "email" => "test@domain.com",
                "cert_type" => "D"
            )
        );

        $options_schedule = array(
            // within 5 minutes
            "schedule" => date('YmdHi', time() + 60*5),
            "unicode" => true,
            "delivery_receipt" => array(
                "lang" => "EN",
                "email" => "test@domain.com",
                "cert_type" => "D"
            )
        );

        // Invalid params
        try {
            $this->instance->public_make_json_mt("", "", "", array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {}
        try {
            $this->instance->public_make_json_mt($id, "", "", array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {}
        try {
            $this->instance->public_make_json_mt($id, $dst, "", array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {}

        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt),
            '{"sms":{"user":"username","password":"password","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"VGVzdCBtZXNzYWdl","encoding":"base64","charset":"iso-8859-1"}}'
        );
        
        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt, $options),
            '{"sms":{"delivery_receipt":{"lang":"EN","email":"test@domain.com","cert_type":"D"},"allow_answer":"1","user":"username","password":"password","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"VGVzdCBtZXNzYWdl","encoding":"base64","charset":"iso-8859-1"}}'
        );
        
        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt, $options_src),
            '{"sms":{"src":"Sender","delivery_receipt":{"lang":"EN","email":"test@domain.com","cert_type":"D"},"user":"username","password":"password","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"VGVzdCBtZXNzYWdl","encoding":"base64","charset":"iso-8859-1"}}'
        );
        
        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt, $options_unicode),
            '{"sms":{"unicode":true,"delivery_receipt":{"lang":"EN","email":"test@domain.com","cert_type":"D"},"allow_answer":"1","user":"username","password":"password","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"AFQAZQBzAHQAIABtAGUAcwBzAGEAZwBl","encoding":"base64","charset":"UTF-16","data_coding":"unicode"}}'
        );
        

        $GMTDiff = date("O");
        if(intval($GMTDiff) >= 0){
            $expectedSchedule = $options_schedule['schedule'] . str_replace("+", "-", $GMTDiff);
        }else{
            $expectedSchedule = $options_schedule['schedule'] . str_replace("-", "+", $GMTDiff);
        }
        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt, $options_schedule),
            '{"sms":{"schedule":"'. $expectedSchedule .'","unicode":true,"delivery_receipt":{"lang":"EN","email":"test@domain.com","cert_type":"D"},"allow_answer":"1","user":"username","password":"password","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"AFQAZQBzAHQAIABtAGUAcwBzAGEAZwBl","encoding":"base64","charset":"UTF-16","data_coding":"unicode"}}'
        );
    }

    /** @test */
    public function test_make_json_mmt()
    {
        $id = time();
        $dst = array("+34666666666");
        $txt = "Test message";
        $subject = "Test";
        $attachment = array(
            "mime" => "image/png",
            "content" => "iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAACXBIWXMAAAsTAAALEwEAmpwYAAAKT2lDQ1BQ=="
        );
        $options = array(
            "delivery_receipt" => array(
                "lang" => "EN",
                "email" => "test@domain.com",
                "cert_type" => "D"
            )
        );

        // Invalid params
        try {
            $this->instance->public_make_json_mmt("", "", "", "", array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {}
        try {
            $this->instance->public_make_json_mmt($id, "", "", "", array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {}
        try {
            $this->instance->public_make_json_mmt($id, $dst, "", "", array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {}
        try {
            $this->instance->public_make_json_mmt($id, $dst, $txt, "", array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {}
        try {
            $this->instance->public_make_json_mmt($id, $dst, $txt, $subject, array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {}
        try {
            $this->instance->public_make_json_mmt($id, $dst, $txt, $subject, "Invalid attachment");
            $this->fail("Invalid attachment: No exception thrown");
        } catch (\Exception $e) {}
        try {
            $this->instance->public_make_json_mmt($id, $dst, array("Invalid Text"), $subject, $attachment);
            $this->fail("Invalid text: No exception thrown");
        } catch (\Exception $e) {}
        try {
            $this->instance->public_make_json_mmt($id, $dst, $txt, array("Invalid Subject"), $attachment);
            $this->fail("Invalid subject: No exception thrown");
        } catch (\Exception $e) {}
        
        // Valid
        $this->assertEquals(
            $this->instance->public_make_json_mmt($id, $dst, $txt, $subject, $attachment, $options),
            '{"mms":{"delivery_receipt":{"lang":"EN","email":"test@domain.com","cert_type":"D"},"txt":"VGVzdCBtZXNzYWdl","encoding":"base64","charset":"iso-8859-1","user":"username","password":"password","user_id":'. $id .',"dst":{"num":["+34666666666"]},"subject":"Test","attachment":{"mime":"image\/png","content":"iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAACXBIWXMAAAsTAAALEwEAmpwYAAAKT2lDQ1BQ=="}}}'
        );
    }

    /** @test **/
    public function test_make_json_status()
    {
        $this->assertEquals(
            $this->instance->public_make_json_status("mt", "1234"),
            "{\"user\":\"". self::$username ."\",\"password\":\"". self::$password ."\",\"user_id\":\"1234\",\"request\":\"mt\"}"
        );
    }

    /** @test **/
    public function test_make_dst()
    {
        $this->assertEquals(
            $this->instance->public_make_dst("+34666666666"),
            array(
                "num" => array(
                    "+34666666666"
                )
            )
        );
        $this->assertEquals(
            $this->instance->public_make_dst(array("+34000000000", "+34666666666")),
            array(
                "num" => array(
                    "+34000000000",
                    "+34666666666"
                )
            )
        );
        
        // Empty dst
        try {
            $this->instance->public_make_dst(array());
            $this->fail("Empty dst: No exception thrown");
        } catch (\Exception $e) {
        }

        $this->expectException(\Exception::class);
        $this->instance->public_make_dst(2);
    }

    /** @test **/
    public function test_make_text()
    {
        $this->assertEquals(
            $this->instance->public_make_text("This is a test", array()),
            array(
                "txt" => "VGhpcyBpcyBhIHRlc3Q=",
                "encoding" => "base64",
                "charset" => "iso-8859-1"
            )
        );
        $this->assertEquals(
            $this->instance->public_make_text("This is a test", array("unicode" => true)),
            array(
                'txt' => "AFQAaABpAHMAIABpAHMAIABhACAAdABlAHMAdA==",
                'encoding' => "base64",
                'charset' => "UTF-16",
                'data_coding' => "unicode"
            )
        );
        $this->expectException(\Exception::class);
        $this->instance->public_make_text(2, []);
    }
    
    /** @test */
    public function test_check_options()
    {
        // Empty options
        $options = array(
            "delivery_receipt" => array()
        );
        $this->instance->public_check_options($options);
        $this->assertEquals(
            $options,
            array("delivery_receipt" => array(
                "lang" => $this->instance->getLang(),
                "email" => "INTERNALID"
            ))
        );

        // Invalid email
        $options = array(
            "delivery_receipt" => array(
                "email" => "plainaddress"
            )
        );
        $this->instance->public_check_options($options);
        $this->assertEquals(
            $options,
            array("delivery_receipt" => array(
                "lang" => $this->instance->getLang(),
                "email" => "INTERNALID"
            ))
        );

        // Invalid lang
        try {
            $options = array(
                "delivery_receipt" => array(
                    "lang" => "invalidLang"
                )
            );
            $this->instance->public_check_options($options);
            $this->fail("Invalid lang: No exception thrown");
        } catch (\Exception $e) {
        }

        // Invalid cert_type
        try {
            $options = array(
                "delivery_receipt" => array(
                    "cert_type" => "invalidCertType"
                )
            );
            $this->instance->public_check_options($options);
            $this->fail("Invalid cert_type: No exception thrown");
        } catch (\Exception $e) {
        }

        // Valid data
        $lang = Sender::$languages[rand(0, sizeof(Sender::$languages)-1)];
        $cert_type = Sender::$registered_types[rand(0, sizeof(Sender::$registered_types)-1)];
        $options = array(
            "delivery_receipt" => array(
                "lang" => $lang,
                "email" => "test@domain.com",
                "cert_type" => $cert_type
            )
        );
        $this->instance->public_check_options($options);
        $this->assertEquals(
            $options,
            array("delivery_receipt" => array(
                "lang" => $lang,
                "email" => "test@domain.com",
                "cert_type" => $cert_type
            ))
        );

        $this->assertTrue(true);
    }
    /** @test */
    public function test_check_attachment()
    {
        // No attachment
        try {
            $this->instance->public_check_attachment(1);
            $this->fail("Invalid attachment: No exception thrown");
        } catch (\Exception $e) {
        }

        // Missing keys
        try {
            $this->instance->public_check_attachment(array());
            $this->fail("Invalid attachment: No exception thrown");
        } catch (\Exception $e) {
        }

        try {
            $this->instance->public_check_attachment(array("mime" => "image/jpeg"));
            $this->fail("Invalid attachment: No exception thrown");
        } catch (\Exception $e) {
        }

        try {
            $this->instance->public_check_attachment(array("content" => ""));
            $this->fail("Invalid attachment: No exception thrown");
        } catch (\Exception $e) {
        }

        // Invalid contents
        try {
            $this->instance->public_check_attachment(array("mime" => "", "content" => ""));
            $this->fail("Invalid attachment: No exception thrown");
        } catch (\Exception $e) {
        }

        try {
            $this->instance->public_check_attachment(array("mime" => "image/png", "content" => ""));
            $this->fail("Invalid attachment: No exception thrown");
        } catch (\Exception $e) {
        }

        // Invalid base64
        try {
            $this->instance->public_check_attachment(array("mime" => "image/png", "content" => "notb64"));
            $this->fail("Invalid attachment: No exception thrown");
        } catch (\Exception $e) {
        }

        // Valid: Not expecting exception
        try {
            $this->instance->public_check_attachment(
                array(
                    "mime" => "image/png",
                    "content" => self::$b64img
                )
            );
            // No exception thrown
            $this->assertTrue(true);
        } catch (\Exception $e) {
            // Unexpected exception
            $this->fail("Exception on valid attachment", $e->getMessage());
        }
    }
    
    /** @test */
    public function test_check_registered_type()
    {
        foreach (Sender::$registered_types as $rtype) {
            $this->assertEquals(
                $this->instance->public_check_registered_type($rtype),
                $rtype
            );
            $this->assertEquals(
                $this->instance->public_check_registered_type(strtolower($rtype)),
                $rtype
            );
            
            if (!in_array($rtype."-", Sender::$languages)) {
                try {
                    $this->instance->public_check_registered_type($rtype."-");
                    $this->fail("Invalid cert_type: No exception thrown");
                } catch (\Exception $e) {
                }
            }
        }
    }

    /** @test */
    public function test_check_lang()
    {
        foreach (Sender::$languages as $lang) {
            $this->assertEquals(
                $this->instance->public_check_lang($lang),
                $lang
            );
            $this->assertEquals(
                $this->instance->public_check_lang(strtolower($lang)),
                $lang
            );
            
            if (!in_array(strrev($lang), Sender::$languages)) {
                try {
                    $this->instance->public_check_lang(strrev($lang));
                    $this->fail("Invalid lang: No exception thrown");
                } catch (\Exception $e) {
                }
            }
        }
    }

    /** @test */
    public function test_check_src()
    {
        $p1 = "ca455d0f37";
        $p2 = "c7336959fe";
        $tests = array(
            array(" ". $p1 ."\r\n". $p2,        $p1.$p2),
            array(" ". $p1 .".". $p2,           $p1.$p2),
            array(" ". $p1 ."+". $p2,           $p1.$p2),
            array("((". $p1 ."+". $p2 .") ",    $p1.$p2),
            array($p1.$p2.$p2.$p1,              $p1.$p2),
        );
        foreach ($tests as $test) {
            $this->assertEquals(
                $this->instance->public_check_src($test[0]),
                $test[1]
            );
        }
    }

    /** @test */
    public function test_check_schedule()
    {
        // +0100 (Spain)
        date_default_timezone_set("Europe/Madrid");
        $tests = array(
            // YYYYMMDDHHmm+ZZzz
            array("202001021319+0100", "202001021319-0100"),
            // YYYYMMDDHHmm-ZZzz
            array("202001021319-0100", "202001021319-0100"),
            // YYYYMMDDhhmm
            array("202001021319", "202001021319-". substr(date("O"), 1))
        );
        foreach ($tests as $test) {
            $this->assertEquals(
                $this->instance->public_check_schedule($test[0]),
                $test[1]
            );
        }
        // -0500 (Colombia)
        date_default_timezone_set("America/Bogota");
        $tests = array(
            // YYYYMMDDHHmm+ZZzz
            array("202001021319+0100", "202001021319+0100"),
            // YYYYMMDDHHmm-ZZzz
            array("202001021319-0100", "202001021319+0100"),
            // YYYYMMDDhhmm
            array("202001021319", "202001021319+". substr(date("O"), 1))
        );
        foreach ($tests as $test) {
            $this->assertEquals(
                $this->instance->public_check_schedule($test[0]),
                $test[1]
            );
        }
        
        // Invalid "int" formats
        try {
            $this->instance->public_check_schedule(time());
            $this->fail("Invalid schedule: No exception thrown");
        } catch (\Exception $e) {
        }

        // Invalid date format
        try {
            $this->instance->public_check_schedule(date("Ymdhiss", time()));
            $this->fail("Invalid schedule: No exception thrown");
        } catch (\Exception $e) {
        }

        // Invalid format
        try {
            $this->instance->public_check_schedule("Invalid format");
            $this->fail("Invalid schedule: No exception thrown");
        } catch (\Exception $e) {
        }
    }

    /** @test */
    public function test_check_prefix()
    {
        $tests = array(
            array("", false),
            array("+34", "34"),
            array(" 34", "34"),
            array("0034", "34"),
            array("+", false),
            array("00", false),
            array("1", "1"),
            array("34", "34"),
            array(34, "34"),
            array(1, "1"),
        );
        foreach ($tests as $test) {
            $this->assertEquals(
                $this->instance->public_check_prefix($test[0]),
                $test[1]
            );
        }
    }

    /** @test */
    public function test_check_number()
    {
        $tests = array(
            // invalid numbers
            array("", ""),
            array("600000000a", ""),
            // premium numbers
            array(123, ""),
            array("1234", ""),
            // with prefix
            array("+34600000000",  "+34600000000"),
            array(" 34600000000",  "+34600000000"),
            array("0034600000000", "+34600000000"),
            // undefined prefix
            array("600000000",      "+34600000000"),
            array("34600000000",    "+34600000000")
        );
        foreach ($tests as $test) {
            $this->assertEquals(
                $this->instance->public_check_number($test[0]),
                $test[1]
            );
        }
    }

    /** @test */
    public function test_check_email()
    {
        $valid = array(
            "email@domain.com",
            "firstname.lastname@domain.com",
            "email@subdomain.domain.com",
            "firstname+lastname@domain.com",
            "1234567890@domain.com",
            "email@domain-one.com",
            "_______@domain.com",
            "email@domain.name",
            "email@domain.co.jp",
            "firstname-lastname@domain.com"
        );
        $invalid = array(
            "plainaddress",
            "#@%^%#$@#$@#.com",
            "@domain.com",
            "Joe Smith <email@domain.com>",
            "email.domain.com",
            "email@domain@domain.com",
            ".email@domain.com",
            "email.@domain.com",
            "email..email@domain.com",
            "あいうえお@domain.com",
            "email@domain.com (Joe Smith)",
            "email@domain",
            "email@-domain.com",
            "email@111.222.333.44444",
            "email@domain..com",
            "INTERNAL",
            "INTERNALID"
        );
        foreach ($valid as $email) {
            $this->assertEquals($this->instance->public_check_email($email), $email);
        }
        foreach ($invalid as $email) {
            $this->assertEquals($this->instance->public_check_email($email), "INTERNALID");
        }
    }
    
    /** @test */
    public function test_toBool()
    {
        $test_true  = array(1, 10, true, "1", "true", "True", "on", "On", "yes", "YES", "y", "Y");
        $test_false = array(0, false, "0", "false", "False", "off", "Off", "no", "NO", "n", "N");

        foreach ($test_true as $test) {
            $this->assertTrue($this->instance->public_toBool($test));
        }
        foreach ($test_false as $test) {
            $this->assertFalse($this->instance->public_toBool($test));
        }
    }

    /** @test */
    public function test_response_parser()
    {
        $this->assertTrue(
            $this->instance->public_response_parser('{"status":"Success","code":200,"request":"test"}')
        );
        $this->assertFalse($this->instance->error);
        $this->assertEquals($this->instance->errno, 0);

        
        $this->assertFalse(
            $this->instance->public_response_parser('{"status":"Error!","code":1601,"request":"test"}')
        );
        $this->assertEquals($this->instance->error, "Error!");
        $this->assertEquals($this->instance->errno, 1601);
        
        // Invalid json
        $this->expectException(\Exception::class);
        $this->instance->public_response_parser("Invalid JSON Response");
    }

    public function test_response_parser_status(){

        $this->assertEquals(
            $this->instance->public_response_parser_status('mt', 1, '{"status":"Success","code":200,"request":"test","messages":[{"state":"P"}]}'),
            "P"
        );
        $this->assertFalse($this->instance->error);
        $this->assertEquals($this->instance->errno, 0);

        
        $this->assertEquals(
            $this->instance->public_response_parser_status('mmt', 1, '{"status":"Error!","code":1601,"request":"test"}'),
            "U" // Default state
        );
        $this->assertEquals($this->instance->error, "Error!");
        $this->assertEquals($this->instance->errno, 1601);
        
        // Invalid json
        $this->expectException(\Exception::class);
        $this->instance->public_response_parser_status('mmt', 1, "Invalid JSON Response");
    }
}
