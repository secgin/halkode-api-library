<?php

namespace S\Halkode;

use S\Halkode\Api\Authorization\GetAccessToken;
use S\Halkode\Api\Payment\PaySmart3D;
use S\Halkode\Api\Payment\ValidatePaymentHash;
use S\Halkode\Api\Pos\GetPos;
use S\Halkode\Completion\GetAccessTokenResult;
use S\Halkode\Completion\GetPosResult;
use S\Halkode\Completion\ValidatePaymentHasResult;
use S\Halkode\Core\Abstracts\Result\Result;

/**
 * @method Result|GetAccessTokenResult getAccessToken(GetAccessToken $request)
 * @method Result paySmart3D(PaySmart3D $request)
 * @method Result|GetPosResult getPos(GetPos $request)
 * @method Result|ValidatePaymentHasResult validatePaymentHash(ValidatePaymentHash $request)
 */
interface HalkodeApiClient
{
}