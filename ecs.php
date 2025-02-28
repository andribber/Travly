<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedInterfacesFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedTraitsFixer;
use PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAfterConstructFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $services = $ecsConfig->services();
    $ecsConfig->import(SetList::PSR_12);

    //phpcs
    $services->set(LineLengthSniff::class)->property('absoluteLineLimit', 120);

    //php-cs-fixer
    $services->set(ArraySyntaxFixer::class)->call('configure', [['syntax' => 'short']]);
    $services->set(ConcatSpaceFixer::class)->call('configure', [['spacing' => 'none']]);
    $services->set(TrailingCommaInMultilineFixer::class)->call('configure', [[
        'elements' => ['arrays', 'parameters', 'arguments'],
    ]]);

    $ecsConfig->rules([
        ArrayIndentationFixer::class,
        MethodChainingIndentationFixer::class,
        NoSuperfluousElseifFixer::class,
        NoUnusedImportsFixer::class,
        NotOperatorWithSuccessorSpaceFixer::class,
        OrderedInterfacesFixer::class,
        OrderedTraitsFixer::class,
        SingleSpaceAfterConstructFixer::class,
    ]);

    $parameters = $ecsConfig->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/tests',
        __DIR__.'/routes',
        __DIR__.'/database/factories',
        __DIR__.'/database/seeders',
    ]);
};
