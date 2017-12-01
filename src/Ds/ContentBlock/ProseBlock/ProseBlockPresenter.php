<?php
declare(strict_types = 1);

namespace App\Ds\ContentBlock\ProseBlock;

use App\BlogsService\Domain\ContentBlock\Prose;
use App\Ds\Presenter;
use Cake\Utility\Text;

class ProseBlockPresenter extends Presenter
{
    /** @var Prose */
    private $content;

    /** @var int|null */
    private $limit;

    public function __construct(Prose $content, int $limit = null, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
        $this->limit = $limit;
    }

    public function getProse(): string
    {
        if ($this->limit === null) {
            return $this->content->getProse();
        }

        return Text::truncate($this->content->getProse(), $this->limit, ['html' => true, 'exact' => false]);
    }
}
