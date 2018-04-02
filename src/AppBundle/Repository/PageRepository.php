<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class PageRepository
 *core/pdo.php
 */
class PageRepository extends EntityRepository
{

    use RepositoryHelper;
}
