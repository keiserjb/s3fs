<?php

declare (strict_types=1);
namespace BackdropS3FS\PhpParser\Node\Expr\AssignOp;

use BackdropS3FS\PhpParser\Node\Expr\AssignOp;
class Mod extends AssignOp
{
    public function getType(): string
    {
        return 'Expr_AssignOp_Mod';
    }
}
