<?php

namespace Neuedev\Apineu\Filter;

enum FilterStage
{
    case Filter;
    case Order;
    case Pagination;
}
