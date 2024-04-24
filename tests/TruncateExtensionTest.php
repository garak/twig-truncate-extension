<?php

namespace Bluetel\Twig\Tests;

use Bluetel\Twig\TruncateExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TwigFilter;

class TruncateExtensionTest extends TestCase
{
    private TruncateExtension $extension;

    private Environment $twig;

    protected function setUp(): void
    {
        $loader = new ArrayLoader();
        $this->extension = new TruncateExtension();
        $this->twig = new Environment($loader);
        $this->twig->addExtension($this->extension);
    }

    /**
     * @covers \Bluetel\Twig\TruncateExtension::getFilters
     **/
    public function testGetFilters(): void
    {
        $filters = $this->extension->getFilters();
        $this->assertArrayHasKey('truncate_letters', $filters);
        $this->assertInstanceOf(TwigFilter::class, $filters['truncate_letters']);
        $this->assertArrayHasKey('truncate_words', $filters);
        $this->assertInstanceOf(TwigFilter::class, $filters['truncate_words']);
    }

    /**
     * @covers \Bluetel\Twig\TruncateExtension::truncateLetters
     */
    public function testLettersTruncation(): void
    {
        $data = $this->twig->render('{{ "<b>hello world</b>"|truncate_letters(5) }}');
	    $this->assertStringContainsString("<b>hello</b>", $data);
    }

    /**
     * @covers \Bluetel\Twig\TruncateExtension::truncateWords
     */
    public function testWordsTruncation(): void
    {
        $data = $this->twig->render('{{ "<b>hello world</b>"|truncate_words(1) }}');
        $this->assertStringContainsString("<b>hello</b>", $data);
    }

    /**
     * Ensures we preserve tricky HTML entities.
     * @covers \Bluetel\Twig\TruncateExtension::htmlToDomDocument
     */
    public function testHtmlEntityConversion(): void
    {
        $html = $this
            ->extension
            ->htmlToDomDocument("<DOCTYPE html><html><head></head><body>Foo’s bar</body></html>")
            ->saveHtml()
        ;
        $this->assertStringContainsString("Foo&rsquo;s bar", $html);
    }
}
