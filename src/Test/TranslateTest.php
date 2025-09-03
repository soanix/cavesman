<?php
declare(strict_types=1);

namespace Cavesman\Test;


use Cavesman\Enum\Locale;
use Cavesman\Translate;
use Exception;
use PHPUnit\Framework\TestCase;

final class TranslateTest extends TestCase
{
    /**
     * @group Twig
     * @return void
     * @throws Exception
     */
    public function testDefault(): void
    {
        $this->assertEquals(Translate::getLocales(), [Locale::en]);
    }

    /**
     * @group Twig
     * @return void
     * @throws Exception
     */
    public function testOverride(): void
    {
        Translate::setClass(\Cavesman\Test\Enum\Locale::class);
        $this->assertEquals(Translate::getLocales(), [\Cavesman\Test\Enum\Locale::en, \Cavesman\Test\Enum\Locale::es]);
    }
}
