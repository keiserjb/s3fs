<?php

namespace BackdropS3FS\Aws\Auth;

use BackdropS3FS\Aws\Identity\IdentityInterface;
/**
 * An AuthSchemeResolver object determines which auth scheme will be used for request signing.
 */
interface AuthSchemeResolverInterface
{
    /**
     * Selects an auth scheme for request signing.
     *
     * @param array $authSchemes a priority-ordered list of authentication schemes.
     * @param array $args
     *
     * @return string|null
     */
    public function selectAuthScheme(array $authSchemes, array $args): ?string;
}
