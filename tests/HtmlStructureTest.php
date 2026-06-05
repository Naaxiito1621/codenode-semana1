<?php

namespace Tests;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;

class HtmlStructureTest extends TestCase
{
    private DOMDocument $indexDom;
    private DOMDocument $respuestaDom;
    private DOMXPath $indexXpath;
    private DOMXPath $respuestaXpath;

    protected function setUp(): void
    {
        $this->indexDom = new DOMDocument();
        @$this->indexDom->loadHTMLFile(__DIR__ . '/../index.html');
        $this->indexXpath = new DOMXPath($this->indexDom);

        $this->respuestaDom = new DOMDocument();
        @$this->respuestaDom->loadHTMLFile(__DIR__ . '/../respuesta.html');
        $this->respuestaXpath = new DOMXPath($this->respuestaDom);
    }

    // --- index.html ---

    public function testIndexHasFormElement(): void
    {
        $forms = $this->indexDom->getElementsByTagName('form');
        $this->assertGreaterThan(0, $forms->length, 'index.html should contain a <form> element');
    }

    public function testFormPostsToFormPhp(): void
    {
        $forms = $this->indexDom->getElementsByTagName('form');
        $form = $forms->item(0);
        $this->assertEquals('form.php', $form->getAttribute('action'));
        $this->assertEquals('post', $form->getAttribute('method'));
    }

    public function testFormHasNameInput(): void
    {
        $inputs = $this->indexXpath->query('//input[@name="name"]');
        $this->assertEquals(1, $inputs->length, 'Form must have an input with name="name"');

        $input = $inputs->item(0);
        $this->assertEquals('text', $input->getAttribute('type'));
        $this->assertTrue($input->hasAttribute('required'));
    }

    public function testFormHasApellidosInput(): void
    {
        $inputs = $this->indexXpath->query('//input[@name="Apellidos"]');
        $this->assertEquals(1, $inputs->length, 'Form must have an input with name="Apellidos"');
        $this->assertTrue($inputs->item(0)->hasAttribute('required'));
    }

    public function testFormHasEmailInput(): void
    {
        $inputs = $this->indexXpath->query('//input[@name="email"]');
        $this->assertEquals(1, $inputs->length, 'Form must have an input with name="email"');

        $input = $inputs->item(0);
        $this->assertEquals('email', $input->getAttribute('type'));
        $this->assertTrue($input->hasAttribute('required'));
    }

    public function testFormHasTelefonoInput(): void
    {
        $inputs = $this->indexXpath->query('//input[@name="telefono"]');
        $this->assertEquals(1, $inputs->length, 'Form must have an input with name="telefono"');

        $input = $inputs->item(0);
        $this->assertEquals('tel', $input->getAttribute('type'));
        $this->assertTrue($input->hasAttribute('required'));
    }

    public function testFormHasSubmitButton(): void
    {
        $buttons = $this->indexDom->getElementsByTagName('button');
        $this->assertGreaterThan(0, $buttons->length, 'Form must have a submit button');
    }

    public function testIndexHasTitle(): void
    {
        $titles = $this->indexDom->getElementsByTagName('title');
        $this->assertGreaterThan(0, $titles->length);
        $this->assertEquals('Formulario', $titles->item(0)->textContent);
    }

    public function testIndexLinksStylesheet(): void
    {
        $links = $this->indexXpath->query('//link[@rel="stylesheet"]');
        $this->assertGreaterThan(0, $links->length);
        $this->assertEquals('style.css', $links->item(0)->getAttribute('href'));
    }

    public function testIndexHasHeading(): void
    {
        $headings = $this->indexDom->getElementsByTagName('h1');
        $this->assertGreaterThan(0, $headings->length);
        $this->assertStringContainsString('Formulario', $headings->item(0)->textContent);
    }

    // --- respuesta.html ---

    public function testRespuestaHasThankYouMessage(): void
    {
        $paragraphs = $this->respuestaDom->getElementsByTagName('p');
        $found = false;
        for ($i = 0; $i < $paragraphs->length; $i++) {
            if (str_contains($paragraphs->item($i)->textContent, 'gracias')) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'respuesta.html should contain a thank-you message');
    }

    public function testRespuestaHasBackLink(): void
    {
        $links = $this->respuestaDom->getElementsByTagName('a');
        $found = false;
        for ($i = 0; $i < $links->length; $i++) {
            if ($links->item($i)->getAttribute('href') === 'index.html') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'respuesta.html should have a link back to index.html');
    }

    public function testRespuestaHasTitle(): void
    {
        $titles = $this->respuestaDom->getElementsByTagName('title');
        $this->assertGreaterThan(0, $titles->length);
        $this->assertStringContainsString('Formulario', $titles->item(0)->textContent);
    }
}
