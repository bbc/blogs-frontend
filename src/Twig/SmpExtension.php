<?php
declare(strict_types = 1);
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig_Function;

class SmpExtension extends AbstractExtension
{
    private $smps;

    public function __construct()
    {
        $this->smps = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new Twig_Function('add_smp', [$this, 'addSmp']),
            new Twig_Function('build_smps', [$this, 'buildSmps']),
        ];
    }

    public function addSmp(string $player)
    {
        $this->smps[] = $player;
    }

    public function buildSmps(): ?string
    {
        if (empty($this->smps)) {
            return null;
        }
        $smps = 'require([\'smp\'], function(SMP) {';
        foreach ($this->smps as $player) {
            $smps .= 'new SMP(' . $player . ');';
        }
        $smps .= '});';
        return $smps;
    }
}
