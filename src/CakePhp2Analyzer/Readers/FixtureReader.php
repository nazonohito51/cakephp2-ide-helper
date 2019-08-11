<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\PhpParser\Ast;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;

class FixtureReader
{
    private $file;
    private $ast;

    public function __construct(string $path)
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException('invalid fixture path: ' . $path);
        }

        $this->file = new \SplFileInfo($path);
        $this->ast = new Ast($path);
    }

    public function getFabricateDefineNames()
    {
        $staticCalls = $this->ast->getStaticCalls(['Fabricate', 'Fabricate\\Fabricate', ['\\Fabricate\\Fabricate']], 'define');

        $ret = [];
        foreach ($staticCalls as $staticCall) {
            if (isset($staticCall->args[0]) && $staticCall->args[0] instanceof Arg) {
                $firstArg = $staticCall->args[0];
                if ($firstArg->value instanceof Array_ && isset($firstArg->value->items[0])) {
                    $firstArrayItemInFirstArg = $firstArg->value->items[0];
                    if ($firstArrayItemInFirstArg->value instanceof String_) {
                        $ret[] = $firstArrayItemInFirstArg->value->value;
                    }
                } elseif ($firstArg->value instanceof String_) {
                    $ret[] = $firstArg->value->value;
                }
            }
        }

        return $ret;
    }
}
