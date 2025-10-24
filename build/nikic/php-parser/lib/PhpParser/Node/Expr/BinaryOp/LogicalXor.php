<?php

declare (strict_types=1);
namespace BackdropS3FS\PhpParser\Node\Expr\BinaryOp;

use BackdropS3FS\PhpParser\Node\Expr\BinaryOp;
class LogicalXor extends BinaryOp
{
    public function getOperatorSigil(): string
    {
        return 'xor';
    }
    public function getType(): string
    {
        return 'Expr_BinaryOp_LogicalXor';
    }
}
