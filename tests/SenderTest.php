<?php
/**
 * SenderTest.php
 * API PHP v4
 * @author Javier Sanahuja <jsanahuja@lleida.net>
 * @version 5.0.0
 *
 */

namespace lnst\Tests;

use PHPUnit\Framework\TestCase;
use lnst\Sender;
use lnst\Logger;

/**
 * Class to access protected methods
 */
class SenderExt extends Sender
{
    /**
     * Access to protected methods
     */
    public function public_make_json_mt($id, $dst, $text, $options = array())
    {
        return $this->make_json_mt($id, $dst, $text, $options);
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

    public function public_response_parser($response)
    {
        return $this->response_parser($response);
    }

    public function public_response_parser_status($request, $id, $response)
    {
        return $this->response_parser_status($request, $id, $response);
    }
}

/**
 * Test class
 */
class SenderTest extends TestCase
{
    protected static $username;
    protected static $apikey;
    protected $instance;

    public static function setUpBeforeClass()
    {
        self::$username = "username";
        self::$apikey = "apikey";
    }

    protected function setUp()
    {
        $this->instance = new SenderExt(self::$username, self::$apikey);
        $this->instance->setLogger("tests.log");
    }

    /** @test **/
    public function test_constructor()
    {
        try {
            new SenderExt("", self::$apikey);
            $this->fail("Empty username: No exception thrown");
        } catch (\Exception $e) {
        }

        try {
            new SenderExt(self::$username, "");
            $this->fail("Empty apikey: No exception thrown");
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
        } catch (\Exception $e) {
        }
        try {
            $this->instance->public_make_json_mt($id, "", "", array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {
        }
        try {
            $this->instance->public_make_json_mt($id, $dst, "", array());
            $this->fail("Empty parameter: No exception thrown");
        } catch (\Exception $e) {
        }

        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt),
            '{"sms":{"user":"username","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"VGVzdCBtZXNzYWdl","encoding":"base64","charset":"iso-8859-1"}}'
        );

        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt, $options),
            '{"sms":{"delivery_receipt":{"lang":"EN","email":"test@domain.com","cert_type":"D"},"allow_answer":"1","user":"username","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"VGVzdCBtZXNzYWdl","encoding":"base64","charset":"iso-8859-1"}}'
        );

        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt, $options_src),
            '{"sms":{"src":"Sender","delivery_receipt":{"lang":"EN","email":"test@domain.com","cert_type":"D"},"user":"username","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"VGVzdCBtZXNzYWdl","encoding":"base64","charset":"iso-8859-1"}}'
        );

        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt, $options_unicode),
            '{"sms":{"unicode":true,"delivery_receipt":{"lang":"EN","email":"test@domain.com","cert_type":"D"},"allow_answer":"1","user":"username","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"AFQAZQBzAHQAIABtAGUAcwBzAGEAZwBl","encoding":"base64","charset":"UTF-16","data_coding":"unicode"}}'
        );


        $GMTDiff = date("O");
        if (intval($GMTDiff) >= 0) {
            $expectedSchedule = $options_schedule['schedule'] . str_replace("+", "-", $GMTDiff);
        } else {
            $expectedSchedule = $options_schedule['schedule'] . str_replace("-", "+", $GMTDiff);
        }
        $this->assertEquals(
            $this->instance->public_make_json_mt($id, $dst, $txt, $options_schedule),
            '{"sms":{"schedule":"'. $expectedSchedule .'","unicode":true,"delivery_receipt":{"lang":"EN","email":"test@domain.com","cert_type":"D"},"allow_answer":"1","user":"username","user_id":'. $id .',"dst":{"num":["+34666666666"]},"txt":"AFQAZQBzAHQAIABtAGUAcwBzAGEAZwBl","encoding":"base64","charset":"UTF-16","data_coding":"unicode"}}'
        );
    }

    /** @test **/
    public function test_make_json_status()
    {
        $this->assertEquals(
            $this->instance->public_make_json_status("mt", "1234"),
            "{\"user\":\"". self::$username ."\",\"user_id\":\"1234\",\"request\":\"mt\"}"
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

    public function test_response_parser_status()
    {
        $this->assertEquals(
            $this->instance->public_response_parser_status('mt', 1, '{"status":"Success","code":200,"request":"test","messages":[{"state":"P"}]}'),
            "P"
        );
        $this->assertFalse($this->instance->error);
        $this->assertEquals($this->instance->errno, 0);


        $this->assertEquals(
            $this->instance->public_response_parser_status('mt', 1, '{"status":"Error!","code":1601,"request":"test"}'),
            "U" // Default state
        );
        $this->assertEquals($this->instance->error, "Error!");
        $this->assertEquals($this->instance->errno, 1601);

        // Invalid json
        $this->expectException(\Exception::class);
        $this->instance->public_response_parser_status('mt', 1, "Invalid JSON Response");
    }
}
