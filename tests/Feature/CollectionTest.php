<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1, 2, 3]);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6]);
        foreach ($collection as $key => $value) {
            self::assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1, 2, 3);
        self::assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();
        self::assertEquals(3, $result);
        self::assertEqualsCanonicalizing([1, 2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });
        self::assertEquals([2, 4, 6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["Christian"]);
        $result = $collection->mapInto(Person::class);
        self::assertEquals([new Person("Christian")], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([["Chris", "Tian"], ["Tian", "Chris"]]);
        $result = $collection->mapSpread(function ($firstName, $lastName) {
            $fullName = $firstName . " " . $lastName;
            return new Person($fullName);
        });
        self::assertEquals([
            new Person("Chris Tian"),
            new Person("Tian Chris")
        ], $result->all());
    }

    public function testMapToGroup()
    {
        $collection = collect([
            [
                "name" => "Christian",
                "departement" => "IT"
            ],
            [
                "name" => "Budi",
                "departement" => "IT"
            ],
            [
                "name" => "Callista",
                "departement" => "HR"
            ]
        ]);
        $result = $collection->mapToGroups(function ($item) {
            return [$item["departement"] => $item["name"]];
        });
        self::assertEquals([
            "IT" => collect(["Christian", "Budi"]),
            "HR" => collect(["Callista"])
        ], $result->all());
    }

    public function testZip()
    {
        $collection = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection->zip($collection2);

        self::assertEquals([
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6])
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection->concat($collection2);

        self::assertEquals([
            1, 2, 3, 4, 5, 6
        ], $collection3->all());
    }

    public function testCombine()
    {
        $collection = collect(["name", "country"]);
        $collection2 = collect(["Christian", "Indonesia"]);
        $collection3 = $collection->combine($collection2);

        self::assertEquals([
            "name" => "Christian",
            "country" => "Indonesia"
        ], $collection3->all());
    }
}
