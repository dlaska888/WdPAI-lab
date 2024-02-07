<?php

namespace src\LinkyRouting\attributes\authorization;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class SkipAuthorization
{

}