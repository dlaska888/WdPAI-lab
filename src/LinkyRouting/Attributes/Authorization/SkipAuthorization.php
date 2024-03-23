<?php

namespace LinkyApp\LinkyRouting\Attributes\Authorization;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class SkipAuthorization
{

}