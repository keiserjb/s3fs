<?php

/**
 * Copyright Amazon.com, Inc. or its affiliates. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0.
 */
namespace BackdropS3FS\AWS\CRT\Auth;

class SignedBodyHeaderType
{
    const NONE = 0;
    const X_AMZ_CONTENT_SHA256 = 1;
}
