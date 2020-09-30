<?php

namespace Lukeraymonddowning\Mula\Tests;

use Lukeraymonddowning\Mula\Exceptions\UnreadableMonetaryValue;
use Lukeraymonddowning\Mula\Facades\Mula;
use Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver\AggregateMoneyParserResolver;
use Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver\DecimalMoneyParserResolver;
use Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver\ParserResolver;
use Lukeraymonddowning\Mula\MulaServiceProvider;
use Orchestra\Testbench\TestCase;

class PhpMoneyTest extends TestCase
{
    /** @test */
    public function it_can_create_new_money()
    {
        $money = Mula::create('12350', 'GBP');
        $this->assertTrue(Mula::create('12350', 'GBP')->equals($money));
    }

    /** @test */
    public function it_can_create_new_money_with_an_integer()
    {
        $money = Mula::create(12350, 'GBP');
        $this->assertTrue(Mula::create('12350', 'GBP')->equals($money));
    }

    /** @test */
    public function it_throws_an_exception_if_the_amount_is_unreadable()
    {
        $this->expectException(UnreadableMonetaryValue::class);

        Mula::create('foobar');
    }

    /** @test */
    public function it_can_parse_money_as_an_integer()
    {
        $money = Mula::parse(123, 'GBP');
        $this->assertTrue(Mula::create('12300', 'GBP')->equals($money));
    }

    /** @test */
    public function it_can_parse_money_as_a_float()
    {
        $money = Mula::parse(123.99, 'GBP');
        $this->assertTrue(Mula::create('12399', 'GBP')->equals($money));
    }

    /** @test */
    public function the_currency_can_be_null_and_will_use_the_config_default()
    {
        $money = Mula::create('12350');

        $this->assertEquals(config('mula.currency'), $money->currency());
    }

    /** @test */
    public function it_throws_an_exception_if_it_cannot_be_parsed()
    {
        $this->expectException(UnreadableMonetaryValue::class);

        Mula::parse('foobar');
    }

    /** @test */
    public function it_can_equate_money()
    {
        $money = Mula::create('12350', 'GBP');
        $this->assertTrue(Mula::create('12350', 'GBP')->equals($money));

        $money2 = Mula::create('12350', 'GBP');
        $this->assertFalse(Mula::create('12351', 'GBP')->equals($money2));

        $money = Mula::create('12350', 'GBP');
        $otherMoney = Mula::create('12351', 'GBP');
        $this->assertFalse(Mula::create('12351', 'GBP')->equals($money, $otherMoney));
    }

    /** @test */
    public function it_can_add_money_immutably()
    {
        $money = Mula::create('10000', 'GBP');
        $newMoney = $money->add(Mula::create('10000', 'GBP'), Mula::create('10000', 'GBP'));

        $this->assertNotSame($money, $newMoney);
        $this->assertTrue(Mula::create('30000', 'GBP')->equals($newMoney));
    }

    /** @test */
    public function it_can_subtract_money_immutably()
    {
        $money = Mula::create('30000', 'GBP');
        $newMoney = $money->subtract(Mula::create('10000', 'GBP'), Mula::create('10000', 'GBP'));

        $this->assertNotSame($money, $newMoney);
        $this->assertTrue(Mula::create('10000', 'GBP')->equals($newMoney));
    }

    /** @test */
    public function it_can_multiply_immutably()
    {
        $money = Mula::create('30000', 'GBP');
        $newMoney = $money->multiplyBy(2);

        $this->assertNotSame($money, $newMoney);
        $this->assertTrue(Mula::create('60000', 'GBP')->equals($newMoney));
    }

    /** @test */
    public function it_can_divide_immutably()
    {
        $money = Mula::create('30000', 'GBP');
        $newMoney = $money->divideBy(2);

        $this->assertNotSame($money, $newMoney);
        $this->assertTrue(Mula::create('15000', 'GBP')->equals($newMoney));
    }

    /** @test */
    public function it_can_return_the_modulus_immutably()
    {
        $money = Mula::create('30000', 'GBP');
        $newMoney = $money->mod(Mula::create('20000', 'GBP'));

        $this->assertNotSame($money, $newMoney);
        $this->assertTrue(Mula::create('10000', 'GBP')->equals($newMoney));
    }

    /** @test */
    public function it_can_tell_if_money_values_have_the_same_currency()
    {
        $this->assertTrue(Mula::create('12000', 'GBP')->hasSameCurrencyAs(Mula::create('15000', 'GBP')));

        $this->assertFalse(Mula::create('12000', 'GBP')->hasSameCurrencyAs(Mula::create('15000', 'EUR')));

        $this->assertFalse(
            Mula::create('12000', 'GBP')->hasSameCurrencyAs(
                Mula::create('15000', 'GBP'),
                Mula::create('15000', 'EUR'),
            )
        );
    }

    /** @test */
    public function it_can_tell_if_a_value_is_greater_than_another_value()
    {
        $this->assertTrue(Mula::create('10000', 'GBP')->isGreaterThan(Mula::create('9999', 'GBP')));
        $this->assertFalse(Mula::create('10000', 'GBP')->isGreaterThan(Mula::create('20000', 'GBP')));

        $this->assertTrue(
            Mula::create('10000', 'GBP')->isGreaterThan(
                Mula::create('9999', 'GBP'),
                Mula::create('9998', 'GBP'),
            )
        );

        $this->assertFalse(
            Mula::create('10000', 'GBP')->isGreaterThan(
                Mula::create('9999', 'GBP'),
                Mula::create('10000', 'GBP'),
            )
        );
    }

    /** @test */
    public function it_can_tell_if_a_value_is_greater_than_or_equal_to_another_value()
    {
        $this->assertTrue(Mula::create('10000', 'GBP')->isGreaterThanOrEqualTo(Mula::create('10000', 'GBP')));
        $this->assertTrue(Mula::create('10000', 'GBP')->isGreaterThanOrEqualTo(Mula::create('9999', 'GBP')));
        $this->assertFalse(Mula::create('10000', 'GBP')->isGreaterThanOrEqualTo(Mula::create('20000', 'GBP')));

        $this->assertTrue(
            Mula::create('10000', 'GBP')->isGreaterThanOrEqualTo(
                Mula::create('10000', 'GBP'),
                Mula::create('9999', 'GBP'),
            )
        );

        $this->assertFalse(
            Mula::create('10000', 'GBP')->isGreaterThanOrEqualTo(
                Mula::create('9999', 'GBP'),
                Mula::create('10001', 'GBP'),
            )
        );
    }

    /** @test */
    public function it_can_tell_if_a_value_is_less_than_another_value()
    {
        $this->assertTrue(Mula::create('10000', 'GBP')->isLessThan(Mula::create('10001', 'GBP')));
        $this->assertFalse(Mula::create('10000', 'GBP')->isLessThan(Mula::create('9999', 'GBP')));

        $this->assertTrue(
            Mula::create('10000', 'GBP')->isLessThan(
                Mula::create('10001', 'GBP'),
                Mula::create('10002', 'GBP'),
            )
        );

        $this->assertFalse(
            Mula::create('10000', 'GBP')->isLessThan(
                Mula::create('10000', 'GBP'),
                Mula::create('10001', 'GBP'),
            )
        );
    }

    /** @test */
    public function it_can_tell_if_a_value_is_less_than_or_equal_to_another_value()
    {
        $this->assertTrue(Mula::create('10000', 'GBP')->isLessThanOrEqualTo(Mula::create('10001', 'GBP')));
        $this->assertTrue(Mula::create('10000', 'GBP')->isLessThanOrEqualTo(Mula::create('10000', 'GBP')));
        $this->assertFalse(Mula::create('10000', 'GBP')->isLessThanOrEqualTo(Mula::create('9999', 'GBP')));

        $this->assertTrue(
            Mula::create('10000', 'GBP')->isLessThanOrEqualTo(
                Mula::create('10000', 'GBP'),
                Mula::create('10002', 'GBP'),
            )
        );

        $this->assertFalse(
            Mula::create('10000', 'GBP')->isLessThanOrEqualTo(
                Mula::create('9999', 'GBP'),
                Mula::create('9998', 'GBP'),
            )
        );
    }

    /**
     * @test
     * @dataProvider displayableDataProvider
     */
    public function it_can_display_its_value($money, $currency, $displayedAs)
    {
        $this->assertEquals($displayedAs, Mula::create($money, $currency)->display());
    }

    public function displayableDataProvider()
    {
        return [
            ['1000', 'GBP', '£10.00'],
            ['100000', 'GBP', '£1,000.00'],
            ['100000000', 'GBP', '£1,000,000.00'],
            ['100000000', 'USD', '$1,000,000.00'],
            ['100000000', 'EUR', '€1,000,000.00'],
        ];
    }

    /**
     * @test
     * @dataProvider displayableWithoutCurrencyDataProvider
     */
    public function it_can_display_its_value_without_currency($money, $currency, $displayedAs)
    {
        $this->assertEquals($displayedAs, Mula::create($money, $currency)->displayWithoutCurrency());
        $this->assertEquals($displayedAs, Mula::create($money, $currency)->display(false));
    }

    public function displayableWithoutCurrencyDataProvider()
    {
        return [
            ['1000', 'GBP', '10'],
            ['100000', 'GBP', '1,000'],
            ['100000000', 'GBP', '1,000,000'],
            ['100000000', 'USD', '1,000,000'],
            ['100000000', 'EUR', '1,000,000'],
            ['100000099', 'EUR', '1,000,000.99'],
        ];
    }

    /** @test */
    public function it_is_stringable()
    {
        $money = Mula::create('10000', 'GBP');
        $this->assertEquals('£100.00', $money->__toString());
    }

    /**
     * @test
     * @dataProvider parseableCurrencyDataProvider
     */
    public function it_can_parse_a_monetary_value($parsedValue, $check)
    {
        $money = Mula::parse($parsedValue);

        $this->assertEquals($check, $money->display());
    }

    public function parseableCurrencyDataProvider()
    {
        return [
            ['£1000', '£1,000.00'],
            ['$1000', '$1,000.00'],
            ['£1,500', '£1,500.00'],
            ['€1,700.99', '€1,700.99'],
        ];
    }

    /** @test */
    public function it_can_split_allocation()
    {
        $results = Mula::create('1000', 'GBP')->split([75, 25]);

        $this->assertTrue(Mula::create('750', 'GBP')->equals($results[0]));
        $this->assertTrue(Mula::create('250', 'GBP')->equals($results[1]));
    }

    /** @test */
    public function it_can_split_allocation_passed_as_a_collection()
    {
        $results = Mula::create('1000', 'GBP')->split(collect([75, 25]));

        $this->assertTrue(Mula::create('750', 'GBP')->equals($results[0]));
        $this->assertTrue(Mula::create('250', 'GBP')->equals($results[1]));
    }

    /** @test */
    public function it_can_split_allocation_evenly()
    {
        $results = Mula::create('1000', 'GBP')->split(3);

        $this->assertTrue(Mula::create('334', 'GBP')->equals($results[0]));
        $this->assertTrue(Mula::create('333', 'GBP')->equals($results[1]));
        $this->assertTrue(Mula::create('333', 'GBP')->equals($results[2]));
    }

    /** @test */
    public function it_can_return_the_currency()
    {
        $money = Mula::create(10000, 'GBP');
        $this->assertEquals('GBP', $money->currency());

        $money = Mula::create(10000, 'EUR');
        $this->assertEquals('EUR', $money->currency());

        $money = Mula::create(10000, 'USD');
        $this->assertEquals('USD', $money->currency());
    }

    /** @test */
    public function it_can_parse_money_as_a_decimal_with_the_decimal_parser()
    {
        $this->swap(ParserResolver::class, app(DecimalMoneyParserResolver::class));

        $money = Mula::parse('120.55', 'USD');

        $this->assertTrue(Mula::create('12055', 'USD')->equals($money));
    }

    /** @test */
    public function it_can_parse_money_of_any_format_with_the_aggregate_parser()
    {
        $this->swap(ParserResolver::class, app(AggregateMoneyParserResolver::class));

        $money = Mula::parse('120.55', 'USD');
        $this->assertTrue(Mula::create('12055', 'USD')->equals($money));

        $money = Mula::parse('£120.55');
        $this->assertTrue(Mula::create('12055', 'GBP')->equals($money));

        $money = Mula::parse('£120.55', 'USD');
        $this->assertTrue(Mula::create('12055', 'USD')->equals($money));
    }

    protected function getPackageProviders($app)
    {
        return [MulaServiceProvider::class];
    }
}
