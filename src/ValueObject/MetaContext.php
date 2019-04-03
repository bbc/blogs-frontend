<?php
declare(strict_types = 1);

namespace App\ValueObject;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class MetaContext
{
    /** @var bool */
    private $metaNoIndex;

    public function __construct(bool $metaNoIndex)
    {
        $this->metaNoIndex = $metaNoIndex;
    }

    public function isNoIndex(): bool
    {
        return $this->metaNoIndex;
    }

    public function getBBCFacebookPageIds(): string
    {
        return implode(',', $this->bbcFacebookPageIds());
    }

    /** @return int[] */
    private function bbcFacebookPageIds(): array
    {
        return [
            6025943146,
            7397061762,
            7519460786,
            7833211321,
            8244244903,
            8251776107,
            8585725981,
            21750735380,
            80758950658,
            125309456546,
            130593816777,
            154344434967,
            228735667216,
            260212261199,
            260967092113,
            294662213128,
            295830058648,
            304314573046,
            401538510458,
            107909022566650,
            118883634811868,
            129044383774217,
            156060587793370,
            156400551056385,
            163571453661989,
            168895963122035,
            185246968166196,
            193022337414607,
            193435954068976,
            194575130577797,
            215504865453262,
            239931389545417,
            273726292719943,
            283348121682053,
            286567251709437,
            292291897588734,
            310719525611571,
            317278538359186,
            413132078795966,
            470911516262605,
            512423982152360,
            647687225371774,
            658551547588605,
            742734325867560,
            944295152308991,
            958681370814419,
            1143803202301544,
            1159932557403143,
            1392506827668140,
            1411916919051820,
            1477945425811579,
            1659215157653827,
            1731770190373618,
        ];
    }
}
