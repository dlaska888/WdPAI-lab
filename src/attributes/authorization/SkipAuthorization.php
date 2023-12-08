<?php

namespace src\attributes\authorization;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class SkipAuthorization
{

}