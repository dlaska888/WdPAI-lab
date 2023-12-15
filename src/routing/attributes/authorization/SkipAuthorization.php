<?php

namespace src\routing\attributes\authorization;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class SkipAuthorization
{

}