<?php
declare(strict_types=1);

namespace Vendor\Crm\Tests;

use PHPUnit\Framework\TestCase;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

/**
 * Unit tests for Stage constants and helpers.
 * Covers: isValid, rank, ORDER — pure logic, no DB.
 */
final class StageTest extends TestCase
{
    public function testOrderContainsAllStages(): void
    {
        $expected = [
            Stage::ICE,
            Stage::TOUCHED,
            Stage::AWARE,
            Stage::INTERESTED,
            Stage::DEMO_PLANNED,
            Stage::DEMO_DONE,
            Stage::COMMITTED,
            Stage::CUSTOMER,
            Stage::ACTIVATED,
        ];
        $this->assertSame($expected, Stage::ORDER);
    }

    public function testIsValidAcceptsAllKnownStages(): void
    {
        foreach (Stage::ORDER as $stage) {
            $this->assertTrue(Stage::isValid($stage), "Stage '$stage' should be valid");
        }
    }

    public function testIsValidRejectsUnknownStage(): void
    {
        $this->assertFalse(Stage::isValid('Unknown'));
        $this->assertFalse(Stage::isValid(''));
        $this->assertFalse(Stage::isValid('ice')); // case-sensitive
    }

    public function testRankReturnsCorrectIndex(): void
    {
        $this->assertSame(0, Stage::rank(Stage::ICE));
        $this->assertSame(1, Stage::rank(Stage::TOUCHED));
        $this->assertSame(2, Stage::rank(Stage::AWARE));
        $this->assertSame(8, Stage::rank(Stage::ACTIVATED));
    }

    public function testRankThrowsForUnknownStage(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown stage');
        Stage::rank('Invalid');
    }

    public function testAwareIsAboveIceAndTouched(): void
    {
        $this->assertGreaterThan(Stage::rank(Stage::ICE), Stage::rank(Stage::AWARE));
        $this->assertGreaterThan(Stage::rank(Stage::TOUCHED), Stage::rank(Stage::AWARE));
    }
}
