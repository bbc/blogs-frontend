<?php
declare(strict_types = 1);

namespace App\Translate;

use Symfony\Component\Translation\Loader\PoFileLoader;

/**
 * This class exists as PoFileLoader doesn't support empty
 * string correctly. It assumes an empty string is a valid
 * translation, rather than a non-existent one.
 *
 * @see https://github.com/symfony/symfony/issues/13483
 */
class PoFileLoaderExceptItActuallyWorks extends PoFileLoader
{
    protected function loadResource($resource)
    {
        $messages = parent::loadResource($resource);

        foreach ($messages as $key => $value) {
            if ($value === '') {
                unset($messages[$key]);
            }
        }

        return $messages;
    }
}
