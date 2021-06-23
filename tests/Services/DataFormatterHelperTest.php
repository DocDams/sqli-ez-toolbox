<?php

namespace SQLI\EzToolboxBundle\tests\Services;

use PHPUnit\Framework\TestCase;
use SQLI\EzToolboxBundle\Services\DataFormatterHelper;

class DataFormatterHelperTest extends TestCase
{
    /** @var DataFormatterHelper */
    protected $dataFormatterHelper;

    public function setUp(): void
    {
        parent::setUp();
        $this->dataFormatterHelper = new DataFormatterHelper();
    }

    /**
     * format=float
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::format
     */
    public function testFormatFloat(): void
    {
        $formattedData = $this->dataFormatterHelper->format("0,23456", 'float');
        $this->assertNotSame(0.23456, $formattedData);
        $this->assertSame("0.23456", $formattedData);
    }
    /**
     * format=price
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::format
     */
    public function testFormatPriceFloatRound(): void
    {
        $formattedData = $this->dataFormatterHelper->format(123.456789, 'price');
        $this->assertSame("123,46 €", $formattedData);
    }

    /**
     * format=french_date
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::format
     */
    public function testFormatFrenchDate(): void
    {
        $date = \DateTime::createFromFormat('U', 1623094200);
        $formattedData = $this->dataFormatterHelper->format($date, 'french_date');
        $this->assertSame("Lundi 07 Juin à 19:30", $formattedData);
    }

    /**
     * format=filesize
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::format
     */
    public function testFormatFilesize(): void
    {
        $size = 1073741824; // 1Go
        $formattedData = $this->dataFormatterHelper->format($size, 'filesize');
        $this->assertSame("1.0 Go", $formattedData);
    }

    /**
     * format=url
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::format
     */
    public function testFormatUrl(): void
    {
        $url = "https://sqli.com";
        $formattedUrl = $this->dataFormatterHelper->format($url, 'url');
        $this->assertSame($url, $formattedUrl);
    }

    /**
     * format=url
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::format
     */
    public function testFormatUrlTruncatedUrl(): void
    {
        $url = "sqli.com";
        $formattedUrl = $this->dataFormatterHelper->format($url, 'url');
        $this->assertSame("http://$url", $formattedUrl);
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::toDateTime
     */
    public function testToDateTimeFormatDayMonthYear(): void
    {
        $dateString = "07/06/2021";
        $datetime = $this->dataFormatterHelper->toDateTime($dateString);
        $this->assertSame($dateString, $datetime->format("d/m/Y"));
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::toDateTime
     */
    public function testToDateTimeFormatYearMonthDay(): void
    {
        $dateString = "2021-06-07";
        $datetime = $this->dataFormatterHelper->toDateTime($dateString);
        $this->assertSame($dateString, $datetime->format("Y-m-d"));
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::toDateTime
     */
    public function testToDateTimeDatetimeYesterday(): void
    {
        $currentDatetime = new \DateTime('yesterday', new \DateTimeZone('UTC'));
        $datetime = $this->dataFormatterHelper->toDateTime('yesterday');
        $this->assertSame($currentDatetime->getTimestamp(), $datetime->getTimestamp());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::toDateTime
     */
    public function testToDateTimeDatetimeNotGiven(): void
    {
        $currentTimestamp = date('now');
        $datetime = $this->dataFormatterHelper->toDateTime(false);
        $this->assertGreaterThanOrEqual($currentTimestamp, $datetime->getTimestamp());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::toDateTime
     */
    public function testToDateTimeDatetimeGiven(): void
    {
        $datetime = new \DateTime('now', new \DateTimeZone('UTC'));
        $datetimeReturned = $this->dataFormatterHelper->toDateTime($datetime);
        $this->assertSame($datetime, $datetimeReturned);
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::toDateTime
     */
    public function testToDateTimeDefaultReturn(): void
    {
        $default = "default";
        $datetimeReturned = $this->dataFormatterHelper->toDateTime("wtf", $default);
        $this->assertSame($default, $datetimeReturned);
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::slugify
     */
    public function testSlugifyTextSimple(): void
    {
        $slug = $this->dataFormatterHelper->slugify("test simple");
        $this->assertSame("test-simple", $slug);
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::slugify
     */
    public function testSlugifyTextNumeric(): void
    {
        $slug = $this->dataFormatterHelper->slugify("test 007");
        $this->assertSame("test-007", $slug);
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::slugify
     */
    public function testSlugifyTextSpecialChars(): void
    {
        $slug = $this->dataFormatterHelper->slugify("caractères spéciaux où '1€' peut coûter cher !");
        $this->assertSame("caracteres-speciaux-ou-1eur-peut-couter-cher", $slug);
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::slugify
     */
    public function testSlugifyTextIgnoredChars(): void
    {
        $slug = $this->dataFormatterHelper->slugify("jeu de caractères japonais : 日本");
        $this->assertSame("jeu-de-caracteres-japonais", $slug);
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Services\DataFormatterHelper::slugify
     */
    public function testSlugifyOtherDelimiter(): void
    {
        $slug = $this->dataFormatterHelper->slugify("utilisation du dièse comme délimiteur #", '#');
        $this->assertSame("utilisation#du#diese#comme#delimiteur", $slug);
    }
}
