<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\Tests\DateFormatter;

use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;
use Symfony\Component\Intl\Globals\IntlGlobals;
use Symfony\Component\Intl\Intl;

/**
 * Test case for IntlDateFormatter implementations.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
abstract class AbstractIntlDateFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * When a time zone is not specified, it uses the system default however it returns null in the getter method
     * @covers Symfony\Component\Intl\DateFormatter\IntlDateFormatter::getTimeZoneId
     * @see StubIntlDateFormatterTest::testDefaultTimeZoneIntl()
     */
    public function testConstructorDefaultTimeZone()
    {
        $formatter = $this->getDateFormatter('en', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

        // In PHP 5.5 default timezone depends on `date_default_timezone_get()` method
        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $this->assertEquals(date_default_timezone_get(), $formatter->getTimeZoneId());
        } else {
            $this->assertNull($formatter->getTimeZoneId());
        }
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormat($pattern, $timestamp, $expected)
    {
        $errorCode = IntlGlobals::U_ZERO_ERROR;
        $errorMessage = 'U_ZERO_ERROR';

        $formatter = $this->getDefaultDateFormatter($pattern);
        $this->assertSame($expected, $formatter->format($timestamp));
        $this->assertIsIntlSuccess($formatter, $errorMessage, $errorCode);
    }

    public function formatProvider()
    {
        $formatData = array(
            /* general */
            array('y-M-d', 0, '1970-1-1'),
            array("EEE, MMM d, ''yy", 0, "Thu, Jan 1, '70"),
            array('h:mm a', 0, '12:00 AM'),
            array('yyyyy.MMMM.dd hh:mm aaa', 0, '01970.January.01 12:00 AM'),

            /* escaping */
            array("'M'", 0, 'M'),
            array("'yy'", 0, 'yy'),
            array("'''yy'", 0, "'yy"),
            array("''y", 0, "'1970"),
            array("''yy", 0, "'70"),
            array("H 'o'' clock'", 0, "0 o' clock"),

            /* month */
            array('M', 0, '1'),
            array('MM', 0, '01'),
            array('MMM', 0, 'Jan'),
            array('MMMM', 0, 'January'),
            array('MMMMM', 0, 'J'),
            array('MMMMMM', 0, '000001'),

            array('L', 0, '1'),
            array('LL', 0, '01'),
            array('LLL', 0, 'Jan'),
            array('LLLL', 0, 'January'),
            array('LLLLL', 0, 'J'),
            array('LLLLLL', 0, '000001'),

            /* year */
            array('y', 0, '1970'),
            array('yy', 0, '70'),
            array('yyy', 0, '1970'),
            array('yyyy', 0, '1970'),
            array('yyyyy', 0, '01970'),
            array('yyyyyy', 0, '001970'),

            /* day */
            array('d', 0, '1'),
            array('dd', 0, '01'),
            array('ddd', 0, '001'),

            /* quarter */
            array('Q', 0, '1'),
            array('QQ', 0, '01'),
            array('QQQ', 0, 'Q1'),
            array('QQQQ', 0, '1st quarter'),
            array('QQQQQ', 0, '1st quarter'),

            array('q', 0, '1'),
            array('qq', 0, '01'),
            array('qqq', 0, 'Q1'),
            array('qqqq', 0, '1st quarter'),
            array('qqqqq', 0, '1st quarter'),

            // 4 months
            array('Q', 7776000, '2'),
            array('QQ', 7776000, '02'),
            array('QQQ', 7776000, 'Q2'),
            array('QQQQ', 7776000, '2nd quarter'),

            // 7 months
            array('QQQQ', 15638400, '3rd quarter'),

            // 10 months
            array('QQQQ', 23587200, '4th quarter'),

            /* 12-hour (1-12) */
            array('h', 0, '12'),
            array('hh', 0, '12'),
            array('hhh', 0, '012'),

            array('h', 1, '12'),
            array('h', 3600, '1'),
            array('h', 43200, '12'), // 12 hours

            /* day of year */
            array('D', 0, '1'),
            array('D', 86400, '2'), // 1 day
            array('D', 31536000, '1'), // 1 year
            array('D', 31622400, '2'), // 1 year + 1 day

            /* day of week */
            array('E', 0, 'Thu'),
            array('EE', 0, 'Thu'),
            array('EEE', 0, 'Thu'),
            array('EEEE', 0, 'Thursday'),
            array('EEEEE', 0, 'T'),
            array('EEEEEE', 0, 'Th'),

            array('E', 1296540000, 'Tue'), // 2011-02-01
            array('E', 1296950400, 'Sun'), // 2011-02-06

            /* am/pm marker */
            array('a', 0, 'AM'),
            array('aa', 0, 'AM'),
            array('aaa', 0, 'AM'),
            array('aaaa', 0, 'AM'),

            // 12 hours
            array('a', 43200, 'PM'),
            array('aa', 43200, 'PM'),
            array('aaa', 43200, 'PM'),
            array('aaaa', 43200, 'PM'),

            /* 24-hour (0-23) */
            array('H', 0, '0'),
            array('HH', 0, '00'),
            array('HHH', 0, '000'),

            array('H', 1, '0'),
            array('H', 3600, '1'),
            array('H', 43200, '12'),
            array('H', 46800, '13'),

            /* 24-hour (1-24) */
            array('k', 0, '24'),
            array('kk', 0, '24'),
            array('kkk', 0, '024'),

            array('k', 1, '24'),
            array('k', 3600, '1'),
            array('k', 43200, '12'),
            array('k', 46800, '13'),

            /* 12-hour (0-11) */
            array('K', 0, '0'),
            array('KK', 0, '00'),
            array('KKK', 0, '000'),

            array('K', 1, '0'),
            array('K', 3600, '1'),
            array('K', 43200, '0'), // 12 hours

            /* minute */
            array('m', 0, '0'),
            array('mm', 0, '00'),
            array('mmm', 0, '000'),

            array('m', 1, '0'),
            array('m', 60, '1'),
            array('m', 120, '2'),
            array('m', 180, '3'),
            array('m', 3600, '0'),
            array('m', 3660, '1'),
            array('m', 43200, '0'), // 12 hours

            /* second */
            array('s', 0, '0'),
            array('ss', 0, '00'),
            array('sss', 0, '000'),

            array('s', 1, '1'),
            array('s', 2, '2'),
            array('s', 5, '5'),
            array('s', 30, '30'),
            array('s', 59, '59'),
            array('s', 60, '0'),
            array('s', 120, '0'),
            array('s', 180, '0'),
            array('s', 3600, '0'),
            array('s', 3601, '1'),
            array('s', 3630, '30'),
            array('s', 43200, '0'), // 12 hours

            // general
            array("yyyy.MM.dd 'at' HH:mm:ss zzz", 0, '1970.01.01 at 00:00:00 GMT'),
            array('K:mm a, z', 0, '0:00 AM, GMT'),

            // timezone
            array('z', 0, 'GMT'),
            array('zz', 0, 'GMT'),
            array('zzz', 0, 'GMT'),
            array('zzzz', 0, 'GMT'),
            array('zzzzz', 0, 'GMT'),
        );

        // As of PHP 5.3.4, IntlDateFormatter::format() accepts DateTime instances
        if (version_compare(PHP_VERSION, '5.3.4', '>=')) {
            $dateTime = new \DateTime('@0');

            /* general, DateTime */
            $formatData[] = array('y-M-d', $dateTime, '1970-1-1');
            $formatData[] = array("EEE, MMM d, ''yy", $dateTime, "Thu, Jan 1, '70");
            $formatData[] = array('h:mm a', $dateTime, '12:00 AM');
            $formatData[] = array('yyyyy.MMMM.dd hh:mm aaa', $dateTime, '01970.January.01 12:00 AM');

            $formatData[] = array("yyyy.MM.dd 'at' HH:mm:ss zzz", $dateTime, '1970.01.01 at 00:00:00 GMT');
            $formatData[] = array('K:mm a, z', $dateTime, '0:00 AM, GMT');
        }

        return $formatData;
    }

    /**
     * @dataProvider formatErrorProvider
     */
    public function testFormatIllegalArgumentError($pattern, $timestamp, $errorMessage)
    {
        $errorCode = IntlGlobals::U_ILLEGAL_ARGUMENT_ERROR;

        $formatter = $this->getDefaultDateFormatter($pattern);
        $this->assertFalse($formatter->format($timestamp));
        $this->assertIsIntlFailure($formatter, $errorMessage, $errorCode);
    }

    public function formatErrorProvider()
    {
        // With PHP 5.5 IntlDateFormatter accepts empty values ('0')
        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            return array(
                array('y-M-d', 'foobar', 'datefmt_format: string \'foobar\' is not numeric, which would be required for it to be a valid date: U_ILLEGAL_ARGUMENT_ERROR')
            );
        }

        $message = 'datefmt_format: takes either an array  or an integer timestamp value : U_ILLEGAL_ARGUMENT_ERROR';

        if (version_compare(PHP_VERSION, '5.3.4', '>=')) {
            $message = 'datefmt_format: takes either an array or an integer timestamp value or a DateTime object: U_ILLEGAL_ARGUMENT_ERROR';
        }

        return array(
            array('y-M-d', '0', $message),
            array('y-M-d', 'foobar', $message),
        );
    }

    /**
     * @dataProvider formatWithTimezoneProvider
     */
    public function testFormatWithTimezone($timestamp, $timezone, $expected)
    {
        $pattern = 'yyyy-MM-dd HH:mm:ss';
        $formatter = $this->getDateFormatter('en', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT, $timezone, IntlDateFormatter::GREGORIAN, $pattern);
        $this->assertSame($expected, $formatter->format($timestamp));
    }

    public function formatWithTimezoneProvider()
    {
        $data = array(
            array(0, 'UTC', '1970-01-01 00:00:00'),
            array(0, 'GMT', '1970-01-01 00:00:00'),
            array(0, 'GMT-03:00', '1969-12-31 21:00:00'),
            array(0, 'GMT+03:00', '1970-01-01 03:00:00'),
            array(0, 'Europe/Zurich', '1970-01-01 01:00:00'),
            array(0, 'Europe/Paris', '1970-01-01 01:00:00'),
            array(0, 'Africa/Cairo', '1970-01-01 02:00:00'),
            array(0, 'Africa/Casablanca', '1970-01-01 00:00:00'),
            array(0, 'Africa/Djibouti', '1970-01-01 03:00:00'),
            array(0, 'Africa/Johannesburg', '1970-01-01 02:00:00'),
            array(0, 'America/Antigua', '1969-12-31 20:00:00'),
            array(0, 'America/Toronto', '1969-12-31 19:00:00'),
            array(0, 'America/Vancouver', '1969-12-31 16:00:00'),
            array(0, 'Asia/Aqtau', '1970-01-01 05:00:00'),
            array(0, 'Asia/Bangkok', '1970-01-01 07:00:00'),
            array(0, 'Asia/Dubai', '1970-01-01 04:00:00'),
            array(0, 'Australia/Brisbane', '1970-01-01 10:00:00'),
            array(0, 'Australia/Eucla', '1970-01-01 08:45:00'),
            array(0, 'Australia/Melbourne', '1970-01-01 10:00:00'),
            array(0, 'Europe/Berlin', '1970-01-01 01:00:00'),
            array(0, 'Europe/Dublin', '1970-01-01 01:00:00'),
            array(0, 'Europe/Warsaw', '1970-01-01 01:00:00'),
            array(0, 'Pacific/Fiji', '1970-01-01 12:00:00'),
        );

        // As of PHP 5.5, intl ext no longer fallbacks invalid time zones to UTC
        if (!version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            // When time zone not exists, uses UTC by default
            $data[] = array(0, 'Foo/Bar', '1970-01-01 00:00:00');
            $data[] = array(0, 'UTC+04:30', '1970-01-01 00:00:00');
            $data[] = array(0, 'UTC+04:AA', '1970-01-01 00:00:00');
        }

        return $data;
    }

    public function testFormatWithGmtTimezone()
    {
        $formatter = $this->getDefaultDateFormatter('zzzz');

        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $formatter->setTimeZone('GMT+03:00');
        } else {
            $formatter->setTimeZoneId('GMT+03:00');
        }

        $this->assertEquals('GMT+03:00', $formatter->format(0));
    }

    public function testFormatWithGmtTimeZoneAndMinutesOffset()
    {
        $formatter = $this->getDefaultDateFormatter('zzzz');

        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $formatter->setTimeZone('GMT+00:30');
        } else {
            $formatter->setTimeZoneId('GMT+00:30');
        }

        $this->assertEquals('GMT+00:30', $formatter->format(0));
    }

    public function testFormatWithNonStandardTimezone()
    {
        $formatter = $this->getDefaultDateFormatter('zzzz');

        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $formatter->setTimeZone('Pacific/Fiji');
        } else {
            $formatter->setTimeZoneId('Pacific/Fiji');
        }

        $this->assertEquals('Fiji Standard Time', $formatter->format(0));
    }

    public function testFormatWithConstructorTimezone()
    {
        $formatter = $this->getDateFormatter('en', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT, 'UTC');
        $formatter->setPattern('yyyy-MM-dd HH:mm:ss');

        $this->assertEquals(
            $this->getDateTime(0)->format('Y-m-d H:i:s'),
            $formatter->format(0)
        );
    }

    public function testFormatWithTimezoneFromEnvironmentVariable()
    {
        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $this->markTestSkipped('IntlDateFormatter in PHP 5.5 no longer depends on TZ environment.');
        }

        $tz = getenv('TZ');
        putenv('TZ=Europe/London');

        $formatter = $this->getDateFormatter('en', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
        $formatter->setPattern('yyyy-MM-dd HH:mm:ss');

        $this->assertEquals(
            $this->getDateTime(0)->format('Y-m-d H:i:s'),
            $formatter->format(0)
        );

        $this->assertEquals('Europe/London', getenv('TZ'));

        // Restores TZ.
        putenv('TZ='.$tz);
    }

    public function testFormatWithTimezoneFromPhp()
    {
        if (!version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $this->markTestSkipped('Only in PHP 5.5 IntlDateFormatter depends on default timezone (`date_default_timezone_get()`).');
        }

        $tz = date_default_timezone_get();
        date_default_timezone_set('Europe/London');

        $formatter = $this->getDateFormatter('en', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
        $formatter->setPattern('yyyy-MM-dd HH:mm:ss');

        $this->assertEquals(
            $this->getDateTime(0)->format('Y-m-d H:i:s'),
            $formatter->format(0)
        );

        $this->assertEquals('Europe/London', date_default_timezone_get());

        // Restores TZ.
        date_default_timezone_set($tz);
    }

    /**
     * @dataProvider dateAndTimeTypeProvider
     */
    public function testDateAndTimeType($timestamp, $datetype, $timetype, $expected)
    {
        $formatter = $this->getDateFormatter('en', $datetype, $timetype, 'UTC');
        $this->assertSame($expected, $formatter->format($timestamp));
    }

    public function dateAndTimeTypeProvider()
    {
        return array(
            array(0, IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Thursday, January 1, 1970'),
            array(0, IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'January 1, 1970'),
            array(0, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, 'Jan 1, 1970'),
            array(0, IntlDateFormatter::SHORT, IntlDateFormatter::NONE, '1/1/70'),
            array(0, IntlDateFormatter::NONE, IntlDateFormatter::FULL, '12:00:00 AM GMT'),
            array(0, IntlDateFormatter::NONE, IntlDateFormatter::LONG, '12:00:00 AM GMT'),
            array(0, IntlDateFormatter::NONE, IntlDateFormatter::MEDIUM, '12:00:00 AM'),
            array(0, IntlDateFormatter::NONE, IntlDateFormatter::SHORT, '12:00 AM'),
        );
    }

    public function testGetCalendar()
    {
        $formatter = $this->getDefaultDateFormatter();
        $this->assertEquals(IntlDateFormatter::GREGORIAN, $formatter->getCalendar());
    }

    public function testGetDateType()
    {
        $formatter = $this->getDateFormatter('en', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $this->assertEquals(IntlDateFormatter::FULL, $formatter->getDateType());
    }

    public function testGetLocale()
    {
        $formatter = $this->getDefaultDateFormatter();
        $this->assertEquals('en', $formatter->getLocale());
    }

    public function testGetPattern()
    {
        $formatter = $this->getDateFormatter('en', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'UTC', IntlDateFormatter::GREGORIAN, 'yyyy-MM-dd');
        $this->assertEquals('yyyy-MM-dd', $formatter->getPattern());
    }

    public function testGetTimeType()
    {
        $formatter = $this->getDateFormatter('en', IntlDateFormatter::NONE, IntlDateFormatter::FULL);
        $this->assertEquals(IntlDateFormatter::FULL, $formatter->getTimeType());
    }

    /**
     * @dataProvider parseProvider
     */
    public function testParse($pattern, $value, $expected)
    {
        $errorCode = IntlGlobals::U_ZERO_ERROR;
        $errorMessage = 'U_ZERO_ERROR';

        $formatter = $this->getDefaultDateFormatter($pattern);
        $this->assertSame($expected, $formatter->parse($value));
        $this->assertIsIntlSuccess($formatter, $errorMessage, $errorCode);
    }

    public function parseProvider()
    {
        return array_merge(
            $this->parseYearProvider(),
            $this->parseQuarterProvider(),
            $this->parseMonthProvider(),
            $this->parseStandaloneMonthProvider(),
            $this->parseDayProvider(),
            $this->parseDayOfWeekProvider(),
            $this->parseDayOfYearProvider(),
            $this->parseHour12ClockOneBasedProvider(),
            $this->parseHour12ClockZeroBasedProvider(),
            $this->parseHour24ClockOneBasedProvider(),
            $this->parseHour24ClockZeroBasedProvider(),
            $this->parseMinuteProvider(),
            $this->parseSecondProvider(),
            $this->parseTimezoneProvider(),
            $this->parseAmPmProvider(),
            $this->parseStandaloneAmPmProvider(),
            $this->parseRegexMetaCharsProvider(),
            $this->parseQuoteCharsProvider(),
            $this->parseDashSlashProvider()
        );
    }

    public function parseYearProvider()
    {
        return array(
            array('y-M-d', '1970-1-1', 0),
            array('yy-M-d', '70-1-1', 0),
        );
    }

    public function parseQuarterProvider()
    {
        return array(
            array('Q', '1', 0),
            array('QQ', '01', 0),
            array('QQQ', 'Q1', 0),
            array('QQQQ', '1st quarter', 0),
            array('QQQQQ', '1st quarter', 0),

            array('Q', '2', 7776000),
            array('QQ', '02', 7776000),
            array('QQQ', 'Q2', 7776000),
            array('QQQQ', '2nd quarter', 7776000),
            array('QQQQQ', '2nd quarter', 7776000),

            array('q', '1', 0),
            array('qq', '01', 0),
            array('qqq', 'Q1', 0),
            array('qqqq', '1st quarter', 0),
            array('qqqqq', '1st quarter', 0),
        );
    }

    public function parseMonthProvider()
    {
        return array(
            array('y-M-d', '1970-1-1', 0),
            array('y-MMM-d', '1970-Jan-1', 0),
            array('y-MMMM-d', '1970-January-1', 0),
        );
    }

    public function parseStandaloneMonthProvider()
    {
        return array(
            array('y-L-d', '1970-1-1', 0),
            array('y-LLL-d', '1970-Jan-1', 0),
            array('y-LLLL-d', '1970-January-1', 0),
        );
    }

    public function parseDayProvider()
    {
        return array(
            array('y-M-d', '1970-1-1', 0),
            array('y-M-dd', '1970-1-01', 0),
            array('y-M-ddd', '1970-1-001', 0),
        );
    }

    public function parseDayOfWeekProvider()
    {
        return array(
            array('E', 'Thu', 0),
            array('EE', 'Thu', 0),
            array('EEE', 'Thu', 0),
            array('EEEE', 'Thursday', 0),
            array('EEEEE', 'T', 432000),
            array('EEEEEE', 'Th', 0),
        );
    }

    public function parseDayOfYearProvider()
    {
        return array(
            array('D', '1', 0),
            array('D', '2', 86400),
        );
    }

    public function parseHour12ClockOneBasedProvider()
    {
        return array(
            // 12 hours (1-12)
            array('y-M-d h', '1970-1-1 1', 3600),
            array('y-M-d h', '1970-1-1 10', 36000),
            array('y-M-d hh', '1970-1-1 11', 39600),
            array('y-M-d hh', '1970-1-1 12', 0),
            array('y-M-d hh a', '1970-1-1 0 AM', 0),
            array('y-M-d hh a', '1970-1-1 1 AM', 3600),
            array('y-M-d hh a', '1970-1-1 10 AM', 36000),
            array('y-M-d hh a', '1970-1-1 11 AM', 39600),
            array('y-M-d hh a', '1970-1-1 12 AM', 0),
            array('y-M-d hh a', '1970-1-1 23 AM', 82800),
            array('y-M-d hh a', '1970-1-1 24 AM', 86400),
            array('y-M-d hh a', '1970-1-1 0 PM', 43200),
            array('y-M-d hh a', '1970-1-1 1 PM', 46800),
            array('y-M-d hh a', '1970-1-1 10 PM', 79200),
            array('y-M-d hh a', '1970-1-1 11 PM', 82800),
            array('y-M-d hh a', '1970-1-1 12 PM', 43200),
            array('y-M-d hh a', '1970-1-1 23 PM', 126000),
            array('y-M-d hh a', '1970-1-1 24 PM', 129600),
        );
    }

    public function parseHour12ClockZeroBasedProvider()
    {
        return array(
            // 12 hours (0-11)
            array('y-M-d K', '1970-1-1 1', 3600),
            array('y-M-d K', '1970-1-1 10', 36000),
            array('y-M-d KK', '1970-1-1 11', 39600),
            array('y-M-d KK', '1970-1-1 12', 43200),
            array('y-M-d KK a', '1970-1-1 0 AM', 0),
            array('y-M-d KK a', '1970-1-1 1 AM', 3600),
            array('y-M-d KK a', '1970-1-1 10 AM', 36000),
            array('y-M-d KK a', '1970-1-1 11 AM', 39600),
            array('y-M-d KK a', '1970-1-1 12 AM', 43200),
            array('y-M-d KK a', '1970-1-1 23 AM', 82800),
            array('y-M-d KK a', '1970-1-1 24 AM', 86400),
            array('y-M-d KK a', '1970-1-1 0 PM', 43200),
            array('y-M-d KK a', '1970-1-1 1 PM', 46800),
            array('y-M-d KK a', '1970-1-1 10 PM', 79200),
            array('y-M-d KK a', '1970-1-1 11 PM', 82800),
            array('y-M-d KK a', '1970-1-1 12 PM', 86400),
            array('y-M-d KK a', '1970-1-1 23 PM', 126000),
            array('y-M-d KK a', '1970-1-1 24 PM', 129600),
        );
    }

    public function parseHour24ClockOneBasedProvider()
    {
        return array(
            // 24 hours (1-24)
            array('y-M-d k', '1970-1-1 1', 3600),
            array('y-M-d k', '1970-1-1 10', 36000),
            array('y-M-d kk', '1970-1-1 11', 39600),
            array('y-M-d kk', '1970-1-1 12', 43200),
            array('y-M-d kk', '1970-1-1 23', 82800),
            array('y-M-d kk', '1970-1-1 24', 0),
            array('y-M-d kk a', '1970-1-1 0 AM', 0),
            array('y-M-d kk a', '1970-1-1 1 AM', 0),
            array('y-M-d kk a', '1970-1-1 10 AM', 0),
            array('y-M-d kk a', '1970-1-1 11 AM', 0),
            array('y-M-d kk a', '1970-1-1 12 AM', 0),
            array('y-M-d kk a', '1970-1-1 23 AM', 0),
            array('y-M-d kk a', '1970-1-1 24 AM', 0),
            array('y-M-d kk a', '1970-1-1 0 PM', 43200),
            array('y-M-d kk a', '1970-1-1 1 PM', 43200),
            array('y-M-d kk a', '1970-1-1 10 PM', 43200),
            array('y-M-d kk a', '1970-1-1 11 PM', 43200),
            array('y-M-d kk a', '1970-1-1 12 PM', 43200),
            array('y-M-d kk a', '1970-1-1 23 PM', 43200),
            array('y-M-d kk a', '1970-1-1 24 PM', 43200),
        );
    }

    public function parseHour24ClockZeroBasedProvider()
    {
        return array(
            // 24 hours (0-23)
            array('y-M-d H', '1970-1-1 0', 0),
            array('y-M-d H', '1970-1-1 1', 3600),
            array('y-M-d H', '1970-1-1 10', 36000),
            array('y-M-d HH', '1970-1-1 11', 39600),
            array('y-M-d HH', '1970-1-1 12', 43200),
            array('y-M-d HH', '1970-1-1 23', 82800),
            array('y-M-d HH a', '1970-1-1 0 AM', 0),
            array('y-M-d HH a', '1970-1-1 1 AM', 0),
            array('y-M-d HH a', '1970-1-1 10 AM', 0),
            array('y-M-d HH a', '1970-1-1 11 AM', 0),
            array('y-M-d HH a', '1970-1-1 12 AM', 0),
            array('y-M-d HH a', '1970-1-1 23 AM', 0),
            array('y-M-d HH a', '1970-1-1 24 AM', 0),
            array('y-M-d HH a', '1970-1-1 0 PM', 43200),
            array('y-M-d HH a', '1970-1-1 1 PM', 43200),
            array('y-M-d HH a', '1970-1-1 10 PM', 43200),
            array('y-M-d HH a', '1970-1-1 11 PM', 43200),
            array('y-M-d HH a', '1970-1-1 12 PM', 43200),
            array('y-M-d HH a', '1970-1-1 23 PM', 43200),
            array('y-M-d HH a', '1970-1-1 24 PM', 43200),
        );
    }

    public function parseMinuteProvider()
    {
        return array(
            array('y-M-d HH:m', '1970-1-1 0:1', 60),
            array('y-M-d HH:mm', '1970-1-1 0:10', 600),
        );
    }

    public function parseSecondProvider()
    {
        return array(
            array('y-M-d HH:mm:s', '1970-1-1 00:01:1', 61),
            array('y-M-d HH:mm:ss', '1970-1-1 00:01:10', 70),
        );
    }

    public function parseTimezoneProvider()
    {
        return array(
            array('y-M-d HH:mm:ss zzzz', '1970-1-1 00:00:00 GMT-03:00', 10800),
            array('y-M-d HH:mm:ss zzzz', '1970-1-1 00:00:00 GMT-04:00', 14400),
            array('y-M-d HH:mm:ss zzzz', '1970-1-1 00:00:00 GMT-00:00', 0),
            array('y-M-d HH:mm:ss zzzz', '1970-1-1 00:00:00 GMT+03:00', -10800),
            array('y-M-d HH:mm:ss zzzz', '1970-1-1 00:00:00 GMT+04:00', -14400),
            array('y-M-d HH:mm:ss zzzz', '1970-1-1 00:00:00 GMT-0300', 10800),
            array('y-M-d HH:mm:ss zzzz', '1970-1-1 00:00:00 GMT+0300', -10800),

            // a previous timezone parsing should not change the timezone for the next parsing
            array('y-M-d HH:mm:ss', '1970-1-1 00:00:00', 0),
        );
    }

    public function parseAmPmProvider()
    {
        return array(
            // AM/PM (already covered by hours tests)
            array('y-M-d HH:mm:ss a', '1970-1-1 00:00:00 AM', 0),
            array('y-M-d HH:mm:ss a', '1970-1-1 00:00:00 PM', 43200),
        );
    }

    public function parseStandaloneAmPmProvider()
    {
        return array(
            array('a', 'AM', 0),
            array('a', 'PM', 43200),
        );
    }

    public function parseRegexMetaCharsProvider()
    {
        return array(
            // regexp meta chars in the pattern string
            array('y[M-d', '1970[1-1', 0),
            array('y[M/d', '1970[1/1', 0),
        );
    }

    public function parseQuoteCharsProvider()
    {
        return array(
            array("'M'", 'M', 0),
            array("'yy'", 'yy', 0),
            array("'''yy'", "'yy", 0),
            array("''y", "'1970", 0),
            array("H 'o'' clock'", "0 o' clock", 0),
        );
    }

    public function parseDashSlashProvider()
    {
        return array(
            array('y-M-d', '1970/1/1', 0),
            array('yy-M-d', '70/1/1', 0),
            array('y/M/d', '1970-1-1', 0),
            array('yy/M/d', '70-1-1', 0),
        );
    }

    /**
     * @dataProvider parseErrorProvider
     */
    public function testParseError($pattern, $value)
    {
        $errorCode = IntlGlobals::U_PARSE_ERROR;
        $errorMessage = 'Date parsing failed: U_PARSE_ERROR';

        $formatter = $this->getDefaultDateFormatter($pattern);
        $this->assertFalse($formatter->parse($value));
        $this->assertIsIntlFailure($formatter, $errorMessage, $errorCode);
    }

    public function parseErrorProvider()
    {
        return array(
            // 1 char month
            array('y-MMMMM-d', '1970-J-1'),
            array('y-MMMMM-d', '1970-S-1'),

            // standalone 1 char month
            array('y-LLLLL-d', '1970-J-1'),
            array('y-LLLLL-d', '1970-S-1'),
        );
    }

    /*
     * https://github.com/symfony/symfony/issues/4242
     */
    public function testParseAfterError()
    {
        $this->testParseError('y-MMMMM-d', '1970-J-1');
        $this->testParse('y-M-d', '1970-1-1', 0);
    }

    public function testParseWithNullPositionValue()
    {
        $position = null;
        $formatter = $this->getDefaultDateFormatter('y');
        $this->assertSame(0, $formatter->parse('1970', $position));
        $this->assertNull($position);
    }

    public function testSetPattern()
    {
        $formatter = $this->getDefaultDateFormatter();
        $formatter->setPattern('yyyy-MM-dd');
        $this->assertEquals('yyyy-MM-dd', $formatter->getPattern());
    }

    /**
     * @covers Symfony\Component\Intl\DateFormatter\IntlDateFormatter::getTimeZoneId
     * @dataProvider setTimeZoneIdProvider
     */
    public function testSetTimeZoneId($timeZoneId, $expectedTimeZoneId)
    {
        $formatter = $this->getDefaultDateFormatter();

        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $formatter->setTimeZone($timeZoneId);
        } else {
            $formatter->setTimeZoneId($timeZoneId);
        }

        $this->assertEquals($expectedTimeZoneId, $formatter->getTimeZoneId());
    }

    public function setTimeZoneIdProvider()
    {
        return array(
            array('UTC', 'UTC'),
            array('GMT', 'GMT'),
            array('GMT-03:00', 'GMT-03:00'),
            array('Europe/Zurich', 'Europe/Zurich'),
            array('GMT-0300', 'GMT-0300'),
            array('Foo/Bar', 'Foo/Bar'),
            array('GMT+00:AA', 'GMT+00:AA'),
            array('GMT+00AA', 'GMT+00AA'),
        );
    }

    protected function getDefaultDateFormatter($pattern = null)
    {
        return $this->getDateFormatter('en', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT, 'UTC', IntlDateFormatter::GREGORIAN, $pattern);
    }

    protected function getDateTime($timestamp = null)
    {
        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $timeZone = date_default_timezone_get();
        } else {
            $timeZone = getenv('TZ') ?: 'UTC';
        }

        $dateTime = new \DateTime();
        $dateTime->setTimestamp(null === $timestamp ? time() : $timestamp);
        $dateTime->setTimeZone(new \DateTimeZone($timeZone));

        return $dateTime;
    }

    protected function assertIsIntlFailure($formatter, $errorMessage, $errorCode)
    {
        $this->assertSame($errorMessage, $this->getIntlErrorMessage());
        $this->assertSame($errorCode, $this->getIntlErrorCode());
        $this->assertTrue($this->isIntlFailure($this->getIntlErrorCode()));
        $this->assertSame($errorMessage, $formatter->getErrorMessage());
        $this->assertSame($errorCode, $formatter->getErrorCode());
        $this->assertTrue($this->isIntlFailure($formatter->getErrorCode()));
    }

    protected function assertIsIntlSuccess($formatter, $errorMessage, $errorCode)
    {
        /* @var IntlDateFormatter $formatter */
        $this->assertSame($errorMessage, $this->getIntlErrorMessage());
        $this->assertSame($errorCode, $this->getIntlErrorCode());
        $this->assertFalse($this->isIntlFailure($this->getIntlErrorCode()));
        $this->assertSame($errorMessage, $formatter->getErrorMessage());
        $this->assertSame($errorCode, $formatter->getErrorCode());
        $this->assertFalse($this->isIntlFailure($formatter->getErrorCode()));
    }

    /**
     * @param $locale
     * @param $datetype
     * @param $timetype
     * @param null $timezone
     * @param int $calendar
     * @param null $pattern
     *
     * @return mixed
     */
    abstract protected function getDateFormatter($locale, $datetype, $timetype, $timezone = null, $calendar = IntlDateFormatter::GREGORIAN, $pattern = null);

    /**
     * @return string
     */
    abstract protected function getIntlErrorMessage();

    /**
     * @return integer
     */
    abstract protected function getIntlErrorCode();

    /**
     * @param integer $errorCode
     *
     * @return Boolean
     */
    abstract protected function isIntlFailure($errorCode);
}
