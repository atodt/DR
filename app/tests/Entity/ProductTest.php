<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductPropertiesAreNullByDefault()
    {
        $product = new Product();

        $this->assertNull($product->getId());
        $this->assertNull($product->getTitle());
        $this->assertNull($product->getDescription());
        $this->assertNull($product->getCategory());
        $this->assertNull($product->getState());
    }

    public function testSetAndGetTitle()
    {
        $product = new Product();
        $title = "Sample Title";
        $description = "Sample Description";
        $category = "Sample Category";
        $state = true;

        $product->setTitle($title);
        $product->setDescription($description);
        $product->setCategory($category);
        $product->setState($state);

        $this->assertEquals($title, $product->getTitle());
        $this->assertEquals($description, $product->getDescription());
        $this->assertEquals($category, $product->getCategory());
        $this->assertEquals($state, $product->getState());
    }

    public function testSetAndGetDescription()
    {
        $product = new Product();
        $description = "This is a test description.";

        $product->setDescription($description);

        $this->assertSame($description, $product->getDescription());
    }

}
