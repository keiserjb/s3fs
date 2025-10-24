<?php

declare (strict_types=1);
namespace BackdropS3FS\PhpParser\Node\Expr\BinaryOp;

use BackdropS3FS\PhpParser\Node\Expr\BinaryOp;
class BooleanOr extends BinaryOp
{
    public function getOperatorSigil(): string
    {
        return '||';
    }
    public function getType(): string
    {
        return 'Expr_BinaryOp_BooleanOr';
    }
}
